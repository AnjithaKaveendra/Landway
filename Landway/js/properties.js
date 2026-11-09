const typeSelect = document.getElementById('propertyTypeFilter');
const priceSelect = document.getElementById('priceFilter');
const currencySelect = document.getElementById('currencyFilter');
for (const select of [typeSelect, priceSelect, currencySelect]) select.addEventListener('change', filterProperties);
function filterProperties() {
  const type = typeSelect.value;
  const price = priceSelect.value;
  if (price !== 'all' && currencySelect.value === 'all') currencySelect.value = 'LKR';
  let visible = 0;
  const cards = document.querySelectorAll('.property-card-full');
  for (const card of cards) {
    const amount = Number(card.dataset.price);
    let show = (type === 'all' || card.dataset.type === type) &&
      (currencySelect.value === 'all' || card.dataset.currency === currencySelect.value);
    if (price === '0-100000') show &&= amount < 100000;
    if (price === '100000-300000') show &&= amount >= 100000 && amount < 300000;
    if (price === '300000-500000') show &&= amount >= 300000 && amount < 500000;
    if (price === '500000+') show &&= amount >= 500000;
    card.style.display = show ? '' : 'none';
    if (show) visible++;
  }
  document.getElementById('noFilterResults').hidden = visible > 0 || cards.length === 0;
}
