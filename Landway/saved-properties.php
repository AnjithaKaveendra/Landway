<?php
require_once __DIR__ . '/app.php';
if (empty($_SESSION['userSession']['userId'])) {
    header('Location: login.php');
    exit;
}
$properties = [];
$databaseError = false;
try {
    $properties = dbQuery('SELECT p.* FROM properties p JOIN favorites f ON f.propertyId = p.id WHERE f.userId = ? ORDER BY f.created_at DESC',
        [$_SESSION['userSession']['userId']])->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $error) {
    error_log((string) $error);
    http_response_code(503);
    $databaseError = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= e($_SESSION['csrfToken']) ?>">
  <title>Saved Properties - Landway</title>
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet"><link href="css/style.css" rel="stylesheet">
  <script src="js/api.js"></script>
</head>
<body class="policy-page">
<?php require __DIR__ . '/includes/nav.php'; ?>
<section class="policy-header"><div class="container"><h1>Saved Properties</h1><p>Your saved listings, available whenever you sign in.</p></div></section>
<main class="policy-container"><article class="policy-article"><section class="policy-section">
  <?php if ($databaseError): ?><p role="alert">Your saved listings are temporarily unavailable.</p>
  <?php elseif (!$properties): ?><p>You have no saved properties yet. <a href="properties.php">Browse properties</a> to find one.</p><?php endif; ?>
  <div class="properties-grid">
    <?php foreach ($properties as $property): ?>
    <div class="property-card-full">
      <div class="property-image-full"><a href="property-detail.php?id=<?= (int) $property['id'] ?>"><img src="<?= e(propertyImage($property)) ?>" alt="<?= e($property['propertyTitle']) ?>"></a></div>
      <div class="property-info-full"><h2 class="property-title-full"><a href="property-detail.php?id=<?= (int) $property['id'] ?>"><?= e($property['propertyTitle']) ?></a></h2><p><?= e($property['propertyCity']) ?></p><p><?= e($property['propertyCurrency']) ?> <?= number_format((float) $property['propertyPrice'], 2) ?></p></div>
    </div>
    <?php endforeach; ?>
  </div>
</section></article></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
<script src="lib/jquery/jquery.min.js"></script><script src="lib/popper/popper.min.js"></script><script src="lib/bootstrap/js/bootstrap.min.js"></script>
</body></html>
