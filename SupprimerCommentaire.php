<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

if (!isset($_GET['id'])) {
    die("Commentaire introuvable.");
}

$commentaire_id = $_GET['id'];
$sql = "SELECT recette_id FROM commentaires WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'id' => $commentaire_id,
    'user_id' => $_SESSION['user_id']
]);
$commentaire = $stmt->fetch();

if (!$commentaire) {
    die("Commentaire introuvable ou non autorisé.");
}

$sql = "DELETE FROM commentaires WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $commentaire_id]);

header("Location: Recette.php?id=" . $commentaire['recette_id']);
exit();
?>
