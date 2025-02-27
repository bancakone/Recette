<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

// $user_id = $_SESSION['user_id'];
$user_id = $_GET['user_id'];

// Récupérer les infos utilisateur
$sql_user = "SELECT nom, prenom, email , photo FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

// Récupérer les recettes publiées par l'utilisateur
$sql = "SELECT * FROM recettes WHERE user_id = :user_id AND statut = 'publie' ORDER BY date_creation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$recettes = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Publications</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="CSS/Publication.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php if (isset($_SESSION['photo']) && $_SESSION['photo'] != ''): ?>
            <img src="<?php echo $_SESSION['photo']; ?>" alt="Photo de profil" width="80" height="80" />
        <?php else: ?>
            <img src="default-avatar.png" alt="Photo de profil" width="80" height="80" />
        <?php endif; ?>
        <h6><?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></h6>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <a href="Accueil.php"><i class="material-icons">home</i> Accueil</a>
        <a href="Profil.php"><i class="material-icons">account_circle</i> Profil</a>
        <a href="Favoris.php"><i class="material-icons">favorite</i> Favoris</a>
        <a href="Enregistrement.php"><i class="material-icons">bookmark</i> Enregistrements</a>
        <a href="Brouillons.php"><i class="material-icons">drafts</i> Brouillons</a>
        <a href="Deconnexion.php"><i class="material-icons">exit_to_app</i> Déconnexion</a>
        <a href="Historique.php"><i class="material-icons">history</i> Historique</a>
        <a href="Notifications"><i class="material-icons">notifications</i> Notifications</a>
    </div>

    <!-- Contenu principal -->
    <div class="content">
        <div class="row">
            <?php if (!empty($recettes)): ?>
                <?php foreach ($recettes as $recette): ?>
                    <div class="col s12 m6 l3">
                        <div class="card">
                            <div class="card-image">
                                <img src="<?php echo $recette['photo']; ?>" alt="Image de la recette">
                            </div>
                            <div class="card-content">
                                <p><?php echo htmlspecialchars($recette['titre']); ?></p>
                            </div>
                            <div class="card-action">
                                <a href="Recette.php?id=<?php echo $recette['id']; ?>" class="btn green waves-effect waves-light">
                                    <i class="material-icons center">visibility</i> 
                                </a>
                                <a href="ModifierRecette.php?id=<?php echo $recette['id']; ?>" class="btn blue lighten-1 waves-effect waves-light">
                                    <i class="material-icons center">edit</i>
                                </a>
                                <a href="SupprimerRecette.php?id=<?php echo $recette['id']; ?>" class="btn red darken-1 waves-effect waves-light">
                                    <i class="material-icons center">delete</i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Vous n'avez aucune recette publiée pour le moment.</p>
            <?php endif; ?>
        </div>

        <!-- Bouton d'ajout flottant -->
        <a href="AjouterRecette.php" class="btn-floating btn-large red waves-effect waves-light">
            <i class="material-icons">add</i>
        </a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
