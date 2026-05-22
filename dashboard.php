<?php
include "db.php";
echo " <br>welcome to dashboard <br>";
session_start();
if($_SESSION['role'] == "User"){
        echo "vous êtes utilisateur";
    }
    else{
        header("Location: admin/dashboard.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <a href="requestcheck.php"> request update</a>
    <a href="logout.php">Deconnecter</a>
</body>
</html>