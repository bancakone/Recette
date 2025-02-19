<?php
// Connexion à la base de données
$conn = new PDO("mysql:host=localhost;dbname=recette", "root", "");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si un ID est présent dans l'URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Récupérer les infos de la recette avec les informations de l'auteur
    $stmt = $conn->prepare("
    SELECT r.*, u.nom, u.prenom, DATE_FORMAT(r.date_creation, '%d/%m/%Y à %H:%i') AS date_creation
    FROM recettes r
    JOIN users u ON r.user_id = u.id
    WHERE r.id = ?
");
    $stmt->execute([$id]);
    $recette = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si la recette existe
    if (!$recette) {
        die("Recette introuvable.");
    }
} else {
    die("Aucune recette spécifiée.");
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Recette</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Recette.css">
</head>
<body>
    <div class="sidebar">
        <h5>Nom&Prénom</h5>
        <p>Email</p>
        <ul>
    <li><a href="Accueil.php"><i class="material-icons">home</i> Accueil</a></li>
    <li><a href="Profil.php"><i class="material-icons">person</i> Profil</a></li>
    <li><a href="Favoris.php"><i class="material-icons">favorite</i> Favoris</a></li>
    <li><a href="Enregistrements.php"><i class="material-icons">bookmark</i> Enregistrements</a></li>
    <li><a href="Brouillons.php"><i class="material-icons">save</i> Brouillons</a></li>
</ul>

    </div>

    <div class="container">
        <div class="recipe-image">
        <img src="<?php echo htmlspecialchars($recette['photo']); ?>" alt="Image de la recette">
        <!-- Ajoutez ceci pour déboguer -->
       

        </div>
        <div class="recipe-content">
            <div class="header">
            <h4><?php echo htmlspecialchars($recette['titre']); ?></h4>
                <div class="icons">
                    <i class="material-icons">favorite_border</i>
                    <i class="material-icons">share</i>
                    <i class="material-icons">file_download</i>
                </div>
            </div>
            <div class="counter-container">
                <span>Portion:</span>
                <div class="counter">
                    <button>-</button>
                    <span>1</span>
                    <button>+</button>
                </div>
            </div>
            <div class="ingredients-methods">
                <div class="ingredients">
                    <h5>Ingrédients</h5>
                    <ul>
                        <?php
                        $ingredients = explode("\n", $recette['ingredients']);
                        foreach ($ingredients as $ingredient) {
                            echo "<li>" . htmlspecialchars($ingredient) . "</li>";
                        }
                        ?>
                    </ul>
                </div>
                <div class="methods">
                    <h5>Étapes</h5>
                    <p><?php echo nl2br(htmlspecialchars($recette['methodes'])); ?></p>
                </div>
            </div>
            <div class="footer">
            <p>Publié par <strong><?php echo htmlspecialchars($recette['prenom'] . " " . $recette['nom']); ?></strong>
       le <?php echo htmlspecialchars($recette['date_creation']); ?></p>
                <button class="btn waves-effect blue">S'abonner</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
