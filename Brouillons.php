<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer les brouillons de l'utilisateur
$sql = "SELECT * FROM recettes WHERE statut = 'brouillon' AND user_id = :user_id";
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
</head>
<body>
    <div class="container">
        <h2>Mes Brouillons</h2>
        
        <?php if (count($brouillons) > 0): ?>
            <ul class="collection">
                <?php foreach ($brouillons as $brouillon): ?>
                    <li class="collection-item">
                        <strong><?php echo htmlspecialchars($brouillon['titre']); ?></strong><br>
                        <span><?php echo htmlspecialchars($brouillon['description']); ?></span><br>
                        <a href="modifier_brouillon.php?id=<?php echo $brouillon['id']; ?>" class="secondary-content">Modifier</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucun brouillon trouvé.</p>
        <?php endif; ?>

        <a href="Accueil.php" class="btn">Retour à l'accueil</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
