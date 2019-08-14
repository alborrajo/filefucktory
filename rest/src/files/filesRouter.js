// /files Router
//
// More info in REST routes.txt
const express = require('express');
const router = new express.Router();

const bodyParser = require('body-parser');

const path = require('path');
const pathIsInside = require("path-is-inside");

const crypto = require('crypto');

const { publicFileAuth, privateFileAuth } = require('../db/auth.js');

const fsutils = require ('./fsutils.js');
const db = require('../db/db.js');

// Load configuration file
const config = require('../../config/config.json')

// Converts the request path into a local path
//	and stores it in req.localPath
//	
//	req.relLocalPath:	userFolder path/to/file -> userFolder/path/to/file
//	req.localPath:		userFolder path/to/file -> FilesFolder/userFolder/path/to/file
//	req.userFolder:		userFolder path/to/file -> FilesFolder/userFolder/
function setLocalPath(req, res, next) {
	req.relLocalPath = decodeURIComponent(req.path);
	req.localPath = path.join(config.userFilesFolder, req.relLocalPath);	
	req.userFolder = path.join(config.userFilesFolder, req.params.userFolder);
	next();
}


// Temporary token stuff:
const fileTokens = [];

// Retrieve the contents of a file by using a temporary token
router.get('/:userFolder/\*', setLocalPath,
	async (req, res, next) => {
		try {
			// If it's public, next (No need for temporary tokens on public files)
			if(await fsutils.isPublic(req.localPath)) {return next();}
			
			// If it's a DIRECTORY, next
			if(await fsutils.isDirectory(req.localPath)) {return next();}
			
			// If no token parameter, next
			if(req.query.token === undefined) {return next();}

			// If invalid token, token for a different path or for a different IP, 403
			if(fileTokens[req.query.token] == null
			|| fileTokens[req.query.token].path !== req.localPath 
			|| fileTokens[req.query.token].ip !== req.ip) {
				return res.status(403).send();
			}
			
			
			// ---- TOKEN IS VALID ----
			
			// Delete the token to avoid it being used multiple times	
			if(config.tokenDeleteAfterUse) {delete fileTokens[req.query.token];}
			
			// Serve file if the token is valid 
			return res.sendFile(req.relLocalPath, {root: config.userFilesFolder});
		} catch(err) {
			return next(err);
		}
	}
);		

// Get a temporary token for a file
//	This is used to avoid having to deal with the browser's HTTP Basic Auth pop up
router.get('/:userFolder/\*', setLocalPath, publicFileAuth,
	async (req, res, next) => {
		try {
			// If it's a DIRECTORY or there's no token in the request, next
			if(await fsutils.isDirectory(req.localPath)) {return next();}
			if(req.query.getToken === undefined) {return next();}
			
			// Generate and store new token
			const newToken = crypto.randomBytes(16).toString("hex");
			fileTokens[newToken] = {
				path: 	req.localPath,
				ip:		req.ip,
			};
			
			// Delete token after the time specified in the configuration file
			setTimeout(() => delete fileTokens[newToken], config.tokenExpirationTime);
						
			// Send back the newly generated token
			return res.json({
				token: newToken
			});
		} catch(err) {
			return next(err);
		}
	}
);


// Retrieves metadata about a directory OR the contents of a file
//	If the query path points to a DIRECTORY, the response sends an object containing
//		its name, size, contents (recursive) and whether it's public or not
//	If the query path points to a FILE, the response will be the contents of the
//		file
router.get('/:userFolder/\*', setLocalPath, publicFileAuth,
	async (req, res, next) => {
		try {
			// If it's a DIRECTORY
			if(await fsutils.isDirectory(req.localPath)) {
				return res.json(await fsutils.readPath(req.localPath));
			}

			// If it's a FILE
			else {
				return res.sendFile(req.relLocalPath, {root: config.userFilesFolder});
			}
		} catch(err) {
			return next(err);
		}
	}
);

// Uploads the request's body as a file
router.post('/:userFolder/\*', bodyParser.raw({limit: config.maxFileSize}), setLocalPath, privateFileAuth,
	async (req, res, next) => {
		try {
			return res.status(201).send(await fsutils.writeFile(req.localPath, req.body));
		} catch(err) {
			return next(err);
		}
	}
);

// Deletes a folder
router.delete('/:userFolder/\*', express.json(), setLocalPath, privateFileAuth,
	async (req, res, next) => {
		try {
			// If it's a DIRECTORY
			if(await fsutils.isDirectory(req.localPath)) {
				return res.status(204).json(await fsutils.rmdir(req.localPath));
			}
			
			// If it's a FILE
			else if(await fsutils.exists(req.localPath)) {
				return res.status(204).send(await fsutils.rm(req.localPath));
			}
			
			// If it doesn't exist
			else {
				return res.status(404).send();
			}
		} catch(err) {
			return next(err);
		}
	}
);

// IF THE OBJECT CONTAINS FROM AND TO:
//	Moves a file or folder in FROM to the folder specified in TO
// OTHERWISE:
//	Creates a new folder OR sets an existing one as public or private
router.put('/:userFolder/\*', express.json(), setLocalPath, privateFileAuth,
	async (req, res, next) => {
		try {
			let pathToSet = req.localPath;
			
			// MOVE
			if(req.body.moveTo) {
				const relMoveTo = path.join(config.userFilesFolder, req.body.moveTo);					
				
				// Check if the path isn't inside the user folder, and if so, throw exception to avoid unsafe relative operations
				if(!pathIsInside(relMoveTo, req.userFolder)) {return res.status(400).send();}
			
				await fsutils.mv(req.localPath, relMoveTo);
				return res.status(204).send();
			}
			
			// NEW FOLDER or SET PUBLIC/PRIVATE
			else {
				// If there's name in the body of the request, CREATE NEW FOLDER
				if(req.body.name) {
					
					// Check if the path isn't inside the user folder, and if so, throw exception to avoid unsafe relative operations
					const pathToMake = path.join(pathToSet,req.body.name);
					if(!pathIsInside(pathToMake, req.userFolder)) {return res.status(400).send();}
					
					// BAD REQUEST if the folder already exists
					if(await fsutils.exists(pathToMake)) {
						return res.status(400).send();
					}
					
					await fsutils.mkdir(pathToMake)
					
					// Set the newly created folder as the path to set as public or private
					pathToSet = req.localPath + req.body.name;
				}
				
				// Set as public or private
				if(req.body.public !== undefined) {
					await fsutils.setPublic(pathToSet, Boolean(req.body.public));
					return res.status(204).send();
				}
				
				// Anything else: Bad Request
				return res.status(400).send();			
			}
				
		} catch(err) {
			return next(err);
		}
	}
);


// Handle file errors caused by a user request
router.use(fsutils.errorHandler);

// Handle DB errors caused by a user request
router.use(db.errorHandler);

module.exports = router;