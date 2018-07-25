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

##### Installation

###### Requires

- Web server (Tested with Apache)

- PHP

###### How to install

- Clone repo

- Make a **files** folder and give your web server permission to use it

- Enable site on your web server configuration

	- Max file upload size should be specified in PHP configuration

- The default configuration at ```config.json``` is valid for most cases, but it can be modified to match your preferences

###### Upgrading from previous versions

Since newer versions don't use **mongodb** anymore, if you want to upgrade from a version that used mongodb, you'll have to run the script ```utils/mongo2json.sh``` and copy the output file to ```config/users.json``` 