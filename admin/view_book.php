<?php
session_start();
if(isset($_SESSION['id'])){
    if($_SESSION['role']=="Admin"){
        include "../db.php";
$sql = "select * from livre";
$result = mysqli_query($conn,$sql);
if(!$result){
    echo "error!: {$result->error}";
}
else{

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliotheque</title>
    <style type="text/css">
        table{
            border: none;
            width:100%;
        }
        tr,th{
            border-bottom: 10px solid green;
        }
        td{
            text-align:center;
            border:none;
            background-color: gray;
            
        }
    
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Genre</th>
                <th>Image</th>
                <th>Qantité</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while($row = mysqli_fetch_assoc($result)){
            ?>
            <tr>
                <td><?php echo "{$row['titre']}" ?></td>
                <td><?php echo "{$row['auteur']}" ?></td>
                <td><?php echo "{$row['isbn']}" ?></td>
                <td> <img src="../image/<?php echo "{$row['image']}"?>" width="50" > </td>
                <td> <?php echo "{$row['quantite']}" ?> </td>
            </tr>
            <?php
            }
            ?>
        </tbody>

    </table>
</body>
</html>