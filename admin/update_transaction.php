<?php
session_start();
include "../db.php";
if(isset($_SESSION['id'])){
    if($_SESSION['role']=="Admin"){
        if(isset($_GET['idemprunt'])){
            $idemprunt=$_GET['idemprunt'];
        }
        if(isset($_POST['submit'])){
            $dateretour= $_POST['dateretour'];
            $sql = "update emprunt set dateretour='$dateretour' where id='$idemprunt'";
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliotheque</title>
</head>
<body>
    <form action="update_transaction.php?idemprunt=<?php echo $idemprunt;?>" method="post">
        <input type="text" name="dateretour" required placeholder="date-formate: 2026-05-10">
        <input type="submit" name="submit" value="update">
    </form>
</body>
</html>