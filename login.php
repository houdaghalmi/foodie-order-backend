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
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Lire input JSON
    $input = json_decode(file_get_contents("php://input"), true);

    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    // Vérifier si email existe
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() == 0) {
        echo json_encode(['success' => false, 'message' => 'Email incorrect']);
        exit;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier le mot de passe hashé
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect']);
        exit;
    }

    // Login OK
    echo json_encode([
        'success' => true,
        'message' => 'Connexion réussie',
        'user_id' => $user['id'],
        'email' => $user['email'],
        'username' => $user['username'],
        'role' => $user['role']
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données']);
}
