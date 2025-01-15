<?php
session_start();
function my_session(){
    if (empty($_SESSION['User'])){
        $_SESSION['User'] = "Okänd användare";
    }
    echo  $_SESSION['User']." Session startad, uppgift 9 avklarad!";
}

function kill_session(){
    session_unset();
    session_destroy();
}
?>