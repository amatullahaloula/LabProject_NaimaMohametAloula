<?php
session_start();
require_once "database.php"; // Assurez-vous que le chemin est correct

// 1. Autorisation : Seulement Faculty/Interns peuvent créer un cours
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['faculty', 'intern'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé.']);
    exit;
}

header('Content-Type: application/json');

// Récupérer les données JSON de la requête Fetch
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST" && $data) {
    $course_code = trim($data["code"]);
    $course_name = trim($data["name"]);
    $description = trim($data["description"]);
    
    // NOTE: Si vous utilisez la table 'users', vous devrez lier user_id au faculty_id.
    // Pour l'exemple, nous allons chercher le faculty_id dans la table 'faculty'
    $faculty_user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

    try {
        // Logique pour trouver le faculty_id (dépend de votre base de données)
        // Si la table 'users' contient directement le 'faculty_id', utilisez-le.
        // Sinon, vous devez trouver l'ID dans la table 'faculty' correspondant à l'utilisateur connecté.
        
        // ** (ADAPTEZ CETTE LIGNE)**: Utilisez l'ID de la session pour l'insertion
        $faculty_id_creator = $faculty_user_id; 

        $sql = "INSERT INTO courses (course_code, course_name, description, faculty_id) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $course_code, $course_name, $description, $faculty_id_creator);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cours créé avec succès!']);
        } else {
            // Gérer les erreurs de la BD (ex: code de cours déjà existant)
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du cours: ' . $conn->error]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête invalide ou données manquantes.']);
}
?>