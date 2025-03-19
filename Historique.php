<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit;
}


$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$sql_user = "SELECT nom, prenom, email, photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();
// Récupérer les 4 dernières recherches de l'historique
$sql_historique = "SELECT * FROM historique WHERE user_id = :user_id ORDER BY id DESC LIMIT 4";
$stmt_historique = $pdo->prepare($sql_historique);
$stmt_historique->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
$stmt_historique->execute();
$historique = $stmt_historique->fetchAll(PDO::FETCH_ASSOC);

// Notifications
$sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND lu = FALSE";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$notif_count = $stmt->fetchColumn();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
    .sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    background-color: #343a40;
    color: white;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Style du profil */
.sidebar .text-center {
    text-align: center;
  
}

.sidebar img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 1px;
}

/* Espacement entre icône et texte */
.sidebar ul {
    padding: 0;
    list-style: none;
}

.sidebar ul li {
    margin-bottom: 9px; /* Espacement entre chaque élément */
}

.sidebar ul li a {
    color: white !important;
    display: flex;
    align-items: center;
    padding: 6px 15px;
    text-decoration: none;
    transition: 0.3s;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
}

.sidebar ul li a i {
    margin-right: 10px; /* Ajout d'espace entre icône et texte */
    font-size: 22px;
}

.sidebar ul li a:hover {
    background-color: #ff5722;
    transform: translateX(5px);
}

/* Pour le badge de notification */
.sidebar ul li a .badge {
    background-color: red;
    color: white;
    font-size: 14px;
    margin-left: auto;
    padding: 3px 8px;
    border-radius: 12px;
}

/* Ajustement du contenu principal */
.content {
    margin-left: 270px; /* Ajusté pour correspondre à la sidebar */
    padding: 20px;
    width: calc(100% - 280px);
}

.grid {
    display: flex;
    justify-content: flex-start; /* Aligne les cartes sur la gauche */
    flex-wrap: wrap;
    gap: 0; /* Aucune marge entre les cartes */
    margin: 0; /* Supprime toute marge autour de la grille */
}

.card {
    width: 23%;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
    border-radius: 10px;
    overflow: hidden;
    margin: 7px; /* Supprime la marge de chaque carte */
    padding: 0; /* Supprime le padding interne de la carte */
}

.card img {
    width: 100%;
    height: 270px;
    object-fit: cover;
}

.card p {
    font-size: 18px;
    text-align: center;
    padding: 12px;
    font-weight: 600;
    line-height: 1.5;
    color: #333;
    margin: 10px; /* Supprime la marge dans le texte des cartes */
}

.card:hover {
    transform: scale(1.05);
}

/* Section "Voir plus" */
.view-more {
    text-align: center;
    margin-top: 15px;
}

/* Texte des liens "Voir plus" */
.view-more a {
    font-size: 18px; /* Taille du texte du lien */
    font-weight: 500; /* Semi-gras */
}

.view-more a:hover {
    color: #f00; /* Changer la couleur lors du survol */
}

/* Titres de la page */
h5 {
    font-size: 2rem; /* Taille de police plus grande */
    font-weight: bold; /* Police en gras */
    color: #333; /* Couleur sombre pour une bonne lisibilité */
    text-align: center; /* Centre le titre */
    text-transform: uppercase; /* Met le texte en majuscules */
    margin-bottom: 30px; /* Espacement en bas pour aérer */
    letter-spacing: 1px; /* Espacement des lettres pour un effet plus moderne */
    border-bottom: 2px solid #ff5722; /* Ajoute une bordure colorée en bas du titre */
    padding-bottom: 10px; /* Espacement entre le titre et la bordure */
    font-family: 'Roboto', sans-serif; /* Police moderne et propre */
}

h5.mb-4 {
    margin-bottom: 40px; /* Si vous voulez un espacement supplémentaire en bas */
}
    </style>
</head>
<body>


<!-- Barre latérale -->
<div class="sidebar">
        <div class="text-center">
            <img src="<?= htmlspecialchars($user['photo'] ?? 'default.png') ?>" 
                 alt="Avatar" class="rounded-circle">
            <p><?= htmlspecialchars($user['nom'] . " " . $user['prenom']) ?></p>
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

        <!-- <div class="view-more">
            <a href="Accueil.php">
                <i class="material-icons">arrow_back</i> Retour à l'accueil
            </a>
        </div> -->
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
