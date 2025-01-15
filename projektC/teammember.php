
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
        <?php
        echo "<li>"     
                . "<a href='team.php?teamID=" . $selected_team["ID"]."'>"
                . $selected_team["fullName"]
                . "</a>"
                . "</li>"
                . "<li class='active'>";
        if ($_GET["role"] == "player"){
            echo $player_info["fullName"];
        }
        elseif ($_GET["role"] == "coach"){
            echo $coach_info["fullName"];
        }
        echo "</li>";
        ?>
    </ul>
    <div class="container xs-8">
        <article class="person">
         <!-- denna del behöver skapas med hjälp av php -->
            <?php
                if ($_GET["role"] == "coach"){
                    echo "
                    <h2>". $coach_info["fullName"]."</h2>"
                    . "<p>". $coach_info["info"] . "</p>"
                    . "<ul>"
                    . "<li>BirthDate: ".$coach_info["birthDate"]."</li>"
                    . "<li>BirthPlace: ".$coach_info["birthPlace"]."</li>"
                    . "</ul>";
                } elseif ($_GET["role"] == "player"){
                    echo "
                    <h2>". $player_info["fullName"]."</h2>"
                    . "<p>". $player_info["info"] . "</p>"
                    . "<ul>"
                    . "<li>BirthDate: ".$player_info["birthDate"]."</li>"
                    . "<li>BirthPlace: ".$player_info["birthPlace"]."</li>"
                    . "<li>Weight: ".$player_info["_weight"]."</li>"
                    . "<li>Height: ".$player_info["height"]."</li>"
                    . "<li>Draftyear: ".$player_info["draftYear"]."</li>"
                    . "<li>Alias: ". echo_multivalued($player_info["alias"])."</li>"
                    . "<li>Position: ". echo_multivalued($player_info["position"])."</li>"
                    . "<li>Debute Team(s): ". echo_multivalued($player_info["debute"])."</li>"
                    . "<li>Current Team(s): ". echo_multivalued($player_info["present"])."</li>"
                    . "<li>Previous Team(s): ". echo_multivalued($player_info["previous"])."</li>"
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