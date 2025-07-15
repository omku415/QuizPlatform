<?php
session_start();
include('database.php');

class QuizPlatform {
    private $conn;
    private $sid;
    private $sname;

    public function __construct($sid, $sname) {
        $this->conn = $this->getDbConnection();
        $this->sid = $sid;
        $this->sname = $sname;
    }

    private function getDbConnection() {
        $database = new Database();
        return $database->connect();
    }
    public function getStudentResults() {
        $sql = "SELECT attend.qid, quizzes.title, attend.score 
                FROM attend 
                JOIN quizzes ON attend.qid = quizzes.qid 
                WHERE attend.sid = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->sid);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public static function checkAuthorization() {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
            header('Location: register.php');
            exit();
        }
    }

    public static function checkStudentId($sid) {
        if (!$sid) {
            header('Location: register.php');
            exit();
        }
    }
}


QuizPlatform::checkAuthorization();
$sid = $_SESSION['sid'] ?? null;
$sname = $_SESSION['sname'] ?? null;
QuizPlatform::checkStudentId($sid);

$quizPlatform = new QuizPlatform($sid, $sname);
$results = $quizPlatform->getStudentResults();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Quiz Platform - Results</title>
    <link rel="stylesheet" href="bootstrap.min.css" />
    <link rel="stylesheet" href="bootstrap-theme.min.css" />
    <link rel="stylesheet" href="main.css" />
    <link rel="stylesheet" href="font.css" />
    <link href="http://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css" />
    <script src="jquery.js" type="text/javascript"></script>
    <script src="bootstrap.min.js" type="text/javascript"></script>
  </head>
  <body>
    <div class="header">
      <div class="row">
        <div class="col-lg-6">
          <span class="logo"></span>
        </div>
        <div class="col-md-4 col-md-offset-2">
          <span class="pull-right top title1">
            <a href="logout.php" class="log">
              <!-- <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;Logout -->
            </a>
          </span>
        </div>
      </div>
    </div>
    <div class="bg">
      <nav class="navbar navbar-default title1">
        <div class="container-fluid">
          <div class="navbar-header">
            <a class="navbar-brand" href="#"><b>TriviaQuiz</b></a>
          </div>
          <div class="collapse navbar-collapse" id="navbar-content">
            <ul class="nav navbar-nav">
              <li><a href="student_dashboard.php">&nbsp;Home</a></li>
              <li class="active"><a href="results.php">&nbsp;Results</a></li>
              <li><a href="profile.php">&nbsp;Profile</a></li>
              <li class="pull-right"><a href="logout.php">&nbsp;&nbsp;&nbsp;&nbsp;Logout</a></li>
            </ul>
          </div>
        </div>
      </nav>
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <h2>Welcome <?php echo htmlspecialchars($sname); ?>!</h2>
            <h3>Your Quiz Results</h3>
            
            <?php if (empty($results)) { ?>
                <p>You have not attempted any quizzes yet.</p>
            <?php } else { ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Quiz Title</th>
                            <th>Score</th>
                            <!-- <th>Take Quiz Again</th> -->
                            <th>Give Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result) { ?>
                            <tr>
                                <td>
                                    <a href="quiz.php?qid=<?php echo htmlspecialchars($result['qid']); ?>">
                                        <?php echo htmlspecialchars($result['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($result['score']); ?></td>
                                <!-- <td>
                                    <a href="quiz.php?qid=<?php echo htmlspecialchars($result['qid']); ?>" class="btn btn-primary">Take Quiz Again</a>
                                </td> -->
                                <td>
                                    <a href="feedback.php?qid=<?php echo htmlspecialchars($result['qid']); ?>" class="btn btn-secondary">Give Feedback</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

  </body>
</html>