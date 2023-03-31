const { Deta, Drive, Base } = require('deta'); // import Deta
const { PromisePool } = require('@supercharge/promise-pool');
const fs = require('fs');
const path = require('path');
const util = require('util');

const deta = Deta();
const drive = Drive('webroot');

const tmpFolder = './tmp';

fs.mkdirSync(tmpFolder, { recursive: true });

async function main() {
	let webrootFiles = await drive.list();

	const { results, errors } = await PromisePool
	  .for(webrootFiles.names)
	  .withConcurrency(100)
	  .process(async file => {

		const openAsync = util.promisify(fs.open);
		const writeAsync = util.promisify(fs.write);
		const closeAsync = util.promisify(fs.close);
		
		const filePath = path.join(tmpFolder, file);
		const folderPath = path.dirname(filePath);
		
		if (!fs.existsSync(folderPath)) {
		  fs.mkdirSync(folderPath, { recursive: true });
		}
		
		(async () => {
		  try {
			const fd = await openAsync(filePath, 'wx');
			const fileData = await drive.get(file);
			await writeAsync(fd, fileData);
			await closeAsync(fd);
			console.log(`${file} downloaded successfully!`);
		  } catch (err) {
			if (err.code === 'EEXIST') {
			  console.error('myfile already exists');
			} else {
			  throw err;
			}
		  }
		})();
		
	  });
}

main();