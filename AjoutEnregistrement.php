<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour enregistrer une recette.");
}

$user_id = $_SESSION['user_id'];
$recette_id = $_POST['recette_id'];

// Vérifier si la recette est déjà enregistrée
$sql = "SELECT * FROM enregistrements WHERE user_id = :user_id AND recette_id = :recette_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
$enregistrement = $stmt->fetch();

if ($enregistrement) {
    // Supprimer des enregistrements
    $sql = "DELETE FROM enregistrements WHERE user_id = :user_id AND recette_id = :recette_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
    echo json_encode(['status' => 'removed']);
} else {
    // Ajouter aux enregistrements
    $sql = "INSERT INTO enregistrements (user_id, recette_id) VALUES (:user_id, :recette_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
    echo json_encode(['status' => 'added']);
}
?>
