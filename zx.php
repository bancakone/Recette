<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Recette</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f4f4f4;
        }

        /* Sidebar */
        #sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            padding-top: 20px;
            position: fixed;
            height: 100%;
        }

        #sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
            transition: background-color 0.3s;
        }

        #sidebar a:hover {
            background-color: #575757;
        }

        /* Contenu principal */
        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .recipe-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .recipe-image img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
        }

        .recipe-description {
            font-size: 1.1rem;
            color: #333;
        }

        .recipe-info {
            display: flex;
            gap: 30px;
            font-size: 1rem;
            color: #333;
        }

        .ingredients, .method {
            flex: 1;
        }

        .comments-section {
            margin-top: 30px;
        }

        .comment {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                width: 100%;
            }
            #sidebar {
                width: 100%;
                position: static;
                display: flex;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <a href="#">Accueil</a>
        <a href="#">Recettes</a>
        <a href="#">Profil</a>
        <a href="#">Mes Publications</a>
    </div>

    <!-- Contenu principal -->
    <div class="content">
        <div class="recipe-details">
            <!-- Image de la recette -->
            <div class="recipe-image">
                <img src="https://via.placeholder.com/800x400" alt="Image de la recette">
            </div>

            <!-- Titre et description -->
            <h1>Nom de la Recette</h1>
            <div class="recipe-description">
                <p>Voici la description détaillée de la recette. Cette recette est facile à préparer et convient à toute la famille. Lisez les étapes suivantes pour en savoir plus !</p>
            </div>

            <!-- Informations sur la recette -->
            <div class="recipe-info">
                <div class="ingredients">
                    <h2>Ingrédients</h2>
                    <ul>
                        <li>Ingrédient 1</li>
                        <li>Ingrédient 2</li>
                        <li>Ingrédient 3</li>
                        <li>Ingrédient 4</li>
                    </ul>
                </div>

                <div class="method">
                    <h2>Méthode</h2>
                    <p>Suivez ces étapes pour préparer la recette : <br>
                    1. Préparation des ingrédients.<br>
                    2. Cuisson de la recette.<br>
                    3. Assemblage et dégustation.</p>
                </div>
            </div>

            <!-- Section commentaires -->
            <div class="comments-section">
                <h2>Commentaires</h2>
                <div class="comment">
                    <p><strong>Utilisateur 1 :</strong> Super recette, mes enfants ont adoré !</p>
                </div>
                <div class="comment">
                    <p><strong>Utilisateur 2 :</strong> Très facile à faire et délicieux, je recommande !</p>
                </div>
            </div>

            <!-- Recettes similaires -->
            <div class="similar-recipes">
                <h2>Recettes Similaires</h2>
                <ul>
                    <li><a href="#">Recette 1</a></li>
                    <li><a href="#">Recette 2</a></li>
                    <li><a href="#">Recette 3</a></li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
