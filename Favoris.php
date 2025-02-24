<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour accéder à cette page.");
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=recette", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_id = intval($_SESSION['user_id']); // Sécurisation de l'ID utilisateur

    // Récupérer les favoris de l'utilisateur
    $stmt = $conn->prepare("
        SELECT r.* 
        FROM recettes r
        JOIN favoris f ON r.id = f.recette_id
        WHERE f.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Une erreur est survenue. Veuillez réessayer plus tard.");
}

// Suppression d'un favori si une requête POST est envoyée
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['recette_id']) && isset($_POST['action']) && $_POST['action'] === 'remove') {
    try {
        $recette_id = intval($_POST['recette_id']);
        $delete_stmt = $conn->prepare("DELETE FROM favoris WHERE user_id = ? AND recette_id = ?");
        $delete_stmt->execute([$user_id, $recette_id]);
        echo "Recette retirée des favoris.";
        exit;
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression du favori : " . $e->getMessage());
        echo "Erreur lors de la suppression.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <style>
        .card:hover {
            transform: scale(1.05);
            transition: 0.3s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <h4>Mes Recettes Favorites</h4>
        <div class="row">
            <?php if (empty($favoris)): ?>
                <p>Aucune recette favorite.</p>
            <?php else: ?>
                <?php foreach ($favoris as $recette): ?>
                    <div class="col s12 m6 l4">
                        <div class="card">
                            <div class="card-image">
                                <img src="images/<?php echo htmlspecialchars($recette['photo']); ?>" alt="Image de la recette">
                            </div>
                            <div class="card-content">
                                <span class="card-title"><?php echo htmlspecialchars($recette['titre']); ?></span>
                                <p><?php echo nl2br(htmlspecialchars($recette['description'])); ?></p>
                            </div>
                            <div class="card-action">
                                <a href="Recette.php?id=<?php echo $recette['id']; ?>">Voir la recette</a>
                                <button class="btn red remove-fav" data-id="<?php echo $recette['id']; ?>">Retirer</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
    document.querySelectorAll('.remove-fav').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm("Voulez-vous vraiment retirer cette recette de vos favoris ?")) {
                let recetteId = this.getAttribute('data-id');

                fetch('Favoris.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `recette_id=${recetteId}&action=remove`
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    location.reload();
                })
                .catch(error => console.error('Erreur:', error));
            }
        });
    });
    </script>
</body>
</html>
