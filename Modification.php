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
    <title>Ajouter Recette</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Modification.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="Accueil.php" class="material-icons">close</a>
            <span class="title">Ajouter Recette</span>
        </div>
        
        <form action="Modification.php" method="POST" enctype="multipart/form-data">
            <!-- Champ pour télécharger l'image -->
            <div class="row center">
                <a class="btn-floating btn-large red" onclick="document.getElementById('file-input').click();">
                    <i class="large material-icons">add</i>
                </a>
                <p>Ajouter une photo</p>

                <!-- Champ de fichier invisible -->
                <input type="file" id="file-input" name="photo" accept="image/*" style="display: none;" onchange="previewImage(event)">
            </div>

            <!-- Zone de prévisualisation de l'image -->
            <div id="image-preview" class="center">
                <img id="preview-img" src="" alt="Prévisualisation de l'image" style="display: none; max-width: 100px; max-height: 100px;">
            </div>

            <!-- Champ pour le titre -->
            <div class="row flex-row">
                <div class="col s6">
                    <div class="input-field">
                        <input type="text" id="titre" name="titre" placeholder="Titre" required>
                    </div>
                </div>
            </div>

            <!-- Champ pour la description -->
            <div class="row flex-row">
                <div class="col s6">
                    <div class="input-field">
                        <textarea id="description" name="description" class="materialize-textarea" placeholder="Description" required></textarea>
                    </div>
                </div>
            </div>

            <!-- Champ pour les portions et la durée -->
            <div class="row flex-row">
                <div class="col s6">
                    <div class="input-field">
                        <input type="number" id="portions" name="portions" min="1" placeholder="Portions" required>
                    </div>
                </div>
                <div class="col s6">
                    <div class="input-field">
                        <input type="text" id="duree" name="duree" placeholder="Durée (ex: 30 min)" required>
                    </div>
                </div>
            </div>

            <!-- Champs pour les ingrédients et méthodes -->
            <div class="row flex-row">
                <div class="col s6">
                    <div class="input-field" id="ingredients-container">
                        <input type="text" name="ingredients[]" placeholder="Ingrédients" required>
                        <a class="btn-floating btn-small" onclick="ajouterIngredient()"><i class="material-icons">add</i></a>
                    </div>
                </div>
                <div class="col s6">
                    <div class="input-field" id="methodes-container">
                        <input type="text" name="methodes[]" placeholder="Méthodes" required>
                        <a class="btn-floating btn-small" onclick="ajouterMethode()"><i class="material-icons">add</i></a>
                    </div>
                </div>
            </div>
            <input type="hidden" id="statut" name="statut" value="publie">

            <!-- Boutons pour enregistrer ou publier -->
            <div class="row center">
                <a class="btn btn-small grey" onclick="sauvegarderBrouillon()">Brouillons</a>
                <button type="submit" class="btn btn-small red">Publier</button>
            </div>

        </form>
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
            input.placeholder = "Ingrédients";
            container.appendChild(input);
        }

        function ajouterMethode() {
            let container = document.getElementById("methodes-container");
            let input = document.createElement("input");
            input.type = "text";
            input.name = "methodes[]";
            input.placeholder = "Méthodes";
            container.appendChild(input);
        }
        function sauvegarderBrouillon() {
        document.getElementById('statut').value = "brouillon";
        document.querySelector("form").submit(); // Soumettre le formulaire après avoir changé la valeur
    }
    </script>
</body>
</html>
