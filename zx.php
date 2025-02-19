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
    <link rel="stylesheet" href="CSS/Recette.css">
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
                        // Vérifier si la recette a des ingrédients valides
                        $ingredients = isset($recette['ingredients']) && !empty($recette['ingredients']) 
                            ? explode("\n", trim($recette['ingredients'])) 
                            : [];

                        foreach ($ingredients as $ingredient) {
                            echo "<li>" . htmlspecialchars(trim($ingredient)) . "</li>";
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
