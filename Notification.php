<?php
session_start();
include('config.php');

$sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY date_creation DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h4>Notifications</h4>
        <ul class="collection">
            <?php foreach ($notifications as $notif): ?>
                <li class="collection-item"><?= htmlspecialchars($notif['message']) ?> - <small><?= $notif['date_creation'] ?></small></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    fetch("MarquerLu.php")
        .then(response => response.text())
        .then(data => console.log(data)) // Optionnel : pour voir la réponse dans la console
        .catch(error => console.error("Erreur :", error));
});
</script>

</body>
</html>
