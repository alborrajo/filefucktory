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

- nodejs (Tested with nodejs v12.x)

- npm

###### How to install

- Clone repo

- Run ```npm install``` in ```rest/```

- Make a new SQLite database (```db/filefucktory.db```, for example) and import ```db/ddl.sql``` into it.

	```
	$ sqlite3 filefucktory.db
	
	sqlite> .read ddl.sql
	```
	
- Make a folder to store the users' files (```rest/files/``` for example)

- Configure ```rest/config/config.json``` to your liking. The default configuration (```rest/config/config.example.json```) is valid for most cases

- Configure ```frontend/public/config/config.js``` as well. Again, you can use the default configuration (```frontend/public/config/config.example.js```)
	
- (Optional) Add a new user to the database using the ```useradd.js``` script, providing the database, username, and password

	```node ./useradd.js filefucktory.db username password```
	
- Run ```npm install``` in ```frontend/```

###### Running the application

- Run ```npm run build``` in ```/frontend```

- Run ```node app.js``` in ```/rest```

- DONE!

###### Upgrading from previous versions

Previous versions used a ```JSON``` file to store user data. To migrate from that file to the SQLite database used in the newer versions,
run the script ````rest/db/json2sql.js``` providing the JSON and the DB files to be accessed

```
# For example:
node ./json2sql.js users.json filefucktory.db
```