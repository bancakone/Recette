<!-- <!-- <?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: Connexion.php');
    exit;
}

// Récupérer les statistiques
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_recipes = $pdo->query("SELECT COUNT(*) FROM recettes")->fetchColumn();
$total_comments = $pdo->query("SELECT COUNT(*) FROM commentaires")->fetchColumn();
$total_likes = $pdo->query("SELECT COUNT(*) FROM likes")->fetchColumn();

// Récupérer les données pour chaque section
$users = $pdo->query("SELECT id, nom, email, role FROM users LIMIT 5")->fetchAll();
$recipes = $pdo->query("SELECT id, titre FROM recettes LIMIT 5")->fetchAll();
$comments = $pdo->query("SELECT id, contenu FROM commentaires LIMIT 5")->fetchAll();
$top_liked_recipes = $pdo->query("SELECT recettes.titre, COUNT(likes.id) as total_likes FROM recettes LEFT JOIN likes ON recettes.id = likes.recette_id GROUP BY recettes.id ORDER BY total_likes DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <style>
        :root {
        --primary-color: #FF6F61;
        --secondary-color: #2E3B4E;
        --text-color: #333;
        --background-color: #F5F5F5;
    }

    .sidebar {
        width: 150px;
        height: 100vh;
        position: fixed;
        background-color: var(--secondary-color);
        color: white;
        padding: 20px;
    }
    .sidebar ul {
        padding: 0;
        list-style: none;
    }
    .sidebar ul li a {
        color: white;
        display: flex;
        align-items: center;
        padding: 12px;
        text-decoration: none;
        transition: 0.3s;
    }
    .sidebar ul li a:hover {
        background-color: var(--primary-color);
    }
    .content {
        margin-left: 270px;
        padding: 30px;
        background-color: var(--background-color);
        min-height: 100vh;
    }
    .cards-container {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .card {
        width: 200px;
        background-color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        padding: 20px;
        border-radius: 10px;
    }
    </style>
</head>
<body>

<div class="row">
    <div class="col s3 blue-grey darken-4 white-text" style="height: 100vh; padding: 20px;">
        <div class="center-align">
            <img src="admin_avatar.png" alt="Admin" class="circle responsive-img" style="width: 80px;">
            <p>Admin</p>
        </div>
        <ul>
            <li><a href="admin_dashboard.php" class="white-text"><i class="material-icons">dashboard</i> Tableau de Bord</a></li>
            <li><a href="admin_users.php" class="white-text"><i class="material-icons">people</i> Utilisateurs</a></li>
            <li><a href="admin_recipes.php" class="white-text"><i class="material-icons">restaurant_menu</i> Recettes</a></li>
            <li><a href="admin_categories.php" class="white-text"><i class="material-icons">category</i> Catégories</a></li>
            <li><a href="admin_comments.php" class="white-text"><i class="material-icons">comment</i> Commentaires</a></li>
            <li><a href="logout.php" class="white-text"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
        </ul>
    </div>

    <div class="col s9" style="padding: 30px;">
        <h4>Tableau de Bord</h4>
        <div class="row">
            <div class="col s12 m6 l3">
                <div class="card blue lighten-2 white-text center-align">
                    <div class="card-content">
                        <span class="card-title">Utilisateurs</span>
                        <p><?= $total_users; ?> inscrits</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card orange lighten-2 white-text center-align">
                    <div class="card-content">
                        <span class="card-title">Recettes</span>
                        <p><?= $total_recipes; ?> publiées</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card green lighten-2 white-text center-align">
                    <div class="card-content">
                        <span class="card-title">Commentaires</span>
                        <p><?= $total_comments; ?> écrits</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card red lighten-2 white-text center-align">
                    <div class="card-content">
                        <span class="card-title">Likes</span>
                        <p><?= $total_likes; ?> donnés</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m6 l6">
                <h5>Utilisateurs Récents</h5>
                <table class="highlight">
                    <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr><td><?= $user['nom']; ?></td><td><?= $user['email']; ?></td><td><?= $user['role']; ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="col s12 m6 l6">
                <h5>Dernières Recettes</h5>
                <table class="highlight">
                    <thead><tr><th>Titre</th><th>Auteur</th></tr></thead>
                    <tbody>
                        <?php foreach ($recipes as $recipe): ?>
                            <tr><td><?= $recipe['titre']; ?></td><td><?= $recipe['id']; ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html> -->
<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: Connexion.php');
    exit;
}

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Récupérer les statistiques
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_recipes = $pdo->query("SELECT COUNT(*) FROM recettes")->fetchColumn();

// Récupérer les utilisateurs récents avec pagination
$stmt = $pdo->prepare("SELECT id, nom, prenom, email, role FROM users LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

// Récupérer les recettes avec les informations nécessaires
$stmt = $pdo->prepare("SELECT r.id, r.titre, r.ingredients, r.methodes, r.photo, c.nom AS categorie, CONCAT(u.nom, ' ', u.prenom) AS auteur 
                       FROM recettes r 
                       JOIN users u ON r.user_id = u.id 
                       JOIN categories c ON r.categorie_id = c.id 
                       LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$recipes = $stmt->fetchAll();

// Calcul des pages totales
$total_pages_users = ceil($total_users / $limit);
$total_pages_recipes = ceil($total_recipes / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        .card-action {
            display: flex;
            justify-content: space-between;
        }
        .card img {
            max-width: 100%;
        }
        .pagination a {
            margin: 0 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h4 class="center-align">Tableau de Bord - Administration</h4>

    <!-- Statistiques -->
    <div class="row">
        <div class="col s12 m6 l3"><p><strong>Utilisateurs:</strong> <?= $total_users; ?></p></div>
        <div class="col s12 m6 l3"><p><strong>Recettes:</strong> <?= $total_recipes; ?></p></div>
    </div>

    <!-- Utilisateurs Récents -->
    <h5>Utilisateurs Récents</h5>
    <table class="highlight responsive-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['nom']); ?></td>
                    <td><?= htmlspecialchars($user['prenom']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                    <td><?= htmlspecialchars($user['role']); ?></td>
                    <td>
                        <a href="modifier_utilisateur.php?id=<?= $user['id']; ?>" class="btn-small waves-effect waves-light">Modifier</a>
                        <a href="supprimer_utilisateur.php?id=<?= $user['id']; ?>" class="btn-small red waves-effect waves-light">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination pour les utilisateurs -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages_users; $i++): ?>
            <a href="?page=<?= $i; ?>" class="waves-effect <?= $i == $page ? 'active' : '' ?>"> <?= $i; ?> </a>
        <?php endfor; ?>
    </div>

    <!-- Dernières Recettes 
    <h5>Dernières Recettes</h5>
    <div class="row">
        <?php foreach ($recipes as $recipe): ?>
            <div class="col s12 m6 l4">
                <div class="card">
                    <div class="card-image">
                        <img src="<?= htmlspecialchars($recipe['photo']); ?>" alt="<?= htmlspecialchars($recipe['titre']); ?>">
                        <span class="card-title"><?= htmlspecialchars($recipe['titre']); ?></span>
                    </div>
                    <div class="card-content">
                        <p><strong>Auteur:</strong> <?= htmlspecialchars($recipe['auteur']); ?></p>
                        <p><strong>Ingrédients:</strong> <?= htmlspecialchars($recipe['ingredients']); ?></p>
                        <p><strong>Catégorie:</strong> <?= htmlspecialchars($recipe['categorie']); ?></p>
                    </div>
                    <div class="card-action">
                        <a href="modifier_recette.php?id=<?= $recipe['id']; ?>" class="waves-effect waves-light">Modifier</a>
                        <a href="supprimer_recette.php?id=<?= $recipe['id']; ?>" class="red-text waves-effect waves-light">Supprimer</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

     Pagination pour les recettes
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages_recipes; $i++): ?>
            <a href="?page=<?= $i; ?>" class="waves-effect <?= $i == $page ? 'active' : '' ?>"> <?= $i; ?> </a>
        <?php endfor; ?>
    </div>
</div>

 JavaScript pour Materialize 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        M.AutoInit();
    });
</script>
</body>
</html> -->
