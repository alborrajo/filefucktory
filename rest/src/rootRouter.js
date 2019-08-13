// Express
const express = require('express')
const router = new express.Router();

// ROUTERS
router.use('/files', require('./files/filesRouter.js'));
router.use('/user', require('./user/userRouter.js'));

module.exports = router;