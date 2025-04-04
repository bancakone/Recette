<?php

include('config.php'); // Inclut le fichier de connexion à la base de données avec PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Récupérer le rôle

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        $error = "Cet email est déjà utilisé.";
    } else {
        // Hachage du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur dans la base de données
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password,role) VALUES (?,?,?,?,?)");
        $stmt->execute([$nom, $prenom, $email, $hashed_password, $role]); // Insérer le rôle

     

        // Rediriger vers la page d'accueil
        header("Location: Admin1.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Inscription.css">
</head>
<body>

    <div class="container">
        <div class="card">
            <h4>Ajouter </h4>
            <form action="AjouterUtilisateur.php" method="POST">
                <div class="input-field">
                    <input id="last_name" type="text" class="validate" name="nom" required>
                    <label for="last_name">Nom</label>
                </div>
                <div class="input-field">
                    <input id="first_name" type="text" class="validate" name="prenom" required>
                    <label for="first_name">Prénom</label>
                </div>
                <div class="input-field">
                    <input id="email" type="email" class="validate" name="email" required>
                    <label for="email">Email</label>
                </div>
                <div class="input-field">
                    <input id="role" type="text" class="validate" name="role" required>
                    <label for="role">Rôle</label>
                </div>
                <div class="input-field">
                    <input id="password" type="password" class="validate" name="password" required>
                    <label for="password">Mot de passe</label>
                </div>
                <button type="submit" class="btn">Ajouter</button>
            </form>
          
          
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
