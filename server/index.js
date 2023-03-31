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


const port = process.env.PORT;

const deta = Deta();
const drive = deta.Drive('webroot');


// ======================================================
// Load the webroot files from Deta Drive
// ======================================================
const tmpFolder = '/tmp/webroot';

async function main() {
	await fs.mkdir(tmpFolder, { recursive: true });

	let webrootFiles = await drive.list();

	const { results, errors } = await PromisePool
	  .for(webrootFiles.names)
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

	  //console.log('Downloaded files:', results.length);
	  console.log(errors);


}

main();
// ======================================================
// ======================================================

function startPHP(port, path) {
	exec("php -S localhost:"+port+" -t "+path+" &", (error, stdout, stderr) => {
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

startPHP(1111, "/tmp/webroot");
startPHP(4040, "./webroot");


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


		if (proxyRes.statusCode == 404) {
			//res.writeHeader(404, {"Content-Type": "text/html"});  

			console.log(response);
			//response.end(fsOld.readFileSync(path.join(__dirname, '/webroot/errors/404.html'), 'utf8'));

			return fsOld.readFileSync(path.join(__dirname, '/webroot/errors/404.html'), 'utf8');


		//res.statusCode //res.write(fs.readFile(path.join(__dirname, '/webroot/404.html'), 'utf8')); 

		/*
			res.writeHeader(500, {"Content-Type": "text/html"});  
			res.end(fsOld.readFileSync(path.join(__dirname, '/webroot/errors/500.html'), 'utf8'));
		*/

		}

		return response.replace('Hello', 'Goodbye'); // manipulate response and return the result
	  }),
	},
  });


  

app.get('/*', proxy);

app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})