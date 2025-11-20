<?php
session_start();
require_once "./database.php";

// ---------- Check if faculty is logged in ----------
$faculty_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if (!$faculty_id || $role !== 'faculty') {
    die("Access denied. Please log in as faculty.");
}

// ---------- Fetch courses for this faculty ----------
$course_list = $conn->query("SELECT * FROM course WHERE faculty_id = $faculty_id ORDER BY course_id DESC");

// ---------- Include HTML template ----------
ob_start();
require_once "../html/faculty_courses.html"; // Table only
$page = ob_get_clean();

// ---------- Build Table Rows ----------
$tableRows = "";
while ($row = $course_list->fetch_assoc()) {
    $tableRows .= "
        <tr>
            <td>{$row['course_id']}</td>
            <td>{$row['course_code']}</td>
            <td>{$row['course_title']}</td>
            <td>{$row['credits']}</td>
            <td>{$row['description']}</td>
            <td>
                <a class='action-btn btn-edit' href='edit_course.php?id={$row['course_id']}'>Edit</a>
                <a class='action-btn btn-delete' href='delete_course.php?id={$row['course_id']}'
                   onclick='return confirm(\"Delete this course?\")'>Delete</a>
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

// ---------- Output the page ----------
echo $page;
