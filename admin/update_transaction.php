<?php
session_start();
include "../db.php";

if(!isset($_SESSION['id'])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION['role'] !== "Admin"){
    header("Location: ../dashboard.php");
    exit();
}

if(!isset($_GET['idemprunt'])){
    header("Location: view_transaction.php");
    exit();
}

$idemprunt = mysqli_real_escape_string($conn, $_GET['idemprunt']);
$error = "";
$success = "";
$submittedStatus = '';
$submittedDate = '';

$sql = "SELECT e.*, u.nom_utilisateur, l.titre, l.quantite FROM emprunt e " .
       "JOIN utilisateur u ON e.id = u.id " .
       "JOIN livre l ON e.idlivre = l.idlivre " .
       "WHERE e.idemprunt = '$idemprunt'";
$result = mysqli_query($conn, $sql);

if(!$result || $result->num_rows === 0){
    header("Location: view_transaction.php");
    exit();
}

$transaction = mysqli_fetch_assoc($result);
$submittedStatus = $transaction['status'];
$submittedDate = $transaction['dateretour'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $submittedStatus = isset($_POST['status']) ? trim($_POST['status']) : '';
    $submittedDate = isset($_POST['dateretour']) ? trim($_POST['dateretour']) : '';
    $status = $submittedStatus === 'retourné' ? 'retourné' : 'emprunté';
    $dateretour = mysqli_real_escape_string($conn, $submittedDate);

    if($status === 'retourné' && $dateretour === ''){
        $dateretour = date('Y-m-d');
    }

    if($status === 'retourné' && $dateretour === ''){
        $error = "Veuillez indiquer la date de retour.";
    } else {
        $currentStatus = $transaction['status'];
        $bookId = $transaction['idlivre'];

        if($currentStatus !== $status){
            if($currentStatus === 'emprunté' && $status === 'retourné'){
                mysqli_query($conn, "UPDATE livre SET quantite = quantite + 1 WHERE idlivre='$bookId'");
            } elseif($currentStatus === 'retourné' && $status === 'emprunté'){
                $stockCheck = mysqli_query($conn, "SELECT quantite FROM livre WHERE idlivre='$bookId'");
                $book = mysqli_fetch_assoc($stockCheck);
                if($book['quantite'] < 1){
                    $error = "Impossible de réactiver l'emprunt : le livre n'est pas disponible.";
                } else {
                    mysqli_query($conn, "UPDATE livre SET quantite = quantite - 1 WHERE idlivre='$bookId'");
                }
            }
        }

        if(!$error){
            $dateretourValue = $status === 'retourné' ? "'$dateretour'" : "NULL";
            $sqlUpdate = "UPDATE emprunt SET status='$status', dateretour=$dateretourValue WHERE idemprunt='$idemprunt'";
            $updateResult = mysqli_query($conn, $sqlUpdate);
            if($updateResult){
                header("Location: view_transaction.php");
                exit();
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
    <title>Modifier la transaction - Admin</title>
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
            <h2>✏️ Modifier la transaction #<?php echo htmlspecialchars($transaction['idemprunt']); ?></h2>

            <?php if($error): ?>
                <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="update_transaction.php?idemprunt=<?php echo urlencode($idemprunt); ?>" method="post" style="max-width: 600px;">
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <p><strong>Utilisateur :</strong> <?php echo htmlspecialchars($transaction['nom_utilisateur']); ?></p>
                    <p><strong>Livre :</strong> <?php echo htmlspecialchars($transaction['titre']); ?></p>
                    <p><strong>Date d'emprunt :</strong> <?php echo date('d/m/Y', strtotime($transaction['dateemprunt'])); ?></p>

                    <label for="status"><strong>Statut</strong></label>
                    <select id="status" name="status" required>
                        <option value="emprunté" <?php echo ($submittedStatus==='emprunté' ? 'selected' : ''); ?>>Emprunté</option>
                        <option value="retourné" <?php echo ($submittedStatus==='retourné' ? 'selected' : ''); ?>>Retourné</option>
                    </select>

                    <label for="dateretour"><strong>Date de retour</strong></label>
                    <input type="date" id="dateretour" name="dateretour" value="<?php echo ($submittedDate ? date('Y-m-d', strtotime($submittedDate)) : ''); ?>">

                    <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Mettre à jour</button>
                    <a href="view_transaction.php" class="btn btn-secondary" style="margin-top: 20px;">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>