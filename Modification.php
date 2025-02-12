<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Recette</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }
        .title {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .input-field input, .input-field textarea {
            font-size: 1rem;
            border-bottom: 2px solid #ff7675;
        }
        .btn-small {
            width: 40%;
            margin: 10px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 20px;
        }
        .flex-row {
            display: flex;
            gap: 15px;
        }
        .btn-floating {
            background-color: #ff7675;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="Accueil.php" class="material-icons">close</a>
            <span class="title">Ajouter Recette</span>
        </div>
        
        <div class="row center">
            <a class="btn-floating btn-large red">
                <i class="large material-icons">add</i>
            </a>
            <p>Ajouter une photo</p>
        </div>
        
        <div class="row flex-row">
            <div class="col s6">
                <div class="input-field">
                    <input type="text" id="titre" placeholder="Titre">
                </div>
            </div>
            <div class="col s6">
                <div class="input-field">
                    <textarea id="description" class="materialize-textarea" placeholder="Description"></textarea>
                </div>
            </div>
        </div>
        
        <div class="row flex-row">
            <div class="col s6">
                <div class="input-field">
                    <input type="number" id="portions" min="1" placeholder="Portions">
                </div>
            </div>
            <div class="col s6">
                <div class="input-field">
                    <input type="text" id="duree" placeholder="Durée (ex: 30 min)">
                </div>
            </div>
        </div>
        
        <div class="row flex-row">
            <div class="col s6">
                <div class="input-field" id="ingredients-container">
                    <input type="text" name="ingredients[]" placeholder="Ingrédients">
                    <a class="btn-floating btn-small" onclick="ajouterIngredient()"><i class="material-icons">add</i></a>
                </div>
            </div>
            <div class="col s6">
                <div class="input-field" id="methodes-container">
                    <input type="text" name="methodes[]" placeholder="Méthodes">
                    <a class="btn-floating btn-small" onclick="ajouterMethode()"><i class="material-icons">add</i></a>
                </div>
            </div>
        </div>
        
        <div class="row center">
            <a class="btn btn-small grey">Brouillons</a>
            <a class="btn btn-small red">Publier</a>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        function ajouterIngredient() {
            let container = document.getElementById("ingredients-container");
            let input = document.createElement("input");
            input.type = "text";
            input.name = "ingredients[]";
            input.placeholder = "Ingrédients";
            container.appendChild(input);
        }
        
        function ajouterMethode() {
            let container = document.getElementById("methodes-container");
            let input = document.createElement("input");
            input.type = "text";
            input.name = "methodes[]";
            input.placeholder = "Méthodes";
            container.appendChild(input);
        }
    </script>
</body>
</html>