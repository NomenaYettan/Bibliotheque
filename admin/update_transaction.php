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
    $status = $submittedStatus === 'rendu' ? 'rendu' : 'emprunté';
    $dateretour = mysqli_real_escape_string($conn, $submittedDate);

    if($status === 'rendu' && $dateretour === ''){
        $dateretour = date('Y-m-d');
    }

    if($status === 'rendu' && $dateretour === ''){
        $error = "Veuillez indiquer la date de retour.";
    } else {
        $currentStatus = trim(strtolower($transaction['status']));
        $bookId = $transaction['idlivre'];

        if($currentStatus === 'en attente' && $status === 'rendu'){
            $error = "Impossible de marquer une demande en attente comme rendu. Veuillez d'abord approuver ou refuser la demande.";
        }

        if(!$error){
            $dateretourValue = $status === 'rendu' ? "'$dateretour'" : "NULL";

            // Pré-vérifications : lire le nombre d'emprunts actifs et la quantité du livre AVANT de modifier la transaction
            $activeBefore = null;
            $beforeQty = null;
            if($currentStatus === 'emprunté' || ($currentStatus === 'en attente' && $status === 'emprunté')){
                $activeResBefore = mysqli_query($conn, "SELECT COUNT(*) AS active FROM emprunt WHERE idlivre='$bookId' AND LOWER(TRIM(status)) LIKE 'emprunt%'");
                if($activeResBefore) $activeBefore = intval(mysqli_fetch_assoc($activeResBefore)['active']);

                $beforeQtyRes = mysqli_query($conn, "SELECT quantite FROM livre WHERE idlivre='$bookId'");
                $beforeQty = ($beforeQtyRes && mysqli_num_rows($beforeQtyRes)>0) ? intval(mysqli_fetch_assoc($beforeQtyRes)['quantite']) : null;
            }

            mysqli_begin_transaction($conn);

            $sqlUpdate = "UPDATE emprunt SET status='$status', dateretour=$dateretourValue WHERE idemprunt='$idemprunt'";
            $updateResult = mysqli_query($conn, $sqlUpdate);

            if(!$updateResult || $conn->affected_rows === 0){
                mysqli_rollback($conn);
                if(!$updateResult){
                    $error = "Erreur lors de la mise à jour : " . $conn->error;
                } else {
                    $error = "La transaction a peut-être déjà été modifiée. Veuillez rafraîchir la page.";
                }
            } else {
                if($currentStatus === 'emprunté' && $status === 'rendu'){
                    // Incrémenter la quantité du livre lors du retour
                    $qtyResult = mysqli_query($conn, "UPDATE livre SET quantite = quantite + 1 WHERE idlivre='$bookId'");
                    if(!$qtyResult){
                        mysqli_rollback($conn);
                        $error = "Erreur lors de la mise à jour du stock : " . $conn->error;
                    } else {
                        $afterQty = null;
                        $afterQtyRes = mysqli_query($conn, "SELECT quantite FROM livre WHERE idlivre='$bookId'");
                        if($afterQtyRes && mysqli_num_rows($afterQtyRes)>0) $afterQty = intval(mysqli_fetch_assoc($afterQtyRes)['quantite']);
                        $logLine = date('Y-m-d H:i:s') . " | return_committed | idemprunt=$idemprunt | idlivre=$bookId | before={$beforeQty} | after={$afterQty} | active_before={$activeBefore}\n";
                        @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
                    }
                } elseif($currentStatus === 'rendu' && $status === 'emprunté'){
                    // Pour réactiver un emprunt, vérifier la disponibilité avant de décrémenter la quantité
                    $stockCheck = mysqli_query($conn, "SELECT quantite FROM livre WHERE idlivre='$bookId'");
                    $book = ($stockCheck && mysqli_num_rows($stockCheck) > 0) ? mysqli_fetch_assoc($stockCheck) : null;
                    $availableQty = ($book) ? intval($book['quantite']) : null;

                    if($availableQty !== null && $availableQty <= 0){
                        mysqli_rollback($conn);
                        $error = "Impossible de réactiver l'emprunt : pas de copies disponibles.";
                        $logLine = date('Y-m-d H:i:s') . " | reactivate_denied | idemprunt=$idemprunt | idlivre=$bookId | available_qty={$availableQty}\n";
                        @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
                    } else {
                        $qtyResult = mysqli_query($conn, "UPDATE livre SET quantite = quantite - 1 WHERE idlivre='$bookId' AND quantite > 0");
                        if(!$qtyResult || $conn->affected_rows === 0){
                            mysqli_rollback($conn);
                            $error = "Impossible de réactiver l'emprunt : pas de copies disponibles pour décrémenter.";
                            $logLine = date('Y-m-d H:i:s') . " | reactivate_failed_decrement | idemprunt=$idemprunt | idlivre=$bookId | available_qty={$availableQty}\n";
                            @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
                        } else {
                            $logLine = date('Y-m-d H:i:s') . " | reactivate_committed | idemprunt=$idemprunt | idlivre=$bookId | available_qty_before={$availableQty}\n";
                            @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
                        }
                    }
                } elseif($currentStatus === 'en attente' && $status === 'emprunté'){
                    // Pour l'approbation depuis 'en attente', vérifier la disponibilité avant de décrémenter la quantité
                    $stockCheck = mysqli_query($conn, "SELECT quantite FROM livre WHERE idlivre='$bookId'");
                    $book = ($stockCheck && mysqli_num_rows($stockCheck) > 0) ? mysqli_fetch_assoc($stockCheck) : null;
                    $availableQty = ($book) ? intval($book['quantite']) : null;

                    if($availableQty !== null && $availableQty <= 0){
                        mysqli_rollback($conn);
                        $error = "Impossible d'approuver la demande : pas de copies disponibles.";
                        $logLine = date('Y-m-d H:i:s') . " | approve_denied | idemprunt=$idemprunt | idlivre=$bookId | available_qty={$availableQty}\n";
                        @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
                    } else {
                        $qtyResult = mysqli_query($conn, "UPDATE livre SET quantite = quantite - 1 WHERE idlivre='$bookId' AND quantite > 0");
                        if(!$qtyResult || $conn->affected_rows === 0){
                            mysqli_rollback($conn);
                            $error = "Impossible d'approuver la demande : pas de copies disponibles pour décrémenter.";
                            $logLine = date('Y-m-d H:i:s') . " | approve_failed_decrement | idemprunt=$idemprunt | idlivre=$bookId | available_qty={$availableQty}\n";
                            @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
                        } else {
                            $logLine = date('Y-m-d H:i:s') . " | approve_committed | idemprunt=$idemprunt | idlivre=$bookId | available_qty_before={$availableQty}\n";
                            @file_put_contents(__DIR__ . '/stock_changes.log', $logLine, FILE_APPEND);
                        }
                    }
                }

                if(!$error){
                    mysqli_commit($conn);
                    header("Location: view_transaction.php");
                    exit();
                }
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
                        <option value="rendu" <?php echo ($submittedStatus==='rendu' ? 'selected' : ''); ?>>Rendu</option>
                    </select>

                    <label for="dateretour"><strong>Date de retour</strong></label>
                    <input type="date" id="dateretour" name="dateretour" value="<?php echo ($submittedDate ? date('Y-m-d', strtotime($submittedDate)) : ''); ?>">

                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        <a href="view_transaction.php" class="btn btn-secondary">Annuler</a>
                        <?php if($transaction['status'] !== 'rendu'): ?>
                            <button type="button" class="btn btn-danger" disabled style="opacity: 0.65; cursor: not-allowed;">Supprimer (seulement si rendu)</button>
                        <?php else: ?>
                            <a href="delete_transaction.php?idemprunt=<?php echo urlencode($idemprunt); ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ? Cette action est définitive.');">Supprimer la transaction</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Bibliothèque SALT IVORY - Tous droits réservés</p>
    </footer>
</body>
</html>