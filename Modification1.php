<?php
session_start();
include('config.php'); // Connexion à la base de données

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['titre'], $_POST['description'], $_FILES['photo'], $_POST['ingredients'], $_POST['methodes'], $_POST['portions'], $_POST['duree'], $_POST['categorie_id'])) {
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $photo = $_FILES['photo']['name'];
    $photoTmp = $_FILES['photo']['tmp_name'];
    $portions = $_POST['portions'];
    $duree = $_POST['duree'];
    $categorie_id = $_POST['categorie_id']; // ✅ Ajout de la catégorie
    $ingredients = implode(',', $_POST['ingredients']);
    $methodes = implode(',', $_POST['methodes']);
    $statut = isset($_POST['statut']) ? $_POST['statut'] : 'publie';


    // Vérifier si une image a été téléchargée
    if (!empty($photo)) {
        $imagePath = 'images/' . basename($photo);
        if (move_uploaded_file($photoTmp, $imagePath)) {
            // Insérer les données dans la base de données
            $sql = "INSERT INTO recettes (user_id, titre, description, photo, ingredients, methodes, portions, duree, statut, categorie_id) 
                    VALUES (:user_id, :titre, :description, :photo, :ingredients, :methodes, :portions, :duree, :statut, :categorie_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':titre' => $titre,
                ':description' => $description,
                ':photo' => $imagePath,
                ':ingredients' => $ingredients,
                ':methodes' => $methodes,
                ':portions' => $portions,
                ':duree' => $duree,
                ':statut' => $statut,
                ':categorie_id' => $categorie_id
            ]);
            echo "<pre>";
            print_r($categories);
            echo "</pre>";
            // Récupérer l'ID de la recette insérée
            $recetteId = $pdo->lastInsertId();

            // ---- TRAITEMENT DES INGREDIENTS ----
            foreach ($_POST['ingredients'] as $ingredient) {
                $ingredient = trim($ingredient);

                if (!empty($ingredient)) {
                    // Vérifier si l'ingrédient existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM ingredients WHERE nom = :nom");
                    $stmt->execute([':nom' => $ingredient]);
                    $ingredientId = $stmt->fetchColumn();

                    // Si l'ingrédient n'existe pas, on l'ajoute
                    if (!$ingredientId) {
                        $stmt = $pdo->prepare("INSERT INTO ingredients (nom) VALUES (:nom)");
                        $stmt->execute([':nom' => $ingredient]);
                        $ingredientId = $pdo->lastInsertId();
                    }

                    // Lier l'ingrédient à la recette
                    $stmt = $pdo->prepare("INSERT INTO recette_ingredients (recette_id, ingredient_id) 
                                           VALUES (:recette_id, :ingredient_id)");
                    $stmt->execute([
                        ':recette_id' => $recetteId,
                        ':ingredient_id' => $ingredientId
                    ]);
                }
            }

            // ---- TRAITEMENT DES METHODES ----
            foreach ($_POST['methodes'] as $methode) {
                $methode = trim($methode);

                if (!empty($methode)) {
                    // Insérer la méthode
                    $stmt = $pdo->prepare("INSERT INTO methodes (description) VALUES (:description)");
                    $stmt->execute([':description' => $methode]);
                    $methodeId = $pdo->lastInsertId();

                    // Lier la méthode à la recette
                    $stmt = $pdo->prepare("INSERT INTO recette_methodes (recette_id, methode_id) 
                                           VALUES (:recette_id, :methode_id)");
                    $stmt->execute([
                        ':recette_id' => $recetteId,
                        ':methode_id' => $methodeId
                    ]);
                }
            }

            // ✅ Redirection après succès
            header('Location: Admin1.php');
            exit;
        } else {
            echo "❌ Erreur lors du téléchargement de l'image.";
        }
    } else {
        echo "❌ Aucune image sélectionnée.";
    }
} else {
    // echo "❌ Tous les champs sont obligatoires.";
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Recette</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            display: flex;
            height: 100vh;
            margin: 0;
        }
        .left-section, .right-section {
            flex: 1;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .left-section {
            background: url('background.jpg') center/cover;
            height: 100vh;
        }
        .left-section img {
            width: 100%;
            height: 100vh;
            object-fit: cover;
        }
        .right-section {
            background: white;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 900px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: 1000px;
        }
        .right-section .container {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.title{
    margin-left: 220px; font-size: 30px; font-weight: 600; color: #333;
}
    </style>
</head>
<body>
    <div class="left-section">
        <img src="Image/ajout.png" alt="Illustration Ajout">
    </div>
    <div class="right-section">
        <div class="container">
            <div class="header">
                <a href="Admin1.php" class="material-icons">close</a>
                <span class="title" style="">Ajouter Recette</span>
            </div>
            
            <form action="Modification.php" method="POST" enctype="multipart/form-data">
            <div class="row">
    <div class="file-field input-field col s6">
        <div class="btn red">
            <span>Photo</span>
            <input type="file" id="file-input" name="photo" accept="image/*" onchange="previewImage(event)">
        </div>
        <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="Ajouter une photo">
        </div>
    </div>
    <div class="input-field col s6">
        <select name="categorie_id" required>
            <option value="" disabled selected></option>
            <?php foreach ($categories as $categorie) : ?>
                <option value="<?= $categorie['id'] ?>"><?= htmlspecialchars($categorie['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Catégorie</label>
    </div>
</div>

                <div class="row">
                    <div class="input-field col s6">
                        <input type="text" id="titre" name="titre" required>
                        <label for="titre">Titre</label>
                    </div>
                    <div class="input-field col s6">
                        <input type="text" id="duree" name="duree" required>
                        <label for="duree">Durée (ex: 30 min)</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s6">
                        <textarea id="description" name="description" class="materialize-textarea" required></textarea>
                        <label for="description">Description</label>
                    </div>
                    <div class="input-field col s6">
                        <input type="number" id="portions" name="portions" min="1" required>
                        <label for="portions">Portions</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s6" id="ingredients-container">
                        <label>Ingrédients</label>
                        <input type="text" name="ingredients[]" required>
                        <a class="btn-floating btn-small red" onclick="ajouterIngredient()">
                            <i class="material-icons">add</i>
                        </a>
                    </div>
                    <div class="input-field col s6" id="methodes-container">
                        <label>Méthodes</label>
                        <input type="text" name="methodes[]" required>
                        <a class="btn-floating btn-small red" onclick="ajouterMethode()">
                            <i class="material-icons">add</i>
                        </a>
                    </div>
                </div>
                <input type="hidden" id="statut" name="statut" value="publie">
                <div class="row center">
                    <button type="button" class="btn grey" onclick="sauvegarderBrouillon()">Brouillon</button>
                    <button type="submit" class="btn red">Publier</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
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
        function sauvegarderBrouillon() {
            document.getElementById('statut').value = "brouillon";
            document.querySelector("form").submit();
        }
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);
        });
    </script>
</body>
</html>
