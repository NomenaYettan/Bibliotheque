<?php
session_start();
if(!isset($_SESSION['id']) || $_SESSION['role'] != "Admin"){
    header("Location: ../login.php");
    exit();
}

include "../db.php";
$success = "";
$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action']) && $_POST['action'] === 'add_user'){
    $nom = mysqli_real_escape_string($conn, trim($_POST['nom']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $role = isset($_POST['role']) && $_POST['role'] === 'Admin' ? 'Admin' : 'User';

    if($nom === '' || $email === '' || $password === ''){
        $error = "Veuillez remplir tous les champs.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM utilisateur WHERE email='$email'");
        if($check && $check->num_rows > 0){
            $error = "Un compte avec cet email existe déjà.";
        } else {
            $sql_insert = "INSERT INTO utilisateur(nom_utilisateur, email, password, role) VALUES ('$nom','$email','$password','$role')";
            $result_insert = mysqli_query($conn, $sql_insert);
            if(!$result_insert){
                $error = "Erreur : " . $conn->error;
            } else {
                $success = "Le compte a été créé avec succès.";
            }
        }
    }
}

$sql = "select id, nom_utilisateur, email, role from utilisateur where role != 'Admin'";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>BIBLIOTHÈQUE SALT IVORY - ADMINISTRATION</h1>
        <nav>
            <a href="dashboard.php">Tableau de bord</a> | 
            <a href="view_book.php">Voir les livres</a> | 
            <a href="add_book.php">Ajouter un livre</a> | 
            <a href="manage_users.php" class="active">Gérer les utilisateurs</a> | 
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
            <h2>👥 Gestion des Utilisateurs</h2>

            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="form-container" style="max-width: 700px; margin-bottom: 30px; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h3>➕ Ajouter un nouvel administrateur</h3>
                <form action="manage_users.php" method="POST" style="display: grid; gap: 12px;">
                    <input type="hidden" name="action" value="add_user">
                    <input type="text" name="nom" placeholder="Nom complet" required>
                    <input type="email" name="email" placeholder="Adresse e-mail" required>
                    <input type="password" name="password" placeholder="Mot de passe" required>
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="radio" name="role" value="Admin" checked>
                        Administrateur
                    </label>
                    <button type="submit" class="btn btn-success">Créer un administrateur</button>
                </form>
            </div>

            <?php if($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom d'utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nom_utilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td>
                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Êtes-vous sûr?');">🗑️ Supprimer</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">Aucun utilisateur trouvé.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>
