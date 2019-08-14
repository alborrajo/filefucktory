// MAIN
//
// This is the file that has to be run to initiate the REST API

// Express
const express = require('express')
const app = express()

// Configuration file
const config = require('./config/config.json')

// Security
const helmet = require('helmet');
app.use(helmet());

// ROUTER
app.use(config.restRoute, require('./src/rootRouter.js'));

// Proxy
if(config.trustProxy) {
	app.enable('trust proxy');
}

// Frontend
if(config.serveFrontend) {
	app.use(express.static('../frontend/public'));
}

// Actually launch the app
app.listen(config.port, () => console.log(`Filefucktory listening on port ${config.port}!`))