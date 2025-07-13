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
$question_id = $_GET['question_id'] ?? null;
$qid = $_GET['qid'] ?? null;

if (!$question_id || !$qid) {
    header('Location: teacher_dashboard.php');
    exit();
}
$query = "DELETE FROM questions WHERE question_id = ? AND qid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $question_id, $qid);
if ($stmt->execute()) {
    echo "<script>alert('Question deleted successfully.'); window.location.href='manage_questions.php?qid=$qid';</script>";
} else {
    echo "<script>alert('Failed to delete question. Please try again.'); window.location.href='manage_questions.php?qid=$qid';</script>";
}

$stmt->close();
$conn->close();
?>