
#!/bin/bash

node bootstrap.js
exit 0

mkdir /tmp/webroot
./php bootstrap.php


ls -la
ls -la /tmp
ls -la /tmp/webroot

cp ./nginx /tmp/nginx
cp ./php /tmp/php

/tmp/nginx -t -c /tmp/nginx.conf

./php -S localhost:1111 -t /tmp/webroot/ &
/tmp/nginx -c /tmp/nginx.conf -g "pid /tmp/nginx.pid; worker_processes 1;"