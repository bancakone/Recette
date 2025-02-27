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

$user_id = $_SESSION['user_id'];
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
  }
        .container {
            margin-left: 270px;
        }

        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-body h5{
text-align : center;
        }
        .card img {
            height: 300px;
            object-fit: cover;
            cursor: pointer;
        }

        img.rounded-circle {
            height: 80px;
            width: 80px;
            object-fit: cover;
        }

        .nav-link {
            color: white;
            display: flex;
            align-items: center;
        }

        .nav-link i {
            margin-right: 10px;
        }

        .badge {
            background-color: red;
            position: absolute;
            margin-left: 5px;
            font-size: 12px;
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
            <h6><?= htmlspecialchars($user['nom'] . " " . $user['prenom']) ?></h6>
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
        <h3 style= "text-align:center;"> Enregistrement</h3>
        <div class="row">
            <?php if (count($enregistrements) > 0): ?>
                <?php foreach ($enregistrements as $recette): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <a href="Recette.php?id=<?= $recette['id'] ?>">
                                <img src="<?= htmlspecialchars($recette['photo']) ?>" class="card-img-top" alt="Recette">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($recette['titre']) ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Vous n'avez pas encore de favoris.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
