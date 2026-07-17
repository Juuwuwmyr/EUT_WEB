<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .shopee-orange { background-color: #ee4d2d; }
        .shopee-orange-text { color: #ee4d2d; }
        .shopee-bg-light { background-color: #f5f5f5; }
        .timeline-dot { width: 16px; height: 16px; border-radius: 50%; }
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

    <!-- Tracking Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Order #EUT-{{ str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-gray-600 mb-8">Thank you for your order! Here's your delivery status:</p>

            <!-- Timeline -->
            <div class="space-y-8">
                <!-- Step 1: Order Placed -->
                <div class="flex gap-4 items-start">
                    <div class="flex flex-col items-center">
                        <div class="timeline-dot shopee-orange"></div>
                        <div class="w-0.5 h-20 bg-gray-300 mt-2"></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Order Placed</h3>
                        <p class="text-sm text-gray-500">{{ date('F j, Y, g:i A') }}</p>
                        <p class="text-sm text-gray-600 mt-1">Your order has been received and is being processed</p>
                    </div>
                </div>

                <!-- Step 2: Preparing -->
                <div class="flex gap-4 items-start">
                    <div class="flex flex-col items-center">
                        <div class="timeline-dot bg-yellow-400"></div>
                        <div class="w-0.5 h-20 bg-gray-300 mt-2"></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Preparing Your Order</h3>
                        <p class="text-sm text-gray-500">{{ date('F j, Y, g:i A', strtotime('+5 minutes')) }}</p>
                        <p class="text-sm text-gray-600 mt-1">Our chefs are preparing your delicious food</p>
                    </div>
                </div>

                <!-- Step 3: Out for Delivery -->
                <div class="flex gap-4 items-start opacity-50">
                    <div class="flex flex-col items-center">
                        <div class="timeline-dot bg-gray-300"></div>
                        <div class="w-0.5 h-20 bg-gray-300 mt-2"></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Out for Delivery</h3>
                        <p class="text-sm text-gray-500">Estimated: {{ date('F j, Y, g:i A', strtotime('+30 minutes')) }}</p>
                        <p class="text-sm text-gray-600 mt-1">Your order is on the way!</p>
                    </div>
                </div>

                <!-- Step 4: Delivered -->
                <div class="flex gap-4 items-start opacity-50">
                    <div class="flex flex-col items-center">
                        <div class="timeline-dot bg-gray-300"></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Delivered</h3>
                        <p class="text-sm text-gray-500">Estimated: {{ date('F j, Y, g:i A', strtotime('+45 minutes')) }}</p>
                        <p class="text-sm text-gray-600 mt-1">Enjoy your meal!</p>
                    </div>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="mt-12 border-t pt-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Delivery Information</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Delivery Address</h4>
                        <p class="text-gray-600 text-sm">123 Food Street, Culinary District, Metro Manila, 1234</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Estimated Arrival</h4>
                        <p class="text-gray-600 text-sm">{{ date('F j, Y, g:i A', strtotime('+30 minutes')) }} - {{ date('F j, Y, g:i A', strtotime('+45 minutes')) }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex gap-4">
                <a href="{{ route('shop.home') }}" class="flex-1 shopee-orange text-white py-3 rounded-lg font-semibold text-center hover:bg-orange-600 transition">
                    Order Again
                </a>
                <a href="{{ route('shop.home') }}" class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg font-semibold text-center hover:bg-gray-50 transition">
                    Back to Menu
                </a>
            </div>
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
</body>
</html>
