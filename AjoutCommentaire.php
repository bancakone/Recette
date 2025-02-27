<?php
session_start();
include('config.php');

if (!isset($_POST['recette_id']) || !isset($_POST['contenu']) || !isset($_SESSION['user_id'])) {
    die("Requête invalide.");
}

$recette_id = $_POST['recette_id'];
$contenu = htmlspecialchars($_POST['contenu']);
$sender_id = $_SESSION['user_id']; // L'utilisateur qui commente

// 1️⃣ Ajouter le commentaire  
$sql = "INSERT INTO commentaires (recette_id, user_id, contenu, date_creation) 
        VALUES (:recette_id, :user_id, :contenu, NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute(['recette_id' => $recette_id, 'user_id' => $sender_id, 'contenu' => $contenu]);

// 2️⃣ Récupérer l'auteur de la recette  
$sql = "SELECT user_id FROM recettes WHERE id = :recette_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['recette_id' => $recette_id]);
$recette = $stmt->fetch();

if ($recette && $recette['user_id'] != $sender_id) {
    // 3️⃣ Récupérer le nom de l'utilisateur qui a commenté  
    $userSql = "SELECT nom FROM users WHERE id = :sender_id";
    $userStmt = $pdo->prepare($userSql);
    $userStmt->execute(['sender_id' => $sender_id]);
    $user = $userStmt->fetch();

    $nom_utilisateur = $user ? $user['nom'] : "Un utilisateur";

    // 4️⃣ Ajouter la notification pour l'auteur de la recette  
    $notifSql = "INSERT INTO notifications (user_id, sender_id, type, recette_id, message, lu, date_creation) 
                 VALUES (:user_id, :sender_id, 'comment', :recette_id, :message, 0, NOW())";
    $notifStmt = $pdo->prepare($notifSql);
    $notifStmt->execute([
        'user_id'    => $recette['user_id'], // L'auteur de la recette reçoit la notif
        'sender_id'  => $sender_id, // Celui qui commente
        'recette_id' => $recette_id,
        'message'    => "$nom_utilisateur a commenté votre recette."
    ]);
}

// 5️⃣ Rediriger après l'ajout du commentaire  
header("Location: Recette.php?id=" . $recette_id);
exit;
?>
