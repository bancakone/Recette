<?php
session_start();
include('config.php');

if (!isset($_GET['id'])) {
    die("Recette introuvable.");
}

$recette_id = $_GET['id'];
$sql = "SELECT r.*, u.nom AS auteur FROM recettes r JOIN users u ON r.user_id = u.id WHERE r.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $recette_id]);
$recette = $stmt->fetch();

if (!$recette) {
    die("Recette introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recette['titre']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { display: flex; background: #f8f9fa; font-family: 'Roboto', sans-serif; }
        .sidebar { width: 280px; background: #2c3e50; padding: 20px; color: white; height: 100vh; position: fixed; }
        .content { margin-left: 300px; flex: 1; padding: 20px; }
        .recette-header { display: flex; align-items: flex-start; gap: 20px; }
        .recette-img { width: 50%; border-radius: 10px; }
        .recette-info { width: 50%; }
        .recette-title { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .recette-actions { display: flex; gap: 15px; }
        .stars { display: flex; gap: 5px; cursor: pointer; margin-bottom: 20px; }
        .stars i { color: #ccc; }
        .stars i.active { color: gold; }
        .comment-section { margin-top: 20px; }
        .published-container { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .subscribe-btn { padding: 10px 20px; background: #ff5722; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .details-container { display: flex; justify-content: space-between; margin-top: 20px; }
        .details-container div { width: 48%; }
        .quantity-control { display: flex; align-items: center; gap: 10px; margin-top: 20px; }
        .quantity-btn { padding: 5px 10px; background: #ff5722; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .quantity { font-size: 18px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h6>Menu</h6>
        <ul>
            <li><a href="dashboard.php"><i class="material-icons">home</i> Accueil</a></li>
            <li><a href="profil.php"><i class="material-icons">person</i> Profil</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="recette-header">
            <img src="<?= htmlspecialchars($recette['photo']) ?>" class="recette-img" alt="Recette">
            <div class="recette-info">
                <div class="recette-title">
                    <h4><?= htmlspecialchars($recette['titre']) ?></h4>
                    <div class="recette-actions">
                        <i class="material-icons">favorite_border</i>
                        <i class="material-icons">bookmark_border</i>
                        <i class="material-icons">cloud_download</i>
                    </div>
                </div>
                <div class="stars">
                    <i class="material-icons" onclick="rate(1)">star</i>
                    <i class="material-icons" onclick="rate(2)">star</i>
                    <i class="material-icons" onclick="rate(3)">star</i>
                    <i class="material-icons" onclick="rate(4)">star</i>
                    <i class="material-icons" onclick="rate(5)">star</i>
                </div>
                <p><?= nl2br(htmlspecialchars($recette['description'])) ?></p>
                <div class="comment-section">
            <h5>Commentaires</h5>
            <textarea placeholder="Laisser un commentaire..." class="materialize-textarea"></textarea>
        </div>
            </div>
        </div>
        
        <div class="details-container">
            <div>
                
                <h5>Ingrédients</h5>
                <p><?= nl2br(htmlspecialchars($recette['ingredients'])) ?></p>
            </div>
            <div>
                <h5>Méthodes</h5>
                <p><?= nl2br(htmlspecialchars($recette['methodes'])) ?></p>
            </div>
        </div>
    
       

        <div class="published-container">
            <p>Publié par <?= htmlspecialchars($recette['auteur']) ?> le <?= date('d/m/Y H:i', strtotime($recette['date_creation'])) ?></p>
            <button class="subscribe-btn">S'abonner</button>
        </div>
    </div>
    <script>
        function rate(stars) {
            let starElements = document.querySelectorAll('.stars i');
            starElements.forEach((star, index) => {
                star.classList.toggle('active', index < stars);
            });
        }
        
        function increaseQuantity() {
            let quantityElement = document.getElementById("quantity");
            let quantity = parseInt(quantityElement.innerText);
            quantityElement.innerText = quantity + 1;
        }
        
        function decreaseQuantity() {
            let quantityElement = document.getElementById("quantity");
            let quantity = parseInt(quantityElement.innerText);
            if (quantity > 1) {
                quantityElement.innerText = quantity - 1;
            }
        }
    </script>
</body>
</html>
