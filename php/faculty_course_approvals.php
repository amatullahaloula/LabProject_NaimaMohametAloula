<?php
session_start();
require_once "./database.php";

// ---------- Check if faculty is logged in ----------
$faculty_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$faculty_id || $role !== 'faculty') {
    die("Access denied. Please log in as faculty.");
}

// ---------- Handle AJAX Approve/Reject ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_course_id'], $_POST['action'])) {
    $student_course_id = intval($_POST['student_course_id']);
    $action = $_POST['action'];

    if ($student_course_id <= 0 || !in_array($action, ['approve', 'reject'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        exit;
    }

    $status = $action === 'approve' ? 'Approved' : 'Rejected';

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
}

// ---------- Fetch student requests for this faculty ----------
$sql = "
    SELECT 
        sc.student_course_id,
        sc.status,
        s.full_name AS student_name,
        s.email AS student_email,
        c.course_code,
        c.course_title
    FROM student_course sc
    JOIN student s ON sc.student_id = s.student_id
    JOIN course c ON sc.course_id = c.course_id
    WHERE c.faculty_id = ?
    ORDER BY sc.student_course_id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Requests</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        button { margin-right: 5px; padding: 5px 10px; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
<h2>Student Enrollment Requests</h2>
<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Email</th>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="request-table-body">
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr id="row-<?= $row['student_course_id'] ?>">
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['student_email']) ?></td>
                <td><?= htmlspecialchars($row['course_code']) ?></td>
                <td><?= htmlspecialchars($row['course_title']) ?></td>
                <td class="status"><?= $row['status'] ?></td>
                <td>
                    <?php if ($row['status'] === 'Pending'): ?>
                        <button class="btn-action" data-id="<?= $row['student_course_id'] ?>" data-action="approve">Approve</button>
                        <button class="btn-action" data-id="<?= $row['student_course_id'] ?>" data-action="reject">Reject</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
// ---------- AJAX for Approve/Reject ----------
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".btn-action").forEach(btn => {
        btn.addEventListener("click", function() {
            const student_course_id = this.dataset.id;
            const action = this.dataset.action;

            fetch("", { // Send POST to the same file
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: `student_course_id=${student_course_id}&action=${action}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById(`row-${student_course_id}`);
                    row.querySelector(".status").textContent = data.status;
                    row.querySelectorAll(".btn-action").forEach(b => b.style.display = "none");
                } else {
                    alert("Error: " + (data.error || "Unknown error"));
                }
            })
            .catch(err => alert("Request failed: " + err));
        });
    });
});
</script>
</body>
</html>
