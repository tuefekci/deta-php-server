const express = require('express')
const app = express();
const request = require('request');
const { exec } = require("child_process");
const { Deta, Drive, Base } = require('deta'); // import Deta
const { PromisePool } = require('@supercharge/promise-pool');
const fs = require('fs').promises;
const fsOld = require('fs');
const path = require('path');
const util = require('util');
const { createProxyMiddleware, responseInterceptor } = require('http-proxy-middleware');

const phpFileTypes = ['php', 'php3', 'php4', 'php5', 'phtml', 'phar', 'inc'];
const phpDataTypes = ["env", "ini", "txt", "csv", "tsv", "xml", "json", "yml", "yaml", "xls", "xlsx", "doc", "docx"];

const port = process.env.PORT;

const deta = Deta();
const drive = deta.Drive('public');
const logs = deta.Base('logs');
const sessions = deta.Base('sessions');
sessions.put({key: 'test', value: 'test'});
var publicFiles = [];
var syncFiles = [];

// ======================================================
// Load the public files from Deta Drive
// ======================================================
const tmpFolder = '/tmp/public';

async function sync() {

	await fs.mkdir(tmpFolder, { recursive: true });
	let allFiles = [];

	if(syncFiles.length == 0 || publicFiles.length == 0) {
		// get all files
		let result = await drive.list();
		allFiles = result.names;
		let last = false;

		if(result.paging && result.paging.last) {
			last = result.paging.last;

			while (last) {
				// provide last from previous call
				result = await drive.list({ last: result.paging.last });
				allFiles = allFiles.concat(result.names);
			
				if(result.paging && result.paging.last) {
					last = result.paging.last;
				}else{
					last = false;
				}
			}
		}
		
		if(allFiles.length == 0) {
			console.info('No files found in Deta Drive');
			return;
		}
	}

	if(process.env.SYNC_ALL_FILES) {
		syncFiles = allFiles;
	}else{
		// get all executable files
		for (let i = 0; i < allFiles.length; i++) {
			const file = allFiles[i];
			const fileType = file.split('.').pop();

			if (phpFileTypes.indexOf(fileType) > -1 || phpDataTypes.indexOf(fileType) > -1) {
				syncFiles.push(file);
			} else {
				publicFiles.push(file);
			}
		}
	}

	const { results, errors } = await PromisePool
	  .for(syncFiles)
	  .withConcurrency(65)
	  .process(async file => {

		const filePath = path.join(tmpFolder, file);
		const folderPath = path.dirname(filePath);
		await fs.mkdir(folderPath, { recursive: true });
		
		try {
			await fs.access(filePath, fs.constants.F_OK);
			//console.error('myfile already exists');
		  } catch (err) {
			if (err.code === 'ENOENT') {
			  try {
				const fileData = await drive.get(file);
				await fs.writeFile(filePath, Buffer.from(await fileData.arrayBuffer()));
				//console.log(`${file} downloaded successfully!`);
			  } catch (err) {
				throw err;
			  }
			} else {
			  throw err;
			}
		  }
		
	  });

	console.log('Total files:', allFiles.length);
	console.log('Downloaded files:', results.length);
	console.log('Downloaded errors:', errors.length);
}

sync();
// ======================================================
// ======================================================
var phpEnv = {
	"PHP_INCLUDE_PATH": process.mainModule.path+'/php-includes'
};
phpEnv = Object.assign(phpEnv, process.env);

var phpPath = "php";

console.log(phpEnv);

if(phpEnv.DETA_RUNTIME) {
	phpPath = "./php";
}

function startPHP(port, path) {
	exec(phpPath+" -S localhost:"+port+" -t "+path+" -c ./config/php.ini", {env: phpEnv}, (error, stdout, stderr) => {
		if (error) {
			console.log(`error: ${error.message}`);
			return;
		}
		if (stderr) { 
			console.log(`stderr: ${stderr}`);
			return;
		}
		console.log(`stdout: ${stdout}`);
	});
}

startPHP(1111, "/tmp/public");
//startPHP(4040, "./webroot");

const middleware = {
    requireAuthentication: function(req, res, next) {
        console.log('private route list!');
        next();
    },
    logger: function(req, res, next) {
       console.log('Original request hit : '+req.originalUrl);
       next();
    },
	handleStaticFiles: function(req, res, next) {
		console.log('Original request hit : '+req.originalUrl);

		const fileType = req.originalUrl.split('.').pop();

		// if is file
		if (req.originalUrl.indexOf('.') > -1 && phpFileTypes.indexOf(fileType) == -1) {

			if(publicFiles.indexOf(req.originalUrl) == -1) {
				res.sendFile(path.join(__dirname, '/public/errors/404.html'));
				return;
			}


			// if file exists
			if (fsOld.existsSync(path.join(tmpFolder, '/public'+req.originalUrl))) {
				res.sendFile(path.join(tmpFolder, '/public'+req.originalUrl));
			}else{
				res.sendFile(path.join(__dirname, '/public/errors/404.html'));
			}
		}else{
			next();
		}

		//console.log('Original request hit : '+req.originalUrl);
		//next();
	}
}

const proxy = createProxyMiddleware({
	target: 'http://localhost:1111',
	/**
	 * IMPORTANT: avoid res.end being called automatically
	 **/
	selfHandleResponse: true, // res.end() will be called internally by responseInterceptor()
  
	/**
	 * Intercept response and replace 'Hello' with 'Goodbye'
	 **/
	on: {
	  proxyRes: responseInterceptor(async (responseBuffer, proxyRes, req, res) => {
		console.log('proxyRes', proxyRes.statusCode);
		console.log(__dirname);
		const response = responseBuffer.toString('utf8');

		try {
			if (proxyRes.statusCode >= 400) {
				return fsOld.readFileSync(path.join(__dirname, '/public/errors/404.html'), 'utf8');
			}
		} catch (error) {
			console.error(`Error reading error file: ${error}`);
			return `An error occurred while processing your request. Please try again later.`;
		}
	
		return response; // manipulate response and return the result
	  }),
	},
  });


app.get('/detaphant/flush', middleware.logger, async(req, res) => {
	await sync();
	res.send('Hello World!')
});

app.get('/*', [middleware.handleStaticFiles, proxy]);

app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})