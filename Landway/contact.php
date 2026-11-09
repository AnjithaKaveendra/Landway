<?php
require_once __DIR__ . '/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="<?= e($_SESSION['csrfToken']) ?>">
  <script src="js/api.js"></script>
  <meta charset="utf-8">
  <title>Contact Us - Landway</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="lib/animate/animate.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>

<body class="policy-page">

  <!-- Navbar -->
  <?php require __DIR__ . '/includes/nav.php'; ?>

  <!-- Contact Header -->
  <section class="policy-header contact-header">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <h1>Contact Us</h1>
          <p>We're here to help you find the perfect property.</p>
        </div>
        <div class="col-lg-4 text-lg-right text-left">
          <a class="btn-b" href="#contact-form"><i class="fa fa-envelope"></i> Make an Inquiry</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Content -->
  <div class="policy-container">
    <article class="policy-article">
      <section class="policy-section" id="contact-form">
        <h2>Send Us a Message</h2>
        <form class="form-a" onsubmit="handleContactSubmit(event)">
          <div id="contactMessage" class="alert" style="display: none;"></div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="contactName">Full Name <span class="required-field">*</span></label>
              <input type="text" class="form-control form-control-a" id="contactName" placeholder="John Doe" required>
            </div>
            <div class="form-group col-md-6">
              <label for="contactEmail">Email <span class="required-field">*</span></label>
              <input type="email" class="form-control form-control-a" id="contactEmail" placeholder="you@example.com" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="contactPhone">Phone <span class="required-field">*</span></label>
              <input type="tel" class="form-control form-control-a" id="contactPhone" placeholder="+1 234 567 8900" required>
            </div>
            <div class="form-group col-md-6">
              <label for="contactSubject">Subject <span class="required-field">*</span></label>
              <input type="text" class="form-control form-control-a" id="contactSubject" placeholder="Property inquiry" required>
            </div>
          </div>

          <div class="form-group">
            <label for="contactPropertyType">Property Type (Optional)</label>
            <select class="form-control form-control-a" id="contactPropertyType">
              <option value="">Select a property type</option>
              <option value="house">House</option>
              <option value="apartment">Apartment</option>
              <option value="land">Land</option>
              <option value="commercial">Commercial</option>
            </select>
          </div>

          <div class="form-group">
            <label for="contactMessageText">Message <span class="required-field">*</span></label>
            <textarea class="form-control form-control-a" id="contactMessageText" rows="5" placeholder="Your message here..." required></textarea>
          </div>

          <button type="submit" class="btn btn-b">Send Message</button>
        </form>
      </section>
    </article>
  </div>

  <script src="lib/jquery/jquery.min.js"></script>
  <script>
    function handleContactSubmit(event) {
      event.preventDefault();

      const contactName = document.getElementById('contactName').value.trim();
      const contactEmail = document.getElementById('contactEmail').value.trim();
      const contactPhone = document.getElementById('contactPhone').value.trim();
      const contactSubject = document.getElementById('contactSubject').value.trim();
      const contactPropertyType = document.getElementById('contactPropertyType').value;
      const contactMessageText = document.getElementById('contactMessageText').value.trim();

      if (!contactName || !contactEmail || !contactPhone || !contactSubject || !contactMessageText) {
        showMessage('Please fill in all required fields', 'alert-danger');
        return;
      }

      const btn = event.target.querySelector('button[type="submit"]');
      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Sending...';

      fetch('api-inquiries.php?action=create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          contactName: contactName,
          contactEmail: contactEmail,
          contactPhone: contactPhone,
          contactSubject: contactSubject,
          contactPropertyType: contactPropertyType,
          contactMessageText: contactMessageText
        })
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;

        if (data.status === 'success') {
          showMessage('Message sent successfully! We will contact you soon.', 'alert-success');
          event.target.reset();
        } else {
          showMessage(data.message, 'alert-danger');
        }
      })
      .catch(error => {
        btn.disabled = false;
        btn.textContent = originalText;
        showMessage('Error: ' + error.message, 'alert-danger');
      });
    }

    function showMessage(message, alertClass) {
      const messageDiv = document.getElementById('contactMessage');
      messageDiv.textContent = message;
      messageDiv.className = 'alert ' + alertClass;
      messageDiv.style.display = 'block';
      setTimeout(() => {
        messageDiv.style.display = 'none';
      }, 5000);
    }
  </script>

<script src="lib/popper/popper.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
