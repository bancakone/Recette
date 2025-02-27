<?php
session_start();
include('config.php'); // Connexion à la base de données

if (!isset($_GET['id'])) {
    die("Recette introuvable.");
}

$recette_id = $_GET['id'];

// Récupérer tous les commentaires
$sql = "SELECT c.id, c.contenu, c.date_creation, u.nom, u.prenom, c.user_id 
        FROM commentaires c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.recette_id = :recette_id 
        ORDER BY c.date_creation DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['recette_id' => $recette_id]);
$commentaires = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Commentaires</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <style>
    .comment-section {
        margin-top: 20px;
    }

    .view-more {
        margin-top: 15px;
    }

    .comments-list {
        list-style: none;
        padding: 0;
    }

    .comment-item {
        border-bottom: 1px solid #ddd;
        padding: 10px 0;
    }
</style>

</head>
<body>
    <div class="container">
        <h4>Commentaires</h4>

        <?php if (!empty($commentaires)): ?>
            <ul class="comments-list">
                <?php foreach ($commentaires as $commentaire): ?>
                    <li class="comment-item">
                        <strong><?= htmlspecialchars($commentaire['nom'] . " " . $commentaire['prenom']) ?></strong>
                        <p><?= nl2br(htmlspecialchars($commentaire['contenu'])) ?></p>
                        <small>Posté le <?= date("d/m/Y à H:i", strtotime($commentaire['date_creation'])) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun commentaire pour cette recette.</p>
        <?php endif; ?>

        <!-- Bouton retour -->
        <a href="Recette.php?id=<?= $recette_id ?>" class="btn orange">Retour à la recette</a>
    </div>
</body>
</html>
