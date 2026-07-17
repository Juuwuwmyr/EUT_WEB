<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item['name'] }} - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .shopee-orange { background-color: #ee4d2d; }
        .shopee-orange-text { color: #ee4d2d; }
        .shopee-bg-light { background-color: #f5f5f5; }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ee4d2d;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
        }
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
                <div class="flex-1 max-w-2xl mx-8">
                    <div class="relative">
                        <input type="text" placeholder="Search for burgers, fries, drinks..."
                               class="w-full px-4 py-2 rounded-sm text-gray-800 focus:outline-none">
                        <button class="absolute right-1 top-1 bg-orange-500 hover:bg-orange-600 px-4 py-1.5 rounded-sm transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <a href="{{ route('shop.cart') }}" class="relative p-2 hover:bg-white/10 rounded transition">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="cart-badge" id="cartBadge">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Product Details -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Product Image -->
                <div>
                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-96 object-cover rounded-lg">
                </div>

                <!-- Product Info -->
                <div class="flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            @if(!empty($item['featured']))
                                <span class="bg-red-500 text-white text-xs px-3 py-1 rounded">Hot Product</span>
                            @endif
                            <span class="flex items-center gap-1 text-yellow-500 text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                4.9
                            </span>
                            <span class="text-gray-500 text-sm">{{ rand(100, 5000) }} Reviews</span>
                            <span class="text-gray-500 text-sm">{{ rand(100, 5000) }} Sold</span>
                        </div>

                        <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $item['name'] }}</h1>
                        <p class="text-gray-600 mb-6">{{ $item['description'] }}</p>

                        <div class="bg-orange-50 p-4 rounded-lg mb-6">
                            <span class="text-gray-500 text-sm">Price</span>
                            <div class="text-3xl font-bold shopee-orange-text mt-1">₱{{ number_format($item['price'], 0) }}</div>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-6">
                            <span class="text-gray-700 font-medium mb-3 block">Quantity</span>
                            <div class="flex items-center gap-3">
                                <button id="decreaseQty" class="w-10 h-10 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100 transition">-</button>
                                <input type="number" id="quantity" value="1" min="1" class="w-20 text-center border border-gray-300 rounded py-2">
                                <button id="increaseQty" class="w-10 h-10 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100 transition">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button id="addToCartBtn" class="flex-1 border-2 shopee-orange-border shopee-orange-text font-semibold py-3 rounded hover:bg-orange-50 transition">
                            Add to Cart
                        </button>
                        <a href="{{ route('shop.cart') }}" class="flex-1 shopee-orange text-white font-semibold py-3 rounded text-center hover:bg-orange-600 transition">
                            Buy Now
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Product Description</h3>
            <p class="text-gray-600">{{ $item['description'] }}</p>
            @if(!empty($item['ingredients']))
                <div class="mt-4">
                    <h4 class="font-semibold text-gray-700 mb-2">Ingredients:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($item['ingredients'] as $ingredient)
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">{{ $ingredient }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
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
        // Cart management
        let cart = JSON.parse(localStorage.getItem('eutCart') || '[]');

        function updateCartBadge() {
            const badge = document.getElementById('cartBadge');
            badge.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
        }
        updateCartBadge();

        // Quantity controls
        const quantityInput = document.getElementById('quantity');
        document.getElementById('decreaseQty').addEventListener('click', () => {
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
        document.getElementById('increaseQty').addEventListener('click', () => {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        });

        // Add to cart
        document.getElementById('addToCartBtn').addEventListener('click', () => {
            const id = {{ $item['id'] }};
            const name = @json($item['name']);
            const price = {{ $item['price'] }};
            const image = @json($item['image']);
            const quantity = parseInt(quantityInput.value);

            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({ id, name, price, image, quantity });
            }
            localStorage.setItem('eutCart', JSON.stringify(cart));
            updateCartBadge();

            // Feedback
            const btn = document.getElementById('addToCartBtn');
            const originalText = btn.textContent;
            btn.textContent = 'Added to Cart!';
            btn.classList.add('bg-green-500', 'text-white', 'border-green-500');
            btn.classList.remove('shopee-orange-text', 'shopee-orange-border');
            setTimeout(() => {
                btn.textContent = originalText;
                btn.classList.remove('bg-green-500', 'text-white', 'border-green-500');
                btn.classList.add('shopee-orange-text', 'shopee-orange-border');
            }, 1500);
        });
    </script>
</body>
</html>
