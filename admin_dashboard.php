<?php
require_once 'database.php';
class AdminDashboard {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->connect();
    }
    public function getTeachers() {
        $sql = "SELECT tid, tname, temail, tphone, tcert, verified FROM teacher";
        return $this->conn->query($sql);
    }
    public function getStudents() {
        $sql = "SELECT sid, sname, semail, sphone FROM student";
        return $this->conn->query($sql);
    }
    public function getStatistics() {
        $stats = [];
        $stats['pending_teachers'] = $this->conn->query("SELECT COUNT(*) AS count FROM teacher WHERE verified = 0")->fetch_assoc()['count'] ?? 0;
        return $stats;
    }
    public function updateTeacherStatus($teacherId, $status) {
        $sql = "UPDATE teacher SET verified = ? WHERE tid = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $status, $teacherId);

        if ($stmt->execute()) {
            $action = $status === 1 ? 'approved' : 'rejected';
            echo "<script>alert('Teacher successfully $action'); window.location.href='?view=teachers';</script>";
        } else {
            echo "<script>alert('Error updating teacher status.');</script>";
        }
        $stmt->close();
    }
    public function deleteTeacher($teacherId) {
        $sql = "DELETE FROM teacher WHERE tid = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $teacherId);

        if ($stmt->execute()) {
            echo "<script>alert('Teacher deleted successfully.'); window.location.href='?view=teachers';</script>";
        } else {
            echo "<script>alert('Error deleting teacher: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }

    public function deleteStudent($studentId) {
        $sql = "DELETE FROM student WHERE sid = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $studentId);

        if ($stmt->execute()) {
            echo "<script>alert('Student deleted successfully.'); window.location.href='?view=students';</script>";
        } else {
            echo "<script>alert('Error deleting student: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}
$dashboard = new AdminDashboard();
if (isset($_GET['update']) && isset($_GET['type']) && $_GET['type'] === 'teacher') {
    $teacherId = intval($_GET['update']);
    $action = $_GET['action'] ?? '';
    if ($action === 'approve') {
        $dashboard->updateTeacherStatus($teacherId, 1); 
    } elseif ($action === 'reject') {
        $dashboard->updateTeacherStatus($teacherId, 2); 
    }
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $type = $_GET['type'] ?? '';
    if ($type === 'teacher') {
        $dashboard->deleteTeacher($id);
    } elseif ($type === 'student') {
        $dashboard->deleteStudent($id);
    }
}
if (isset($_GET['view'])) {
    $view = $_GET['view'];
    if ($view === 'teachers') {
        $teachers = $dashboard->getTeachers();
    } elseif ($view === 'students') {
        $students = $dashboard->getStudents();
    }
} else {
    $stats = $dashboard->getStatistics();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <nav class="navigation">
                <p style="font-family:Brush Script MT;font-size:30px;color:#b6e020">TriviaQuiz</p><br><br>
                <a href="?view=teachers"><i class="fas fa-user-tie"></i> Teachers</a>
                <a href="?view=students"><i class="fas fa-user-friends"></i> Students</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        <div class="content">
    <?php if (!isset($view)): ?>
        <h2>Welcome to the Admin Dashboard</h2><br>
        <div class="overview">
            <div>
                <h3>Pending Teacher Approvals</h3>
                <p><?php echo $stats['pending_teachers']; ?></p>
            </div>
        </div>
    <?php elseif ($view === 'teachers'): ?>
        <h2>Teacher Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Certification</th>
                    <th>Verification</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($teachers->num_rows > 0): ?>
                    <?php while ($row = $teachers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['tname']); ?></td>
                            <td><?php echo htmlspecialchars($row['temail']); ?></td>
                            <td><?php echo htmlspecialchars($row['tphone']); ?></td>
                            <td>
                                <?php if ($row['tcert']): ?>
                                    <a href="<?php echo htmlspecialchars($row['tcert']); ?>" target="_blank">View Certificate</a>
                                <?php else: ?>
                                    No Certificate
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['verified'] == 0): ?>
                                    <button onclick="window.location.href='?update=<?php echo $row['tid']; ?>&type=teacher&action=approve'">Approve</button>
                                    <button onclick="window.location.href='?update=<?php echo $row['tid']; ?>&type=teacher&action=reject'">Reject</button>
                                <?php elseif ($row['verified'] == 1): ?>
                                    Approved
                                <?php elseif ($row['verified'] == 2): ?>
                                    Rejected
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="confirmDelete(<?php echo $row['tid']; ?>, 'teacher')">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No teachers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php elseif ($view === 'students'): ?>
        <h2>Student Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($students->num_rows > 0): ?>
                    <?php while ($row = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['sname']); ?></td>
                            <td><?php echo htmlspecialchars($row['semail']); ?></td>
                            <td><?php echo htmlspecialchars($row['sphone']); ?></td>
                            <td>
                                <button onclick="confirmDelete(<?php echo $row['sid']; ?>, 'student')">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No students found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
    </div>
    <script>
    function confirmDelete(id, type) {
        if (confirm("Are you sure you want to delete this " + type + "?")) {
            window.location.href = "?view=" + type + "s&delete=" + id + "&type=" + type;
        }
    }
    </script>
</body>
</html>