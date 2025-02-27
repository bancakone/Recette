<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour commenter.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recette_id = $_POST['recette_id'];
    $contenu = trim($_POST['contenu']);
    $user_id = $_SESSION['user_id'];

    if (empty($contenu)) {
        die("Le commentaire ne peut pas être vide.");
    }

    $sql = "INSERT INTO commentaires (recette_id, user_id, contenu, date_creation) VALUES (:recette_id, :user_id, :contenu, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'recette_id' => $recette_id,
        'user_id' => $user_id,
        'contenu' => $contenu
    ]);

    header("Location: Recette.php?id=" . $recette_id);
    exit();
}
?>
