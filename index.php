
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Quiz</title>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
      crossorigin="anonymous"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="login.css" />
    <link rel="stylesheet" href="style.css" />
    <style>
      body {
        font-family: "Roboto", sans-serif;
      }
    </style>
  </head>

  <body>
    <header>
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">
            <img
              src="quizlogo.jpg"
              alt="Logo"
              style="height: 40px"
            />
          </a>
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="index.php"
                  >Home</a
                >
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#about">About Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#contact">Contact Us</a>
              </li>
            </ul>
            <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                <a class="nav-link" href="register.php">
                  <button class="btn login_btn">Login</button>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
    <main>
      <div class="container">
        <div class="card">
          <img
            src="quiz1.jpg"
            alt="Image 1"
          />
        </div>
        <div class="card">
          <img
            src="quiz2.jpg"
            alt="Image 2"
          />
        </div>
        <div class="card">
          <img
            src="quiz3.jpg"
            alt="Image 3"
          />
        </div>
      </div>

      <div class="section">
        <div class="con">
          <div class="content-section">
            <div class="title">
              <h1 id="about">ABOUT US</h1>
            </div>
            <div class="content" style="text-align:left;">
              <p>
                Welcome to our quiz platform, your ultimate destination for
                creating, managing, and participating in interactive quizzes!
                Our mission is to make learning and testing knowledge engaging
                and seamless for students, teachers, and administrators,
                fostering a thriving and dynamic educational community.
              </p>
              <h3>JOIN US IN MASTERING KNOWLEDGE</h3>
              <p>
                Whether you're a student eager to test your knowledge and
                challenge yourself, a teacher looking to create impactful and
                insightful quizzes, or an admin aiming to streamline quiz
                management and analytics, our quiz platform is here to empower
                you. Join us today and experience a smarter way to learn and
                grow.
              </p>
            </div>
          </div>
          <div class="image-section">
            <img
              src="quiz4.webp"
              alt="About Us"
            />
          </div>
        </div>
      </div>
      <br />
      <section class="contact">
        <div class="cont">
 <!--          <h2 id="contact">CONTACT US</h2>
          <div class="contact-wrapper">
            <div class="contact-form">
              <h3>Send Us a Message</h3>
              <form action="https://api.web3forms.com/submit" method="POST">
                <div class="form-group">
                  <input
                    type="hidden"
                    name="access_key"
                    value="166f1d31-2790-491a-8c35-3ac598838ad1"
                  />
                </div>

                <div class="form-group">
                  <input
                    type="text"
                    name="name"
                    placeholder="Your Name"
                    required
                  />
                </div>

                <div class="form-group">
                  <input
                    type="email"
                    name="email"
                    placeholder="Your Email"
                    required
                  />
                </div>
                <div class="form-group">
                  <textarea
                    name="message"
                    placeholder="Your Message"
                    required
                  ></textarea>
                </div>
                <button type="submit">Send Message</button>
              </form>
            </div> -->
            <div class="contact-info">
              <h3>Contact Information</h3>
              <p><b>PHONE NO:</b>+91 6202924319</p>
              <p><b>EMAIL:</b>admin2024@gmail.com</p>
              <p><b>ADDRESS:</b> CUCEK</p>
            </div>
          </div>
        </div>
      </section>
      <br />
    </main>
  </body>
</html>
