<?php
include "db.php";
session_start();

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] == "Admin"){
    header("Location: admin/dashboard.php");
    exit();
}

$user_id = $_SESSION['id'];
$sql = "SELECT nom_utilisateur FROM utilisateur WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Bibliothèque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY</h1>
        <nav>
            <a href="index.php">Accueil</a> | 
            <a href="dashboard.php" class="active">Tableau de bord</a> | 
            <a href="requestcheck.php">Mes demandes</a> | 
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>

    <div class="container">
        <div class="alert alert-info">
            <h2>👋 Bienvenue, <?php echo htmlspecialchars($user['nom_utilisateur']); ?>!</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
                <h3>📚 Mes Emprunts</h3>
                <p>Consultez et gérez vos emprunts de livres</p>
                <a href="requestcheck.php" class="btn btn-primary">Voir mes emprunts</a>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
                <h3>📖 Tous les livres</h3>
                <p>Parcourir la collection complète</p>
                <a href="index.php" class="btn btn-success">Voir les livres</a>
            </div>

            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
                <h3>⚙️ Profil</h3>
                <p>Gérer vos informations personnelles</p>
                <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>