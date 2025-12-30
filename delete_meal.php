<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Configuration base de données
$host = 'localhost';
$dbname = 'foodie_order';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lire données JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? '';

    $id = (int) $id;

    // Avant suppression, récupérer le chemin de l'image pour suppression fichier
    $stmtSelect = $conn->prepare("SELECT image FROM meals WHERE id = ?");
    $stmtSelect->execute([$id]);
    $meal = $stmtSelect->fetch(PDO::FETCH_ASSOC);

    if (!$meal) {
        echo json_encode(['success' => false, 'message' => 'Repas non trouvé']);
        exit;
    }

    // Supprimer le repas
    $stmtDelete = $conn->prepare("DELETE FROM meals WHERE id = ?");
    if ($stmtDelete->execute([$id])) {
        // Supprimer le fichier image si existe
        if (!empty($meal['image']) && file_exists($meal['image'])) {
            unlink($meal['image']);
        }
        echo json_encode(['success' => true, 'message' => 'Repas supprimé avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du repas']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()]);
}
