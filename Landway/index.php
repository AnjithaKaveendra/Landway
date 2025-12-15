<?php
// Landway - Main entry point
// Redirect to login/main site as needed
session_start();

// If admin is logged in, go to dashboard
if (isset($_SESSION['adminSession']) && $_SESSION['adminSession']['isAdmin']) {
    header('Location: admin-dashboard.php');
    exit();
}

// If user is logged in, show dashboard or home
if (isset($_SESSION['userSession'])) {
    // Could redirect to user profile or home
    // For now, show home page
}

// Otherwise show home page
// The original index.html content can be here or served as separate page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Landway - Find Your Perfect Property</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
    .welcome-section {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      text-align: center;
      color: white;
      padding: 20px;
    }
    .welcome-content {
      max-width: 600px;
    }
    .welcome-content h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .welcome-content p {
      font-size: 18px;
      margin-bottom: 30px;
      opacity: 0.9;
    }
    .action-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .action-buttons a {
      display: inline-block;
      padding: 12px 30px;
      background: white;
      color: #667eea;
      text-decoration: none;
      border-radius: 5px;
      font-weight: 600;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .action-buttons a:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .action-buttons a.btn-secondary {
      background: rgba(255,255,255,0.2);
      color: white;
      border: 2px solid white;
    }
  </style>
</head>
<body>

  <div class="welcome-section">
    <div class="welcome-content">
      <h1>Land<span style="color: #ffc107;">way</span></h1>
      <p>Find Your Perfect Property Today</p>
      
      <p style="font-size: 16px; margin: 30px 0;">Welcome to Landway, your premier real estate platform.</p>

      <div class="action-buttons">
        <a href="properties.php"><i class="fa fa-home"></i> Browse Properties</a>
        <a href="add-property.php" class="btn-secondary"><i class="fa fa-plus"></i> List Property</a>
        <a href="contact.php" class="btn-secondary"><i class="fa fa-envelope"></i> Contact Us</a>
        <?php if (!isset($_SESSION['userSession'])): ?>
          <a href="login.php" class="btn-secondary"><i class="fa fa-sign-in"></i> Login</a>
        <?php endif; ?>
      </div>

      <div style="margin-top: 60px; opacity: 0.8; font-size: 14px;">
        <p>🚀 Powered by PHP & MySQL</p>
        <p><a href="PHP_MYSQL_SETUP.md" style="color: white; text-decoration: underline;">Documentation</a> | 
           <a href="QUICK_START.md" style="color: white; text-decoration: underline;">Quick Start</a> | 
           <a href="CONVERSION_SUMMARY.md" style="color: white; text-decoration: underline;">What's New</a></p>
      </div>
    </div>
  </div>

  <script src="lib/jquery/jquery.min.js"></script>
  <script src="lib/bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
