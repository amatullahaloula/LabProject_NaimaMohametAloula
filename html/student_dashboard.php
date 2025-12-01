<?php
// student_dashboard.php

// 1. Démarrer la session pour accéder aux données
session_start();

// 2. VÉRIFICATION D'AUTORISATION (Empêche l'accès si l'utilisateur n'est PAS un 'student')
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    // Rediriger vers la page de connexion (index.html ou login.html)
    header("Location: student_dashboard.php"); 
    exit(); 
}

// L'utilisateur est autorisé. Le code HTML commence ici.
// Récupérer le nom complet à partir de la session pour l'affichage
$full_name = htmlspecialchars($_SESSION['full_name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <header class="faculty_dashboard">
        <h1>Student Dashboard</h1>
        <p>Bienvenue, <?php echo $full_name; ?>!</p> 
        <a href="logout.php" class="logout-btn">Log Out</a>
    </header>

    <div class="dashboard-layout">
        <nav class="dashboard-nav">
            <ul>
                <li><a href="../php/student_course.php">My Courses</a></li>
                <li><a href="#grades-and-progress">Grades & Progress</a></li>
                <li><a href="#schedule">Class Schedule</a></li>
                <li><a href="#resources">Academic Resources</a></li>
            </ul>
        </nav>

        <main class="dashboard">

            <div class="top">
                <section id="my-courses" class="dashboard-section">
                    <h2>My Courses</h2>
                    <p>A list of courses you are currently enrolled in for the semester.</p>

                    <div class="content-placeholder">
                        <p>A table showing course names, faculty assigned, and quick access links to course materials will be displayed here.</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>ID</th>
                                    <th>Faculty</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Advanced Algorithms</td>
                                    <td>CS 405</td>
                                    <td>Dr. Mensah</td>
                                    <td>Active</td>
                                </tr>
                                <tr>
                                    <td>Ethical Leadership</td>
                                    <td>GS 301</td>
                                    <td>Prof. Amoah</td>
                                    <td>Active</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <hr>

                <section id="grades-and-progress" class="dashboard-section">
                    <h2>Grades & Progress</h2>
                    <p>View your performance, current grades, and cumulative GPA.</p>

                    <div class="content-placeholder">
                        <p>Detailed grade breakdown for all assignments, quizzes, and exams will be accessible, including final course grades.</p>
                    </div>
                </section>
            </div>

            <hr>

            <section id="schedule" class="dashboard-section">
                <h2>Class Schedule</h2>
                <p>Your weekly class timetable, including room numbers and meeting times.</p>

                <div class="content-placeholder">
                    <p>A weekly calendar view or a detailed list of your class schedule will be displayed here.</p>
                </div>
            </section>

            <hr>

            <section id="resources" class="dashboard-section">
                <h2>Academic Resources</h2>
                <p>Links to the academic calendar, library services, and tutoring support.</p>

                <div class="content-placeholder">
                    <ul>
                        <li><a href="#">Library Portal</a></li>
                        <li><a href="#">Tutoring Sign-up</a></li>
                        <li><a href="#">Academic Calendar</a></li>
                    </ul>
                </div>
            </section>

        </main>
    </div>

    <footer>
        <p>&copy; 2025 Your Project. Student Access.</p>
    </footer>

</body>
</html>