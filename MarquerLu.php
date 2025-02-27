<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

$user_id = $_SESSION['user_id'];

// Mettre à jour les notifications comme lues
$sql = "UPDATE notifications SET lu = 1 WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);

echo "Notifications marquées comme lues.";
?>
