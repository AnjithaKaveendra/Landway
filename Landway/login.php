<?php
require_once __DIR__ . '/app.php';

// Check if already logged in
if (isset($_SESSION['userSession']) || isset($_SESSION['adminSession'])) {
    if (isset($_SESSION['adminSession'])) {
        header('Location: admin-dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="<?= e($_SESSION['csrfToken']) ?>">
  <script src="js/api.js"></script>
  <meta charset="utf-8">
  <title>Login - Landway</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 20px;
    }
    .login-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      max-width: 400px;
      width: 100%;
      padding: 40px;
    }
    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .login-header h1 {
      font-size: 28px;
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
    }
    .login-header p {
      color: #666;
      font-size: 14px;
    }
    .form-group label {
      font-weight: 500;
      color: #333;
      margin-bottom: 8px;
    }
    .form-control {
      border-radius: 5px;
      border: 1px solid #ddd;
      padding: 10px 15px;
      font-size: 14px;
    }
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .btn-login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      font-weight: 600;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      margin-top: 20px;
      transition: transform 0.3s ease;
    }
    .btn-login:hover {
      transform: translateY(-2px);
    }
    .login-footer {
      text-align: center;
      margin-top: 20px;
    }
    .login-footer a {
      color: #667eea;
      text-decoration: none;
      font-size: 14px;
    }
    .alert {
      margin-bottom: 20px;
    }
    .admin-badge {
      display: inline-block;
      background: #ffc107;
      color: #333;
      padding: 5px 10px;
      border-radius: 3px;
      font-size: 12px;
      font-weight: 600;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Land<span style="color: #e74c3c;">way</span></h1>
        <p>Login to your account</p>
      </div>

      <div id="errorMessage" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="successMessage" class="alert alert-success" role="alert" style="display: none;"></div>

      <form id="loginForm" onsubmit="handleLogin(event)">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" class="form-control" placeholder="your@email.com" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-login">Login</button>
      </form>

      <div class="login-footer">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
      </div>
    </div>
  </div>

  <script src="lib/jquery/jquery.min.js"></script>
  <script src="js/login.js"></script>

</body>
</html>
