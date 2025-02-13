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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <style>
       body {
    background-color: #f4f4f9;
    font-family: 'Roboto', sans-serif;
}

.container {
    margin-top: 50px;
    max-width: 800px;
}

.card {
    padding: 30px;
    border-radius: 15px;
    background-color: #ffffff;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

h4 {
    font-size: 26px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.input-field input {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 12px 15px;
    font-size: 16px;
    background-color: #f9f9f9;
}

.input-field input:focus {
    border-color: #ff4081;
    box-shadow: 0 0 5px rgba(255, 64, 129, 0.5);
}

.label {
    font-size: 18px;
    color: #333;
}

.file-field {
    margin-top: 20px;
    border-radius: 10px;
    overflow: hidden;
}

.file-field .btn {
    background-color: #ff4081;
    border-radius: 5px;
    padding: 12px 30px;
    font-size: 16px;
}

.file-field .btn:hover {
    background-color: #f50057;
}

.file-path-wrapper .file-path {
    border-radius: 5px;
    padding: 10px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
}

.file-path-wrapper .file-path:focus {
    border-color: #ff4081;
    box-shadow: 0 0 5px rgba(255, 64, 129, 0.5);
}

.btn {
    width: 100%;
    background-color: #ff4081;
    font-size: 18px;
    border-radius: 5px;
    padding: 12px 0;
    margin-top: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn:hover {
    background-color: #f50057;
}

.footer-text {
    text-align: center;
    margin-top: 30px;
    font-size: 16px;
}

.footer-text a {
    color: #ff4081;
    font-weight: bold;
    text-decoration: none;
}

.footer-text a:hover {
    color: #f50057;
}

@media (max-width: 600px) {
    .container {
        margin-top: 20px;
    }

    .card {
        padding: 20px;
    }

    h4 {
        font-size: 22px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h4>Modifier votre Profil</h4>

            <form action="Profil.php" method="POST" enctype="multipart/form-data">
                <!-- Nom -->
                <div class="input-field">
                    <input id="nom" type="text" class="validate" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                    <label for="nom">Nom</label>
                </div>

                <!-- Prénom -->
                <div class="input-field">
                    <input id="prenom" type="text" class="validate" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                    <label for="prenom">Prénom</label>
                </div>

                <!-- Email -->
                <div class="input-field">
                    <input id="email" type="email" class="validate" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    <label for="email">Email</label>
                </div>

                <!-- Photo de profil -->
                <div class="file-field input-field">
                    <div class="btn">
                        <span>Changer la photo</span>
                        <input type="file" id="photo" name="photo" accept="image/*">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" placeholder="Choisir une photo de profil">
                    </div>
                </div>

                <!-- Bouton de mise à jour -->
                <button type="submit" class="btn">Mettre à jour</button>
            </form>
        </div>

        <div class="footer-text">
            <p><a href="Accueil.php">Retour à l'accueil</a></p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
