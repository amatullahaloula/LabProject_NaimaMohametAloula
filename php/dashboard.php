<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<h1>Welcome, <?php echo $_SESSION['fullname']; ?>!</h1>
<p>You are logged in successfully.</p>