<?php
session_start();
include "db.php";

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if($_GET && isset($_GET['idlivre'])){
    $idlivre = $_GET['idlivre'];
    $user_id = $_SESSION['id'];
    
    // Vérifier que l'emprunt existe et appartient à l'utilisateur
    $sql_check = "SELECT * FROM emprunt WHERE id='$user_id' AND idlivre='$idlivre' AND status='emprunté'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if($result_check && $result_check->num_rows > 0){
        // Mettre à jour le statut de l'emprunt
        $sql_update = "UPDATE emprunt SET status='retourné', dateretour=CURDATE() WHERE id='$user_id' AND idlivre='$idlivre'";
        $result_update = mysqli_query($conn, $sql_update);
        
        if($result_update){
            // Augmenter la quantité du livre
            $sql_qty = "UPDATE livre SET quantite=quantite+1 WHERE idlivre='$idlivre'";
            $result_qty = mysqli_query($conn, $sql_qty);
            
            $success = "Livre retourné avec succès!";
        } else {
            $error = "Erreur lors de la mise à jour du retour.";
        }
    } else {
        $error = "Cet emprunt n'existe pas ou n'a pas pu être trouvé.";
    }
}

// Récupérer les emprunts actuels de l'utilisateur
$sql = "SELECT e.*, l.titre, l.image FROM emprunt e 
        JOIN livre l ON e.idlivre=l.idlivre 
        WHERE e.id='{$_SESSION['id']}' AND e.status='emprunté'
        ORDER BY e.dateemprunt DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retourner un livre - Bibliothèque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY</h1>
        <nav>
            <a href="index.php">Accueil</a> | 
            <a href="dashboard.php">Tableau de bord</a> | 
            <a href="requestcheck.php" class="active">Mes demandes</a> | 
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>

    <div class="container">
        <h2>📚 Retourner un Livre</h2>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">Vos emprunts actuels:</h3>
        
        <?php if($result && $result->num_rows > 0): ?>
            <div class="books-grid">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="book-card">
                        <img src="image/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['titre']); ?>">
                        <h3><?php echo htmlspecialchars($row['titre']); ?></h3>
                        <p><strong>Emprunté le:</strong> <?php echo date('d/m/Y', strtotime($row['dateemprunt'])); ?></p>
                        <p><strong>Statut:</strong> <span style="color: #27ae60; font-weight: bold;">En cours</span></p>
                        <div class="book-actions">
                            <a href="return.php?idlivre=<?php echo $row['idlivre']; ?>" class="btn btn-danger" onclick="return confirm('Confirmer le retour de ce livre?');">↩️ Retourner ce livre</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p>Vous n'avez aucun livre emprunté actuellement.</p>
                <a href="index.php" class="btn btn-primary mt-20">Parcourir les livres</a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>
