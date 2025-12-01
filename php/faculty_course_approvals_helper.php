<?php
session_start();
require_once "./database.php";

// ---------- Only allow logged-in faculty ----------
$faculty_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$faculty_id || $role !== 'faculty') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// ---------- Only allow POST requests ----------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

// ---------- Get POST parameters ----------
$student_course_id = intval($_POST['student_course_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($student_course_id <= 0 || !in_array($action, ['approve', 'reject'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

// ---------- Determine new status ----------
$status = $action === 'approve' ? 'Approved' : 'Rejected';

// ---------- Update the request, only if it belongs to this faculty ----------
$stmt = $conn->prepare("
    UPDATE student_course sc
    JOIN course c ON sc.course_id = c.course_id
    SET sc.status = ?
    WHERE sc.student_course_id = ? AND c.faculty_id = ?
");
$stmt->bind_param("sii", $status, $student_course_id, $faculty_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'status' => $status]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
exit;
?>