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

// Gérer la requête OPTIONS (pré-vol CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si c'est une requête avec fichier (multipart/form-data)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
        // Récupérer les données du formulaire
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        // Vérifier si une nouvelle image est uploadée
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            // Récupérer l'ancien chemin de l'image pour la supprimer plus tard
            $stmt = $conn->prepare("SELECT image FROM meals WHERE id = ?");
            $stmt->execute([$id]);
            $oldMeal = $stmt->fetch(PDO::FETCH_ASSOC);
            $oldImagePath = $oldMeal['image'] ?? '';

            // Préparer le nouvel upload d'image
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Générer un nom unique pour la nouvelle image
            $tmp_name = $_FILES['image']['tmp_name'];
            $image_name = uniqid() . "_" . basename($_FILES['image']['name']);
            $image_path = $upload_dir . $image_name;

            // Déplacer la nouvelle image
            if (move_uploaded_file($tmp_name, $image_path)) {
                // Supprimer l'ancienne image si elle existe
                if (!empty($oldImagePath) && file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                
                // Mettre à jour avec la nouvelle image
                $stmt = $conn->prepare("UPDATE meals SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
                if ($stmt->execute([$name, $description, $price, $image_path, $id])) {
                    echo json_encode(['success' => true, 'message' => 'Repas modifié avec succès']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement de l\'image']);
            }
        } else {
            // Mettre à jour sans changer l'image
            $stmt = $conn->prepare("UPDATE meals SET name = ?, description = ?, price = ? WHERE id = ?");
            if ($stmt->execute([$name, $description, $price, $id])) {
                echo json_encode(['success' => true, 'message' => 'Repas modifié avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
?>