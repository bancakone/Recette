<?php
session_start(); // Démarrer la session
include('config.php'); // Connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si l'email existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Stocker les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role']; // Stocker le rôle

        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            header("Location: Admin1.php"); // Page admin
        } else {
            header("Location: Accueil.php"); // Page utilisateur
        }
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/Connexion.css">
</head>
<body>

    <div class="container">
        <div class="card">
            <h4>Connexion</h4>
            <form action="Connexion.php" method="POST">
                <div class="input-field">
                    <input id="email" type="email" class="validate" name="email" required>
                    <label for="email">Email</label>
                </div>
                <div class="input-field">
                    <input id="password" type="password" class="validate" name="password" required>
                    <label for="password">Mot de passe</label>
                </div>
                <button type="submit" class="btn">Se connecter</button>
            </form>
            <div class="footer-text">
                <p>Pas encore inscrit? <a href="Inscription.php">Créer un compte</a></p>
            </div>
            <?php
                if (isset($error)) {
                    echo "<p style='color: red;'>$error</p>";
                }
            ?>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
