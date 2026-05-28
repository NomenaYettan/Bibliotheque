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

$idemprunt = $conn->real_escape_string($_GET['idemprunt']);
$action = $_GET['action'];

$sql = "SELECT e.*, l.quantite FROM emprunt e JOIN livre l ON e.idlivre = l.idlivre WHERE e.idemprunt = '$idemprunt' AND e.status = 'en attente'";
$result = $conn->query($sql);

if(!$result || $result->num_rows === 0){
    header("Location: requests.php");
    exit();
}

$request = mysqli_fetch_assoc($result);

if($action === 'approve'){
    if($request['quantite'] <= 0){
        header("Location: requests.php?error=nostock");
        exit();
    }

    $bookId = $request['idlivre'];
    $beforeQty = intval($request['quantite']);

    $conn->begin_transaction();

    $sqlUpdate = "UPDATE emprunt SET status='emprunté' WHERE idemprunt='$idemprunt' AND status='en attente'";
    $resultUpdate = $conn->query($sqlUpdate);

    if(!$resultUpdate || $conn->affected_rows === 0){
        $conn->rollback();
        $logLine = date('Y-m-d H:i:s') . " | approve_failed_update | idemprunt=$idemprunt | idlivre=$bookId | before_qty={$beforeQty}\n";
        @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
        header("Location: requests.php?error=updatefail");
        exit();
    }
    $qtyUpdate = $conn->query("UPDATE livre SET quantite = quantite - 1 WHERE idlivre='$bookId' AND quantite > 0");
    if(!$qtyUpdate || $conn->affected_rows === 0){
        $conn->rollback();
        $logLine = date('Y-m-d H:i:s') . " | approve_failed_no_stock_to_decrement | idemprunt=$idemprunt | idlivre=$bookId | before_qty={$beforeQty}\n";
        @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
        header("Location: requests.php?error=nostock");
        exit();
    }
    $conn->commit();
    $logLine = date('Y-m-d H:i:s') . " | approve_committed | idemprunt=$idemprunt | idlivre=$bookId | before_qty={$beforeQty}\n";
    @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
} elseif($action === 'deny'){
    $conn->query("DELETE FROM emprunt WHERE idemprunt='$idemprunt' AND status='en attente'");
}

header("Location: requests.php");
exit();
