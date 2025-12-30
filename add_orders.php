<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'foodie_order';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $input = json_decode(file_get_contents("php://input"), true);

    $user_id = intval($input["user_id"] ?? 0);
    $commandes = $input["commandes"] ?? [];
    $status = $input["status"] ?? "pending";

    if (!$user_id || empty($commandes)) {
        echo json_encode(["success" => false, "message" => "user_id ou commandes manquants"]);
        exit;
    }

    $conn->beginTransaction();

    foreach ($commandes as $commande) {
        $meal_id = intval($commande["meal_id"] ?? 0);
        $quantity = intval($commande["quantity"] ?? 1);

        if (!$meal_id) {
            $conn->rollBack();
            echo json_encode(["success" => false, "message" => "meal_id manquant dans une commande"]);
            exit;
        }

        // 🔎 Vérifier si le meal existe
        $stmtMeal = $conn->prepare("SELECT price FROM meals WHERE id = ?");
        $stmtMeal->execute([$meal_id]);

        if ($stmtMeal->rowCount() == 0) {
            $conn->rollBack();
            echo json_encode(["success" => false, "message" => "Repas introuvable: $meal_id"]);
            exit;
        }

        $meal = $stmtMeal->fetch(PDO::FETCH_ASSOC);
        $price = floatval($meal["price"]);

        // 💰 Calcul du total
        $total_price = $price * $quantity;

        // ➕ Ajouter la commande
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, meal_id, quantity, total_price, status)
            VALUES (?, ?, ?, ?, ?)
        ");

        if (!$stmt->execute([$user_id, $meal_id, $quantity, $total_price, $status])) {
            $conn->rollBack();
            echo json_encode([
                "success" => false,
                "message" => "Erreur lors de l'ajout de la commande pour le repas $meal_id"
            ]);
            exit;
        }
    }

    $conn->commit();
    echo json_encode([
        "success" => true,
        "message" => "Commandes ajoutées avec succès"
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}