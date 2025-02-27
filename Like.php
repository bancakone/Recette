<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['recette_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$recette_id = $_POST['recette_id'];

// Vérifier si l'utilisateur a déjà aimé
$query = "SELECT * FROM likes WHERE user_id = :user_id AND recette_id = :recette_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
$like = $stmt->fetch();

if ($like) {
    // Supprimer le like
    $query = "DELETE FROM likes WHERE user_id = :user_id AND recette_id = :recette_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
    $liked = false;
} else {
    // Ajouter un like
    $query = "INSERT INTO likes (user_id, recette_id) VALUES (:user_id, :recette_id)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
    $liked = true;

    // Récupérer l'auteur de la recette
    $sql = "SELECT user_id FROM recettes WHERE id = :recette_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['recette_id' => $recette_id]);
    $auteur_id = $stmt->fetchColumn();

    if ($auteur_id && $auteur_id != $user_id) {
        // Ajouter une notification uniquement si c'est un nouveau like
        $notif_sql = "INSERT INTO notifications (user_id, type, recette_id, sender_id, message) 
                      VALUES (:user_id, 'like', :recette_id, :sender_id, :message)";
        $notif_stmt = $pdo->prepare($notif_sql);
        $notif_stmt->execute([
            'user_id' => $auteur_id,
            'recette_id' => $recette_id,
            'sender_id' => $user_id,
            'message' => $_SESSION['nom'] . " a aimé votre recette." // Ajout du nom de l'utilisateur
        ]);
    }
}

// Compter le total des likes après la mise à jour
$query = "SELECT COUNT(*) FROM likes WHERE recette_id = :recette_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['recette_id' => $recette_id]);
$total_likes = $stmt->fetchColumn();

// Retourner la réponse JSON
echo json_encode(['success' => true, 'liked' => $liked, 'total_likes' => $total_likes]);

?>
