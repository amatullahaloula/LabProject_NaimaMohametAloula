<?php
// signup.php
session_start();
require_once "./database.php";

// Only load the HTML form when not submitting
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    require_once "../html/register.html";
    exit();
}

// -----------------------------
// Get and validate inputs
// -----------------------------
$fullName = trim($_POST['fullName'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';
$role = trim($_POST['role'] ?? '');

if ($fullName === '' || $email === '' || $role === '') {
    exit("All required fields must be filled.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Invalid email address.");
}

if ($password === '' || $confirmPassword === '') {
    exit("Password and confirmation are required.");
}

if ($password !== $confirmPassword) {
    exit("Passwords do not match.");
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// -----------------------------
// INSERT BASED ON ROLE
// -----------------------------

if ($role === 'student') {

    $sql = "INSERT INTO student (full_name, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $fullName, $email, $password_hash);

    if ($stmt->execute()) {
        header("Location: ../php/login.php");
        exit();
    } else {
        exit("Registration failed: " . htmlspecialchars($stmt->error));
    }

} elseif ($role === 'faculty') {

    $sql = "INSERT INTO faculty (full_name, email, password_hash, hire_date)
            VALUES (?, ?, ?, CURRENT_DATE)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $fullName, $email, $password_hash);

    if ($stmt->execute()) {
        header("Location: ../php/login.php");
        exit();
    } else {
        exit("Registration failed: " . htmlspecialchars($stmt->error));
    }

} elseif ($role === 'intern') {

    $sql = "INSERT INTO faculty_intern (full_name, email, password_hash, start_date)
            VALUES (?, ?, ?, CURRENT_DATE)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $fullName, $email, $password_hash);

    if ($stmt->execute()) {
        header("Location: ../php/login.php");
        exit();
    } else {
        exit("Registration failed: " . htmlspecialchars($stmt->error));
    }
}

?>
