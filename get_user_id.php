<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Gérer la requête OPTIONS (pré-vol CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Connexion à la base
$host = "localhost";
$dbname = "foodie_order";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lire JSON envoyé par Flutter
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input["username"])) {
        echo json_encode([
            "success" => false,
            "message" => "username manquant"
        ]);
        exit;
    }

    $usernameInput = trim($input["username"]);

    // Vérifier si l'utilisateur existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$usernameInput]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "user_id" => $user["id"]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Utilisateur introuvable"
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erreur serveur : " . $e->getMessage()
    ]);
}
?>