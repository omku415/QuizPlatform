<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: register.php');
    exit();
}

$tid = $_SESSION['tid'] ?? null;
$tname = $_SESSION['tname'] ?? null;
if (!$tid) {
    header('Location: register.php');
    exit();
}

include('database.php');
$db = new Database();
$conn = $db->connect();

$quizId = $_GET['qid'] ?? null;
if (!$quizId) {
    header('Location: teacher_dashboard.php');
    exit();
}
$query = "SELECT * FROM questions WHERE qid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question'])) {
    $quesid = $_POST['quesid'];
    $deleteQuery = "DELETE FROM questions WHERE quesid = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $quesid);
    $deleteStmt->execute();
    $deleteStmt->close();
    header("Location: deletequestion.php?qid=$quizId");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Questions</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        h2 {
            padding-left: 40%;
        }
        .question-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .question-table thead tr {
            background-color: #8f44ae;
            color: #ffffff;
            text-transform: uppercase;
        }
        .question-table th,
        .question-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .question-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .action-btn {
            color: #ffffff;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
        }
        button[type="submit"]{
            background-color: #dc3545
        }
        button[type="submit"]:hover{
            background-color: #c82333;
        }
        .no-questions {
            font-size: 18px;
            color: #555;
            text-align: center;
            margin: 20px 0;
        }
        .no-questions i {
            color: #007BFF;
            margin-right: 8px;
        }
        .create-link {
            color: #007BFF;
            font-weight: bold;
            text-decoration: none;
        }

        .create-link:hover {
            text-decoration: underline;
        }
        .options-list {
            list-style: none;
            padding-left: 0;
            font-size: 16px;
            margin-top: 10px;
        }

        .options-list li {
            background-color: #f1f1f1;
            border: 2px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }

        .options-list li:hover {
            background-color: #e0e0e0;
            border-color:rgb(15, 84, 134);
            cursor: pointer;
        }

        .options-list li strong {
            font-weight: bold;
            color: rgb(40, 116, 171);
        }

    </style>
</head>
<body>
    <header>
        <div class="logo"></div>
        <nav class="navigation">
            <a href="teacher_dashboard.php">Home</a>
            <!-- <a href="create_quiz.php">Create Quiz</a> -->
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section class="home">
        <h2>Questions for Quiz: <?= htmlspecialchars($quizId) ?></h2>

        <?php if (!empty($questions)): ?>
            <table class="question-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-question-circle"></i> Question</th>
                        <th><i class="fas fa-cogs"></i> Options</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $question): ?>
                        <tr>
                            <td><?= htmlspecialchars($question['qtext']) ?></td>
                            <td>
                                <ul class="options-list">
                                    <li><strong>Option 1:</strong> <?= htmlspecialchars($question['opone']) ?></li>
                                    <li><strong>Option 2:</strong> <?= htmlspecialchars($question['optwo']) ?></li>
                                    <li><strong>Option 3:</strong> <?= htmlspecialchars($question['opthree']) ?></li>
                                    <li><strong>Option 4:</strong> <?= htmlspecialchars($question['opfour']) ?></li>
                                </ul>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="quesid" value="<?= $question['quesid'] ?>">
                                    <button type="submit" name="delete_question" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this question?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-questions">
                <i class="fas fa-exclamation-circle"></i> No questions found for this quiz. 
                <a class="create-link" href="addquestions.php?qid=<?= $quizId ?>">Add questions now!</a>
            </p>
        <?php endif; ?>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>