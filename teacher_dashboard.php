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
$query = "SELECT * FROM quizzes WHERE tid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $tid);
$stmt->execute();
$result = $stmt->get_result();

$quizzes = [];
while ($row = $result->fetch_assoc()) {
    $quizzes[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="teacher_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        h1,h2{
            padding-left: 40%;
        }
.quiz-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 18px;
    text-align: left;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.quiz-table thead tr {
    background-color: #8f44ae;
    color: #ffffff;
    text-transform: uppercase;
}

.quiz-table th,
.quiz-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

.quiz-table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

.quiz-link {
    color: #8f44ae;
    text-decoration: none;
    font-weight: bold;
}

.quiz-link:hover {
    text-decoration: underline;
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
.feedback-btn{
    background-color: #007BFF;
}
.edit-btn {
    background-color: #28a745;
}

.edit-btn:hover {
    background-color: #218838;
}

.delete-btn {
    background-color: #dc3545;
}

.delete-btn:hover {
    background-color: #c82333;
}

.no-quizzes {
    font-size: 18px;
    color: #555;
    text-align: center;
    margin: 20px 0;
}

.no-quizzes i {
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
    </style>
</head>
<body>
    <header>
        <div class="logo"></div>
        <nav class="navigation">
            <a href="teacher_dashboard.php">Home</a>
            <a href="create_quiz.php">Create Quiz</a>
            <!-- <a href="#">Feedbackss</a> -->
            <a href="logout.php">Logout</a>
        </nav>
        <div class="menu-icon"><i class="fas fa-bars"></i></div>
    </header>
    <div class="dropdown-menu">
        <a href="index.php">Home</a>
        <a href="create_quiz.php">Create Quiz</a>
    </div>

    <section class="home">
        <div class="home-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide slide">
                    <div class="content">
               <!-- <span>Welcome,<?=htmlspecialchars($tname) ?></span> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="o"><br>
   <h1 style="font-size:30px; color:#007BFF"> Welcome <?=htmlspecialchars($tname) ?></h1>
   <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Empower your students and elevate learning - create, manage, and inspire with your own quizzes today!</p>
   <br><h2>Your Quizzes</h2>
    <?php if (!empty($quizzes)): ?>
        <table class="quiz-table">
        <thead>
    <tr>
        <th><i class="fas fa-book"></i> Quiz Title&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        <th><i class="fas fa-chalkboard-teacher"></i> Subject&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        <th><i class="fas fa-calendar-alt"></i> Start Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        <th><i class="fas fa-calendar-alt"></i> End Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        <th><i class="fas fa-cog"></i> Quiz&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        <th><i class="fas fa-question-circle"></i> Questions</th>
        <th><i class="fas fa-chart-bar"></i> Results</th>
        <th><i class="fas fa-comments"></i> Feedback</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($quizzes as $quiz): ?>
        <tr>
        <td>
    <span class="quiz-title">
        <?= htmlspecialchars($quiz['title']) ?>
    </span>
</td>
            <td><?= htmlspecialchars($quiz['subject']) ?></td>
            <td><?= htmlspecialchars($quiz['sdate']) ?></td>
            <td><?= htmlspecialchars($quiz['edate']) ?></td>
            <td>
                <a class="action-btn edit-btn" href="edit_quiz.php?qid=<?= $quiz['qid'] ?>">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a class="action-btn delete-btn" href="delete_quiz.php?qid=<?= $quiz['qid'] ?>" onclick="return confirm('Are you sure you want to delete this quiz?');">
                    <i class="fas fa-trash-alt"></i> Delete
                </a>
            </td>
            <td>
                <a class="action-btn edit-btn" href="addquestions.php?qid=<?= $quiz['qid'] ?>">
                    <i class="fas fa-plus-circle"></i> Add
                </a>
                <a class="action-btn delete-btn" href="deletequestion.php?qid=<?= $quiz['qid'] ?>">
                    <i class="fas fa-trash-alt"></i> Delete
                </a>
            </td>
            <td>
            <a class="action-btn feedback-btn" href="view_results.php?qid=<?= $quiz['qid'] ?>"> 
                <i class="fas fa-chart-bar"></i> View
            </a>
        </td>

            <td>
                <a class="action-btn feedback-btn" href="view_feedback.php?qid=<?= $quiz['qid'] ?>">
                    <i class="fas fa-comments"></i> Feedback
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    <?php else: ?>
        <p class="no-quizzes">
            <i class="fas fa-exclamation-circle"></i> No quizzes created yet. 
            <a class="create-link" href="create_quiz.php">
                 &nbsp;&nbsp;<i class="fas fa-plus-circle"></i>Create one now!
            </a>
        </p>
    <?php endif; ?>
</section>

    <section class="footer">
        <div class="box-container">
            <div class="box">
                <h3>Quick Links</h3>
                <a href="index.php"><i class="fas fa-angle-right"></i> Home</a>
                <a href="create_quiz.php"><i class="fas fa-angle-right"></i> Create Quiz</a>
                <a href="#"><i class="fas fa-angle-right"></i> Logout</a>
            </div>
            <div class="box">
                <h3>Contact Info</h3>
                <a href="#"><i class="fas fa-phone"></i> +91 9447260477</a>
                <a href="#"><i class="fas fa-phone"></i> +91 9352753791</a>
                <a href="#"><i class="fas fa-envelope"></i> admin2024@gmail.com</a>
                <a href="#"><i class="fas fa-map"></i> Alappuzha, Kerala, India</a>
            </div>
            <div class="box">
                <h3>Follow us</h3>
                <a href="#"><i class="fab fa-facebook-f"></i> Facebook</a>
                <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
            </div>
        </div>
        <div class="credit">Created By <span>StudentIT</span> | All Rights Reserved!</div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="teacher_dashboard.js"></script>
</body>
</html>