<?php
// faculty_dashboard.php

// 1. Démarrer la session
session_start();

// 2. VÉRIFICATION D'AUTORISATION (Empêche l'accès si l'utilisateur n'est PAS un 'faculty')
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    // Rediriger vers la page de connexion
    header("Location: ../html/index.html"); 
    exit(); 
}

// L'utilisateur est autorisé. 
// Récupérer le nom complet à partir de la session pour l'affichage
$full_name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <header class="Faculty-dashboard">
        <h1>Faculty Dashboard</h1>
        <p>Bienvenue, Professeur <?php echo $full_name; ?>!</p>
        <a href="logout.php" class="logout-btn">Log Out</a>
    </header>

    <div class="dashboard-layout">
        <nav class="dashboard-nav">
            <ul>
                <li><a href="faculty_courses.php">My Courses</a></li>
                <li><a href="faculty_course_management.php">Course Management</a></li>
                <li><a href="#">Performance Reports</a></li>
                <li><a href="#">Administrative Tasks</a></li>
            </ul>
        </nav>

        <main class="dashboard">
            <div class="top">
                <section id="my-courses">
                    <h2>My Courses</h2>
                    <p>Overview of courses you are currently teaching and upcoming schedules.</p>
                    <div>
                        <p>A table showing course names, student count, and assigned interns will be displayed here.</p>
                    </div>
                </section>
                <hr>
                <section id="course-management">
                    <h2>Course Management</h2>
                    <p>Tools for managing grades, attendance, and course materials.</p>
                </section>
            </div>

            <div class="bottom">
                <section id="reports">
                    <h2>Session Overview</h2>
                    <p>View student feedback, department reviews, and performance metrics.</p>
                </section>

                <hr>

                <section id="administrative-tasks">
                    <h2>Attendance Report</h2>
                    <p>Forms and tools for student partification and approval of notice of absence.</p>
                </section>
            </div>


        </main>
    </div>

    <footer>
        <p>&copy; 2025 Lab Project. Faculty Access.</p>
    </footer>

</body>

</html>