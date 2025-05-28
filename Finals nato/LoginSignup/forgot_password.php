<?php
date_default_timezone_set('Asia/Manila');

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';
require_once 'db.php'; // Assuming db.php contains your database connection ($conn)

$message = '';
$reset_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']); // Trim whitespace from email

    // Check if email exists
    $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes')); // Using 15 minutes expiry

        // Save token and expiry
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expiry, $user['id']);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            $message = "Failed to update reset token. Please try again.";
            $reset_link = '';
        } else {
            // Send email
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = ''; // Your Gmail address
                $mail->Password = '';   // Your App Password
                $mail->SMTPSecure = 'tls';                 // Use 'tls' for port 587
                $mail->Port = 587;                         // Use 587 for TLS

                $mail->setFrom('your-email@gmail.com', 'Compass Trip Planner');
                $mail->addAddress($email);

                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Click the link below to reset your password:\n\n" .
                              "http://localhost/Finals%20nato/LoginSignup/reset_password.php?token=$token"; // Corrected path

                $mail->send();
                $message = "Reset link sent to your email.";
                $reset_link = "http://localhost/Finals%20nato/LoginSignup/reset_password.php?token=" . $token; // Corrected path for direct access button
            } catch (Exception $e) {
                $message = "Mailer Error: " . $mail->ErrorInfo;
            }
        }
    } else {
        $message = "No account found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Forgot Password</title>
<style>
    /* All the CSS from forgot_password.php is retained here */
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

    /* Body and container */
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
    .forgot-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 480px;
        background: none;
    }

    /* Form styling */
    form {
        background: rgba(255, 255, 255, 0.84);
        padding: 30px;
        border-radius: 10px;
        width: 90%;
        max-width: 400px;
        box-shadow: 0 0px 40px rgb(0, 51, 255);
    }
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
    }
    label {
        display: block;
        margin-top: 20px;
        color: #555;
        font-weight: bold;
    }
    input[type="email"] {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 1rem;
    }
    input[type="email"]:focus {
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

    /* Message styling */
    .message {
        margin-top: 15px;
        font-size: 1rem;
        color: #333;
        text-align: center;
    }
    .message.error {
        color: #d9534f;
    }
    .message.success {
        color: #28a745;
    }

    /* Reset link button */
    .center-btn {
        display: flex;
        justify-content: center;
        width: 100%;
    }
    .reset-link-btn {
        display: inline-block;
        margin-top: 15px;
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        transition: background-color 0.3s ease;
        text-align: center;
    }
    .reset-link-btn:hover {
        background-color: #0056b3;
    }

    /* Header text */
    h1 {
        color: white;
        text-shadow: 0 2px 8px #000;
        margin: 0;
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
<div class="forgot-container">

    <div class="compass" aria-label="Compass icon" role="img">
        <div class="needle"></div>
        <div class="direction north">N</div>
        <div class="direction east">E</div>
        <div class="direction south">S</div>
        <div class="direction west">W</div>
    </div>

    <h1>COMPASS</h1>
    <p class="tagline">Find your direction</p>

    <form method="POST" novalidate>
        <h2>Forgot Password</h2>

        <?php if ($message): ?>
            <p class="message <?= (strpos($message, 'No account') !== false || strpos($message, 'Failed') !== false || strpos($message, 'Mailer Error') !== false) ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <label for="email">Enter your email address</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" required autofocus>

        <button type="submit">Send Reset Link</button>

        <?php if ($reset_link && strpos($message, 'Reset link sent') !== false): // Only show button if email was successfully sent ?>
            <div class="center-btn">
                <a href="<?= htmlspecialchars($reset_link) ?>" class="reset-link-btn" target="_blank" rel="noopener noreferrer">
                    Reset Password
                </a>
            </div>
        <?php endif; ?>
    </form>

</div>
</body>
</html>
