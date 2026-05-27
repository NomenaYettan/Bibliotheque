<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "Admin"){
    header("Location: ../login.php");
    exit();
}

include "../db.php";
$sql = "SELECT e.idemprunt, e.id, u.nom_utilisateur, e.idlivre, l.titre, e.dateemprunt, e.dateretour, e.status
        FROM emprunt e
        JOIN utilisateur u ON e.id = u.id
        JOIN livre l ON e.idlivre = l.idlivre
        WHERE e.status = 'en attente'
        ORDER BY e.dateemprunt DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes d'emprunt - Admin</title>
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
            <a href="requests.php" class="active">Demandes d'emprunt</a> | 
            <a href="view_transaction.php">Voir les emprunts</a> | 
            <a href="../logout.php">Déconnexion</a>
        </nav>
    </header>

    <div class="admin-layout">
        <div class="admin-sidebar">
            <ul>
                <li><a href="view_book.php">📚 Gérer les livres</a></li>
                <li><a href="add_book.php">➕ Ajouter un livre</a></li>
                <li><a href="manage_users.php">👥 Gérer les utilisateurs</a></li>
                <li><a href="requests.php">🕒 Demandes d'emprunt</a></li>
                <li><a href="view_transaction.php">📋 Transactions</a></li>
                <li><a href="../logout.php">🚪 Déconnexion</a></li>
            </ul>
        </div>

        <div class="admin-content">
            <h2>🕒 Demandes d'emprunt en attente</h2>

            <?php if($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID Demande</th>
                            <th>Utilisateur</th>
                            <th>Livre</th>
                            <th>Date de demande</th>
                            <th>Date de retour prévue</th>
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
                            <td style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="handle_request.php?idemprunt=<?php echo $row['idemprunt']; ?>&action=approve" class="btn btn-success" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Valider cette demande d\'emprunt ?');">✔️ Valider</a>
                                <a href="handle_request.php?idemprunt=<?php echo $row['idemprunt']; ?>&action=deny" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Refuser cette demande d\'emprunt ?');">✖️ Refuser</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">Aucune demande d'emprunt en attente.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>
