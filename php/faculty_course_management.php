<?php
session_start();
require_once "./database.php";
require_once "../html/faculty_course_management.html";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $msg = "";
    $course_code = trim($_POST["course_code"]);
    $course_title = trim($_POST["course_title"]);
    $credits = trim($_POST["credits"]);
    $description = trim($_POST["description"]);
    $faculty_id = $_SESSION['user_id'];

    if (!empty($course_code) && !empty($course_title) && !empty($credits) && !empty($description)) {

        $sql = "INSERT INTO course (course_code, course_title, description , credits, faculty_id)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $course_code, $course_title, $description, $credits, $faculty_id);

        if ($stmt->execute()) {
            $msg = "Course added successfully!";
        } else {
            $msg = "Error adding course.";
        }
    } else {
        $msg = "All fields are required.";
    }
    echo "<script>alert('".$msg."'); window.location.href = '../php/faculty_course_management.php';</script>";
}


?>