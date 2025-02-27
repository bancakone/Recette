<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit;
}

// Récupérer les 4 dernières recherches de l'historique
$sql_historique = "SELECT * FROM historique WHERE user_id = :user_id ORDER BY id DESC LIMIT 4";
$stmt_historique = $pdo->prepare($sql_historique);
$stmt_historique->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
$stmt_historique->execute();
$historique = $stmt_historique->fetchAll(PDO::FETCH_ASSOC);

// Si l'historique est vide, on peut afficher un message d'alerte
if (empty($historique)) {
    echo "Aucune recette trouvée dans l'historique.";
    exit;
}

// Récupérer les ID des recettes dans l'historique
$recette_ids = array_map(function($item) {
    return $item['recette_id'];
}, $historique);

// Si des recettes sont présentes dans l'historique, on les récupère en une seule requête
if (!empty($recette_ids)) {
    $placeholders = implode(',', array_fill(0, count($recette_ids), '?'));
    $sql_recettes = "SELECT * FROM recettes WHERE id IN ($placeholders)";
    $stmt_recettes = $pdo->prepare($sql_recettes);
    $stmt_recettes->execute($recette_ids);
    $historique_recettes = $stmt_recettes->fetchAll(PDO::FETCH_ASSOC);
} else {
    $historique_recettes = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Recherches</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
   
    <style>
      .sidebar {
    width: 250px;
    background-color: #37474F;
    color: white;
    height: 100vh; /* Pleine hauteur de l'écran */
    padding: 10px 0; /* Moins de padding */
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow-y: auto; /* Défilement si nécessaire */
}

/* Profil */
.profile {
    text-align: center;
    padding-bottom: 10px;
}

.profile img {
    border-radius: 50%;
    width: 80px; /* Augmenté */
    height: 80px;
    margin-bottom: 10px;
}

.profile h6 {
    font-size: 18px; /* Augmenté */
    font-weight: bold;
    margin: 0;
    padding: 0;
}

.profile p {
    font-size: 16px; /* Augmenté */
    margin: 0;
    padding: 0;
}

/* Liens de la sidebar */
.sidebar a {
    color: white;
    display: flex;
    align-items: center;
    padding: 12px 15px; /* Ajusté */
    font-size: 16px; /* Augmenté */
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a i {
    font-size: 20px; /* Augmenté */
    margin-right: 30px; /* Ajusté */
}

.sidebar a:hover {
    background-color: #455A64;
    border-radius: 5px;
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
/* Ajustement du contenu principal */
.content {
    margin-left: 260px; /* Ajusté pour correspondre à la sidebar */
    padding: 20px;
    width: calc(100% - 260px);
}

/* Mise en page des cartes */
.grid {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px; /* Espacement ajusté */
}

.card {
    width: 29%;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
    border-radius: 5px;
    overflow: hidden;
}

.card img {
    width: 100%;
    height: 290px;
    object-fit: cover;
}

.card p {
    padding: 10px;
    text-align: center;
    font-size: 16px; /* Augmenté */
}

.card:hover {
    transform: scale(1.05);
}

/* Section "Voir plus" */
.view-more {
    text-align: center;
    margin-top: 15px;
}

.view-more a {
    text-decoration: none;
    color: #000;
    font-size: 18px; /* Ajusté */
}

.view-more a:hover {
    color: #f00;
}

    </style>
</head>
<body>
<div class="sidebar">
    <div class="profile">
        <?php if (isset($_SESSION['photo']) && $_SESSION['photo'] != ''): ?>
            <img src="<?php echo $_SESSION['photo']; ?>" alt="Photo de profil">
        <?php else: ?>
            <img src="default-avatar.png" alt="Photo de profil">
        <?php endif; ?>
        <h6><?php echo htmlspecialchars($_SESSION['nom']) . ' ' . htmlspecialchars($_SESSION['prenom']); ?></h6>
        <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
    </div>

    <a href="Accueil.php"><i class="material-icons">home</i> Accueil</a>
    <a href="Profil.php"><i class="material-icons">account_circle</i> Profil</a>
    <a href="Favoris.php"><i class="material-icons">favorite</i> Favoris</a>
    <a href="Enregistrement.php"><i class="material-icons">bookmark</i> Enregistrements</a>
    <a href="Publication.php"><i class="material-icons">post_add</i> Publications</a>
    <a href="Brouillons.php"><i class="material-icons">drafts</i> Brouillons</a>
    <a href="Notifications.php"><i class="material-icons">notifications</i> Notifications</a>
    <a href="Deconnexion.php"><i class="material-icons">exit_to_app</i> Déconnexion</a>
</div>


    <div class="content">
        <h5>Historique des Recherches</h5>
        <div class="grid">
            <?php if (count($historique_recettes) > 0): ?>
                <?php foreach ($historique_recettes as $recette_details): ?>
                    <div class="card">
                        <a href="Recette.php?id=<?php echo $recette_details['id']; ?>">
                            <?php if (isset($recette_details['photo']) && $recette_details['photo'] != ''): ?>
                                <img src="<?php echo $recette_details['photo']; ?>" alt="Image de la recette">
                            <?php else: ?>
                                <img src="default_image.jpg" alt="Image de la recette">
                            <?php endif; ?>
                            <p><?php echo isset($recette_details['titre']) ? $recette_details['titre'] : 'Titre non disponible'; ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune recherche récente disponible.</p>
            <?php endif; ?>
        </div>

        <div class="view-more">
            <a href="Accueil.php">
                <i class="material-icons">arrow_back</i> Retour à l'accueil
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
