<?php
include "db.php";
session_start();
$sql = "select * from livre";
$result = mysqli_query($conn,$sql);
if(!$result){
    echo "error!: {$result->error}";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque SALT IVORY</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY</h1>
        <nav>
            <a href="index.php" class="active">Accueil</a> | 
            <?php if(isset($_SESSION['id'])): ?>
                <a href="dashboard.php">Tableau de bord</a> | 
                <a href="requestcheck.php">Mes demandes</a> | 
                <a href="logout.php">Déconnexion</a>
            <?php else: ?>
                <a href="login.php">Connexion</a> | 
                <a href="registre.php">Inscription</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
        <h2 style="margin-bottom: 20px;">📚 Catalogue de Livres</h2>
        <div class="books-grid">
            <?php 
            while($row = mysqli_fetch_assoc($result)){
                // Compter les emprunts en cours pour ce livre
                $idlivre = $row['idlivre'];
                $sql_emprunts = "SELECT COUNT(*) as nb_emprunts FROM emprunt WHERE idlivre='$idlivre' AND status='emprunté'";
                $result_emprunts = mysqli_query($conn, $sql_emprunts);
                $emprunts = mysqli_fetch_assoc($result_emprunts);
                $nb_emprunts = $emprunts['nb_emprunts'];
                
                // Quantité disponible (toujours >= 0)
                $quantite_disponible = max(0, $row['quantite']);
            ?>
                <div class="book-card">
                    <img src="image/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['titre']); ?>">
                    <h3><?php echo htmlspecialchars($row['titre']); ?></h3>
                    <p><strong>Auteur:</strong> <?php echo htmlspecialchars($row['auteur']); ?></p>
                    <p><strong>ISBN:</strong> <?php echo htmlspecialchars($row['isbn']); ?></p>
                    
                    <div style="background: #f0f0f0; padding: 10px; border-radius: 4px; margin: 10px 0;">
                        <p style="margin: 5px 0;"><strong>📊 Copies disponibles:</strong> <span class="quantity"><?php echo $quantite_disponible; ?></span></p>
                        <?php if($nb_emprunts > 0): ?>
                            <p style="margin: 5px 0; font-size: 13px; color: #e74c3c;"><strong>⏳ En cours d'emprunt:</strong> <?php echo $nb_emprunts; ?> copie(s)</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="book-actions">
                        <?php if(isset($_SESSION['id'])): ?>
                            <?php if($quantite_disponible > 0): ?>
                                <a href="borrow.php?idlivre=<?php echo $row['idlivre']; ?>" class="btn btn-success">📖 Emprunter</a>
                            <?php else: ?>
                                <button class="btn btn-disabled" disabled>❌ Indisponible</button>
                                <?php if($nb_emprunts > 0): ?>
                                    <p style="font-size: 12px; color: #7f8c8d; text-align: center; margin-top: 8px;">
                                        En attente de retour par d'autres utilisateurs
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <a href="return.php?idlivre=<?php echo $row['idlivre']; ?>" class="btn btn-warning">↩️ Retourner</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">Se connecter pour emprunter</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>
