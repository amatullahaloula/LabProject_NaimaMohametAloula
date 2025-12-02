<?php
session_start();
require_once "./database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if (empty($email) || empty($password)) {
        die("All fields are required.");
    }

    $userFound = false;

    // ---------- CHECK STUDENT ----------
    $stmt = $conn->prepare("SELECT student_id AS id, full_name, email, password_hash FROM student WHERE email=? LIMIT 1");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($student && password_verify($password, $student['password_hash'])) {
        $_SESSION['user_id'] = $student['id'];
        $_SESSION['full_name'] = $student['full_name'];
        $_SESSION['email'] = $student['email'];
        $_SESSION['role'] = 'student';
        header("Location: ../html/student_dashboard.html");
        exit;
    }

    // ---------- CHECK FACULTY ----------
    $stmt = $conn->prepare("SELECT faculty_id AS id, full_name, email, password_hash FROM faculty WHERE email=? LIMIT 1");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $faculty = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($faculty && password_verify($password, $faculty['password_hash'])) {
        $_SESSION['user_id'] = $faculty['id'];
        $_SESSION['full_name'] = $faculty['full_name'];
        $_SESSION['email'] = $faculty['email'];
        $_SESSION['role'] = 'faculty';
        header("Location: ../html/faculty_dashboard.html");
        exit;
    }

    // ---------- CHECK FACULTY INTERN ----------
    $stmt = $conn->prepare("SELECT intern_id AS id, full_name, email, password_hash FROM faculty_intern WHERE email=? LIMIT 1");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $intern = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($intern && password_verify($password, $intern['password_hash'])) {
        $_SESSION['user_id'] = $intern['id'];
        $_SESSION['full_name'] = $intern['full_name'];
        $_SESSION['email'] = $intern['email'];
        $_SESSION['role'] = 'intern';
        header("Location: ../html/intern_dashboard.html");
        exit;
    }

    // If none matched
    echo "Invalid email or password.";
    exit;
}

// Include login form only if not POST
include '../html/login.html';
?>
