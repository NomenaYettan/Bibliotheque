<?php
session_start();
include "../db.php";
if(isset($_SESSION['id'])){
    if($_SESSION['role']=="Admin"){
        if(isset($_GET['id'])){
            $id = mysqli_real_escape_string($conn, $_GET['id']);
            $sql = "DELETE FROM utilisateur WHERE id='$id'";
            $result = mysqli_query($conn, $sql);
            if(!$result){
                echo "error!: " . $conn->error;
            } else {
                header("Location: manage_users.php");
                exit();
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