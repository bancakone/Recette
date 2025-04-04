<?php 
include('config.php');
$user_id = $_SESSION['user_id'];  // ID de l'utilisateur connecté

// Requête pour récupérer les informations de l'utilisateur
$sql = "SELECT nom, prenom FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// Vérifier si un utilisateur a été trouvé
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Afficher les informations de l'utilisateur
    $user_name = htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']);
} else {
    // Gestion du cas où l'utilisateur n'existe pas
    $user_name = "Utilisateur non trouvé";
}
// Définir le nombre de résultats par page
$results_per_page = 5; 

// Trouver le numéro de la page actuelle pour les utilisateurs et les recettes
$page_users = isset($_GET['page_users']) ? $_GET['page_users'] : 1;
$page_recipes = isset($_GET['page_recipes']) ? $_GET['page_recipes'] : 1;

// Calculer le point de départ des résultats pour les utilisateurs et les recettes
$start_from_users = ($page_users - 1) * $results_per_page;
$start_from_recipes = ($page_recipes - 1) * $results_per_page;

// Requête SQL pour récupérer les utilisateurs avec pagination
$sql_users = "SELECT  id,nom, prenom, email, role FROM users LIMIT $start_from_users, $results_per_page";
$result_users = $pdo->query($sql_users);

// Requête pour obtenir le nombre total d'utilisateurs
$sql_total_users = "SELECT COUNT(*) FROM users";
$total_result_users = $pdo->query($sql_total_users);
$total_rows_users = $total_result_users->fetchColumn();

// Calculer le nombre total de pages pour les utilisateurs
$total_pages_users = ceil($total_rows_users / $results_per_page);

// Requête SQL pour récupérer les recettes avec pagination
$sql_recipes = "SELECT r.id, r.titre, u.nom AS utilisateur_nom, u.prenom AS utilisateur_prenom, 
                       COUNT(c.id) AS nb_commentaires, COUNT(l.id) AS nb_likes, 
                       cat.nom AS categorie, r.date_creation
                FROM recettes r
                JOIN users u ON r.user_id = u.id
                LEFT JOIN commentaires c ON r.id = c.recette_id
                LEFT JOIN likes l ON r.id = l.recette_id
                JOIN categories cat ON r.categorie_id = cat.id
                GROUP BY r.id
                LIMIT $start_from_recipes, $results_per_page";


$result_recipes = $pdo->query($sql_recipes);

// Requête pour obtenir le nombre total de recettes
$sql_total_recipes = "SELECT COUNT(*) FROM recettes";
$total_result_recipes = $pdo->query($sql_total_recipes);
$total_rows_recipes = $total_result_recipes->fetchColumn();

// Calculer le nombre total de pages pour les recettes
$total_pages_recipes = ceil($total_rows_recipes / $results_per_page);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Recettes</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Roboto', sans-serif;
        }

        .topbar {
            background-color: #333;
            padding: 10px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .topbar div {
            font-size: 16px;
        }

        .topbar .fa-user-circle {
            margin-right: 10px;
        }

        .card-panel {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .card-panel h5 {
            font-size: 20px;
            font-weight: 500;
            color: #333;
        }

        .table-header {
            background-color: #4CAF50;
            color: white;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a {
            padding: 8px 16px;
            color: #4CAF50;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .pagination li a:hover {
            background-color: #4CAF50;
            color: white;
        }

        .pagination li.active a {
            background-color: white;
            color: black;
            font-weight: bold;
        }

        .btn, .btn-small {
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .btn:hover, .btn-small:hover {
            transform: scale(1.05);
            background-color: #388E3C;
        }

        .card-panel table td, .card-panel table th {
            font-size: 14px;
            color: #555;
        }

        .card-panel table td a {
            transition: background-color 0.3s;
        }

        .card-panel table td a:hover {
            background-color: #FFEB3B;
        }

    </style>
</head>
<body>

    <!-- Topbar -->
    <div class="topbar">
        <div><i class="fa fa-user-circle"></i> <?php echo $user_name; ?> </div>
        <div>ESAPCE ADMIN</div>
        <div>
            <a href="Deconnexion.php"><i class="material-icons">exit_to_app</i></a>
        </div>
    </div>

    <div class="container" style="margin-top: 20px;">
        <div class="row">
            <!-- Gestion des Utilisateurs -->
            <div class="col s12">
                <div class="card-panel white">
                    <div class="row">
                        <div class="col s10">
                            <h5><i class="fa fa-users"></i> Gestion des Utilisateurs</h5>
                        </div>
                        <div class="col s2 right-align">
                            <!-- Bouton Ajouter -->
                            <a href="AjouterUtilisateur.php" class="btn green"><i class="fa fa-user-plus"></i> Ajouter</a>
                        </div>
                    </div>
                    <table class="striped">
                        <thead>
                            <tr class="table-header">
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_users->rowCount() > 0) {
                                while($row = $result_users->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row["nom"]) . "</td>
                                            <td>" . htmlspecialchars($row["prenom"]) . "</td>
                                            <td>" . htmlspecialchars($row["email"]) . "</td>
                                            <td>" . htmlspecialchars($row["role"]) . "</td>
                                            <td >
                                                <a href='ModifierUtilisateur.php?id=" . $row['id'] . "' class='btn-small green'>Modifier</a>
                                                <a href='SupprimerUtilisateur.php?id=" . $row['id'] . "' class='btn-small red' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur ?\")'>Supprimer</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Aucun utilisateur trouvé</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div class="pagination">
                        <?php
                        if ($page_users > 1) {
                            echo "<li><a href='?page_users=" . ($page_users - 1) . "'><i class='fa fa-arrow-left'></i></a></li>";
                        }
                        for ($i = 1; $i <= $total_pages_users; $i++) {
                            echo "<li class='" . ($i == $page_users ? 'active' : '') . "'><a href='?page_users=$i'>$i</a></li>";
                        }
                        if ($page_users < $total_pages_users) {
                            echo "<li><a href='?page_users=" . ($page_users + 1) . "'><i class='fa fa-arrow-right'></i></a></li>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Gestion des Recettes -->
            <div class="col s12">
                <div class="card-panel white">
                    <div class="row">
                        <div class="col s10">
                            <h5><i class="fa fa-utensils"></i> Gestion des Recettes</h5>
                        </div>
                        <div class="col s2 right-align">
                            <a href="Modification1.php" class="btn green"><i class="fa fa-plus"></i> Ajouter</a>
                        </div>
                    </div>
                    <table class="striped">
                        <thead>
                            <tr class="table-header">
                                <th>Titre</th>
                                <th>Auteur</th>
                                <th>J'aime</th>
                                <th>Commentaires</th>
                                <th>Catégorie</th>
                                <th>Date Création</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_recipes->rowCount() > 0) {
                                while($row = $result_recipes->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row["titre"]) . "</td>
                                            <td>" . htmlspecialchars($row["utilisateur_nom"]) . " " . htmlspecialchars($row["utilisateur_prenom"]) . "</td>
                                            <td>" . $row["nb_likes"] . "</td>
                                            <td>" . $row["nb_commentaires"] . "</td>
                                            <td>" . htmlspecialchars($row["categorie"]) . "</td>
                                           <td>" . (isset($row['date_creation']) ? date('d-m-Y', strtotime($row['date_creation'])) : 'N/A') . "</td>
                                            <td>
                                                <a href='Recette1.php?id=" . $row['id'] . "' class='btn-small green'>Voir</a>
                                                <a href='SupprimerRecette1.php?id=" . $row['id'] . "' class='btn-small red' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cette recette ?\")'>Supprimer</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>Aucune recette trouvée</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <!-- Pagination pour les recettes -->
                    <div class="pagination">
                        <?php
                        if ($page_recipes > 1) {
                            echo "<li><a href='?page_recipes=" . ($page_recipes - 1) . "'><i class='fa fa-arrow-left'></i></a></li>";
                        }
                        for ($i = 1; $i <= $total_pages_recipes; $i++) {
                            echo "<li class='" . ($i == $page_recipes ? 'active' : '') . "'><a href='?page_recipes=$i'>$i</a></li>";
                        }
                        if ($page_recipes < $total_pages_recipes) {
                            echo "<li><a href='?page_recipes=" . ($page_recipes + 1) . "'><i class='fa fa-arrow-right'></i></a></li>";
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
