# 🧠 Quiz Platform

A web-based Quiz Platform that allows teachers to create quizzes, students to attend them, and admins to manage users and approvals.

---

## 🛠️ Tech Stack

- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: PHP  
- **Database**: MySQL  

---

## 🚀 Features & Functionality

### 👑 3.1. ADMIN MANAGEMENT

- ✅ Verify and approve/reject teacher registration by reviewing uploaded qualification certificates.
- ✅ Delete any user account (teacher or student).

---

### 📝 3.2. USER REGISTRATION

- **Teacher Registration**:
  - Inputs: Name, Email, Phone Number, Password
  - Must upload a qualification certificate (PDF/JPG/PNG)
- **Student Registration**:
  - Inputs: Name, Email, Phone Number, Password

---

### 🔐 3.3. LOGIN

- **Teacher/Student Login**: via Email and Password.
- **Admin Login**: via Admin Email and Password.

---

### 🧾 3.4. QUIZ MANAGEMENT (Teacher)

- Create new MCQ quizzes:
  - Inputs: Subject Name, Questions, Choices, Correct Answer, Marks per Question, Total Marks, Quiz Duration.
- Edit existing quizzes.
- Delete quizzes.
- View results of all students who attempted their quizzes.
- Auto-grade quiz and assign scores to students.

---

### ❓ 3.5. QUESTION MANAGEMENT

- Add new questions to an existing quiz.
- Edit questions in an existing quiz.
- Delete questions from a quiz.
- Upload images with questions (e.g., diagrams, graphs).

---

### 🎓 3.6. STUDENT MANAGEMENT

- Students can:
  - Start and attend quizzes.
  - View their quiz results in their profile.
  - Submit feedback on quizzes (visible to the quiz creator teacher).

---

## 🔒 Roles & Permissions

| Role     | Permissions |
|----------|-------------|
| **Admin**    | Approve/reject teachers, delete users |
| **Teacher**  | Create/edit/delete quizzes & questions, view student results |
| **Student**  | Attend quizzes, view results, give feedback |






