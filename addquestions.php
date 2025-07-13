<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: register.php');
    exit();
}
include('database.php');
include('Question.php');
$qid = $_GET['qid'] ?? null;
if (!$qid) {
    header('Location: teacher_dashboard.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$question = new Question($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qtext = $_POST['qtext'];
    $correct = $_POST['correct'];
    $mark = $_POST['mark'];
    $opone = $_POST['opone'];
    $optwo = $_POST['optwo'];
    $opthree = $_POST['opthree'];
    $opfour = $_POST['opfour'];
    $success = $question->addQuestion($qid, $qtext, $correct, $mark, $opone, $optwo, $opthree, $opfour);

    if ($success) {
        echo "<script>alert('Question added successfully!'); window.location.href='teacher_dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to add question. Please try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Question</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        section {
            width: 50%;
            margin: 30px auto;
            margin-top: 60px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #555;
            margin: 10px 0 5px;
            display: block;
        }

        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea {
            height: 100px;
        }

        input[type="text"]:focus, input[type="number"]:focus, textarea:focus {
            border-color: #6c5ce7;
            outline: none;
        }

        button {
            background-color: #6c5ce7;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #5a4cd1;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"></div>
        <nav class="navigation">
            <a href="teacher_dashboard.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section>
        <h2>Add Question to Quiz</h2>
        <form action="addquestions.php?qid=<?= $qid ?>" method="POST">
            <label for="qtext"><b>Question Text:</b></label>
            <textarea name="qtext" required></textarea>

            <label for="opone"><b>Option 1:</b></label>
            <input type="text" name="opone" required>

            <label for="optwo"><b>Option 2:</b></label>
            <input type="text" name="optwo" required>

            <label for="opthree"><b>Option 3:</b></label>
            <input type="text" name="opthree" required>

            <label for="opfour"><b>Option 4:</b></label>
            <input type="text" name="opfour" required>

            <label for="correct"><b>Correct Option:</b></label>
            <input type="text" name="correct" min="1" max="4" required>

            <label for="mark"><b>Marks:</b></label>
            <input type="number" name="mark" min="1" required>

            <button type="submit">Add Question</b></button>
        </form>
    </section>
</body>
</html>