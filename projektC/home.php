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
            <h1>NFL TEAMS</h1>
            <p>The teams of the NFL, click on a team for their full roster</p>
        </header>
    </div>
    <div class="container xs-8">
        <table class="table table-striped table-hover ">
            <thead>
            <tr>
                <!-- Lägger till kolumnerna och sätter variabeln som används för att sortera efter. -->
                <th><a href="?sortBy=fullName">Team Name</a></th>
                <th><a href="?sortBy=creationDate">Start Date</a></th>
                <th><a href="?sortBy=ownerName">Owner</a></th>
            </tr>
            </thead>

            <tbody>
            <!-- Hämtar in alla teams från databasen, och skapar en rad för varje team-->
            <?php
                require __DIR__ . '/connectdb.php';
                $safeVar = array("fullName", "creationDate", "ownerName");
                function update_page($sortBy){
                    $teams_list = get_teams_list(connectDB(), $sortBy);
                    foreach ($teams_list as $x){
                        echo "<tr>"
                        
                        . "<td>" 
                        . "<a href='team.php?teamID=" . $x["ID"] . "'>"
                        . $x["fullName"] 
                        . "</a>"
                        . "</td>"
                        . "<td>" . $x["creationDate"] . "</td>"
                        . "<td>" . $x["ownerName"] . "</td>"
                        
                        . "</tr>";
                    }
                }
                // kollar så att sortBy har ett värde, och om den är en av värdena som man ska kunna sortera efter.
                //Om den finns och är ett säkert värde sätter vi variabeln till det. Annars till fullName.
                $sortBy = (isset($_GET['sortBy']) && in_array($_GET['sortBy'], $safeVar)) ? $_GET['sortBy'] : 'fullName';
                update_page($sortBy);
            ?>
            </tbody>
        </table>
        </div>
    </body>
</html>

<!-- https://php-web.edu.liu.se/~klabe908/729G28/projektC/home.php -->