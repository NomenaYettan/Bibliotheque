<?php
session_start();
if(isset($_SESSION['id'])){
    if($_SESSION['role']=="Admin"){      
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $titre = $_POST['titre'];
            $auteur = $_POST['auteur'];
            $isbn = $_POST['isbn'];
            $image = $_FILES['image']['name'];
            $quantite = $_POST['quantite'];
            include '../db.php';
            $sql = "INSERT INTO livre(titre, auteur, isbn, image, quantite) VALUES ('$titre', '$auteur', '$isbn', '$image',
             '$quantite')";
            $result = mysqli_query($conn,$sql);
            if (!$result) {
                echo "Erreur : " . $conn->error;    
            }
            else {
                $image_location = $_FILES['image']['tmp_name'];
                $upload_location = "../image/";
                move_uploaded_file($image_location, $upload_location.$image);
                echo "Livre Ajouter!";
            }
        } 
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bibliotheque</title>
    <style type="text/css">
/* ajout livre*/

body{
    font-family:arial, sans-serif;
    background: linear-gradient(135deg,#4facfe, #00f2fe);
    display:flex;
    justify-content:center;
    align-items:center;
    height:90vh;
}

.admin_add_book{
    background: #fff;
    padding:30px;
    border-radius:12px;
    width: 350px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
.admin_add_book h2 {
    text-align:center;
    margin-bottom:15px;
}
.admin_add_book input{
    padding: 20px;
    width: 88%;
    margin: 5px 0;
    border-radius: 8px; 
    border:1px solid #4facfe;
    outline:none;
    transition: 0.3s;
}
.admin_add_book input:focus{
    border-color:#4facfe;
    box-shadow:0 0 5px rgba(79,172,254,0.5);
}
.admin_add_book button{
    padding: 12px;
    width: 100%;
    background: #4facfe;
    margin-top: 10px ;
    border-radius: 8px; 
    border:none;
    font-size:16px;
    cursor:pointer;
    transition: 0.3s;
    color: white;    
}
.admin_add_book button:hover{
    background: #007bff;
    transform:scale(1.03);
}
.file{
    
    padding:8px;
    background:#f5f5f5 ;

}
    </style>
</head>
<body>
    <div class="admin_add_book">
        <h2>Ajouter des livres</h2> <br>
        <form action="add_book.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="titre" placeholder="Entrer la titre du livre">
            <input type="text" name="auteur" placeholder="Entrer l'auteur du livre">
            <input type="text" name="isbn" placeholder="Entrer l'isbn du livre">
            <input class="file" type="file" name="image" placeholder='Inserer la photo du livre'>
            <input type="text" name="quantite" placeholder=" Qantité ou nombre du livre">
            <button type="submit">Ajouter</button>
        </form>
    </div>

</body>
</html>