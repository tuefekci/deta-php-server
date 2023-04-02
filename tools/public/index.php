<!DOCTYPE html>
<html>
  <head>
    <title>deta-php-engine</title>
    <style>
		body {
			background-color: #121212;
			color: #f8f8f8;
			font-family: Arial, sans-serif;
			text-align: center;
		}

		h1 {
			font-size: 6em;
			text-shadow: 3px 3px #FF0000;
		}

		p {
			font-size: 1.5em;
			margin-top: 0;
			margin-left: auto;
			margin-right: auto;
			max-width: 80%;
		}

		a {
			text-decoration: none;
			color: #f2f2f2;
			background-color: #333;
			padding: 10px 20px;
			border-radius: 20px;
			transition: all 0.3s ease-in-out;
		}

		a:hover {
			color: #333;
			background-color: #f2f2f2;
			transform: scale(1.1);
		}

    </style>
  </head>
  <body>
    <h1>deta-php-engine</h1>
	<hr/><br/>
	  <p>
		<?php
			// Display PHP version
			echo "PHP Version: " . phpversion() . "<br>";

			// Display loaded extensions
			$loaded_extensions = get_loaded_extensions();
			echo "Loaded Extensions: " . implode(", ", $loaded_extensions) . "<br>";

			// Display maximum execution time
			$max_execution_time = ini_get('max_execution_time');
			echo "Max Execution Time: " . $max_execution_time . "<br>";

			// Display memory limit
			$memory_limit = ini_get('memory_limit');
			echo "Memory Limit: " . $memory_limit . "<br>";

			// Display error reporting level
			$error_reporting = error_reporting();
			echo "Error Reporting: " . $error_reporting . "<br>";

			// Display timezone
			$timezone = date_default_timezone_get();
			echo "Timezone: " . $timezone . "<br>";	

			// Display time
			$time = date('Y-m-d H:i:s');
			echo "Time: " . $time . "<br>";

			// Display server name
			$server_name = $_SERVER['SERVER_NAME'];
			echo "Server Name: " . $server_name . "<br>";

			// Display server address
			$server_addr = $_SERVER['SERVER_ADDR'];
			echo "Server Address: " . $server_addr . "<br>";

			// Display server port
			$server_port = $_SERVER['SERVER_PORT'];
			echo "Server Port: " . $server_port . "<br>";

			// Display server protocol
			$server_protocol = $_SERVER['SERVER_PROTOCOL'];
			echo "Server Protocol: " . $server_protocol . "<br>";

			// Display request method
			$request_method = $_SERVER['REQUEST_METHOD'];
			echo "Request Method: " . $request_method . "<br>";

			// Display request uri
			$request_uri = $_SERVER['REQUEST_URI'];
			echo "Request URI: " . $request_uri . "<br>";

			// Display request time
			$request_time = $_SERVER['REQUEST_TIME'];
			echo "Request Time: " . $request_time . "<br>";

			// Display request time float
			$request_time_float = $_SERVER['REQUEST_TIME_FLOAT'];
			echo "Request Time Float: " . $request_time_float . "<br>";

			// Display remote address
			$remote_addr = $_SERVER['REMOTE_ADDR'];
			echo "Remote Address: " . $remote_addr . "<br>";

			// Display remote port
			$remote_port = $_SERVER['REMOTE_PORT'];
			echo "Remote Port: " . $remote_port . "<br>";

			
		?>

	  </p>
		<hr/><br/>
	<a href="https://github.com/tuefekci/deta-php-engine">github.com/tuefekci/deta-php-engine</a>
	<a href="/phpinfo.php">phpinfo</a>
  </body>
</html>