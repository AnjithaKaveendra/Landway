<?php
require_once __DIR__ . '/app.php';
$properties = [];
$databaseError = false;
try {
    $properties = dbQuery('SELECT * FROM properties ORDER BY listedDate DESC, id DESC')->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $error) {
    error_log((string) $error);
    http_response_code(503);
    $databaseError = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="csrf-token" content="<?= e($_SESSION['csrfToken']) ?>">
  <script src="js/api.js"></script>
  <meta charset="utf-8">
  <title>Available Properties - Landway</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

  <!-- Bootstrap CSS File -->
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Libraries CSS Files -->
  <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="lib/animate/animate.min.css" rel="stylesheet">

  <!-- Main Stylesheet File -->
  <link href="css/style.css" rel="stylesheet">
</head>

<body class="policy-page">

  <!--/ Nav Star /-->
  <?php require __DIR__ . '/includes/nav.php'; ?>
  <!--/ Nav End /-->

  <!--/ Properties Header /-->
  <section class="policy-header">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1>Available Properties</h1>
          <p>Explore our collection of premium properties available for sale</p>
        </div>
      </div>
    </div>
  </section>
  <!--/ Properties Header End /-->

  <!--/ Properties Main Content /-->
  <div class="policy-container">
    <article class="policy-article">
      
      <!--/ Add Property Button Section /-->
      <section class="policy-section add-property-section">
        <div class="add-property-banner">
          <div class="add-property-content">
            <h3>Have a Property to Sell?</h3>
            <p>List your property with us and reach thousands of potential buyers</p>
            <a href="add-property.php" class="btn-b">
              <i class="fa fa-plus-circle"></i> Add Your Property
            </a>
          </div>
        </div>
      </section>
      <!--/ Add Property Button Section End /-->

      <!--/ Properties Grid Section /-->
      <section class="policy-section properties-grid-section">
        <div class="properties-header">
          <h2>Featured Properties</h2>
          <div class="properties-filter">
            <select class="filter-select" id="propertyTypeFilter">
              <option value="all">All Types</option>
              <option value="house">House</option>
              <option value="land">Land</option>
              <option value="apartment">Apartment</option>
              <option value="commercial">Commercial</option>
            </select>
            <select class="filter-select" id="currencyFilter" aria-label="Currency">
              <option value="all">All Currencies</option><option value="LKR">LKR</option><option value="USD">USD</option><option value="EUR">EUR</option><option value="GBP">GBP</option>
            </select>
            <select class="filter-select" id="priceFilter" aria-label="Price range">
              <option value="all">All Prices</option>
              <option value="0-100000">Under 100,000</option>
              <option value="100000-300000">100,000 - 300,000</option>
              <option value="300000-500000">300,000 - 500,000</option>
              <option value="500000+">Above 500,000</option>
            </select>
          </div>
        </div>

        <?php if ($databaseError): ?>
          <p class="alert alert-warning" role="alert">Property listings are temporarily unavailable. Please try again later.</p>
        <?php elseif (!$properties): ?>
          <p>No properties have been listed yet. Be the first to add one.</p>
        <?php endif; ?>
        <p id="noFilterResults" hidden>No properties match these filters.</p>
        <div class="properties-grid">
          <?php foreach ($properties as $property): $detailUrl = 'property-detail.php?id=' . (int) $property['id']; ?>
          <div class="property-card-full" data-type="<?= e($property['propertyType']) ?>" data-price="<?= e($property['propertyPrice']) ?>" data-currency="<?= e($property['propertyCurrency']) ?>">
            <div class="property-image-full">
              <img src="<?= e(propertyImage($property)) ?>" alt="<?= e($property['propertyTitle']) ?>">
              <div class="property-overlay-full"><a href="<?= e($detailUrl) ?>" class="btn-view-details-full">View Details</a></div>
              <span class="property-badge-full featured">For <?= e(ucfirst($property['propertyStatus'])) ?></span>
            </div>
            <div class="property-info-full">
              <h3 class="property-title-full"><a href="<?= e($detailUrl) ?>"><?= e($property['propertyTitle']) ?></a></h3>
              <p class="property-location-full"><i class="fa fa-map-marker"></i> <?= e($property['propertyAddress']) ?>, <?= e($property['propertyCity']) ?></p>
              <p class="property-description-full"><?= e($property['propertyDescription']) ?></p>
              <div class="property-specs-full">
                <span><?= (int) $property['propertyBedrooms'] ?> Beds</span>
                <span><?= (int) $property['propertyBathrooms'] ?> Baths</span>
                <span><?= e(number_format((float) $property['propertyArea'])) ?> sqft</span>
              </div>
              <div class="property-footer-full">
                <div class="property-price-full"><h4><?= e($property['propertyCurrency']) ?> <?= number_format((float) $property['propertyPrice'], 2) ?></h4></div>
                <a href="<?= e($detailUrl) ?>#inquiry" class="btn-inquiry-full">Make Inquiry</a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>
      <!--/ Properties Grid Section End /-->

    </article>
  </div>
  <!--/ Properties Main Content End /-->

  <!--/ footer Star /-->
  <section class="section-footer">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-4">
          <div class="widget-a">
            <div class="w-header-a">
              <h3 class="w-title-a text-brand">Landway</h3>
            </div>
            <div class="w-body-a">
              <p class="w-text-a color-text-a">
                Your trusted partner in finding the perfect property. We help you discover, buy, and sell properties with confidence.
              </p>
            </div>
            <div class="w-footer-a">
              <ul class="list-unstyled">
                <li class="color-a">
                  <span class="color-text-a">Phone .</span> contact@landway.com</li>
                <li class="color-a">
                  <span class="color-text-a">Email .</span> +1 234 567 8900</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-4 section-md-t3">
          <div class="widget-a">
            <div class="w-header-a">
              <h3 class="w-title-a text-brand">Quick Links</h3>
            </div>
            <div class="w-body-a">
              <div class="w-body-a">
                <ul class="list-unstyled">
                  <li class="item-list-a">
                    <i class="fa fa-angle-right"></i> <a href="index.php">Home</a>
                  </li>
                  <li class="item-list-a">
                    <i class="fa fa-angle-right"></i> <a href="#about">About Us</a>
                  </li>
                  <li class="item-list-a">
                    <i class="fa fa-angle-right"></i> <a href="properties.php">Properties</a>
                  </li>
                  <li class="item-list-a">
                    <i class="fa fa-angle-right"></i> <a href="#contact">Contact</a>
                  </li>
                  <li class="item-list-a">
                    <i class="fa fa-angle-right"></i> <a href="policy.php">Privacy Policy</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-4 section-md-t3">
          <div class="widget-a">
            <div class="w-header-a">
              <h3 class="w-title-a text-brand">Connect With Us</h3>
            </div>
            <div class="w-body-a">
              <p class="w-text-a color-text-a">
                Follow us on social media for the latest property listings and updates.
              </p>
              <ul class="list-unstyled">
                <li class="item-list-a">
                  <i class="fa fa-facebook"></i> <a href="#">Facebook</a>
                </li>
                <li class="item-list-a">
                  <i class="fa fa-twitter"></i> <a href="#">Twitter</a>
                </li>
                <li class="item-list-a">
                  <i class="fa fa-instagram"></i> <a href="#">Instagram</a>
                </li>
                <li class="item-list-a">
                  <i class="fa fa-linkedin"></i> <a href="#">LinkedIn</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <nav class="nav-footer">
            <ul class="list-inline">
              <li class="list-inline-item">
                <a href="index.php">Home</a>
              </li>
              <li class="list-inline-item">
                <a href="#about">About</a>
              </li>
              <li class="list-inline-item">
                <a href="properties.php">Properties</a>
              </li>
              <li class="list-inline-item">
                <a href="#contact">Contact</a>
              </li>
            </ul>
          </nav>
          <div class="socials-a">
            <ul class="list-inline">
              <li class="list-inline-item">
                <a href="#">
                  <i class="fa fa-facebook" aria-hidden="true"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="fa fa-twitter" aria-hidden="true"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="fa fa-instagram" aria-hidden="true"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="fa fa-linkedin" aria-hidden="true"></i>
                </a>
              </li>
            </ul>
          </div>
          <div class="copyright-footer">
            <p class="copyright color-text-a">
              &copy; Copyright 2024
              <span class="color-a">Landway</span> All Rights Reserved.
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <!--/ Footer End /-->

  <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

  <!-- JavaScript Libraries -->
  <script src="lib/jquery/jquery.min.js"></script>
  <script src="lib/jquery/jquery-migrate.min.js"></script>
  <script src="lib/popper/popper.min.js"></script>
  <script src="lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="lib/easing/easing.min.js"></script>

  <!-- Contact Form JavaScript File -->
  <script src="js/main.js"></script>

  <script src="js/properties.js"></script>

</body>
</html>
