
 
 
 <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Recettes</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="CSS/Accueil.css">
</head>
<body>
    <div class="sidebar">
        <div class="profile">
            <input type="file" id="upload-profile" hidden>
            <label for="upload-profile">
                <img src="https://via.placeholder.com/80" alt="Profile">
            </label>
            <p><strong>Nom&Prénom</strong><br>Email</p>
        </div>
        <ul>
            <li><a href="#" class="black-text"><i class="material-icons">person</i> Profil</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">favorite</i> Favoris</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">bookmark</i> Enregistrements</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">archive</i> Brouillons</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">post_add</i> Publication</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
            <li><a href="#" class="black-text"><i class="material-icons">notifications</i> Notifications</a></li>
        </ul>
    </div>
    <div class="content">
       
        <div class="header-icons">
    <div class="left-items">
        <div class="search-bar">
            <i class="material-icons">search</i>
        </div>
    </div>
    <div class="right-items">
        <span>Catégories</span>
        <i class="material-icons">menu</i>
    </div>
    </div>


        <h5>Recherches Populaires</h5>
        <div class="grid">
            <div class="card"><img src="Image/Chocolat.jpeg"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
        </div>
        <div class="view-more">
             <i class="material-icons">arrow_forward</i>
        </div>

        <h5>Recherches Récentes</h5>
        <div class="grid">
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
        </div>
        <div class="view-more">
             <i class="material-icons">arrow_forward</i>
        </div>

        <h5>Publications</h5>
        <div class="grid">
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
            <div class="card"><img src="https://via.placeholder.com/150"><p>Titre</p></div>
        </div>

        <div class="view-more">
             <i class="material-icons">arrow_forward</i>
        </div>
    </div>
    <a href="<?php echo isset($_SESSION['user_id']) ? 'Recette.php' : 'Connexion.php'; ?>"  class="btn-floating btn-large red floating-btn">
         <i class="material-icons">add</i>
    </a>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html> 

