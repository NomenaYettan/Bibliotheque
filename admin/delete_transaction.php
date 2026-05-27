<?php
session_start();
include "../db.php";

if(!isset($_SESSION['id'])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION['role'] !== "Admin"){
    header("Location: ../dashboard.php");
    exit();
}

if(!isset($_GET['idemprunt'])){
    header("Location: view_transaction.php");
    exit();
}

$idemprunt = mysqli_real_escape_string($conn, $_GET['idemprunt']);
$statusCheck = mysqli_query($conn, "SELECT status FROM emprunt WHERE idemprunt='$idemprunt'");
if(!$statusCheck || $statusCheck->num_rows === 0){
    echo "Transaction introuvable.";
    exit();
}

$row = mysqli_fetch_assoc($statusCheck);
if($row['status'] === 'emprunté'){
    echo "Impossible de supprimer cette transaction tant que le livre est encore emprunté. Retournez d'abord le livre.";
    exit();
}

$sql = "DELETE FROM emprunt WHERE idemprunt='$idemprunt'";
$result = mysqli_query($conn, $sql);

if(!$result){
    echo "Erreur : " . $conn->error;
    exit();
}

header("Location: view_transaction.php");
exit();
?>