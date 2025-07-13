<?php
session_start();
include('database.php');

if (!isset($_GET['qid'])) {
    echo "Quiz ID is missing!";
    exit();
}

$qid = $_GET['qid'];
$sid = $_SESSION['sid']; 

$conn = (new Database())->connect();

$stmt = $conn->prepare("SELECT tid FROM quizzes WHERE qid = ?");
$stmt->bind_param("i", $qid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Quiz not found!";
    exit();
}

$quiz = $result->fetch_assoc();
$tid = $quiz['tid']; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = $_POST['feedback'];
    $stmt = $conn->prepare("INSERT INTO feedback (sid, qid, tid, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $sid, $qid, $tid, $feedback);

    if ($stmt->execute()) {
        header("Location: student_dashboard.php");
    } else {
        $message = "Error submitting feedback!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback</title>
    <style>
        :root {
            --main-color: #007BFF;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --hover-color: #0056b3;
            --error-color: #ff4d4f;
            --success-color: #28a745;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--secondary-color);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .feedback-container {
            background: white;
            width: 400px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        h3 {
            color: ;black;
            margin-bottom: 20px;
        }

        form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
            font-size: 1em;
            font-family: inherit;
            transition: border-color 0.3s;
            margin-bottom: 15px;
        }

        form textarea:focus {
            border-color: var(--main-color);
            outline: none;
        }

        button {
            background-color: #265a88;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color:#1e3e5b ;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .message.success {
            color: black;
        }

        .message.error {
            background-color: var(--error-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="feedback-container">
        <h3>Feedback</h3>
        <?php if (isset($message)): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <textarea name="feedback" rows="4" placeholder="Write your feedback here..." required></textarea>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</body>
</html>