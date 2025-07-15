<?php
session_start();
include('database.php');

class Student {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function isStudentLoggedIn() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';
    }
}

class QuizSubmission {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getCorrectAnswersAndMarks($quizId) {
        $stmt = $this->conn->prepare("SELECT quesid, qtext, correct, mark, opone, optwo, opthree, opfour FROM questions WHERE qid = ?");
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $correctAnswers = [];
        while ($row = $result->fetch_assoc()) {
            $correctAnswers[$row['quesid']] = [
                'qtext' => $row['qtext'], 
                'correct' => $row['correct'], 
                'mark' => $row['mark'], 
                'opone' => $row['opone'],
                'optwo' => $row['optwo'],
                'opthree' => $row['opthree'],
                'opfour' => $row['opfour']
            ];
        }
        $stmt->close();
        return $correctAnswers;
    }

    public function saveSubmission($sid, $quizId, $score) {
        $stmt = $this->conn->prepare("INSERT INTO attend (sid, qid, score) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $sid, $quizId, $score);
        $stmt->execute();
        $stmt->close();
    }

    public function saveFeedback($sid, $quizId, $teacherId, $comment) {
        $stmt = $this->conn->prepare("INSERT INTO feedback (sid, qid, tid, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $sid, $quizId, $teacherId, $comment);
        $stmt->execute();
        $stmt->close();
    }

    public function getTeacherIdForQuiz($quizId) {
        $stmt = $this->conn->prepare("SELECT tid FROM quizzes WHERE qid = ?");
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $teacherId = $result->fetch_assoc()['tid'];
        $stmt->close();
        return $teacherId;
    }
}

$db = new Database();
$conn = $db->connect();
$quizSubmission = new QuizSubmission($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id']) && isset($_POST['answers'])) {
    $quizId = intval($_POST['quiz_id']);
    $answers = $_POST['answers'];
    $correctAnswers = $quizSubmission->getCorrectAnswersAndMarks($quizId);

    $score = 0;
    $totalMarks = 0; 
    $results = [];
    foreach ($correctAnswers as $quesId => $data) {
        $questionText = $data['qtext'];
        $correctOption = $data['correct'];
        $mark = $data['mark'];
        $options = [
            1 => $data['opone'],
            2 => $data['optwo'],
            3 => $data['opthree'],
            4 => $data['opfour']
        ];
        $correctOptionText = $options[$correctOption];
        $studentAnswer = isset($answers[$quesId]) ? intval($answers[$quesId]) : null;
        $isCorrect = ($studentAnswer === $correctOption);
        $marksScored = $isCorrect ? $mark : 0; 
        $totalMarks += $mark;
        $score += $marksScored;
        $results[] = [
            'question' => $questionText,
            'studentAnswer' => $studentAnswer,
            'correctOptionText' => $correctOptionText,
            'marksScored' => $marksScored,
            'isCorrect' => $isCorrect,
        ];
    }
    $studentId = $_SESSION['sid'];
    $teacherId = $quizSubmission->getTeacherIdForQuiz($quizId);
    $quizSubmission->saveSubmission($studentId, $quizId, $score);
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Quiz Results</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
                margin: 0;
                padding: 20px;
            }
            .result {
                background-color: #fff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .result h1 {
                color: #333;
                text-align: center;
            }
            .score-summary {
                font-size: 24px;
                margin-bottom: 20px;
                color: #007bff;
                text-align: center;
            }
            .question {
                border-bottom: 1px solid #ddd;
                padding: 10px 0;
            }
            .question p {
                margin: 5px 0;
            }
            .correct {
                color: green;
                font-weight: bold;
            }
            .wrong {
                color: red;
                font-weight: bold;
            }
            .back-btn {
                margin-top: 20px;
                display: block;
                text-align: center;
                background-color: #007bff;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                font-size: 16px;
            }
            .back-btn:hover {
                background-color: #0056b3;
            }
            .tick {
                color: green;
                font-size: 20px;
            }
            .cross {
                color: red;
                font-size: 20px;
            }
        </style>
    </head>
    <body>
        <div class='result'>
            <h1>Quiz Results</h1>
            <div class='score-summary'>You scored $score out of $totalMarks marks.</div>";

    foreach ($results as $result) {
        $question = htmlspecialchars($result['question']);
        $marksScored = $result['marksScored'];
        $correctOptionText = htmlspecialchars($result['correctOptionText']);
        $isCorrect = $result['isCorrect'];
        $status = $isCorrect ? "<span class='tick'>✓</span> <span class='correct'>Correct</span>" : "<span class='cross'>✗</span> <span class='wrong'>Wrong</span>";

        echo "<div class='question'>
                <p><strong>Question:</strong> $question</p>
                <p><strong>Correct Answer:</strong> $correctOptionText</p>
                <p><strong>Marks Scored:</strong> $marksScored</p>
                <p><strong>Status:</strong> $status</p>
              </div>";
    }

    echo "<a class='back-btn' href='student_dashboard.php'>Go to Dashboard</a>
        </div>
    </body>
    </html>";

} else {
    echo "<p>Invalid request. Please try again.</p>";
    exit;
}
?>