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

    // Récupérer les informations de la recette
    $sql = "SELECT * FROM recettes WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $recette_id, 'user_id' => $_SESSION['user_id']]);
    $recette = $stmt->fetch();

    if (!$recette) {
        echo "Recette non trouvée.";
        exit();
    }

    // Traitement de la modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Mettre à jour les informations de la recette
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $portions = $_POST['portions'];
        $duree = $_POST['duree'];
        $image = $_POST['image']; // Assurez-vous de gérer l'upload d'image si nécessaire
        // Mettre à jour dans la base de données
        $sql = "UPDATE recettes SET titre = :titre, description = :description, portions = :portions, duree = :duree, image = :image WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre' => $titre, 
            'description' => $description, 
            'portions' => $portions, 
            'duree' => $duree, 
            'image' => $image,
            'id' => $recette_id
        ]);

        // Rediriger vers la page des publications
        header('Location: MesPublications.php');
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
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 20px;
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
        .title {
            font-size: 1.8rem;
            font-weight: bold;
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
        .flex-row {
            display: flex;
            gap: 15px;
        }
        .btn-floating {
            background-color: #ff7675;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="Publication.php" class="material-icons">close</a>
            <span class="title">Modifier Recette</span>
        </div>
        
        <div class="row center">
            <a class="btn-floating btn-large red">
                <i class="large material-icons">add</i>
            </a>
            <p>Modifier une photo</p>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row flex-row">
                <div class="col s6">
                    <div class="input-field">
                        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($recette['titre']); ?>" placeholder="Titre" required>
                    </div>
                </div>
                <div class="col s6">
                    <div class="input-field">
                        <textarea id="description" name="description" class="materialize-textarea" placeholder="Description" required><?php echo htmlspecialchars($recette['description']); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row flex-row">
                <div class="col s6">
                    <div class="input-field">
                        <input type="number" id="portions" name="portions" value="<?php echo htmlspecialchars($recette['portions']); ?>" min="1" placeholder="Portions" required>
                    </div>
                </div>
                <div class="col s6">
                    <div class="input-field">
                        <input type="text" id="duree" name="duree" value="<?php echo htmlspecialchars($recette['duree']); ?>" placeholder="Durée (ex: 30 min)" required>
                    </div>
                </div>
            </div>
            
            <div class="row center">
                <button type="submit" class="btn btn-small red">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
