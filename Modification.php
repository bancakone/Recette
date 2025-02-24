<?php
session_start();
include('config.php'); // Connexion à la base de données

if (isset($_POST['titre'], $_POST['description'], $_FILES['photo'], $_POST['ingredients'], $_POST['methodes'], $_POST['portions'], $_POST['duree'])) {
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $photo = $_FILES['photo']['name']; // Nom de l'image
    $photoTmp = $_FILES['photo']['tmp_name']; // Temporaire pour le transfert
    $portions = $_POST['portions'];
    $duree = $_POST['duree'];
    $ingredients = implode(',', $_POST['ingredients']); // Liste d'ingrédients séparée par des virgules
    $methodes = implode(',', $_POST['methodes']); // Liste des méthodes séparée par des virgules
    $statut = isset($_POST['statut']) ? $_POST['statut'] : 'publie'; // Défaut : publie

    // Vérifier si une image a été téléchargée
    if ($photo) {
        // Déplacer l'image téléchargée dans le dossier "images/"
        $imagePath = 'images/' . basename($photo); // Chemin final de l'image
        if (move_uploaded_file($photoTmp, $imagePath)) {
            // Insérer les données dans la base de données
            $sql = "INSERT INTO recettes (user_id, titre, description, photo, ingredients, methodes, portions, duree, statut) 
                    VALUES (:user_id, :titre, :description, :photo, :ingredients, :methodes, :portions, :duree, :statut)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'], // Assurer que l'utilisateur est connecté
                ':titre' => $titre,
                ':description' => $description,
                ':photo' => $imagePath, // Stocke le chemin de l'image dans la base de données
                ':ingredients' => $ingredients,
                ':methodes' => $methodes,
                ':portions' => $portions,
                ':duree' => $duree,
                ':statut' => $statut // Ajout du statut
            ]);

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

            // Rediriger vers la page d'accueil ou une autre page après publication
            header('Location: Accueil.php');
            exit;
        } else {
            echo "Erreur lors de l'upload de l'image.";
        }
    } else {
        echo "Aucune image téléchargée.";
    }
} else {
    // echo "Erreur lors de l'ajout de la recette. Veuillez vérifier tous les champs.";
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
        }
        .left-section, .right-section {
            flex: 1;
            height: 100vh;
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
        }
        .right-section {
            background: white;
            display: flex;
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
    </style>
</head>
<body>
    <div class="left-section">
        Nom de l'Application
    </div>
    <div class="right-section">
        <div class="container">
            <div class="header">
                <a href="Accueil.php" class="material-icons">close</a>
                <span class="title">Ajouter Recette</span>
            </div>
            
            <form action="Modification.php" method="POST" enctype="multipart/form-data">
                <div class="file-field input-field">
                    <div class="btn red">
                        <span>Photo</span>
                        <input type="file" id="file-input" name="photo" accept="image/*" onchange="previewImage(event)">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Ajouter une photo">
                    </div>
                </div>
                <div id="image-preview" class="center">
                    <img id="preview-img" src="" alt="Prévisualisation" style="display: none; max-width: 100%;">
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
        function previewImage(event) {
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

        function sauvegarderBrouillon() {
            document.getElementById('statut').value = "brouillon";
            document.querySelector("form").submit();
        }
    </script>
</body>
</html>
