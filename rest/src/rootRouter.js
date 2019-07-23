// Express
const express = require('express')
const router = new express.Router();

// ROUTERS
router.use('/files', require('./files/filesRouter.js'));
router.use('/user', require('./user/userRouter.js'));

// 420
router.use('/', (req, res, next) => {
	res.status(420).send("Smoke Weed Everyday");
});

module.exports = router;