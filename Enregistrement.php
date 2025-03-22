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
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            border-bottom: 2px solid var(--secondary-color);
        }

        .card-body {
            padding: 20px;
        }

        .card-body h5 {
            font-size: 18px;
            font-weight: bold;
            color: var(--text-color);
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .card-body p {
            font-size: 14px;
            color: #777;
            margin-top: 10px;
        }

        /* Mise en forme des messages d'alerte */
        p.text-center {
            font-size: 1.2rem;
            color: #666;
            font-weight: 600;
            margin-top: 40px;
            font-family: 'Poppins', sans-serif;
        }

        /* Effet sur les boutons */
        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--accent-color);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Barre latérale -->
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
