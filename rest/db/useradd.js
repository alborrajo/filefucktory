const sqlite3 = require('sqlite3');
const crypto = require('crypto');
const bcrypt = require('bcrypt');

// Arguments:
if(process.argv.length < 5) {
	console.log("Parameters: database username password");
	process.exit(1);
}

const database = process.argv[2];
const username = process.argv[3];
const password = process.argv[4];

// Connect to Database
const db = new sqlite3.Database(
	database,
	sqlite3.OPEN_READWRITE,
	error => {
		if(error) {throw error;}
		console.log(`Connected to database in ${database}`);
	}
);

// Generate user folder string
const folder = crypto.randomBytes(16).toString("hex");	
			
// Hash password
const hashedPassword = bcrypt.hashSync(password, 10);

db.run(
	"INSERT INTO User(username, password, folder) VALUES(?, ?, ?);",
	[username, hashedPassword, folder],
	function(err) {
		if(err) {throw err;}
		console.log("Added user with ID:",this.lastID); // Log if no errors happened
	}
);