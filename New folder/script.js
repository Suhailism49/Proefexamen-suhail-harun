// Beheer het winkelmandje in de frontend en stuur de bestelling door naar PHP.
const cartItemsElement = document.getElementById('cartItems');
const cartTotalElement = document.getElementById('cartTotal');
const clearCartButton = document.getElementById('clearCart');
const orderForm = document.getElementById('orderForm');
const orderMessage = document.getElementById('orderMessage');
const cartDataInput = document.getElementById('cartData');

let cart = JSON.parse(localStorage.getItem('caribbean-cart') || '[]');

function formatCurrency(value) {
  return `€${value.toFixed(2).replace('.', ',')}`;
}

function saveCart() {
  localStorage.setItem('caribbean-cart', JSON.stringify(cart));
}

// Toon de huidige inhoud van het winkelmandje op de pagina.
function renderCart() {
  if (!cart.length) {
    cartItemsElement.innerHTML = '<p class="empty-cart">Je winkelmandje is nog leeg.</p>';
    cartTotalElement.textContent = formatCurrency(0);
    return;
  }

  const itemsMarkup = cart
    .map(
      (item) => `
        <div class="cart-item">
          <div class="cart-item-left">
            <strong>${item.name}</strong>
            <small>${item.quantity} x ${formatCurrency(item.price)}</small>
          </div>
          <div>
            <strong>${formatCurrency(item.price * item.quantity)}</strong>
            <button class="remove-item" data-id="${item.id}">Verwijder</button>
          </div>
        </div>
      `
    )
    .join('');

  cartItemsElement.innerHTML = itemsMarkup;
  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  cartTotalElement.textContent = formatCurrency(total);
}

// Voeg een gerecht toe aan het winkelmandje.
function addToCart(id, name, price) {
  const existing = cart.find((item) => item.id === id);

  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({ id, name, price, quantity: 1 });
  }

  saveCart();
  renderCart();
  orderMessage.textContent = `${name} toegevoegd aan je mandje.`;
  orderMessage.style.color = '#0f766e';
}

// Verwijder een product uit het winkelmandje.
function removeFromCart(id) {
  cart = cart.filter((item) => item.id !== id);
  saveCart();
  renderCart();
}

// Maak het winkelmandje leeg.
function clearCart() {
  cart = [];
  saveCart();
  renderCart();
  orderMessage.textContent = 'Je winkelmandje is geleegd.';
  orderMessage.style.color = '#a8340f';
}

clearCartButton.addEventListener('click', clearCart);

document.querySelectorAll('.add-to-cart').forEach((button) => {
  button.addEventListener('click', () => {
    addToCart(Number(button.dataset.id), button.dataset.name, Number(button.dataset.price));
  });
});

cartItemsElement.addEventListener('click', (event) => {
  const removeButton = event.target.closest('.remove-item');
  if (removeButton) {
    removeFromCart(Number(removeButton.dataset.id));
  }
});

// Verstuur de gekozen producten met de bestelgegevens naar de PHP-server.
orderForm.addEventListener('submit', (event) => {
  if (!cart.length) {
    event.preventDefault();
    orderMessage.textContent = 'Voeg eerst producten toe aan je mandje.';
    orderMessage.style.color = '#a8340f';
    return;
  }

  cartDataInput.value = JSON.stringify(cart);
  cart = [];
  saveCart();
  renderCart();
});

renderCart();
