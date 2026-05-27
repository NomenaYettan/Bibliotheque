<?php
session_start();
include "db.php";

if(!isset($_SESSION['id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== "User"){
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if(isset($_GET['idlivre'])){
    $idlivre = $_GET['idlivre'];
    $user_id = $_SESSION['id'];
    
    // Vérifier que le livre existe et qu'il y a des copies disponibles
    $sql_check = "SELECT quantite FROM livre WHERE idlivre='$idlivre'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if($result_check && $result_check->num_rows > 0){
        $book = mysqli_fetch_assoc($result_check);
        if($book['quantite'] > 0){
            // Vérifier le nombre de demandes et d'emprunts déjà en cours pour ce livre
            $sql_pending = "SELECT COUNT(*) as total FROM emprunt WHERE idlivre='$idlivre' AND status IN ('emprunté','en attente')";
            $result_pending = mysqli_query($conn, $sql_pending);
            $pending = mysqli_fetch_assoc($result_pending);

            if($pending['total'] >= $book['quantite']){
                $error = "Toutes les copies de ce livre sont déjà empruntées ou en attente d'approbation.";
            } else {
                // Calculer la date de retour prévue (10 jours après la date de demande)
                $expectedReturn = date('Y-m-d', strtotime('+10 days'));

                // Insérer la demande sans modifier la quantité du livre
                $sql = "INSERT INTO emprunt (id, idlivre, dateemprunt, dateretour, status) VALUES ('$user_id','$idlivre',CURDATE(),'$expectedReturn','en attente')";
                $result = mysqli_query($conn, $sql);

                if($result){
                    $success = "Demande d'emprunt envoyée. L'administrateur doit encore la confirmer.";
                } else {
                    $error = "Erreur lors de la demande d'emprunt: " . $conn->error;
                }
            }
        }
        else{
            $error = "Ce livre n'est pas disponible pour le moment.";
        }
    }
    else{
        $error = "Livre non trouvé.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emprunt de Livre - Bibliothèque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY</h1>
        <nav>
            <a href="index.php">Accueil</a> | 
            <a href="dashboard.php">Tableau de bord</a> | 
            <a href="requestcheck.php">Mes demandes</a> | 
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>

    <div class="container">
        <div style="max-width: 600px; margin: 50px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <?php if($success): ?>
                <div class="alert alert-success">
                    <h2>✅ <?php echo $success; ?></h2>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="index.php" class="btn btn-primary" style="width: auto; padding: 12px 30px;">Retour au catalogue</a>
                    <a href="requestcheck.php" class="btn btn-success" style="width: auto; padding: 12px 30px; margin-left: 10px;">Mes emprunts</a>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    <h2>❌ Erreur d'emprunt</h2>
                    <p><?php echo $error ?: "Une erreur inconnue s'est produite."; ?></p>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="index.php" class="btn btn-primary" style="width: auto; padding: 12px 30px;">Retour au catalogue</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>