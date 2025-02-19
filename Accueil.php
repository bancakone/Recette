<?php
session_start();
include('config.php'); // Connexion à la base de données

// Limite de 4 recettes pour la page d'accueil
$limite = 4;

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
                <li><a href="Brouillons.php" class="black-text"><i class="material-icons">post_add</i> Brouillons</a></li>
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
                        <div class="view-more">
                            <a href="Historique.php">
                                <i class="material-icons">arrow_forward</i>
                            </a>
                        </div>
                    <?php else: ?>
                        <p>Aucune recherche récente disponible.</p>
                    <?php endif; ?>



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