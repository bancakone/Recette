<?php
session_start();
include('config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: Connexion.php"); // Rediriger vers la page de connexion si non connecté
    exit();
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
        }

        .sidebar .text-center p {
            margin: 5px 0;
            font-weight: 500;
            color: #bbb;
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

        /* Contenu principal */
        .container {
            margin-left: 270px;
            width: calc(100% - 270px);
            padding: 30px;
            background-color: var(--background-color);
            min-height: 100vh;
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

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }

        .card {
            width: 250px;
            background-color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            text-align: center;
            margin-bottom: 30px;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            cursor: pointer;
            border-bottom: 2px solid var(--secondary-color);
        }
        .card-body {
            padding: 10px;
        }

        .card-body h5 {
            font-size: 18px;
            font-weight: bold;
            color: var(--text-color);
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .card-body p {
            font-size: 16px;
            color: black;
            margin-top: 10px;
            font-weight: bold;
        }

        /* Mise en forme des messages d'alerte */
        p.text-center {
            font-size: 1.2rem;
            color: #666;
            font-weight: 600;
            margin-top: 40px;
            font-family: 'Poppins', sans-serif;
        }


        .badge {
            background-color: var(--accent-color);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }
        
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="text-center">
            <!-- Image de profil dynamique -->
            <img src="<?= htmlspecialchars($user['photo'] ?? 'default.png') ?>" alt="Avatar" class="rounded-circle">
            <p><?= htmlspecialchars($user['nom'] . " " . $user['prenom']) ?></p>
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
        <h3>Mes Favoris</h3>
        <div class="row">
            <?php if (count($favoris) > 0): ?>
                <?php foreach ($favoris as $recette): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <a href="Recette.php?id=<?= $recette['id'] ?>">
                                <img src="<?= htmlspecialchars($recette['photo']) ?>" class="card-img-top" alt="Recette">
                            </a>
                            <div class="card-body">
                                <p><?= htmlspecialchars($recette['titre']) ?></p>
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
