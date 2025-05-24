<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR username=?");
    $stmt->bind_param("ss", $user, $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (!empty($row['lock_until']) && strtotime($row['lock_until']) > time()) {
            $_SESSION['error'] = "Account is locked. Please try again later.";
            header("Location: login.php");
            exit();
        }

        if (password_verify($password, $row['password'])) {
            $conn->query("UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE id = {$row['id']}");
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            header("Location: ../landing page.html");
            exit();
        } else {
            $conn->query("UPDATE users SET failed_attempts = failed_attempts + 1 WHERE id = {$row['id']}");
            $failed_attempts_result = $conn->query("SELECT failed_attempts FROM users WHERE id = {$row['id']}");
            $failed_attempts_row = $failed_attempts_result->fetch_assoc();
            $failed_attempts = $failed_attempts_row['failed_attempts'];

            if ($failed_attempts >= 3) {
                $lockout_time = time() + (1 * 60); // Lock for 5 minutes
                $conn->query("UPDATE users SET lock_until = FROM_UNIXTIME({$lockout_time}) WHERE id = {$row['id']}");
                $_SESSION['error'] = "Too many failed attempts. Your account is locked for 5 minutes.";
            } else {
                $_SESSION['error'] = "Incorrect username/email or password. You have " . (3 - $failed_attempts) . " attempts remaining.";
            }
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header("Location: login.php");
        exit();
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(to right, #6495ED, #4682B4);
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                background-image: url('0.jpg');
                background-size: cover;
            }
            .signup-container {
                background-color: white;
                border-radius: 15px;
                box-shadow: 0 0px 40px rgb(0, 51, 255);
                display: flex;
                width: 80%;
                max-width: 960px;
                overflow: hidden;
            }
            .left-section {
                background-image: url('4.jpg');
                background-size: cover;
                background-position: center;
                color: white;
                padding: 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                width: 50%;
                text-align: center;
            }
            .logo-container {
                width: 180px;
                height: auto;
                margin-bottom: 20px;
            }
            .logo-container img {
                display: block;
                width: 100%;
                height: auto;
            }
            .compass-container {
                position: relative;
                width: 80px;
                height: 80px;
                margin-bottom: 30px;
            }
            .compass {
                position: absolute;
                width: 100%;
                height: 100%;
                border: 2px solid rgb(255, 255, 255);
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.15);
                box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                animation: compassRotate 30s linear infinite;
                z-index: 1;
            }
            .compass .needle {
                position: absolute;
                width: 4px;
                height: 40%;
                background: rgb(0, 247, 255);
                border-radius: 2px;
                top: 20%;
                left: 50%;
                transform: translateX(-50%);
                box-shadow: 0 0 3px rgba(255, 255, 255, 0.8);
            }
            .compass .needle::before {
                content: '';
                position: absolute;
                top: 0px;
                left: 50%;
                transform: translateX(-50%);
                width: 0px;
                height: 0;
                border-left: 6px solid transparent;
                border-right: 6px solid transparent;
                border-bottom: 8px solid rgb(0, 247, 255);
                filter: drop-shadow(0 0 1px rgba(255, 255, 255, 0.7));
            }
            .compass .direction {
                position: absolute;
                font-weight: 700;
                color: white;
                font-size: 14px;
                user-select: none;
                text-shadow: 0 0 3px rgba(0, 0, 0, 0.7);
            }
            .compass .north { top: 5px; left: 50%; transform: translateX(-50%); }
            .compass .east { right: 5px; top: 50%; transform: translateY(-50%); }
            .compass .south { bottom: 5px; left: 50%; transform: translateX(-50%); }
            .compass .west { left: 5px; top: 50%; transform: translateY(-50%); }
            @keyframes compassRotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .left-section h2 {
                font-size: 2em;
                margin-bottom: 10px;
                margin-top: 0;
                width: 100%;
                padding: 20px;
                color: white;
                border-radius: 10px;
                cursor: default;
                text-shadow: 2px 2px 40px rgb(63, 124, 255);
                box-sizing: border-box;
            }
            .left-section p {
                font-size: 0.9em;
                margin-bottom: 20px;
                text-shadow: 2px 2px 50px rgb(63, 124, 255);
            }
            .left-section button {
                background-color: transparent;
                color: white;
                border: 2px solid white;
                padding: 12px 30px;
                border-radius: 25px;
                font-size: 1em;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .left-section button:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }
            .right-section {
                background-color: white;
                padding: 40px;
                width: 50%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                border-radius: 0 15px 15px 0;
            }
            .right-section h2 {
                color: #333;
                text-align: center;
                margin-bottom: 25px;
                font-size: 2em;
            }
            .right-section label {
                display: block;
                margin-top: 15px;
                color: #555;
                font-weight: bold;
                font-size: 0.95em;
            }
            .right-section input[type="text"],
            .right-section input[type="email"],
            .right-section input[type="password"] {
                width: calc(100% - 30px);
                padding: 10px 15px;
                margin-top: 5px;
                border: 1px solid #ddd;
                border-radius: 20px;
                box-sizing: border-box;
                font-size: 0.95em;
            }
            .right-section input[type="text"]:focus,
            .right-section input[type="email"]:focus,
            .right-section input[type="password"]:focus {
                outline: none;
                border-color: #6495ED;
                box-shadow: 0 0 5px rgba(100, 149, 237, 0.25);
            }
            .right-section button[type="submit"] {
                margin-top: 25px;
                width: 100%;
                padding: 12px;
                background: #6495ED;
                color: white;
                border: none;
                border-radius: 25px;
                cursor: pointer;
                font-size: 1.1em;
                transition: background-color 0.3s ease;
            }
            .right-section button[type="submit"]:hover {
                background: #4682B4;
            }
            .right-section .info {
                font-size: 0.8em;
                color: #777;
                margin-top: 8px;
            }
            .right-section .password-options {
                display: flex;
                align-items: center;
                margin-top: 10px;
                margin-bottom: 5px;
            }
            .right-section .password-options input[type="checkbox"] {
                margin-right: 5px;
            }
            .right-section .password-options label {
                margin-top: 0;
                font-weight: normal;
                font-size: 0.85em;
                color: #666;
            }
            .right-section .forgot-password {
                text-align: right;
                margin-top: 12px;
                font-size: 0.9em;
            }
            .right-section .signup-link {
                text-align: center;
                margin-top: 20px;
                font-size: 0.9em;
                color: #555;
            }
            .right-section .signup-link a {
                color: #6495ED;
                text-decoration: none;
            }
            .right-section .signup-link a:hover {
                text-decoration: underline;
            }
            .right-section .error-message {
                color: red;
                text-align: center;
                margin-bottom: 15px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="signup-container">
            <div class="left-section">
                <div class="logo-container">
                    <img src="logologo.png" alt="Logo">
                </div>
                <div class="compass-container">
                    <div class="compass" aria-label="Animated Compass Icon" role="img">
                        <div class="needle"></div>
                        <div class="direction north">N</div>
                        <div class="direction east">E</div>
                        <div class="direction south">S</div>
                        <div class="direction west">W</div>
                    </div>
                </div>
                <h2>Welcome Back!</h2>
                <p>Login to continue your adventure.</p>
            </div>
            <div class="right-section">
                <h2>Login</h2>
                <form action="login.php" method="POST" id="loginForm">
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<p class="error-message">' . htmlspecialchars($_SESSION['error']) . '</p>';
                        unset($_SESSION['error']);
                    }
                    ?>
                    <label for="email">Email or Username</label>
                    <input type="text" name="email" id="email" placeholder="Enter your email or username" required>
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <div class="password-options">
                        <input type="checkbox" id="show-password" onclick="togglePassword()">
                        <label for="show-password">Show Password</label>
                    </div>
                    <div class="forgot-password">
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                    <button type="submit">Login</button>
                    <div class="signup-link">
                        Don't have an account? <a href="signup.html">Sign up here</a>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function togglePassword() {
                var passwordField = document.getElementById('password');
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                } else {
                    passwordField.type = 'password';
                }
            }
        </script>
    </body>
    </html>
    <?php
}
?>
