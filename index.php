<?php
include "db.php";
session_start();
$sql = "select * from livre";
$result = mysqli_query($conn,$sql);
if(!$result){
    echo "error!: {$result->error}";
}

$canBorrow = true;
$borrowRestriction = '';
$borrowSummary = '';
$pendingBooks = [];
if(isset($_SESSION['id']) && isset($_SESSION['role']) && $_SESSION['role'] === "User"){
    $user_id = $_SESSION['id'];
    $activeRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE id='$user_id' AND LOWER(TRIM(status)) LIKE 'emprunt%'");
    $activeCount = ($activeRes && $activeRes->num_rows > 0) ? intval(mysqli_fetch_assoc($activeRes)['total']) : 0;
    $pendingRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM emprunt WHERE id='$user_id' AND (LOWER(TRIM(status)) LIKE 'en attente%' OR status='' OR status IS NULL)");
    $pendingCount = ($pendingRes && $pendingRes->num_rows > 0) ? intval(mysqli_fetch_assoc($pendingRes)['total']) : 0;

    $pendingListRes = mysqli_query($conn, "SELECT idlivre FROM emprunt WHERE id='$user_id' AND (LOWER(TRIM(status)) LIKE 'en attente%' OR status='' OR status IS NULL)");
    if($pendingListRes){
        while($rowPending = mysqli_fetch_assoc($pendingListRes)){
            $pendingBooks[] = intval($rowPending['idlivre']);
        }
    }

    $totalRequests = $activeCount + $pendingCount;
    $borrowSummary = "Vous avez $activeCount emprunt(s) en cours et $pendingCount demande(s) en attente.";

    if($activeCount > 0){
        $canBorrow = false;
        $borrowRestriction = "Vous avez encore un emprunt non rendu. Retournez-le avant de demander un nouveau livre.";
    } elseif($totalRequests >= 5){
        $canBorrow = false;
        $borrowRestriction = "Vous avez atteint la limite de 5 livres demandés ou empruntés.";
    }
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
        <?php if($borrowSummary): ?>
            <div class="alert alert-info" style="margin-bottom: 20px;">
                <?php echo htmlspecialchars($borrowSummary); ?>
                <?php if(!$canBorrow): ?>
                    <br><?php echo htmlspecialchars($borrowRestriction); ?>
                <?php else: ?>
                    <br>Vous pouvez demander jusqu'à 5 livres en même temps.
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="books-grid">
            <?php 
            while($row = mysqli_fetch_assoc($result)){
                // Compter les emprunts en cours pour ce livre
                $idlivre = $row['idlivre'];
                $sql_emprunts = "SELECT COUNT(*) as nb_emprunts FROM emprunt WHERE idlivre='$idlivre' AND status='emprunté'";
                $result_emprunts = mysqli_query($conn, $sql_emprunts);
                $emprunts = mysqli_fetch_assoc($result_emprunts);
                $nb_emprunts = intval($emprunts['nb_emprunts']);

                // Quantité disponible stockée dans la table livre
                $quantite_disponible = max(0, intval($row['quantite']));
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
                            <?php $alreadyRequested = in_array($row['idlivre'], $pendingBooks); ?>
                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'User' && !$canBorrow): ?>
                                <button class="btn btn-disabled" disabled>❌ <?php echo htmlspecialchars($borrowRestriction); ?></button>
                            <?php elseif($alreadyRequested): ?>
                                <button class="btn btn-disabled" disabled>❌ Vous avez déjà demandé ce livre</button>
                            <?php elseif($quantite_disponible > 0): ?>
                                <a href="borrow.php?idlivre=<?php echo $row['idlivre']; ?>" class="btn btn-success">📖 Emprunter</a>
                            <?php else: ?>
                                <button class="btn btn-disabled" disabled>❌ Indisponible</button>
                                <?php if($nb_emprunts > 0): ?>
                                    <p style="font-size: 12px; color: #7f8c8d; text-align: center; margin-top: 8px;">
                                        En attente de retour par d'autres utilisateurs
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                <a href="admin/view_transaction.php" class="btn btn-warning">↩️ Gérer les retours</a>
                            <?php else: ?>
                                <span style="color: #7f8c8d; font-size: 13px;">Retour uniquement via l'administrateur</span>
                            <?php endif; ?>
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
