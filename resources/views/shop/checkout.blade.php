<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .theme-toggle {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .theme-toggle:hover {
            background: rgba(255,255,255,0.2);
        }
        /* Light mode overrides */
        .light-mode body {
            background-color: #f5f5f5;
            color: #111;
        }
        .light-mode nav {
            background-color: rgba(255,255,255,0.95) !important;
            border-color: rgba(0,0,0,0.1) !important;
        }
        .light-mode .text-white {
            color: #111 !important;
        }
        .light-mode .text-gray-300 {
            color: #666 !important;
        }
        .light-mode .text-gray-400 {
            color: #888 !important;
        }
        .light-mode .border-white/10 {
            border-color: rgba(0,0,0,0.1) !important;
        }
        .light-mode .bg-black/95 {
            background-color: rgba(255,255,255,0.95) !important;
        }
        .light-mode .bg-gray-900 {
            background-color: #fff !important;
        }
        .light-mode .text-yellow-400 {
            color: #f59e0b !important;
        }
        .light-mode .text-red-400 {
            color: #ef4444 !important;
        }
        .light-mode input,
        .light-mode textarea {
            background-color: #fff !important;
            border-color: #d1d5db !important;
            color: #111 !important;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    <!-- Navbar -->
    <nav class="fixed w-full top-0 z-50 bg-black/95 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('shop.home') }}" class="text-2xl font-bold flex items-center gap-2">
                    <span class="text-3xl">🍔</span>
                    <span class="text-yellow-400 font-serif" style="font-family: 'Playfair Display', serif;">EUT Food</span>
                </a>
                <div class="ml-auto flex items-center gap-4">
                    <button id="shopThemeToggle" class="theme-toggle text-white">
                        <svg id="shopSunIcon" class="w-5 h-5 text-yellow-400 hidden" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg id="shopMoonIcon" class="w-5 h-5 text-gray-200" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                        </svg>
                    </button>
                    <a href="{{ route('shop.profile') }}" class="p-2 hover:bg-white/10 rounded transition text-yellow-400">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Checkout Content -->
    <div class="pt-24 pb-8 max-w-7xl mx-auto px-6">
        <h1 class="text-2xl font-bold text-yellow-400 mb-6" style="font-family: 'Playfair Display', serif;">Checkout</h1>

        <form id="checkoutForm" class="grid lg:grid-cols-3 gap-6">
            <!-- Shipping & Payment Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Address -->
                <div class="bg-gray-900 border border-white/10 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
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
                            <label class="block text-sm font-medium text-gray-300 mb-1">Full Name</label>
                            <input type="text" name="name" required class="w-full border border-white/20 bg-gray-900 rounded-lg px-4 py-2 focus:outline-none focus:border-yellow-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Phone Number</label>
                            <input type="tel" name="phone" required class="w-full border border-white/20 bg-gray-900 rounded-lg px-4 py-2 focus:outline-none focus:border-yellow-400">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Address</label>
                        <textarea name="address" rows="3" required class="w-full border border-white/20 bg-gray-900 rounded-lg px-4 py-2 focus:outline-none focus:border-yellow-400"></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">City</label>
                            <input type="text" name="city" required class="w-full border border-white/20 bg-gray-900 rounded-lg px-4 py-2 focus:outline-none focus:border-yellow-400">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Postal Code</label>
                            <input type="text" name="postal" required class="w-full border border-white/20 bg-gray-900 rounded-lg px-4 py-2 focus:outline-none focus:border-yellow-400">
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-gray-900 border border-white/10 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Payment Method
                    </h3>

                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 border border-white/20 rounded-xl cursor-pointer hover:border-yellow-400 transition">
                            <input type="radio" name="payment" value="cod" checked class="w-5 h-5 accent-yellow-500">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">💵</span>
                                <div>
                                    <div class="font-medium text-white">Cash on Delivery</div>
                                    <div class="text-sm text-gray-400">Pay when you receive your order</div>
                                </div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border border-white/20 rounded-xl cursor-pointer hover:border-yellow-400 transition">
                            <input type="radio" name="payment" value="gcash" class="w-5 h-5 accent-yellow-500">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">📱</span>
                                <div>
                                    <div class="font-medium text-white">GCash</div>
                                    <div class="text-sm text-gray-400">Pay via GCash e-wallet</div>
                                </div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 border border-white/20 rounded-xl cursor-pointer hover:border-yellow-400 transition">
                            <input type="radio" name="payment" value="card" class="w-5 h-5 accent-yellow-500">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">💳</span>
                                <div>
                                    <div class="font-medium text-white">Credit/Debit Card</div>
                                    <div class="text-sm text-gray-400">Pay with Visa or Mastercard</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-gray-900 border border-white/10 rounded-2xl p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-white mb-4">Order Summary</h3>

                    <div class="max-h-64 overflow-y-auto mb-4 space-y-3" id="checkoutItemsList"></div>

                    <div class="border-t border-white/10 pt-4 space-y-3 text-sm">
                        <div class="flex justify-between text-gray-400">
                            <span>Subtotal</span>
                            <span id="checkoutSubtotal">₱0</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>Delivery Fee</span>
                            <span>₱50</span>
                        </div>
                        <div class="border-t border-white/10 pt-3 flex justify-between text-xl font-bold">
                            <span>Total</span>
                            <span id="checkoutGrandTotal" class="text-yellow-400">₱0</span>
                        </div>
                    </div>

                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-400 text-black w-full py-3 rounded-lg font-semibold mt-6 transition">
                        Place Order
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bottom Navigation for Mobile -->
    <nav style="position:fixed; bottom:0; left:0; right:0; background:#111827; border-top:1px solid rgba(255,255,255,0.1); padding:10px 0; z-index:50;" class="lg:hidden">
        <div style="display:flex; flex-direction:row; width:100%;">
            <a href="{{ route('shop.home') }}" style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; color:#9ca3af; text-decoration:none;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span style="font-size:11px;">Menu</span>
            </a>
            <a href="{{ route('shop.tracking') }}" style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; color:#facc15; text-decoration:none;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span style="font-size:11px;">Orders</span>
            </a>
            <a href="{{ route('shop.cart') }}" style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; color:#9ca3af; text-decoration:none;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span style="font-size:11px;">Cart</span>
            </a>
            <a href="{{ route('shop.profile') }}" style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; color:#9ca3af; text-decoration:none;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span style="font-size:11px;">Profile</span>
            </a>
        </div>
    </nav>

    <div class="h-20 lg:hidden"></div>

    <script>
        // Theme management
        function applyTheme(theme) {
            const html = document.documentElement;
            if (theme === 'light') {
                html.classList.add('light-mode');
                document.getElementById('shopSunIcon').classList.add('hidden');
                document.getElementById('shopMoonIcon').classList.remove('hidden');
            } else {
                html.classList.remove('light-mode');
                document.getElementById('shopSunIcon').classList.remove('hidden');
                document.getElementById('shopMoonIcon').classList.add('hidden');
            }
        }

        function toggleTheme() {
            const currentTheme = localStorage.getItem('eutTheme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('eutTheme', newTheme);
            applyTheme(newTheme);
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('eutTheme') || 'dark';
            applyTheme(savedTheme);
            document.getElementById('shopThemeToggle').addEventListener('click', toggleTheme);
        });

        function renderCheckout() {
            const cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
            const itemsList = document.getElementById('checkoutItemsList');
            itemsList.innerHTML = '';

            let subtotal = 0;

            cart.forEach(item => {
                const itemEl = document.createElement('div');
                itemEl.className = 'flex items-center gap-3';
                itemEl.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-white">${item.name}</div>
                        <div class="text-xs text-gray-400">x${item.quantity}</div>
                    </div>
                    <div class="text-sm text-yellow-400 font-medium">₱${(item.price * item.quantity).toLocaleString()}</div>
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

            // Save completed order to localStorage history
            const cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
            const subtotal = cart.reduce((s, i) => s + i.price * i.quantity, 0);
            const orderId = 'EUT-' + String(Math.floor(Math.random() * 99999)).padStart(5, '0');
            const now = new Date();
            const dateStr = now.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' })
                + ' · ' + now.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit' });

            const completedOrder = {
                id: orderId,
                date: dateStr,
                total: subtotal + 50,
                items: cart.map(i => ({
                    name: i.name,
                    qty: i.quantity,
                    price: i.price,
                    img: i.image
                }))
            };

            const history = JSON.parse(localStorage.getItem('eutOrderHistory') || '[]');
            history.unshift(completedOrder); // newest first
            localStorage.setItem('eutOrderHistory', JSON.stringify(history));
            localStorage.setItem('eutLastOrder', JSON.stringify(completedOrder));

            // Clear cart
            localStorage.setItem('eutCart', JSON.stringify([]));

            window.location.href = '{{ route('shop.tracking') }}';
        });
    </script>
</body>
</html>
