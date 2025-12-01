<?php
// intern_dashboard.php

// 1. Démarrer la session
session_start();

// 2. VÉRIFICATION D'AUTORISATION (Empêche l'accès si l'utilisateur n'est PAS un 'intern')
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'intern') {
    // Rediriger vers la page de connexion
    header("Location: ../html/login.php"); 
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
    <title>Faculty Intern Dashboard</title>
    
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body class="dashboard-layout">

    <header class="intern-dashboard">
        <h1>Faculty Intern Dashboard</h1>
        <p>Bienvenue, Intern <?php echo $full_name; ?>!</p> 
        <a href="logout.php" class="logout-btn">Log Out</a>
    </header>

    <nav class="dashboard-nav">
        <ul>
            <li><a href="#course-list">Course List</a></li>
            <li><a href="#sessions">Teaching Sessions</a></li>
            <li><a href="#reports">Performance Reports</a></li>
            <li><a href="#auditors-management">Auditors/Observers</a></li>
        </ul>
    </nav>

    <main class="dashboard-content">

        <section id="course-list" class="dashboard-section">
            <h2>Course List</h2>
            <p>Welcome to your list of assigned courses for the semester.</p>
            
            <div class="content-placeholder">
                <p>A detailed table listing course IDs, names, and schedules will be displayed here.</p>
                <table>
                    <thead>
                        <tr><th>ID</th><th>Course Name</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>CS 101</td><td>Intro to Programming</td><td>Active</td></tr>
                        <tr><td>BA 202</td><td>Financial Accounting</td><td>Active</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <hr>

        <section id="sessions" class="dashboard-section">
            <h2>📅 Teaching Sessions</h2>
            <p>Manage your upcoming and past teaching sessions and tutorials.</p>
            
            <div class="content-placeholder">
                <p>A calendar or list view of scheduled sessions, including time and location, will go here.</p>
            </div>
        </section>

        <hr>

        <section id="reports" class="dashboard-section">
            <h2>📈 Performance Reports</h2>
            <p>View feedback and performance metrics from faculty and students.</p>
            
            <div class="content-placeholder">
                <p>Charts and summaries of your student evaluation and faculty feedback reports will be available here.</p>
            </div>
        </section>

        <hr>

        <section id="auditors-management" class="dashboard-section">
            <h2>👤 Auditors/Observers Management</h2>
            <p>Interface to manage and submit reports regarding course auditors and observers.</p>
            
            <div class="content-placeholder">
                <p>Forms and tables to track observers assigned to your sessions will be implemented here.</p>
            </div>
        </section>

    </main>
    
    <footer>
        <p>&copy; 2025 Your Project. Intern Access.</p>
    </footer>

</body>
</html>