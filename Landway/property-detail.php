<?php
require_once __DIR__ . '/app.php';
$property = null;
$errorMessage = '';
$saved = false;
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    http_response_code(404);
    $errorMessage = 'Property not found.';
} else {
    try {
        $property = dbQuery('SELECT * FROM properties WHERE id = ?', [$id])->get_result()->fetch_assoc();
        if (!$property) {
            http_response_code(404);
            $errorMessage = 'Property not found.';
        } elseif (!empty($_SESSION['userSession']['userId'])) {
            $saved = dbQuery('SELECT propertyId FROM favorites WHERE userId = ? AND propertyId = ?',
                [$_SESSION['userSession']['userId'], $id])->get_result()->num_rows > 0;
        }
    } catch (Throwable $error) {
        error_log((string) $error);
        http_response_code(503);
        $property = null;
        $errorMessage = 'This listing is temporarily unavailable. Please try again later.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= e($_SESSION['csrfToken']) ?>">
  <title><?= e($property['propertyTitle'] ?? 'Property') ?> - Landway</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <script src="js/api.js"></script>
</head>
<body>
<?php require __DIR__ . '/includes/nav.php'; ?>
<?php if (!$property): ?>
  <main class="container" style="padding:180px 20px 80px"><h1><?= e($errorMessage) ?></h1><a href="properties.php">Browse properties</a></main>
<?php else: ?>
<main class="property-detail-wrapper" id="propertyDetail" data-id="<?= (int) $property['id'] ?>" data-title="<?= e($property['propertyTitle']) ?>" data-type="<?= e($property['propertyType']) ?>" data-currency="<?= e($property['propertyCurrency']) ?>">
  <section class="property-detail-hero">
    <img src="<?= e(propertyImage($property)) ?>" alt="<?= e($property['propertyTitle']) ?>" id="mainPropertyImage">
    <div class="property-hero-overlay"><div class="container"><span class="badge-featured">For <?= e(ucfirst($property['propertyStatus'])) ?></span></div></div>
  </section>
  <div class="container property-detail-container"><div class="row">
    <div class="col-lg-8">
      <div class="property-detail-header">
        <div class="property-title-section"><h1><?= e($property['propertyTitle']) ?></h1><p class="property-detail-location"><?= e($property['propertyAddress']) ?>, <?= e($property['propertyCity']) ?></p></div>
        <div class="property-price-section"><h2 class="property-detail-price"><?= e($property['propertyCurrency']) ?> <?= number_format((float) $property['propertyPrice'], 2) ?></h2><p class="price-label">Price</p></div>
      </div>
      <div class="property-quick-info">
        <?php foreach (['propertyBedrooms' => 'Bedrooms', 'propertyBathrooms' => 'Bathrooms', 'propertyArea' => 'sqft', 'propertyGarage' => 'Parking'] as $field => $label): ?>
        <div class="quick-info-item"><div class="quick-info-text"><h4><?= e($property[$field] ?: '—') ?></h4><p><?= e($label) ?></p></div></div>
        <?php endforeach; ?>
      </div>
      <section class="property-detail-section"><h3>Description</h3><p><?= nl2br(e($property['propertyDescription'])) ?></p></section>
      <section class="property-detail-section"><h3>Property Details</h3><div class="property-details-grid">
        <?php foreach (['id' => 'Property ID', 'propertyType' => 'Type', 'propertyStatus' => 'Status', 'propertyYearBuilt' => 'Year Built', 'propertyState' => 'State / Province', 'propertyCountry' => 'Country', 'propertyZipCode' => 'Postal Code'] as $field => $label): ?>
        <div class="detail-item"><span class="detail-label"><?= e($label) ?>:</span><span class="detail-value"><?= e($property[$field] ?: '—') ?></span></div>
        <?php endforeach; ?>
      </div></section>
      <section class="property-detail-section"><h3>Features &amp; Amenities</h3><div class="features-grid">
        <?php foreach (propertyFeatures($property) as $feature): ?><div class="feature-item"><i class="fa fa-check"></i> <?= e($feature) ?></div><?php endforeach; ?>
        <?php if (!propertyFeatures($property)): ?><p>No additional features listed.</p><?php endif; ?>
      </div></section>
    </div>
    <aside class="col-lg-4">
      <section class="sidebar-card" id="inquiry">
        <h3>Contact about this property</h3><p><?= e($property['ownerName']) ?></p>
        <form class="contact-agent-form" id="propertyInquiryForm">
          <div class="form-group"><label for="inquiryName">Your name</label><input id="inquiryName" name="contactName" class="form-control-a" maxlength="100" value="<?= e(($_SESSION['userSession']['firstName'] ?? '') . ' ' . ($_SESSION['userSession']['lastName'] ?? '')) ?>" required></div>
          <div class="form-group"><label for="inquiryEmail">Email</label><input id="inquiryEmail" type="email" name="contactEmail" class="form-control-a" maxlength="120" value="<?= e($_SESSION['userSession']['email'] ?? '') ?>" required></div>
          <div class="form-group"><label for="inquiryPhone">Phone</label><input id="inquiryPhone" type="tel" name="contactPhone" class="form-control-a" maxlength="20"></div>
          <div class="form-group"><label for="inquiryMessage">Message</label><textarea id="inquiryMessage" name="contactMessageText" class="form-control-a" rows="4" maxlength="20000" required>I'm interested in <?= e($property['propertyTitle']) ?>.</textarea></div>
          <p id="inquiryResult" role="status"></p><button type="submit" class="btn-b btn-block">Send Message</button>
        </form>
      </section>
      <section class="sidebar-card property-actions">
        <button type="button" class="action-btn" id="shareListing">Share Listing</button>
        <button type="button" class="action-btn" onclick="window.print()">Print Details</button>
        <?php if (!isAdmin()): ?><button type="button" class="action-btn" id="saveListing" aria-pressed="<?= $saved ? 'true' : 'false' ?>"><?= $saved ? 'Remove Saved Property' : 'Save Property' ?></button><?php endif; ?>
        <p id="actionResult" role="status"></p>
      </section>
      <section class="sidebar-card">
        <h3>Mortgage Calculator</h3>
        <form id="mortgageForm" class="mortgage-calculator">
          <div class="form-group"><label for="propertyPrice">Price (<?= e($property['propertyCurrency']) ?>)</label><input class="form-control-a" type="number" id="propertyPrice" min="0.01" step="0.01" value="<?= e($property['propertyPrice']) ?>" required></div>
          <div class="form-group"><label for="downPayment">Down Payment (%)</label><input class="form-control-a" type="number" id="downPayment" min="0" max="100" step="0.1" value="20" required></div>
          <div class="form-group"><label for="interestRate">Annual Interest (%)</label><input class="form-control-a" type="number" id="interestRate" min="0" max="100" step="0.01" value="3.5" required></div>
          <div class="form-group"><label for="loanTerm">Term (years)</label><input class="form-control-a" type="number" id="loanTerm" min="1" max="50" value="30" required></div>
          <button type="submit" class="btn-b btn-block">Calculate</button><p id="monthlyPayment" role="status"></p>
        </form>
      </section>
    </aside>
  </div></div>
</main>
<script src="js/property-detail.js"></script>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
<script src="lib/jquery/jquery.min.js"></script>
<script src="lib/popper/popper.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
