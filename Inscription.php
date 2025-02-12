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
            <h4>Inscription</h4>
            <form action="signup.php" method="POST">
                <div class="input-field">
                    <input id="last_name" type="text" class="validate" name="nom"  required>
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
                    <input id="password" type="password" class="validate" name="password" required>
                    <label for="password">Mot de passe</label>
                </div>
                <button type="submit" class="btn">S'inscrire</button>
            </form>
            <div class="footer-text">
                <p>Déjà inscrit? <a href="Connexion.php">Se connecter</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
