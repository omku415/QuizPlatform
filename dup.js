class QuizPlatform {
    constructor() {
        this.wrapper = document.querySelector('.wrapper');
        this.loginLink = document.querySelector('.login-link');
        this.registerLink = document.querySelector('.register-link');
        this.iconClose = document.querySelector('.icon-close');
        this.buttonBox = document.querySelector('.button-box');
        this.switchToTeacherBtn = document.querySelector('.switch-to-teacher');
        this.switchToStudentBtn = document.querySelector('.switch-to-student');
        this.teacherForm = document.querySelector('.teacher-form');
        this.studentForm = document.querySelector('.student-form');
        this.adminLoginForm = document.querySelector('.admin-login');
        this.backButtons = document.querySelectorAll('.back-button');
        this.userLoginForm = document.querySelector('.user-login');
        
        this.namePattern = /^[A-Za-z\s]{3,}$/;

        this.init();
    }

    init() {
        this.registerLink?.addEventListener('click', () => this.showRegistrationForm());
        this.loginLink?.addEventListener('click', () => this.showLoginForm());
        this.iconClose?.addEventListener('click', () => this.closePopup());
        this.switchToTeacherBtn?.addEventListener('click', () => this.switchToTeacherForm());
        this.switchToStudentBtn?.addEventListener('click', () => this.switchToStudentForm());
        this.backButtons.forEach(button =>
            button.addEventListener('click', () => this.showButtonBox())
        );

        const adminLoginLink = document.querySelector('.admin-login-link');
        adminLoginLink?.addEventListener('click', () => this.showAdminLoginForm());
        const userLoginLink = document.querySelector('.user-login-link');
        userLoginLink?.addEventListener('click', () => this.showUserLoginForm());

        this.setupPasswordToggle('#teacherPassword', '#togglePasswordTourist');
        this.setupPasswordToggle('#teacherConfirmPassword', '#toggleConfirmPasswordTeacher');
        this.setupPasswordToggle('#studentPassword', '#togglePasswordGuide');
        this.setupPasswordToggle('#studentConfirmPassword', '#toggleConfirmPasswordGuide');

        this.validateNameInput('teacherName', 'teacherNameError');
        this.validateNameInput('studentName', 'studentNameError');

        this.validatePasswordsMatch('teacherPassword', 'teacherConfirmPassword', 'teacherPasswordError');
        this.validatePasswordsMatch('studentPassword', 'studentConfirmPassword', 'studentPasswordError');
    }

    showRegistrationForm() {
        this.userLoginForm.style.display = 'none';
        this.wrapper.classList.add('active');
    }

    showLoginForm() {
        this.wrapper.classList.remove('active');
        this.userLoginForm.style.display = 'block';
    }

    closePopup() {
        this.wrapper.classList.remove('active-popup');
    }

    switchToTeacherForm() {
        this.buttonBox.style.display = 'none';
        this.teacherForm.style.display = 'block';
        this.studentForm.style.display = 'none';
    }

    switchToStudentForm() {
        this.buttonBox.style.display = 'none';
        this.studentForm.style.display = 'block';
        this.teacherForm.style.display = 'none';
    }

    showButtonBox() {
        this.buttonBox.style.display = 'block';
        this.teacherForm.style.display = 'none';
        this.studentForm.style.display = 'none';
    }

    showAdminLoginForm() {
        this.userLoginForm.style.display = 'none';
        this.adminLoginForm.style.display = 'block'; 
    }

    showUserLoginForm() {
        this.adminLoginForm.style.display = 'none'; 
        this.userLoginForm.style.display = 'block'; 
    }

    setupPasswordToggle(inputId, toggleId) {
        const passwordInput = document.querySelector(inputId);
        const toggleButton = document.querySelector(toggleId);

        toggleButton?.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            toggleButton.innerHTML = type === 'password'
                ? '<ion-icon name="eye-off-outline"></ion-icon>'
                : '<ion-icon name="eye-outline"></ion-icon>';
        });
    }


}

document.addEventListener('DOMContentLoaded', () => {
    new QuizPlatform();
});