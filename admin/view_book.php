<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "Admin"){
    header("Location: ../login.php");
    exit();
}

include "../db.php";
$sql = "select * from livre";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY - ADMINISTRATION</h1>
        <nav>
            <a href="dashboard.php">Tableau de bord</a> | 
            <a href="view_book.php" class="active">Voir les livres</a> | 
            <a href="add_book.php">Ajouter un livre</a> | 
            <a href="manage_users.php">Gérer les utilisateurs</a> | 
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
                <li><a href="view_transaction.php">📋 Transactions</a></li>
                <li><a href="../logout.php">🚪 Déconnexion</a></li>
            </ul>
        </div>

        <div class="admin-content">
            <h2>📚 Tous les Livres</h2>
            
            <a href="add_book.php" class="btn btn-success" style="margin-bottom: 20px;">➕ Ajouter un nouveau livre</a>

            <?php if($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>ISBN</th>
                            <th>Image</th>
                            <th>Quantité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['idlivre']; ?></td>
                            <td><?php echo htmlspecialchars($row['titre']); ?></td>
                            <td><?php echo htmlspecialchars($row['auteur']); ?></td>
                            <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                            <td><img src="../image/<?php echo htmlspecialchars($row['image']); ?>" width="50" style="border-radius: 4px;"></td>
                            <td style="text-align: center; font-weight: bold; color: #27ae60;"><?php echo intval($row['quantite']); ?></td>
                            <td>
                                <a href="update_book.php?idlivre=<?php echo $row['idlivre']; ?>" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px;">✏️ Modifier</a>
                                <a href="delete_book.php?idlivre=<?php echo $row['idlivre']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Confirmer la suppression?');">🗑️ Supprimer</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">Aucun livre trouvé dans la base de données.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>