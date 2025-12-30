<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');
include "db_connect.php";

// Gérer la requête OPTIONS (pré-vol CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Vérifier si on filtre par user_id
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $user_id = $data['user_id'] ?? null;

    if ($user_id) {
        // Récupérer les commandes pour un utilisateur spécifique
        $stmt = $conn->prepare("
            SELECT o.*, m.name as meal_name 
            FROM orders o 
            LEFT JOIN meals m ON o.meal_id = m.id 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id]);
    } else {
        // Récupérer toutes les commandes
        $stmt = $conn->prepare("
            SELECT o.*, m.name as meal_name 
            FROM orders o 
            LEFT JOIN meals m ON o.meal_id = m.id 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute();
    }

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "orders" => $orders
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur: " . $e->getMessage()
    ]);
}
?>