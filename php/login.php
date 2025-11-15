<?php include '../html/login.html'; ?>

<?php
session_start();
require_once "./database.php"; // include your DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Check if user exists
    $sql = "SELECT full_name, email, password_hash FROM student WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Validate password
        if (password_verify($password, $user["password_hash"])) {

            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["email"] = $user["email"];

            header("Location: ../html/student_dashboard.html");
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>