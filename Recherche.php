<?php
require 'config.php'; // Connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Recettes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            background: white;
            padding-bottom: 15px;
        }

        .card img {
            width: 100%;
            height: auto;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #007bff; /* Bleu Bootstrap */
            margin-top: 10px;
        }

        .card a {
            display: block;
            text-decoration: none;
            font-weight: bold;
            color: #007bff;
            margin-top: 5px;
        }

        .container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
<?php
require 'config.php'; // Connexion à la base de données

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = htmlspecialchars($_GET['q']);

    // Requête SQL pour filtrer les recettes publiées par titre ou ingrédients
    $stmt = $pdo->prepare("SELECT * FROM recettes WHERE statut = 'publie' AND (titre LIKE ? OR ingredients LIKE ?) ORDER BY date_creation DESC LIMIT 5");
    $stmt->execute(["%$q%", "%$q%"]);
    $recettes = $stmt->fetchAll();

    if ($recettes) {
        foreach ($recettes as $recette) {
            echo "
                <div class='card'>
                    <a href='Recette.php?id={$recette['id']}'>
                        <img src='".htmlspecialchars($recette['photo'])."' alt='".htmlspecialchars($recette['titre'])."'>
                        <p>".htmlspecialchars($recette['titre'])."</p>
                    </a>
                </div>
            ";
        }
    } else {
        echo "<p>Aucune recette trouvée.</p>";
    }
}
?>

</div>

</body>
</html>
