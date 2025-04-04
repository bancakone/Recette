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

$sql = "SELECT recettes.*, users.nom, users.prenom 
        FROM recettes 
        JOIN users ON recettes.user_id = users.id 
        WHERE recettes.id = :recette_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['recette_id' => $recette_id]);
$recette1 = $stmt->fetch(PDO::FETCH_ASSOC);

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
 <link rel="stylesheet" href="CSS/Recette.css">
 <style>
.details-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.section {
    padding: 20px;
}



.section ul {
    padding-left: 0;
    list-style-type: none;
    margin: 0;
}

.section ul li {
    font-size: 16px;
    color: #555;
    padding: 8px 0;
}

.section ul li:before {
    content: '\2022'; /* Caractère point */
    color: #555;
    font-weight: bold;
    display: inline-block;
    width: 1em;
    margin-left: -1em;
}

.section ul li:hover {
    background-color: #f0f0f0;
    cursor: pointer;
}

/* Styliser le texte */
.published-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 16px;
    color: #333;
}

/* Nom et prénom en gras et bleu */
.author-name {
    font-weight: bold;
    color:rgb(26, 27, 27);
    font-family: 'Poppins', sans-serif; /* Tu peux changer par 'Roboto', 'Lato', etc. */
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
    <div class="section">
        <h5>Ingrédients</h5>
        <ul>
            <?php
            // Supposons que $recette['ingredients'] contient la chaîne d'ingrédients
            $ingredients = explode(', ', $recette['ingredients']);
            foreach ($ingredients as $ingredient) {
                echo "<li>$ingredient</li>";
            }
            ?>
        </ul>
    </div>
    <div class="section">
        <h5>Méthodes</h5>
        <ul>
            <?php
            // Supposons que $recette['methodes'] contient la chaîne des étapes
            $methodes = explode(', ', $recette['methodes']);
            foreach ($methodes as $methode) {
                echo "<li>$methode</li>";
            }
            ?>
        </ul>
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
    <a href="Publication2.php?user_id=<?= $recette['user_id'] ?>">
        <i class="material-icons" style =" color: #ff5722; margin-left: 600px;">arrow_forward</i>
    </a>
</div>


<div class="published-container">
    <p>Publié par <span class="author-name"><?= htmlspecialchars($recette1['nom'] . ' ' . $recette1['prenom']) ?></span> 
       le <?= date('d/m/Y H:i', strtotime($recette['date_creation'])) ?></p>
       <button class="btn red" onclick="window.history.back()">Retour</button>

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
