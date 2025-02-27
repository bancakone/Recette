<?php
session_start();
include('config.php'); // Connexion à la base de données

if (!isset($_GET['nom'])) {
    die("Catégorie non spécifiée.");
}

$categorie_nom = $_GET['nom'];

// Récupérer l'ID de la catégorie
$sql_categorie = "SELECT id FROM categories WHERE nom = :nom";
$stmt_categorie = $pdo->prepare($sql_categorie);
$stmt_categorie->bindParam(':nom', $categorie_nom, PDO::PARAM_STR);
$stmt_categorie->execute();
$categorie = $stmt_categorie->fetch();

if (!$categorie) {
    die("Catégorie introuvable.");
}

$categorie_id = $categorie['id'];

// Récupérer les recettes de cette catégorie
$sql_recettes = "SELECT * FROM recettes WHERE categorie_id = :categorie_id AND statut = 'publie' ORDER BY date_creation DESC";
$stmt_recettes = $pdo->prepare($sql_recettes);
$stmt_recettes->bindParam(':categorie_id', $categorie_id, PDO::PARAM_INT);
$stmt_recettes->execute();
$recettes = $stmt_recettes->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recettes - <?php echo htmlspecialchars($categorie_nom); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>
<body>
    <div class="container">
        <h3>Recettes : <?php echo htmlspecialchars($categorie_nom); ?></h3>
        <div class="row">
            <?php if (count($recettes) > 0): ?>
                <?php foreach ($recettes as $recette): ?>
                    <div class="col s12 m4">
                        <div class="card">
                            <div class="card-image">
                                <img src="<?php echo $recette['photo']; ?>" alt="Image">
                                <span class="card-title"><?php echo htmlspecialchars($recette['titre']); ?></span>
                            </div>
                            <div class="card-content">
                                <p><?php echo htmlspecialchars(substr($recette['description'], 0, 100)) . '...'; ?></p>
                            </div>
                            <div class="card-action">
                                <a href="Recette.php?id=<?php echo $recette['id']; ?>">Voir la recette</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune recette trouvée pour cette catégorie.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
