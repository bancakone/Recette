<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour ajouter aux favoris.");
}

$user_id = $_SESSION['user_id'];
$recette_id = $_POST['recette_id'];

// Vérifier si la recette est déjà en favoris
$sql = "SELECT * FROM favoris WHERE user_id = :user_id AND recette_id = :recette_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
$favori = $stmt->fetch();

if ($favori) {
    // Supprimer des favoris
    $sql = "DELETE FROM favoris WHERE user_id = :user_id AND recette_id = :recette_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
    echo json_encode(['status' => 'removed']);
} else {
    // Ajouter aux favoris
    $sql = "INSERT INTO favoris (user_id, recette_id) VALUES (:user_id, :recette_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
    echo json_encode(['status' => 'added']);
}
?>
