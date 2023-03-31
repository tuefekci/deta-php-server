
#!/bin/bash
mkdir /tmp/webroot
node bootstrap.js

#./php bootstrap.php

ls -la
ls -la /tmp
ls -la /tmp/webroot

cp ./php /tmp/php

./php -S localhost:$PORT -t /tmp/webroot/