<?php
include('config.php');

if (!isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$user_id = $_GET['user_id'];

// Nombre d'abonnés
$query = "SELECT COUNT(*) FROM abonnements WHERE following_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$nb_abonnes = $stmt->fetchColumn();

// Nombre d'abonnements
$query = "SELECT COUNT(*) FROM abonnements WHERE follower_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$nb_abonnements = $stmt->fetchColumn();

echo json_encode(['success' => true, 'abonnes' => $nb_abonnes, 'abonnements' => $nb_abonnements]);
?>
