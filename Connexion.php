<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <!-- Inclure Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
   <link rel="stylesheet" href="CSS/Connexion.css">
</head>
<body>

    <div class="container">
        <div class="card">
            <h4>Connexion</h4>
            <form action="login.php" method="POST">
                <div class="input-field">
                    <input id="email" type="email" class="validate" required>
                    <label for="email">Email</label>
                </div>
                <div class="input-field">
                    <input id="password" type="password" class="validate" required>
                    <label for="password">Mot de passe</label>
                </div>
                <button type="submit" class="btn">Se connecter</button>
            </form>
            <div class="footer-text">
                <p>Pas encore inscrit? <a href="Inscription.php">Créer un compte</a></p>
            </div>
        </div>
    </div>

    <!-- Inclure Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
