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

if(!isset($_GET['idemprunt']) || !isset($_GET['action'])){
    header("Location: requests.php");
    exit();
}

$idemprunt = mysqli_real_escape_string($conn, $_GET['idemprunt']);
$action = $_GET['action'];

$sql = "SELECT e.*, l.quantite FROM emprunt e JOIN livre l ON e.idlivre = l.idlivre WHERE e.idemprunt = '$idemprunt' AND e.status = 'en attente'";
$result = mysqli_query($conn, $sql);

if(!$result || $result->num_rows === 0){
    header("Location: requests.php");
    exit();
}

$request = mysqli_fetch_assoc($result);

if($action === 'approve'){
    if($request['quantite'] <= 0){
        echo "Impossible de valider : aucune copie disponible pour le livre demandé.";
        exit();
    }

    $sqlUpdate = "UPDATE emprunt SET status='emprunté' WHERE idemprunt='$idemprunt'";
    $resultUpdate = mysqli_query($conn, $sqlUpdate);
    if($resultUpdate){
        mysqli_query($conn, "UPDATE livre SET quantite = quantite - 1 WHERE idlivre='{$request['idlivre']}'");
    }
} elseif($action === 'deny'){
    mysqli_query($conn, "DELETE FROM emprunt WHERE idemprunt='$idemprunt'");
}

header("Location: requests.php");
exit();
