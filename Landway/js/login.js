async function handleLogin(event) {
  event.preventDefault();
  const button = event.target.querySelector('button[type="submit"]');
  const label = button.textContent;
  const payload = {email: document.getElementById('email').value.trim(), password: document.getElementById('password').value};
  button.disabled = true;
  button.textContent = 'Logging in...';
  try {
    let response = await fetch('api-admin.php?action=login', {
      method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload)
    });
    let data = await response.json();
    if (response.status === 401) {
      response = await fetch('api-users.php?action=login', {
        method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload)
      });
      data = await response.json();
    }
    if (data.status !== 'success') throw new Error(data.message);
    document.getElementById('errorMessage').style.display = 'none';
    document.getElementById('successMessage').textContent = 'Login successful. Redirecting...';
    document.getElementById('successMessage').style.display = 'block';
    location.href = data.admin ? 'admin-dashboard.php' : 'index.php';
  } catch (error) {
    document.getElementById('errorMessage').textContent = error.message || 'Unable to log in. Please try again.';
    document.getElementById('errorMessage').style.display = 'block';
    document.getElementById('successMessage').style.display = 'none';
  } finally {
    button.disabled = false;
    button.textContent = label;
  }
}
