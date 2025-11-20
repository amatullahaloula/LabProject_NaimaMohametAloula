<?php
// signup.php
require_once "./database.php";
require_once "../html/register.html";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Method Not Allowed");
}

// Get and trim inputs
$fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

if ($fullName === '' || $email === '' || $role === '') {
    exit("All required fields must be filled.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Invalid email address.");
}

// Default supervisor ID for interns
$defaultSupervisorID = 1;

// ----------------------------------------------------
// PASSWORD VALIDATION FOR ALL ROLES
// ----------------------------------------------------
if ($password === '' || $confirmPassword === '') {
    exit("Password and confirmation are required.");
}

if ($password !== $confirmPassword) {
    exit("Passwords do not match.");
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// ----------------------------------------------------
// ROLE-BASED INSERTION
// ----------------------------------------------------

if ($role === 'student') {

    $sql = "INSERT INTO student (full_name, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { exit("Prepare failed: " . $conn->error); }

    $stmt->bind_param("sss", $fullName, $email, $password_hash);

    if ($stmt->execute()) {
        echo "<script>alert('".$fullName." registered successfully as student.');</script>";
        header("Location: ../php/login.php");
        exit();
    } else {
        echo "Registration failed: " . htmlspecialchars($stmt->error);
    }

} elseif ($role === 'faculty') {

    $sql = "INSERT INTO faculty (full_name, email, password_hash, hire_date)
            VALUES (?, ?, ?, CURRENT_DATE)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) { exit("Prepare failed: " . $conn->error); }

    $stmt->bind_param("sss", $fullName, $email, $password_hash);

    if ($stmt->execute()) {
        echo "Faculty registered successfully.";
    } else {
        echo "Registration failed: " . htmlspecialchars($stmt->error);
    }

} elseif ($role === 'intern') {

    $sql = "INSERT INTO faculty_intern (full_name, email, password_hash, supervisor_id, start_date)
            VALUES (?, ?, ?, ?, CURRENT_DATE)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) { exit("Prepare failed: " . $conn->error); }

    $stmt->bind_param("sssi", $fullName, $email, $password_hash, $defaultSupervisorID);

    if ($stmt->execute()) {
        echo "Faculty intern registered successfully. Supervisor assigned automatically.";
    } else {
        echo "Registration failed: " . htmlspecialchars($stmt->error);
    }

} else {
    exit("Invalid role selected.");
}

$stmt->close();
$conn->close();
?>
