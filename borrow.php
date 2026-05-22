<?php
session_start();
include "db.php";
if($_GET['idlivre']){
    $idlivre = $_GET['idlivre'];
}
if(isset($_SESSION['id'])){
    $id =$_SESSION['id'];
    
    if($_SESSION['role'] == "User"){
        $sql = "insert into emprunt (id, idlivre, dateemprunt, etat) values ('$id','$idlivre',CURDATE(),'emprunté')";
        $result = mysqli_query($conn,$sql);
        if($result){
            $sql2 = "update livre set quantite = quantite-1 where idlivre = '$idlivre'";
            $result2 = mysqli_query($conn,$sql2);
            echo "votre demande est bien envoyée <a href='index.php'> Retour </a>";
        }
        else{
            echo "erreur!: {$conn->error}";
        }
    }
    else{
        header("Location: admin/dashboard.php");
    }
}
else{
    header("Location: login.php");
}

?>