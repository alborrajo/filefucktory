// Database utils
//
// Handles miscelaneous database operations

const sqlite3 = require('sqlite3');
const crypto = require("crypto");
const bcrypt = require('bcrypt');

// Load configuration file
const config = require('../../config/config.json')

// Connect to Database
const db = new sqlite3.Database(
	config.database,
	sqlite3.OPEN_READWRITE,
	error => {
		if(error) {throw error;}
		console.log(`Connected to database in ${config.database}`);
	}
);

// Middleware to handle DB errors caused by the users' requests.
//	It does NOT handle DB errors such as no permissions, no space left, etc.
//	Those are sent to next.
function errorHandler(err, req, res, next) {
	// Errors to be handled by dbErrorHandler, and their corresponding status
	const error2status = {
		"SQLITE_TOOBIG": 400,
		"SQLITE_CONSTRAINT": 400,
		"Used up all invites": 400,
	}
	
	if(err.code in error2status) {
		return res.status(error2status[err.code])
				  .send(err.code); // TODO: Dont reveal DB info to the clients
	}

	next(err);
}


// Checks if an User with the specified email and password exists in the DB
function login(username, password) {
	return new Promise((resolve, reject) => {db.get(
		"SELECT * FROM User WHERE username = ?;",
		[username],
		async (err, row) => {
			// Throw exception if there's an error
			if(err) {return reject(err);}
			
			// If the password matches, resolve. Otherwise reject.
			try {
				if(await bcrypt.compare(password, row.password)) {return resolve(row);}
				else {return reject();}
			} catch(err) {
				return reject();
			}
		}
    )});
}


// Retrieves an invite by its ID
function getInvite(inviteID) {
	return new Promise((resolve, reject) => {db.get(
		`SELECT
			*,
			(SELECT COUNT(*)
			FROM User
			WHERE invitedBy = Invite.inviteID)==0 AS pending
		FROM Invite
		WHERE inviteID = ?`,
		[inviteID],
		(err, row) => {
			row.pending = Boolean(row.pending);
			if(err) {return reject(err);}
			return resolve(row);
		}
	)});
}

// Retrieves all Invites sent by a User.
//	Also checks if the Invite has been used
//	(Meaning, checks whether there's an user in the DB with the Invite's ID)
function getInvitesByUsername(username) {
	return new Promise((resolve, reject) => {db.all(
		// Select all data from invite and check if there's an user in the DB that has used that invite
		`SELECT
			*,
			(SELECT COUNT(*)
			FROM User
			WHERE invitedBy = Invite.inviteID)==0 AS pending
		FROM Invite
		WHERE sentBy = ?`,
		[username],
		(err, rows) => {
			if(err) {return reject(err);}
			// Convert pending to boolean (1 -> true, 0 -> false)
			try {rows.map(row => row.pending = Boolean(row.pending));} catch(e) {} // If it fails (if there are no invites, for example), do nothing
			return resolve(rows);
		}
	)});
}

// Inserts into the DB a new Invite
async function newInvite(user) {
	// Check if the user has already used all their invites
	if((await getInvitesByUsername(user.username)).length >= user.maxInvites) {throw {code: "Used up all invites"};}
	
	// Generate random invite ID
	const inviteID = crypto.randomBytes(4).readUIntBE(0, 4);
	
	return new Promise((resolve, reject) => {db.run(
		"INSERT INTO Invite(inviteID, sentBy) VALUES(?, ?);",
		[inviteID, user.username],
		// We use function here in order to preserve "this" and retrieve this.lastID
		// It won't work with an arrow function, blame Javascript
		function(err) {
			if(err) {return reject(err);}
			console.log(this);
			return resolve(this.lastID);
		}
	)});
}

// Adds a new User to the DB using the specified Invite, and returns its data through the callback
//	In other words, inserts a new User using Invite.email and the specified password
//
//	DB queries are promisified so we can work with callbacks without ending in a callback hell
async function useInvite(inviteID, username, password, callback) {
	return new Promise((resolve,reject) => {
		db.serialize(async function() {
			db.run('BEGIN TRANSACTION;');
			
			try{
				// Retrieve Invite
				let invite = await getInvite(inviteID);
				
				// If there's no invite or it has been used, stop
				if(!invite || !invite.pending) {
					db.run('ROLLBACK;'); 		// Stop transaction
					return resolve(null); 	// Return null
				}
				
				
				// Generate user folder string
				const folder = crypto.randomBytes(16).toString("hex");	
				
				// Hash password
				const hashedPassword = await bcrypt.hash(password, 10);

				
				// Insert User
				await new Promise((resolve, reject) => {
					db.run(
						"INSERT INTO User(username, password, folder, invitedBy) VALUES(?, ?, ?, ?);",
						[username, hashedPassword, folder, inviteID],
						(err) => {
							if(err) {
								return reject(err); 		// Reject. This will send err to the catch block
							}
							return resolve(this.lastID); 	// Resolve if no errors happened
						}
					);
				});
				
				// Retrieve newly created User
				let newUser = await new Promise((resolve, reject) => {db.get(
					"SELECT * FROM User WHERE username = ?;",
					[username],
					async (err, row) => {
						// Throw exception if there's an error
						if(err) {return reject(err);}
						return resolve(row);						
					}
				)});
				db.run('COMMIT;');
				
				// Return data
				return resolve(newUser);
				
			} catch(err) {
				db.run('ROLLBACK;');// Stop transaction
				return reject(err); // Throw exception
			}
		});	
	});
}



function isOwner(username, folder) {
	return new Promise((resolve, reject) => {db.get(
		"SELECT COUNT(*)>0 AS isOwner FROM User WHERE username = ? AND folder = ?",
		[username, folder],
		(err, row) => {
			if(err) {return reject(err);}
			return resolve(Boolean(row.isOwner)); // Convert result to Boolean and resolve
		}
	)});
}



module.exports = {
	errorHandler: errorHandler,
	
	login: login,
	
	getInvite: getInvite,
	getInvitesByUsername: getInvitesByUsername,
	newInvite: newInvite,
	useInvite: useInvite,
	
	isOwner: isOwner,
};