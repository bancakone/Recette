<?php
session_start();
include('config.php'); // Connexion à la base de données

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

if (isset($_GET['id'])) {
    $recette_id = $_GET['id'];

    // Récupérer les informations de la recette
    $sql = "SELECT * FROM recettes WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $recette_id, 'user_id' => $_SESSION['user_id']]);
    $recette = $stmt->fetch();

    if (!$recette) {
        echo "Recette non trouvée.";
        exit();
    }

    // Définir la catégorie actuelle
    $categorie_id_actuelle = $recette['categorie_id'];

    // Traitement de la modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $portions = $_POST['portions'];
        $duree = $_POST['duree'];

        // Convertir les tableaux en chaînes
        $ingredients = implode(", ", $_POST['ingredients']);
        $methode = implode(", ", $_POST['methodes']);

        // Gestion de l'image
        $image = $recette['photo']; // Image par défaut (ancienne image)
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['photo']['tmp_name'];
            $imageName = $_FILES['photo']['name'];
            $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array(strtolower($imageExtension), $validExtensions)) {
                $newImageName = uniqid('recette_', true) . '.' . $imageExtension;
                $imagePath = 'images/' . $newImageName;
                move_uploaded_file($imageTmpPath, $imagePath);
                $image = $imagePath;
            } else {
                echo "Extension d'image invalide. Utilisez jpg, jpeg, png, ou gif.";
                exit();
            }
        }

        $sql = "UPDATE recettes 
        SET titre = :titre, description = :description, portions = :portions, duree = :duree, 
            ingredients = :ingredients, methodes = :methodes, photo = :photo, categorie_id = :categorie_id 
        WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre' => $titre, 
            'description' => $description, 
            'portions' => $portions, 
            'duree' => $duree, 
            'ingredients' => $ingredients,
            'methodes' => $methode,
            'photo' => $image,
            'categorie_id' => $_POST['categorie_id'],  // Nouvelle catégorie
            'id' => $recette_id
        ]);

        header('Location: Publication.php');
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
            margin: 0;
            justify-content: space-between; /* Alignement côte à côte */
        }

        .left-section, .right-section {
            display: flex;
            align-items: center;  /* Centrer verticalement */
            justify-content: center; /* Centrer horizontalement */
            height: 100%; /* Hauteur 100% pour occuper tout l'écran */
        }

        .left-section {
            background: url('background.jpg') center/cover;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(168, 161, 161, 0.5);
            width: 50%;  /* 50% de la largeur */
            padding: 0;
        }

        .right-section {
            width: 50%; /* 50% de la largeur */
            height: 100%; /* Hauteur 100% */
            padding: 20px;
            overflow-y: auto;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(160, 93, 93, 0.1);
        }

        .title {
            font-family: 'Arial', sans-serif;
            font-size: 1.5rem;
            font-weight: bold;
            color: #37474F;
            margin-bottom: 20px;
            text-align: center;
            margin-left: 190px;
        }

        .input-field {
            margin-bottom: 20px;
        }

        .row {
            margin-bottom: 15px;
        }

        .left-section img {
            object-fit: cover; /* Maintient le ratio d'aspect */
            width: 100%; /* L'image occupe 100% de la largeur */
            height: 100%; /* L'image occupe 100% de la hauteur */
        }
        label{
            font-weight: bold;
            color: #37474F;
        }
    </style>
</head>
<body>
    <div class="left-section">
        <img src="Image/recette.png" alt="">
    </div>
    <div class="right-section">
        <div class="container">
            <div class="header">
                <a href="Publication.php" class="material-icons">close</a>
                <span class="title">Modifier Recette</span>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="file-field input-field col s6">
                        <div class="btn red">
                            <span>Photo</span>
                            <input type="file" id="file-input" name="photo" accept="image/*" onchange="previewImage(event)">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Modifier la photo">
                        </div>
                    </div>
                    <div class="input-field col s6">
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
