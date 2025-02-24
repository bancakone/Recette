<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: Connexion.php"); // Rediriger vers la page de connexion si non connecté
    exit();
}

include('config.php'); // Inclure la connexion à la base de données

// Récupérer les informations actuelles de l'utilisateur
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

// Vérifier si les informations sont présentes
if (!$user) {
    echo "Utilisateur non trouvé";
    exit();
}

// Traitement du formulaire de mise à jour
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    // Si l'utilisateur a téléchargé une nouvelle photo
    if ($_FILES['photo']['error'] == 0) {
        $photo = $_FILES['photo'];
        $photo_name = uniqid() . '-' . basename($photo['name']);
        $photo_path = 'uploads/' . $photo_name;

        // Déplacer la photo vers le répertoire des téléchargements
        if (move_uploaded_file($photo['tmp_name'], $photo_path)) {
            // Mettre à jour l'URL de la photo dans la base de données
            $query = "UPDATE users SET nom = :nom, prenom = :prenom, email = :email, photo = :photo WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'photo' => $photo_path, 'id' => $user_id]);
        } else {
            echo "Erreur lors de l'upload de la photo.";
        }
    } else {
        // Si aucune nouvelle photo n'est téléchargée
        $query = "UPDATE users SET nom = :nom, prenom = :prenom, email = :email WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'id' => $user_id]);
    }

    // Mettre à jour les informations dans la session
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['email'] = $email;
    if (isset($photo_path)) {
        $_SESSION['photo'] = $photo_path; // Enregistrer le nouveau chemin de la photo dans la session
    }

    // Redirection vers la page d'accueil après la mise à jour
    header("Location: Accueil.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes blinkText {
            50% { opacity: 0; }
        }
        @keyframes slideBackground {
            0% { background-position: -100% 0; }
            100% { background-position: 100% 0; }
        }
        @keyframes gradientText {
            0% { background-position: -100% 0; }
            100% { background-position: 100% 0; }
        }

        body {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            background-color: #fff8e1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            width: 100vw;
            height: 100vh;
        }
        .image-section {
            width: 50%;
            position: relative;
            background: url('uploads/istockphoto-1327954795-612x612.jpg') no-repeat center center/cover;
            filter: brightness(50%);
            animation: slideBackground 8s infinite linear;
        }
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            text-align: center;
        }
        .form-section {
            width: 50%;
            padding: 50px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #ffcc80;
        }
        .animated-title {
            font-size: 2em;
            font-weight: bold;
            color: #d84315;
            animation: fadeIn 2s ease-out, blinkText 1s infinite;
            background: linear-gradient(90deg, #ff5722, #e64a19);
            background-size: 200% 100%;
            color: transparent;
            -webkit-background-clip: text;
            animation: gradientText 4s ease-in-out infinite;
        }
        h2 {
            color: #d84315;
            animation: fadeIn 2s ease-out;
        }
        .input-group {
            margin: 15px 0;
            text-align: left;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"], input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #d84315;
            border-radius: 5px;
            background-color: #fff3e0;
        }
        .btn {
            background: #ff5722;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .btn:hover {
            background: #e64a19;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section">
            <div class="image-overlay">
                <h1 class="animated-title">Bienvenue sur CookPad !</h1>
                <p class="animated-title">“La cuisine, c'est le partage et l'amour des bons plats.”</p>
            </div>
        </div>
        <div class="form-section">
            <div class="animated-title">CookPad</div>
            <h2>Modifier votre Profil</h2>
            <form action="Profil.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="photo">Photo de profil</label>
                    <input type="file" id="photo" name="photo">
                </div>
                <button type="submit" class="btn">Mettre à jour</button>
            </form>
        </div>
    </div>
</body>
</html>
