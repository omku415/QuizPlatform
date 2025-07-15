<?php
session_start();
include('database.php');
class Quiz {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getQuizDetails($quizId) {
        $stmt = $this->conn->prepare("SELECT title, subject, sdate, edate, timelt, nquestion FROM quizzes WHERE qid = ?");
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $quiz = $result->fetch_assoc();
        $stmt->close();
        return $quiz;
    }


    public function getQuizQuestions($quizId) {
        $stmt = $this->conn->prepare("SELECT quesid, qtext, opone, optwo, opthree, opfour FROM questions WHERE qid = ?");
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        $stmt->close();
        return $questions;
    }
}

$db = new Database();
$conn = $db->connect();
$quiz = new Quiz($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $quizId = intval($_GET['id']);
    $quizDetails = $quiz->getQuizDetails($quizId);
    $quizQuestions = $quiz->getQuizQuestions($quizId);

    if (!$quizDetails) {
        echo "<p>Quiz not found.</p>";
        exit;
    }
} else {
    echo "<p>Invalid request.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($quizDetails['title']); ?> - Start Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        h1, h2 {
            color: #333;
        }
        .quiz-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .quiz-questions {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .question {
            margin-bottom: 20px;
        }
        .options label {
            display: block;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .submit-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="quiz-details">
        <h1><?php echo htmlspecialchars($quizDetails['title']); ?></h1>
        <p><strong>Subject:</strong> <?php echo htmlspecialchars($quizDetails['subject']); ?></p>
        <p><strong>Start Date:</strong> <?php echo htmlspecialchars($quizDetails['sdate']); ?></p>
        <p><strong>End Date:</strong> <?php echo htmlspecialchars($quizDetails['edate']); ?></p>
        <p><strong>Time Limit:</strong> <?php echo htmlspecialchars($quizDetails['timelt']); ?> minutes</p>
        <p><strong>Number of Questions:</strong> <?php echo htmlspecialchars($quizDetails['nquestion']); ?></p>
    </div>

    <div class="quiz-questions">
        <h2>Questions</h2>
        <form action="submit_quiz.php" method="POST">
            <?php foreach ($quizQuestions as $index => $question): ?>
                <div class="question">
                    <p><strong>Q<?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['qtext']); ?></strong></p>
                    <div class="options">
                        <label>
                            <input type="radio" name="answers[<?php echo $question['quesid']; ?>]" value="1" required>
                            <?php echo htmlspecialchars($question['opone']); ?>
                        </label>
                        <label>
                            <input type="radio" name="answers[<?php echo $question['quesid']; ?>]" value="2" required>
                            <?php echo htmlspecialchars($question['optwo']); ?>
                        </label>
                        <label>
                            <input type="radio" name="answers[<?php echo $question['quesid']; ?>]" value="3" required>
                            <?php echo htmlspecialchars($question['opthree']); ?>
                        </label>
                        <label>
                            <input type="radio" name="answers[<?php echo $question['quesid']; ?>]" value="4" required>
                            <?php echo htmlspecialchars($question['opfour']); ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
            <input type="hidden" name="quiz_id" value="<?php echo $quizId; ?>">
            <button class="submit-btn" type="submit">Submit Quiz</button>
        </form>
    </div>
</body>
</html>