<?php
session_start();
include "db.php";


// =========================
// REGISTER USER
// =========================
if (isset($_POST['register'])) {

    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Check if username exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($check) > 0) {
        header("Location: register.php?error=Username already exists");
        exit();
    }

    // Insert user
    $insert = "INSERT INTO users(fullname, username, email, password)
               VALUES('$fullname', '$username', '$email', '$password')";
    mysqli_query($conn, $insert);

    header("Location: login.php?success=Account created. Please log in.");
    exit();
}



// =========================
// LOGIN USER
// =========================
if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $checkLogin = mysqli_query($conn, 
        "SELECT * FROM users WHERE username='$username' AND password='$password'"
    );

    if (mysqli_num_rows($checkLogin) == 1) {

        $row = mysqli_fetch_assoc($checkLogin);

        // create session
        $_SESSION['username'] = $row['username'];
        $_SESSION['fullname'] = $row['fullname'];

        header("Location: dashboard.php");
        exit();

    } else {

        // If wrong credentials or user not found
        header("Location: register.php?error=Account not found. Please register.");
        exit();
    }
}

?>
