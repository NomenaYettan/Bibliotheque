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

// Récupérer les emprunts de l'utilisateur avec les informations du livre
$sql = "select e.*, l.titre, l.auteur, l.image from emprunt e 
        join livre l on e.idlivre = l.idlivre 
        where e.id='{$_SESSION['id']}' 
        order by e.dateemprunt DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Demandes - Bibliothèque</title>
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
        <h2>📚 Historique de mes emprunts</h2>
        
        <?php if($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Auteur</th>
                        <th>Date d'emprunt</th>
                        <th>Date de retour</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($row['titre']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($row['auteur']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['dateemprunt'])); ?></td>
                        <td><?php echo ($row['dateretour'] ? date('d/m/Y', strtotime($row['dateretour'])) : '-'); ?></td>
                        <td>
                            <span style="padding: 6px 12px; border-radius: 4px; font-weight: bold; display: inline-block;
                                <?php echo ($row['status'] == 'emprunté' ? 'background-color: #f39c12; color: white;' : 'background-color: #27ae60; color: white;'); ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info" style="margin-top: 30px;">
                <p>Vous n'avez aucun emprunt enregistré.</p>
                <a href="index.php" class="btn btn-primary mt-20">Parcourir les livres</a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>