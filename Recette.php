<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recette</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .recipe-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .recipe-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }
        .like-btn, .save-btn, .download-btn, .back-btn, .subscribe-btn {
            cursor: pointer;
        }
        .like-btn:hover, .save-btn:hover, .download-btn:hover, .back-btn:hover {
            color: #ff6f61;
        }
        .subscribe-btn {
            background-color: #ff6f61;
        }
        .subscribe-btn:hover {
            background-color: #e05a50;
        }
        h3, h5 {
            color: #ff6f61;
        }
        .btn {
            background-color: #ff6f61;
        }
        .btn:hover {
            background-color: #e05a50;
        }
        .icon-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="container recipe-container">
        <img src="./Image/Chocolat.jpeg" alt="Recette" class="recipe-image z-depth-2">
        
        <div class="row valign-wrapper">
            <div class="col s2">
                <i class="material-icons back-btn" onclick="history.back()">arrow_back</i>
            </div>
            <div class="col s8 center">
                <h3>Chocolat Fondant</h3>
            </div>
            <div class="col s2 right-align icon-group">
                <i class="material-icons like-btn">favorite_border</i>
                <i class="material-icons save-btn">bookmark_border</i>
                <i class="material-icons download-btn">file_download</i>
            </div>
        </div>
        
        <p class="flow-text">Description de la recette...</p>
        <p><strong>Durée :</strong> 30 min</p>
        
        <div class="row">
            <div class="col s6">
                <h5>Ingrédients</h5>
                <ul class="browser-default">
                    <li>Ingrédient 1</li>
                    <li>Ingrédient 2</li>
                    <li>Ingrédient 3</li>
                </ul>
            </div>
            <div class="col s6">
                <h5>Méthode</h5>
                <ol class="browser-default">
                    <li>Étape 1</li>
                    <li>Étape 2</li>
                    <li>Étape 3</li>
                </ol>
            </div>
        </div>
        
        <h5>Note</h5>
        <div>
            <i class="material-icons">star</i>
            <i class="material-icons">star</i>
            <i class="material-icons">star</i>
            <i class="material-icons">star_half</i>
            <i class="material-icons">star_border</i>
        </div>
        
        <h5>Commentaires</h5>
        <div class="input-field">
            <input type="text" id="comment-input" placeholder="Ajouter un commentaire">
            <button class="btn" onclick="addComment()">Publier</button>
        </div>
        <ul id="comment-list" class="collection"></ul>
        <a href="#" onclick="showMoreComments()">Voir plus de commentaires</a>
        
        <h5>Plus de recettes</h5>
        <div class="row">
            <div class="col s4"><img src="https://via.placeholder.com/150" class="responsive-img"></div>
            <div class="col s4"><img src="https://via.placeholder.com/150" class="responsive-img"></div>
            <div class="col s4"><img src="https://via.placeholder.com/150" class="responsive-img"></div>
        </div>
        
        <p class="center">Publié le <strong>01/02/2025</strong> par <strong>Nom de l'auteur</strong></p>
        
        <div class="center">
            <button class="btn subscribe-btn">S'abonner</button>
        </div>
    </div>
    
    <script>
        document.querySelector('.like-btn').addEventListener('click', function() {
            let count = document.getElementById('like-count');
            count.textContent = parseInt(count.textContent) + 1;
            this.textContent = 'favorite';
            this.style.color = 'red';
        });

        function addComment() {
            let input = document.getElementById('comment-input');
            if (input.value.trim() !== '') {
                let li = document.createElement('li');
                li.className = 'collection-item';
                li.textContent = input.value;
                document.getElementById('comment-list').appendChild(li);
                input.value = '';
            }
        }

        function showMoreComments() {
            alert('Afficher plus de commentaires...');
        }
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
