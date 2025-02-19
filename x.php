<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Recette</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            display: flex;
        }
        .sidebar {
            background-color: #607080;
            height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            text-align: center;
            color: white;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px;
            text-align: left;
            display: flex;
            align-items: center;
        }
        .sidebar ul li i {
            margin-right: 10px;
        }
        .container {
            margin-left: 270px;
            padding: 20px;
            flex-grow: 1;
        }
        .recipe-image {
            background: #e0e0e0;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .recipe-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .icons {
            display: flex;
            gap: 10px;
        }
        .ingredients-methods {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        .ingredients-methods::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #000;
            transform: translateX(-50%);
        }
        .counter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .counter {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .counter button {
            border: none;
            background-color: #ddd;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .stars {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .more-recipes img {
            width: 80px;
            height: 80px;
            background: #ddd;
            border-radius: 5px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h5>Nom&Prénom</h5>
        <p>Email</p>
        <ul>
            <li><i class="material-icons">person</i> Profil</li>
            <li><i class="material-icons">favorite</i> Favoris</li>
            <li><i class="material-icons">bookmark</i> Enregistrements</li>
            <li><i class="material-icons">save</i> Brouillons</li>
            <li><i class="material-icons">exit_to_app</i> Déconnexion</li>
            <li><i class="material-icons">notifications</i> Notifications</li>
        </ul>
    </div>
    <div class="container">
        <div class="recipe-content">
            <div class="recipe-image">
                <p>Image</p>
            </div>
            <div class="header">
                <h4>Nom de la recette</h4>
                <div class="icons">
                    <i class="material-icons">favorite_border</i>
                    <i class="material-icons">bookmark_border</i>
                    <i class="material-icons">file_download</i>
                </div>
            </div>
            <div class="counter-container">
                <div class="counter">
                    <button onclick="decrement()">-</button>
                    <span id="counter-value">2</span>
                    <button onclick="increment()">+</button>
                </div>
                <div class="stars">
                    <i class="material-icons">star_border</i>
                    <i class="material-icons">star_border</i>
                    <i class="material-icons">star_border</i>
                    <i class="material-icons">star_border</i>
                    <i class="material-icons">star_border</i>
                </div>
                <span>30 min</span>
            </div>
            <div class="ingredients-methods">
                <div>
                    <h6>Ingrédients</h6>
                </div>
                <div>
                    <h6>Méthodes</h6>
                </div>
            </div>
            <h5>Plus de recettes</h5>
            <div class="more-recipes">
                <img src="#" alt="recette">
                <img src="#" alt="recette">
                <img src="#" alt="recette">
                <img src="#" alt="recette">
            </div>
            <div class="footer">
                <p>Publié le ...... par ....</p>
                <button class="btn">S'abonner</button>
            </div>
        </div>
    </div>
    <script>
        function increment() {
            let counter = document.getElementById('counter-value');
            counter.textContent = parseInt(counter.textContent) + 1;
        }
        function decrement() {
            let counter = document.getElementById('counter-value');
            let value = parseInt(counter.textContent);
            if (value > 1) {
                counter.textContent = value - 1;
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
