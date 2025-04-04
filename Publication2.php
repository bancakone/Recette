<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

// $user_id = $_SESSION['user_id'];
$user_id = $_GET['user_id'];

// Récupérer les infos utilisateur
$sql_user = "SELECT nom, prenom, email , photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

// Récupérer les recettes publiées par l'utilisateur
$sql = "SELECT * FROM recettes WHERE user_id = :user_id AND statut = 'publie' ORDER BY date_creation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$recettes = $stmt->fetchAll();

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
    <title>Mes Publications</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="CSS/Publication1.css">
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
            width: 100px;
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
        .card .card-content{
    text-align: center;
    padding: 10px;
}
.card .card-action{
    display: flex;
    justify-content: space-between;
    padding: 12px 90px;
}
    </style>
</head>
<body>

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
    <div class="content">
        <div class="row">
            <?php if (!empty($recettes)): ?>
                <?php foreach ($recettes as $recette): ?>
                    <div class="col s12 m6 l3">
    <div class="card">
        <div class="card-image">
            <a href="Recette.php?id=<?php echo $recette['id']; ?>">
                <img src="<?php echo $recette['photo']; ?>" alt="Image de la recette">
            </a>
        </div>
        <div class="card-content">
            <p><?php echo htmlspecialchars($recette['titre']); ?></p>
        </div>
        <div class="card-action">
            <a href="ModifierRecette.php?id=<?php echo $recette['id']; ?>" class="btn blue lighten-1 waves-effect waves-light">
                <i class="material-icons center">edit</i>
            </a>
            <a href="SupprimerRecette.php?id=<?php echo $recette['id']; ?>" class="btn red darken-1 waves-effect waves-light">
                <i class="material-icons center">delete</i>
            </a>
        </div>
    </div>
</div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>Vous n'avez aucune recette publiée pour le moment.</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin: 20px 0;">
    <a href="javascript:history.back()" class="btn grey darken-2 waves-effect waves-light">
        <i class="material-icons left">arrow_back</i> Retour
    </a>
</div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    
</body>


</html>
