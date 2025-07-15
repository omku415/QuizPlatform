<?php
class Question {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function addQuestion($qid, $qtext, $correct, $mark, $opone, $optwo, $opthree, $opfour) {
        $query = "INSERT INTO questions (qid, qtext, correct, mark, opone, optwo, opthree, opfour) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ississss", $qid, $qtext, $correct, $mark, $opone, $optwo, $opthree, $opfour);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>