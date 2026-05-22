<?php
session_start();
include "../db.php";
if(isset($_SESSION['id'])){
    if($_SESSION['role']=="Admin"){
        if(isset($_GET['idemprunt'])){
            $idemprunt=$_GET['idemprunt'];
            $sql = "delete from emprunt where id ='$idemprunt'";
$result = mysqli_query($conn,$sql);
if(!$result){
    echo "error!: {$result->error}";
}
else{
header("Location: view_transaction.php");
}
}
}
else{
  header("Location: ../dashboard.php");  
    }
}
else{
    header("Location: ../login.php");
}
?>