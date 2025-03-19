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
    
    // Définir la catégorie actuelle
    $categorie_id_actuelle = $recette['categorie_id'];

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
            font-family: 'Roboto', sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: space-between;
            padding: 30px;
            height: 100vh;
        }
        .left-section {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 48%;
            overflow-y: auto;
        }
        .right-section {
            width: 48%;
            background-color: #2c3e50;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
        }
        .right-section h1 {
            font-size: 3em;
            font-weight: bold;
            text-align: center;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }
        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header a {
            color: #ff5722;
            font-size: 1.5em;
            text-decoration: none;
        }
        .form-container form {
            width: 100%;
        }
        .input-field {
            margin-bottom: 20px;
        }
        .btn {
            background-color: #ff5722;
            width: 100%;
            border-radius: 50px;
            padding: 10px;
            font-size: 18px;
        }
        .file-field .btn {
            background-color: #ff5722;
        }
        .file-path-wrapper input {
            border: 1px solid #ccc;
        }
        .file-path-wrapper {
            margin-top: 10px;
        }
        #image-preview {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        #preview-img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .row {
            display: flex;
            justify-content: space-between;
        }
        .row .input-field {
            flex: 1;
            margin-right: 20px;
        }
        .row .input-field:last-child {
            margin-right: 0;
        }
        select {
            background-color: #fff;
            border-radius: 5px;
            padding: 5px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="left-section">
            <div class="form-container">
                <div class="header">
                    <a href="Brouillons.php" class="material-icons">close</a>
                    <span class="title">Modifier Recette</span>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="file-field input-field">
                        <div class="btn red">
                            <span>PHOTO</span>
                            <input type="file" name="photo" accept="image/*" >
                            <img id="preview-img" style="display:none;">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Modifier la photo">
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field">
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
                        <div class="input-field">
                            <input type="text" name="titre" value="<?= htmlspecialchars($recette['titre']) ?>" required>
                            <label>Titre</label>
                        </div>
                        <div class="input-field">
                            <input type="text" name="duree" value="<?= htmlspecialchars($recette['duree']) ?>" required>
                            <label>Durée (ex: 30 min)</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field">
                            <input type="number" name="portions" value="<?= htmlspecialchars($recette['portions']) ?>" min="1" required>
                            <label>Portions</label>
                        </div>
                        <div class="input-field">
                            <textarea name="description" class="materialize-textarea" required><?= htmlspecialchars($recette['description']) ?></textarea>
                            <label>Description</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field">
                            <textarea name="ingredients" class="materialize-textarea" required><?= htmlspecialchars($recette['ingredients']) ?></textarea>
                            <label>Ingrédients</label>
                        </div>
                        <div class="input-field">
                            <textarea name="methodes" class="materialize-textarea" required><?= htmlspecialchars($recette['methodes']) ?></textarea>
                            <label>Méthodes</label>
                        </div>
                    </div>

                    <button type="submit" class="btn waves-effect waves-light">ENREGISTRER LES MODIFICATIONS</button>
                </form>
            </div>
        </div>

        <div class="right-section">
            <h1>Nom de l'Application</h1>
        </div>
    </div>

    <script>
        // function previewImage(event) {
        //     let reader = new FileReader();
        //     reader.onload = function() {
        //         let output = document.getElementById('preview-img');
        //         output.src = reader.result;
        //         output.style.display = 'block';
        //     }
        //     reader.readAsDataURL(event.target.files[0]);
        // }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
