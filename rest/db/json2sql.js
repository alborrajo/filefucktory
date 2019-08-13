const sqlite3 = require('sqlite3');
const md5 = require('md5');

// Arguments:
if(process.argv.length < 3) {
	console.log("Parameters: json database");
	process.exit(1);
}

const json = process.argv[2];
const database = process.argv[3];

// Connect to Database
const db = new sqlite3.Database(
	database,
	sqlite3.OPEN_READWRITE,
	error => {
		if(error) {throw error;}
		console.log(`Connected to database in ${database}`);
	}
);

const users = require('./'+json);
users.users.forEach(user => {
	
	console.log("INSERTING",user.email)
	
	// Replace $2y$ from PHP's password_hash with $2a$
	// https://stackoverflow.com/questions/23015043/verify-password-hash-in-nodejs-which-was-generated-in-php
	const transformedPassword = user.password.replace(/^\$2y(.+)$/i, '$2a$1');
	const maxSpace = user.spacemb*1024*1024;
	
	db.run(
		"INSERT INTO User(username, password, folder, maxSpace) VALUES(?, ?, ?, ?);",
		[user.email, transformedPassword, md5(user.email), maxSpace],
		function(err) {
			if(err) {throw err;}
			console.log("\tID:",this.lastID); // Log if no errors happened
		}
	);
});

db.close();