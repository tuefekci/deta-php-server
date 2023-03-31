const { Deta, Drive, Base } = require('deta'); // import Deta
const { PromisePool } = require('@supercharge/promise-pool');
const fs = require('fs').promises;
const path = require('path');
const util = require('util');

const deta = Deta();
const drive = deta.Drive('webroot');

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
			console.error('myfile already exists');
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

	  console.log('Downloaded files:', results.length);
	  console.log(errors);


	}

main();