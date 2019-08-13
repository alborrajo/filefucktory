# FileFucktory
### Barebones cloud storage

Made as a project to learn how to develop webpages

It can be tried in http://filefucktory.ga/ (Requires invitation to join)

##### Features

- Account based system

- Limited storage per user

- Uploading, deleting and moving files

- New users can register only by being invited by other users

- Client-side and server-side password hashing

- Public and private folders

##### Installation

###### Requires

- Node

- NPM

###### How to install

- Clone repo

- Make a new SQLite database (```db/filefucktory.db```, for example) and import ```db/ddl.sql``` into it.

- Add a new user to the database using the ```useradd.js``` script, providing the database, username, and password

	- ```node ./useradd.js filefucktory.db username password```
	
- Make a folder to store the users' files (```rest/files/``` for example)

- Configure ```rest/config/config.json``` to your liking. The default configuration is valid for most cases

	- If you change ```restRoute```, don't forget to change it on ```frontend/public/config/config.js``` as well.

TODO: How to run the app

###### Upgrading from previous versions

Previous versions used PHP and a ```JSON``` file to store user data, to migrate from that file to the newer version, which uses SQLite,
run the script ````rest/db/json2sql.js``` providing the JSON and the DB files to be accessed

```
# For example:
node ./json2sql.js users.json filefucktory.db
```