// /user Router
//
// More info in REST routes.txt

const express = require('express');
const router = new express.Router();

const dbAuth = require('../db/auth.js').dbAuth;
const db = require('../db/db.js');

const fsutils = require('../files/fsutils.js');

const config = require('../../config/config.json');

// Populate request body
router.use(express.json());

// Log in route
//	200 OK: 			Retrieves user object (without password) from DB if auth is correct
//	401 Unauthorized:	If no auth or auth isn't in the DB
router.get('/', dbAuth,
	(req, res, next) => {
		// Send User row as response **without the password field**
		const user = req.user;
		delete user.password;
		return res.json(user);
	}
);

// Retrieve user invites
//	200 OK: 			Retrieves array of invites sent by authenticated user
//	401 Unauthorized: 	If no auth or auth isn't in the DB
router.get('/invites', dbAuth,
	async (req, res, next) => {
		try {
			const invites = await db.getInvitesByEmail(req.user.username);
			return res.json({invites});
		}
		catch(err) {
			return next(err); // If there's an error, pass it to the next error handler
		}
	}
);

// Invite a new user
//	201 Created: 		Inserts in DB a new invite
//	401 Unauthorized: 	If no auth or auth isn't in the DB
router.post('/invites', dbAuth,	
	async (req, res, next) => {		
		try {
			const inviteID = await db.newInvite(req.user.username);
			return res.status(201).json({inviteID: inviteID});
		}
		catch(err) {
			return next(err); // If there's an error, pass it to the next error handler
		}
	}
);

// Retrieve invite information
//	200 OK:				Retrieves the invite information from the DB
//	404 Not Found:		If the invite doesn't exist
router.get('/invites/:inviteID',
	async (req, res, next) => {
		try {
			const invite = await db.getInvite(req.params.inviteID);
			
			// If there's no row, the invite doesn't exist in the DB. Not Found.
			if(!invite) {return res.status(404).send();}
				
			// Dont show publically who invited who
			delete invite.sentBy;
			return res.json(invite);
		}
		catch(err) {
			return next(err); // If there's an error, pass it to the next error handler
		}
	}
);

// Register new user, using invite
//	201 Created:	User is registered and its data is retrieved
//	404 Not Found:	If the invite doesn't exist
router.post('/invites/:inviteID',
	async (req, res, next) => {
		try {
			const newUser = await db.useInvite(req.params.inviteID, req.body.username, req.body.password);
			
			// If there's no row, the invite doesn't exist in the DB. Not Found.
			if(!newUser) {return res.status(404).send();}
			
			// Create the user's folder
			await fsutils.mkdir(path.normalize(config.userFilesFolder, newUser.folder));

			// Send User row as response **without the password field**
			const user = newUser;
			delete user.password;
			return res.status(201).json(user);
		}
		catch(err) {
			return next(err); // If there's an error, pass it to the next error handler
		}
	}
);


// Handle DB errors caused by a user request
router.use(db.errorHandler);

module.exports = router;