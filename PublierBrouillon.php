<?php
session_start();
include('config.php'); // Connexion à la base de données

if (!isset($_SESSION['user_id'])) {
    header("Location: Connexion.php");
    exit();
}

if (isset($_GET['id'])) {
    $recette_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE recettes SET statut = 'publie' WHERE id = :recette_id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['recette_id' => $recette_id, 'user_id' => $user_id])) {
        // Redirection vers la page des brouillons ou accueil après publication
        header("Location: Brouillons.php?success=1");
        exit();
    } else {
        header("Location: Brouillons.php?error=1");
        exit();
    }
}
?>
