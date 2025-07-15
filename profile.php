<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: register.php');
    exit();
}

$sid = $_SESSION['sid'] ?? null;

if (!$sid) {
    header('Location: register.php');
    exit();
}

require 'database.php';

$db = new Database();
$conn = $db->connect();

$sql = "SELECT sname, semail, sphone FROM student WHERE sid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sid);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "Error: Student not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_name = $_POST['sname'];
    $new_email = $_POST['semail'];
    $new_phone = $_POST['sphone'];

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!preg_match('/^\d{10}$/', $new_phone)) {
        $error_message = "Phone number must be 10 digits.";
    } else {
        $update_sql = "UPDATE student SET sname = ?, semail = ?, sphone = ? WHERE sid = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $new_name, $new_email, $new_phone, $sid);

        if ($update_stmt->execute()) {
            $_SESSION['sname'] = $new_name;  
            $success_message = "Profile updated successfully!";
            $student['sname'] = $new_name;
            $student['semail'] = $new_email;
            $student['sphone'] = $new_phone;
        } else {
            $error_message = "Error updating profile.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">My Profile</h2>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <form method="POST" action="profile.php">
            <div class="form-group">
                <label for="sname">Name:</label>
                <input type="text" name="sname" class="form-control" value="<?= htmlspecialchars($student['sname']) ?>" required>
            </div>
            <div class="form-group">
                <label for="semail">Email:</label>
                <input type="email" name="semail" class="form-control" value="<?= htmlspecialchars($student['semail']) ?>" required>
            </div>
            <div class="form-group">
                <label for="sphone">Phone Number:</label>
                <input type="text" name="sphone" class="form-control" value="<?= htmlspecialchars($student['sphone']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
        <br>
        <a href="student_dashboard.php" class="btn btn-secondary"><span style="background-color:white;"><ion-icon name="arrow-back-outline"></ion-icon> Back</span></a>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>