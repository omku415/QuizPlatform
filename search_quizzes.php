<?php
include('database.php'); 

$db = new Database();
$conn = $db->connect();

class Student {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function isStudentLoggedIn() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = $_GET['query'];
    $today = date('Y-m-d'); 

    $stmt = $conn->prepare(
        "SELECT qid, title, subject, sdate, edate 
         FROM quizzes 
         WHERE subject LIKE ? 
         AND sdate <= ? AND edate >= ?"
    );
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("sss", $searchTerm, $today, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Search Results</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
                margin: 0;
                padding: 20px;
            }
            h3 {
                color: #333;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                background-color: #fff;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: center;
            }
            th {
                background-color: #007bff;
                color: white;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .start-quiz {
                background-color: #28a745;
                color: white;
                border: none;
                padding: 8px 12px;
                text-decoration: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
            }
            .start-quiz:hover {
                background-color: #218838;
            }
            .no-results {
                font-size: 18px;
                color: #666;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>";

    if ($result->num_rows > 0) {
        echo "<h3>Search Results for '$query'</h3>";
        echo "<table>
                <tr>
                    <th>Quiz Title</th>
                    <th>Subject</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['title']) . "</td>
                    <td>" . htmlspecialchars($row['subject']) . "</td>
                    <td>" . htmlspecialchars($row['sdate']) . "</td>
                    <td>" . htmlspecialchars($row['edate']) . "</td>
                    <td>
                        <a class='start-quiz' href='start_quiz.php?id=" . $row['qid'] . "'>Start Quiz</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='no-results'>No quizzes found for '$query'.</p>";
    }

    echo "</body>
    </html>";

    $stmt->close();
    $conn->close();
} else {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Invalid Request</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #333;
                text-align: center;
                margin-top: 50px;
            }
        </style>
    </head>
    <body>
        <p>Invalid request. Please use the search form.</p>
    </body>
    </html>";
}
?>