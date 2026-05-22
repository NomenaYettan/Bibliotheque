<?php

session_start();
if($_SESSION['role'] == "Admin"){
    echo "<br>Welcome to admin dashboard";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque</title>
    <style type="text/css">
        .adminnavbar {
            display: flex;
            width: 250px;
            flex-direction: column;
            background-color: green;
        }
        .adminnavbar a {
            color: white;
            text-decoration: none;
        }
        .adminnavbar ul li {
            list-style: none;

        }

    </style>
</head>
<body>
    <nav class= "adminnavbar">
        <ul>
            <li><a href="view_transaction.php">voir tout les liste d'emprunt</a></li>
        </ul>
    </nav>
    <a href="../logout.php" >Deconnecter</a>
</body>
</html>