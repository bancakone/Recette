<?php
session_start();
include('config.php'); // Connexion à la base de données


// Limite de 4 recettes pour la page d'accueil
$limite = 4;

// Récupération des catégories
$query = $pdo->query("SELECT nom FROM categories");
$categories = $query->fetchAll(PDO::FETCH_ASSOC);

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

    // Vérifier que l'ID est bien un entier valide
    if (!is_numeric($id_recette) || $id_recette <= 0) {
        continue; // Passer à l'élément suivant si l'ID est invalide
    }

    $sql_recette = "SELECT * FROM recettes WHERE id = :id";
    $stmt_recette = $pdo->prepare($sql_recette);
    $stmt_recette->bindParam(':id', $id_recette, PDO::PARAM_INT);
    $stmt_recette->execute();
    $recette_details = $stmt_recette->fetch();

    // Ajouter uniquement si la recette existe
    if ($recette_details) {
        $historique_recettes[] = $recette_details;
    }
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

// Notifications
$sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND lu = FALSE";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$notif_count = $stmt->fetchColumn();

// Récupérer le nombre d'abonnements (combien de personnes l'utilisateur suit)
$sql_abonnements = "SELECT COUNT(*) FROM abonnement WHERE follower_id = :user_id";
$stmt_abonnements = $pdo->prepare($sql_abonnements);
$stmt_abonnements->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt_abonnements->execute();
$nb_abonnements = $stmt_abonnements->fetchColumn();


// Récupérer le nombre d'abonnés (combien de personnes suivent l'utilisateur)
$sql_abonnes = "SELECT COUNT(*) FROM abonnement WHERE following_id = :user_id";
$stmt_abonnes = $pdo->prepare($sql_abonnes);
$stmt_abonnes->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt_abonnes->execute();
$nb_abonnes = $stmt_abonnes->fetchColumn();

?>
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Recettes</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
     <link rel="stylesheet" href="CSS/Accueil.css">  
</head>
<style>
 /* Barre de recherche */
 .search-bar {
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            padding: 10px;
            border-radius: 25px;
            background: #f1f1f1;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .search-bar i {
            margin-right: 10px;
            color: #007bff;
        }

        .search-bar input {
            border: none;
            outline: none;
            flex: 1;
            background: transparent;
            font-size: 16px;
        }

        /* Conteneur des résultats */
        #resultats {
            margin: 20px auto;
            max-width: 600px;
        }

        /* Style des résultats */
        .result-card {
            display: flex;
            align-items: center;
            background: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .result-card img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }

        .result-card a {
            text-decoration: none;
            font-weight: bold;
            color: #007bff;
        }
</style>
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
            <div class="abonnements-info">

</div>

        </div>


        <ul>
            <li><a href="Profil.php" class="black-text"><i class="material-icons">person</i> Profil</a></li>
            <li><a href="Favoris.php" class="black-text"><i class="material-icons">favorite</i> Favoris</a></li>
            <li><a href="Enregistrement.php" class="black-text"><i class="material-icons">bookmark</i> Enregistrements</a></li>

            <?php if (isset($_SESSION['user_id'])): // Afficher la section publication uniquement si l'utilisateur est connecté ?>
                <li><a href="Brouillons.php" class="black-text"><i class="material-icons">save</i> Brouillons</a></li>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): // Afficher la section publication uniquement si l'utilisateur est connecté ?>
                <li><a href="Publication.php" class="black-text"><i class="material-icons">post_add</i> Publication</a></li>
            <?php endif; ?>

            <li>
                <a href="Notification.php">
                    <i class="material-icons">notifications</i> Notifications
                    <?php if ($notif_count > 0): ?>
                        <span class="new badge red" data-badge-caption=""><?php echo $notif_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="Historique.php" class="black-text"><i class="material-icons">history</i> Historique</a></li>
                    <?php endif; ?>
                    <li><a href="Deconnexion.php" class="black-text"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
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
                <img src="Image/I2.jpg" alt="Image 3">
            </div>
            <div class="carousel-item" href="#four!">
                <img src="Image/I3.jpg" alt="Image 4">
            </div>
        </div>

        <div class="header-icons">
            <div class="left-items">
              <!-- Barre de recherche -->
<div class="search-bar">
    <i class="material-icons">search</i>
    <input type="text" id="search" name="query" placeholder="Rechercher..." required>
</div>

<!-- Conteneur pour afficher les résultats -->
<div id="resultats"></div>



            </div>
 

<div class="right-items">
    <span>Catégories</span>
    <a class="dropdown-trigger" href="#" data-target="dropdown-categories">
        <i class="material-icons">menu</i>
    </a>

<ul id="dropdown-categories" class="dropdown-content">
    <?php foreach ($categories as $categorie): ?>
        <li><a href="Categorie.php?nom=<?= urlencode($categorie['nom']) ?>"><?= htmlspecialchars($categorie['nom']) ?></a></li>
    <?php endforeach; ?>
</ul>
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
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="Actualité.php"><i class="material-icons">arrow_forward</i></a>
    <?php else: ?>
        <a href="Actualité1.php"><i class="material-icons">arrow_forward</i></a>
    <?php endif; ?>
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
      fullWidth: true,   // Mode plein écran
      indicators: true,  // Affichage des indicateurs
      duration: 200,     // Durée de transition entre les images (en ms)
    });

    // Défilement automatique toutes les 3 secondes
    setInterval(() => {
      var activeCarousel = M.Carousel.getInstance(document.querySelector('.carousel'));
      activeCarousel.next();
    }, 2000); // Changer toutes les 3 secondes
  });

    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(elems);
    });

document.getElementById("search").addEventListener("input", function() {
    let query = this.value.trim();

    if (query.length > 1) { // Pour éviter les requêtes trop courtes
        fetch("Recherche.php?q=" + encodeURIComponent(query))
        .then(response => response.text())
        .then(data => {
            document.getElementById("resultats").innerHTML = data;
        })
        .catch(error => console.error("Erreur de recherche :", error));
    } else {
        document.getElementById("resultats").innerHTML = "";
    }
});
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    
</body>
</html>