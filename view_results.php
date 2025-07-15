<?php
session_start();
include('database.php');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: register.php');
    exit();
}
$qid = $_GET['qid'] ?? null;

if (!$qid) {
    echo "Invalid Quiz ID.";
    exit();
}
$database = new Database();
$conn = $database->connect();
$sql_quiz = "SELECT title FROM quizzes WHERE qid = ?";
$stmt_quiz = $conn->prepare($sql_quiz);
$stmt_quiz->bind_param("i", $qid);
$stmt_quiz->execute();
$result_quiz = $stmt_quiz->get_result();
$quiz = $result_quiz->fetch_assoc();

if (!$quiz) {
    echo "Quiz not found.";
    exit();
}
$sql_results = "SELECT student.sname, attend.score 
                FROM attend 
                JOIN student ON attend.sid = student.sid 
                WHERE attend.qid = ?";
$stmt_results = $conn->prepare($sql_results);
$stmt_results->bind_param("i", $qid);
$stmt_results->execute();
$result_results = $stmt_results->get_result();
$student_results = $result_results->fetch_all(MYSQLI_ASSOC);

$stmt_results->close();
$stmt_quiz->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quiz Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
        }
        .container {
            margin-top: 50px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color:rgb(4, 10, 4);
            font-weight: bold;
            margin-bottom: 30px;
        }
        .quiz-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .quiz-table th,
        .quiz-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .quiz-table th {
            background-color:rgb(63, 18, 91);
            color: white;
        }
        .quiz-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn {
            background-color:rgb(0, 0, 0);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            display: inline-block;
        }
        .btn:hover {
            background-color:rgb(27, 9, 9);
        } 
    </style>
</head>
<body>
    <div class="container">
        <h2>Results for Quiz: <?= htmlspecialchars($quiz['title']); ?></h2>
        
        <?php if (empty($student_results)) { ?>
            <p class="text-center">No students have attended this quiz yet.</p>
        <?php } else { ?>
            <table class="quiz-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($student_results as $result) { ?>
                        <tr>
                            <td><?= htmlspecialchars($result['sname']); ?></td>
                            <td><?= htmlspecialchars($result['score']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <a href="teacher_dashboard.php" class="btn">Back to Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>