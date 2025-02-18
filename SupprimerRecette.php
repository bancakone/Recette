<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

if (isset($_GET['id'])) {
    $recette_id = $_GET['id'];

    // Supprimer la recette
    $sql = "DELETE FROM recettes WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $recette_id, 'user_id' => $_SESSION['user_id']]);

    // Rediriger vers la page des publications
    header('Location: Publication.php');
    exit();
}
?>
