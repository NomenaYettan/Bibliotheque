<?php
session_start();
include "db.php";

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

// Désactivation du retour direct par les utilisateurs : seul l'Admin peut marquer un emprunt comme retourné
if($_GET && isset($_GET['idlivre'])){
    if(!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin"){
        $error = "Action non autorisée : seul l'administrateur peut marquer un livre comme retourné.";
    } else {
        // Pour les administrateurs, la gestion des retours doit se faire via le panneau d'administration
        $error = "Utilisez le panneau d'administration pour marquer un emprunt comme retourné.";
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
                                <a href="return.php?idlivre=<?php echo $row['idlivre']; ?>" class="btn btn-danger" onclick="return confirm('Confirmer le retour de ce livre?');">↩️ Retourner ce livre</a>
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
