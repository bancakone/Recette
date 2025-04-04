<?php
require('libs/fpdf/fpdf.php'); // Assure-toi du bon chemin

// Connexion à la base de données
$bdd = new PDO('mysql:host=localhost;dbname=recette;charset=utf8', 'root', '');

// Récupération de la recette par ID
if (!isset($_GET['id'])) {
    die("Recette introuvable");
}

$id = intval($_GET['id']);
$stmt = $bdd->prepare("SELECT r.*, u.nom AS auteur FROM recettes r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
$stmt->execute([$id]);
$recette = $stmt->fetch();

if (!$recette) {
    die("Recette non trouvée");
}

// Afficher les données de la recette
echo '<pre>';
var_dump($recette); // Affiche les données récupérées
echo '</pre>';
// Commence la mise en tampon de sortie pour éviter toute sortie avant le PDF
ob_start();

class PDF extends FPDF {
    function Header() {
        // Titre du PDF
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Fiche Recette', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        // Numéro de page
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

// Création du PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Titre
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode($recette['titre']), 0, 1);
$pdf->Ln(3);

// Image (si elle existe et que le chemin est valide)
$imagePath = 'images_recettes/' . $recette['photo'];
if (!empty($recette['photo']) && file_exists($imagePath)) {
    $pdf->Image($imagePath, $pdf->GetX(), $pdf->GetY(), 100);
    $pdf->Ln(60);
}

// Description
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Description', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, utf8_decode($recette['description']));
$pdf->Ln(5);

// Ingrédients
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Ingrédients'), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, utf8_decode($recette['ingredients']));
$pdf->Ln(5);

// Méthode
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Méthode de préparation'), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, utf8_decode($recette['methodes']));
$pdf->Ln(5);

// Infos supplémentaires
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, utf8_decode("Auteur : " . $recette['auteur'] . " | Créée le : " . date('d/m/Y', strtotime($recette['date_creation']))), 0, 1);

// Génération du PDF
$pdf->Output('D', 'recette_' . $recette['id'] . '.pdf');

// Vide le tampon et évite toute sortie avant le PDF
ob_end_clean();
exit;
?>
