<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    die("Accès refusé.");
}

if (!isset($_GET['id'])) {
    die("Commentaire introuvable.");
}

$commentaire_id = $_GET['id'];
$sql = "SELECT * FROM commentaires WHERE id = :id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'id' => $commentaire_id,
    'user_id' => $_SESSION['user_id']
]);
$commentaire = $stmt->fetch();

if (!$commentaire) {
    die("Commentaire introuvable ou non autorisé.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu']);
    if (empty($contenu)) {
        die("Le commentaire ne peut pas être vide.");
    }

    $sql = "UPDATE commentaires SET contenu = :contenu WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'contenu' => $contenu,
        'id' => $commentaire_id
    ]);

    header("Location: Recette.php?id=" . $commentaire['recette_id']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le commentaire</title>
</head>
<body>
    <h2>Modifier le commentaire</h2>
    <form method="POST">
        <textarea name="contenu" required><?= htmlspecialchars($commentaire['contenu']) ?></textarea>
        <button type="submit">Modifier</button>
    </form>
</body>
</html>
