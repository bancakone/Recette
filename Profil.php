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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 80vw;
            max-width: 900px;
            height: 80vh;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Section Image */
        .image-section {
            background: url('uploads/istockphoto-1327954795-612x612.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 30px;
            color: white;
            position: relative;
        }

        .image-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
           
            z-index: 1;
        }

        .image-section h1,
        .image-section p {
            position: relative;
            z-index: 2;
        }

        .image-section h1 {
            font-size: 2rem;
            font-weight: 600;
        }

        .image-section p {
            font-size: 1rem;
            font-weight: 300;
            margin-top: 10px;
        }

        /* Section Formulaire */
        .form-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            text-align: center;
            background: rgba(243, 236, 236, 0.5);
        }

        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .input-group {
            width: 100%;
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            font-weight: 400;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #ff758c;
            box-shadow: 0px 0px 5px rgba(255, 117, 140, 0.5);
        }

        .btn {
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: white;
            font-size: 1rem;
            border: none;
            padding: 9px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
            margin-top: 10px;
        }

        .btn:hover {
            background: linear-gradient(135deg, #ff5c7a, #ff709b);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                height: auto;
            }
            .image-section {
                display: none;
            }
            .form-section {
                width: 100%;
                padding: 30px;
            }
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
       
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Section Image -->
        <div class="image-section">
           <img src="Image/profil.png" alt="" srcset="">
        </div>

        <!-- Section Formulaire -->
        <div class="form-section">
            <h2>Modifier votre Profil</h2>
            <a href="Accueil.php" class="close-btn">&times;</a>
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
