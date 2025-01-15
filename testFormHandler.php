<?php
require __DIR__ . '/session.php';
function sanitize_input($value){
    if (empty($value)){
        return "";
    } else {
        if ($value===$_POST["Uname"]){
            $value = trim($value);
            $value = stripslashes($value);
            $value = htmlspecialchars($value);
            $_SESSION['User'] = $value;
            return $value;
        } else {
            $value = trim($value);
            $value = stripslashes($value);
            $value = htmlspecialchars($value);
            return $value;
        }
        
    }
}
