<?php
session_start();
include('config.php'); // Connexion à la base de données


// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les infos utilisateur
$sql_user = "SELECT nom, prenom, email FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['user_id' => $user_id]);
$user = $stmt_user->fetch();

// Récupérer les brouillons de l'utilisateur
$sql = "SELECT id, titre,  photo FROM recettes WHERE statut = 'brouillon' AND user_id = :user_id";
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
    <link rel="stylesheet" href="CSS/Brouillons.css">
</head>

<body>
     <!-- Sidebar -->
     <div class="sidebar">
        <h6><?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></h6>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <a href="Accueil.php"><i class="material-icons">home</i> Accueil</a>
        <a href="#"><i class="material-icons">account_circle</i> Profil</a>
        <a href="#"><i class="material-icons">favorite</i> Favoris</a>
        <a href="#"><i class="material-icons">bookmark</i> Enregistrements</a>
        <a href="#"><i class="material-icons">drafts</i> Brouillons</a>
        <a href="#"><i class="material-icons">exit_to_app</i> Déconnexion</a>
        <a href="#"><i class="material-icons">notifications</i> Notifications</a>
    </div>

    <div class="container">
        <h2 class="center-align">Mes Brouillons</h2>
        
        <div class="row">
            <?php if (count($brouillons) > 0): ?>
                <?php foreach ($brouillons as $brouillon): ?>
                    <div class="col s12 m6 l4">
                        <div class="card">
                            <div class="card-image">
                                <img src="images/<?php echo !empty($brouillon['photo']) ? htmlspecialchars($brouillon['photo']) : 'default.jpg'; ?>" alt="Image de la recette">
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
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="center-align">Aucun brouillon trouvé.</p>
            <?php endif; ?>
        </div>

        <div class="center-align">
            <a href="Accueil.php" class="btn">Retour à l'accueil</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
