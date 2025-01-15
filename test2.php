<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" type="text/css" href="testdesign.css">
        <title>Uppgift 2</title>
    </head>
    
    <body>
        <h1>
        <?php
        //Startar man på denna sida så har session_start() inte körts än, därmed kommer $_SESSION['User] inte kunna deklareras. Session startar när test1 körs.
        $humor = $_GET["humor"];
        switch ($humor) {
            case "glad";
                echo "Solen skiner och himlen är blå!";
                break;
            case "arg";
                echo "Solen skiner för starkt och det är massa måsar!";
                break;
            case "ledsen";
                echo "Solen skiner ej! Himlen är grå!";
                break;
        };
        ?>
        </h1>
        <p>Uppgift 4 fixad! Variabler i URL:en hanterade!</p>
        <form method="POST" action="testAction.php">
            <label for="Uname">Username</label>
            <input type="text" id="Uname" name="Uname">
            <label for="Uemail">Email</label>
            <input type="text" id="Uemail" name="Uemail">
            <input type="submit" value="Submit">
        </form>
        <p>Uppgift 5 avklarad! Formuläret är skapat!</p>
    </body>
</html>
