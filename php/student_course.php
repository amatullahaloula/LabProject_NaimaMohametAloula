<?php
session_start();
require_once "./database.php";

// ---------- Check if student is logged in ----------
$student_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$student_id || $role !== 'student') {
    die("Access denied. Please log in as a student.");
}

// ---------- Handle enrollment request ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);

    // Check if student already requested/enrolled
    $check = $conn->prepare("SELECT * FROM student_course WHERE student_id = ? AND course_id = ?");
    $check->bind_param("ii", $student_id, $course_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "You have already requested or enrolled in this course.";
    } else {
        // Insert enrollment request
        $stmt = $conn->prepare("INSERT INTO student_course (student_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $course_id);

        if ($stmt->execute()) {
            $message = "Request sent successfully.";
        } else {
            $message = "Failed to send request: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }

    $check->close();
}

// ---------- Fetch all courses ----------
$course_list = $conn->query("SELECT c.*, f.full_name AS faculty_name 
                             FROM course c 
                             JOIN faculty f ON c.faculty_id = f.faculty_id
                             ORDER BY c.course_id DESC");

// ---------- Include HTML template ----------
ob_start();
require_once "../html/student_course.html"; // Contains the table with <tbody id="course-table-body"></tbody>
$page = ob_get_clean();

// ---------- Build Table Rows ----------
$tableRows = "";
while ($row = $course_list->fetch_assoc()) {
    $tableRows .= "
        <tr>
            <td>{$row['course_code']}</td>
            <td>{$row['course_title']}</td>
            <td>{$row['credits']}</td>
            <td>{$row['description']}</td>
            <td>{$row['faculty_name']}</td>
            <td>
                <form method='POST' style='margin:0;'>
                    <input type='hidden' name='course_id' value='{$row['course_id']}'>
                    <button type='submit'>Request Enrollment</button>
                </form>
            </td>
        </tr>
    ";
}

// ---------- Inject table rows ----------
$page = str_replace(
    '<tbody id="course-table-body"></tbody>',
    '<tbody id="course-table-body">'.$tableRows.'</tbody>',
    $page
);

// ---------- Show optional message ----------
if (isset($message)) {
    $page = str_replace('<!--MESSAGE-->', "<p style='color:green;'>$message</p>", $page);
} else {
    $page = str_replace('<!--MESSAGE-->', '', $page);
}

// ---------- Output the page ----------
echo $page;
?>
