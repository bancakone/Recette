<?php
include 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // Correction ici
    $role = $_POST['role'];

    // Rechercher l'utilisateur dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND role = ? LIMIT 1");
    $stmt->execute([$email, $role]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Rediriger selon le rôle
        switch ($role) {
            case 'client':
                header("Location: dashboardclient.php");
                break;
            case 'gie':
                header("Location: dashboardgie.php");
                break;
            case 'admin':
                header("Location: dashboardadmin.php");
                break;
        }
        exit();
    } else {
        echo htmlspecialchars("Email ou mot de passe incorrect.");
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Connexion</h2>
    <form action="connexion.php" method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br> <!-- Correction ici -->
        <select name="role">
            <option value="client">Client</option>
            <option value="gie">GIE</option>
            <option value="admin">Admin</option>
        </select><br>
        <button type="submit">Se connecter</button>
    </form>
    <a href="inscription.php">Pas encore inscrit ? Inscrivez-vous</a>
</body>
</html>