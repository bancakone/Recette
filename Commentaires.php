<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifier si un ID de recette est passé dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $recette_id = (int) $_GET['id']; // Récupérer l'ID de la recette
} else {
    // Rediriger vers la page d'accueil si aucun ID n'est spécifié
    header('Location: index.php');
    exit();
}

// Récupérer tous les commentaires pour la recette spécifiée
$sql = "SELECT c.id, c.contenu, c.date_creation, u.nom, u.prenom, c.user_id, r.titre AS recette_nom, r.id AS recette_id
        FROM commentaires c 
        JOIN users u ON c.user_id = u.id
        JOIN recettes r ON c.recette_id = r.id
        WHERE r.id = :recette_id
        ORDER BY c.date_creation DESC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':recette_id', $recette_id, PDO::PARAM_INT);
$stmt->execute();
$commentaires = $stmt->fetchAll();

// Récupérer les informations de l'utilisateur pour la sidebar
$user_id = $_SESSION['user_id'];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commentaires de la Recette</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        /* Style de la sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #343a40;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Profil utilisateur */
        .sidebar img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 3px solid #ff5722;
        }

        .sidebar h6 {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }

        .sidebar p {
            font-size: 14px;
            color: #bbb;
        }

        /* Liens de la sidebar */
        .sidebar ul {
            width: 100%;
            padding: 0;
            margin-top: 20px;
        }

        .sidebar ul li {
            list-style: none;
        }

        .sidebar ul li a {
            color: white !important;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            text-decoration: none;
            transition: 0.3s;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
        }

        .sidebar ul li a i {
            font-size: 24px;
            margin-right: 10px;
        }

        .sidebar ul li a:hover {
            background-color: #ff5722;
            transform: translateX(5px);
        }

        /* Contenu principal */
        .container {
            margin-left: 270px;
            padding: 20px;
        }

        h4 {
            color: #343a40;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Liste des commentaires */
        .collection {
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Réduire la taille de la boîte des commentaires */
.collection-item {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px; /* Réduire la taille de la police */
    background-color: #ffe0b2; /* Couleur de fond pour un meilleur contraste */
    font-weight: normal; /* Police de caractères moins grasse */
    border-left: 5px solid #ff5722; /* Bordure de gauche */
    margin-bottom: 5px; /* Espacement entre les commentaires */
}

/* Appliquer une police différente */
.collection-item strong {
    font-family: 'Arial', sans-serif;
    font-weight: bold;
    font-size: 16px; /* Taille de la police du nom */
    color: #333;
}

.collection-item p {
    font-family: 'Verdana', sans-serif; /* Police différente pour le texte */
    font-size: 14px;
    color: #555;
}

/* Style du petit texte (date) */
.collection-item small {
    font-family: 'Courier New', Courier, monospace; /* Changer la police */
    font-size: 12px;
    color: #777;
}

        /* Boutons modifier/supprimer */
        .action-btns {
            display: flex;
            gap: 10px;
        }

        .action-btns a {
            padding: 5px 10px;
            background-color: #ff5722;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }

        .action-btns a:hover {
            background-color: #e64a19;
        }
    </style>
</head>
<body>
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

    <div class="container">
        <h4>Commentaires  <?= htmlspecialchars($commentaires[0]['recette_nom']) ?></h4>

        <?php if (!empty($commentaires)): ?>
            <ul class="collection">
                <?php foreach ($commentaires as $commentaire): ?>
                    <li class="collection-item" 
                        style="border-left: 5px solid #ff5722;">
                        <span>
                            <strong><?= htmlspecialchars($commentaire['nom'] . " " . $commentaire['prenom']) ?></strong>
                            <br>
                            <p><?= nl2br(htmlspecialchars($commentaire['contenu'])) ?></p>
                            <small style="color: #777;"><?= date('d M Y à H:i', strtotime($commentaire['date_creation'])) ?></small>
                        </span>

                        <?php if ($commentaire['user_id'] == $_SESSION['user_id']): ?>
                            <div class="action-btns">
                                <a href="ModifierCommentaire.php?id=<?= $commentaire['id'] ?>">Modifier</a>
                                <a href="SupprimerCommentaire.php?id=<?= $commentaire['id'] ?>" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">Supprimer</a>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="center">Aucun commentaire disponible pour cette recette.</p>
        <?php endif; ?>

        <!-- Bouton retour -->
        <div class="center">
            <a href="Recette.php?id=<?= $recette_id ?>" class="btn">Retour à la recette</a>
        </div>
    </div>
</body>
</html>
