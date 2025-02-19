<?php
session_start();
include('config.php'); // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit();
}

if (isset($_GET['id'])) {
    $recette_id = $_GET['id'];

    // Vérifier si la recette est bien un brouillon
    $sql = "SELECT * FROM recettes WHERE id = :id AND user_id = :user_id AND statut = 'brouillon'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $recette_id, 'user_id' => $_SESSION['user_id']]);
    $recette = $stmt->fetch();

    if (!$recette) {
        echo "Brouillon non trouvé.";
        exit();
    }

    // Traitement de la modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $portions = $_POST['portions'];
        $duree = $_POST['duree'];
        $ingredients = $_POST['ingredients'];
        $methode = $_POST['methode'];
        
        // Gestion de l'image
        $image = $recette['photo']; // Garder l'ancienne image par défaut
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['photo']['tmp_name'];
            $imageName = $_FILES['photo']['name'];
            $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array(strtolower($imageExtension), $validExtensions)) {
                // Générer un nom unique
                $newImageName = uniqid('recette_', true) . '.' . $imageExtension;
                $imagePath = 'images/' . $newImageName;

                // Déplacer l'image vers le dossier "images"
                move_uploaded_file($imageTmpPath, $imagePath);
                $image = $newImageName; // Stocker seulement le nom du fichier
            } else {
                echo "Extension d'image invalide. Utilisez jpg, jpeg, png ou gif.";
                exit();
            }
        }

        // Mise à jour du brouillon
        $sql = "UPDATE recettes SET titre = :titre, description = :description, portions = :portions, duree = :duree, ingredients = :ingredients, methodes = :methodes, photo = :photo WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre' => $titre, 
            'description' => $description, 
            'portions' => $portions, 
            'duree' => $duree, 
            'ingredients' => $ingredients,
            'methodes' => $methode,
            'photo' => $image,
            'id' => $recette_id
        ]);

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
    <title>Modifier Brouillon</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            display: flex;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
        }
        .sidebar {
            width: 250px;
            background: #fff;
            padding: 20px;
            height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }
        .input-field input, .input-field textarea {
            font-size: 1rem;
            border-bottom: 2px solid #ff7675;
        }
        .btn-small {
            width: 40%;
            margin: 10px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 20px;
        }
        .btn-floating {
            background-color: #ff7675;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <ul>
            <li><a href="Profil.php">Profil</a></li>
            <li><a href="MesBrouillons.php">Mes Brouillons</a></li>
            <li><a href="Deconnexion.php">Déconnexion</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="container">
            <div class="header">
                <a href="MesBrouillons.php" class="material-icons">close</a>
                <span class="title">Modifier Brouillon</span>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="section-title">Image</div>
                <div class="input-field">
                    <?php if (!empty($recette['photo'])): ?>
                        <img src="images/<?php echo htmlspecialchars($recette['photo']); ?>" alt="Image de la recette" width="150">
                    <?php endif; ?>
                    <input type="file" name="photo" accept="image/*">
                </div>

                <div class="section-title">Informations du Brouillon</div>
                <div class="input-field">
                    <input type="text" name="titre" value="<?php echo htmlspecialchars($recette['titre']); ?>" required>
                </div>
                <div class="input-field">
                    <textarea name="description" class="materialize-textarea" required><?php echo htmlspecialchars($recette['description']); ?></textarea>
                </div>
                
                <div class="section-title">Ingrédients & Méthode</div>
                <div class="input-field">
                    <input type="text" name="ingredients" value="<?php echo htmlspecialchars($recette['ingredients']); ?>" required>
                </div>
                <div class="input-field">
                    <textarea name="methode" class="materialize-textarea" required><?php echo htmlspecialchars($recette['methodes']); ?></textarea>
                </div>
                
                <div class="section-title">Détails</div>
                <div class="input-field">
                    <input type="number" name="portions" value="<?php echo htmlspecialchars($recette['portions']); ?>" min="1" required>
                </div>
                <div class="input-field">
                    <input type="text" name="duree" value="<?php echo htmlspecialchars($recette['duree']); ?>" required>
                </div>
                
                <div class="row center">
                    <button type="submit" class="btn btn-small red">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
