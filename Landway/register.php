<?php
session_start();

// Check if already logged in
if (isset($_SESSION['userSession']) || isset($_SESSION['adminSession'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - Landway</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <style>
    .register-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 20px;
      margin-top: 80px;
    }
    .register-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      max-width: 500px;
      width: 100%;
      padding: 40px;
    }
    .register-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .register-header h1 {
      font-size: 28px;
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
    }
    .register-header p {
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
    .btn-register {
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
    }
    .btn-register:hover {
      transform: translateY(-2px);
    }
    .register-footer {
      text-align: center;
      margin-top: 20px;
    }
    .register-footer a {
      color: #667eea;
      text-decoration: none;
      font-size: 14px;
    }
    .alert {
      margin-bottom: 20px;
    }
  </style>
</head>

<body class="policy-page">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light navbar-default navbar-fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand text-brand" href="index.php">Land<span class="color-b">Way</span></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarDefault">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse collapse" id="navbarDefault">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="properties.php">Properties</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="contact.php">Contact</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h1>Create Account</h1>
        <p>Join Landway and start exploring properties</p>
      </div>

      <div id="errorMessage" class="alert alert-danger" role="alert" style="display: none;"></div>
      <div id="successMessage" class="alert alert-success" role="alert" style="display: none;"></div>

      <form id="registrationForm" onsubmit="handleRegistration(event)">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" class="form-control" placeholder="John" required>
          </div>
          <div class="form-group col-md-6">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" class="form-control" placeholder="Doe" required>
          </div>
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" class="form-control" placeholder="your@email.com" required>
        </div>

        <div class="form-group">
          <label for="phone">Phone</label>
          <input type="tel" id="phone" class="form-control" placeholder="+1 (555) 123-4567">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" class="form-control" placeholder="At least 8 characters" required minlength="8">
        </div>

        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm password" required>
        </div>

        <button type="submit" class="btn-register">Create Account</button>
      </form>

      <div class="register-footer">
        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
    </div>
  </div>

  <script src="lib/jquery/jquery.min.js"></script>
  <script>
    function handleRegistration(event) {
      event.preventDefault();

      const firstName = document.getElementById('firstName').value.trim();
      const lastName = document.getElementById('lastName').value.trim();
      const email = document.getElementById('email').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      // Validation
      if (!firstName || !lastName || !email || !password) {
        showError('Please fill in all required fields');
        return;
      }

      if (password !== confirmPassword) {
        showError('Passwords do not match');
        return;
      }

      if (password.length < 8) {
        showError('Password must be at least 8 characters');
        return;
      }

      const btn = event.target.querySelector('button[type="submit"]');
      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Creating account...';

      fetch('api-users.php?action=register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          firstName: firstName,
          lastName: lastName,
          email: email,
          phone: phone,
          password: password
        })
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;

        if (data.status === 'success') {
          showSuccess('Registration successful! Redirecting to login...');
          setTimeout(() => {
            window.location.href = 'login.php';
          }, 2000);
        } else {
          showError(data.message);
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.textContent = originalText;
        showError('Error: ' + error.message);
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
