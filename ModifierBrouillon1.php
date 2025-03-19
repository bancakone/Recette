<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT nom, email, photo FROM users WHERE id = :id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute(['id' => $user_id]);
$user = $stmt_user->fetch();

// Vérifie si l'ID de la recette est passé dans l'URL
if (isset($_GET['id'])) {
    $recette_id = $_GET['id'];
    $sql = "SELECT * FROM recettes WHERE id = :id AND user_id = :user_id AND statut = 'brouillon'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $recette_id, 'user_id' => $_SESSION['user_id']]);
    $recette = $stmt->fetch();
    
    if (!$recette) {
        echo "Brouillon non trouvé.";
        exit();
    }

    // Traitement du formulaire lors de la soumission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = $_POST['titre'];
        $duree = $_POST['duree'];
        $portions = $_POST['portions'];
        $description = $_POST['description'];
        $ingredients = $_POST['ingredients'];
        $methode = $_POST['methodes'];
        
        // Gérer l'image téléchargée
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Dossier où l'image sera stockée
            $target_dir = "brouillon_image/";
            $photo = $target_dir . basename($_FILES['photo']['name']);
            
            // Déplace l'image téléchargée vers le dossier
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo)) {
                echo "L'image a été téléchargée avec succès.";
            } else {
                echo "Erreur lors du téléchargement de l'image.";
            }
        } else {
            // Si aucune photo n'est téléchargée, conserver l'ancienne photo
            $photo = $recette['photo'];
        }
        
        // Mise à jour de la recette
        $sql_update = "UPDATE recettes SET titre = :titre, duree = :duree, portions = :portions, description = :description, ingredients = :ingredients, methodes = :methodes, photo = :photo WHERE id = :id AND user_id = :user_id";
        $stmt_update = $pdo->prepare($sql_update);
        if (!$stmt_update->execute([
            'titre' => $titre,
            'duree' => $duree,
            'portions' => $portions,
            'description' => $description,
            'ingredients' => $ingredients,
            'methodes' => $methode,
            'photo' => $photo,
            'id' => $recette_id,
            'user_id' => $_SESSION['user_id']
        ])) {
            // Si une erreur survient, afficher le détail de l'erreur
            print_r($stmt_update->errorInfo());
        } else {
            echo "Recette mise à jour avec succès";
        }
        
        // Redirection après la mise à jour
        header('Location: Brouillons.php');
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Recette</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            display: flex;
            height: 100vh;
            overflow: hidden; /* Empêche la barre de défilement */
        }
        .left-section, .right-section {
            flex: 1;
            height: 100vh;
            width: 50%;
        }
        .left-section {
            background: url('background.jpg') center/cover;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
     
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .container {
            width: 100%;
            max-width: 700px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .title {
    font-family: 'Arial', sans-serif; /* Remplacez par la police de votre choix */
    font-size: 1.5rem; /* Taille de la police */
    font-weight: bold; /* Gras */
    color: #37474F; /* Couleur du texte */
    margin-bottom: 20px; /* Espace en bas */
    margin-left: 180px; /* Espace à gauche */
     
}
.left-section {
        background: url('background.jpg') center/cover;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px;
        font-size: 24px;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        flex-direction: column;
        opacity: 0; /* Initialement caché */
        animation: fadeIn 2s forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .app-name {
        font-size: 36px;
        font-family: 'Roboto', sans-serif;
        font-weight: 700;
    }

    </style>
</head>
<body>
    <div class="left-section">
        Nom de l'Application
    </div>
    <div class="right-section">
        <div class="container">
            <div class="header">
                <a href="Publication.php" class="material-icons">close</a>
                <span class="title" style="text-align: center;">Modifier Recette</span>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
            <div class="row" style="display: flex; align-items: center;">
    <div class="file-field input-field" style="flex: 1; margin-right: 20px;">
        <div class="btn red">
            <span>Photo</span>
            <input type="file" id="file-input" name="photo" accept="image/*" onchange="previewImage(event)">
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="Modifier la photo">
        </div>
    </div>

    <div class="input-field" style="flex: 1;">
        <select name="categorie_id" required>
            <option value="" disabled selected>Choisir une catégorie</option>
            <?php foreach ($categories as $categorie): ?>
                <option value="<?= $categorie['id'] ?>" <?= ($categorie_id_actuelle == $categorie['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($categorie['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label>Catégorie</label>
    </div>
</div>

                <!-- <div id="image-preview" class="center">
                    <img id="preview-img" src="<?php echo htmlspecialchars($recette['photo']); ?>" alt="Prévisualisation" style="max-width:10%;">
                </div> -->

                <div class="row">
                    <div class="input-field col s6">
                        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($recette['titre']); ?>" required>
                        <label for="titre">Titre</label>
                    </div>
                    <div class="input-field col s6">
                        <input type="text" id="duree" name="duree" value="<?php echo htmlspecialchars($recette['duree']); ?>" required>
                        <label for="duree">Durée (ex: 30 min)</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s6">
                        <textarea id="description" name="description" class="materialize-textarea" required><?php echo htmlspecialchars($recette['description']); ?></textarea>
                        <label for="description">Description</label>
                    </div>
                    <div class="input-field col s6">
                        <input type="number" id="portions" name="portions" min="1" value="<?php echo htmlspecialchars($recette['portions']); ?>" required>
                        <label for="portions">Portions</label>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s6" id="ingredients-container">
                        <label>Ingrédients</label>
                        <input type="text" name="ingredients[]" value="<?php echo htmlspecialchars($recette['ingredients']); ?>" required>
                        <a class="btn-floating btn-small red" onclick="ajouterIngredient()">
                            <i class="material-icons">add</i>
                        </a>
                    </div>
                    <div class="input-field col s6" id="methodes-container">
                        <label>Méthodes</label>
                        <input type="text" name="methodes[]" value="<?php echo htmlspecialchars($recette['methodes']); ?>" required>
                        <a class="btn-floating btn-small red" onclick="ajouterMethode()">
                            <i class="material-icons">add</i>
                        </a>
                    </div>
                </div>
                
                <div class="row center">
                    <button type="submit" class="btn red">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
       function previewImage(event) {
    alert("Image sélectionnée !");
    let reader = new FileReader();
    reader.onload = function() {
        let output = document.getElementById('preview-img');
        output.src = reader.result;
        output.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}


        function ajouterIngredient() {
            let container = document.getElementById("ingredients-container");
            let input = document.createElement("input");
            input.type = "text";
            input.name = "ingredients[]";
            container.appendChild(input);
        }

        function ajouterMethode() {
            let container = document.getElementById("methodes-container");
            let input = document.createElement("input");
            input.type = "text";
            input.name = "methodes[]";
            container.appendChild(input);
        }
        
document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('select');
    M.FormSelect.init(elems);
});


    </script>
</body>
</html>
