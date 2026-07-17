<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EUT Restaurant - Order Food Online</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .shopee-orange { background-color: #ee4d2d; }
        .shopee-orange-text { color: #ee4d2d; }
        .shopee-orange-border { border-color: #ee4d2d; }
        .shopee-bg-light { background-color: #f5f5f5; }
        .category-pill:hover { background-color: #ee4d2d; color: white; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
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
    <!-- Top Banner -->
    <div class="bg-gradient-to-r from-orange-600 to-red-500 text-white py-2 text-center text-sm">
        🎉 Free Delivery on Orders Over ₱500! Use Code: EUTFREE
    </div>

    <!-- Navbar -->
    <nav class="shopee-orange text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex items-center gap-4 mb-3">
                <a href="{{ route('shop.home') }}" class="text-2xl font-bold flex items-center gap-2">
                    <span class="text-3xl">🍔</span>
                    EUT Food
                </a>
                <div class="flex-1 max-w-2xl">
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

    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-red-500 to-orange-500 py-8">
        <div class="max-w-7xl mx-auto px-4 flex items-center gap-8">
            <div class="text-white">
                <h1 class="text-4xl font-bold mb-2">EUT Restaurant</h1>
                <p class="text-xl mb-4">Eat • Unwind • Tea - Delicious Food Delivered Fast!</p>
                <div class="flex gap-4 text-sm">
                    <span class="bg-white/20 px-3 py-1 rounded-full">🚀 30-45 min delivery</span>
                    <span class="bg-white/20 px-3 py-1 rounded-full">⭐ 4.9 Rating</span>
                    <span class="bg-white/20 px-3 py-1 rounded-full">📍 Open Now</span>
                </div>
            </div>
            <img src="{{ asset('images/hero-burger.jpg') }}" alt="Burger" class="w-64 h-64 object-cover rounded-2xl shadow-2xl">
        </div>
    </div>

    <!-- Categories -->
    <div class="bg-white py-4 border-b sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 flex items-center gap-3 overflow-x-auto">
            <button class="category-pill shopee-orange text-white px-6 py-2 rounded-full text-sm font-medium whitespace-nowrap transition" data-category="all">
                All Items
            </button>
            @foreach(['burgers', 'sides', 'beverages', 'combos'] as $cat)
                <button class="category-pill bg-gray-100 text-gray-700 px-6 py-2 rounded-full text-sm font-medium whitespace-nowrap transition" data-category="{{ $cat }}">
                    {{ ucfirst($cat) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Products Grid -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Our Menu</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4" id="productsGrid">
            @foreach(\App\Models\MenuItem::getAllMenuItems() as $item)
                <div class="product-card bg-white rounded shadow-sm overflow-hidden cursor-pointer transition duration-300" data-category="{{ $item['category'] }}">
                    <a href="{{ route('shop.product', $item['id']) }}">
                        <div class="relative">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-48 object-cover">
                            @if(!empty($item['featured']))
                                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">Hot</span>
                            @endif
                            <span class="absolute top-2 right-2 bg-yellow-400 text-xs px-2 py-1 rounded flex items-center gap-1">
                                <svg class="w-3 h-3 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                4.9
                            </span>
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm text-gray-800 line-clamp-2 mb-2">{{ $item['name'] }}</h3>
                            <p class="text-xs text-gray-500 mb-2 line-clamp-2">{{ $item['description'] }}</p>
                            <div class="flex items-baseline gap-2 mb-2">
                                <span class="text-lg font-bold shopee-orange-text">₱{{ number_format($item['price'], 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>{{ rand(100, 5000) }} sold</span>
                            </div>
                        </div>
                    </a>
                    <div class="px-3 pb-3">
                        <button class="w-full shopee-orange text-white py-2 rounded text-sm font-medium hover:bg-orange-600 transition add-to-cart-btn" data-id="{{ $item['id'] }}" data-name="{{ $item['name'] }}" data-price="{{ $item['price'] }}" data-image="{{ $item['image'] }}">
                            Add to Cart
                        </button>
                    </div>
                </div>
            @endforeach
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

        // Add to cart
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const id = parseInt(btn.dataset.id);
                const name = btn.dataset.name;
                const price = parseInt(btn.dataset.price);
                const image = btn.dataset.image;

                const existingItem = cart.find(item => item.id === id);
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push({ id, name, price, image, quantity: 1 });
                }
                localStorage.setItem('eutCart', JSON.stringify(cart));
                updateCartBadge();

                // Feedback
                const originalText = btn.textContent;
                btn.textContent = 'Added!';
                btn.classList.remove('shopee-orange');
                btn.classList.add('bg-green-500');
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.classList.add('shopee-orange');
                    btn.classList.remove('bg-green-500');
                }, 1000);
            });
        });

        // Category filter
        document.querySelectorAll('.category-pill').forEach(btn => {
            btn.addEventListener('click', () => {
                // Update active state
                document.querySelectorAll('.category-pill').forEach(b => {
                    b.classList.remove('shopee-orange', 'text-white');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                btn.classList.add('shopee-orange', 'text-white');
                btn.classList.remove('bg-gray-100', 'text-gray-700');

                // Filter products
                const category = btn.dataset.category;
                document.querySelectorAll('.product-card').forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
