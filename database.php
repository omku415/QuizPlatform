<?php

class Database {
    private $host = "localhost";
    private $user = "root";
    private $pwd = "";
    private $dbName = "quiz";

    public function connect() {
        $conn = new mysqli($this->host, $this->user, $this->pwd, $this->dbName);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }
}