<?php
session_start();
include('database.php');

class QuizPlatform {
    private $conn;
    private $sid;

    public function __construct($sid) {
        $this->conn = $this->getDbConnection();
        $this->sid = $sid;
    }

    private function getDbConnection() {
        $database = new Database();
        return $database->connect();
    }

    public function getQuizDetails($qid) {
        $sql = "SELECT * FROM quizzes WHERE qid = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $qid);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getQuizQuestions($qid) {
        $sql = "SELECT * FROM questions WHERE qid = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $qid);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function saveQuizResults($qid, $answers) {
        $score = 0;
        $totalQuestions = count($answers);
        foreach ($answers as $questionId => $answer) {
            $sql = "SELECT correct FROM questions WHERE quesid = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $questionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $correctAnswer = $result->fetch_assoc()['correct'];

            if ($correctAnswer == $answer) {
                $score++;
            }
        }
        $sql = "INSERT INTO attend (sid, qid, score) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iii", $this->sid, $qid, $score);
        $stmt->execute();
        
        return $score;
    }
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: register.php');
    exit();
}

$sid = $_SESSION['sid'] ?? null;

$qid = $_GET['qid'] ?? null;
if (!$qid) {
    header('Location: results.php');
    exit();
}

$quizPlatform = new QuizPlatform($sid);
$quizDetails = $quizPlatform->getQuizDetails($qid);
$questions = $quizPlatform->getQuizQuestions($qid);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $answers = $_POST['answers']; 
    $score = $quizPlatform->saveQuizResults($qid, $answers);
    $message = "You scored $score out of " . count($questions) . "!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Take Quiz - <?php echo htmlspecialchars($quizDetails['title']); ?></title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <script src="jquery.js"></script>
    <script src="bootstrap.min.js"></script>
</head>
<body>
    <div class="header">
        <div class="row">
            <div class="col-lg-6">
                <span class="logo"></span>
            </div>
            <div class="col-md-4 col-md-offset-2">
                <span class="pull-right top title1">
                    <a href="logout.php" class="log">
                        <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;Logout
                    </a>
                </span>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Quiz: <?php echo htmlspecialchars($quizDetails['title']); ?></h2>

        <?php if (isset($message)) { ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form action="quiz.php?qid=<?php echo $qid; ?>" method="POST">
            <?php foreach ($questions as $question) { ?>
                <div class="form-group">
                    <label><?php echo htmlspecialchars($question['qtext']); ?></label><br>
                    <?php
                    $options = [
                        $question['opone'],
                        $question['optwo'],
                        $question['opthree'],
                        $question['opfour']
                    ];
                    foreach ($options as $index => $option) {
                    ?>
                        <label>
                            <input type="radio" name="answers[<?php echo $question['quesid']; ?>]" value="<?php echo $index; ?>" required>
                            <?php echo htmlspecialchars($option); ?>
                        </label><br>
                    <?php } ?>
                </div>
            <?php } ?>

            <button type="submit" class="btn btn-primary">Submit Quiz</button>
        </form>
    </div>
    <div class="row footer">
        <div class="col-md-3 box"><a href="http://www.projectworlds.in/online-examination" target="_blank">About us</a></div>
        <div class="col-md-3 box"><a href="#" data-toggle="modal" data-target="#login">Admin Login</a></div>
        <div class="col-md-3 box"><a href="#" data-toggle="modal" data-target="#developers">Developers</a></div>
        <div class="col-md-3 box"><a href="feedback.html" target="_blank">Feedback</a></div>
    </div>

</body>
</html>