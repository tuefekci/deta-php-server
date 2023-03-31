#!/bin/bash
export CPUCOUNT=$(nproc --all)
echo $CPUCOUNT

cp ./nginx /tmp/nginx
ls -la /tmp
ls -la

echo "# Define environment variables" > /tmp/nginx.conf
echo "env PORT;" >> /tmp/nginx.conf
echo "" >> /tmp/nginx.conf

echo "# Set up the events section" >> /tmp/nginx.conf
echo "events {" >> /tmp/nginx.conf
    echo "	# Define the worker_connections setting" >> /tmp/nginx.conf
    echo "	worker_connections 1024;" >> /tmp/nginx.conf
echo "}" >> /tmp/nginx.conf

echo "" >> /tmp/nginx.conf
echo "http {" >> /tmp/nginx.conf

echo "" >> /tmp/nginx.conf
echo "client_body_temp_path /tmp;" >> /tmp/nginx.conf
echo "proxy_temp_path  /tmp;" >> /tmp/nginx.conf
#echo "fastcgi_temp_path  /tmp;" >> /tmp/nginx.conf

echo "access_log   /tmp/access.log;" >> /tmp/nginx.conf
echo "error_log   /tmp/error.log;" >> /tmp/nginx.conf
echo "" >> /tmp/nginx.conf


# Start with an empty upstream block
echo "upstream backend {" >> /tmp/nginx.conf
echo "    least_conn;" >> /tmp/nginx.conf

for i in $(seq 1 $CPUCOUNT)
do
	phpport=$(shuf -i 60535-65535 -n 1)
	echo "Port $i: $phpport"
	echo "Starting php server @ localhost:$phpport"
	#./php -S localhost:$port -t webroot/ &

	# Add the new server to the upstream block
  	echo "    server localhost:$phpport;" >> /tmp/nginx.conf
done

# Close the upstream block
echo "}" >> /tmp/nginx.conf

echo "" >> /tmp/nginx.conf
echo "server {" >> /tmp/nginx.conf
echo "	listen $PORT;" >> /tmp/nginx.conf
echo "	server_name localhost;" >> /tmp/nginx.conf
echo "	location / {" >> /tmp/nginx.conf
echo '		return 200 "My variable value is: 2222";' >> /tmp/nginx.conf
echo "	}" >> /tmp/nginx.conf
echo "}" >> /tmp/nginx.conf

echo "" >> /tmp/nginx.conf

echo "}" >> /tmp/nginx.conf

echo "Starting nginx @ localhost:$PORT"

/tmp/nginx -t /tmp/nginx.conf
/tmp/nginx -c /tmp/nginx.conf -g "pid /tmp/nginx.pid; worker_processes $CPUCOUNT;"