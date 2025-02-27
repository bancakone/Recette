<?php
session_start();
include('config.php'); // Connexion à la base de données

if (!isset($_GET['nom'])) {
    die("Catégorie non spécifiée.");
}


$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$sql_user = "SELECT nom, prenom, email, photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

$categorie_nom = $_GET['nom'];

// Récupérer l'ID de la catégorie
$sql_categorie = "SELECT id FROM categories WHERE nom = :nom";
$stmt_categorie = $pdo->prepare($sql_categorie);
$stmt_categorie->bindParam(':nom', $categorie_nom, PDO::PARAM_STR);
$stmt_categorie->execute();
$categorie = $stmt_categorie->fetch();

if (!$categorie) {
    die("Catégorie introuvable.");
}

$categorie_id = $categorie['id'];

// Récupérer les recettes de cette catégorie
$sql_recettes = "SELECT * FROM recettes WHERE categorie_id = :categorie_id AND statut = 'publie' ORDER BY date_creation DESC";
$stmt_recettes = $pdo->prepare($sql_recettes);
$stmt_recettes->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
$stmt_recettes->execute();
$recettes = $stmt_recettes->fetchAll();

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
    <title>Recettes - <?php echo htmlspecialchars($categorie_nom); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
         body {
            background: #f5f5f5;
            display: flex;
            height: 100vh;
        }
   .sidebar {
            width: 250px;
            height: 120vh;
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

        /* .card {
    border-radius: 12px;
    overflow: hidden;  Pour éviter que l'image dépasse 
    text-align: center;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}
.card-image img {
    width: 90%;
    height: 200px; /* Ajuste selon la taille souhaitée 
    object-fit: cover;  Évite la déformation 
}
.card-content {
    font-size: 16px;
    font-weight: bold;
}
 */
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
            color: white;
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
        <h3>Recettes : <?php echo htmlspecialchars($categorie_nom); ?></h3>
        <div class="row">
            <?php if (count($recettes) > 0): ?>
                <?php foreach ($recettes as $recette): ?>
                    <div class="col s12 m4">
                        <div class="card">
                            <div class="card-image">
                            <a href="Recette.php?id=<?= $recette['id'] ?>">
                                <img src="<?php echo $recette['photo']; ?>" alt="Image">
                            </a>    
                                <span class="card-title"><?php echo htmlspecialchars($recette['titre']); ?></span>
                            </div>
                            <!--  -->
                            <div class="card-action">
                                <a href="Recette.php?id=<?php echo $recette['id']; ?>">Voir la recette</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune recette trouvée pour cette catégorie.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
