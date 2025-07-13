<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: register.php');
    exit();
}

$tid = $_SESSION['tid'] ?? null;
if (!$tid) {
    header('Location: register.php');
    exit();
}

include('database.php');
$db = new Database();
$conn = $db->connect();

if (isset($_GET['qid'])) {
    $qid = $_GET['qid'];

    $query = "SELECT * FROM quizzes WHERE qid = ? AND tid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $qid, $tid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fetchQuestionsQuery = "SELECT quesid FROM questions WHERE qid = ?";
        $fetchQuestionsStmt = $conn->prepare($fetchQuestionsQuery);
        $fetchQuestionsStmt->bind_param("i", $qid);
        $fetchQuestionsStmt->execute();
        $questionsResult = $fetchQuestionsStmt->get_result();

        $deleteQuestionsQuery = "DELETE FROM questions WHERE qid = ?";
        $deleteQuestionsStmt = $conn->prepare($deleteQuestionsQuery);
        $deleteQuestionsStmt->bind_param("i", $qid);
        $deleteQuestionsStmt->execute();

        $deleteQuizQuery = "DELETE FROM quizzes WHERE qid = ?";
        $deleteQuizStmt = $conn->prepare($deleteQuizQuery);
        $deleteQuizStmt->bind_param("i", $qid);

        if ($deleteQuizStmt->execute()) {
            header('Location: teacher_dashboard.php');
            exit();
        } else {
            echo "Error deleting quiz.";
        }
    } else {
        echo "Quiz not found or you do not have permission to delete it.";
    }
} else {
    echo "Invalid request. No quiz ID provided.";
}
?>