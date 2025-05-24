<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    
    header("Location: ./LoginSignup/login.php"); 
    exit();
}

$loggedInUserId = $_SESSION['user_id']; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $servername = "localhost"; 
    $username = "root";     
    $password = "";        
    $dbname = "auth_system"; 

    
    $conn = new mysqli($servername, $username, $password, $dbname);

    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $city = htmlspecialchars($_POST["city"] ?? "");
    $country = htmlspecialchars($_POST["country"] ?? "");
    $start_date = htmlspecialchars($_POST["start_date"] ?? "");
    $end_date = htmlspecialchars($_POST["end_date"] ?? "");
    $budget = htmlspecialchars($_POST["budget"] ?? "");
    $travelers = htmlspecialchars($_POST["travelers"] ?? ""); 
    $additional_notes = htmlspecialchars($_POST["additional_notes"] ?? "");

    
    $activitiesArray = $_POST["activities"] ?? [];
    $activities = implode(", ", array_map('htmlspecialchars', $activitiesArray));

    $infoArray = $_POST["info"] ?? [];
    $infoPreferences = implode(", ", array_map('htmlspecialchars', $infoArray));

   
    $stmt = $conn->prepare("INSERT INTO trips (user_id, city, country, activities, info_preferences, submission_date, start_date, end_date, budget, travelers, additional_notes)
                           VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)");

    $stmt->bind_param("isssssssss", $loggedInUserId, $city, $country, $activities, $infoPreferences, $start_date, $end_date, $budget, $travelers, $additional_notes);

    if ($stmt->execute()) {
        
        header("Location: Travelog.php?status=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    
    header("Location: Trip Planner.php"); 
}
?>