<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Vous devez être connecté pour noter une recette.']));
}

$user_id = $_SESSION['user_id'];
$recette_id = $_POST['recette_id'];
$note = intval($_POST['note']);

if ($note < 1 || $note > 5) {
    die(json_encode(['error' => 'Note invalide.']));
}

// Vérifier si l'utilisateur a déjà noté cette recette
$sql = "SELECT * FROM notes WHERE user_id = :user_id AND recette_id = :recette_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
$note_existante = $stmt->fetch();

if ($note_existante) {
    // Modifier la note
    $sql = "UPDATE notes SET note = :note WHERE user_id = :user_id AND recette_id = :recette_id";
} else {
    // Ajouter une nouvelle note
    $sql = "INSERT INTO notes (user_id, recette_id, note) VALUES (:user_id, :recette_id, :note)";
}
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id, 'note' => $note]);

// Calculer la nouvelle moyenne des notes
$sql = "SELECT AVG(note) AS moyenne FROM notes WHERE recette_id = :recette_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['recette_id' => $recette_id]);
$moyenne = $stmt->fetch()['moyenne'];

echo json_encode(['success' => true, 'moyenne' => round($moyenne, 1)]);
?>
