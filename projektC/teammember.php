
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
            <?php
            require __DIR__ . '/connectdb.php';
            if ($_GET["role"] == "coach"){
                $coach_info = get_coach_info(connectdb(), $_GET["memberID"]);
                $selected_team = get_team_info(connectDB(), $_GET['teamID']);

                echo "
                <h1>". $selected_team["fullName"]."</h1>"
                . "<p>Coach</p>
                ";
            } elseif ($_GET["role"] == "player"){
                $player_info = get_player_info(connectdb(), $_GET["memberID"]);
                $selected_team = get_team_info(connectDB(), $_GET['teamID']);

                echo "
                <h1>". $selected_team["fullName"]."</h1>"
                . "<p>Player</p>
                ";
            } else {
                echo "
                    <h1>TEAM NAME</h1>
                    <p>ROLE IN TEAM</p>
                    ";
            }
            ?>
        </header>
    </div>
    <ul class="breadcrumb">
        <li><a href="home.php">Home</a></li>
        <li><a href="<!-- beöhver fyllas i med vilket lag det gäller-->">Team</a></li>
        <li class="active">Player</li>
    </ul>
    <div class="container xs-8">
        <article class="person">
         <!-- denna del behöver skapas med hjälp av php -->
            <?php
                if ($_GET["role"] == "coach"){
                    echo "
                    <h1>". $coach_info["fullName"]."</h1>"
                    . "<p>". $coach_info["info"] . "</p>
                    ";
                } elseif ($_GET["role"] == "player"){
                    echo "
                    <h1>". $player_info["fullName"]."</h1>"
                    . "<p>". $player_info["info"] . "</p>"
                    . "<ul>"
                    . "<li>BirthDate: ".$player_info["birthDate"]."</li>"
                    . "<li>BirthPlace: ".$player_info["birthPlace"]."</li>"
                    . "<li>Weight: ".$player_info["_weight"]."</li>"
                    . "<li>Height: ".$player_info["height"]."</li>"
                    . "<li>Draftyear: ".$player_info["draftYear"]."</li>"
                    . "<li>Alias: ". echo_multivalued($player_info["alias"])."</li>"
                    . "<li>Position: ". echo_multivalued($player_info["position"])."</li>"
                    . echo_team_multivalued($player_info["team_ID"])
                    . "</ul>";
                } else {
                    echo "
                        <ul>
                            <li>BIRTH DATE</li>
                            <li>BIRTH PLACE</li>
                            <li>WEIGHT</li>
                            <li>HEIGHT</li>
                            <li>DRAFT YEAR</li>
                            <li>ALIAS</li>
                            <li>POSITION</li>
                            <li>PREVIOUS TEAMS</li>
                            <li>DEBUTE TEAM</li>
                        </ul>
                        ";
                }
            ?>
        <!-- __________________________________________ -->
        </article>
    </div>
    </body>
</html>