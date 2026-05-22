<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "Admin"){
    header("Location: ../login.php");
    exit();
}

include "../db.php";
$sql = "select e.idemprunt, e.id, u.nom_utilisateur, e.idlivre, l.titre, e.dateemprunt, e.dateretour, e.status 
        from emprunt e 
        join utilisateur u on e.id = u.id 
        join livre l on e.idlivre = l.idlivre 
        order by e.dateemprunt DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY - ADMINISTRATION</h1>
        <nav>
            <a href="dashboard.php">Tableau de bord</a> | 
            <a href="view_book.php">Voir les livres</a> | 
            <a href="add_book.php">Ajouter un livre</a> | 
            <a href="manage_users.php">Gérer les utilisateurs</a> | 
            <a href="view_transaction.php" class="active">Voir les emprunts</a> | 
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </header>

    <div class="admin-layout">
        <div class="admin-sidebar">
            <ul>
                <li><a href="view_book.php">📚 Gérer les livres</a></li>
                <li><a href="add_book.php">➕ Ajouter un livre</a></li>
                <li><a href="manage_users.php">👥 Gérer les utilisateurs</a></li>
                <li><a href="view_transaction.php">📋 Transactions</a></li>
                <li><a href="../logout.php">🚪 Déconnexion</a></li>
            </ul>
        </div>

        <div class="admin-content">
            <h2>📋 Historique des Transactions</h2>

            <?php if($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Emprunt</th>
                            <th>Utilisateur</th>
                            <th>Livre</th>
                            <th>Date d'emprunt</th>
                            <th>Date de retour</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['idemprunt']; ?></td>
                            <td><?php echo htmlspecialchars($row['nom_utilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($row['titre']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['dateemprunt'])); ?></td>
                            <td><?php echo ($row['dateretour'] ? date('d/m/Y', strtotime($row['dateretour'])) : '-'); ?></td>
                            <td>
                                <span style="padding: 4px 8px; border-radius: 4px; font-weight: bold;
                                    <?php echo ($row['status'] == 'emprunté' ? 'background-color: #f39c12; color: white;' : 'background-color: #27ae60; color: white;'); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="update_transaction.php?idemprunt=<?php echo $row['idemprunt']; ?>" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px;">✏️ Modifier</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">Aucune transaction trouvée.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>