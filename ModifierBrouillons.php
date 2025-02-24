<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT nom, email, photo FROM users WHERE id = :id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['id' => $user_id]);
$user = $stmt_user->fetch();

if (isset($_GET['id'])) {
    $recette_id = $_GET['id'];
    $sql = "SELECT * FROM recettes WHERE id = :id AND user_id = :user_id AND statut = 'brouillon'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $recette_id, 'user_id' => $_SESSION['user_id']]);
    $recette = $stmt->fetch();
    if (!$recette) {
        echo "Brouillon non trouvé.";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Recette</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            display: flex;
            background: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #2c3e50;
            padding: 20px;
            color: white;
        }
        .sidebar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
        }
        .sidebar h6, .sidebar p {
            text-align: center;
        }
        .sidebar ul {
            padding: 0;
        }
        .sidebar ul li {
            list-style: none;
            padding: 10px;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .sidebar ul li a i {
            margin-right: 10px;
        }
        .container {
            flex: 1;
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin: 50px auto;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="images/<?= $user['photo'] ?: 'default.png' ?>" alt="Photo de profil">
        <h6><?= htmlspecialchars($user['nom']) ?></h6>
        <p><?= htmlspecialchars($user['email']) ?></p>
        <ul>
            <li><a href="dashboard.php"><i class="material-icons">home</i> Accueil</a></li>
            <li><a href="profil.php"><i class="material-icons">person</i> Profil</a></li>
            <li><a href="favoris.php"><i class="material-icons">favorite</i> Favoris</a></li>
            <li><a href="enregistrements.php"><i class="material-icons">bookmark</i> Enregistrements</a></li>
            <li><a href="brouillons.php"><i class="material-icons">drafts</i> Brouillons</a></li>
            <li><a href="historique.php"><i class="material-icons">history</i> Historique</a></li>
            <li><a href="notifications.php"><i class="material-icons">notifications</i> Notifications</a></li>
            <li><a href="deconnexion.php"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
        </ul>
    </div>
    <div class="container">
        <h5>Modifier Recette</h5>
        <form method="POST" enctype="multipart/form-data">
            <div class="file-field input-field">
                <div class="btn red">
                    <span>PHOTO</span>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Modifier la photo">
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <input type="text" name="titre" value="<?= htmlspecialchars($recette['titre']) ?>" required>
                    <label>Titre</label>
                </div>
                <div class="input-field col s6">
                    <input type="text" name="duree" value="<?= htmlspecialchars($recette['duree']) ?>" required>
                    <label>Durée (ex: 30 min)</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea name="description" class="materialize-textarea" required><?= htmlspecialchars($recette['description']) ?></textarea>
                    <label>Description</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <input type="number" name="portions" value="<?= htmlspecialchars($recette['portions']) ?>" min="1" required>
                    <label>Portions</label>
                </div>
                <div class="input-field col s6">
                    <input type="text" name="ingredients" value="<?= htmlspecialchars($recette['ingredients']) ?>" required>
                    <label>Ingrédients</label>
                    <a class="btn-floating btn-small red"><i class="material-icons">add</i></a>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea name="methode" class="materialize-textarea" required><?= htmlspecialchars($recette['methodes']) ?></textarea>
                    <label>Méthodes</label>
                    <a class="btn-floating btn-small red"><i class="material-icons">add</i></a>
                </div>
            </div>
            <button type="submit" class="btn-large waves-effect waves-light">ENREGISTRER LES MODIFICATIONS</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
