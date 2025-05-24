<?php
session_start(); // Start the session at the very beginning of the PHP file

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./LoginSignup/login.php"); // Redirect to your login page
    exit();
}

$loggedInUserId = $_SESSION['user_id']; // Get the logged-in user's ID

// Database connection details
$servername = "localhost"; // Usually "localhost"
$username = "root"; // Your database username (often "root" for local development)
$password = "";     // Your database password (empty for XAMPP/WAMP default root)
$dbname = "auth_system"; // The name of the database where 'trips' table is

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userTrips = []; // Array to store trips for the logged-in user

// Fetch trips for the logged-in user ONLY
// Modified: Added 'start_date', 'end_date', 'budget', 'travelers', 'additional_notes' to the SELECT statement
$stmt = $conn->prepare("SELECT id, city, country, activities, info_preferences, submission_date, start_date, end_date, budget, travelers, additional_notes FROM trips WHERE user_id = ? ORDER BY submission_date DESC");
$stmt->bind_param("i", $loggedInUserId); // This is the crucial line for user-specific data
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userTrips[] = $row;
    }
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Travelogs - Trip Planner</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Add the CSS from Travelog.html here directly or link an external stylesheet */
    :root {
      /* Shades of blue for primary, secondary, and accent */
      --primary: #2196f3; /* A vibrant blue */
      --secondary: #03a9f4; /* A lighter, bright blue */
      --accent: #90caf9; /* A even lighter blue, for subtle highlights */
      --bg-glass: rgba(227, 242, 253, 0.72); /* Light blue tint with transparency */
      --shadow: 0 8px 32px rgba(33, 150, 243, 0.13); /* Shadow with blue tint */
      --radius: 18px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(120deg, #e0f2f7 0%, #bbdefb 100%);
      color: #1a237e;
      min-height: 10vh;
      position: relative;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: url('https://www.transparenttextures.com/patterns/cubes.png');
      opacity: 0.05;
      pointer-events: none;
      z-index: 0;
    }

    /* Navigation */
    .top-nav {
      position: sticky;
      top: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 48px;
      background: var(--bg-glass);
      box-shadow: 0 8px 24px rgba(80,80,180,0.08);
      backdrop-filter: blur(14px);
      border-radius: 0 0 var(--radius) var(--radius);
      margin-bottom: 32px;
    }

    .header-logo-link {
      display: flex;
      align-items: center;
      gap: 14px;
      text-decoration: none;
    }

    .same-size-header-logo-link{
       display: flex;
      align-items: center;
      gap: 14px;
      text-decoration: none;
    }

    .header-logo {
      position: relative;
      height: 36px;
      width: 36px;
      overflow: hidden;
      border-radius: 50%;
      box-shadow: 0 4px 16px rgba(94, 234, 212, 0.4);
      background: #fff;
      transition: transform 0.2s;
      height: 100%;
      width: 100%;
      object-fit: cover;
    }

     .same-size-header-logo {
      position: relative;
      height: 36px;
      width: 36px;
      overflow: hidden;
      border-radius: 50%;
      box-shadow: 0 4px 16px rgba(94, 234, 212, 0.4);
      background: #fff;
      transition: transform 0.2s;
      height: 100%;
      width: 100%;
      object-fit: cover;
    }

    .header-logo:hover, .same-size-header-logo:hover {
      transform: scale(1.07) rotate(-4deg);
    }

    .logo-text {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--primary);
      letter-spacing: 1px;
    }

    .nav-links {
      display: flex;
      gap: 1.1rem;
    }

    .nav-link {
      text-decoration: none;
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(90deg, var(--secondary) 0%, var(--primary) 100%);
      padding: 10px 26px;
      border-radius: 13px;
      font-size: 1.05rem;
      box-shadow: 0 2px 10px rgba(99, 102, 241, 0.1);
      transition: all 0.25s;
      user-select: none;
      border: none;
      outline: none;
      display: inline-block;
    }

    .nav-link:hover, .nav-link:focus {
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      color: #fff;
      transform: scale(1.06) translateY(-2px);
      box-shadow: 0 6px 16px rgba(99, 102, 241, 0.2);
    }

    /* Main Content */
    .main-content {
      max-width: 1200px;
      margin: 0 auto 32px auto;
      padding: 0 18px;
      display: flex;
      flex-direction: column;
      gap: 64px;
    }


    /* Trip Planner Title Styling */
    .hero-title {
      position: relative;
      display: inline-block;
      font-family: 'Montserrat', sans-serif;
      font-size: 4rem;
      font-weight: 800;
      background: linear-gradient(90deg, #0d47a1 0%, #1976d2 50%, #2196f3 100%);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin-bottom: 16px;
      letter-spacing: -1px;
    }

    .hero-title::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 0;
      right: 0;
      height: 4px;
      width: 75%;
      margin: 0 auto;
      background: linear-gradient(90deg, #03a9f4 0%, #2196f3 100%);
      border-radius: 4px;
      transform: scaleX(0.8);
    }

    .hero-description {
      font-size: 1.125rem;
      color: #4a5568;
      margin-bottom: 32px;
      line-height: 1.6;
    }

    .hero-button {
      display: inline-flex;
      align-items: center;
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(90deg, var(--secondary) 0%, var(--primary) 100%);
      padding: 12px 32px;
      border-radius: 13px;
      font-size: 1.05rem;
      box-shadow: 0 4px 16px rgba(99, 102, 241, 0.15);
      transition: all 0.25s;
      text-decoration: none;
    }

    .hero-button:hover {
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(99, 102, 241, 0.2);
    }

    .hero-button-icon {
      margin-left: 8px;
    }

    .hero-content {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
    }


    /* Section Titles */
    .section-title {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.875rem;
      font-weight: 700;
      color: #232946;
      text-align: center;
      margin-bottom: 24px;
    }

    .section-title-accent {
      color: #2196f3;
    }

    .section-title-primary {
      color: var(--primary);
    }

    /* Adventure Carousel */
    .carousel-container {
      display: flex;
      justify-content: center;
      margin-bottom: 24px;
    }

    .adventure-carousel {
      position: relative;
      height: 320px;
      width: 100%;
      max-width: 640px;
      overflow: hidden;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(80, 80, 180, 0.1);
      cursor: pointer;
    }

    .carousel-slide {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      transition: opacity 0.5s ease;
      z-index: 0;
    }

    .carousel-slide.active {
      opacity: 1;
      z-index: 10;
    }

    .carousel-slide.transitioning {
      opacity: 0;
    }

    .carousel-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .adventure-carousel:hover .carousel-image {
      transform: scale(1.05);
    }

    .carousel-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);
      padding: 16px;
      display: flex;
      align-items: flex-end;
    }

    .carousel-label {
      color: white;
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.25rem;
    }

    .carousel-indicators {
      position: absolute;
      bottom: 8px;
      left: 0;
      right: 0;
      display: flex;
      justify-content: center;
      gap: 8px;
      z-index: 20;
    }

    .carousel-indicator {
      height: 8px;
      width: 8px;
      background-color: rgba(255, 255, 255, 0.5);
      border-radius: 50%;
      border: none;
      padding: 0;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .carousel-indicator.active {
      background-color: white;
      width: 16px;
      border-radius: 4px;
    }

    .zoom-indicator {
      position: absolute;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.2);
      opacity: 0;
      transition: opacity 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 15;
    }

    .adventure-carousel:hover .zoom-indicator {
      opacity: 1;
    }

    .zoom-icon-container {
      background-color: rgba(255, 255, 255, 0.8);
      border-radius: 50%;
      padding: 12px;
      transform: scale(0);
      transition: transform 0.3s ease;
    }

    .adventure-carousel:hover .zoom-icon-container {
      transform: scale(1);
    }

    .zoom-icon {
      color: var(--primary);
    }

    /* Lightbox */
    .lightbox {
      position: fixed;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.9);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }

    .lightbox.active {
      opacity: 1;
      pointer-events: auto;
    }

    .lightbox-close {
      position: absolute;
      top: 16px;
      right: 16px;
      background: none;
      border: none;
      color: white;
      font-size: 32px;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .lightbox-close:hover {
      color: #d1d5db;
    }

    .lightbox-content {
      position: relative;
      width: 100%;
      max-width: 1024px;
      height: 80vh;
    }

    .lightbox-image {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .lightbox-caption {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background-color: rgba(0, 0, 0, 0.5);
      padding: 16px;
      color: white;
    }

    .lightbox-title {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.5rem;
      margin-bottom: 4px;
    }

    .lightbox-subtitle {
      color: rgba(255, 255, 255, 0.8);
    }

    /* Adventure Cards - NEW APPROACH */
    .adventure-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }

    .adventure-card {
      background-color: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(80,80,180,0.1);
      transition: all 0.3s ease;
      border-left: 4px solid var(--primary);
      height: 100%;
      position: relative; /* Added for the badge */
    }

    .adventure-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(80,80,180,0.15);
    }

    /* New style for user's saved trip cards */
    .adventure-card.user-trip {
      border: 2px solid var(--secondary); /* Distinct border */
      box-shadow: 0 8px 24px rgba(94, 234, 212, 0.2); /* Different shadow */
      background-color: rgba(235, 255, 250, 0.8); /* Slightly different background */
    }

    .adventure-card.user-trip .card-icon-container {
      border: 2px solid var(--primary); /* Adjust icon border for user trips */
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }

    .card-content {
      padding: 24px;
      display: flex;
      flex-direction: column;
      gap: 16px;
      height: 100%;
    }

    .card-header {
      display: flex;
      align-items: center;
      gap: 16px;
      position: relative; /* For badge positioning */
    }

    .card-icon-container {
      position: relative;
      height: 64px;
      width: 64px;
      border-radius: 50%;
      border: 2px solid var(--secondary);
      background-color: white;
      padding: 8px;
      box-shadow: 0 4px 12px rgba(94, 234, 212, 0.2);
      flex-shrink: 0;
    }

    .card-icon {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 100px;
      transition: transform 0.3s ease;
    }

    .adventure-card:hover .card-icon {
      transform: scale(1.1) rotate(-3deg);
    }

    .card-location {
      display: flex;
      align-items: center;
      font-size: 0.875rem;
      color: #6b7280;
    }

    .location-icon {
      margin-right: 4px;
      color: var(--accent);
    }

    .card-body {
      margin-bottom: 8px;
    }

    .card-title {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.125rem;
      color: var(--accent);
      margin-bottom: 8px;
    }

    .card-description {
     color: #4b5563;
     display: -webkit-box;
     -webkit-line-clamp: 3; /* Number of lines to show */
     line-clamp: 3; /* Standard property for future compatibility */
     -webkit-box-orient: vertical;
     overflow: hidden;
    }

    .card-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: auto;
    }

    .card-button {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      color: white;
      background: linear-gradient(90deg, var(--secondary) 0%, var(--primary) 100%);
      padding: 8px 20px;
      border-radius: 8px;
      font-size: 0.875rem;
      border: none;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
      transition: all 0.3s ease;
    }

    .card-button:hover {
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
    }

    /* Badge for user trips */
    .user-trip-badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: var(--secondary); /* Or a different color to distinguish */
        color: white;
        padding: 4px 10px;
        border-bottom-left-radius: 16px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(94, 234, 212, 0.3);
    }

    /* New styles for the details box that appears below */
    .details-container {
      grid-column: 1 / -1; /* Span all columns */
      display: none;
      margin-top: 16px;
      margin-bottom: 16px;
      background-color: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(80,80,180,0.1);
      border-left: 4px solid var(--primary);
      animation: slideDown 0.3s ease-out;
      position: relative;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .details-container.active {
      display: block;
    }

    .details-close {
      position: absolute;
      top: 16px;
      right: 16px;
      background: none;
      border: none;
      color: var(--primary);
      font-size: 24px;
      cursor: pointer;
      transition: transform 0.3s ease;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }

    .details-close:hover {
      background-color: rgba(99, 102, 241, 0.1);
      transform: rotate(90deg);
    }

    .details-title {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--primary);
      margin-bottom: 16px;
      margin-left: 10px;
      margin-top: 10px;
    }

    .details-subtitle {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      color: var(--accent);
      margin-top: 16px;
      margin-bottom: 8px;
      margin-left: 10px
    }

    .details-text {
      color: #4b5563;
      line-height: 1.6;
      margin-bottom: 16px;
      margin-left: 10px
    }

    .details-list {
      margin-top: 12px;
      padding-left: 20px;
    }

    .details-list li {
      margin-bottom: 8px;
      color: #4b5563;
    }

    .card-button .button-icon {
      display: inline-block;
      margin-left: 6px;
      transition: transform 0.3s ease;
    }

    .card-button.active .button-icon {
      transform: rotate(180deg);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .adventure-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .top-nav {
        padding: 16px 24px;
      }

      .hero-section {
        padding: 24px;
      }

      .hero-title {
        font-size: 2.5rem;
      }

      .adventure-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 640px) {
      .top-nav {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
      }

      .nav-links {
        width: 100%;
        justify-content: space-between;
      }

      .nav-link {
        padding: 8px 16px;
        font-size: 0.875rem;
      }

      .hero-section {
        padding: 20px;
      }

      .hero-title {
        font-size: 2rem;
      }

      .hero-description {
        font-size: 1rem;
      }

      .adventure-carousel {
        height: 240px;
      }
  </style>
</head>
<body>
  <nav class="top-nav">
    <a href="landing page.html" class="header-logo-link same-size-header-logo-link">
      <img src="Compass_Site/Assets/images/compass_logo.gif" alt="Compass Logo" class="header-logo same-size-header-logo" />
    </a>
    <div class="nav-links">
      <a href="Trip Planner.php" class="nav-link">Plan Trip</a>
      <a href="Destination.html" class="nav-link">Destinations</a>
      <a href="./LoginSignup/login.php" class="nav-link">Logout</a>
    </div>
  </nav>

  <main class="main-content">
    <section class="hero-section">
      <div class="hero-circle-1"></div>
      <div class="hero-circle-2"></div>
      <div class="hero-content">
        <h1 class="hero-title">Travelog</h1>
      </div>
    </section>

    <section>
      <h2 class="section-title">
        Popular <span class="section-title-accent">Adventures</span>
      </h2>

      <div class="carousel-container">
        <div class="adventure-carousel" id="adventure-carousel">
          <div class="carousel-indicators" id="carousel-indicators">
            </div>
          <div class="zoom-indicator">
            <div class="zoom-icon-container">
              <svg class="zoom-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="11" y1="8" x2="11" y2="14"></line>
                <line x1="8" y1="11" x2="14" y2="11"></line>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section>
      <h2 class="section-title">
        Recent <span class="section-title-primary">Adventures</span>
      </h2>

      <div class="adventure-grid" id="adventure-grid">
        <article class="adventure-card" id="card-1">
          <div class="card-content">
            <div class="card-header">
              <div class="card-icon-container">
                <img src="images/kayaklogo.jpg" alt="Kayak Icon" class="card-icon" />
              </div>
              <div class="card-location">
                <svg class="location-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 118 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Rutan Islands</span>
              </div>
            </div>
            <div class="card-body">
              <h3 class="card-title">Conquering the rapids of the Rutan Islands</h3>
              <p class="card-description">
                Definitely our craziest journey ever! A beautiful collage of nature. Rapid reaching nearly 50 mph, more than a dozen of waterfalls (various sizes), and some killer rocks gave us the biggest rush. Nothing beats the feeling of complete loss of control! The Rutan Islands also has a lighter, more relaxing side -- check out the local villages.
              </p>
            </div>

            <div class="card-actions">
              <button class="card-button" data-details="details-1">
                More Details
                <span class="button-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </span>
              </button>
            </div>
          </div>
        </article>

        <article class="adventure-card" id="card-2">
          <div class="card-content">
            <div class="card-header">
              <div class="card-icon-container">
                <img src="images/climblogo.png" alt="Climbing Icon" class="card-icon" />
              </div>
              <div class="card-location">
                <svg class="location-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Manurai</span>
              </div>
            </div>

            <div class="card-body">
              <h3 class="card-title">Scaling mountains in Manurai</h3>
              <p class="card-description">
                Some of the steepest cliffs around! My buddy and I began our 3 day scale above the majestic raging watersf of Nanna
              </p>
            </div>

            <div class="card-actions">
              <button class="card-button" data-details="details-2">
                More Details
                <span class="button-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </span>
              </button>
            </div>
          </div>
        </article>

        <article class="adventure-card" id="card-3">
          <div class="card-content">
            <div class="card-header">
              <div class="card-icon-container">
                <img src="images/bikelogo.jpg" alt="Bike Icon" class="card-icon" />
              </div>
              <div class="card-location">
                <svg class="location-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Irma Coastline</span>
              </div>
            </div>

            <div class="card-body">
              <h3 class="card-title">Cycling the Irma coastline</h3>
              <p class="card-description">
                Beautiful scenery combined with steep inclines and fast rodes allowed for some great cyclings. Don't forget the helmet!!
              </p>
            </div>

            <div class="card-actions">
              <button class="card-button" data-details="details-3">
                More Details
                <span class="button-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </span>
              </button>
            </div>
          </div>
        </article>

        <div id="details-1-container" class="details-container">
          <div class="details-content">
            <button class="details-close" aria-label="Close details">×</button>
            <h3 class="details-title">Adventure Details</h3>
            <p class="details-text">
              Fly Fishing in the Rocky Mountains
              You'll get a seasoned guide and lots of dehydrated ravioli.
            </p>
            <h4 class="details-subtitle">Highlights</h4>
            <ul class="details-list">
              <li>Level 5 Rapids!
                  Put your helmet on and grab your wetsuit. It's time to conquer Siberia.</li>
              <li>Puget Sound Kayaking
                  One week of ocean kayaking in the Puget Sound.</li>
            </ul>
          </div>
        </div>

        <div id="details-2-container" class="details-container">
          <div class="details-content">
            <button class="details-close" aria-label="Close details">×</button>
            <h3 class="details-title">Climbing Experience</h3>
            <p class="details-text">
              Wyoming's climbing Mecca, Devil's Tower, stands at 865 feet and offers the beginner or the expert climber 200 fun and challenging routes. (In fact, a 6-year-old boy conquered the Tower in 1994.) The array of cracks in the walls allows you to use your imagination as you test your climbing skills.
            </p>
            <h4 class="details-subtitle">________________________________________________________________________________________________________________________________</h4>
            <p class="details-text"> President Teddy Roosevelt named Devil's Tower the first national monument in 1906. Today, the park hosts approximately 450,000 visitors annually; 5,000 of those visitors are climbers. But beware, environmentalists are trying to limit that number so treat the park with respect.
</p>
          </div>
        </div>

        <div id="details-3-container" class="details-container">
          <div class="details-content">
            <button class="details-close" aria-label="Close details">×</button>
            <h3 class="details-title">Cycling Route</h3>
            <p class="details-text">
              The Karapoti Trail, home to the Trek Karapoti Classic, twists around the Akatarawa Range and delivers 31 miles of technical single track and challenging fire road climbs. During the ride, there are several vistas to soothe those eyes while you reward your burning legs by taking a quick breather.
            </p>
            <h4 class="details-subtitle">Best Stops Along the Way</h4>
            <p class="details-text">Upper Hutt is New Zealand's mountain biking hub. If you're looking for a group ride, stop by Mountain Trails bike shop. Or if you want a number plate on your handlebar, the Trek Karapoti Classic is scheduled for March 4, 2001.
            </p>
          </div>
        </div>

        <?php if (empty($userTrips)): ?>
            <p style="grid-column: 1 / -1; text-align: center; color: #4a5568;">No trip plans submitted yet. Create one on the Trip Planner page!</p>
        <?php else: ?>
            <?php foreach ($userTrips as $index => $trip): ?>
                <article class="adventure-card user-trip" id="card-<?= $trip['id'] ?>">
                    <span class="user-trip-badge">Your Trip</span>
                  <div class="card-content">
                    <div class="card-header">
                      <div class="card-icon-container">
                        <img src="images/logo.png" alt="Climbing Icon" class="card-icon" />
                      </div>
                      <div class="card-location">
                        <svg class="location-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                          <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span><?= htmlspecialchars($trip['city']) ?>, <?= htmlspecialchars($trip['country']) ?></span>
                      </div>
                    </div>

                    <div class="card-body">
                      <h3 class="card-title">Trip to <?= htmlspecialchars($trip['city']) ?></h3>
                      <p class="card-description">
                        Activities: <?= empty($trip['activities']) ? 'None specified' : htmlspecialchars($trip['activities']) ?><br>
                        Preferences: <?= empty($trip['info_preferences']) ? 'None specified' : htmlspecialchars($trip['info_preferences']) ?><br>
                        Submitted on: <?= date('M d, Y', strtotime($trip['submission_date'])) ?>
                      </p>
                    </div>

                    <div class="card-actions">
                      <button class="card-button" data-details="user-trip-details-<?= $trip['id'] ?>">
                        More Details
                        <span class="button-icon">
                          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                          </svg>
                        </span>
                      </button>
                    </div>
                  </div>
                </article>

                <div id="user-trip-details-<?= $trip['id'] ?>-container" class="details-container">
                  <div class="details-content">
                    <button class="details-close" aria-label="Close details">×</button>
                    <h3 class="details-title">Details for Trip to <?= htmlspecialchars($trip['city']) ?></h3>
                    <p class="details-text">
                      <strong style="color: var(--primary);">Start Date:</strong> <?= date('M d, Y', strtotime($trip['start_date'])) ?><br>
                      <strong style="color: var(--primary);">End Date:</strong> <?= date('M d, Y', strtotime($trip['end_date'])) ?><br>
                      <strong style="color: var(--primary);">Budget (USD):</strong> <?= htmlspecialchars($trip['budget']) ?><br>
                      <strong style="color: var(--primary);">Number of Travelers:</strong> <?= htmlspecialchars($trip['travelers']) ?><br>
                      <strong style="color: var(--primary);">City:</strong> <?= htmlspecialchars($trip['city']) ?><br>
                      <strong style="color: var(--primary);">Country:</strong> <?= htmlspecialchars($trip['country']) ?><br>
                      <strong style="color: var(--primary);">Activities:</strong> <?= empty($trip['activities']) ? 'No specific activities planned.' : htmlspecialchars($trip['activities']) ?><br>
                      <strong style="color: var(--primary);">Information Preferences:</strong> <?= empty($trip['info_preferences']) ? 'No specific info preferences.' : htmlspecialchars($trip['info_preferences']) ?><br>
                      <strong style="color: var(--primary);">Additional Notes/Requests:</strong> <?= empty($trip['additional_notes']) ? 'None.' : htmlspecialchars($trip['additional_notes']) ?><br>
                      <strong style="color: var(--primary);">Submission Date:</strong> <?= date('F j, Y, g:i a', strtotime($trip['submission_date'])) ?>
                    </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lightbox-close" aria-label="Close lightbox">×</button>
    <div class="lightbox-content">
      <img id="lightbox-image" class="lightbox-image" src="/placeholder.svg" alt="" />
      <div class="lightbox-caption">
        <h3 class="lightbox-title" id="lightbox-title"></h3>
        <p class="lightbox-subtitle">Click anywhere outside to close</p>
      </div>
    </div>
  </div>

  <script>
    // Adventure images data (for the Popular Adventures Carousel - remains static)
    const adventureImages = [
      {
        src: "images/kayak.jpg",
        alt: "Kayaking on river",
        label: "Kayaking"
      },
      {
        src: "images/climb.jpg",
        alt: "Climber on mountain",
        label: "Climbing"
      },
      {
        src: "images/bike2.jpg",
        alt: "Cycling on trail",
        label: "Cycling"
      }
    ];

    // Carousel functionality (remains largely the same)
    document.addEventListener('DOMContentLoaded', function() {
      const carousel = document.getElementById('adventure-carousel');
      const indicators = document.getElementById('carousel-indicators');
      const lightbox = document.getElementById('lightbox');
      const lightboxImage = document.getElementById('lightbox-image');
      const lightboxTitle = document.getElementById('lightbox-title');
      const lightboxClose = document.getElementById('lightbox-close');

      let currentIndex = 0;
      let isTransitioning = false;
      let carouselInterval;

      // Initialize carousel
      function initCarousel() {
        // Clear existing carousel content (if any, from static HTML)
        carousel.innerHTML = '';
        indicators.innerHTML = '';

        // Create slides
        adventureImages.forEach((image, index) => {
          const slide = document.createElement('div');
          slide.className = `carousel-slide ${index === 0 ? 'active' : ''}`;
          slide.innerHTML = `
            <img src="${image.src}" alt="${image.alt}" class="carousel-image">
            <div class="carousel-overlay">
              <span class="carousel-label">${image.label}</span>
            </div>
          `;
          carousel.appendChild(slide);

          // Create indicator
          const indicator = document.createElement('button');
          indicator.className = `carousel-indicator ${index === 0 ? 'active' : ''}`;
          indicator.setAttribute('aria-label', `Go to slide ${index + 1}`);
          indicator.addEventListener('click', (e) => {
            e.stopPropagation();
            goToSlide(index);
          });
          indicators.appendChild(indicator);
        });

        // Re-add zoom indicator as it was removed by innerHTML clear
        const zoomIndicator = document.createElement('div');
        zoomIndicator.className = 'zoom-indicator';
        zoomIndicator.innerHTML = `
            <div class="zoom-icon-container">
              <svg class="zoom-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="11" y1="8" x2="11" y2="14"></line>
                <line x1="8" y1="11" x2="14" y2="11"></line>
              </svg>
            </div>
        `;
        carousel.appendChild(zoomIndicator);

        // Add click event to open lightbox
        carousel.addEventListener('click', openLightbox);

        // Start carousel
        startCarousel();
      }

      // Start automatic carousel
      function startCarousel() {
        carouselInterval = setInterval(() => {
          nextSlide();
        }, 5000);
      }

      // Stop carousel
      function stopCarousel() {
        clearInterval(carouselInterval);
      }

      // Go to next slide
      function nextSlide() {
        if (isTransitioning) return;

        isTransitioning = true;
        const slides = document.querySelectorAll('.carousel-slide');
        const currentSlide = slides[currentIndex];

        currentSlide.classList.add('transitioning');

        setTimeout(() => {
          currentSlide.classList.remove('active');
          currentIndex = (currentIndex + 1) % slides.length;
          slides[currentIndex].classList.add('active');

          // Update indicators
          updateIndicators();

          setTimeout(() => {
            currentSlide.classList.remove('transitioning');
            isTransitioning = false;
          }, 50);
        }, 500);
      }

      // Go to specific slide
      function goToSlide(index) {
        if (isTransitioning || index === currentIndex) return;

        isTransitioning = true;
        stopCarousel();

        const slides = document.querySelectorAll('.carousel-slide');
        const currentSlide = slides[currentIndex];

        currentSlide.classList.add('transitioning');

        setTimeout(() => {
          currentSlide.classList.remove('active');
          currentIndex = index;
          slides[currentIndex].classList.add('active');

          // Update indicators
          updateIndicators();

          setTimeout(() => {
            currentSlide.classList.remove('transitioning');
            isTransitioning = false;
            startCarousel();
          }, 50);
        }, 500);
      }

      // Update indicators
      function updateIndicators() {
        const indicators = document.querySelectorAll('.carousel-indicator');
        indicators.forEach((indicator, index) => {
          if (index === currentIndex) {
            indicator.classList.add('active');
          } else {
            indicator.classList.remove('active');
          }
        });
      }

      // Open lightbox
      function openLightbox() {
        lightboxImage.src = adventureImages[currentIndex].src;
        lightboxImage.alt = adventureImages[currentIndex].alt;
        lightboxTitle.textContent = adventureImages[currentIndex].label;
        lightbox.classList.add('active');
        stopCarousel();

        // Add event listeners for lightbox
        document.addEventListener('keydown', handleLightboxKeydown);
      }

      // Close lightbox
      function closeLightbox() {
        lightbox.classList.remove('active');
        startCarousel();

        // Remove event listeners
        document.removeEventListener('keydown', handleLightboxKeydown);
      }

      // Handle keydown events in lightbox
      function handleLightboxKeydown(e) {
        if (e.key === 'Escape') {
          closeLightbox();
        }
      }

      // Lightbox click events
      lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
          closeLightbox();
        }
      });

      lightboxClose.addEventListener('click', closeLightbox);

      // Initialize carousel
      initCarousel();

      // More Details functionality for DYNAMICALLY LOADED CARDS
      // You need to re-select these elements after the PHP loop runs
      const adventureGrid = document.getElementById('adventure-grid');

      // Function to close all details containers
      function closeAllDetails() {
        // Select all details containers and buttons
        const currentDetailsContainers = adventureGrid.querySelectorAll('.details-container');
        const currentDetailButtons = adventureGrid.querySelectorAll('.card-button[data-details]');

        currentDetailsContainers.forEach(container => {
          container.classList.remove('active');
        });

        currentDetailButtons.forEach(btn => {
          btn.classList.remove('active');
        });
      }

      // Add click event to detail buttons using event delegation
      // This is crucial because cards are generated by PHP,
      // so direct event listeners won't work on them if set up before render.
      adventureGrid.addEventListener('click', function(e) {
        const clickedButton = e.target.closest('.card-button[data-details]');
        if (!clickedButton) return; // Not a detail button click

        e.preventDefault();

        const detailsId = clickedButton.getAttribute('data-details');
        const detailsContainer = document.getElementById(detailsId + '-container');

        // If this container is already active, close it
        if (detailsContainer && detailsContainer.classList.contains('active')) {
          detailsContainer.classList.remove('active');
          clickedButton.classList.remove('active');
          return;
        }

        // Close any open details first
        closeAllDetails();

        // Show this details container
        if (detailsContainer) { // Ensure container exists
            detailsContainer.classList.add('active');
            clickedButton.classList.add('active');

            // Scroll to the details container
            setTimeout(() => {
                detailsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
      });

      // Add click event to close buttons for dynamic content
      adventureGrid.addEventListener('click', function(e) {
        const clickedCloseButton = e.target.closest('.details-close');
        if (clickedCloseButton) {
          closeAllDetails();
        }
      });

      // Close details when pressing Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeAllDetails();
        }
      });
    });
  </script>
</body>
</html>