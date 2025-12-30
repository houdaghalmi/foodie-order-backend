<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

// Configuration de la BDD
$host = 'localhost';
$dbname = 'foodie_order';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier les champs requis
    if (!isset($_POST['name'], $_POST['description'], $_POST['price'])) {
        echo json_encode(['success' => false, 'message' => 'Missing meal fields']);
        exit;
    }

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Vérifier l'image
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Image missing or upload error']);
        exit;
    }

    // Répertoire upload
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Nom unique
    $tmp_name = $_FILES['image']['tmp_name'];
    $image_name = uniqid() . "_" . basename($_FILES['image']['name']);
    $image_path = $upload_dir . $image_name;

    // Déplacer l'image
    if (!move_uploaded_file($tmp_name, $image_path)) {
        echo json_encode(['success' => false, 'message' => 'Error saving image']);
        exit;
    }

    // INSÉRTION dans la BDD
    $stmt = $conn->prepare("
        INSERT INTO meals (name, description, price, image)
        VALUES (?, ?, ?, ?)
    ");

    if ($stmt->execute([$name, $description, $price, $image_path])) {
        echo json_encode(['success' => true, 'message' => 'Meal added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database insert error']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
