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
				<h1>Signup</h1>
				<p>Here you can sign up as a new user.</p>
			</header>
	    </div>
	    <ul class="breadcrumb">
			<li><a href="home.php">Home</a></li>
			<!-- Add proper breadcrumbs -->
	    </ul>
	    <div class="container xs-8">
		
			<form action="signup.php" method="post">
				<label>Name:<input type="text" name="name"></label><br>
				<label>Email:<input type="text" name="email"></label><br>
				<label>Password:<input type="password" name="password"></label><br>
				<input type="submit" name="usersubmit" value="Sign up">
			</form>
		<?php
			if (!isset($_POST['usersubmit'])){
			}else{
				$name = htmlspecialchars(trim($_POST['name']));
				$email = htmlspecialchars(trim($_POST['email']));
				$password = htmlspecialchars(trim($_POST['password']));
				$errors = array();

				if (empty($name)) {
					$errors[] = "Name is required.";
				}
				if (empty($email) or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$errors[] = "Valid email is required.";
				}
				if (empty($password) or strlen($password) < 8) {
					$errors[] = "Password must be at least 8 characters long.";
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
					echo add_user(connectDB(), $userInfo)["message"];
				}


				console_log($errors);
				//Here you need to 1. Read and validate the incoming data based on the requirements 2. Store the user if the validation is successful 3. Show an appropriate message.
				// $conn is your database endpoint and shall be used for working with the database.
			}
        ?>
	       
	    </div>
    </body>
</html>
