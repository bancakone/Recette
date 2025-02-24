<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['recette_id'], $_POST['action'])) {
        $recette_id = intval($_POST['recette_id']);
        $action = $_POST['action'];
        $user_id = $_SESSION['user_id']; // Assurez-vous que l'ID de l'utilisateur est stocké en session

        try {
            $conn = new PDO("mysql:host=localhost;dbname=recette", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($action === 'add') {
                // Ajouter aux enregistrements
                $stmt = $conn->prepare("INSERT INTO enregistrements (user_id, recette_id) VALUES (?, ?)");
                $stmt->execute([$user_id, $recette_id]);
            } elseif ($action === 'remove') {
                // Retirer des enregistrements
                $stmt = $conn->prepare("DELETE FROM enregistrements WHERE user_id = ? AND recette_id = ?");
                $stmt->execute([$user_id, $recette_id]);
            }
        } catch (PDOException $e) {
            die("Erreur : " . $e->getMessage());
        }
    }
}
?>

