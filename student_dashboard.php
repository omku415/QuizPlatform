<?php
session_start(); 
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: register.php');
    exit();
}

$sid = $_SESSION['sid'] ?? null;
$sname = $_SESSION['sname'] ?? null;
if (!$sid) {
    header('Location: register.php');
    exit();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Quiz Platform</title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="bootstrap.min.css" />
    <link rel="stylesheet" href="bootstrap-theme.min.css" />
    <link rel="stylesheet" href="main.css" />
    <link rel="stylesheet" href="font.css" />
    <link
      href="http://fonts.googleapis.com/css?family=Roboto:400,700,300"
      rel="stylesheet"
      type="text/css"
    />
    <script src="jquery.js" type="text/javascript"></script>
    <script src="bootstrap.min.js" type="text/javascript"></script>
  </head>
  <body>
    <!-- Header -->
    <div class="header">
      <div class="row">
        <div class="col-lg-6">
          <span class="logo"></span>
        </div>
        <div class="col-md-4 col-md-offset-2">
          <span class="pull-right top title1">
            <span class="log1">
<!--             <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
              &nbsp;&nbsp;&nbsp;&nbsp;Hello, <strong>User</strong>
            </span> -->
            <!-- <a href="logout.php" class="log">
              <span
                class="glyphicon glyphicon-log-out"
                aria-hidden="true"
              ></span
              >&nbsp;Logout
            </a> -->
          </span>
        </div>
      </div>
    </div>
    <div class="bg">
      <nav class="navbar navbar-default title1">
        <div class="container-fluid">
          <div class="navbar-header">
            <button
              type="button"
              class="navbar-toggle collapsed"
              data-toggle="collapse"
              data-target="#navbar-content"
              aria-expanded="false"
            >
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><b>TriviaQuiz</b></a>
          </div>
          <div class="collapse navbar-collapse" id="navbar-content">
            <ul class="nav navbar-nav">
              <li class="active">
                <a href="index.php"
                  ><span
                    class=""
                    aria-hidden="true"
                  ></span
                  >&nbsp;Home</a
                >
              </li>

              <li>
                <a href="profile.php"
                  ><span
                    class=""
                    aria-hidden="true"
                  ></span
                  >&nbsp;Profile</a
                >
              </li>
              <li>
                <a href="results.php"
                  ><span
                    class=""
                    aria-hidden="true"
                  ></span
                  >&nbsp;Results</a
                >
              </li>
              <li class="pull-right">
                <a href="logout.php"
                  ><span
                    class=""
                    aria-hidden="true"
                  ></span
                  >&nbsp;&nbsp;&nbsp;&nbsp;Logout</a
                >
              </li>
            </ul>
            <form class="navbar-form navbar-left" role="search" method="GET" action="search_quizzes.php">
    <div class="form-group">
        <input
            type="text"
            class="form-control"
            name="query"
            placeholder="Enter subject name"
            required
        />
    </div>
    <button type="submit" class="btn btn-default">
        <!-- <span class="glyphicon glyphicon-search" aria-hidden="true"></span> -->
        &nbsp;Search
    </button>
</form>
          </div>
        </div>
      </nav>
      <div class="container">
      <div class="row">
    <div class="col-md-12">
    <h2>Welcome, <?=htmlspecialchars($sname) ?></h2>
            <p>
              Enter a subject to attend the quiz
            </p>
    <h2>Available Quizzes</h2>
<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=quiz', 'root', '');

    $currentDate = date('Y-m-d');
    $query = "SELECT title, subject, sdate, edate 
              FROM quizzes 
              WHERE edate >= :currentDate 
              ORDER BY sdate DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([':currentDate' => $currentDate]);

    $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($quizzes)) {
        echo '<table class="table table-bordered">';
        echo '<thead><tr><th>Quiz Title</th><th>Subject</th><th>Start Date</th><th>End Date</th></tr></thead><tbody>';
        foreach ($quizzes as $quiz) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($quiz['title']) . '</td>';
            echo '<td>' . htmlspecialchars($quiz['subject']) . '</td>';
            echo '<td>' . htmlspecialchars($quiz['sdate']) . '</td>';
            echo '<td>' . htmlspecialchars($quiz['edate']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p class="alert alert-info">No quizzes available at the moment. Please check back later.</p>';
    }
} catch (PDOException $e) {
    echo '<p class="alert alert-danger">Error fetching quizzes: ' . $e->getMessage() . '</p>';
}
?>


          </div>
        </div>
          </div>
        </div>
      </div>
    </div>
   <!--  <div class="row footer">
      <div class="col-md-3 box">
        <a href="http://www.projectworlds.in/online-examination" target="_blank"
          >About us</a
        >
      </div>
      <div class="col-md-3 box">
        <a href="#" data-toggle="modal" data-target="#login">Admin Login</a>
      </div>
      <div class="col-md-3 box">
        <a href="#" data-toggle="modal" data-target="#developers">Developers</a>
      </div>
      <div class="col-md-3 box">
        <a href="feedback.html" target="_blank">Feedback</a>
      </div>
    </div> -->
    <div class="modal fade title1" id="developers">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>
            <h4 class="modal-title" style="font-family: 'typo'">
              <span style="color: orange">Developers</span>
            </h4>
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-md-4">
                <img
                  src="quiz3.jpg"
                  width="100"
                  height="100"
                  alt="quiz"
                  class="img-rounded"
                />
              </div>
              <div class="col-md-5">
                <a
                  href="#"
                  style="color: #202020; font-family: 'typo'; font-size: 18px"
                  title="Find on Facebook"
                  >Quiz Group</a
                >
                <h4
                  style="color: #202020; font-family: 'typo'; font-size: 16px"
                  class="title1"
                >
                  +91 6202924319
                </h4>
                <h4 style="font-family: 'typo'">quizgroup@gmail.com</h4>
                <h4 style="font-family: 'typo'">
                  Cochin University College of Engineering Kuttanad (CUCEK)
                </h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="login">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">&times;</span>
              <span class="sr-only">Close</span>
            </button>
            <h4 class="modal-title">
              <span style="color: orange; font-family: 'typo'">LOGIN</span>
            </h4>
          </div>
          <div class="modal-body title1">
            <div class="row">
              <div class="col-md-3"></div>
              <div class="col-md-6">
                <form role="form" method="post" action="admin.php?q=index.php">
                  <div class="form-group">
                    <input
                      type="text"
                      name="uname"
                      maxlength="20"
                      placeholder="Admin user id"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group">
                    <input
                      type="password"
                      name="password"
                      maxlength="15"
                      placeholder="Password"
                      class="form-control"
                    />
                  </div>
                  <div class="form-group" align="center">
                    <input
                      type="submit"
                      name="login"
                      value="Login"
                      class="btn btn-primary"
                    />
                  </div>
                </form>
              </div>
              <div class="col-md-3"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
