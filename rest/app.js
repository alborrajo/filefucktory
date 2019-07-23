// MAIN
//
// This is the file that has to be run to initiate the REST API

// Express
const express = require('express')
const app = express()

// Configuration file
const config = require('./config/config.json')


// ROUTER
app.use('/', require('./src/rootRouter.js'));


// Actually launch the app
app.listen(config.port, () => console.log(`Filefucktory listening on port ${config.port}!`))