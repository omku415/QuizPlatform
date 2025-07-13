<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: register.php');
    exit();
}

include('database.php');
$db = new Database();
$conn = $db->connect();

$qid = $_GET['qid'] ?? null;

if (!$qid) {
    header('Location: teacher_dashboard.php');
    exit();
}

$quiz_query = "SELECT title FROM quizzes WHERE qid = ?";
$stmt_quiz = $conn->prepare($quiz_query);
$stmt_quiz->bind_param("i", $qid);
$stmt_quiz->execute();
$quiz_result = $stmt_quiz->get_result();
$quiz = $quiz_result->fetch_assoc();
$stmt_quiz->close();

$feedback_query = "SELECT f.comment, s.sname 
                   FROM feedback f 
                   JOIN student s ON f.sid = s.sid 
                   WHERE f.qid = ?";
$stmt_feedback = $conn->prepare($feedback_query);
$stmt_feedback->bind_param("i", $qid);
$stmt_feedback->execute();
$feedback_result = $stmt_feedback->get_result();

$feedbacks = [];
while ($row = $feedback_result->fetch_assoc()) {
    $feedbacks[] = $row;
}

$stmt_feedback->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Feedback</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <style>
        .feedback-container {
            max-width: 800px;
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .feedback-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #8f44ae;
        }

        .feedback-item {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
        }

        .student-name {
            font-weight: bold;
            color: #007bff;
        }

        .feedback-comment {
            margin-top: 5px;
        }

        .no-feedback {
            text-align: center;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="feedback-container">
        <h2 class="feedback-title">Feedback for Quiz: <?= htmlspecialchars($quiz['title']) ?></h2>

        <?php if (!empty($feedbacks)): ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="feedback-item">
                    <span class="student-name"><?= htmlspecialchars($feedback['sname']) ?>:</span>
                    <p class="feedback-comment"><?= htmlspecialchars($feedback['comment']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-feedback"><i class="fas fa-exclamation-circle"></i> No feedback available for this quiz.</p>
        <?php endif; ?>
    </div>
</body>
</html>