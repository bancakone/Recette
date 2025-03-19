<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour voir vos favoris.");
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$sql_user = "SELECT nom, prenom, email, photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

// Récupérer les favoris de l'utilisateur
$sql_favoris = "SELECT r.* FROM recettes r JOIN favoris f ON r.id = f.recette_id WHERE f.user_id = :user_id";
$stmt_favoris = $pdo->prepare($sql_favoris);
$stmt_favoris->execute(['user_id' => $user_id]);
$favoris = $stmt_favoris->fetchAll();


// Notifications
$sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND lu = FALSE";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$notif_count = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Favoris</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }

        .sidebar ul li a {
    color: white !important;
    display: flex;
    align-items: center;
    padding: 9px 13px;
    text-decoration: none;
    transition: 0.3s;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
  }

        .sidebar ul li a:hover {
    background-color: #ff5722;
    transform: translateX(5px);
  }/* Contenu principal */
.container {
    margin-left: 270px;
    font-family: 'Arial', sans-serif;
}

h3 {
    font-size: 2rem; /* Taille de police plus grande */
    font-weight: bold; /* Police en gras */
    color: #333; /* Couleur sombre pour une bonne lisibilité */
    text-align: center; /* Centre le titre */
    text-transform: uppercase; /* Met le texte en majuscules */
    margin-bottom: 30px; /* Espacement en bas pour aérer */
    letter-spacing: 1px; /* Espacement des lettres pour un effet plus moderne */
    border-bottom: 2px solid #ff5722; /* Ajoute une bordure colorée en bas du titre */
    padding-bottom: 10px; /* Espacement entre le titre et la bordure */
    font-family: 'Roboto', sans-serif; /* Police moderne et propre */
}

h3.mb-4 {
    margin-bottom: 40px; /* Si vous voulez un espacement supplémentaire en bas */
}
/* Cartes */
.card {
    width: 85%;
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    transition: transform 0.3s ease; /* Ajout d'un effet au survol */
   
}

.card:hover {
    transform: scale(1.05); /* Effet de zoom sur la carte */
}

.card img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    cursor: pointer;
}

.card-body {
    text-align: center;
    padding: 10px; /* Espacement ajouté pour aérer le contenu */
}

.card-body p {
    font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
}



/* Image de profil */
img.rounded-circle {
    height: 80px;
    width: 80px;
    object-fit: cover;
}

/* Liens de navigation */
.nav-link {
    color: white;
    display: flex;
    align-items: center;
    padding: 10px;
    font-weight: 500;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.nav-link:hover {
    background-color: #ff5722; /* Changer la couleur au survol */
    border-radius: 5px;
}

.nav-link i {
    margin-right: 10px;
    color: white;
}

/* Badge notifications */
.badge {
    background-color: red;
    position: absolute;
    margin-left: 5px;
    font-size: 12px;
    padding: 2px 6px; /* Taille ajustée pour plus de visibilité */
    border-radius: 12px; /* Badge arrondi */
}

    </style>
</head>
<body>

<div class="d-flex">
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="text-center">
            <!-- Image de profil dynamique -->
            <img src="<?= htmlspecialchars($user['photo'] ?? 'default.png') ?>" 
                 alt="Avatar" class="rounded-circle">
            <p></p><?= htmlspecialchars($user['nom'] . " " . $user['prenom']) ?></p>
            <p><?= htmlspecialchars($user['email']) ?></p>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item"><a href="Accueil.php" class="nav-link"><i class="material-icons">home</i> Accueil</a></li>
            <li class="nav-item"><a href="Profil.php" class="nav-link"><i class="material-icons">person</i> Profil</a></li>
            <li class="nav-item"><a href="Enregistrement.php" class="nav-link"><i class="material-icons">bookmark</i> Enregistrements</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item"><a href="Brouillons.php" class="nav-link"><i class="material-icons">save</i> Brouillons</a></li>
                <li class="nav-item"><a href="Publication.php" class="nav-link"><i class="material-icons">post_add</i> Publication</a></li>
                <li class="nav-item"><a href="Historique.php" class="nav-link"><i class="material-icons">history</i> Historique</a></li>
            <?php endif; ?>

            <li class="nav-item">
                <a href="Notification.php" class="nav-link">
                    <i class="material-icons">notifications</i> Notifications
                    <?php if ($notif_count > 0): ?>
                        <span class="badge"><?= $notif_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item"><a href="Deconnexion.php" class="nav-link"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
        </ul>
    </div>

    <!-- Contenu principal -->
    <div class="container mt-4">
        <h3 style= "text-align:center;"> Favoris</h3>
        <div class="row">
            <?php if (count($favoris) > 0): ?>
                <?php foreach ($favoris as $recette): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <a href="Recette.php?id=<?= $recette['id'] ?>">
                                <img src="<?= htmlspecialchars($recette['photo']) ?>" class="card-img-top" alt="Recette">
                            </a>
                            <div class="card-body">
                                <p ><?= htmlspecialchars($recette['titre']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Vous n'avez pas encore de favoris.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
