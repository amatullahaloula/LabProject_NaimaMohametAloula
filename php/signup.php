<?php
session_start();
require_once "./database.php";

// Only load form when not POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    include "../html/register.html";
    exit();
}

// Get inputs
$fullName = trim($_POST['fullName'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$role = trim($_POST['role'] ?? '');

// Validation
if ($fullName === '' || $email === '' || $role === '' || $password === '' || $confirmPassword === '') {
    exit("All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Invalid email address.");
}

if ($password !== $confirmPassword) {
    exit("Passwords do not match.");
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert into DB based on role
switch ($role) {
    case 'student':
        $stmt = $conn->prepare("INSERT INTO student (full_name, email, password_hash) VALUES (?, ?, ?)");
        break;
    case 'faculty':
        $stmt = $conn->prepare("INSERT INTO faculty (full_name, email, password_hash, hire_date) VALUES (?, ?, ?, CURRENT_DATE)");
        break;
    case 'intern':
        $stmt = $conn->prepare("INSERT INTO faculty_intern (full_name, email, password_hash, start_date) VALUES (?, ?, ?, CURRENT_DATE)");
        break;
    default:
        exit("Invalid role selected.");
}

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("sss", $fullName, $email, $password_hash);

// Execute
if ($stmt->execute()) {
    $stmt->close();
    // Redirect to login after successful registration
    header("Location: ../php/login.php");
    exit();
} else {
    $stmt->close();
    exit("Registration failed: " . htmlspecialchars($stmt->error));
}
?>
