<?php
$password = "banca";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
?>

<div class="sidebar">
        <div class="profile">
            <?php if (isset($_SESSION['nom']) && isset($_SESSION['prenom']) && isset($_SESSION['email'])): ?>
                <!-- Affichage de la photo avant le nom et prénom -->
                <?php if (isset($_SESSION['photo']) && $_SESSION['photo'] != ''): ?>
                    <img src="<?php echo $_SESSION['photo']; ?>" alt="Photo de profil" width="80" height="80" />
                <?php else: ?>
                    <img src="default-avatar.png" alt="Photo de profil" width="80" height="80" />
                <?php endif; ?>
                <!-- Affichage du nom, prénom et email -->
                <p><strong><?php echo $_SESSION['nom']; ?> <?php echo $_SESSION['prenom']; ?></strong><br><?php echo $_SESSION['email']; ?></p>
            <?php else: ?>
                <p><strong>Nom&Prénom</strong><br>Email</p>
            <?php endif; ?>
            <div class="abonnements-info">
    <p><strong>Abonnés :</strong> <?php echo $nb_abonnes; ?>      <strong>Abonnements :</strong> <?php echo $nb_abonnements; ?></p>
</div>

        </div>
        
  .sidebar {
    background-color: #37474F; /* Couleur plus foncée pour un meilleur contraste */
    padding: 20px;
    height: 100vh;
    color: #fff;
    position: fixed;
    width: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
  }

  .sidebar .profile {
    margin-bottom: 20px;
    text-align: center;
    width: 100%;
  }

  .sidebar img {
    border-radius: 50%;
    margin-bottom: 1px;
    width: 100px;
    height: 100px;
    border: 3px solid #ff5722;
  }

  .sidebar h2 {
    font-size: 18px;
    font-weight: bold;
    margin: 5px 0;
    color: #ffffff;
  }

  .sidebar p {
    font-size: 14px;
    margin: 0;
    color: #cfd8dc;
  }

  .sidebar ul {
    padding: 0;
    list-style: none;
    width: 90%;
  }

  .sidebar ul li {
    width: 100%;
  }

  .sidebar ul li a {
    color: white !important;
    display: flex;
    align-items: center;
    padding: 9px 15px;
    text-decoration: none;
    transition: 0.3s;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
  }

  .sidebar ul li a:hover {
    background-color: #ff5722;
    transform: translateX(5px);
  }

  .sidebar ul li a .material-icons {
    margin-right: 15px;
    font-size: 20px;
  }
