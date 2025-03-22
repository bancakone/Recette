<?php
session_start();
include('config.php');

$user_id = $_SESSION['user_id'];


// Récupérer les notifications avec le nom et prénom de l'utilisateur qui a aimé/commenté
$sql = "SELECT notifications.*, users.nom, users.prenom 
        FROM notifications 
        JOIN users ON notifications.sender_id = users.id 
        WHERE notifications.user_id = :user_id 
        ORDER BY notifications.date_creation DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$notifications = $stmt->fetchAll();

// Récupérer les informations de l'utilisateur
$sql_user = "SELECT nom, prenom, email, photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

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
    <title>Notifications</title>
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


        /* Badge de notification */
        .badge {
            background-color: red;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 50%;
            margin-left: 10px;
        }

        /* Contenu principal */
        .container {
            margin-left: 270px;
            padding: 20px;
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
        /* Liste des notifications */
        .collection {
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

       /* Style des notifications */
.collection-item {
    font-size: 16px; /* Taille de texte principale */
    font-weight: 400; /* Police normale pour le texte des notifications */
}

.collection-item strong {
    font-size: 16px; /* Mettre le texte principal en taille plus grande */
    font-weight: 700; /* Mettre le message en gras */
    color: #333; /* Couleur du texte */
}

.collection-item small {
    color: #777; /* Couleur grise pour les dates */
    font-size: 13px; /* Taille plus petite pour les dates */
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

    <div class="container">
    <h3>Mes Notifications</h3>
    <ul class="collection">
        <?php foreach ($notifications as $notif): ?>
            <li class="collection-item" 
                style="border-left: 5px solid <?= $notif['lu'] ? '#ccc' : '#ff5722' ?>;
                       background-color: <?= $notif['lu'] ? '#f9f9f9' : '#ffe0b2' ?>;
                       font-weight: <?= $notif['lu'] ? 'normal' : 'bold' ?>;
                       border-radius: 5px; margin-bottom: 10px; transition: 0.3s;">
                
                <a href="Recette.php?id=<?= htmlspecialchars($notif['recette_id']) ?>&notif_id=<?= $notif['id'] ?>" 
                   style="text-decoration: none; color: #333; display: flex; justify-content: space-between; align-items: center; padding: 10px;">
                   
                    <span>
                        <strong><?= htmlspecialchars($notif['message']) ?></strong>  
                        <br>
                        <small style="color: #777;"><?= date('d M Y à H:i', strtotime($notif['date_creation'])) ?></small>
                    </span>
                    <i class="material-icons" style="color: #ff5722;">arrow_forward</i>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

    <script>
document.addEventListener("DOMContentLoaded", function() {
    fetch("MarquerLu.php")
        .then(response => response.text())
        .then(data => console.log(data)) // Optionnel : pour voir la réponse dans la console
        .catch(error => console.error("Erreur :", error));
});
</script>

</body>
</html>
