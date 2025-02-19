<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit;
}

// Récupérer les 4 dernières recherches de l'historique
$sql_historique = "SELECT * FROM historique WHERE user_id = :user_id ORDER BY id DESC";
$stmt_historique = $pdo->prepare($sql_historique);
$stmt_historique->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR); 
$stmt_historique->execute();
$historique = $stmt_historique->fetchAll();

// Récupérer les détails des recettes dans l'historique
$historique_recettes = [];
foreach ($historique as $item) {
    $id_recette = $item['recette_id'];
    $sql_recette = "SELECT * FROM recettes WHERE id = :id";
    $stmt_recette = $pdo->prepare($sql_recette);
    $stmt_recette->bindParam(':id', $id_recette, PDO::PARAM_INT);
    $stmt_recette->execute();
    $recette_details = $stmt_recette->fetch();
    $historique_recettes[] = $recette_details;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Recherches</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="CSS/Accueil1.css">
</head>
<body>
    <div class="sidebar">
        <!-- Sidebar content (le même que dans la page d'accueil) -->
        <div class="profile">
            <?php if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['email'])): ?>
                <?php if (isset($_SESSION['photo'])): ?>
                    <img src="<?php echo $_SESSION['photo']; ?>" alt="Photo de profil" width="80" height="80" />
                <?php endif; ?>
                <p><strong><?php echo $_SESSION['nom']; ?> <?php echo $_SESSION['prenom']; ?></strong><br><?php echo $_SESSION['email']; ?></p>
            <?php else: ?>
                <p><strong>Nom&Prénom</strong><br>Email</p>
            <?php endif; ?>
        </div>

        <ul>
            <li><a href="Profil.php" class="black-text"><i class="material-icons">person</i> Profil</a></li>
            <li><a href="Deconnexion.php" class="black-text"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">notifications</i> Notifications</a></li>
        </ul>
    </div>

    <div class="content">
        <h5>Historique des Recherches</h5>
        <div class="grid">
            <?php if (count($historique_recettes) > 0): ?>
                <?php foreach ($historique_recettes as $recette_details): ?>
                    <div class="card">
                        <a href="Recette.php?id=<?php echo $recette_details['id']; ?>">
                            <img src="<?php echo $recette_details['photo']; ?>" alt="Image de la recette">
                            <p><?php echo $recette_details['titre']; ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune recherche récente disponible.</p>
            <?php endif; ?>
        </div>

        <div class="view-more">
            <a href="Accueil.php">
                <i class="material-icons">arrow_back</i> Retour à l'accueil
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
