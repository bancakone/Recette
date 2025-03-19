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
       .card-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: stretch;
}

.card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    text-align: center;
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
    <div class="row card-container"> <!-- Conteneur flex pour bien organiser les cartes -->
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
                        <div class='col s12 m6 l3'> <!-- Adaptatif selon la taille de l'écran -->
                            <div class='card'>
                                <a href='Recette.php?id={$recette['id']}'>
                                    <img src='".htmlspecialchars($recette['photo'])."' alt='".htmlspecialchars($recette['titre'])."'>
                                    <p class='card-title'>".htmlspecialchars($recette['titre'])."</p>
                                </a>
                            </div>
                        </div>
                    ";
                }
            } else {
                echo "<p>Aucune recette trouvée.</p>";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
