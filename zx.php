<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application de Recettes</title>
    <style>
        /* Reset des marges et paddings */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Style global */
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    background-color: #f4f4f4;
}

/* En-tête */
header {
    background-color: #333;
    color: #fff;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header .logo {
    font-size: 1.8rem;
    font-weight: bold;
}

header nav ul {
    list-style-type: none;
    display: flex;
}

header nav ul li {
    margin-left: 1rem;
}

header nav ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 1rem;
}

header nav ul li a:hover {
    text-decoration: underline;
}

/* Bannière d'accueil */
.hero {
    text-align: center;
    background: url('https://via.placeholder.com/1500x800') no-repeat center center/cover;
    padding: 4rem 0;
    color: #fff;
}

.hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.hero p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
}

.hero .search input {
    padding: 0.5rem;
    font-size: 1rem;
    width: 300px;
    border: none;
    border-radius: 5px;
}

.hero .search button {
    padding: 0.5rem 1rem;
    font-size: 1rem;
    background-color: #333;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.hero .search button:hover {
    background-color: #555;
}

/* Section des recettes populaires */
.popular-recipes {
    text-align: center;
    padding: 3rem 0;
    background-color: #fff;
}

.popular-recipes h2 {
    font-size: 2.5rem;
    margin-bottom: 2rem;
}

.recipe-list {
    display: flex;
    justify-content: center;
    gap: 2rem;
}

.recipe {
    text-align: center;
    width: 300px;
}

.recipe img {
    width: 100%;
    border-radius: 8px;
}

.recipe h3 {
    margin-top: 1rem;
    font-size: 1.2rem;
}

/* Footer */
footer {
    text-align: center;
    padding: 1rem;
    background-color: #333;
    color: #fff;
}

    </style>
</head>
<body>

    <!-- En-tête avec le logo et la navigation -->
    <header>
        <div class="logo">Recettes Gourmandes</div>
        <nav>
            <ul>
                <li><a href="#">Accueil</a></li>
                <li><a href="#">Recettes</a></li>
                <li><a href="#">À propos</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#">Se connecter</a></li>
                <li><a href="#">S'inscrire</a></li>
            </ul>
        </nav>
    </header>

    <!-- Bannière d'accueil avec des images de recettes -->
    <section class="hero">
        <h1>Bienvenue sur Recettes Gourmandes</h1>
        <p>Découvrez des recettes savoureuses, faciles à préparer!</p>
        <div class="search">
            <input type="text" placeholder="Rechercher des recettes...">
            <button>Rechercher</button>
        </div>
    </section>

    <!-- Recettes populaires -->
    <section class="popular-recipes">
        <h2>Recettes Populaires</h2>
        <div class="recipe-list">
            <div class="recipe">
                <img src="https://via.placeholder.com/300x200" alt="Recette 1">
                <h3>Soupe de légumes</h3>
            </div>
            <div class="recipe">
                <img src="https://via.placeholder.com/300x200" alt="Recette 2">
                <h3>Pâtes à la sauce tomate</h3>
            </div>
            <div class="recipe">
                <img src="https://via.placeholder.com/300x200" alt="Recette 3">
                <h3>Salade César</h3>
            </div>
        </div>
    </section>

    <!-- Footer avec des liens supplémentaires -->
    <footer>
        <p>&copy; 2025 Recettes Gourmandes | Tous droits réservés</p>
    </footer>

</body>
</html>
