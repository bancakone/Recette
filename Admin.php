<?php
// Connexion à la base de données
include('config.php'); // Connexion à la base de données

// Récupérer le nombre total d'utilisateurs
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Récupérer le nombre total de recettes
$totalRecettes = $pdo->query("SELECT COUNT(*) FROM recettes")->fetchColumn();

// Récupérer le nombre total de commentaires
$totalCommentaires = $pdo->query("SELECT COUNT(*) FROM commentaires")->fetchColumn();

// Récupérer le nombre total de "J'aime"
$totalLikes = $pdo->query("SELECT COUNT(*) FROM likes")->fetchColumn();

// Récupérer le nombre total d'abonnements
$totalAbonnements = $pdo->query("SELECT COUNT(*) FROM abonnement")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<body>

<div class="container">
    <h4 class="center-align">📊 Tableau de Bord</h4>
    <div class="row">
        <div class="col s12 m6 l4">
            <div class="card blue">
                <div class="card-content white-text">
                    <span class="card-title">Utilisateurs</span>
                    <h5><?php echo $totalUsers; ?></h5>
                </div>
            </div>
        </div>

        <div class="col s12 m6 l4">
            <div class="card red">
                <div class="card-content white-text">
                    <span class="card-title">Recettes</span>
                    <h5><?php echo $totalRecettes; ?></h5>
                </div>
            </div>
        </div>

        <div class="col s12 m6 l4">
            <div class="card green">
                <div class="card-content white-text">
                    <span class="card-title">Commentaires</span>
                    <h5><?php echo $totalCommentaires; ?></h5>
                </div>
            </div>
        </div>

        <div class="col s12 m6 l4">
            <div class="card purple">
                <div class="card-content white-text">
                    <span class="card-title">"J'aime"</span>
                    <h5><?php echo $totalLikes; ?></h5>
                </div>
            </div>
        </div>

        <div class="col s12 m6 l4">
            <div class="card orange">
                <div class="card-content white-text">
                    <span class="card-title">Abonnements</span>
                    <h5><?php echo $totalAbonnements; ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
