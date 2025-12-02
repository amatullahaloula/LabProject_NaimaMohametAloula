<?php
session_start();
require_once "./database.php";

// Only show form when not POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    include "../html/register.html";
    exit();
}

// Get input
$fullName = trim($_POST['fullName'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$role = trim($_POST['role'] ?? '');

if ($fullName === '' || $email === '' || $role === '' || $password === '' || $confirmPassword === '') {
    exit("All fields are required.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Invalid email address.");
}

if ($password !== $confirmPassword) {
    exit("Passwords do not match.");
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert based on role
if ($role === 'student') {
    $stmt = $conn->prepare("INSERT INTO student (full_name, email, password_hash) VALUES (?, ?, ?)");
} elseif ($role === 'faculty') {
    $stmt = $conn->prepare("INSERT INTO faculty (full_name, email, password_hash, hire_date) VALUES (?, ?, ?, CURRENT_DATE)");
} elseif ($role === 'intern') {
    $stmt = $conn->prepare("INSERT INTO faculty_intern (full_name, email, password_hash, start_date) VALUES (?, ?, ?, CURRENT_DATE)");
} else {
    exit("Invalid role selected.");
}

if (!$stmt) die("Prepare failed: " . $conn->error);

// Bind parameters
$stmt->bind_param("sss", $fullName, $email, $password_hash);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: ../php/login.php");
    exit();
} else {
    $stmt->close();
    exit("Registration failed: " . htmlspecialchars($stmt->error));
}
?>
