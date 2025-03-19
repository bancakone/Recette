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

// Définir la catégorie actuelle de la recette
$categorie_id_actuelle = isset($recette['categorie_id']) ? $recette['categorie_id'] : null;

// Vérifier si la recette est déjà en favoris
$favoriSql = "SELECT * FROM favoris WHERE user_id = :user_id AND recette_id = :recette_id";
$favoriStmt = $pdo->prepare($favoriSql);
$favoriStmt->execute(['user_id' => $_SESSION['user_id'] ?? 0, 'recette_id' => $recette_id]);
$estFavori = $favoriStmt->rowCount() > 0;

// Vérifier si la recette est déjà enregistrée
$enregistrementSql = "SELECT * FROM enregistrements WHERE user_id = :user_id AND recette_id = :recette_id";
$enregistrementStmt = $pdo->prepare($enregistrementSql);
$enregistrementStmt->execute(['user_id' => $_SESSION['user_id'] ?? 0, 'recette_id' => $recette_id]);
$estEnregistre = $enregistrementStmt->rowCount() > 0;

// Récupérer la note moyenne
$sql = "SELECT AVG(note) AS moyenne FROM notes WHERE recette_id = :recette_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['recette_id' => $recette_id]);
$moyenne = round($stmt->fetch()['moyenne'], 1);

// Récupérer la note de l'utilisateur
$user_note = 0;
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT note FROM notes WHERE user_id = :user_id AND recette_id = :recette_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'recette_id' => $recette_id]);
    $user_note = $stmt->fetch()['note'] ?? 0;
}

// Vérifier si l'utilisateur a déjà liké la recette
$sql = "SELECT COUNT(*) FROM likes WHERE user_id = :user_id AND recette_id = :recette_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['user_id'], 'recette_id' => $recette_id]);
$liked = $stmt->fetchColumn() > 0;

// Compter le nombre total de likes
$sql = "SELECT COUNT(*) FROM likes WHERE recette_id = :recette_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['recette_id' => $recette_id]);
$total_likes = $stmt->fetchColumn();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Vérifier si cette recette est déjà enregistrée dans l'historique aujourd'hui
    $sql = "SELECT * FROM historique WHERE user_id = :user_id AND recette_id = :recette_id AND DATE(date_consultation) = CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);

    if ($stmt->rowCount() == 0) {
        // Ajouter à l'historique si ce n'est pas déjà fait aujourd'hui
        $sql = "INSERT INTO historique (user_id, recette_id) VALUES (:user_id, :recette_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id, 'recette_id' => $recette_id]);
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <title><?= htmlspecialchars($recette['titre']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    color: #333;
}
        .sidebar { width: 280px; background: #2c3e50; padding: 20px; color: white; height: 100vh; position: fixed; text-align: center; }
        .sidebar img { width: 100px; height: 80px; border-radius: 10%; border: 3px solid orange; margin-left : 2px; }
        .sidebar h6, .sidebar p { margin: 2px 0; }
        .sidebar ul { padding: 0; }
        .sidebar ul li { list-style: none; margin: 15px 0; }
        .sidebar ul li a { color: white; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .content { margin-left: 300px; flex: 1; padding: 20px; }
        .recette-header { display: flex; align-items: flex-start; gap: 20px; }
        .recette-img {
    width: 100%;
    max-width: 500px; /* Largeur maximale pour éviter les images trop grandes */
    height: 400px; /* Hauteur fixe pour garder l'uniformité */
    object-fit: cover; /* Coupe l'image pour qu'elle remplisse l'espace sans déformation */
    border-radius: 10px;
    display: block;
    margin: 0 auto; /* Centre l'image */
}

        .recette-info { width: 50%; }
        .recette-title {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin-bottom: 10px;
}

.actions-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-top: 10px;
}
.stars {
    display: flex;
    gap: 5px;
    align-items: center;
}
.recette-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.like-container {
    display: flex;
    align-items: center;
    gap: 5px;
}
        .stars i { color: #ccc; }
        .stars i.active { color: gold; }
        .comment-section { margin-top: 20px; }
        .published-container { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .subscribe-btn { padding: 10px 20px; background: #ff5722; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .details-container { display: flex; justify-content: space-between; margin-top: 20px; }
        .details-container div { width: 48%; }
        .favorite-icon {
    cursor: pointer;
    color: gray;
    }
    .favorite-icon.active {
        color: red;
    }
    .save-icon {
    cursor: pointer;
    color: gray;
}
.save-icon.active {
    color: blue;
}
.star-icon {
    cursor: pointer;
    color: gray;
}
.star-icon.active {
    color: gold;
}
.like-btn {
    cursor: pointer;
}

.sidebar ul li a {
    color: white !important;
    display: flex;
    align-items: center;
    padding: 5px 10px;
    text-decoration: none;
    transition: 0.3s;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
  }

        .sidebar ul li a:hover {
    background-color: #ff5722;
    transform: translateX(5px);
  }
 /* ✅ Amélioration des commentaires */
.comment-item {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

.comment-item strong {
    font-size: 16px;
    color: #2c3e50;
}

.comment-item p {
    font-size: 15px;
    color: #555;
}

.comment-item small {
    font-size: 12px;
    color: #888;
}

.comment-item a {
    font-size: 12px;
    color: #ff5722;
    text-decoration: none;
    font-weight: bold;
    margin-right: 10px;
    padding: 1px 5px;
}

.comment-item a:hover {
    text-decoration: underline;
}

/* ✅ Amélioration du bouton Voir plus */
.view-more a {
    display: inline-block;
    color: white;
    padding: 8px 12px;
    font-size: 14px;
    border-radius: 5px;
    text-decoration: none;
    transition: 0.3s;
}



/* ✅ Meilleure mise en page des recettes */
.more-recipes {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.recipe-card {
    width: 30%;
    background: white;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.recipe-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 8px;
}

.recipe-card p {
    margin-top: 10px;
    font-weight: bold;
    color: #333;
}

/* ✅ Amélioration des étoiles */
.stars i.active {
    color: gold;
    font-size: 22px;
}

/* ✅ Ajout de transition aux icônes */
.favorite-icon, .save-icon, .star-icon, .like-btn {
    transition: 0.3s;
    cursor: pointer;
}

.favorite-icon.active {
    color: red;
    font-size: 22px;
}

.save-icon.active {
    color: blue;
    font-size: 22px;
}

.like-btn {
    font-size: 22px;
    color: #007BFF;
}

.like-btn.blue-text {
    color: #0056b3;
}

/* ✅ Amélioration de l'affichage des détails */
.details-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.details-container div {
    width: 48%;
}

.details-container h5 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

h4{
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}
/* ✅ Titres principaux */
h1, h2, h3, h5 {
    font-size: 1.5rem; /* Taille du texte */
  font-weight: bold; /* Texte en gras */
  color: #333; /* Couleur du texte */
  text-transform: uppercase; /* Mettre en majuscules */
  border-left: 4px solid #ff5722; /* Bordure gauche pour l'accent */
  padding-left: 10px; /* Espacement du texte par rapport à la bordure */
  margin-bottom: 15px; /* Espacement avec les éléments en dessous */
  font-family: 'Arial', sans-serif; /* Police de caractères */
}
h1, h2, h3, h4, h5:hover {
  color: #ff5722; /* Change la couleur du texte */
  border-left-color: #333; /* Change la couleur de la bordure */
  transform: translateX(5px); /* Déplace légèrement le titre vers la droite */
}
/* ✅ Sous-titres et détails */
h6, .small-text {
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    color: white;
}

/* ✅ Texte des paragraphes */
p {
    font-size: 16px;
    line-height: 1.6;
    color: #444;
}

/* ✅ Style des liens */
a {
    text-decoration: none;
    color: #007BFF;
    font-weight: 500;
    transition: 0.3s;
}

a:hover {
    color: #0056b3;
    text-decoration: underline;
}

/* ✅ Titres des sections */
.section h5 {
    font-size: 20px;
    font-weight: bold;
    color: #ff5722;
    text-transform: uppercase;
    margin-bottom: 15px;
}

/* ✅ Amélioration des tableaux */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

th {
    background: #2c3e50;
    color: white;
    padding: 12px;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

tr:hover {
    background: #f1f1f1;
}

/* ✅ Amélioration des boutons */
button, .btn {
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    padding: 10px 15px;
    border-radius: 5px;
    transition: 0.3s;
    cursor: pointer;
}

button:hover, .btn:hover {
    opacity: 0.8;
}

/* ✅ Amélioration des formulaires */
form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

input, textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 14px;
}

textarea {
    resize: none;
    height: 80px;
}

    </style>
</head>
<body>
    <div class="sidebar">
        <?php if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['email'])): ?>
            <img src="<?= isset($_SESSION['photo']) && $_SESSION['photo'] != '' ? htmlspecialchars($_SESSION['photo']) : 'default-avatar.png' ?>" alt="Photo de profil">
            <p><?= htmlspecialchars($_SESSION['nom']) . ' ' . htmlspecialchars($_SESSION['prenom']) ?></p>
            <p><?= htmlspecialchars($_SESSION['email']) ?></p>
        <?php else: ?>
            <img src="default-avatar.png" alt="Photo de profil">
            <h6>Nom & Prénom</h6>
            <p>Email</p>
        <?php endif; ?>
        <ul>
            <li><a href="Accueil.php"><i class="material-icons">home</i> Accueil</a></li>
            <li><a href="Profil.php"><i class="material-icons">person</i> Profil</a></li>
            <li><a href="Favoris.php"><i class="material-icons">favorite</i> Favoris</a></li>
            <li><a href="Enregistrement.php"><i class="material-icons">bookmark</i> Enregistrements</a></li>
            <li><a href="brouillons.php"><i class="material-icons">save</i> Brouillons</a></li>
            <li><a href="Publication.php"><i class="material-icons">edit</i> Publication</a></li>
            <li><a href="Deconnexion.php"><i class="material-icons">exit_to_app</i> Déconnexion</a></li>
            <li><a href="Notification.php"><i class="material-icons">notifications</i> Notifications</a></li>
            <li><a href="Historique.php"><i class="material-icons">history</i> Historique</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="recette-header">
        <img src="<?= htmlspecialchars($recette['photo']) ?>" class="recette-img" alt="Image de la recette">
        
            <div class="recette-info">
            <div class="recette-title">
    <h4><?= htmlspecialchars($recette['titre']) ?></h4>
</div>

<!-- Conteneur des étoiles et actions sur la même ligne -->
<div class="actions-container">
    <!-- Étoiles -->
    <div class="stars">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <i class="material-icons star-icon <?= ($i <= $user_note) ? 'active' : '' ?>" data-note="<?= $i ?>">star</i>
        <?php endfor; ?>
        <span id="moyenne-note">(<?= $moyenne ?>)</span>
    </div>
    
    <!-- Bouton J'aime avec le compteur -->
    <div class="like-container">
        <i class="material-icons like-btn <?= $liked ? 'blue-text' : '' ?>" data-id="<?= $recette_id ?>">thumb_up</i>
        <span id="like-count"><?= $total_likes ?></span>
    </div>
    <!-- Actions : Favoris, Enregistrement, J'aime, Téléchargement -->
    <div class="recette-actions">
        <i class="material-icons favorite-icon <?= $estFavori ? 'active' : '' ?>" data-id="<?= $recette_id ?>">favorite</i>
        <i class="material-icons save-icon <?= $estEnregistre ? 'active' : '' ?>" data-id="<?= $recette_id ?>">bookmark</i>
        

        <i class="material-icons">cloud_download</i>
    </div>
</div>


                
                <p><?= nl2br(htmlspecialchars($recette['description'])) ?></p>

                
                <div class="comment-section">
                <div class="comment-section">
    <h4>Commentaires</h5>

    <!-- Formulaire d'ajout de commentaire -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="AjoutCommentaire.php" method="POST" class="comment-form">
            <input type="hidden" name="recette_id" value="<?= $recette_id ?>">
            <div class="comment-box">
                <textarea name="contenu" class="materialize-textarea" placeholder="Écrivez un commentaire..." required></textarea>
                <button type="submit" class="send-btn">
                    <img src="send-icon.png" alt="Envoyer">
                </button>
            </div>
        </form>
    <?php else: ?>
        <p><a href="Connexion.php">Connectez-vous</a> pour commenter.</p>
    <?php endif; ?>

   
</div>


    <!-- Bouton pour voir tous les commentaires -->
    <div class="view-more">
        <a href="Commentaires.php?id=<?= $recette_id ?>" class="btn orange">Voir tous les commentaires</a>
    </div>
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
        <h4 style="text-align:center;">Plus de recettes</h5>
  <div class="more-recipes">
    <?php
   $sql = "SELECT id, titre, photo FROM recettes WHERE user_id = :user_id AND id != :recette_id ORDER BY RAND() LIMIT 3";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $recette['user_id'], 'recette_id' => $recette_id]);
    $recettes_auteur = $stmt->fetchAll();

    if ($recettes_auteur):
        foreach ($recettes_auteur as $r): ?>
            <div class="recipe-card">
                <a href="Recette.php?id=<?= $r['id'] ?>">
                    <img src="<?= htmlspecialchars($r['photo']) ?>" alt="<?= htmlspecialchars($r['titre']) ?>">
                    <p><?= htmlspecialchars($r['titre']) ?></p>
                </a>
            </div>
        <?php endforeach;
    else:
        echo "<p>Aucune autre recette de cet auteur.</p>";
    endif;
    ?>
</div>
<div class="view-more">
    <a href="Publication1.php?user_id=<?= $recette['user_id'] ?>">
        <i class="material-icons" style =" color: #ff5722; margin-left: 420px;">arrow_forward</i>
    </a>
</div>


        <div class="published-container">
            <p>Publié par <?= htmlspecialchars($recette['auteur']) ?> le <?= date('d/m/Y H:i', strtotime($recette['date_creation'])) ?></p>
            <button class="subscribe-btn" data-id="<?= $recette['user_id'] ?>">S'abonner</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".favorite-icon").forEach(icon => {
        icon.addEventListener("click", function() {
            let recetteId = this.getAttribute("data-id");
            let icon = this;
            
            fetch("AjoutFavoris.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "recette_id=" + recetteId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "added") {
                    icon.classList.add("active");
                } else {
                    icon.classList.remove("active");
                }
            });
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".save-icon").forEach(icon => {
        icon.addEventListener("click", function() {
            let recetteId = this.getAttribute("data-id");
            let icon = this;
            
            fetch("AjoutEnregistrement.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "recette_id=" + recetteId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "added") {
                    icon.classList.add("active");
                } else {
                    icon.classList.remove("active");
                }
            });
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".star-icon").forEach(star => {
        star.addEventListener("click", function() {
            let recetteId = <?= $recette_id ?>;
            let note = this.getAttribute("data-note");
            
            fetch("AjoutNotes.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "recette_id=" + recetteId + "&note=" + note
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll(".star-icon").forEach(s => s.classList.remove("active"));
                    for (let i = 0; i < note; i++) {
                        document.querySelectorAll(".star-icon")[i].classList.add("active");
                    }
                    document.getElementById("moyenne-note").innerText = `(${data.moyenne})`;
                }
            });
        });
    });
});

$(document).ready(function() {
    $(".like-btn").click(function() {
        var recetteId = $(this).data("id");
        var icon = $(this);
        
        $.post("Like.php", { recette_id: recetteId }, function(response) {
            if (response.success) {
                if (response.liked) {
                    icon.addClass("blue-text");
                } else {
                    icon.removeClass("blue-text");
                }
                $("#like-count").text(response.total_likes);
            }
        }, "json");
    });
});
document.addEventListener("DOMContentLoaded", function() {
    const subscribeBtn = document.querySelector(".subscribe-btn");

    if (subscribeBtn) {
        subscribeBtn.addEventListener("click", function() {
            let followingId = subscribeBtn.getAttribute("data-id"); // Récupération de l'ID de l'utilisateur à suivre
            let csrfToken = document.querySelector("meta[name='csrf-token']").getAttribute("content"); // Récupération du CSRF token

            fetch("Abonnement.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `following_id=${followingId}&csrf_token=${csrfToken}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.abonne) {
                        subscribeBtn.textContent = "Abonné ✅";
                        subscribeBtn.style.background = "#4CAF50";
                    } else {
                        subscribeBtn.textContent = "S'abonner";
                        subscribeBtn.style.background = "#ff5722";
                    }
                } else {
                    alert(data.message); // Affiche un message d'erreur si nécessaire
                }
            })
            .catch(error => console.error("Erreur :", error));
        });
    }
});


</script>

</script>


</body>
</html>
