<?php
// attendance.php

require_once "./database.php";
// -----------------------------
// 2. Handle form submission
// -----------------------------
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['attendance'])) {
    foreach($_POST['attendance'] as $student_id => $data) {
        $date = $data['date'];
        $present = isset($data['present']) ? 'present' : 'absent';
        $course_id = $data['course_id'];

        $stmt = $conn->prepare("INSERT INTO attendance (intern_id, faculty_id, date, status) VALUES (?, ?, ?, ?)");
        // NOTE: Replace intern_id and faculty_id logic as needed. Here we assume faculty_id = 1 for example
        $faculty_id = 1;
        $stmt->bind_param("iiss", $student_id, $faculty_id, $date, $present);
        $stmt->execute();
        $stmt->close();
    }
    $message = "Attendance saved successfully!";
}

// -----------------------------
// 3. Fetch students enrolled in courses
// -----------------------------
$students_data = [];
$sql = "SELECT sc.student_id, s.full_name AS name, c.course_id, c.course_title AS course_name
        FROM student_course sc
        JOIN student s ON sc.student_id = s.student_id
        JOIN course c ON sc.course_id = c.course_id
        WHERE sc.status = 'Approved'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students_data[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { margin-bottom: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        button { margin-top: 10px; padding: 8px 16px; cursor: pointer; }
        input[type="date"] { padding: 4px; }
        .message { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>

<h2>Attendance Form</h2>
<p>Mark attendance for students enrolled in your courses:</p>

<?php if(!empty($message)) echo "<p class='message'>$message</p>"; ?>

<form action="" method="POST">
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Date</th>
                <th>Present</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($students_data)): ?>
                <?php foreach($students_data as $student): ?>
                    <tr>
                        <td><?= $student['student_id'] ?></td>
                        <td><?= $student['name'] ?></td>
                        <td><?= $student['course_name'] ?></td>
                        <td><input type="date" name="attendance[<?= $student['student_id'] ?>][date]" required></td>
                        <td>
                            <input type="checkbox" name="attendance[<?= $student['student_id'] ?>][present]" value="1">
                            <input type="hidden" name="attendance[<?= $student['student_id'] ?>][course_id]" value="<?= $student['course_id'] ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No students enrolled yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <button type="submit">Save Attendance</button>
</form>

</body>
</html>
