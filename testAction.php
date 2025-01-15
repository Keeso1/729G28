<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" type="text/css" href="testdesign.css">
        <title>Uppgift 6</title>
    </head>
    
    <body>
        <h1>Uppgift 6</h1>

        <?php
        require __DIR__ . '/testFormHandler.php';
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = sanitize_input($_POST["Uname"]);
            $email = sanitize_input($_POST["Uemail"]);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                $login_details = array("Username"=>$name, "Email"=>$email);
                echo "hej {$login_details['Username']}!<br>";
                echo "Din E-post är: {$login_details['Email']}";
            } else{
                echo("$email email is not valid");
            }
        }
        ?>
        <p>Uppgift 6 avklarad, formuläret avläst!</p>
        <p>Uppgift 7 avklarad, inmatningskontroll och skydd mot XSS implementerat!</p>
        <p>Uppgift 8 avklarad, funktionen fungerar!</p>
    </body>
</html>