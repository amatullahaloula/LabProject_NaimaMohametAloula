<?php
session_start();
require_once "database.php"; 

// 1. Autorisation : Seulement les Students peuvent faire une demande
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès réservé aux étudiants.']);
    exit;
}

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data["course_id"])) {
    $course_id = (int)$data["course_id"];
    $student_id = $_SESSION['user_id']; 

    try {
        // Vérification 1: L'étudiant n'est-il pas déjà inscrit/en attente?
        $check_sql = "SELECT status FROM course_requests WHERE course_id = ? AND student_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $course_id, $student_id);
        $check_stmt->execute();
        $existing_request = $check_stmt->get_result()->fetch_assoc();

        if ($existing_request) {
            if ($existing_request['status'] === 'approved') {
                echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à ce cours.']);
            } elseif ($existing_request['status'] === 'pending') {
                echo json_encode(['success' => false, 'message' => 'Votre demande est déjà en attente.']);
            }
            exit;
        }

        // Insertion de la nouvelle demande (statut par défaut: 'pending')
        $insert_sql = "INSERT INTO course_requests (course_id, student_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $course_id, $student_id);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Demande d\'inscription envoyée avec succès!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi de la demande.']);
        }
        
    } catch (Exception $e) {
        // Gérer les erreurs de clé unique si l'étudiant a déjà une demande (sécurité supplémentaire)
        if (strpos($e->getMessage(), 'unique_request') !== false) {
             echo json_encode(['success' => false, 'message' => 'Vous avez déjà fait une demande pour ce cours.']);
        } else {
             http_response_code(500);
             echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de cours manquant.']);
}
?>