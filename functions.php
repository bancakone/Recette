<?php 
function ajouterNotification($pdo, $user_id, $type, $recette_id, $sender_id) {
    $messages = [
        'commentaire' => "a commenté votre recette.",
        'note' => "a noté votre recette.",
        'abonnement' => "s'est abonné à vous.",
        'like' => "a aimé votre recette."
    ];

    if (!isset($messages[$type])) return false; // Vérification du type valide

    $message = $messages[$type];

    $sql = "INSERT INTO notifications (user_id, type, recette_id, sender_id, message, is_read) 
            VALUES (:user_id, :type, :recette_id, :sender_id, :message, 0)";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'user_id' => $user_id,
        'type' => $type,
        'recette_id' => $recette_id,
        'sender_id' => $sender_id,
        'message' => $message
    ]);
}

?>