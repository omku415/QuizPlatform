<?php
require_once 'database.php';

class AdminLogin {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function login($email, $password) {
        $query = "SELECT * FROM admin WHERE aemail = ? AND apassword = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt === false) {
            die("Error preparing the SQL statement: " . $this->db->error);
        }

        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            return true; 
        } else {
            return false; 
        }
    }

    public function __destruct() {
        $this->db->close();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['aemail']);
    $password = trim($_POST['apassword']);
    if (empty($email) || empty($password)) {
        echo "<p style='color: red;'>Both fields are required!</p>";
        exit;
    }

    $adminLogin = new AdminLogin();
    $isAuthenticated = $adminLogin->login($email, $password);

    if ($isAuthenticated) {
        session_start();
        $_SESSION['admin'] = $email; 
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "<script>
                    window.onload = function() {
                        alert('Invalid password.');
                    }
                  </script>";
    }
}
?>