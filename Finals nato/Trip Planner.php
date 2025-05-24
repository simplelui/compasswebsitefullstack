<?php
session_start(); // Start the session at the very beginning of the PHP file

// Optional: Redirect if user is not logged in, if this page should only be accessible to logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: ./LoginSignup/login.php"); // Adjust path if needed
    exit();
}

// Function to get a list of all countries (ISO 3166-1 alpha-2 and full names)
// This is a comprehensive list. You can modify it if you need a smaller subset.
function getAllCountries() {
    return [
        "AF" => "Afghanistan", "AL" => "Albania", "DZ" => "Algeria", "AD" => "Andorra", "AO" => "Angola",
        "AG" => "Antigua and Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AU" => "Australia", "AT" => "Austria",
        "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados",
        "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BT" => "Bhutan",
        "BO" => "Bolivia (Plurinational State of)", "BA" => "Bosnia and Herzegovina", "BW" => "Botswana", "BR" => "Brazil",
        "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "CV" => "Cabo Verde",
        "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CF" => "Central African Republic", "TD" => "Chad",
        "CL" => "Chile", "CN" => "China", "CO" => "Colombia", "KM" => "Comoros", "CD" => "Congo (Democratic Republic of the)",
        "CG" => "Congo", "CR" => "Costa Rica", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus",
        "CZ" => "Czechia", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic",
        "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea",
        "EE" => "Estonia", "SZ" => "Eswatini", "ET" => "Ethiopia", "FJ" => "Fiji", "FI" => "Finland",
        "FR" => "France", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany",
        "GH" => "Ghana", "GR" => "Greece", "GD" => "Grenada", "GT" => "Guatemala", "GN" => "Guinea",
        "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HN" => "Honduras", "HU" => "Hungary",
        "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran (Islamic Republic of)", "IQ" => "Iraq",
        "IE" => "Ireland", "IL" => "Israel", "IT" => "Italy", "CI" => "Côte d'Ivoire", "JM" => "Jamaica",
        "JP" => "Japan", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati",
        "KP" => "Korea (Democratic People's Republic of)", "KR" => "Korea (Republic of)", "KW" => "Kuwait",
        "KG" => "Kyrgyzstan", "LA" => "Lao People's Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon",
        "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libya", "LI" => "Liechtenstein", "LT" => "Lithuania",
        "LU" => "Luxembourg", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives",
        "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MR" => "Mauritania", "MU" => "Mauritius",
        "MX" => "Mexico", "FM" => "Micronesia (Federated States of)", "MD" => "Moldova (Republic of)", "MC" => "Monaco",
        "MN" => "Mongolia", "ME" => "Montenegro", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar",
        "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "NZ" => "New Zealand",
        "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "MK" => "North Macedonia", "NO" => "Norway",
        "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PA" => "Panama", "PG" => "Papua New Guinea",
        "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PL" => "Poland", "PT" => "Portugal",
        "QA" => "Qatar", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda", "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia", "VC" => "Saint Vincent and the Grenadines", "WS" => "Samoa", "SM" => "San Marino",
        "ST" => "Sao Tome and Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "SC" => "Seychelles",
        "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands",
        "SO" => "Somalia", "ZA" => "South Africa", "SS" => "South Sudan", "ES" => "Spain", "LK" => "Sri Lanka",
        "SD" => "Sudan", "SR" => "Suriname", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic",
        "TJ" => "Tajikistan", "TZ" => "Tanzania, United Republic of", "TH" => "Thailand", "TL" => "Timor-Leste",
        "TG" => "Togo", "TO" => "Tonga", "TT" => "Trinidad and Tobago", "TN" => "Tunisia", "TR" => "Turkey",
        "TM" => "Turkmenistan", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates",
        "GB" => "United Kingdom of Great Britain and Northern Ireland", "US" => "United States of America",
        "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela (Bolivarian Republic of)",
        "VN" => "Viet Nam", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe"
    ];
}

$countries = getAllCountries();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Adventure Planner - Compass</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet" />
  <style>
    /* === RESET & BASE STYLES === */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      /* Adjusted body background to blue gradient */
      background: linear-gradient(120deg, #e0f2f7 0%, #bbdefb 100%); /* Light blue to sky blue */
      min-height: 100vh;
      color: #1a237e; /* Darker blue for text */
      position: relative;
      overflow-x: hidden;
      display: flex;
      flex-direction: column;
      align-items: center;
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

    :root {
      /* Shades of blue for primary, secondary, and accent */
      --primary: #2196f3; /* A vibrant blue */
      --secondary: #03a9f4; /* A lighter, bright blue */
      --accent: #90caf9; /* A even lighter blue, for subtle highlights */
      --bg-glass: rgba(227, 242, 253, 0.72); /* Light blue tint with transparency */
      --shadow: 0 8px 32px rgba(33, 150, 243, 0.13); /* Shadow with blue tint */
      --radius: 18px;
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
      box-shadow: 0 8px 24px rgba(33, 150, 243, 0.08); /* Blue tinted shadow */
      backdrop-filter: blur(14px);
      border-radius: 0 0 var(--radius) var(--radius);
      margin-bottom: 32px;
      width: 100%;
    }

    .header-logo-link {
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
      box-shadow: 0 4px 16px rgba(3, 169, 244, 0.4); /* Blue tinted shadow */
      background: #fff;
      transition: transform 0.2s;
      height: 100%;
      width: 100%;
      object-fit: cover;
    }

    .header-logo:hover {
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
      /* Gradient with shades of blue */
      background: linear-gradient(90deg, #0288d1 0%, #2196f3 100%); /* Darker blue to vibrant blue */
      padding: 10px 26px;
      border-radius: 13px;
      font-size: 1.05rem;
      box-shadow: 0 2px 10px rgba(33, 150, 243, 0.1); /* Blue tinted shadow */
      transition: all 0.25s;
      user-select: none;
      border: none;
      outline: none;
      display: inline-block;
    }

    .nav-link:hover, .nav-link:focus {
      /* Reversed gradient on hover */
      background: linear-gradient(90deg, #2196f3 0%, #0288d1 100%);
      color: #fff;
      transform: scale(1.06) translateY(-2px);
      box-shadow: 0 6px 16px rgba(33, 150, 243, 0.3); /* Stronger blue tinted shadow */
    }

    /* Main Content */
    .main-content {
      max-width: 1000px;
      width: 100%;
      margin: 0 auto 48px auto;
      padding: 0 16px;
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* Hero Section */
    .hero-section {
      text-align: center;
      padding: 48px 24px;
      position: relative;
      overflow: hidden;
      margin-bottom: 40px;
      width: 100%;
    }

    .hero-circle-1 {
      position: absolute;
      width: 250px;
      height: 250px;
      background: rgba(33, 150, 243, 0.15); /* Blue tinted circle */
      border-radius: 50%;
      top: -50px;
      left: -50px;
      filter: blur(50px);
      z-index: -1;
    }

    .hero-circle-2 {
      position: absolute;
      width: 300px;
      height: 300px;
      background: rgba(3, 169, 244, 0.15); /* Lighter blue tinted circle */
      border-radius: 50%;
      bottom: -80px;
      right: -80px;
      filter: blur(60px);
      z-index: -1;
    }

    .hero-title {
      position: relative;
      display: inline-block;
      font-family: 'Montserrat', sans-serif;
      font-size: 4rem;
      font-weight: 800;
      /* Blue gradient for title */
      background: linear-gradient(90deg, #0d47a1 0%, #1976d2 50%, #2196f3 100%); /* Dark to vibrant blue */
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin-bottom: 16px;
      letter-spacing: -1px;
      margin-left: auto;
      margin-right: auto;
      text-align: center;
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
      /* Blue gradient for underline */
      background: linear-gradient(90deg, #03a9f4 0%, #2196f3 100%);
      border-radius: 4px;
      transform: scaleX(0.8);
    }

    .hero-description {
      font-size: 1.125rem;
      color: #263238; /* Dark blue-grey for description */
      margin-bottom: 32px;
      line-height: 1.6;
      text-align: center;
      width: 100%;
    }

    /* Form Container */
    .form-container {
      color: white;
      background: var(--bg-glass);
      backdrop-filter: blur(16px);
      border-radius: var(--radius);
      box-shadow: 0 0px 30px rgba(33, 149, 243, 0.6); /* Strong blue shadow */
      padding: 32px;
      display: flex;
      flex-direction: column;
      gap: 28px;
      border: 1px solid rgba(144, 202, 249, 0.3); /* Lighter blue border */
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
    }

    /* Image Box (replacing map) */
    .image-box {
      width: 100%;
      height: 300px; /* Adjust height as needed */
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 24px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      background-image: url('./images/os.jpg'); /* Your image URL */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      /* Removed display: flex, align-items, justify-content, color, font-size, font-weight, text-shadow */
    }


    .form-card {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
      width: 100%;
    }

    .form-card-content {
      display: flex;
      flex-direction: column;
    }

    .form-card-content label {
      font-weight: 600;
      color: #1a237e; /* Dark blue for labels */
      margin-bottom: 8px;
      font-size: 0.95rem;
    }

    .form-card-content input[type="text"],
    .form-card-content input[type="date"],
    .form-card-content textarea,
    .form-card-content select { /* Added select for styling */
      padding: 12px 16px;
      border: 1px solid #90caf9; /* Light blue border */
      border-radius: 8px;
      font-size: 1rem;
      color: #1a237e; /* Dark blue for input text */
      background-color: #e3f2fd; /* Very light blue background */
      transition: all 0.2s;
    }

    .form-card-content input[type="text"]:focus,
    .form-card-content input[type="date"]:focus,
    .form-card-content textarea:focus,
    .form-card-content select:focus { /* Added select for styling */
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2); /* Blue tinted focus shadow */
    }

    .form-card-content textarea {
      min-height: 100px;
      resize: vertical;
    }

    .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .checkbox-group label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 500;
      color: #263238; /* Dark blue-grey for checkbox labels */
      cursor: pointer;
      margin-bottom: 0; /* Override default label margin */
    }

    .checkbox-group input[type="checkbox"] {
      appearance: none;
      width: 20px;
      height: 20px;
      border: 2px solid var(--primary);
      border-radius: 5px;
      background-color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
    }

    .checkbox-group input[type="checkbox"]::before {
      content: '✓';
      font-size: 1.2em;
      color: white;
      transform: scale(0);
      transition: transform 0.2s ease-in-out;
    }

    .checkbox-group input[type="checkbox"]:checked {
      background-color: var(--primary);
      border-color: var(--primary);
    }

    .checkbox-group input[type="checkbox"]:checked::before {
      transform: scale(1);
    }

    .submit-button {
      display: block;
      width: 100%;
      padding: 15px 25px;
      /* Blue gradient for submit button */
      background: linear-gradient(90deg, #0288d1 0%, #2196f3 100%);
      color: white;
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.1rem;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2); /* Blue tinted shadow */
    }

    .submit-button:hover {
      /* Reversed blue gradient on hover */
      background: linear-gradient(90deg, #2196f3 0%, #0288d1 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(33, 150, 243, 0.3); /* Stronger blue tinted shadow */
    }

    /* Responsive */
    @media (max-width: 768px) {
      .top-nav {
        padding: 16px 24px;
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
      }
      .nav-links {
        width: 100%;
        justify-content: space-between;
      }
      .main-content {
        padding: 0 12px;
      }
      .hero-title {
        font-size: 3rem;
      }
      .form-container {
        padding: 24px;
      }
      .image-box {
        height: 200px;
      }
    }

    @media (max-width: 480px) {
      .hero-title {
        font-size: 2.5rem;
      }
      .hero-description {
        font-size: 0.9rem;
      }
      .form-container {
        padding: 20px;
      }
      .image-box {
        height: 150px;
      }
    }
  </style>
</head>
<body>
  <nav class="top-nav">
    <a href="landing page.html" class="header-logo-link">
      <img src="Compass_Site/Assets/images/compass_logo.gif" alt="Compass Logo" class="header-logo" />
      <span class="logo-text"></span>
    </a>
    <div class="nav-links">
      <a href="Travelog.php" class="nav-link">Travelogs</a>
      <a href="Destination.html" class="nav-link">Destinations</a>
      <a href="./LoginSignup/login.php" class="nav-link">Logout</a>
    </div>
  </nav>

  <main class="main-content">
    <div class="hero-circle-1"></div>
    <div class="hero-circle-2"></div>

    <h1 class="hero-title">Plan Your <span class="hero-title-accent">Adventure</span></h1>
    <p class="hero-description">Input your desired destination and activities below!</p>

    <form class="form-container" action="tripps.php" method="POST">
      <div class="image-box">
          </div>

      <div class="form-card">
        <div class="form-card-content">
          <label for="country">Country:</label>
          <select id="country" name="country" required>
            <option value="">-- Select a Country --</option>
            <?php
            foreach ($countries as $code => $name) {
                echo "<option value=\"" . htmlspecialchars($name) . "\">" . htmlspecialchars($name) . "</option>";
            }
            ?>
          </select>
        </div>
        <div class="form-card-content">
          <label for="city">Destination City:</label>
          <input type="text" id="city" name="city" placeholder="e.g., Tokyo" required />
        </div>
        <div class="form-card-content">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
        <div class="form-card-content">
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
        </div>
        <div class="form-card-content">
            <label for="budget">Budget (USD):</label>
            <input type="text" id="budget" name="budget" placeholder="e.g., 1000-2000" required>
        </div>
        <div class="form-card-content">
            <label for="travelers">Number of Travelers:</label>
            <input type="text" id="travelers" name="travelers" placeholder="e.g., 2" required>
        </div>
        <div class="form-card-content">
            <label for="additional_notes">Additional Notes/Requests:</label>
            <textarea id="additional_notes" name="additional_notes" placeholder="e.g., I would like a guided tour and a hotel with an ocean view."></textarea>
        </div>

        <div class="form-card-content">
          <label>Activities:</label>
          <div class="checkbox-group">
            <label><input type="checkbox" name="activities[]" value="Hiking"> Hiking</label>
            <label><input type="checkbox" name="activities[]" value="Swimming"> Swimming</label>
            <label><input type="checkbox" name="activities[]" value="Sightseeing"> Sightseeing</label>
            <label><input type="checkbox" name="activities[]" value="Mountain Biking"> Mountain Biking</label>
            <label><input type="checkbox" name="activities[]" value="Kayaking"> Kayaking</label>
            <label><input type="checkbox" name="activities[]" value="Skiing"> Skiing</label>
            <label><input type="checkbox" name="activities[]" value="Fishing"> Fishing</label>
            <label><input type="checkbox" name="activities[]" value="Surfing"> Surfing</label>
          </div>
        </div>
        <div class="form-card-content">
          <label>Info Preferences:</label>
          <div class="checkbox-group">
            <label><input type="checkbox" name="info[]" value="Transportation"> Transportation</label>
            <label><input type="checkbox" name="info[]" value="Health"> Health</label>
            <label><input type="checkbox" name="info[]" value="Weather"> Weather</label>
            <label><input type="checkbox" name="info[]" value="Gear"> Gear</label>
            <label><input type="checkbox" name="info[]" value="Political Info"> Political Info</label>
            <label><input type="checkbox" name="info[]" value="Activity Specific"> Activity Specific</label>
          </div>
        </div>

        <div class="form-card-content">
          <button type="submit" class="submit-button">Submit Plan</button>
        </div>
      </div>
    </form>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Form validation (remains the same as before)
      const form = document.querySelector('.form-container');
      form.addEventListener('submit', function(e) {
        const city = document.getElementById('city').value;
        const country = document.getElementById('country').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const budget = document.getElementById('budget').value;
        const travelers = document.getElementById('travelers').value;

        if (!city || !country) {
          alert('Please fill in both city and country fields.');
          e.preventDefault();
          return;
        }

        // Check if start date is before end date
        if (startDate && endDate) {
          const start = new Date(startDate);
          const end = new Date(endDate);
          
          if (start > end) {
            alert('Start date must be before end date.');
            e.preventDefault();
            return;
          }
        }
      });
    });
  </script>
</body>
</html>