<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../index.html"); // Corrected path
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
</head>
<body>
<h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
<p>You have successfully signed up and logged in.</p>
<a href="logout.php">Log out</a>
</body>
</html>

