<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les infos utilisateur, y compris la photo de profil
$sql_user = "SELECT nom, prenom, email, photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

// Récupérer les brouillons de l'utilisateur
$sql = "SELECT id, titre, photo FROM recettes WHERE statut = 'brouillon' AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$brouillons = $stmt->fetchAll();

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Brouillons</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Brouillons1.css">
</head>
<style>
body {
    display: flex;
    margin: 0;
    font-family: Arial, sans-serif;
}

 /* Palette de couleurs modernes */
 :root {
        --primary-color: #FF6F61;
        --secondary-color: #2E3B4E;
        --accent-color: #4CAF50;
        --text-color: #333;
        --background-color: #F5F5F5;
    }

    /* Sidebar */
    .sidebar {
        width: 260px;
        height: 100vh;
        position: fixed;
        background-color: var(--secondary-color);
        color: white;
        padding: 25px 20px;
        font-family: 'Poppins', sans-serif;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar .text-center {
        margin-bottom: 30px;
    }

    .sidebar .text-center img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 50%;
        margin-bottom: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        margin-left: 50px;
    }

    .sidebar .text-center p {
            margin : 1px 0 0 45px;
            font-weight: 100;
            font-size: 15px;
            color:#bbb ;
        }

    .sidebar ul li a {
        color: white;
        display: flex;
        align-items: center;
        padding: 12px 20px;
        text-decoration: none;
        transition: 0.3s;
        border-radius: 8px;
        font-size: 16px;
    }

    .sidebar ul li a:hover {
        background-color: var(--primary-color);
        transform: translateX(8px);
    }

    .sidebar ul li a .material-icons {
        margin-right: 10px;
        font-size: 20px;
    }

    .sidebar ul li a .badge {
        background-color: var(--accent-color);
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 10px;
    }


/* 🔹 Contenu principal */
.container {
    margin-left: 270px; /* Ajusté */
    padding: 20px;
    width: calc(100% - 270px);
}

h3 {
        font-size: 2.2rem;
        font-weight: bold;
        color: var(--text-color);
        text-align: center;
        text-transform: uppercase;
        margin-bottom: 30px;
        font-family: 'Montserrat', sans-serif;
        position: relative;
        padding-bottom: 15px;
    }

    h3::after {
        content: "";
        display: block;
        width: 60px;
        height: 4px;
        background-color: var(--primary-color);
        margin-top: 10px;
        margin-left: auto;
        margin-right: auto;
    }
/* 🔹 Cartes de recettes */
.card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-radius: 15px; /* Rounded corners for a modern look */
    overflow: hidden;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Softer, more prominent shadow */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effect */
}

.card:hover {
    transform: translateY(-8px); /* Slightly raise the card on hover */
    box-shadow: 0 16px 32px rgba(0, 0, 0, 0.15); /* More pronounced shadow on hover */
}

/* 🔹 Image des recettes */
.card-image img {
    width: 100%;
    height: 300px; 
    object-fit: cover;
    border-top-left-radius: 15px; /* Rounded top corners for the image */
    border-top-right-radius: 15px;
    transition: transform 0.3s ease; /* Smooth zoom-in effect */
}

.card-image img:hover {
    transform: scale(1.05); /* Slight zoom effect on hover */
}

/* 🔹 Titre */
.card-content {
    text-align: center;
    font-weight: bold;
    padding: 5px;
    font-size: 1.3em; /* Larger font size for the title */
    color: var(--text-color);
    background-color: var(--background-color);
}

/* 🔹 Boutons */
.card-action {
    display: flex;
    justify-content: space-between;
    padding: 1px 2px;
    background-color: var(--background-color); /* Light background for the action area */
    border-bottom-left-radius: 5px; /* Rounded bottom corners for the card */
    border-bottom-right-radius: 15px;
}

/* 🔹 Bouton de l'action */
.card-action .btn {
    flex: 1;
    padding: 2px;
    border-radius: 8px;
    background-color: var(--primary-color);
    color: white;
    text-align: center;
    font-size: 1em;
    transition: background-color 0.3s ease; /* Smooth transition for hover effect */
}

.card-action .btn:hover {
    background-color: var(--accent-color); /* Change color on hover */
}

/* 🔹 Badge des cartes */
.badge {
    background-color: var(--accent-color);
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 14px;
    font-weight: bold;
    margin-left: 10px;
    color: white;
}

/* 🔹 Mise en forme des messages d'alerte */
p.text-center {
    font-size: 1.2rem;
    color: #666;
    font-weight: 600;
    margin-top: 40px;
    font-family: 'Poppins', sans-serif;
}

</style>
<body>
   <!-- Sidebar -->
<div class="sidebar">
    <div class="text-center">
        <img src="<?= htmlspecialchars($user['photo'] ?? 'default.png') ?>" alt="Avatar" class="rounded-circle">
        <p><?= htmlspecialchars($user['nom'] . " " . $user['prenom']) ?></p>
        <p><?= htmlspecialchars($user['email']) ?></p>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item"><a href="Accueil.php" class="nav-link"><i class="material-icons">home</i> Accueil</a></li>
        <li class="nav-item"><a href="Profil.php" class="nav-link"><i class="material-icons">person</i> Profil</a></li>
        <li class="nav-item"><a href="Favoris.php" class="nav-link"><i class="material-icons">favorite</i> Favoris</a></li>
        <li class="nav-item"><a href="Enregistrement.php" class="nav-link"><i class="material-icons">bookmark</i> Enregistrements</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><a href="Brouillons.php" class="nav-link"><i class="material-icons">save</i> Brouillons</a></li>
            <li class="nav-item"><a href="Publication.php" class="nav-link"><i class="material-icons">post_add</i> Publication</a></li>
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

    <div class="container">
    <h3>    Brouillons</h3>
        <div class="row">
            <?php if (count($brouillons) > 0): ?>
                <?php foreach ($brouillons as $brouillon): ?>
                    <div class="col s12 m6 l4">
                        <div class="card">
                            <div class="card-image">
                                <?php 
                              $image_path = !empty($brouillon['photo']) ? 'brouillon_image/' . htmlspecialchars($brouillon['photo']) : 'uploads/default.jpg';
                                ?>
                                <img src="<?php echo $image_path; ?>" alt="Image de la recette">
                            </div>

                            <div class="card-content">
                                <span class="card-title"><?php echo htmlspecialchars($brouillon['titre']); ?></span>
                            </div>
                            <div class="card-action">
                                <a href="ModifierBrouillons.php?id=<?php echo $brouillon['id']; ?>" class="btn blue">
                                    <i class="material-icons">edit</i>
                                </a>
                                <a href="SupprimerBrouillon.php?id=<?php echo $brouillon['id']; ?>" class="btn red">
                                    <i class="material-icons">delete</i>
                                </a>
                                <a href="PublierBrouillon.php?id=<?php echo $brouillon['id']; ?>" class="btn green">
                                    <i class="material-icons">publish</i> 
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="center-align">Aucun brouillon trouvé.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>