<?php
require_once __DIR__ . '/app.php';

// Check if user is logged in
if (!isset($_SESSION['userSession']) && !isset($_SESSION['adminSession'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="<?= e($_SESSION['csrfToken']) ?>">
  <script src="js/api.js"></script>
  <meta charset="utf-8">
  <title>Add Property - Landway</title>
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

  <!-- Add Property Header -->
  <section class="policy-header">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1>Add Your Property</h1>
          <p>List your property and reach thousands of potential buyers</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Add Property Form -->
  <div class="policy-container">
    <article class="policy-article">
      <section class="policy-section">
        <form class="form-a" id="addPropertyForm" onsubmit="handleAddPropertySubmit(event)">
          <div id="addPropertyMessage" class="alert" style="display: none;"></div>

          <!-- Basic Information -->
          <div class="form-section-header">
            <h3><i class="fa fa-info-circle"></i> Basic Information</h3>
          </div>

          <div class="form-group">
            <label for="propertyTitle">Property Title <span class="required-field">*</span></label>
            <input type="text" class="form-control form-control-a" id="propertyTitle" placeholder="Beautiful 3-bed house" required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="propertyType">Property Type <span class="required-field">*</span></label>
              <select class="form-control form-control-a" id="propertyType" required>
                <option value="">Select type</option>
                <option value="house">House</option>
                <option value="apartment">Apartment</option>
                <option value="land">Land</option>
                <option value="commercial">Commercial</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="propertyStatus">Status <span class="required-field">*</span></label>
              <select class="form-control form-control-a" id="propertyStatus" required>
                <option value="">Select status</option>
                <option value="sale">For Sale</option>
                <option value="rent">For Rent</option>
                <option value="lease">For Lease</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="propertyDescription">Description <span class="required-field">*</span></label>
            <textarea class="form-control form-control-a" id="propertyDescription" rows="4" placeholder="Describe your property..." required></textarea>
          </div>

          <!-- Location Information -->
          <div class="form-section-header">
            <h3><i class="fa fa-map-marker"></i> Location</h3>
          </div>

          <div class="form-group">
            <label for="propertyAddress">Address <span class="required-field">*</span></label>
            <input type="text" class="form-control form-control-a" id="propertyAddress" placeholder="123 Main Street" required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="propertyCity">City <span class="required-field">*</span></label>
              <input type="text" class="form-control form-control-a" id="propertyCity" placeholder="New York" required>
            </div>
            <div class="form-group col-md-6">
              <label for="propertyState">State/Province</label>
              <input type="text" class="form-control form-control-a" id="propertyState" placeholder="NY">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="propertyZipCode">ZIP Code</label>
              <input type="text" class="form-control form-control-a" id="propertyZipCode" placeholder="10001">
            </div>
            <div class="form-group col-md-6">
              <label for="propertyCountry">Country</label>
              <input type="text" class="form-control form-control-a" id="propertyCountry" placeholder="United States">
            </div>
          </div>

          <!-- Property Details -->
          <div class="form-section-header">
            <h3><i class="fa fa-home"></i> Property Details</h3>
          </div>

          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="propertyBedrooms">Bedrooms</label>
              <input type="number" class="form-control form-control-a" id="propertyBedrooms" min="0">
            </div>
            <div class="form-group col-md-3">
              <label for="propertyBathrooms">Bathrooms</label>
              <input type="number" class="form-control form-control-a" id="propertyBathrooms" min="0">
            </div>
            <div class="form-group col-md-3">
              <label for="propertyArea">Area (sqft)</label>
              <input type="number" class="form-control form-control-a" id="propertyArea" step="0.01" min="0">
            </div>
            <div class="form-group col-md-3">
              <label for="propertyYearBuilt">Year Built</label>
              <input type="number" class="form-control form-control-a" id="propertyYearBuilt" min="1900">
            </div>
          </div>

          <div class="form-group">
            <label for="propertyGarage">Garage/Parking Spaces</label>
            <input type="text" class="form-control form-control-a" id="propertyGarage" placeholder="e.g., 2 spaces">
          </div>

          <!-- Pricing -->
          <div class="form-section-header">
            <h3><i class="fa fa-dollar"></i> Pricing</h3>
          </div>

          <div class="form-row">
            <div class="form-group col-md-8">
              <label for="propertyPrice">Price <span class="required-field">*</span></label>
              <input type="number" class="form-control form-control-a" id="propertyPrice" step="0.01" placeholder="500000" required>
            </div>
            <div class="form-group col-md-4">
              <label for="propertyCurrency">Currency</label>
              <select class="form-control form-control-a" id="propertyCurrency">
                <option value="USD">USD</option>
                <option value="LKR" selected>LKR</option>
                <option value="EUR">EUR</option>
                <option value="GBP">GBP</option>
              </select>
            </div>
          </div>

          <!-- Features -->
          <div class="form-section-header">
            <h3><i class="fa fa-star"></i> Features</h3>
          </div>

          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="featureAC" value="Air Conditioning">
              <label class="custom-control-label" for="featureAC">Air Conditioning</label>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="featurePool" value="Swimming Pool">
              <label class="custom-control-label" for="featurePool">Swimming Pool</label>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="featureGym" value="Gym">
              <label class="custom-control-label" for="featureGym">Gym</label>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="featureGarden" value="Garden">
              <label class="custom-control-label" for="featureGarden">Garden</label>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="featureSecurity" value="24/7 Security">
              <label class="custom-control-label" for="featureSecurity">24/7 Security</label>
            </div>
          </div>

          <!-- Owner Information -->
          <div class="form-section-header">
            <h3><i class="fa fa-user"></i> Owner Information</h3>
          </div>

          <div class="form-group">
            <label for="ownerName">Owner Name <span class="required-field">*</span></label>
            <input type="text" class="form-control form-control-a" id="ownerName" placeholder="Full name" required>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="ownerEmail">Owner Email <span class="required-field">*</span></label>
              <input type="email" class="form-control form-control-a" id="ownerEmail" placeholder="owner@email.com" required>
            </div>
            <div class="form-group col-md-6">
              <label for="ownerPhone">Owner Phone <span class="required-field">*</span></label>
              <input type="tel" class="form-control form-control-a" id="ownerPhone" placeholder="+1 234 567 8900" required>
            </div>
          </div>

          <div class="form-group">
            <label for="ownerWhatsApp">WhatsApp Number (Optional)</label>
            <input type="tel" class="form-control form-control-a" id="ownerWhatsApp" placeholder="+1 234 567 8900">
          </div>

          <button type="submit" class="btn btn-b">Add Property</button>
        </form>
      </section>
    </article>
  </div>

  <script src="lib/jquery/jquery.min.js"></script>
  <script>
    function handleAddPropertySubmit(event) {
      event.preventDefault();

      const propertyTitle = document.getElementById('propertyTitle').value.trim();
      const propertyType = document.getElementById('propertyType').value;
      const propertyStatus = document.getElementById('propertyStatus').value;
      const propertyDescription = document.getElementById('propertyDescription').value.trim();
      const propertyAddress = document.getElementById('propertyAddress').value.trim();
      const propertyCity = document.getElementById('propertyCity').value.trim();
      const propertyState = document.getElementById('propertyState').value.trim();
      const propertyZipCode = document.getElementById('propertyZipCode').value.trim();
      const propertyCountry = document.getElementById('propertyCountry').value.trim();
      const propertyBedrooms = document.getElementById('propertyBedrooms').value;
      const propertyBathrooms = document.getElementById('propertyBathrooms').value;
      const propertyArea = document.getElementById('propertyArea').value;
      const propertyYearBuilt = document.getElementById('propertyYearBuilt').value;
      const propertyGarage = document.getElementById('propertyGarage').value.trim();
      const propertyPrice = document.getElementById('propertyPrice').value;
      const propertyCurrency = document.getElementById('propertyCurrency').value;
      const ownerName = document.getElementById('ownerName').value.trim();
      const ownerEmail = document.getElementById('ownerEmail').value.trim();
      const ownerPhone = document.getElementById('ownerPhone').value.trim();
      const ownerWhatsApp = document.getElementById('ownerWhatsApp').value.trim();

      // Collect features
      const features = [];
      document.querySelectorAll('.form-group input[type="checkbox"]:checked').forEach(checkbox => {
        features.push(checkbox.value);
      });

      if (!propertyTitle || !propertyType || !propertyStatus || !propertyAddress || !propertyCity || !propertyPrice) {
        showMessage('Please fill in all required fields', 'alert-danger');
        return;
      }

      const btn = event.target.querySelector('button[type="submit"]');
      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Adding Property...';

      fetch('api-properties.php?action=create', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          propertyTitle,
          propertyType,
          propertyStatus,
          propertyDescription,
          propertyAddress,
          propertyCity,
          propertyState,
          propertyZipCode,
          propertyCountry,
          propertyBedrooms,
          propertyBathrooms,
          propertyArea,
          propertyYearBuilt,
          propertyGarage,
          propertyPrice,
          propertyCurrency,
          features: features.join(','),
          ownerName,
          ownerEmail,
          ownerPhone,
          ownerWhatsApp
        })
      })
      .then(response => response.json())
      .then(data => {
        btn.disabled = false;
        btn.textContent = originalText;

        if (data.status === 'success') {
          showMessage('Property added successfully!', 'alert-success');
          event.target.reset();
          setTimeout(() => {
            window.location.href = 'properties.php';
          }, 2000);
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
      const messageDiv = document.getElementById('addPropertyMessage');
      messageDiv.textContent = message;
      messageDiv.className = 'alert ' + alertClass;
      messageDiv.style.display = 'block';
    }
  </script>

<script src="lib/popper/popper.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
