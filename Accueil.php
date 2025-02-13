<?php
session_start();  // Démarrer la session
include('config.php'); // Inclut le fichier de connexion à la base de données avec PDO
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Recettes</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="CSS/Accueil1.css">
   
</head>
<body>
    <div class="sidebar">
        <div class="profile">
            <?php if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['email'])): ?>
                <!-- Affichage de la photo avant le nom et prénom -->
                <?php if (isset($_SESSION['photo'])): ?>
                    <img src="<?php echo $_SESSION['photo']; ?>" alt="Photo de profil" width="80" height="80" />
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
            <li><a href="#" class="black-text"><i class="material-icons">archive</i> Brouillons</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">post_add</i> Publication</a></li>
            <li><a href="Deconnexion.php" class="black-text"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">notifications</i> Notifications</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="carousel carousel-slider center" data-indicators="true">
            <div class="carousel-item" href="#one!">
                <img src="Image/vue.avif" alt="Image 1">
            </div>
            <div class="carousel-item" href="#two!">
                <img src="Image/vue1.avif" alt="Image 2">
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
            <div class="card"><img src="Image/Chocolat.jpeg"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
        </div>
        <div class="view-more">
             <i class="material-icons">arrow_forward</i>
        </div>

        <h5>Recherches Récentes</h5>
        <div class="grid">
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
        </div>
        <div class="view-more">
             <i class="material-icons">arrow_forward</i>
        </div>

        <h5>Publications</h5>
        <div class="grid">
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
        </div>

        <div class="view-more">
             <i class="material-icons">arrow_forward</i>
        </div>
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
