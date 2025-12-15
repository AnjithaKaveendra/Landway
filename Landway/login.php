<?php
session_start();

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

        <div style="font-size: 12px; color: #666; margin-bottom: 20px;">
          <p><strong>Demo Admin Account:</strong></p>
          <p>Email: admin@landway.com</p>
          <p>Password: Admin123456</p>
        </div>

        <button type="submit" class="btn-login">Login</button>
      </form>

      <div class="login-footer">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
      </div>
    </div>
  </div>

  <script src="lib/jquery/jquery.min.js"></script>
  <script>
    function handleLogin(event) {
      event.preventDefault();

      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      if (!email || !password) {
        showError('Please fill in all fields');
        return;
      }

      // Show loading state
      const btn = event.target.querySelector('button[type="submit"]');
      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Logging in...';

      fetch('api-admin.php?action=login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: email, password: password })
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;

        if (data.status === 'success') {
          // Check if admin or user login
          if (data.admin && data.admin.isAdmin) {
            // Store admin session in localStorage
            localStorage.setItem('adminSession', JSON.stringify(data.admin));
            showSuccess('Login successful! Redirecting...');
            setTimeout(() => {
              window.location.href = 'admin-dashboard.php';
            }, 1500);
          } else {
            // Check if it's a regular user
            // Try user login API
            tryUserLogin(email, password, btn);
          }
        } else {
          showError(data.message);
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.textContent = originalText;
        showError('Connection error: ' + error.message);
      });
    }

    function tryUserLogin(email, password, btn) {
      const originalText = btn.textContent;

      fetch('api-users.php?action=login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: email, password: password })
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;

        if (data.status === 'success') {
          localStorage.setItem('userSession', JSON.stringify(data.user));
          showSuccess('Login successful! Redirecting...');
          setTimeout(() => {
            window.location.href = 'index.php';
          }, 1500);
        } else {
          showError('Invalid email or password');
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.textContent = originalText;
        showError('Connection error: ' + error.message);
      });
    }

    function showError(message) {
      document.getElementById('errorMessage').textContent = message;
      document.getElementById('errorMessage').style.display = 'block';
      document.getElementById('successMessage').style.display = 'none';
    }

    function showSuccess(message) {
      document.getElementById('successMessage').textContent = message;
      document.getElementById('successMessage').style.display = 'block';
      document.getElementById('errorMessage').style.display = 'none';
    }
  </script>

</body>
</html>
