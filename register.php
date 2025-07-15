<?php
require_once 'database.php'; 
session_start(); 

class Registration {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function registerTeacher($data, $file) {
        $tname = $this->sanitize($data['tname']);
        $temail = $this->sanitize($data['temail']);
        $tphone = $this->sanitize($data['tphone']);
        $tpassword = password_hash($data['tpassword'], PASSWORD_BCRYPT);
        $tcert = $this->uploadFile($file['tcert'], 'uploads/');
    

        $sql = "SELECT * FROM teacher WHERE temail = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $temail);
        $stmt->execute();
        $result = $stmt->get_result();
    
  if ($result->num_rows > 0) {
            echo "<script>
                    window.onload = function() {
                        alert('The email address is already registered.');
                    }
                  </script>";
            return;
        }

    
        if ($tcert) {
            $sql = "INSERT INTO teacher (tname, temail, tphone, tpassword, tcert) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('sssss', $tname, $temail, $tphone, $tpassword, $tcert);
    
            if ($stmt->execute()) {
                $tid = $this->conn->insert_id;
                $_SESSION['tid'] = $tid;
                $_SESSION['user_type'] = 'teacher';
                $_SESSION['tname'] = $tname;
                header('Location: teacher_dashboard.php'); 
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Failed to upload certification file.";
        }
    }
    public function registerStudent($data) {
        $sname = $this->sanitize($data['sname']);
        $semail = $this->sanitize($data['semail']);
        $sphone = $this->sanitize($data['sphone']);
        $spassword = password_hash($data['spassword'], PASSWORD_BCRYPT);
    
        $sql = "SELECT * FROM student WHERE semail = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $semail);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            echo "<script>
            window.onload = function() {
                alert('The email address is already registered.');
            }
          </script>";
    return;
        }
    
        $sql = "INSERT INTO student (sname, semail, sphone, spassword) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssss', $sname, $semail, $sphone, $spassword);
    
        if ($stmt->execute()) {
            $sid = $this->conn->insert_id;
                $_SESSION['sid'] = $sid;
            $_SESSION['user_type'] = 'student';
            $_SESSION['sname'] = $sname;
            header('Location: student_dashboard.php'); 
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    public function login($email, $password) {

        $sql = "SELECT * FROM teacher WHERE temail = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 1) {
            $teacher = $result->fetch_assoc();
            if (password_verify($password, $teacher['tpassword'])) {
                $_SESSION['tid'] = $teacher['tid']; 
                $_SESSION['user_type'] = 'teacher';
                $_SESSION['tname'] = $teacher['tname'];
                header("Location: teacher_dashboard.php?tid=" . $teacher['tid']);
                exit();
            } else {
                echo "<script>
                    window.onload = function() {
                        alert('Invalid password.');
                    }
                  </script>";
            return;
            }
        }
    

        $sql = "SELECT * FROM student WHERE semail = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 1) {
            $student = $result->fetch_assoc();
            if (password_verify($password, $student['spassword'])) {
                $_SESSION['sid'] = $student['sid']; 
                $_SESSION['user_type'] = 'student';
                $_SESSION['sname'] = $student['sname'];
                header("Location: student_dashboard.php"); 
                exit();
            } else {
                echo "<script>
                window.onload = function() {
                    alert('Invalid password.');
                }
              </script>";
            }
        }
    
        echo "<script>
                window.onload = function() {
                    alert('Invalid email or password.');
                }
              </script>";
    }
  
    private function sanitize($input) {
        return htmlspecialchars(trim($input));
    }

    private function uploadFile($file, $destination) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $maxFileSize = 5 * 1024 * 1024;

        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowedExtensions) && $fileSize <= $maxFileSize) {
            $uniqueName = uniqid() . '.' . $fileExt;
            $filePath = $destination . $uniqueName;

            if (!is_dir($destination)) {
                mkdir($destination, 0777, true);
            }

            if (move_uploaded_file($fileTmp, $filePath)) {
                return $filePath;
            }
        }

        return false;
    }
}

$registration = new Registration();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tname'])) {
        $registration->registerTeacher($_POST, $_FILES);
    } elseif (isset($_POST['sname'])) {
        $registration->registerStudent($_POST);
    } elseif (isset($_POST['email']) && isset($_POST['password'])) {
        $registration->login($_POST['email'], $_POST['password']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Website</title>
    <link rel="icon" href="">
    <link rel="stylesheet" href="dup.css">
</head>
<body>
    <div class="wrapper">
        <span class="icon-close"><ion-icon name="close-outline"></ion-icon></span>
        
        <div class="form-box login user-login">
            <h2>Login</h2>
            <form action="register.php" method="post">
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>Email Id</label>
                </div>
                <div class="input-box">
                    <span class="icon-eye" id="togglePasswordLogin"><ion-icon name="eye-off-outline"></ion-icon></span>
                    <input type="password" name="password" id="loginPassword" required>
                    <label>Password</label>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="login-register">
                    <p>Don't have an account? <a href="#" class="register-link">Register</a></p>
                    <p>Not a user? <a href="#" class="admin-login-link">Admin</a></p>
                </div>
            </form>
        </div>
        <div class="form-box login admin-login" style="display:none;">
            <h2>Admin Login</h2>
            <form action="admin_login.php" method="post">
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="aemail" required>
                    <label>Email Id</label>
                </div>
                <div class="input-box">
                    <span class="icon-eye" id="togglePasswordAdmin"><ion-icon name="eye-off-outline"></ion-icon></span>
                    <input type="password" name="apassword" id="adminPassword" required>
                    <label>Password</label>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="login-register">
                    <p>Not an admin? <a href="#" class="user-login-link">User</a></p>
                </div>
            </form>
        </div>

        <div class="form-box register">
            <div class="button-box">
                <h2>Registration</h2>
                <button type="button" class="btn1 switch-to-teacher">Teacher</button>
                <button type="button" class="btn1 switch-to-student">Student</button>
                <p>Already have an account? <a href="#" class="login-link">Login</a></p>
            </div>
        </div>
    <div class="teacher-form" style="display:none;">
        <form action="register.php" method="post" enctype="multipart/form-data" id="teacherForm">
            <div class="input-box">
                <span class="icon"><ion-icon name="person-circle-outline"></ion-icon></span>
                <input type="text" name="tname" id="teacherName" required>
                <label>Name</label>
                <span class="error-message" id="teacherNameError"></span> 
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                <input type="email" name="temail" required>
                <label>Email</label>
                <span class="error-message"></span>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="call-outline"></ion-icon></span>
                <input type="text" name="tphone" id="teacherPhone" required>
                <label>Phone Number</label>
                <span class="error-message" id="teacherPhoneError"></span> 
            </div>
            <div class="input-box">
                <span class="icon-eye" id="togglePasswordTourist"><ion-icon name="eye-off-outline"></ion-icon></span>
                <input type="password" name="tpassword" id="teacherPassword" required>
                <label>Password</label>
                <span class="error-message" id="teacherPasswordLengthError"></span> 
            </div>
            <div class="input-box">
                <span class="icon-eye" id="toggleConfirmPasswordTeacher"><ion-icon name="eye-off-outline"></ion-icon></span>
                <input type="password" name="tconfirmpassword" id="teacherConfirmPassword" required>
                <label>Confirm Password</label>
                <span class="error-message" id="teacherPasswordError"></span>
            </div>
            <div class="file-upload-container">
                <label for="certification" class="upload-label" style="color:white;">Qualification:</label>
                <input type="file" id="certification" class="file-input" name="tcert" accept="image/*" style="display:none;" required>

                <button type="button" class="custom-upload-btn" id="uploadButton"><ion-icon name="arrow-up-outline"></ion-icon>Upload</button><br>
    
                <span class="error-message" id="fileError"></span>
            </div>
            <button type="submit" class="btn">Register</button>
            <p><button type="button" class="btn2 back-button"><span><ion-icon name="arrow-back-outline"></ion-icon>Back</span></button></p>
        </form>
    </div>
            <div class="student-form" style="display:none;">
                <form action="register.php" method="post" id="studentForm">
                <div class="input-box">
    <span class="icon"><ion-icon name="person-circle-outline"></ion-icon></span>
    <input type="text" name="sname" id="studentName" required>
    <label>Name</label>
    <span class="error-message" id="studentNameError"></span>
</div>
                    <div class="input-box">
                        <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                        <input type="email" name="semail" required>
                        <label>Email</label>
                        <span class="error-message"></span>
                    </div>
                    <div class="input-box">
    <span class="icon"><ion-icon name="call-outline"></ion-icon></span>
    <input type="text" name="sphone" id="studentPhone" required>
    <label>Phone Number</label>
    <span class="error-message" id="studentPhoneError"></span> 
</div>
                    <div class="input-box">
    <span class="icon-eye" id="togglePasswordGuide"><ion-icon name="eye-off-outline"></ion-icon></span>
    <input type="password" name="spassword" id="studentPassword" required>
    <label>Password</label>
    <span class="error-message" id="studentPasswordLengthError"></span> 
</div>
<div class="input-box">
    <span class="icon-eye" id="toggleConfirmPasswordGuide"><ion-icon name="eye-off-outline"></ion-icon></span>
    <input type="password" name="sconfirmpassword" id="studentConfirmPassword" required>
    <label>Confirm Password</label>
    <span class="error-message" id="studentPasswordError"></span> 
</div>
<button type="submit" class="btn">Register</button>
<p><button type="button" class="btn2 back-button"><span><ion-icon name="arrow-back-outline"></ion-icon>Back</span></button></p>
        </form>
    </div>
    </div>
    <script src="dup.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const uploadButton = document.getElementById("uploadButton");
    const fileInput = document.getElementById("certification");
    const fileError = document.getElementById("fileError");

    uploadButton.addEventListener("click", function () {
        fileInput.click(); 
    });

    const teacherForm = document.getElementById("teacherForm");
    const studentForm = document.getElementById("studentForm");


    const validateForm = (form) => {
        const errorMessages = form.querySelectorAll(".error-message");
        let isValid = true;

        errorMessages.forEach((error) => {
            if (error.textContent !== "") {
                isValid = false;
            }
        });

        return isValid;
    };

    teacherForm.addEventListener("submit", (event) => {
        if (!validateForm(teacherForm)) {
            event.preventDefault();
            alert("Please fix all errors before submitting the form.");
        }
    });

    studentForm.addEventListener("submit", (event) => {
        if (!validateForm(studentForm)) {
            event.preventDefault();
            alert("Please fix all errors before submitting the form.");
        }
    });

    fileInput.addEventListener("change", () => {
        const file = fileInput.files[0];
        const allowedExtensions = /(\.jpg|\.png)$/i;

        if (file) {
            if (!allowedExtensions.test(file.name)) {
                fileError.textContent = "JPG and PNG files.";
                fileInput.value = ""; 
            } else {
                fileError.textContent = ""; 
            }
        }
    });


    const validateNameOnInput = (nameInput, errorElement) => {
        nameInput.addEventListener("input", () => {
            const name = nameInput.value.trim();
            const namePattern = /^[A-Za-z\s]+$/;

            if (!namePattern.test(name)) {
                errorElement.textContent = "";
                nameInput.value = name.replace(/[^A-Za-z\s]/g, ""); 
            } else {
                errorElement.textContent = ""; 
            }
        });
    };

    const teacherNameInput = document.getElementById("teacherName");
    const studentNameInput = document.getElementById("studentName");
    const teacherNameError = document.getElementById("teacherNameError");
    const studentNameError = document.getElementById("studentNameError");

    validateNameOnInput(teacherNameInput, teacherNameError);
    validateNameOnInput(studentNameInput, studentNameError);
    document.getElementById('teacherForm').addEventListener('submit', function(event) {
    const password = document.getElementById('teacherPassword').value;
    const confirmPassword = document.getElementById('teacherConfirmPassword').value;

    if (password !== confirmPassword) {
        alert("Password and Confirm Password do not match.");
        event.preventDefault(); 
    }
});

document.getElementById('studentForm').addEventListener('submit', function(event) {
    const password = document.getElementById('studentPassword').value;
    const confirmPassword = document.getElementById('studentConfirmPassword').value;

    if (password !== confirmPassword) {
        alert("Password and Confirm Password do not match.");
        event.preventDefault(); 
    }
});

    const validateEmailOnBlur = (emailInput, errorElement) => {
        emailInput.addEventListener("blur", () => {
            const email = emailInput.value.trim();
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (email === "") {
                errorElement.textContent = ""; 
            } else if (!emailPattern.test(email)) {
                alert("Invalid email.");
                event.preventDefault(); 
            } else {
                errorElement.textContent = ""; 
            }
        });
    };


    const teacherEmailInput = document.querySelector('input[name="temail"]');
    const studentEmailInput = document.querySelector('input[name="semail"]');
    const teacherEmailError = teacherEmailInput.nextElementSibling;
    const studentEmailError = studentEmailInput.nextElementSibling;

    validateEmailOnBlur(teacherEmailInput, teacherEmailError);
    validateEmailOnBlur(studentEmailInput, studentEmailError);


    const validatePhoneNumberOnInput = (phoneInput, errorElement) => {
        phoneInput.addEventListener("input", () => {
            const phone = phoneInput.value.trim();
            const phonePattern = /^[0-9]*$/;

            if (!phonePattern.test(phone)) {
                errorElement.textContent = "Phone number must contain only digits.";
                phoneInput.value = phone.replace(/[^0-9]/g, ""); 
            } else {
                errorElement.textContent = ""; 
            }
        });

        phoneInput.addEventListener("blur", () => {
            const phone = phoneInput.value.trim();
            if (phone.length === 0) {
                errorElement.textContent = ""; 
            } else if (phone.length !== 10) {
                errorElement.textContent = "Phone number must be exactly 10 digits.";
            } else {
                errorElement.textContent = ""; 
            }
        });
    };

    const teacherPhoneInput = document.getElementById("teacherPhone");
    const studentPhoneInput = document.getElementById("studentPhone");
    const teacherPhoneError = document.getElementById("teacherPhoneError");
    const studentPhoneError = document.getElementById("studentPhoneError");

    validatePhoneNumberOnInput(teacherPhoneInput, teacherPhoneError);
    validatePhoneNumberOnInput(studentPhoneInput, studentPhoneError);
});
</script>
</body>
</html>