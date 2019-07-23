// Authorization utils
//
// Strategies to check whether the credentials sent on the request
// are valid or not.
const BasicStrategy = require('passport-http').BasicStrategy;

const db = require('./db.js');
const fsutils = require ('../files/fsutils.js');

// Configure passport to process authentication with the database
//	Checks if the provided email and password can be found in the DB
const dbAuthPassport = require('passport');
dbAuthPassport.use(new BasicStrategy(async (username, password, done) => {
	try {
		const user = await db.login(username, password); 
		if (!user) { return done(null, false); }
		return done(null, user);
	}
	catch(err) {
		return done(err);
	}
}));
const dbAuth = dbAuthPassport.authenticate('basic', { session: false });


// Check if the resource is public OR if the provided auth is the owner of the resource
async function publicFileAuth(req, res, next) {
	try {
		// If folder is public, go on to the next middleware
		if(await fsutils.isPublic(req.localPath)) {return next();}
		
		return privateFileAuth(req, res, next);
	}
	catch(err) {return next(err);}
}

// Check if the provided auth is the owner of the resource to be accessed
async function privateFileAuth(req, res, next) {
	try {
		// If the user exists and is the owner of the folder, go on to the next middleware
		await new Promise((resolve, reject) => {dbAuth(req, res, () => {resolve()});}); // Use dbAuth to check if the user exists, this is a bit hacky
		if(await db.isOwner(req.user.username, req.params.userFolder)) {return next();}

		// Otherwise, 401 Unauthorized
		return res.status(401).send();
	}
	catch(err) {return next(err);} // Push errors to the error handler	
}


module.exports = {
	dbAuth: dbAuth,
	publicFileAuth: publicFileAuth,
	privateFileAuth: privateFileAuth
}