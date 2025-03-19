<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour voir vos enregistrements.");
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$sql_user = "SELECT nom, prenom, email, photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

$sql = "SELECT r.* FROM recettes r JOIN enregistrements e ON r.id = e.recette_id WHERE e.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$enregistrements = $stmt->fetchAll();

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
    <title>Mes Enregistrements</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
  }
        .nav-link i {
    margin-right: 15px; /* Augmente l'espace entre l'icône et le texte */
    font-size: 22px; /* Facultatif : ajuster la taille de l'icône */
}

        /* Mise en page des cartes */
        .container {
    margin-left: 250px; /* Décalage pour ne pas recouvrir la sidebar */
    width: calc(100% - 250px); /* Ajustement pour exclure la sidebar */
    padding: 20px;
}


        .cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Centrer les cartes */
            gap: 20px;
        }

        .card {
            width: 200px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            border-radius: 10px;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            text-align: center;
            margin-left:  10px;        }

        .card:hover {
            transform: scale(1.05);
        }

        .card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            cursor: pointer;
        }

        .card-body {
            padding: 15px;
        }

        .card-body h5 {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }

        img.rounded-circle {
            height: 80px;
            width: 80px;
            object-fit: cover;
        }

        h3 {
    font-size: 2rem; /* Taille de police plus grande */
    font-weight: bold; /* Police en gras */
    color: #333; /* Couleur sombre pour une bonne lisibilité */
    text-align: center; /* Centre le texte */
    text-transform: uppercase; /* Met le texte en majuscules */
    margin-bottom: 30px; /* Espacement en bas pour aérer */
    letter-spacing: 1px; /* Espacement des lettres pour un effet plus moderne */
    font-family: 'Roboto', sans-serif; /* Police moderne et propre */
    position: relative; /* Position relative pour la ligne */
    padding-bottom: 15px; /* Espacement entre le titre et la ligne */
}

h3::after {
    content: ""; 
    display: block;
    width: 100%; /* Prend toute la largeur du conteneur parent */
    height: 3px;
    background-color: #ff5722;
    margin-top: 5px; /* Ajoute un petit espace si nécessaire */
}



    </style>
</head>
<body>

<div class="d-flex">
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="text-center">
            <img src="<?= htmlspecialchars($user['photo'] ?? 'default.png') ?>" 
                 alt="Avatar" class="rounded-circle">
            <p><?= htmlspecialchars($user['nom'] . " " . $user['prenom']) ?></p>
            <p><?= htmlspecialchars($user['email']) ?></p>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item"><a href="Accueil.php" class="nav-link"><i class="material-icons">home</i> Accueil</a></li>
            <li class="nav-item"><a href="Profil.php" class="nav-link"><i class="material-icons">person</i> Profil</a></li>
            <li class="nav-item"><a href="Favoris.php" class="nav-link"><i class="material-icons">favorite</i> Favoris</a></li>
           
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
        <h3>Enregistrement</h3> <!-- Le titre est bien placé au-dessus des cartes -->
        
        <div class="cards-container">
            <?php if (count($enregistrements) > 0): ?>
                <?php foreach ($enregistrements as $recette): ?>
                    <div class="card">
                        <a href="Recette.php?id=<?= $recette['id'] ?>">
                            <img src="<?= htmlspecialchars($recette['photo']) ?>" class="card-img-top" alt="Recette">
                        </a>
                        <div class="card-body">
                            <h5><?= htmlspecialchars($recette['titre']) ?></h5>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">Vous n'avez pas encore d'enregistrements.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
