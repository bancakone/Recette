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
    /* Conteneur des cartes */
    .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            padding: 10px;
        }

        /* Style de la carte */
        .card {
            width: 450px; /* Largeur uniforme */
            height: 350px; /* Hauteur fixe */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-bottom: 10px;
        }

        /* Image des recettes */
        .card img {
            width: 100%;
            height: 300px; /* Hauteur fixe */
            object-fit: cover; /* Ajuste bien l'image sans la déformer */
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        /* Titre de la recette */
        .card .card-title {
            font-size: 20px;
            font-weight: bold;
            color:black;
            margin-top: 10px;
            padding: 0 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        /* Lien de la recette */
        .card a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row card-container"> <!-- Conteneur flex pour bien organiser les cartes -->
        <?php
        

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
                                    <p class='card-title'>".htmlspecialchars($recette['titre'])."</p>
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
</div>

</body>
</html>
