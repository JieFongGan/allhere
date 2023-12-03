<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$companyname = $_SESSION['companyname'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Register - AllHere</title>
  <meta property="og:title" content="Register - AllHere" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="utf-8" />
  <meta property="twitter:card" content="summary_large_image" />

  <style data-tag="reset-style-sheet">
    html {
      line-height: 1.15;
    }

    body {
      margin: 0;
    }

    * {
      box-sizing: border-box;
      border-width: 0;
      border-style: solid;
    }

    p,
    li,
    ul,
    pre,
    div,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    figure,
    blockquote,
    figcaption {
      margin: 0;
      padding: 0;
    }

    button {
      background-color: transparent;
    }

    button,
    input,
    optgroup,
    select,
    textarea {
      font-family: inherit;
      font-size: 100%;
      line-height: 1.15;
      margin: 0;
    }

    button,
    select {
      text-transform: none;
    }

    button,
    [type="button"],
    [type="reset"],
    [type="submit"] {
      -webkit-appearance: button;
    }

    button::-moz-focus-inner,
    [type="button"]::-moz-focus-inner,
    [type="reset"]::-moz-focus-inner,
    [type="submit"]::-moz-focus-inner {
      border-style: none;
      padding: 0;
    }

    button:-moz-focus,
    [type="button"]:-moz-focus,
    [type="reset"]:-moz-focus,
    [type="submit"]:-moz-focus {
      outline: 1px dotted ButtonText;
    }

    a {
      color: inherit;
      text-decoration: inherit;
    }

    input {
      padding: 2px 4px;
    }

    img {
      display: block;
    }

    html {
      scroll-behavior: smooth
    }
  </style>
  <style data-tag="default-style-sheet">
    html {
      font-family: Inter;
      font-size: 16px;
    }

    body {
      font-weight: 400;
      font-style: normal;
      text-decoration: none;
      text-transform: none;
      letter-spacing: normal;
      line-height: 1.15;
      color: var(--dl-color-gray-black);
      background-color: var(--dl-color-gray-white);

    }
  </style>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&amp;display=swap"
    data-tag="font" />
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
    data-tag="font" />
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&amp;display=swap"
    data-tag="font" />
  <style>
    @keyframes fade-in-left {
      0% {
        opacity: 0;
        transform: translateX(-20px);
      }

      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }
  </style>
</head>

<body>
  <link rel="stylesheet" href="css/clientstyle.css" />
  <div>
    <link href="css/register.css" rel="stylesheet" />

    <div class="register-container">
      <div class="register-header">
        <header data-thq="thq-navbar" class="register-navbar-interactive">
          <a href="index.php" class="register-logo">AllHere</a>
          <div data-thq="thq-navbar-nav" class="register-desktop-menu">
            <nav class="register-links">
              <a href="index.php">Home</a>
              <a href="service.php" class="register-nav2">Service</a>
              <a href="about.php" class="register-nav4">About</a>
            </nav>
            <div class="register-buttons">
              <a href="login.php" class="register-login button">Login</a>
              <a href="register.php" class="register-register button">
                Register
              </a>
            </div>
          </div>
          <div data-thq="thq-burger-menu" class="register-burger-menu">
            <svg viewBox="0 0 1024 1024" class="register-icon">
              <path
                d="M128 554.667h768c23.552 0 42.667-19.115 42.667-42.667s-19.115-42.667-42.667-42.667h-768c-23.552 0-42.667 19.115-42.667 42.667s19.115 42.667 42.667 42.667zM128 298.667h768c23.552 0 42.667-19.115 42.667-42.667s-19.115-42.667-42.667-42.667h-768c-23.552 0-42.667 19.115-42.667 42.667s19.115 42.667 42.667 42.667zM128 810.667h768c23.552 0 42.667-19.115 42.667-42.667s-19.115-42.667-42.667-42.667h-768c-23.552 0-42.667 19.115-42.667 42.667s19.115 42.667 42.667 42.667z">
              </path>
            </svg>
          </div>
          <div data-thq="thq-mobile-menu" class="register-mobile-menu">
            <div class="register-nav">
              <div class="register-top">
                <span class="register-logo1">COMPANY</span>
                <div data-thq="thq-close-menu" class="register-close-menu">
                  <svg viewBox="0 0 1024 1024" class="register-icon02">
                    <path
                      d="M810 274l-238 238 238 238-60 60-238-238-238 238-60-60 238-238-238-238 60-60 238 238 238-238z">
                    </path>
                  </svg>
                </div>
              </div>
              <nav class="register-links1">
                <span class="register-nav11">Home</span>
                <span class="register-nav21">Service</span>
                <span class="register-nav41">About</span>
                <span class="register-nav5">Login</span>
              </nav>
              <div class="register-buttons1">
                <button class="register-login1 button">Login</button>
                <button class="register-register1 button">Register</button>
              </div>
            </div>
            <div>
              <svg viewBox="0 0 950.8571428571428 1024" class="register-icon04">
                <path
                  d="M925.714 233.143c-25.143 36.571-56.571 69.143-92.571 95.429 0.571 8 0.571 16 0.571 24 0 244-185.714 525.143-525.143 525.143-104.571 0-201.714-30.286-283.429-82.857 14.857 1.714 29.143 2.286 44.571 2.286 86.286 0 165.714-29.143 229.143-78.857-81.143-1.714-149.143-54.857-172.571-128 11.429 1.714 22.857 2.857 34.857 2.857 16.571 0 33.143-2.286 48.571-6.286-84.571-17.143-148-91.429-148-181.143v-2.286c24.571 13.714 53.143 22.286 83.429 23.429-49.714-33.143-82.286-89.714-82.286-153.714 0-34.286 9.143-65.714 25.143-93.143 90.857 112 227.429 185.143 380.571 193.143-2.857-13.714-4.571-28-4.571-42.286 0-101.714 82.286-184.571 184.571-184.571 53.143 0 101.143 22.286 134.857 58.286 41.714-8 81.714-23.429 117.143-44.571-13.714 42.857-42.857 78.857-81.143 101.714 37.143-4 73.143-14.286 106.286-28.571z">
                </path>
              </svg><svg viewBox="0 0 877.7142857142857 1024" class="register-icon06">
                <path
                  d="M585.143 512c0-80.571-65.714-146.286-146.286-146.286s-146.286 65.714-146.286 146.286 65.714 146.286 146.286 146.286 146.286-65.714 146.286-146.286zM664 512c0 124.571-100.571 225.143-225.143 225.143s-225.143-100.571-225.143-225.143 100.571-225.143 225.143-225.143 225.143 100.571 225.143 225.143zM725.714 277.714c0 29.143-23.429 52.571-52.571 52.571s-52.571-23.429-52.571-52.571 23.429-52.571 52.571-52.571 52.571 23.429 52.571 52.571zM438.857 152c-64 0-201.143-5.143-258.857 17.714-20 8-34.857 17.714-50.286 33.143s-25.143 30.286-33.143 50.286c-22.857 57.714-17.714 194.857-17.714 258.857s-5.143 201.143 17.714 258.857c8 20 17.714 34.857 33.143 50.286s30.286 25.143 50.286 33.143c57.714 22.857 194.857 17.714 258.857 17.714s201.143 5.143 258.857-17.714c20-8 34.857-17.714 50.286-33.143s25.143-30.286 33.143-50.286c22.857-57.714 17.714-194.857 17.714-258.857s5.143-201.143-17.714-258.857c-8-20-17.714-34.857-33.143-50.286s-30.286-25.143-50.286-33.143c-57.714-22.857-194.857-17.714-258.857-17.714zM877.714 512c0 60.571 0.571 120.571-2.857 181.143-3.429 70.286-19.429 132.571-70.857 184s-113.714 67.429-184 70.857c-60.571 3.429-120.571 2.857-181.143 2.857s-120.571 0.571-181.143-2.857c-70.286-3.429-132.571-19.429-184-70.857s-67.429-113.714-70.857-184c-3.429-60.571-2.857-120.571-2.857-181.143s-0.571-120.571 2.857-181.143c3.429-70.286 19.429-132.571 70.857-184s113.714-67.429 184-70.857c60.571-3.429 120.571-2.857 181.143-2.857s120.571-0.571 181.143 2.857c70.286 3.429 132.571 19.429 184 70.857s67.429 113.714 70.857 184c3.429 60.571 2.857 120.571 2.857 181.143z">
                </path>
              </svg><svg viewBox="0 0 602.2582857142856 1024" class="register-icon08">
                <path
                  d="M548 6.857v150.857h-89.714c-70.286 0-83.429 33.714-83.429 82.286v108h167.429l-22.286 169.143h-145.143v433.714h-174.857v-433.714h-145.714v-169.143h145.714v-124.571c0-144.571 88.571-223.429 217.714-223.429 61.714 0 114.857 4.571 130.286 6.857z">
                </path>
              </svg>
            </div>
          </div>
        </header>
      </div>
      <div class="register-hero"></div>
      <div class="register-gallery">
        <div class="register-gallery1">
          <div class="register-hero1">
            <div class="register-container1" style="max-width: 500px;">
              <h1 class="register-text">Full details</h1>
              <h2 class="register-text1">
                Sign up your company entry and plan
              </h2>
              <br>
              <form action="fulldetailsql.php" method="POST">
                <div class="login-input-group">
                  <label for="company-name" class="register-text2">Company Name : </label>
                  <input type="text" id="company_name" name="company_name" placeholder="Enter your company name"
                    class="input register-textinput" required />
                </div>
                <br>
                <div class="login-input-group">
                  <label for="company-email" class="register-text2">Company Email : </label>
                  <input type="text" id="company_email" name="company_email" placeholder="Enter company email"
                    class="input register-textinput" required />
                </div>
                <br>
                <div class="login-input-group">
                  <label for="phone-number" class="register-text2">Phone Number : </label>
                  <input type="tel" id="company_phone_number" name="company_phone_number"
                    placeholder="Enter company phone number" class="input register-textinput" style="width: 264px;"
                    required />
                </div>
                <br>
                <div class="login-input-group">
                  <label for="address" class="register-text2">Address : </label>
                  <textarea id="company_address" name="company_address" placeholder="Enter company address"
                    class="input register-textinput" style="height: 168px; width: 308px;" required></textarea>
                </div>
                <br>
                <div class="login-input-group">
                  <label for="auth-code" class="register-text2">Authentication Code : </label>
                  <input type="text" id="auth_code" name="auth_code" placeholder="Enter authentication code"
                    class="input register-textinput" required />
                </div>
                <br>
                <div class="login-input-group">
                  <label class="login-label">Haven't have a authentication code?</label>
                  <a href="payment.php" class="login-text2" style="color: blue;">Here</a>
                </div>
                <br>
                    <?php
                    // Check if an error message is set
                    if (isset($_SESSION['error_message'])) {

                      echo '<div class="login-input-group">';
                      echo '<label class="register-text2">Invalid Input:</label>';
                      echo '<div class="invalid-input" id="invalid_input">';
                      echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
                      echo '</div>';
                      echo '</div>';
                      // Unset the session variable to clear the error message
                      unset($_SESSION['error_message']);
                      session_destroy();
                    }
                    ?>
                
            </div>
            <div class="register-container1">
              <h2 class="register-text1">
                User Register
              </h2>
              <br>
              <div class="login-input-group">
                <label for="username" class="register-text2">Username : </label>
                <input type="text" id="username" name="username" placeholder="Enter your username"
                  class="input register-textinput" required />
              </div>
              <br>
              <div class="login-input-group">
                <label for="password" class="register-text2">Password : </label>
                <input type="password" id="password" name="password" placeholder="Enter your password"
                  class="input register-textinput" required />
              </div>
              <br>
              <div class="login-input-group">
                <label for="email" class="register-text2">Email : </label>
                <input type="email" id="email" name="email" placeholder="Enter your email"
                  class="input register-textinput" required />
              </div>
              <br>
              <div class="login-input-group">
                <label for="phone-number" class="register-text2">Phone Number : </label>
                <input type="tel" id="phone_number" name="phone_number" placeholder="Enter your phone number"
                  class="input register-textinput" required />
              </div>
              <br>
              <div class="login-input-group">
                <label for="first-name" class="register-text2">First Name : </label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name"
                  class="input register-textinput" required />
              </div>
              <br>
              <div class="login-input-group">
                <label for="last-name" class="register-text2">Last Name : </label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name"
                  class="input register-textinput" required />
              </div>
              <br>
              <div class="register-btn-group">
                <button type="submit" class="register-button button">Sign Up</button>
                <button type="reset" class="register-button1 button">Clear</button>
              </div>
              </form>
              <div class="login-input-group">
                <label class="login-label">Already have an account? </label>
                <a href="login.php" class="login-text2" style="color: blue;">Login</a>
              </div>
            </div>
            <div class="register-container1"></div>
            <br>
          </div>
        </div>

      </div>
      <div class="register-footer">
        <footer class="register-footer1">
          <div class="register-container2">
            <span class="register-logo2">AllHere</span>
            <nav class="register-nav1">
              <a href="index.php" class="register-nav12">Home</a>
              <a href="service.php" class="register-nav22">Service</a>
              <a href="about.php" class="register-nav42">About</a>
            </nav>
          </div>
          <div class="register-separator"></div>
          <div class="register-container3">
            <span class="register-text3">
              © 2023 AllHere, All Rights Reserved.
            </span>
            <div class="register-icon-group1"></div>
          </div>
        </footer>
      </div>
    </div>
  </div>
  <script data-section-id="navbar" src="https://unpkg.com/@teleporthq/teleport-custom-scripts"></script>
</body>

</html>