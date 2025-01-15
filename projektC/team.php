<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>NFL Team Rosters</title>
        <meta name="description" content="Team Rosters">
        <link rel="stylesheet" href="bootstrap_themed.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
    <div class="jumbotron customHeader">
        <!-- ersätt med php -->
        <?php
        require __DIR__ . '/connectdb.php';
        if ($selected_team = get_team_info(connectDB(), $_GET['teamID'])){ // Check if team exist
            echo "<h1>". $selected_team["fullName"] ."</h1>"
            . "<h3>". $selected_team["ownerName"] ."</h3>"
            . "<p>". $selected_team["creationDate"] ."</p>";
        } else {
            echo "
                <h1>NAME OF TEAM</h1>
                <h3>OWNER OF TEAM</h3>
                <p>START</p>
                ";
        }
        ?>
    </div>
    <ul class="breadcrumb">
        <li><a href="home.php">Home</a></li>
        <li class="active">Team</li>
    </ul>
    <div class="container xs-8">
        <h3>Coaches</h3>
        <ul>
            <!-- ersätt med php -->
             <?php 
            $coach_list = get_team_coaches(connectDB(), $_GET['teamID']);
            foreach ($coach_list as $x){
                echo "<li>"
                            
                . "<a href='teammember.php?memberID=" . $x["ID"] . "&teamID=". $_GET['teamID']."&role=coach'>"
                . $x["fullName"] 
                . "</a>"
                . "</li>";
            }
            ?>
        </ul>

        <h3>Players</h3>
	    <ul>

	    <!-- ersätt med php -->
            <?php 
            $player_list = get_team_players(connectDB(), $_GET['teamID']);
            foreach ($player_list as $x){
                echo "<li>"
                            
                . "<a href='teammember.php?memberID=" . $x["ID"] . "&teamID=". $_GET['teamID']."&role=player'>"
                . $x["fullName"] 
                . "</a>"
                . "</li>";
            }
            ?>
        </ul>
        </div>
    </body>
</html>
