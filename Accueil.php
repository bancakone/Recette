<?php
session_start();
include('config.php'); // Connexion à la base de données

// Limite de 4 recettes pour la page d'accueil
$limite = 4;

// Vérification si l'utilisateur est connecté et si une recette a été consultée
if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    $recette_id = $_GET['id'];

    // Vérifier si la recette n'est pas déjà dans l'historique de l'utilisateur
    $sql_check = "SELECT * FROM historique WHERE user_id = :user_id AND recette_id = :recette_id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt_check->bindParam(':recette_id', $recette_id, PDO::PARAM_INT);
    $stmt_check->execute();

    // Si la recette n'est pas déjà dans l'historique, l'ajouter
    if ($stmt_check->rowCount() == 0) {
        $sql_insert = "INSERT INTO historique (user_id, recette_id) VALUES (:user_id, :recette_id)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt_insert->bindParam(':recette_id', $recette_id, PDO::PARAM_INT);
        $stmt_insert->execute();
    }
}

// Requête pour récupérer 4 recettes pour l'actualité (recettes publiées de tous les utilisateurs)
$sql_actualite = "SELECT * FROM recettes WHERE statut = 'publie' ORDER BY date_creation DESC LIMIT :limite";
$stmt_actualite = $pdo->prepare($sql_actualite);
$stmt_actualite->bindParam(':limite', $limite, PDO::PARAM_INT);
$stmt_actualite->execute();
$recettes_actualite = $stmt_actualite->fetchAll();

// Requête pour récupérer uniquement les publications de l'utilisateur connecté (publications publiées)
$recettes_publications = [];
if (isset($_SESSION['user_id'])) {
    $sql_publications = "SELECT * FROM recettes WHERE user_id = :user_id AND statut = 'publie' ORDER BY date_creation DESC LIMIT 4";  // Filtrage par statut 'publie'
    $stmt_publications = $pdo->prepare($sql_publications);
    $stmt_publications->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt_publications->execute();
    $recettes_publications = $stmt_publications->fetchAll();
}

// Récupérer les 4 dernières recettes de l'historique de l'utilisateur
$sql_historique = "SELECT * FROM historique WHERE user_id = :user_id ORDER BY id DESC LIMIT 4";  // Limite 4
$stmt_historique = $pdo->prepare($sql_historique);
$stmt_historique->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT); 
$stmt_historique->execute();
$historique = $stmt_historique->fetchAll();

// Récupérer les détails des recettes dans l'historique une seule fois
$historique_recettes = [];
foreach ($historique as $item) {
    $id_recette = $item['recette_id'];
    $sql_recette = "SELECT * FROM recettes WHERE id = :id";
    $stmt_recette = $pdo->prepare($sql_recette);
    $stmt_recette->bindParam(':id', $id_recette, PDO::PARAM_INT);
    $stmt_recette->execute();
    $recette_details = $stmt_recette->fetch();
    $historique_recettes[] = $recette_details;
}

// Supposons que l'utilisateur est déjà authentifié et que $_SESSION['user_id'] est disponible
$stmt = $pdo->prepare("SELECT photo FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();

// Vérifier si la photo existe, sinon utiliser une image par défaut
if (isset($user['photo']) && !empty($user['photo'])) {
    $_SESSION['photo'] = $user['photo'];
} else {
    $_SESSION['photo'] = 'default-avatar.png';
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Recettes</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- <link rel="stylesheet" href="CSS/Accueil1.css"> -->
    <style>
          /* Ajout de styles personnalisés */
  body {
    font-family: 'Roboto', sans-serif;
    background-color: #f9f9f9;
  }

  .sidebar {
    background-color: #37474F; /* Couleur plus foncée pour un meilleur contraste */
    padding: 20px;
    height: 100vh;
    color: #fff;
    position: fixed;
    width: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
  }

  .sidebar .profile {
    margin-bottom: 20px;
    text-align: center;
    width: 100%;
  }

  .sidebar img {
    border-radius: 50%;
    margin-bottom: 10px;
    width: 100px;
    height: 100px;
    border: 3px solid #ff5722;
  }

  .sidebar h2 {
    font-size: 18px;
    font-weight: bold;
    margin: 5px 0;
    color: #ffffff;
  }

  .sidebar p {
    font-size: 14px;
    margin: 0;
    color: #cfd8dc;
  }

  .sidebar ul {
    padding: 0;
    list-style: none;
    width: 100%;
  }

  .sidebar ul li {
    width: 100%;
  }

  .sidebar ul li a {
    color: white !important;
    display: flex;
    align-items: center;
    padding: 12px 15px;
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

  .sidebar ul li a .material-icons {
    margin-right: 15px;
    font-size: 20px;
  }

  .content {
    margin-left: 270px;
    padding: 30px;
  }

  .carousel {
    margin-bottom: 30px;
  }

  .grid {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
  }

  .card {
    width: 23%;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
    border-radius: 5px;
    overflow: hidden;
  }

  .card img {
    width: 100%;
    height: 170px;
    object-fit: cover;
  }

  .card p {
    padding: 10px;
    text-align: center;
    font-weight: bold;
    color: #37474F;
  }

  .card:hover {
    transform: scale(1.05);
  }

  .view-more {
    text-align: center;
    margin-top: 20px;
  }

  .view-more a {
    text-decoration: none;
    color: #000;
    font-size: 24px;
    transition: 0.3s;
  }

  .view-more a:hover {
    color: #f00;
  }

  .btn-floating {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #ff5722;
  }

  .header-icons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
  }

  .search-bar {
    display: flex;
    align-items: center;
    background-color: #fff;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .search-bar i {
    margin-right: 10px;
  }

  .right-items span {
    margin-right: 10px;
  }

    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile">
            <?php if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['email'])): ?>
                <!-- Affichage de la photo avant le nom et prénom -->
                <?php if (isset($_SESSION['photo']) && $_SESSION['photo'] != ''): ?>
                    <img src="<?php echo $_SESSION['photo']; ?>" alt="Photo de profil" width="80" height="80" />
                <?php else: ?>
                    <img src="default-avatar.png" alt="Photo de profil" width="80" height="80" />
                <?php endif; ?>
                <!-- Affichage du nom, prénom et email -->
                <p><strong><?php echo $_SESSION['nom']; ?> <?php echo $_SESSION['prenom']; ?></strong><br><?php echo $_SESSION['email']; ?></p>
            <?php else: ?>
                <p><strong>Nom&Prénom</strong><br>Email</p>
            <?php endif; ?>
        </div>


        <ul>
            <li><a href="Profil.php" class="black-text"><i class="material-icons">person</i> Profil</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">favorite</i> Favoris</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">bookmark</i> Enregistrements</a></li>

            <?php if (isset($_SESSION['user_id'])): // Afficher la section publication uniquement si l'utilisateur est connecté ?>
                <li><a href="Brouillons.php" class="black-text"><i class="material-icons">save</i> Brouillons</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): // Afficher la section publication uniquement si l'utilisateur est connecté ?>
                <li><a href="Publication.php" class="black-text"><i class="material-icons">post_add</i> Publication</a></li>
            <?php endif; ?>

            <li><a href="Deconnexion.php" class="black-text"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">notifications</i> Notifications</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
             <li><a href="Historique.php" class="black-text"><i class="material-icons">history</i> Historique</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="content">
        <div class="carousel carousel-slider center" data-indicators="true">
            <div class="carousel-item" href="#one!">
                <img src="Image/vue.avif" alt="Image 1">
            </div>
            <div class="carousel-item" href="#two!">
                <img src="Image/vue1.jpg" alt="Image 2">
            </div>
            <div class="carousel-item" href="#three!">
                <img src="https://via.placeholder.com/800x400" alt="Image 3">
            </div>
            <div class="carousel-item" href="#four!">
                <img src="https://via.placeholder.com/800x400" alt="Image 4">
            </div>
        </div>

        <div class="header-icons">
            <div class="left-items">
                <div class="search-bar">
                    <i class="material-icons">search</i>
                </div>
            </div>
            <div class="right-items">
                <span>Catégories</span>
                <i class="material-icons">menu</i>
            </div>
        </div>

        <h5>Actualité</h5>
        <div class="grid">
            <?php foreach ($recettes_actualite as $recette): ?>
                <div class="card">
                    <a href="Recette.php?id=<?php echo $recette['id']; ?>">
                        <img src="<?php echo $recette['photo']; ?>" alt="Image de la recette">
                        <p><?php echo $recette['titre']; ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="view-more">
            <a href="Actualité.php"><i class="material-icons">arrow_forward</i></a>
        </div>


            <h5>Recherches Récentes</h5>

                        <?php if (isset($_SESSION['user_id']) && count($historique_recettes) > 0): ?>
                <div class="grid">
                    <?php foreach ($historique_recettes as $recette_details): ?>
                        <div class="card">
                            <a href="Recette.php?id=<?php echo $recette_details['id']; ?>">
                                <img src="<?php echo $recette_details['photo']; ?>" alt="Image de la recette">
                                <p><?php echo $recette_details['titre']; ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p>Aucune recherche récente disponible.</p>
                    <?php endif; ?>
                    <div class="view-more">
                        <a href="Historique.php">
                            <i class="material-icons">arrow_forward</i>
                        </a>
                    </div>



        <?php if (isset($_SESSION['user_id']) && count($recettes_publications) > 0): ?>
            <h5>Publications</h5>
            <div class="grid">
                <?php foreach ($recettes_publications as $recette): ?>
                    <div class="card">
                        <a href="Recette.php?id=<?php echo $recette['id']; ?>">
                            <img src="<?php echo $recette['photo']; ?>" alt="Image de la recette">
                            <p><?php echo $recette['titre']; ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="view-more">
                <a href="Publication.php"><i class="material-icons">arrow_forward</i></a>
            </div>
        <?php endif; ?>
    </div>

    <a href="<?php echo isset($_SESSION['user_id']) ? 'Modification.php' : 'Connexion.php'; ?>" class="btn-floating btn-large red floating-btn">
        <i class="material-icons">add</i>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.carousel');
            var instances = M.Carousel.init(elems, {
                fullWidth: true,
                indicators: true
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>