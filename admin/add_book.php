<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "Admin"){
    header("Location: ../login.php");
    exit();
}

$success = "";
$error = "";

include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = mysqli_real_escape_string($conn, trim($_POST['titre']));
    $auteur = mysqli_real_escape_string($conn, trim($_POST['auteur']));
    $isbn = mysqli_real_escape_string($conn, trim($_POST['isbn']));
    $quantite = intval($_POST['quantite']);
    $image = basename($_FILES['image']['name']);

    if($titre === '' || $auteur === '' || $isbn === '' || $quantite < 1){
        $error = "Veuillez remplir tous les champs correctement.";
    } elseif(!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = "Veuillez sélectionner une image valide pour le livre.";
    } else {
        $upload_location = "../image/" . $image;
        if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_location)){
            $sql = "INSERT INTO livre(titre, auteur, isbn, image, quantite) VALUES ('$titre', '$auteur', '$isbn', '$image', '$quantite')";
            $result = mysqli_query($conn, $sql);
            if(!$result){
                $error = "Erreur : " . $conn->error;
            } else {
                $success = "Livre ajouté avec succès!";
            }
        } else {
            $error = "Erreur lors de l'upload de l'image.";
        }
    }
} 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un livre - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY - ADMINISTRATION</h1>
        <nav>
            <a href="dashboard.php">Tableau de bord</a> | 
            <a href="view_book.php">Voir les livres</a> | 
            <a href="add_book.php" class="active">Ajouter un livre</a> | 
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
            <h2>➕ Ajouter un Nouveau Livre</h2>
            
            <?php if($success): ?>
                <div class="alert alert-success">✅ <?php echo $success; ?></div>
                <a href="view_book.php" class="btn btn-primary">Retour à la liste</a>
            <?php else: ?>
                <?php if($error): ?>
                    <div class="alert alert-error">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form action="add_book.php" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <label for="titre"><strong>Titre du livre *</strong></label>
                        <input type="text" id="titre" name="titre" placeholder="Ex: Harry Potter" required>

                        <label for="auteur"><strong>Auteur *</strong></label>
                        <input type="text" id="auteur" name="auteur" placeholder="Ex: J.K. Rowling" required>

                        <label for="isbn"><strong>ISBN *</strong></label>
                        <input type="text" id="isbn" name="isbn" placeholder="Ex: 978-0-7475-3269-9" required>

                        <label for="quantite"><strong>Quantité *</strong></label>
                        <input type="number" id="quantite" name="quantite" placeholder="Nombre de copies" min="1" required>

                        <label for="image"><strong>Image du livre *</strong></label>
                        <input type="file" id="image" name="image" accept="image/*" required>

                        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">➕ Ajouter le livre</button>
                        <a href="view_book.php" class="btn btn-secondary" style="margin-top: 20px;">Annuler</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>