<?php
session_start();
include('config.php'); // Connexion à la base de données

$sql_actualite = "SELECT * FROM recettes WHERE statut = 'publie' ORDER BY date_creation DESC";
$stmt_actualite = $pdo->prepare($sql_actualite);
$stmt_actualite->execute();
$recettes_actualite = $stmt_actualite->fetchAll();

// Vérifier si l'utilisateur est connecté
$user = [];
if (isset($_SESSION['user_id'])) {
    $sql_user = "SELECT * FROM users WHERE id = :id";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt_user->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les Recettes</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="CSS/Accueil1.css">
    <style>
        body {
            display: flex;
            background-color: #f4f4f9;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }
        .sidebar img {
            border-radius: 50%;
            display: block;
            margin: 0 auto 10px;
        }
        .sidebar a {
            color: white !important;
    display: flex;
    align-items: center;
    padding: 10px 15px;
    text-decoration: none;
    transition: 0.3s;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
    
        }
        .sidebar a i{
            margin-right : 20px;
        }
        .sidebar a:hover {
            background-color: #ff5722;
            transform: translateX(5px);
            border-radius: 15px;
        }
        .user-info p{
            margin-bottom: 12px;
        }
        .content {
            margin-left: 270px;
            padding: 5px;
            width: 100%;
        }
        .grid {
            display: flex;
            grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
            gap: 2px;
        }
        .card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: white;
            text-align: center;
            padding-bottom: 1px;
        }
        .card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .card p {
            font-weight: bold;
            color: #007bff;
            margin-top: 1px;
            height: 50px;
        }
        .search-bar {
    display: flex;
    align-items: center;
    background: white;
    padding: 5px 10px;
    border-radius: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-width: 250px; /* Réduit la largeur */
}

.search-bar i {
    font-size: 18px;
    color: gray;
    margin-right: 5px;
}

.search-bar input {
    border: none;
    outline: none;
    flex: 1;
    font-size: 14px;
}
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="user-info">
            <?php if (isset($_SESSION['photo']) && $_SESSION['photo'] != ''): ?>
                <img src="<?php echo $_SESSION['photo']; ?>" alt="Photo de profil" width="80" height="80" />
            <?php else: ?>
                <img src="default-avatar.png" alt="Photo de profil" width="80" height="80" />
            <?php endif; ?>
            <h6><?php echo htmlspecialchars($user['nom'] ?? '') . ' ' . htmlspecialchars($user['prenom'] ?? ''); ?></h6>
            <p><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
        </div>
        <a href="Accueil.php"><i class="material-icons">home</i> Accueil</a>
        <a href="Profil.php"><i class="material-icons">account_circle</i> Profil</a>
        <a href="Favoris.php"><i class="material-icons">favorite</i> Favoris</a>
        <a href="Enregistrement.php"><i class="material-icons">bookmark</i> Enregistrements</a>
        <a href="Publication.php"><i class="material-icons">post_add</i> Publications</a>
        <a href="Deconnexion.php"><i class="material-icons">exit_to_app</i> Déconnexion</a>
        <a href="Historique.php"><i class="material-icons">history</i> Historique</a>
        <a href="Notification.php"><i class="material-icons">notifications</i> Notifications</a>
    </div>
    <div class="content">
        <h5>Toutes les Recettes</h5>
        <div class="left-items">
              <div class="search-bar">
                  <i class="material-icons">search</i>
                  <input type="text" id="search" name="query" placeholder="Rechercher..." required>
              </div>
              <div id="resultats"></div>
        <div class="grid">
            <?php foreach ($recettes_actualite as $recette): ?>
                <div class="card">
                    <a href="Recette.php?id=<?php echo $recette['id']; ?>">
                        <img src="<?php echo htmlspecialchars($recette['photo']); ?>" alt="Image de la recette">
                        <p><?php echo htmlspecialchars($recette['titre']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
    let searchInput = document.getElementById("search");
    let resultatsDiv = document.getElementById("resultats");
    let recettesDiv = document.querySelector(".grid");

    searchInput.addEventListener("keyup", function() {
        let query = this.value.trim();

        if (query.length > 2) { // Activer la recherche après 3 caractères
            fetch("Recherche.php?q=" + query)
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "") {
                        resultatsDiv.innerHTML = "<p>Aucune recette trouvée.</p>";
                        recettesDiv.style.display = "none"; // Cacher toutes les recettes
                    } else {
                        resultatsDiv.innerHTML = data;
                        recettesDiv.style.display = "none"; // Cacher la liste originale
                    }
                });
        } else {
            resultatsDiv.innerHTML = ""; // Effacer les résultats
            recettesDiv.style.display = "flex"; // Réafficher la liste originale
        }
    });
});

    </script>
</body>
</html>
