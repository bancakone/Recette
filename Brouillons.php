<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les infos utilisateur, y compris la photo de profil
$sql_user = "SELECT nom, prenom, email, photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

// Récupérer les brouillons de l'utilisateur
$sql = "SELECT id, titre, photo FROM recettes WHERE statut = 'brouillon' AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$brouillons = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Brouillons</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Brouillons1.css">
</head>
<style>
body {
    display: flex;
    margin: 0;
    font-family: Arial, sans-serif;
}

/* 🔹 Sidebar */
.sidebar {
    width: 260px;
    background-color: #37474F;
    color: white;
    height: 100vh;
    padding: 20px 0;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
 
}

/* 🔹 Profil utilisateur */
.user-info {
    text-align: center;
    padding: 10px;
}

.user-info img {
    border-radius: 50%;
    width: 100px; /* Augmenté */
    height: 100px;
    margin-bottom: 10px;
}

.user-info h6 {
    font-size: 18px; /* Augmenté */
    font-weight: bold;
    margin: 5px 0;
}

.user-info p {
    font-size: 16px; /* Augmenté */
    margin: 0;
}

/* 🔹 Liens de navigation */
.sidebar a {
    color: white;
    display: flex;
    align-items: center;
    padding: 15px;
    font-size: 16px; /* Augmenté */
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a i {
    font-size: 20px; /* Augmenté */
    margin-right: 10px;
}

.sidebar a:hover {
    background-color: #455A64;
    border-radius: 5px;
}

/* 🔹 Contenu principal */
.container {
    margin-left: 270px; /* Ajusté */
    padding: 20px;
    width: calc(100% - 270px);
}

/* 🔹 Cartes de recettes */
.card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s;
}

.card:hover {
    /* transform: scale(1.03); */
    transform: translateX(5px);
}

/* 🔹 Image des recettes */
.card-image img {
    width: 100%;
    height: 300px; /* Ajusté */
    object-fit: cover;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

/* 🔹 Titre */
.card-content {
    text-align: center;
    font-weight: bold;
    padding: 10px;
    font-size: 1.2em;
}

/* 🔹 Boutons */
.card-action {
    display: flex;
    justify-content: center;
    gap: 10px;
    padding: 10px;
}

.card-action .btn {
    flex: 1;
    border-radius: 5px;
    text-align: center;
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

</style>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-info">
            <!-- Afficher la photo de profil -->
            <?php if (isset($_SESSION['photo']) && $_SESSION['photo'] != ''): ?>
                    <img src="<?php echo $_SESSION['photo']; ?>" alt="Photo de profil" width="80" height="80" />
                <?php else: ?>
                    <img src="default-avatar.png" alt="Photo de profil" width="80" height="80" />
                <?php endif; ?>
            <h6><?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></h6>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <a href="Accueil.php"><i class="material-icons">home</i> Accueil</a>
        <a href="Profil.php"><i class="material-icons">account_circle</i> Profil</a>
        <a href="Favoris.php"><i class="material-icons">favorite</i> Favoris</a>
        <a href="Enregistrement.php"><i class="material-icons">bookmark</i> Enregistrements</a>
        <a href="Publication.php"><i class="material-icons">post_add</i> Publications</a>
        <a href="Deconnexion.php"><i class="material-icons">exit_to_app</i> Déconnexion</a>
        <a href="Historique.php"><i class="material-icons">history</i> Historique</a>
        <a href="Notifications"><i class="material-icons">notifications</i> Notifications</a>
    </div>

    <div class="container">
       
        
        <div class="row">
            <?php if (count($brouillons) > 0): ?>
                <?php foreach ($brouillons as $brouillon): ?>
                    <div class="col s12 m6 l4">
                        <div class="card">
                             <div class="card-image">
                                <img src="<?php echo !empty($brouillon['photo']) ? 'images/' . htmlspecialchars($brouillon['photo']) : 'images/default.jpg'; ?>" alt="Image de la recette">
                            </div>

                            <div class="card-content">
                                <span class="card-title"><?php echo htmlspecialchars($brouillon['titre']); ?></span>
                            </div>
                            <div class="card-action">
                                <a href="ModifierBrouillons.php?id=<?php echo $brouillon['id']; ?>" class="btn blue">
                                    <i class="material-icons">edit</i>
                                </a>
                                <a href="SupprimerBrouillon.php?id=<?php echo $brouillon['id']; ?>" class="btn red">
                                    <i class="material-icons">delete</i>
                                </a>
                                <a href="PublierBrouillon.php?id=<?php echo $brouillon['id']; ?>" class="btn green">
                                    <i class="material-icons">publish</i> 
                                </a>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="center-align">Aucun brouillon trouvé.</p>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
