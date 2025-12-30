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

$id = $data['id'] ?? null;
$quantity = $data['quantity'] ?? null;
$total_price = $data['total_price'] ?? null;
$status = $data['status'] ?? null;
$tel = $data['tel'] ?? null;
$adresse = $data['adresse'] ?? null;

if (!$id || !$quantity || !$total_price || !$status) {
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

// Validation du téléphone si fourni
if ($tel !== null && $tel !== '') {
    if (!preg_match('/^[0-9\s\-\+\(\)]{8,15}$/', $tel)) {
        echo json_encode(["success" => false, "message" => "Numéro de téléphone invalide"]);
        exit;
    }
}

// Validation de l'adresse si fournie
if ($adresse !== null && $adresse !== '') {
    if (strlen($adresse) < 5) {
        echo json_encode(["success" => false, "message" => "Adresse trop courte (minimum 5 caractères)"]);
        exit;
    }
}

try {
    if ($tel !== null && $adresse !== null) {
        // Mettre à jour avec téléphone et adresse
        $stmt = $conn->prepare("UPDATE orders SET quantity = ?, total_price = ?, status = ?, tel = ?, adresse = ? WHERE id = ?");
        $stmt->execute([$quantity, $total_price, $status, $tel, $adresse, $id]);
    } elseif ($tel !== null) {
        // Mettre à jour avec téléphone seulement
        $stmt = $conn->prepare("UPDATE orders SET quantity = ?, total_price = ?, status = ?, tel = ? WHERE id = ?");
        $stmt->execute([$quantity, $total_price, $status, $tel, $id]);
    } elseif ($adresse !== null) {
        // Mettre à jour avec adresse seulement
        $stmt = $conn->prepare("UPDATE orders SET quantity = ?, total_price = ?, status = ?, adresse = ? WHERE id = ?");
        $stmt->execute([$quantity, $total_price, $status, $adresse, $id]);
    } else {
        // Mettre à jour sans téléphone ni adresse
        $stmt = $conn->prepare("UPDATE orders SET quantity = ?, total_price = ?, status = ? WHERE id = ?");
        $stmt->execute([$quantity, $total_price, $status, $id]);
    }

    echo json_encode(["success" => true, "message" => "Commande mise à jour"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>