<?php

class Logout {
    public function startSession() {
        session_start();
    }

    public function destroySession() {
        session_unset();
        session_destroy();
    }

    public function redirectToLogin() {
        header("Location: register.php");
        exit;
    }
}

require_once 'logout.php';

$session = new Logout();
$session->startSession();
$session->destroySession();
$session->redirectToLogin();
?>