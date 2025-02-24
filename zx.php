<?php
session_start(); // Démarrer la session

// Connexion à la base de données avec try...catch
try {
    $conn = new PDO("mysql:host=localhost;dbname=recette;charset=utf8", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si un ID est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Aucune recette spécifiée.");
}

$id = intval($_GET['id']); // Sécuriser l'ID

  // Récupérer les infos de la recette avec les informations de l'auteur
  $stmt = $conn->prepare("
  SELECT r.*, u.nom, u.prenom, DATE_FORMAT(r.date_creation, '%d/%m/%Y à %H:%i') AS date_creation
  FROM recettes r
  JOIN users u ON r.user_id = u.id
  WHERE r.id = ?
");
  $stmt->execute([$id]);
  $recette = $stmt->fetch(PDO::FETCH_ASSOC);;


if (!$recette) {
    die("Recette introuvable.");
}

// Récupérer les informations de l'utilisateur connecté si disponible
$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt_user = $conn->prepare("SELECT nom, prenom, email, photo FROM users WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $user = $stmt_user->fetch();
}

// Si l'utilisateur est connecté, ajouter la recette à l'historique
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Vérifier si cette recette est déjà dans l'historique
    $sql_check = "SELECT * FROM historique WHERE user_id = :user_id AND recette_id = :recette_id";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':recette_id', $id, PDO::PARAM_INT);
    $stmt_check->execute();

    // Si la recette n'est pas dans l'historique, on l'ajoute
    if ($stmt_check->rowCount() == 0) {
        $sql_insert = "INSERT INTO historique (user_id, recette_id) VALUES (:user_id, :recette_id)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':recette_id', $id, PDO::PARAM_INT);
        $stmt_insert->execute();
    }
}

// Récupérer les recettes similaires
$similarStmt = $conn->prepare("SELECT id, titre, photo FROM recettes WHERE id != ? ORDER BY RAND() LIMIT 4");
$similarStmt->execute([$id]);
$similarRecettes = $similarStmt->fetchAll();

// Récupérer les commentaires
$commentStmt = $conn->prepare("
    SELECT c.*, u.nom, u.prenom 
    FROM commentaires c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.recette_id = ? 
    ORDER BY c.date_creation DESC
");
$commentStmt->execute([$id]);
$comments = $commentStmt->fetchAll();

// Ajouter un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commentaire'])) {
    if (!isset($_SESSION['user_id'])) {
        die("Vous devez être connecté pour commenter.");
    }

    $commentaire = trim($_POST['commentaire']);

    if (!empty($commentaire)) {
        $insertStmt = $conn->prepare("
            INSERT INTO commentaires (recette_id, user_id, commentaire, date_creation) 
            VALUES (?, ?, ?, NOW())
        ");
        $insertStmt->execute([$id, $_SESSION['user_id'], htmlspecialchars($commentaire)]);

        header("Location: recette.php?id=" . $id); // Rediriger après ajout du commentaire
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Recette</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Recette.css">
    <style></style>
</head>
<body>
    <div class="sidebar">
        <!-- Afficher la photo de profil -->
        <?php if (isset($user['photo']) && $user['photo'] != ''): ?>
            <img src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" alt="Photo de profil" width="80" height="80" />
        <?php else: ?>
            <img src="default-avatar.png" alt="Photo de profil" width="80" height="80" />
        <?php endif; ?>
        <h6><?php echo htmlspecialchars($user['nom']) . ' ' . htmlspecialchars($user['prenom']); ?></h6>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <ul>
            <li><a href="Accueil.php"><i class="material-icons">home</i> Accueil</a></li>
            <li><a href="Profil.php"><i class="material-icons">person</i> Profil</a></li>
            <li><a href="Favoris.php"><i class="material-icons">favorite</i> Favoris</a></li>
            <li><a href="Enregistrements.php"><i class="material-icons">bookmark</i> Enregistrements</a></li>
            <li><a href="Brouillons.php"><i class="material-icons">save</i> Brouillons</a></li>
        </ul>
    </div>

    <div class="container">
        <div class="recipe-image">
            <img src="images/<?php echo htmlspecialchars($recette['photo']); ?>" alt="Image de la recette">
        </div>
        <div class="recipe-content">
            <div class="header">
                <h4><?php echo htmlspecialchars($recette['titre']); ?></h4>
                <div class="icons">
                    <i class="material-icons" id="favorite-icon" data-recipe-id="<?php echo $recette['id']; ?>" onclick="toggleFavoris(this)">favorite_border</i>
                    <i class="material-icons" id="bookmark-icon" data-recipe-id="<?php echo $recette['id']; ?>" onclick="toggleEnregistrement(this)">bookmark_border</i>
                    <i class="material-icons" id="download-icon" data-recipe-id="<?php echo $recette['id']; ?>">download</i>
                </div>
            </div>
            <div class="counter-container">
                <span>Portion:</span>
                <div class="counter">
                    <button>-</button>
                    <span>1</span>
                    <button>+</button>
                </div>
            </div>
            <div class="ingredients-methods">
                <div class="ingredients">
                    <h5>Ingrédients</h5>
                    <ul>
                        <?php
                        $ingredients = explode("\n", $recette['ingredients']);
                        foreach ($ingredients as $ingredient) {
                            echo "<li>" . htmlspecialchars($ingredient) . "</li>";
                        }
                        ?>
                    </ul>
                </div>
                <div class="methods">
                    <h5>Étapes</h5>
                    <p><?php echo nl2br(htmlspecialchars($recette['methodes'])); ?></p>
                </div>
            </div>

            <!-- SECTION COMMENTAIRES -->
            <div class="comments-section">
                <h5>Commentaires</h5>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" class="comment-form">
                        <textarea name="commentaire" placeholder="Écrivez un commentaire..." required></textarea>
                        <button type="submit" class="btn waves-effect blue">Commenter</button>
                    </form>
                <?php else: ?>
                    <p>Vous devez être connecté pour laisser un commentaire.</p>
                <?php endif; ?>

                <!-- Liste des commentaires -->
                <ul class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <li class="comment-item">
                            <p><?php echo nl2br(htmlspecialchars($comment['commentaire'])); ?></p>
                            <small>Posté le <?php echo date("d/m/Y à H:i", strtotime($comment['date_creation'])); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- SECTION PLUS DE RECETTES -->
            <div class="more-recipes">
                <h5>Plus de recettes</h5>
                <div class="recipe-list">
                    <?php foreach ($similarRecettes as $recette) : ?>
                        <div class="recipe-card">
                            <a href="recette.php?id=<?php echo $recette['id']; ?>">
                                <img src="<?php echo htmlspecialchars($recette['photo']); ?>" alt="<?php echo htmlspecialchars($recette['titre']); ?>">
                                <p><?php echo htmlspecialchars($recette['titre']); ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="footer">
            <p>Publié par <strong><?php echo htmlspecialchars($user['prenom'] . " " . $user['nom']); ?></strong>
       le <?php echo htmlspecialchars($recette['date_creation']); ?></p>
                <button class="btn waves-effect blue">S'abonner</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
