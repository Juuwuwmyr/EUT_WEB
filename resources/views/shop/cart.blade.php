<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .shopee-orange { background-color: #ee4d2d; }
        .shopee-orange-text { color: #ee4d2d; }
        .shopee-bg-light { background-color: #f5f5f5; }
    </style>
</head>
<body class="shopee-bg-light">
    <!-- Navbar -->
    <nav class="shopee-orange text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('shop.home') }}" class="text-2xl font-bold flex items-center gap-2">
                    <span class="text-3xl">🍔</span>
                    EUT Food
                </a>
            </div>
        </div>
    </nav>

    <!-- Cart Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">My Shopping Cart</h1>

        <div id="emptyCart" class="bg-white rounded-lg shadow-sm p-12 text-center" style="display: none;">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Your cart is empty</h3>
            <p class="text-gray-500 mb-6">Add some delicious items to your cart!</p>
            <a href="{{ route('shop.home') }}" class="shopee-orange text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-600 transition inline-block">
                Start Shopping
            </a>
        </div>

        <div id="cartContainer" class="grid lg:grid-cols-3 gap-6" style="display: none;">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm">
                    <!-- Cart Header -->
                    <div class="grid grid-cols-12 gap-4 p-4 border-b text-sm text-gray-500 font-medium">
                        <div class="col-span-1">Select</div>
                        <div class="col-span-5">Product</div>
                        <div class="col-span-2 text-center">Price</div>
                        <div class="col-span-2 text-center">Quantity</div>
                        <div class="col-span-2 text-center">Total</div>
                    </div>

                    <!-- Cart Items -->
                    <div id="cartItemsList"></div>
                </div>
            </div>

                <!-- Order Summary -->
                <div>
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-20">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Order Summary</h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal (<span id="totalItems">0</span> items)</span>
                                <span id="subtotal">₱0</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Delivery Fee</span>
                                <span id="deliveryFee">₱50</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between text-xl font-bold">
                                <span>Total</span>
                                <span id="grandTotal" class="shopee-orange-text">₱0</span>
                            </div>
                        </div>

                        <a href="{{ route('shop.checkout') }}" class="shopee-orange text-white w-full py-3 rounded-lg font-semibold mt-6 block text-center hover:bg-orange-600 transition">
                            Checkout
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h3 class="text-xl font-bold mb-2">EUT Restaurant</h3>
            <p class="text-gray-400 text-sm mb-4">Eat • Unwind • Tea - Delicious Food Delivered to Your Doorstep</p>
            <p class="text-gray-500 text-xs">&copy; 2026 EUT Restaurant. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let cart = JSON.parse(localStorage.getItem('eutCart') || '[]');

        function saveCart() {
            localStorage.setItem('eutCart', JSON.stringify(cart));
        }

        function renderCart() {
            const cartItemsList = document.getElementById('cartItemsList');
            const emptyCart = document.getElementById('emptyCart');
            const cartContainer = document.getElementById('cartContainer');

            if (cart.length === 0) {
                emptyCart.style.display = 'block';
                cartContainer.style.display = 'none';
                return;
            }

            emptyCart.style.display = 'none';
            cartContainer.style.display = 'grid';
            cartItemsList.innerHTML = '';

            cart.forEach(item => {
                const itemEl = document.createElement('div');
                itemEl.className = 'cart-item grid grid-cols-12 gap-4 p-4 border-b items-center';
                itemEl.dataset.id = item.id;
                itemEl.dataset.price = item.price;
                itemEl.dataset.quantity = item.quantity;

                itemEl.innerHTML = `
                    <div class="col-span-1">
                        <input type="checkbox" class="item-checkbox w-5 h-5 accent-orange-500" checked>
                    </div>
                    <div class="col-span-5 flex gap-4 items-center">
                        <img src="${item.image}" alt="${item.name}" class="w-20 h-20 object-cover rounded">
                        <div>
                            <h3 class="font-medium text-gray-800">${item.name}</h3>
                        </div>
                    </div>
                    <div class="col-span-2 text-center shopee-orange-text font-semibold">₱${item.price.toLocaleString()}</div>
                    <div class="col-span-2 flex justify-center">
                        <div class="flex items-center gap-2 border rounded">
                            <button class="qty-decrease px-3 py-1 hover:bg-gray-100 transition">-</button>
                            <input type="number" class="qty-input w-12 text-center py-1 border-x" value="${item.quantity}" min="1">
                            <button class="qty-increase px-3 py-1 hover:bg-gray-100 transition">+</button>
                        </div>
                    </div>
                    <div class="col-span-2 text-center">
                        <span class="item-total shopee-orange-text font-semibold text-lg">₱${(item.price * item.quantity).toLocaleString()}</span>
                        <button class="remove-item ml-3 text-gray-400 hover:text-red-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                `;

                cartItemsList.appendChild(itemEl);

                // Attach event listeners to the new element
                const qtyInput = itemEl.querySelector('.qty-input');
                const qtyDecrease = itemEl.querySelector('.qty-decrease');
                const qtyIncrease = itemEl.querySelector('.qty-increase');
                const itemTotal = itemEl.querySelector('.item-total');
                const removeBtn = itemEl.querySelector('.remove-item');
                const price = item.price;
                const id = item.id;

                qtyDecrease.addEventListener('click', () => {
                    if (parseInt(qtyInput.value) > 1) {
                        qtyInput.value = parseInt(qtyInput.value) - 1;
                        updateItem();
                    }
                });

                qtyIncrease.addEventListener('click', () => {
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                    updateItem();
                });

                qtyInput.addEventListener('change', updateItem);

                removeBtn.addEventListener('click', () => {
                    cart = cart.filter(cartItem => cartItem.id !== id);
                    saveCart();
                    renderCart();
                });

                function updateItem() {
                    const qty = parseInt(qtyInput.value);
                    const cartItem = cart.find(c => c.id === id);
                    if (cartItem) cartItem.quantity = qty;
                    saveCart();
                    itemEl.dataset.quantity = qty;
                    itemTotal.textContent = '₱' + (price * qty).toLocaleString();
                    updateTotals();
                }
            });

            // Re-attach checkbox listeners
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateTotals);
            });

            updateTotals();
        }

        function updateTotals() {
            let subtotal = 0;
            let totalItems = 0;

            document.querySelectorAll('.cart-item').forEach(item => {
                if (item.querySelector('.item-checkbox').checked) {
                    const price = parseInt(item.dataset.price);
                    const qty = parseInt(item.querySelector('.qty-input').value);
                    subtotal += price * qty;
                    totalItems += qty;
                }
            });

            const deliveryFee = 50;
            const grandTotal = subtotal + deliveryFee;

            document.getElementById('subtotal').textContent = '₱' + subtotal.toLocaleString();
            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('grandTotal').textContent = '₱' + grandTotal.toLocaleString();
        }

        // Initial render
        renderCart();
    </script>
</body>
</html>
