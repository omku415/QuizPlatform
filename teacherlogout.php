<?php
class TeacherLogout {
    public function __construct() {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
$logout = new TeacherLogout();
$logout->logout();