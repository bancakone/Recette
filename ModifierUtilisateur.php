<?php
include('config.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Récupérer les informations de l'utilisateur
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Mise à jour de l'utilisateur
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $email = $_POST['email'];
            $role = $_POST['role'];

            $update_sql = "UPDATE users SET nom = ?, prenom = ?, email = ?, role = ? WHERE id = ?";
            $stmt_update = $pdo->prepare($update_sql);
            $stmt_update->execute([$nom, $prenom, $email, $role, $user_id]);

            header('Location: Admin1.php');
        }
    } else {
        echo "Utilisateur non trouvé.";
        exit;
    }
} else {
    echo "ID manquant.";
    exit;
}
?>

<form method="post">
    <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
    <input type="text" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    <input type="text" name="role" value="<?php echo htmlspecialchars($user['role']); ?>" required>
    <button type="submit" class="btn green">Mettre à jour</button>
</form>
