#!/bin/bash

echo Generating users.json...

originalpwd=$(pwd)
mkdir /tmp/mongo2json 2>/dev/null
cd /tmp/mongo2json

echo { > users.json
echo \"users\": >> users.json
echo [ >> users.json

mongoexport -d filefucktory -c user -o users.tmp > /dev/null
cat users.tmp | sed s/\ \"\_id\".*\}\,/""/ | sed s/}$/},/ >> users.json

echo ], >> users.json
echo \"invites\": >> users.json
echo [ >> users.json

mongoexport -d filefucktory -c invited -o invites.tmp > /dev/null
cat invites.tmp | sed s/\ \"\_id\".*\}\,/""/ | sed s/}$/},/ >> users.json

echo ] >> users.json
echo } >> users.json

echo
echo ...done
echo Exported file: $originalpwd/users.json

cp users.json $originalpwd/users.json