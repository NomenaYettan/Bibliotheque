<?php
include("db.php");
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $sql ="INSERT INTO utilisateur(nom_utilisateur, email, password,role) VALUES ('$nom','$email','$password','$role') ";
    $result = mysqli_query($conn,$sql);
    if(!$result){
        echo "Error! : {$result->error}"; 
    }
    else{
        echo "enregistrement réussi";
    }
    }
?>
<!DOCTYPE html>
<html>
<?php include "heading.php";?>
<body>
    <div class="registre">
        <h2>Inscription</h2>
        <form action="registre.php" method= "post"> 
        <input type="text" name="nom" placeholder="veillez entrer votre nom"> <br>
        <input type="email" name="email" placeholder="veillez entrer votre e-mail"> <br>
        <input type="password" name="password" placeholder="Entrer votre mot de passe"> <br>
        <input type="text" name="role" value="User" > <br>
        <button type="submit" >S'inscrire</button> <br>
        </form>
    </div>
</body>
</html>