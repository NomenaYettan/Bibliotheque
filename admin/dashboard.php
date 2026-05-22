<?php
session_start();
if(!isset($_SESSION['id'])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION['role'] != "Admin"){
    header("Location: ../dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - Bibliothèque</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY - ADMINISTRATION</h1>
        <nav>
            <a href="dashboard.php" class="active">Tableau de bord</a> | 
            <a href="view_book.php">Voir les livres</a> | 
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
            <h2>📊 Tableau de Bord Administrateur</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 30px;">
                <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #3498db; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3>📚 Gestion des Livres</h3>
                    <p style="color: #7f8c8d; margin: 10px 0;">Ajouter, modifier ou supprimer des livres du catalogue</p>
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <a href="view_book.php" class="btn btn-primary" style="flex: 1;">Voir tous les livres</a>
                        <a href="add_book.php" class="btn btn-success" style="flex: 1;">Ajouter</a>
                    </div>
                </div>

                <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #27ae60; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3>👥 Gestion des Utilisateurs</h3>
                    <p style="color: #7f8c8d; margin: 10px 0;">Consulter et gérer les comptes utilisateurs</p>
                    <a href="manage_users.php" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Gérer les utilisateurs</a>
                </div>

                <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #f39c12; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h3>📋 Transactions</h3>
                    <p style="color: #7f8c8d; margin: 10px 0;">Suivi des emprunts et retours de livres</p>
                    <a href="view_transaction.php" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Voir les transactions</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>