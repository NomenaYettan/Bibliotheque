<?php
include("db.php");
session_start();
$error = "";
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    if($email === '' || $password === ''){
        $error = "Veuillez remplir tous les champs.";
    } else {
        $sql = "SELECT * FROM utilisateur WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if($result && $result->num_rows > 0){
            $row = mysqli_fetch_assoc($result);
            if($row['password'] === $password){
                session_regenerate_id(true);
                $_SESSION['id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                if($row['role'] === "Admin"){
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Mot de passe incorrect";
            }
        } else {
            $error = "Email introuvable";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Bibliothèque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY</h1>
        <nav>
            <a href="index.php">Accueil</a> | 
            <a href="login.php" class="active">Connexion</a> | 
            <a href="registre.php">Inscription</a>
        </nav>
    </header>

    <div class="form-container">
        <h2>📝 Connexion</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" class="btn btn-primary">Se Connecter</button>
        </form>

        <div class="text-center mt-20">
            <p>Pas encore inscrit? <a href="registre.php" class="link-secondary">Créer un compte</a></p>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>