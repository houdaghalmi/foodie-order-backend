<?php
header("Content-Type: application/json");
// Autoriser CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gérer la requête OPTIONS (pré-vol CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'foodie_order';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer tous les repas
    $stmt = $conn->prepare("SELECT id, name, description, price, image ,created_at FROM meals ORDER BY id DESC");
    $stmt->execute();
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($meals) {
        echo json_encode([
            'success' => true,
            'meals' => $meals
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'meals' => [],
            'message' => 'Aucun repas trouvé'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()
    ]);
}
?>