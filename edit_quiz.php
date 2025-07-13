<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: register.php');
    exit();
}

class EditQuiz {
    private $conn;
    private $tid;
    private $quiz;

    public function __construct($dbConnection, $tid) {
        $this->conn = $dbConnection;
        $this->tid = $tid;
    }

    public function getQuiz($qid) {
        $query = "SELECT * FROM quizzes WHERE qid = ? AND tid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $qid, $this->tid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $this->quiz = $result->fetch_assoc();
            return $this->quiz;
        } else {
            throw new Exception("Quiz not found.");
        }
    }

    public function updateQuiz($qid, $title, $subject, $sdate, $edate) {
        $query = "UPDATE quizzes SET title = ?, subject = ?, sdate = ?, edate = ? WHERE qid = ? AND tid = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssii", $title, $subject, $sdate, $edate, $qid, $this->tid);
        return $stmt->execute();
    }

    public function getQuizData() {
        return $this->quiz;
    }
}

include('database.php');
$db = new Database();
$conn = $db->connect();

$tid = $_SESSION['tid'] ?? null;
if (!$tid) {
    header('Location: register.php');
    exit();
}

try {
    $editQuiz = new EditQuiz($conn, $tid);

    if (isset($_GET['qid'])) {
        $qid = $_GET['qid'];
        $quiz = $editQuiz->getQuiz($qid);
    } else {
        throw new Exception("Invalid quiz ID.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $subject = $_POST['subject'];
        $sdate = $_POST['sdate'];
        $edate = $_POST['edate'];

        if ($editQuiz->updateQuiz($qid, $title, $subject, $sdate, $edate)) {
            echo "<script>alert('Quiz updated successfully!'); window.location.href = 'teacher_dashboard.php';</script>";
        } else {
            throw new Exception("Error updating quiz details.");
        }
    }
} catch (Exception $e) {
    echo "<script>alert('{$e->getMessage()}'); window.location.href = 'teacher_dashboard.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
        }
        header {
            background: black;
            color: white;
            padding: 15px 0;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
        }
        nav a:hover {
            text-decoration: underline;
        }

        .edit-quiz {
            max-width: 600px;
            margin: 70px ;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .edit-quiz h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .edit-quiz label {
            font-weight: bold;
            color: #555;
        }
        .edit-quiz input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .edit-quiz button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .edit-quiz button:hover {
            background: #0056b3;
        }
        .back-button {
            display: inline-block;
            margin-top: 10px;
            color: black;
            text-decoration: none;
            font-size: 16px;
        }
        .back-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="teacher_dashboard.php">Home</a>
            <a href="create_quiz.php">Create Quiz</a>
            <!-- <a href="teacher_dashboard.php">Home</a> -->
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section class="edit-quiz">
        <h2>Edit Quiz</h2>
        <form action="edit_quiz.php?qid=<?= $quiz['qid'] ?>" method="POST">
            <label for="title">Quiz Title</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($quiz['title']) ?>" required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($quiz['subject']) ?>" required>

            <label for="sdate">Start Date</label>
            <input type="date" id="sdate" name="sdate" value="<?= htmlspecialchars($quiz['sdate']) ?>" required min="<?=date('Y-m-d');?>">

            <label for="edate">End Date</label>
            <input type="date" id="edate" name="edate" value="<?= htmlspecialchars($quiz['edate']) ?>" required min="<?=date('Y-m-d');?>">

            <button type="submit">Update Quiz</button>
        </form>
        <a href="teacher_dashboard.php" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>
    </section>
</body>
</html>