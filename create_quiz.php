<?php
session_start();
include('database.php');

$db = new Database();
$conn = $db->connect();

class Teacher {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function isTeacherLoggedIn() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'teacher';
    }

    public function getTeacherVerificationStatus($tid) {
        $query = "SELECT verified FROM teacher WHERE tid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $tid);
        $stmt->execute();
        $stmt->bind_result($verified);
        $stmt->fetch();
        $stmt->close();
 $statusMapping = [
    0 => 'pending',
    1 => 'approved',
    2 => 'rejected'
];
return $statusMapping[$verified] ?? 'unknown';
}
}

class QuizManager {
private $conn;

public function __construct($dbConnection) {
$this->conn = $dbConnection;
}

public function createQuiz($tid, $title, $subject, $numQuestions, $timeLimit, $startDate, $endDate) {
$query = "INSERT INTO quizzes (tid, title, subject, sdate, edate, timelt, nquestion) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $this->conn->prepare($query);
$stmt->bind_param("issssii", $tid, $title, $subject, $startDate, $endDate, $timeLimit, $numQuestions);

if ($stmt->execute()) {
    return $this->conn->insert_id; 
} else {
    return false;
}
}
public function insertQuestion($qid, $qtext, $correct, $options, $mark) {
    $query = "INSERT INTO questions (qid, qtext, correct, opone, optwo, opthree, opfour, mark) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("issssssi", $qid, $qtext, $correct, $options[1], $options[2], $options[3], $options[4], $mark);

    return $stmt->execute();
}
}
$teacher = new Teacher($conn);
if (!$teacher->isTeacherLoggedIn()) {
header('Location: register.php');
exit();
}
$tid = $_SESSION['tid'];
$verificationStatus = $teacher->getTeacherVerificationStatus($tid);

if ($verificationStatus === 'pending') {
    echo "<script>alert('Your account is still pending approval. Please wait for admin verification.');</script>";
    echo "<script>window.location.href = 'teacher_dashboard.php';</script>";
    exit();
} elseif ($verificationStatus === 'rejected') {
    echo "<script>alert('Your account has been rejected by the admin. You cannot create quizzes.');</script>";
    echo "<script>window.location.href = 'teacher_dashboard.php';</script>";
    exit();
} elseif ($verificationStatus !== 'approved') {
    echo "<script>alert('Invalid verification status. Please contact support.');</script>";
    echo "<script>window.location.href = 'teacher_dashboard.php';</script>";
    exit();
}
$quizManager = new QuizManager($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && !isset($_POST['qtext'])) {
    $title = $_POST['title'];
    $subject = $_POST['subject'];
    $numQuestions = $_POST['nquestion'];
    $timeLimit = $_POST['timelt'];
    $startDate = $_POST['sdate'];
    $endDate = $_POST['edate'];

    $quizId = $quizManager->createQuiz($tid, $title, $subject, $numQuestions, $timeLimit, $startDate, $endDate);

    if ($quizId) {
        $_SESSION['quiz_details'] = [
            'qid' => $quizId,
            'nquestion' => $numQuestions,
        ];
        header('Location: create_quiz.php'); 
        exit();
    } else {
        echo "<script>alert('Error creating quiz. Please try again.');</script>";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qtext'])) {
    $qid = $_SESSION['quiz_details']['qid']; 
    $numQuestions = $_SESSION['quiz_details']['nquestion'];

    for ($i = 0; $i < $numQuestions; $i++) {
        $qtext = $_POST['qtext'][$i];
        $correct = $_POST['correct'][$i];
        $options = $_POST['options'][$i + 1];
        $mark = $_POST['mark'][$i];

        if (!$quizManager->insertQuestion($qid, $qtext, $correct, $options, $mark)) {
            echo "Error inserting question " . ($i + 1) . "<br>";
        }
    }

    echo "<script>alert('Quiz and Questions submitted successfully!');</script>";
    unset($_SESSION['quiz_details']); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: black;
            padding: 10px 0;
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }
        header nav a:hover {
            text-decoration: underline;
        }
        .qform {
            margin: 30px auto;
            max-width: 800px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #8e44ad;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        textarea {
            resize: none;
        }
        button {
            background-color: #8e44ad;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn-back {
            background-color: #6c757d;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
    <script>
        function showQuestions() {
            document.getElementById("quizDetailsForm").style.display = "none"; 
            document.getElementById("questionsForm").style.display = "block"; 
        }

    </script>
</head>
<body>
    <header>
        <nav>
            <a href="teacher_dashboard.php"style="color:white;">Home</a>&nbsp;&nbsp;
<!--              <a href="create_quiz.php">Create Quiz</a> -->
            <a href="logout.php"style="color:white;">Logout</a>
        </nav> 
    </header>
    
    <div class="qform">
        <div class="form-container" id="quizDetailsForm" 
             style="display: <?= !isset($_SESSION['quiz_details']) ? 'block' : 'none'; ?>;">
            <h2>Create Quiz</h2>
            <form action="" method="POST" onsubmit="return validateForm()">
                <div class="input-box">
                    <label for="title">Quiz Title:</label>
                    <input type="text" id="title" name="title" required>
                    <br>
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>
                    <br>
                    <label for="nquestion">Number of Questions:</label>
                    <input type="number" id="nquestion" name="nquestion" required min="1">
                    <br>
                    <label for="timelt">Time Limit (in minutes):</label>
                    <input type="number" id="timelt" name="timelt" required min="1">
                    <br>
                    <label for="sdate">Start Date:</label>
                    <input type="date" id="sdate" name="sdate" required min="<?=date('Y-m-d');?>">
                    <br> 
                    <label for="edate">End Date:</label>
                    <input type="date" id="edate" name="edate" required min="<?=date('Y-m-d');?>">
                    <br>
                    <button type="submit" class="btn-next">Next</button>
                </div>
            </form>
        </div>
    
        <div class="form-container" id="questionsForm" 
             style="display: <?= isset($_SESSION['quiz_details']) ? 'block' : 'none'; ?>;">
            <h2>Questions for Quiz</h2>
            <form action="create_quiz.php" method="POST">
                <?php if (isset($_SESSION['quiz_details'])): ?>
                    <?php 
                        $numQuestions = $_SESSION['quiz_details']['nquestion']; 
                        for ($i = 1; $i <= $numQuestions; $i++): 
                    ?>
                        <h3 style="color:#007BFF">Question <?= $i ?></h3>
                        <label for="qtext_<?= $i ?>"></label>
                        <textarea id="qtext_<?= $i ?>" name="qtext[]" rows="5" cols="55"style="border-radius: 5px;" required></textarea>
                        <br>
                        <label for="correct_<?= $i ?>">Correct Answer (Enter option number 1-4):</label>
                        <input type="number" id="correct_<?= $i ?>" name="correct[]" min="1" max="4" placeholder="Enter the correct option number"style="border-radius: 5px; border: 1px solid #6f6f6f;" required><br>
                        <br>
                        <h4>Options:</h4>
                        <label for="option1_<?= $i ?>">Option 1:&nbsp;&nbsp;</label>
                        <input type="text" id="option1_<?= $i ?>" name="options[<?= $i ?>][1]" style="border-radius: 5px;border: 1px solid #6f6f6f;" required><br>
                        <label for="option2_<?= $i ?>">Option 2:&nbsp;</label>
                        <input type="text" id="option2_<?= $i ?>" name="options[<?= $i ?>][2]" style="border-radius: 5px;border: 1px solid #6f6f6f;" required><br>
                        <label for="option3_<?= $i ?>">Option 3:&nbsp;</label>
                        <input type="text" id="option3_<?= $i ?>" name="options[<?= $i ?>][3]" style="border-radius: 5px;border: 1px solid #6f6f6f;" required><br>
                        <label for="option4_<?= $i ?>">Option 4:&nbsp;</label>
                        <input type="text" id="option4_<?= $i ?>" name="options[<?= $i ?>][4]" style="border-radius: 5px;border: 1px solid #6f6f6f;" required><br><br>
                        <label for="mark_<?= $i ?>">Mark for this Question:</label>
                        <input type="number" id="mark_<?= $i ?>" name="mark[]" min="1" placeholder="Enter mark for this question"style="border-radius: 5px;border: 1px solid #6f6f6f;" required><br><br>
                    <?php endfor; ?>
                    <br>
                    <!-- <button type="button" class="btn2 back-button" onclick="goBack()">
                        <span style="background-color:white;"><ion-icon name="arrow-back-outline"></ion-icon> Back</span>
                    </button> -->
<button type="submit">Submit</button>
<?php else: ?>
<p>Please complete the quiz creation form first.</p>
<?php endif; ?>
</form>
</div>
</div>
<script>
    function validateForm() {
        var numQuestions = document.getElementById("nquestion").value;
        var timeLimit = document.getElementById("timelt").value;
        if (numQuestions < 1) {
            alert("Number of questions cannot be negative or zero.");
            return false;
        }

        if (timeLimit < 1) {
            alert("Time limit cannot be negative or zero.");
            return false; 
        }
        var correctAnswers = document.querySelectorAll("input[name='correct[]']");
        for (var i = 0; i < correctAnswers.length; i++) {
            var correctAnswer = correctAnswers[i].value;
            if (correctAnswer < 1 || correctAnswer > 4) {
                alert("Correct answer for question " + (i + 1) + " must be between 1 and 4.");
                return false;
            }
        }

        return true;
    }
</script>
</body>
</html>