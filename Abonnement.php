<?php
session_start();
error_log("Données POST reçues : " . print_r($_POST, true));
error_log("Données SESSION : " . print_r($_SESSION, true));

include('config.php');

if (!isset($_SESSION['user_id'], $_POST['following_id'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Requête invalide']);
    exit;
}

$follower_id = $_SESSION['user_id']; // L'utilisateur qui s'abonne
$following_id = $_POST['following_id']; // L'utilisateur à suivre

if ($follower_id == $following_id) {
    echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas vous abonner à vous-même.']);
    exit;
}

try {
    $pdo->beginTransaction(); // Début de la transaction

    // Vérifier si l'utilisateur à suivre existe
    $query = "SELECT COUNT(*) FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $following_id]);
    if ($stmt->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => "L'utilisateur n'existe pas."]);
        exit;
    }

    // Vérifier si l'abonnement existe déjà
    $query = "SELECT EXISTS(SELECT 1 FROM abonnement WHERE follower_id = :follower_id AND following_id = :following_id)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['follower_id' => $follower_id, 'following_id' => $following_id]);
    $abonnementExiste = $stmt->fetchColumn();

    if ($abonnementExiste) {
        // Désabonnement
        $query = "DELETE FROM abonnement WHERE follower_id = :follower_id AND following_id = :following_id LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['follower_id' => $follower_id, 'following_id' => $following_id]);
        $abonne = false;
    } else {
        // Ajouter un abonnement avec la date
        $query = "INSERT INTO abonnement (follower_id, following_id, date_abonnement) 
                  VALUES (:follower_id, :following_id, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['follower_id' => $follower_id, 'following_id' => $following_id]);
        $abonne = true;

        // Ajouter la notification
        try {
            $query = "SELECT nom FROM users WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $follower_id]);
            $follower_name = $stmt->fetchColumn();

            if ($follower_name) {
                $notif_sql = "INSERT INTO notifications (user_id, type, sender_id, message) 
                              VALUES (:user_id, 'abonnement', :sender_id, :message)";
                $notif_stmt = $pdo->prepare($notif_sql);
                $notif_stmt->execute([
                    'user_id' => $following_id,
                    'sender_id' => $follower_id,
                    'message' => "$follower_name vous a suivi."
                ]);
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout de la notification : " . $e->getMessage());
        }
    }

    // Récupérer le nombre d'abonnés après mise à jour
    $query = "SELECT COUNT(*) FROM abonnement WHERE following_id = :following_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['following_id' => $following_id]);
    $nb_abonnes = $stmt->fetchColumn();

    $pdo->commit(); // Valide la transaction

    echo json_encode(['success' => true, 'abonne' => $abonne, 'abonnes' => $nb_abonnes]);
} catch (PDOException $e) {
    $pdo->rollBack(); // Annule la transaction en cas d'erreur SQL
    echo json_encode(['success' => false, 'message' => 'Erreur SQL : ' . $e->getMessage()]);
} catch (Exception $e) {
    $pdo->rollBack(); // Annule la transaction en cas d'erreur générale
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
