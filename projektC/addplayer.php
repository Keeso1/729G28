<?php
require __DIR__ . '/connectdb.php';
$selected_team = get_team_info(connectDB(), $_GET['teamID']);
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
				<h1>Add player to <?php echo $selected_team["fullName"]?></h1>
				<p>Here you can add a new player.</p>
				<p>You are Logged in as: <?php echo $_SESSION["userName"]; ?></p>
			</header>
	    </div>
	    <ul class="breadcrumb">
			<li><a href="home.php">Home</a></li>
			<?php
				echo "<li>"     
						. "<a href='team.php?teamID=" . $selected_team["ID"]."'>"
						. $selected_team["fullName"]
						. "</a>"
						. "</li>";
				echo "<li class='active'>Add player</li>";
        	?>
			<!-- Add proper breadcrumbs -->
	    </ul>
	    <div class="container xs-8">
			<form action=<?php echo "'addplayer.php?teamID=". $_GET["teamID"]. "'" ?> method="post">
				<label>Name:<input type="text" name="name"></label><br>
				<label>Weight:<input type="number" name="weight" step="any"></label><br>
				<label>Length:<input type="number" name="length" step="any"></label><br>
				<label>Birthdate:<input type="date" name="birthdate"></label><br>
				<label>Information:<input type="text" name="information"></label><br>
				<input type="submit" name="playersubmit" value="Add player">
			</form>
			<!-- Create a form where a user can add info about a player and then send that your database to create a new player. -->
			<?php
				if (!isset($_POST['playersubmit'])){
				}else{
					$name = htmlspecialchars(trim($_POST['name']));
					$weight = htmlspecialchars(trim($_POST['weight']));
					$length = htmlspecialchars(trim($_POST['length']));
					$birthdate = htmlspecialchars(trim($_POST['birthdate']));
					$information = htmlspecialchars(trim($_POST['information']));
					$errors = array();

					if (empty($name)) {
						$errors[] = "Name must be chosen";
					}

					if (!empty($errors)) {
						// Display errors to the user
						foreach ($errors as $error) {
							echo "<p style='color: red;'>$error</p>";
						}
					} else {
						$playerInfo = [
							'name' => $name,
							'weight' => $weight,
							'length' => $length,
							'birthdate' => $birthdate,
							'information' => $information
						];
						echo add_player(connectDB(), $_GET['teamID'], $playerInfo, $_SESSION["userName"])["message"];
					}


					console_log($errors);
					//Here you need to 1. Read and validate the incoming data based on the requirements 2. Store the user if the validation is successful 3. Show an appropriate message.
					// $conn is your database endpoint and shall be used for working with the database.
				}
        	?>
	    </div>
    </body>
</html>

