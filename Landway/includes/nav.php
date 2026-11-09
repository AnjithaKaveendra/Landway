<nav class="navbar navbar-default navbar-trans navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand text-brand" href="index.php">Land<span class="color-b">way</span></a>
    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarDefault" aria-controls="navbarDefault" aria-expanded="false" aria-label="Toggle navigation"><span></span><span></span><span></span></button>
    <div class="navbar-collapse collapse justify-content-center" id="navbarDefault"><ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      <li class="nav-item"><a class="nav-link" href="properties.php">Properties</a></li>
      <li class="nav-item"><a class="nav-link" href="add-property.php">List Property</a></li>
      <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      <?php if (isAdmin()): ?>
      <li class="nav-item"><a class="nav-link" href="admin-dashboard.php">Dashboard</a></li>
      <?php elseif (isLoggedIn()): ?>
      <li class="nav-item"><a class="nav-link" href="saved-properties.php">Saved Properties</a></li>
      <?php else: ?>
      <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
      <?php endif; ?>
      <?php if (isLoggedIn()): ?>
      <li class="nav-item"><button class="btn btn-link nav-link" type="button" onclick="fetch('api-users.php?action=logout', {method:'POST'}).then(r=>r.json()).then(data=>{if(data.status==='success') location.href='index.php'; else alert(data.message);}).catch(()=>alert('Unable to log out. Please try again.'))">Logout</button></li>
      <?php endif; ?>
    </ul></div>
  </div>
</nav>
