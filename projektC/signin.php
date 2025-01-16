<?php
require __DIR__ . '/connectdb.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>MLB Team Rosters</title>
        <meta name="description" content="Team Rosters">
        <link rel="stylesheet" href="bootstrap_themed.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
	    <div class="jumbotron">
			<header class ="customHeader">
				<h1>Sign In</h1>
				<p>Here you can sign in as an existing user.</p>
			</header>
	    </div>
	    <ul class="breadcrumb">
			<li><a href="home.php">Home</a></li>
			<li class="active">Sign in</li>
			<!-- Add proper breadcrumbs -->
	    </ul>
	    <div class="container xs-8">
		
			<form action="signin.php" method="post">
				<label>Name:<input type="text" name="name"></label><br>
				<label>Email:<input type="text" name="email"></label><br>
				<label>Password:<input type="password" name="password"></label><br>
				<input type="submit" name="submit" value="Sign in">
			</form>

			<p>Don't have an account?</p>
			<a href="signup.php">Click here to Sign up!</a>
		<?php
			if (!isset($_POST['submit'])){
			}else{
				$name = htmlspecialchars(trim($_POST['name']));
				$email = htmlspecialchars(trim($_POST['email']));
				$password = htmlspecialchars(trim($_POST['password']));
				$errors = array();

				if (empty($name)) {
					$errors[] = "Incorrect Username";
				}
				if (empty($email) or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$errors[] = "Incorrect Email";
				}
				if (empty($password) or strlen($password) < 8) {
					$errors[] = "Incorrect Password";
				}


				if (!empty($errors)) {
					// Display errors to the user
					foreach ($errors as $error) {
						echo "<p style='color: red;'>$error</p>";
					}
				} else {
					$userInfo = [
						'name' => $name,
						'email' => $email,
						'password' => $password
					];
					echo log_in(connectDB(), $userInfo);
				}


				console_log($errors);
				//Here you need to 1. Read and validate the incoming data based on the requirements 2. Store the user if the validation is successful 3. Show an appropriate message.
				// $conn is your database endpoint and shall be used for working with the database.
			}
        ?>
	       
	    </div>
    </body>
</html>
