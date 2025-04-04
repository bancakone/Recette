<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Démarrage - Recette de Cuisine</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Appliquer un motif d'arrière-plan sans répétition */
        body {
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center; /* Centre le div horizontalement */
    align-items: center; /* Centre le div verticalement */
    height: 100vh;
    background-color:rgb(248, 248, 248); /* Un fond neutre */
}

/* Style pour l'élément container */
.container {
    text-align: center;
    padding: 50px;

    border-radius: 10px;
    width: 60%; /* Réduction de la largeur */
    max-width: 600px; /* Largeur maximale pour éviter que ce soit trop large sur grand écran */
    margin: 50px auto; /* Centrer horizontalement */
}


/* Style du titre */
h1 {
    font-size: 3em;
    color: #ff6347; /* Couleur rouge tomate */
    margin-bottom: 20px;
    font-weight: bold;
    animation: fadeIn 2s ease-out; /* Animation pour le titre */
}

/* Style pour le sous-titre */
p.subtitle {
    font-size: 1.5em;
    color: #333;
    margin-top: 10px;
    font-style: italic;
    animation: fadeIn 3s ease-out; /* Animation pour le sous-titre */
}

/* Animation pour l'apparition du texte */
@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
.background-container {
    width: 51%; /* Réduire la largeur de l’image */
    height: 80vh; /* Hauteur ajustable */
    background-image: url('https://media.istockphoto.com/id/506791876/fr/vectoriel/livre-de-recettes-%C3%A9quilibr%C3%A9es.jpg?s=612x612&w=0&k=20&c=81IWWJtOjqgBEOg7QRiueVndbYP5IsL79z4Vj8hFmao=');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    border-radius: 10px; /* Coins arrondis pour un meilleur design */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Ajoute une ombre pour du relief */
    margin-right: 5px;
}

        /* Style du bouton démarrer */
        .btn-start {
            display: inline-block;
            padding: 15px 30px;
            font-size: 1.2em;
            background-color: #ff6347; /* Rouge tomate */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s, transform 0.3s;
        }

        /* Effet de survol pour le bouton */
        .btn-start:hover {
            background-color: #e55347; /* Couleur plus sombre au survol */
            transform: scale(1.1); /* Effet d'agrandissement au survol */
        }



    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur Recettes Gourmandes</h1>
        <p>Explorez des recettes simples et délicieuses pour toutes les occasions.</p>
        <p class="subtitle">Faites plaisir à vos papilles !</p>
        <!-- Bouton Démarrer qui redirige vers la page d'accueil -->
        <a href="Accueil.php" class="btn-start">Démarrer</a>
    </div>
    <div class="background-container"></div>

</body>
</html>
