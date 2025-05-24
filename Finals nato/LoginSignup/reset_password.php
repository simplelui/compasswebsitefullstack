<?php
require_once 'db.php'; 

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$show_form = true;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST['token'] ?? '';
    $new_pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    
    if ($new_pass !== $confirm) {
        $error = "Passwords do not match!";
    } 
    
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_pass)) {
        $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
    } else {
        // Changed 'reset_expires' to 'token_expiry' to match the database column name in forgot_password.php
        $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $userId = $user['id'];
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

           // Changed 'reset_expires' to 'token_expiry'
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $userId);
            $stmt->execute();

            $success = "Password has been reset. <a href='http://localhost/Finals%20nato/LoginSignup/login.php'>Click here to login</a>.";
            $show_form = false;
            $error = '';
        } else {
            $error = "Invalid or expired reset token.";
        }
    }
} else {
    // Initial load for token validation (only if token is in GET request)
    if (!empty($token)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result->fetch_assoc()) {
            $error = "Invalid or expired reset token.";
            $show_form = false; // Do not show form if token is invalid/expired on initial load
        }
    } else {
        $error = "Reset token missing.";
        $show_form = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Reset Password</title>
<style>
    /* All the CSS from reset_password.php is retained here */
    .compass {
        position: relative;
        width: 80px;
        height: 80px;
        border: 4px solid white;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        animation: compassRotate 30s linear infinite;
        margin-bottom: 15px;
        margin-top: 30px;
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
            top: 3px;
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
    .compass .north { top: 8px; left: 50%; transform: translateX(-50%); }
    .compass .east { right: 8px; top: 50%; transform: translateY(-50%); }
    .compass .south { bottom: 8px; left: 50%; transform: translateX(-50%); }
    .compass .west { left: 8px; top: 50%; transform: translateY(-50%); }
    @keyframes compassRotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @media (max-width: 600px) {
        .compass {
            width: 60px;
            height: 60px;
        }
        .compass .needle {
            height: 30px;
            width: 4px;
            top: 15px;
        }
        .compass .needle::before {
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 12px solid white;
        }
        .compass .direction {
            font-size: 12px;
        }
    }

   
    body {
        font-family: Arial, sans-serif;
        background-image: url('0.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }
    .reset-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 480px;
        background: none;
    }

    
    form {
        background: rgba(255, 255, 255, 0.84);
        padding: 30px;
        border-radius: 10px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 0px 40px rgb(0, 51, 255);
    }
    h2 {
        color: #333;
        margin-bottom: 25px;
    }
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 1rem;
    }
    input[type="password"]:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
    }
    button {
        margin-top: 30px;
        width: 100%;
        padding: 12px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1.1rem;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background: #0056b3;
    }

    
    .message {
        font-size: 1rem;
        margin-bottom: 15px;
    }
    .message.error {
        color: #d9534f;
    }
    .message.success {
        color: #28a745;
    }

    
    .checkbox-container {
        text-align: left;
        margin-top: 10px;
        font-size: 0.9rem;
        color: #555;
    }
    .checkbox-container input[type="checkbox"] {
        margin-right: 8px;
        transform: scale(1.1);
        vertical-align: middle;
        cursor: pointer;
    }
    .checkbox-container label {
        cursor: pointer;
        user-select: none;
    }

    
    h1 {
        color: white;
        text-shadow: 0 2px 8px #000;
        margin: 0;
    }
    h2 {
        color: black;

    }
    .tagline {
        font-style: italic;
        font-size: 1.2rem;
        margin-top: 0;
        opacity: 0.9;
        color: white;
        text-shadow: 0 2px 8px #000;
        margin-bottom: 25px;
    }
</style>
</head>
<body>
<div class="reset-container">

    <div class="compass" aria-label="Compass icon" role="img">
        <div class="needle"></div>
        <div class="direction north">N</div>
        <div class="direction east">E</div>
        <div class="direction south">S</div>
        <div class="direction west">W</div>
    </div>

    <h1>COMPASS</h1>
    <p class="tagline">Find your direction</p>

    <div class="form-wrapper">

        <?php if ($error): ?>
            <p class="message error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="message success"><?= $success ?></p>
        <?php endif; ?>

        <?php if ($show_form): ?>
            <form method="POST" novalidate>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <h2>Reset Your Password</h2>
                <input type="password" id="new_password" name="password" placeholder="Enter new password" required>

                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>

                <div class="checkbox-container">
                    <label>
                        <input type="checkbox" id="show_password" onclick="togglePassword()"> Show Passwords
                    </label>
                </div>
            
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    function togglePassword() {
        const pw1 = document.getElementById("new_password");
        const pw2 = document.getElementById("confirm_password");
        const isText = pw1.type === "text";
        pw1.type = isText ? "password" : "text";
        pw2.type = isText ? "password" : "text";
    }
</script>
</body>
</html>