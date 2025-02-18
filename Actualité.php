<?php
session_start();
include('config.php'); // Connexion à la base de données

// Requête pour récupérer toutes les recettes
$sql = "SELECT * FROM recettes ORDER BY date_creation DESC";
$stmt = $pdo->query($sql);
$recettes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Recettes</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="CSS/Accueil1.css">
</head>
<body>
    <h5>Toutes les Recettes</h5>
    <div class="grid">
        <?php foreach ($recettes as $recette): ?>
            <div class="card">
                <!-- Lien vers la page de détails -->
                <a href="Recette.php?id=<?php echo $recette['id']; ?>">
                    <img src="<?php echo $recette['photo']; ?>" alt="Image de la recette">
                    <p><?php echo $recette['titre']; ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="index.php" class="btn-floating btn-large red floating-btn">
        <i class="material-icons">arrow_back</i>
    </a>
</body>
</html>
