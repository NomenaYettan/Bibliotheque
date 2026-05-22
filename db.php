<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

$server = "localhost";
$user = "root";
$pass = "";
$dbname = "bibliotheque";
$conn = new mysqli($server, $user, $pass, $dbname);
if(!$conn){
    echo "oopps! : {$conn->connect_error}";
}

?>