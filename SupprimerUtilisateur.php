<?php
include('config.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Supprimer l'utilisateur
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $stmt_delete = $pdo->prepare($delete_sql);
    $stmt_delete->execute([$user_id]);

    header('Location: Admin1.php');
} else {
    echo "ID manquant.";
    exit;
}
?>
