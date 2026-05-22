<?php
include("db.php");
session_start();
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM utilisateur WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if($result->num_rows> 0){
    $row = mysqli_fetch_assoc($result);
    if($row['password'] == $password){
        $_SESSION['id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
    if($row['role'] == "Admin"){
    header("Location: admin/dashboard.php");
    }else {
    header("Location: dashboard.php");}
    exit();
    } else {
    echo "Mot de passe incorrect";
    }
    } else {
    echo "Email introuvable";
    }
}
 ?>
<!DOCTYPE html>
<html>
<?php include "heading.php";?>
<body>
    <div class="registre">
        <h2>Login</h2>
        <form action="login.php" method= "post"> 
        <input type="email" name="email" placeholder="veillez entrer votre e-mail"> <br>
        <input type="password" name="password" placeholder="Entrer votre mot de passe"> <br>
        <input type="text" name="role" value="utilisateur" hidden> <br>
        <button type="submit" >Connecter</button> <br>
        </form>
    </div>
</body>
</html>