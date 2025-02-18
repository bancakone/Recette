<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifier si l'ID de la recette est passé dans l'URL
if (isset($_GET['id'])) {
    $id_recette = $_GET['id'];

    // Requête pour récupérer les détails de la recette
    $sql = "SELECT * FROM recettes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_recette, PDO::PARAM_INT);
    $stmt->execute();
    $recette = $stmt->fetch();

    // Vérifier si la recette existe
    if (!$recette) {
        echo "Recette non trouvée.";
        exit;
    }

    // Enregistrement dans l'historique
    if (!isset($_SESSION['unique_id'])) {
        $_SESSION['unique_id'] = uniqid('user_', true); // Créer un ID unique si non existant
    }
    $user_id = $_SESSION['unique_id']; // Utiliser l'ID unique de l'utilisateur

    // Vérifier si la recette est déjà dans l'historique
    $verif = $pdo->prepare("SELECT COUNT(*) FROM historique WHERE user_id = :user_id AND recette_id = :recette_id");
    $verif->bindParam(':user_id', $user_id, PDO::PARAM_STR); 
    $verif->bindParam(':recette_id', $id_recette, PDO::PARAM_INT);
    $verif->execute();
    $deja_vu = $verif->fetchColumn();

    // Si la recette n'a pas été vue, l'enregistrer dans l'historique
    if ($deja_vu == 0) {
        $stmt_historique = $pdo->prepare("INSERT INTO historique (user_id, recette_id, titre) VALUES (:user_id, :recette_id, :titre)");
        $stmt_historique->bindParam(':user_id', $user_id, PDO::PARAM_STR); 
        $stmt_historique->bindParam(':recette_id', $id_recette, PDO::PARAM_INT);
        $stmt_historique->bindParam(':titre', $recette['titre'], PDO::PARAM_STR); 
        $stmt_historique->execute();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Recette</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            background-image: url('https://www.toptal.com/designers/subtlepatterns/patterns/wood_light.png');
            background-size: cover;
            display: flex;
            height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        #sidebar {
            width: 250px;
            background-color: #2C3E50;
            color: white;
            padding-top: 20px;
            height: 100%;
            position: fixed;
            top: 0;
            transition: width 0.3s ease;
        }

        #sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
            transition: background-color 0.3s;
            font-size: 18px;
        }

        #sidebar a:hover {
            background-color: #34495E;
        }

        /* Contenu principal */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }

        .container {
            max-width: 100%;
            margin: auto;
        }

        .recipe-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .recipe-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-weight: 600;
        }

        .recipe-header h4 {
            margin: 0;
            font-size: 2rem;
            color: #2C3E50;
        }

        .actions i {
            margin: 0 8px;
            cursor: pointer;
            color: #3498db;
            transition: color 0.3s;
        }

        .actions i:hover {
            color: #2980b9;
        }

        /* Aligner Portion, Note et Durée */
        .recipe-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            gap: 20px;
        }

        .portion, .rating, .duration {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .portion button, .rating i, .duration input {
            cursor: pointer;
        }

        .portion button {
            padding: 8px 12px;
            font-size: 1.2rem;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .portion button:hover {
            background-color: #2980b9;
        }

        .rating i {
            cursor: pointer;
            color: #f39c12;
            font-size: 24px;
        }

        .rating i:hover {
            color: #e67e22;
        }

        .duration input {
            width: 60px;
            padding: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .recipe-details {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 20px;
        }

        .ingredients, .method {
            width: 48%;
        }

        .ingredients ul {
            list-style-type: none;
        }

        .ingredients li {
            background-color: #ecf0f1;
            margin: 8px 0;
            padding: 10px;
            border-radius: 8px;
            font-size: 1rem;
        }

        .method p {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            font-size: 1rem;
        }

        .comment-section {
            margin-top: 30px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            color: #7f8c8d;
        }

        .btn {
            background-color: #3498db;
            color: white;
            border-radius: 50px;
            padding: 12px 25px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        /* Mobile view */
        @media (max-width: 768px) {
            #sidebar {
                width: 100%;
                height: auto;
                position: static;
            }
            .main-content {
                margin-left: 0;
            }
            .recipe-details {
                flex-direction: column;
            }
            .ingredients, .method {
                width: 100%;
                margin-bottom: 20px;
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
    <div class="main-content">
        <div class="container">
            <img src="<?php echo $recette['photo']; ?>" alt="Image de la recette" class="recipe-image">
            
            <div class="recipe-header">
                <h4><?php echo $recette['titre']; ?></h4>
                <div class="actions">
                    <i class="material-icons">favorite_border</i>
                    <i class="material-icons">share</i>
                </div>
            </div>

            <div class="recipe-controls">
                <div class="portion">
                    <button>Portions: 2</button>
                </div>
                <div class="rating">
                    <i class="material-icons">star</i>
                    <i class="material-icons">star</i>
                    <i class="material-icons">star</i>
                    <i class="material-icons">star_half</i>
                    <i class="material-icons">star_border</i>
                </div>
                <div class="duration">
                    <label>Temps: <input type="text" value="45 min" disabled></label>
                </div>
            </div>

            <div class="recipe-details">
                <div class="ingredients">
                    <h5>Ingrédients</h5>
                    <ul>
                        <?php 
                        // Affichage des ingrédients
                        $ingredients = json_decode($recette['ingredients'], true);
                        foreach ($ingredients as $ingredient) {
                            echo "<li>$ingredient</li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="method">
                    <h5>Méthode</h5>
                    <p><?php echo nl2br($recette['description']); ?></p>
                </div>
            </div>

            <div class="comment-section">
                <h5>Commentaires</h5>
                <form>
                    <textarea placeholder="Ajouter un commentaire..." rows="4"></textarea><br>
                    <button class="btn">Envoyer</button>
                </form>
            </div>

            <div class="footer">
                <p>&copy; 2025 Recettes App</p>
                <p><a href="mailto:support@recettesapp.com">Support</a></p>
            </div>
        </div>
    </div>
    
</body>
</html>
