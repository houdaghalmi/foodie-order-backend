<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');
include "db_connect.php";

// Gérer la requête OPTIONS (pré-vol CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Données JSON invalides"]);
    exit;
}

$order_id = $data['order_id'] ?? null;
$tel = $data['tel'] ?? null;
$adresse = $data['adresse'] ?? null;

if (!$order_id || !$tel || !$adresse) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

// Validation du téléphone
if (!preg_match('/^[0-9\s\-\+\(\)]{8,15}$/', $tel)) {
    echo json_encode(["success" => false, "message" => "Numéro de téléphone invalide"]);
    exit;
}

// Validation de l'adresse
if (strlen($adresse) < 5) {
    echo json_encode(["success" => false, "message" => "Adresse trop courte"]);
    exit;
}

try {
    // Mettre à jour l'adresse dans la commande
    $stmt = $conn->prepare("UPDATE orders SET adresse = ? WHERE id = ?");
    $stmt->execute([$adresse, $order_id]);
    
       // Mettre à jour tel dans la commande
       $stmt = $conn->prepare("UPDATE orders SET tel = ? WHERE id = ?");
       $stmt->execute([$tel, $order_id]);


    echo json_encode([
        "success" => true, 
        "message" => "Informations mises à jour avec succès"
    ]);
} catch (Exception $e) {
    error_log("Erreur update_order_address.php: " . $e->getMessage());
    echo json_encode([
        "success" => false, 
        "message" => "Erreur serveur: " . $e->getMessage()
    ]);
}
?>