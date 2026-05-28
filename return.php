<?php
session_start();
include "db.php";

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

// Traiter le retour uniquement si l'administrateur soumet une transaction valide
if($_GET && isset($_GET['idemprunt'])){
    $idemprunt = mysqli_real_escape_string($conn, $_GET['idemprunt']);
    if(!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin"){
        $error = "Action non autorisée : seul l'administrateur peut marquer un livre comme retourné.";
    } else {
        $sqlReturn = "SELECT e.idemprunt, e.idlivre, e.status, l.titre FROM emprunt e JOIN livre l ON e.idlivre = l.idlivre WHERE e.idemprunt='$idemprunt' AND e.status='emprunté'";
        $returnResult = mysqli_query($conn, $sqlReturn);
        if($returnResult && $returnResult->num_rows > 0){
            $returnRow = mysqli_fetch_assoc($returnResult);
            $bookId = $returnRow['idlivre'];
            mysqli_begin_transaction($conn);
            $sqlUpdate = "UPDATE emprunt SET status='rendu', dateretour=CURDATE() WHERE idemprunt='$idemprunt' AND status='emprunté'";
            $updateResult = mysqli_query($conn, $sqlUpdate);
            if($updateResult && $conn->affected_rows > 0){
                $qtyResult = mysqli_query($conn, "UPDATE livre SET quantite = quantite + 1 WHERE idlivre='$bookId'");
                if($qtyResult && $conn->affected_rows > 0){
                    mysqli_commit($conn);
                    $success = "Le livre a été marqué comme rendu et la quantité a été mise à jour.";
                } else {
                    mysqli_rollback($conn);
                    $error = "Erreur lors de la mise à jour du stock.";
                }
            } else {
                mysqli_rollback($conn);
                $error = "Impossible de marquer ce livre comme rendu.";
            }
        } else {
            $error = "Aucun emprunt actif trouvé pour ce retour.";
        }
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
                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                                <a href="return.php?idemprunt=<?php echo $row['idemprunt']; ?>" class="btn btn-danger" onclick="return confirm('Confirmer le retour de ce livre?');">↩️ Retourner ce livre</a>
                            <?php else: ?>
                                <span style="color: #7f8c8d;">En attente de retour (contacter l'administrateur)</span>
                            <?php endif; ?>
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
