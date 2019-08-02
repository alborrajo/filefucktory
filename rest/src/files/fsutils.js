// Filesystem utils
//
const path = require('path');
const fs = require('fs').promises;
const rimraf = require("rimraf");

const config = require('../../config/config.json');


// Middleware to handle FS errors caused by the users' requests.
//	It does NOT handle errors such as no permissions, non existing user, etc.
//	Those are sent to next.
function errorHandler(err, req, res, next) {
	console.log(JSON.stringify(err));
	
	const customErrors = {
		"Undefined path": 	400,	// Undefined path
		"File exists":		400,	// Already exists
	}
	
	if(err in customErrors) {
		return res.status(customErrors[err]).send(err);
	}
	
	
	// Errors to be handled by errorHandler, and their corresponding status
	const error2status = {
		"-2":	404,	// ENOENT: no such file or directory
		"-17":	400,	// EEXIST: file already exists
	}
	
	if(err.errno in error2status) {
		return res.status(error2status[err.errno])
				  .send(err.toString()); // TODO: Dont reveal info to the clients
	}
	
	next(err);
}

async function exists(pathToRead) {
	try {
		await fs.stat(pathToRead);
		return true;
	} catch(err) {
		return false;
	}
}

// Returns true or false depending on if the file in the path is a directory or not
async function isDirectory(pathToRead) {
	return (await fs.stat(pathToRead)).isDirectory();
}

// Returns true or false depending on if the directory in the path is public or not
//	A file is considered public if any of the parent directories is public
//	A directory is considered public if there's a file named .public in it
//
// This function checks a path, and if it's not public, recursively checks the parent directories
//	until it can't go up anymore (not public) or until it finds a directory with a .public in it (public)
async function isPublic(pathToRead) {
	try {
		await fs.stat(path.normalize(pathToRead+"/.public"));
		return true;
	}
	catch (err) {
		const nextPath = path.normalize(path.dirname(pathToRead)); 	// Go up one level. /path/to/file -> /path/to
		if(nextPath === ".") {return false;}						// If it can't go up anymore, the folder is not public
		return isPublic(nextPath);
	}
}

// Recursively reads the file/directory of the path and returns an object
//	{
//		name:	
//		size:
//
//		// If it's a directory
//		public:			
//		contents: []
//	}
async function readPath(pathToRead) {
	// Read file metadata
	const pathToReadStat = await fs.stat(pathToRead);

	// Store its name and size
	const toReturn = {
		name: path.basename(pathToRead),
		size: pathToReadStat.size
	}
	
	// If the file is a directory, also store the directory contents
	//	and whether the directory is public or not
	if(pathToReadStat.isDirectory()) {
		// Store whether the directory is public
		toReturn.public = await isPublic(pathToRead);
		
		// Store the contents of the directory
		toReturn.contents = []
		
		const pathToReadContents = await fs.readdir(pathToRead);
		for(pathToReadChild of pathToReadContents) {
			// Don't include .public
			if(pathToReadChild !== ".public") {
				toReturn.contents.push(
					await readPath(path.normalize(pathToRead+"/"+pathToReadChild))
				);
			}
		}
	}
	
	return toReturn;
}

// Creates a new directory on the path specified, with the name specified
async function mkdir(pathToNewDir, newDirName) {
	const fullPath = path.normalize(pathToNewDir+"/"+newDirName);
	
	return fs.mkdir(fullPath);
}

// Deletes a directory
async function rmdir(pathToDelete) {
	const fullPath = path.normalize(pathToDelete);
	
	// Promisify rimraf(fullPath)
	return new Promise((resolve, reject) => {
		rimraf(fullPath, () => {resolve();});
	});
}

// Deletes a file
async function rm(pathToDelete) {
	const fullPath = path.normalize(pathToDelete);

	return fs.unlink(fullPath);
}

// Moves a file or directory
async function mv(from, to) {
	// FROM: 		/path/to//file.bin
	// TO:			/path/to/destination
	
	// fullFrom:	/path/to/file.bin
	// fullTo:		/path/to/destination/file.bin
	const fullFrom = path.normalize(from);
	const fullTo = path.normalize(to+"/"+path.basename(from));
	
	// Check if the destination path already contains a file or directory and if so, throw error
	if(await exists(fullTo)) {throw "File exists";}
	
	return fs.rename(fullFrom, fullTo);
}

// Sets a directory as public or makes it private
async function setPublic(pathToSet, public, userFolder) {
	const fullPath = path.normalize(path.join(pathToSet, ".public")); // path/to/set/.public

	try {
		if(public) {
			// Set as public
			await fs.writeFile(fullPath, "");
		} else {
			// Set as private
			await fs.unlink(fullPath);
		}
	}
	catch(err) {
		// If the error is that .public exists or not while creating/deleting it, ignore it.
		// If it's a different error, throw it
		const expectedErrors = [-2 /* ENOENT */, -17 /* EEXIST */]
		if(expectedErrors.indexOf(err.errno) === -1) {
			console.log(err);
			throw err;
		}
	}
	
	return;
}	

async function writeFile(filePath, data, userFolder) {
	const fullPath = path.normalize(filePath);
	
	// Check if the destination path already contains a file or directory and if so, throw error
	if(await exists(fullPath)) {throw "File exists";}
	
	return fs.writeFile(fullPath, data);
}


module.exports = {
	errorHandler: errorHandler,
	
	exists: exists,
	isDirectory: isDirectory,
	isPublic: isPublic,
	
	readPath: readPath,
	
	mkdir: mkdir,
	rmdir: rmdir,
	rm: rm,
	mv: mv,
	setPublic: setPublic,
	writeFile: writeFile
}