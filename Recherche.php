<?php
require 'config.php'; // Connexion à la base de données

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = htmlspecialchars($_GET['q']);

    $stmt = $pdo->prepare("SELECT * FROM recettes WHERE titre LIKE ? OR ingredients LIKE ? LIMIT 5");
    $stmt->execute(["%$q%", "%$q%"]);
    $recettes = $stmt->fetchAll();

    if ($recettes) {
        foreach ($recettes as $recette) {
            echo "
                <div class='card horizontal'>
                    <div class='card-image'>
                        <img src='uploads/{$recette['photo']}' alt='{$recette['titre']}'>
                    </div>
                    <div class='card-stacked'>
                        <div class='card-content'>
                            <a href='recette.php?id={$recette['id']}'>{$recette['titre']}</a>
                        </div>
                    </div>
                </div>
            ";
        }
    } else {
        echo "<p>Aucune recette trouvée.</p>";
    }
}

?>
