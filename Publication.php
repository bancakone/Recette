<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les infos utilisateur
$sql_user = "SELECT nom, prenom, email FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

// Récupérer les recettes publiées par l'utilisateur
$sql = "SELECT * FROM recettes WHERE user_id = :user_id ORDER BY date_creation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$recettes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Publications</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 250px;
            background-color: #37474F;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
        }
        .sidebar a {
            color: white;
            display: flex;
            align-items: center;
            padding: 21px;
            text-decoration: none;
        }
        .sidebar a i {
            margin-right: 20px;
        }
        .sidebar a:hover {
            background-color: #455A64;
        }
        .content {
            margin-left: 270px;
            padding: 10px;
            width: 100%;
        }
        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .card-image img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .card-content {
            flex-grow: 0;
        }
        .card-action {
            display: flex;
            justify-content: space-between;
        }
        .btn-floating {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h6><?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></h6>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <a href="Accueil.php"><i class="material-icons">home</i> Accueil</a>
        <a href="#"><i class="material-icons">account_circle</i> Profil</a>
        <a href="#"><i class="material-icons">favorite</i> Favoris</a>
        <a href="#"><i class="material-icons">bookmark</i> Enregistrements</a>
        <a href="#"><i class="material-icons">drafts</i> Brouillons</a>
        <a href="#"><i class="material-icons">exit_to_app</i> Déconnexion</a>
        <a href="#"><i class="material-icons">notifications</i> Notifications</a>
    </div>

    <!-- Contenu principal -->
    <div class="content">
        

        <div class="row">
            <?php if (!empty($recettes)): ?>
                <?php foreach ($recettes as $recette): ?>
                    <div class="col s12 m6 l3">
                        <div class="card">
                            <div class="card-image">
                                <img src="<?php echo $recette['photo']; ?>" alt="Image de la recette">
                            </div>
                            <div class="card-content">
                                <p><?php echo htmlspecialchars($recette['titre']); ?></p>
                            </div>
                            <div class="card-action">
                                <a href="ModifierRecette.php?id=<?php echo $recette['id']; ?>" class="btn blue lighten-1 waves-effect waves-light">
                                    <i class="material-icons left">edit</i> Modifier
                                </a>
                                <a href="SupprimerRecette.php?id=<?php echo $recette['id']; ?>" class="btn red darken-1 waves-effect waves-light">
                                    <i class="material-icons left">delete</i> Supprimer
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Vous n'avez aucune recette publiée pour le moment.</p>
            <?php endif; ?>
        </div>

        <!-- Bouton d'ajout flottant -->
        <a href="AjouterRecette.php" class="btn-floating btn-large red waves-effect waves-light">
            <i class="material-icons">add</i>
        </a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
