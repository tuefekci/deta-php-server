<?php

function generateNginxConf($port) {

	return '
		# Set up the events section
		events {
			# Define the worker_connections setting
			worker_connections 1024;
		}

		http {
			
			client_body_temp_path /tmp;
			proxy_temp_path  /tmp;

			access_log /dev/stdout;
			error_log /dev/stdout info;

			upstream backend {
				server localhost:1111;
			}
		
			server {
				listen '.$port.';
				server_name localhost;
				location / {
					proxy_pass http://backend;

					# Set some proxy headers to pass through to the backend
					proxy_set_header Host $host;
					proxy_set_header X-Real-IP $remote_addr;
					proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

					# Serve static files directly if they exist
					try_files $uri $uri/ /tmp/webroot/$uri /tmp/webroot/$uri/ =404;

					# Enable gzip compression for responses
					gzip on;
					gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

					# Define some caching parameters
					expires 1d;
					add_header Cache-Control "public, max-age=86400, must-revalidate";

					# Limit request body size to 4MB
					client_max_body_size 4M;
				
				}
			}
		}

	';

}