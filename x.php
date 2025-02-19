<?php
session_start();
include('config.php'); // Connexion à la base de données

if (isset($_GET['id'])) {
    $id_recette = $_GET['id'];

    $sql = "SELECT * FROM recettes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_recette, PDO::PARAM_INT);
    $stmt->execute();
    $recette = $stmt->fetch();

    if (!$recette) {
        echo "Recette non trouvée.";
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = uniqid('user_', true);
    }
    $user_id = $_SESSION['user_id'];

    $verif = $pdo->prepare("SELECT COUNT(*) FROM historique WHERE user_id = :user_id AND recette_id = :recette_id");
    $verif->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $verif->bindParam(':recette_id', $id_recette, PDO::PARAM_INT);
    $verif->execute();
    $deja_vu = $verif->fetchColumn();

    if ($deja_vu == 0) {
        $stmt_historique = $pdo->prepare("INSERT INTO historique (user_id, recette_id, titre) VALUES (:user_id, :recette_id, :titre)");
        $stmt_historique->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt_historique->bindParam(':recette_id', $id_recette, PDO::PARAM_INT);
        $stmt_historique->bindParam(':titre', $recette['titre'], PDO::PARAM_STR);
        $stmt_historique->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recette['titre']); ?></title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- <link rel="stylesheet" href="CSS/Recette.css"> -->
    <style>
        body {
            display: flex;
            background-color: #f4f4f4;
            font-family: 'Roboto', sans-serif;
            margin: 0;
        }

        .sidenav {
            width: 250px;
            background: linear-gradient(45deg, #0066cc, #003366);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }

        .sidenav .logo h5 {
            font-size: 24px;
            font-weight: bold;
        }

        .sidenav a {
            font-size: 18px;
            padding: 15px;
            transition: background 0.3s ease;
        }

        .sidenav a:hover {
            background-color: #004080;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
            background-color: #ffffff;
        }

        .card {
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            background-color: #ffffff;
        }

        .card-image img {
            width: 100%;
            border-radius: 15px;
        }

        .card-content h4 {
            color: #333;
            font-size: 26px;
            text-align: center;
            margin-bottom: 20px;
        }

        .recipe-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .recipe-actions i {
            cursor: pointer;
            transition: 0.3s;
            color: #0066cc;
        }

        .recipe-actions i:hover {
            color: #ff6347;
            transform: scale(1.3);
        }

        .separator {
            border-left: 2px solid #ccc;
            height: 100%;
            margin: 0 20px;
        }

        .row {
            margin-bottom: 20px;
        }

        .portion {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
        }

        .portion button {
            padding: 5px 10px;
            background-color: #0066cc;
            color: white;
            border-radius: 10px;
            border: none;
            cursor: pointer;
        }

        .portion button:hover {
            background-color: #005bb5;
        }

        .ingredients {
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 10px;
            margin-top: 20px;
        }

        .method-duration {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .method-duration span {
            font-size: 16px;
            color: #666;
        }

        .comment-section {
            margin-top: 30px;
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 15px;
        }

        .comment-section h5 {
            color: #0066cc;
            font-size: 22px;
        }

        .comment-section form {
            margin-top: 15px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .profile span {
            font-size: 18px;
            color: #333;
        }

        .comment-textarea {
            border-radius: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .carousel {
            margin-top: 30px;
        }

        .carousel-item img {
            border-radius: 10px;
            width: 100%;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
        }

        footer p {
            font-size: 14px;
        }

        footer strong {
            color: #0066cc;
        }

        .subscribe-btn {
            text-align: right;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<ul id="slide-out" class="sidenav sidenav-fixed blue darken-3">
    <li class="logo center-align">
        <h5 class="white-text">Menu</h5>
    </li>
    <li><a href="#" class="white-text"><i class="material-icons white-text">home</i>Accueil</a></li>
    <li><a href="#" class="white-text"><i class="material-icons white-text">restaurant_menu</i>Recettes</a></li>
    <li><a href="#" class="white-text"><i class="material-icons white-text">person</i>Profil</a></li>
    <li><a href="#" class="white-text"><i class="material-icons white-text">favorite</i>Favoris</a></li>
</ul>

<!-- Contenu principal -->
<div class="main-content">
    <div class="container">
        <div class="card hoverable">
            <div class="card-image">
                <img src="<?php echo htmlspecialchars($recette['photo']); ?>" alt="Image de la recette">
            </div>
            <div class="card-content">
                <h4 class="blue-text text-darken-3"><?php echo htmlspecialchars($recette['titre']); ?></h4>
                
                <div class="recipe-actions">
                    <i class="material-icons">favorite_border</i>
                    <i class="material-icons">share</i>
                    <i class="material-icons">file_download</i>
                </div>

                <!-- Portion -->
                <div class="portion">
                    <span>Portion</span>
                    <div>
                        <button id="decrement">-</button>
                        <span id="portionCount">1</span>
                        <button id="increment">+</button>
                    </div>
                </div>

                <div class="separator"></div>

                <!-- Ingrédients -->
                <div class="ingredients">
                    <h5>🛒 Ingrédients</h5>
                    <ul class="collection with-header">
                        <?php
                        $ingredients = explode("\n", trim($recette['ingredients']));
                        foreach ($ingredients as $ingredient) {
                            echo "<li class='collection-item'>" . htmlspecialchars(trim($ingredient)) . "</li>";
                        }
                        ?>
                    </ul>
                </div>

                <!-- Méthode, Durée et Note -->
                <div class="method-duration">
                    <span>⏱️ Durée: <?php echo htmlspecialchars($recette['duree']); ?> min</span>
                    <span>⭐ Note: <?php echo htmlspecialchars($recette['note']); ?>/5</span>
                </div>
                <h5>👨‍🍳 Méthode</h5>
                <p class="flow-text"><?php echo nl2br(htmlspecialchars($recette['description'])); ?></p>

                <!-- Commentaires -->
                <div class="comment-section">
                    <h5>💬 Commentaires</h5>
                    <div class="profile">
                        <img src="user-profile.jpg" alt="User Profile">
                        <span>Utilisateur</span>
                    </div>
                    <form method="POST">
                        <div class="input-field">
                            <textarea id="comment" class="comment-textarea" name="comment"></textarea>
                            <label for="comment">Ajouter un commentaire...</label>
                        </div>
                        <button class="btn blue waves-effect">Envoyer</button>
                    </form>
                    <a href="#">Voir les autres commentaires →</a>
                </div>

                <!-- Plus de recettes -->
                <h5>🍽️ Plus de Recettes de <?php echo htmlspecialchars($recette['auteur']); ?></h5>
                <div class="carousel">
                    <!-- Dynamically load author's recipes -->
                    <?php
                    $author_recipes = $pdo->prepare("SELECT * FROM recettes WHERE auteur = :auteur LIMIT 5");
                    $author_recipes->bindParam(':auteur', $recette['auteur'], PDO::PARAM_STR);
                    $author_recipes->execute();
                    while ($author_recipe = $author_recipes->fetch()) {
                        echo "<a class='carousel-item' href='recette.php?id=" . $author_recipe['id'] . "'><img src='" . $author_recipe['photo'] . "' alt='" . $author_recipe['titre'] . "'></a>";
                    }
                    ?>
                </div>

                <!-- Footer -->
                <p>Publié le <?php echo date("d/m/Y"); ?> par <strong><?php echo htmlspecialchars($recette['auteur']); ?></strong></p>
                
                <div class="subscribe-btn">
                    <button class="btn waves-effect blue">S'abonner</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts Materialize -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    M.Sidenav.init(document.querySelectorAll('.sidenav'));
    M.Carousel.init(document.querySelectorAll('.carousel'), {
        fullWidth: true,
        indicators: true
    });

    // Portion adjustment
    let portionCount = document.getElementById('portionCount');
    document.getElementById('increment').addEventListener('click', function() {
        portionCount.textContent = parseInt(portionCount.textContent) + 1;
    });
    document.getElementById('decrement').addEventListener('click', function() {
        if (parseInt(portionCount.textContent) > 1) {
            portionCount.textContent = parseInt(portionCount.textContent) - 1;
        }
    });
});
</script>

</body>
</html>
