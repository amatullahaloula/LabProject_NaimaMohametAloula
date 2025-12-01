<?php
session_start();
// Initialise les variables pour les messages d'état
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';

// Nettoyer les variables de session après les avoir récupérées pour ne pas les réafficher
unset($_SESSION['error']);
unset($_SESSION['success']);
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Your Project</title>
    
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>

    <header>
        <h1>Authentication Portal</h1>
        <p>Register a new account to access the system.</p>
    </header>

    <main id="login">
        <form action="signup.php" method="POST"> 
            <h2>Sign Up</h2>

            <?php if ($error): ?>
                <div style="color: red; font-weight: bold; margin-bottom: 15px; border: 1px solid red; padding: 10px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="color: green; font-weight: bold; margin-bottom: 15px; border: 1px solid green; padding: 10px;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = "login.php"; // Rediriger vers la page de connexion PHP
                    }, 3000); // 3 secondes
                </script>
            <?php endif; ?>
            <div>
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
            </div>

            <div>
                <label for="email">Ashesi Email:</label>
                <input type="email" id="email" name="email" placeholder="name@ashesi.edu.gh" required >
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required >
            </div>

            <div>
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter password" required >
            </div>
            
            <div class="form">
                <label for="role">I am a:</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Select Role</option> 
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                    <option value="intern">Faculty Intern</option>
                </select>
            </div>

            <button type="submit">Register Account</button> 
            
        </form>

        <p class="switch-view">
            Already have an account? 
            <a href="login.php">Log In</a>. 
        </p>
    </main>

    <footer>
        <p>&copy; 2025 Your Project. All rights reserved.</p>
    </footer>

</body>
</html>