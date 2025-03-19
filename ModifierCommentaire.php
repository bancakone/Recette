<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

if (!isset($_GET['id'])) {
    die("Commentaire introuvable.");
}

$commentaire_id = $_GET['id'];
$sql = "SELECT * FROM commentaires WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'id' => $commentaire_id,
    'user_id' => $_SESSION['user_id']
]);
$commentaire = $stmt->fetch();

if (!$commentaire) {
    die("Commentaire introuvable ou non autorisé.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu']);
    if (empty($contenu)) {
        die("Le commentaire ne peut pas être vide.");
    }

    $sql = "UPDATE commentaires SET contenu = :contenu WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'contenu' => $contenu,
        'id' => $commentaire_id
    ]);

    header("Location: Recette.php?id=" . $commentaire['recette_id']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le commentaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #ff5722;
            margin-bottom: 30px;
        }

        .container {
    max-width: 300px; /* Réduire la largeur */
    margin: 0 auto;
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    margin-top:100px;
}


        textarea {
            width: 100%;
            height: 50px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: none;
            font-size: 16px;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }

        textarea:focus {
            border-color: #ff5722;
            outline: none;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #ff5722;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #e64a19;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .cancel-button {
            display: inline-block;
            text-decoration: none;
            background-color: #ccc;
            padding: 10px 20px;
            border-radius: 5px;
            color: #333;
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            margin-left: 50px;
        }

        .cancel-button:hover {
            background-color: #999;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Modifier le commentaire</h2>

    <form method="POST">
        <div class="form-group">
            <textarea name="contenu" required><?= htmlspecialchars($commentaire['contenu']) ?></textarea>
        </div>
        <button type="submit">Modifier</button>
    </form>

    <a href="Recette.php?id=<?= $commentaire['recette_id'] ?>" class="cancel-button">Retour à la recette</a>
</div>

</body>
</html>
