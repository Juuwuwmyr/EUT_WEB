<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EUT Restaurant</title>
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

    <!-- Checkout Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Checkout</h1>

        <form id="checkoutForm" class="grid lg:grid-cols-3 gap-6">
            <!-- Shipping & Payment Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Address -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Delivery Address
                    </h3>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:border-orange-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" rows="3" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:border-orange-500"></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" name="city" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                            <input type="text" name="postal" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:border-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Payment Method
                    </h3>

                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-orange-500 transition">
                            <input type="radio" name="payment" value="cod" checked class="w-5 h-5 accent-orange-500">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">💵</span>
                                <div>
                                    <div class="font-medium text-gray-800">Cash on Delivery</div>
                                    <div class="text-sm text-gray-500">Pay when you receive your order</div>
                                </div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-orange-500 transition">
                            <input type="radio" name="payment" value="gcash" class="w-5 h-5 accent-orange-500">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">📱</span>
                                <div>
                                    <div class="font-medium text-gray-800">GCash</div>
                                    <div class="text-sm text-gray-500">Pay via GCash e-wallet</div>
                                </div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-orange-500 transition">
                            <input type="radio" name="payment" value="card" class="w-5 h-5 accent-orange-500">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">💳</span>
                                <div>
                                    <div class="font-medium text-gray-800">Credit/Debit Card</div>
                                    <div class="text-sm text-gray-500">Pay with Visa or Mastercard</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Order Summary</h3>

                    <div class="max-h-64 overflow-y-auto mb-4 space-y-3" id="checkoutItemsList"></div>

                    <div class="border-t pt-4 space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span id="checkoutSubtotal">₱0</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Delivery Fee</span>
                            <span>₱50</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between text-xl font-bold">
                            <span>Total</span>
                            <span id="checkoutGrandTotal" class="shopee-orange-text">₱0</span>
                        </div>
                    </div>

                    <button type="submit" class="shopee-orange text-white w-full py-3 rounded-lg font-semibold mt-6 hover:bg-orange-600 transition">
                        Place Order
                    </button>
                </div>
            </div>
        </form>
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
        function renderCheckout() {
            const cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
            const itemsList = document.getElementById('checkoutItemsList');
            itemsList.innerHTML = '';

            let subtotal = 0;

            cart.forEach(item => {
                const itemEl = document.createElement('div');
                itemEl.className = 'flex items-center gap-3';
                itemEl.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-800">${item.name}</div>
                        <div class="text-xs text-gray-500">x${item.quantity}</div>
                    </div>
                    <div class="text-sm shopee-orange-text font-medium">₱${(item.price * item.quantity).toLocaleString()}</div>
                `;
                itemsList.appendChild(itemEl);
                subtotal += item.price * item.quantity;
            });

            document.getElementById('checkoutSubtotal').textContent = '₱' + subtotal.toLocaleString();
            document.getElementById('checkoutGrandTotal').textContent = '₱' + (subtotal + 50).toLocaleString();
        }

        renderCheckout();

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Simulate order placement
            localStorage.setItem('eutCart', JSON.stringify([]));
            alert('Order placed successfully!');
            window.location.href = '{{ route('shop.tracking') }}';
        });
    </script>
</body>
</html>
