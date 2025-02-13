<?php
// Vérifier si une session est déjà active avant de la démarrer
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=localhost;dbname=recette;charset=utf8", "root", "");

    // Activer les erreurs et le mode sécurisé
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
