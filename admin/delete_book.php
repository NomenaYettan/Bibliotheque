<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] !== "Admin"){
    header("Location: ../login.php");
    exit();
}

include '../db.php';

if(!isset($_GET['idlivre'])){
    header("Location: view_book.php");
    exit();
}

$idlivre = mysqli_real_escape_string($conn, $_GET['idlivre']);

$check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE idlivre='$idlivre'");
if($check){
    $row = mysqli_fetch_assoc($check);
    if($row['total'] > 0){
        echo "<p>Impossible de supprimer ce livre car il est lié à des emprunts existants.</p>";
        echo "<p><a href='view_book.php'>Retour</a></p>";
        exit();
    }
}

$sql = "DELETE FROM livre WHERE idlivre='$idlivre'";
$result = mysqli_query($conn, $sql);
if(!$result){
    echo "Erreur : " . $conn->error;
    exit();
}

header("Location: view_book.php");
exit();
