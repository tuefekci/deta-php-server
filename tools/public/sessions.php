<?php

// Start the session
session_start();

$newSession = false;

if(!isset($_SESSION["username"])) {
	$newSession = true;
		// Set session variables
	$_SESSION["username"] = "john_doe";
	$_SESSION["email"] = "john_doe@example.com";
}


?>

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

			if($newSession) {
				echo "New session started.<br>";
			} else {
				echo "Session already started.<br>";
			}
			echo "<hr/><br/>";

			// Access session variables
			echo "Username is " . $_SESSION["username"] . "<br>";
			echo "Email is " . $_SESSION["email"];

		?>

	  </p>
		<hr/><br/>
	<a href="https://github.com/tuefekci/deta-php-engine">github.com/tuefekci/deta-php-engine</a>
	<a href="/phpinfo.php">phpinfo</a>
  </body>
</html>