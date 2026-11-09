const detail = document.getElementById('propertyDetail');
document.getElementById('propertyInquiryForm').addEventListener('submit', async event => {
  event.preventDefault();
  const form = event.currentTarget;
  const button = form.querySelector('button[type="submit"]');
  const result = document.getElementById('inquiryResult');
  button.disabled = true;
  try {
    const payload = Object.fromEntries(new FormData(form));
    payload.contactSubject = `Property #${detail.dataset.id}: ${detail.dataset.title}`.slice(0, 200);
    payload.contactPropertyType = detail.dataset.type;
    const response = await fetch('api-inquiries.php?action=create', {
      method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload)
    });
    const data = await response.json();
    if (data.status !== 'success') throw new Error(data.message);
    result.textContent = 'Thank you. Your inquiry has been submitted to Landway.';
    form.reset();
  } catch (error) {
    result.textContent = error.message || 'Unable to send your inquiry. Please try again.';
  } finally {
    button.disabled = false;
  }
});
document.getElementById('saveListing')?.addEventListener('click', async event => {
  const button = event.currentTarget;
  button.disabled = true;
  try {
    const response = await fetch('api-favorites.php?action=toggle', {
      method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({id: Number(detail.dataset.id)})
    });
    const data = await response.json();
    if (response.status === 401) { location.href = 'login.php'; return; }
    if (data.status !== 'success') throw new Error(data.message);
    button.textContent = data.saved ? 'Remove Saved Property' : 'Save Property';
    button.setAttribute('aria-pressed', String(data.saved));
    document.getElementById('actionResult').textContent = data.message;
  } catch (error) {
    document.getElementById('actionResult').textContent = error.message;
  } finally {
    button.disabled = false;
  }
});
document.getElementById('shareListing').addEventListener('click', async () => {
  try {
    if (navigator.share) await navigator.share({title: detail.dataset.title, url: location.href});
    else if (navigator.clipboard) {
      await navigator.clipboard.writeText(location.href);
      document.getElementById('actionResult').textContent = 'Listing link copied.';
    } else document.getElementById('actionResult').textContent = location.href;
  } catch (error) {
    if (error.name !== 'AbortError') document.getElementById('actionResult').textContent = location.href;
  }
});
document.getElementById('mortgageForm').addEventListener('submit', event => {
  event.preventDefault();
  const price = Number(document.getElementById('propertyPrice').value);
  const down = Number(document.getElementById('downPayment').value);
  const rate = Number(document.getElementById('interestRate').value) / 1200;
  const months = Number(document.getElementById('loanTerm').value) * 12;
  const principal = price * (1 - down / 100);
  const monthly = rate === 0 ? principal / months : principal * rate / (1 - (1 + rate) ** -months);
  document.getElementById('monthlyPayment').textContent = `${detail.dataset.currency} ${monthly.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} per month`;
});
