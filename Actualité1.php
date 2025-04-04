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
// Notifications
$sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND lu = FALSE";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$notif_count = $stmt->fetchColumn();
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
       
        .content {
            margin-left: 120px;
            width: calc(100% - 270px);
            padding: 30px;
            background-color: var(--background-color);
            min-height: 100vh;
        }
        .grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
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
            height: 300px;
            object-fit: cover;
        }
        .card p {
            font-weight: bold;
            color: black;
            margin-top: 5px;
            height: 33px;
            font-size: 18px;
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
h5 {
            font-size: 2.2rem;
            font-weight: bold;
            color: var(--text-color);
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 30px;
            font-family: 'Montserrat', sans-serif;
            position: relative;
            padding-bottom: 15px;
        }

        h5::after {
            content: "";
            display: block;
            width: 60px;
            height: 4px;
            background-color: var(--primary-color);
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
        }
        .badge {
            background-color: var(--accent-color);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }
        
    </style>
</head>
<body>


    <div class="content">
        <h5>Toutes les Recettes</h5>
        <div class="search-bar">
            <i class="material-icons">search</i>
            <input type="text" id="search" name="query" placeholder="Rechercher..." required>
        </div>
        <div id="resultats" class="grid" style="display: none;"></div>
        <div id="recettes" class="grid">
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
    <div style="text-align: center; margin: 20px 0;">
    <a href="javascript:history.back()" class="btn grey darken-2 waves-effect waves-light">
        <i class="material-icons left">arrow_back</i> Retour
    </a>
</div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let searchInput = document.getElementById("search");
            let resultatsDiv = document.getElementById("resultats");
            let recettesDiv = document.getElementById("recettes");
            
            searchInput.addEventListener("keyup", function() {
                let query = this.value.trim();
                
                if (query.length > 2) {
                    fetch("Recherche.php?q=" + encodeURIComponent(query))
                        .then(response => response.text())
                        .then(data => {
                            if (data.trim() === "") {
                                resultatsDiv.innerHTML = "<p>Aucune recette trouvée.</p>";
                                resultatsDiv.style.display = "block";
                                recettesDiv.style.display = "none";
                            } else {
                                resultatsDiv.innerHTML = data;
                                resultatsDiv.style.display = "grid";
                                recettesDiv.style.display = "none";
                            }
                        })
                        .catch(error => console.error("Erreur lors de la recherche :", error));
                } else {
                    resultatsDiv.innerHTML = "";
                    resultatsDiv.style.display = "none";
                    recettesDiv.style.display = "grid";
                }
            });
        });
    </script>
</body>
</html>
