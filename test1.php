<?php
require __DIR__ . '/session.php';
my_session()
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" type="text/css" href="testdesign.css">
        <title>Uppgift 1</title>
    </head>
    
    <body>
        <h1>Uppgift 1 avklarad</h1>
        <p>Här är ett HTML-dokument!</p>
        <ul>
            <li><a href="test2.php?humor=glad">Jag är glad!</a></li>
            <li><a href="test2.php?humor=arg">Jag är arg!</a></li>
            <li><a href="test2.php?humor=ledsen">Jag är ledsen!</a></li>
        </ul>
        <form method="POST" action="<?php kill_session() ?>">
            <input type="submit" value="kill session">
        </form>
        <p>Nu är uppgift 3 avklarad, CSS gjorde sidan så fin så fin</p>
        <p>Nu är uppgift 10 avklarad. Session hantering.</p>
        <p>Nu är uppgift 11 avklarad. Session avslutad.</p>
    </body>
</html>