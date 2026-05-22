<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] !== "Admin"){
    header("Location: ../login.php");
    exit();
}

include '../db.php';
$error = "";
$success = "";

if(!isset($_GET['idlivre'])){
    header("Location: view_book.php");
    exit();
}

$idlivre = mysqli_real_escape_string($conn, $_GET['idlivre']);

$sql = "SELECT * FROM livre WHERE idlivre='$idlivre'";
$result = mysqli_query($conn, $sql);
if(!$result || $result->num_rows === 0){
    header("Location: view_book.php");
    exit();
}

$book = mysqli_fetch_assoc($result);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $titre = mysqli_real_escape_string($conn, trim($_POST['titre']));
    $auteur = mysqli_real_escape_string($conn, trim($_POST['auteur']));
    $isbn = mysqli_real_escape_string($conn, trim($_POST['isbn']));
    $quantite = intval($_POST['quantite']);
    $imageUpdate = '';

    if($titre === '' || $auteur === '' || $isbn === '' || $quantite < 0){
        $error = "Veuillez remplir tous les champs correctement.";
    } else {
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
            $imageName = basename($_FILES['image']['name']);
            $uploadLocation = "../image/" . $imageName;
            if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadLocation)){
                $imageUpdate = ", image='$imageName'";
            } else {
                $error = "Erreur lors de l'upload de l'image.";
            }
        }

        if(!$error){
            $sqlUpdate = "UPDATE livre SET titre='$titre', auteur='$auteur', isbn='$isbn', quantite='$quantite'$imageUpdate WHERE idlivre='$idlivre'";
            $updateResult = mysqli_query($conn, $sqlUpdate);
            if($updateResult){
                $success = "Livre mis à jour avec succès.";
                $sql = "SELECT * FROM livre WHERE idlivre='$idlivre'";
                $result = mysqli_query($conn, $sql);
                $book = mysqli_fetch_assoc($result);
            } else {
                $error = "Erreur lors de la mise à jour : " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un livre - Admin</title>
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
            <h2>✏️ Modifier le livre</h2>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="update_book.php?idlivre=<?php echo urlencode($idlivre); ?>" method="post" enctype="multipart/form-data" style="max-width: 600px;">
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <label for="titre"><strong>Titre du livre</strong></label>
                    <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($book['titre']); ?>" required>

                    <label for="auteur"><strong>Auteur</strong></label>
                    <input type="text" id="auteur" name="auteur" value="<?php echo htmlspecialchars($book['auteur']); ?>" required>

                    <label for="isbn"><strong>ISBN</strong></label>
                    <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>

                    <label for="quantite"><strong>Quantité</strong></label>
                    <input type="number" id="quantite" name="quantite" value="<?php echo htmlspecialchars($book['quantite']); ?>" min="0" required>

                    <label for="image"><strong>Image du livre</strong></label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <?php if(!empty($book['image'])): ?>
                        <p>Image actuelle : <img src="../image/<?php echo htmlspecialchars($book['image']); ?>" width="80" alt="<?php echo htmlspecialchars($book['titre']); ?>"></p>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Mettre à jour le livre</button>
                    <a href="view_book.php" class="btn btn-secondary" style="margin-top: 20px;">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>
