<?php
// Démarrer la session (si nécessaire)
session_start();

// Paramètres de connexion
$host = "localhost";   // Serveur (ex: 127.0.0.1 ou localhost)
$user = "root";        // Nom d'utilisateur MySQL
$password = "";        // Mot de passe (laisser vide en local)
$dbname = "recette"; // Nom de ta base de données

try {
    // Connexion avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    
    // Définir le mode d'erreur sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
