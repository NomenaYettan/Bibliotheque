<?php
include("db.php");
$success = "";
$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $nom = mysqli_real_escape_string($conn, trim($_POST['nom']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $role = "User";

    if($nom === '' || $email === '' || $password === ''){
        $error = "Veuillez remplir tous les champs.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM utilisateur WHERE email='$email'");
        if($check && $check->num_rows > 0){
            $error = "Un compte avec cet email existe déjà.";
        } else {
            $sql = "INSERT INTO utilisateur(nom_utilisateur, email, password, role) VALUES ('$nom','$email','$password','$role')";
            $result = mysqli_query($conn, $sql);

            if(!$result){
                $error = "Erreur : " . $conn->error;
            } else {
                $success = "Inscription réussie! Vous pouvez maintenant vous connecter.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription - Bibliothèque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY</h1>
        <nav>
            <a href="index.php">Accueil</a> | 
            <a href="login.php">Connexion</a> | 
            <a href="registre.php" class="active">Inscription</a>
        </nav>
    </header>

    <div class="form-container">
        <h2>📋 Inscription</h2>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="text-center">
                <a href="login.php" class="btn btn-primary">Aller à la Connexion</a>
            </div>
        <?php else: ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="registre.php" method="POST">
                <input type="text" name="nom" placeholder="Nom complet" required>
                <input type="email" name="email" placeholder="Adresse e-mail" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </form>

            <div class="text-center mt-20">
                <p>Déjà inscrit? <a href="login.php" class="link-secondary">Se connecter</a></p>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>