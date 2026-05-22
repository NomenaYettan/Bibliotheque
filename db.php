<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

$server = "localhost";
$user = "root";
$pass = "";
$dbname = "bibliotheque";
$conn = new mysqli($server, $user, $pass, $dbname);
if($conn->connect_errno){
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

?>