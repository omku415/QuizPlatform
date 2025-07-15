<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: register.php');
    exit();
}

$tid = $_SESSION['tid'] ?? null;
$quizId = $_GET['qid'] ?? null;

if (!$tid || !$quizId) {
    header('Location: teacher_dashboard.php');
    exit();
}

include('database.php');
$db = new Database();
$conn = $db->connect();

$query = "SELECT * FROM quizzes WHERE qid = ? AND tid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $quizId, $tid);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();
$stmt->close();

$query = "SELECT * FROM questions WHERE qid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $quizId);
$stmt->execute();
$questions = $stmt->get_result();
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qtext'])) {
    $qtext = $_POST['qtext'];
    $correct = $_POST['correct'];
    $options = $_POST['options'];
    $mark = $_POST['mark'];

    $query = "INSERT INTO questions (qid, qtext, correct, opone, optwo, opthree, opfour, mark) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssssi", $quizId, $qtext, $correct, $options[1], $options[2], $options[3], $options[4], $mark);

    if ($stmt->execute()) {
        header("Location: quiz_details.php?qid=$quizId");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Details</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        header {
            background-color:black;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
        }
        nav a:hover {
            text-decoration: underline;
        }
        section {
            padding: 20px;
        }
        h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
        }
        .quiz-details {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 45px;
            margin-bottom: 20px;
        }
        .quiz-details p {
            font-size: 18px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 20px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #8f44ae;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table a {
            text-decoration: none;
            color: #8f44ae;
            font-weight: bold;
        }
        table a:hover {
            text-decoration: underline;
        }
        .add-question-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .add-question-form input, .add-question-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .add-question-form button {
            background-color: #8f44ae;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .add-question-form button:hover {
            background-color: #732e8b;
        }
        button{
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover{
            background-color: #732e8b;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="teacher_dashboard.php">Back to Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section>
        <div class="quiz-details">
            <h2>Quiz: <?= htmlspecialchars($quiz['title']) ?></h2>
            <p><strong>Subject:</strong> <?= htmlspecialchars($quiz['subject']) ?></p>
            <p><strong>Start Date:</strong> <?= htmlspecialchars($quiz['sdate']) ?></p>
            <p><strong>End Date:</strong> <?= htmlspecialchars($quiz['edate']) ?></p>
        </div>

        <h3>Questions</h3>
        <?php if ($questions->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Question Text</th>
                    <th>Correct Answer</th>
                    <th>Options</th>
                    <th>Delete</th>
                </tr>
                <?php while ($question = $questions->fetch_assoc()): ?>
                    <tr>
    <td><?= htmlspecialchars($question['qtext']) ?></td>
    <td>Option <?= $question['correct'] ?></td>
    <td>
        <ol>
            <li><?= htmlspecialchars($question['opone']) ?></li>
            <li><?= htmlspecialchars($question['optwo']) ?></li>
            <li><?= htmlspecialchars($question['opthree']) ?></li>
            <li><?= htmlspecialchars($question['opfour']) ?></li>
        </ol>
    </td>
    <td>
        <a href="delete_question.php?quesid=<?= $question['quesid'] ?>" onclick="return confirm('Are you sure you want to delete this question?');">
            <button>Delete</button>
        </a>
    </td>
</tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No questions available. You can add a new question below.</p>
        <?php endif; ?>

        <div class="add-question-form">
            <h3>Add New Question</h3>
            <form method="POST">
                <div>
                    <label for="qtext">Question Text:</label>
                    <textarea id="qtext" name="qtext" required></textarea>
                </div>
                <div>
                    <label for="correct">Correct Answer (Option number):</label>
                    <input type="number" id="correct" name="correct" required min="1" max="4">
                </div>
                <div>
                    <h4>Options:</h4>
                    <label for="option1">Option 1:</label>
                    <input type="text" id="option1" name="options[1]" required>
                    <label for="option2">Option 2:</label>
                    <input type="text" id="option2" name="options[2]" required>
                    <label for="option3">Option 3:</label>
                    <input type="text" id="option3" name="options[3]" required>
                    <label for="option4">Option 4:</label>
                    <input type="text" id="option4" name="options[4]" required>
                </div>
                <div>
                    <label for="mark">Marks:</label>
                    <input type="number" id="mark" name="mark" required min="1">
                </div>
                <button type="submit">Add Question</button>
            </form>
        </div>
    </section>
</body>
</html>