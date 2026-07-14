<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EUT Restaurant - Eat Unwind Tea Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 bg-black/95 backdrop-blur-md border-b border-red-600/50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-yellow-400 font-serif" style="font-family: 'Playfair Display', serif;">EUT</h1>
                </div>
                
                <!-- Menu Items -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-yellow-400 hover:text-red-400 transition duration-300 font-medium">HOME</a>
                    <a href="#menu" class="text-white hover:text-yellow-400 transition duration-300 font-medium">MENU</a>
                    <a href="#about" class="text-white hover:text-yellow-400 transition duration-300 font-medium">ABOUT</a>
                    <a href="#contact" class="text-white hover:text-yellow-400 transition duration-300 font-medium">CONTACT</a>
                </div>

                <!-- Right Side Icons & Button -->
                <div class="flex items-center space-x-4">
                    <button class="text-white hover:text-yellow-400 transition duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                    </button>
                    <button class="text-white hover:text-yellow-400 transition duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                        </svg>
                    </button>
                    <button class="text-white hover:text-yellow-400 transition duration-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full font-semibold transition duration-300">
                        Reserve Table
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section id="home" class="min-h-screen flex items-center relative overflow-hidden pt-20">
        <!-- Background gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-black via-red-900/20 to-black"></div>
        
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center relative z-10">
            <!-- Left Content -->
            <div class="space-y-8">
                <div>
                    <h1 class="text-5xl lg:text-7xl font-bold mb-4" style="font-family: 'Playfair Display', serif;">
                        <span class="text-yellow-400">EUT</span> <span class="text-red-500">Restaurant</span>
                    </h1>
                    <h2 class="text-2xl lg:text-3xl text-blue-400 mb-6 font-light">
                        Eat • Unwind • Tea
                    </h2>
                </div>
                
                <p class="text-lg text-gray-300 leading-relaxed max-w-md">
                    Experience culinary excellence where traditional flavors meet modern innovation. 
                    Savor exquisite dishes, unwind in our cozy atmosphere, and discover our premium tea collection.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-full font-semibold text-lg transition duration-300 transform hover:scale-105">
                        Order Now
                    </button>
                    <button class="border-2 border-yellow-400 text-yellow-400 hover:bg-yellow-400 hover:text-black px-8 py-4 rounded-full font-semibold text-lg transition duration-300">
                        View Menu
                    </button>
                </div>
                
                <!-- Dots indicator -->
                <div class="flex space-x-2 pt-8">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                </div>
            </div>
            
            <!-- Right Content - Separate Burger and Fries -->
            <div class="relative">
                <!-- Burger Image -->
                <div class="relative z-10 mb-6">
                    <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl p-4 shadow-2xl transform rotate-2 hover:rotate-0 transition duration-500">
                        <div class="bg-white rounded-xl p-3 shadow-lg">
                            <img src="{{ asset('images/single-burger.png') }}" 
                                 alt="Delicious Burger" 
                                 class="w-full h-48 object-cover rounded-lg shadow-md">
                        </div>
                    </div>
                </div>
                
                <!-- Fries Image -->
                <div class="relative z-10">
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl p-4 shadow-2xl transform -rotate-2 hover:rotate-0 transition duration-500">
                        <div class="bg-white rounded-xl p-3 shadow-lg">
                            <img src="{{ asset('images/single-fries.png') }}" 
                                 alt="Crispy French Fries" 
                                 class="w-full h-48 object-cover rounded-lg shadow-md">
                        </div>
                    </div>
                </div>
                
                <!-- Floating elements -->
                <div class="absolute top-10 -right-5 w-20 h-20 bg-red-500 rounded-full opacity-20 animate-pulse"></div>
                <div class="absolute bottom-10 -left-5 w-16 h-16 bg-yellow-400 rounded-full opacity-30 animate-bounce"></div>
                <div class="absolute top-1/2 right-10 w-12 h-12 bg-blue-500 rounded-full opacity-25 animate-ping"></div>
            </div>
        </div>
        
        <!-- Scroll indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2">
            <div class="animate-bounce">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </div>
    </section>
    <!-- Features Section -->
    <section class="py-20 bg-gray-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4 text-yellow-400" style="font-family: 'Playfair Display', serif;">Why Choose EUT?</h2>
                <p class="text-gray-300 text-lg">Discover what makes our restaurant special</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center group">
                    <div class="bg-red-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-yellow-400">Fresh Ingredients</h3>
                    <p class="text-gray-300">We use only the freshest, locally-sourced ingredients to create our delicious meals.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="text-center group">
                    <div class="bg-yellow-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-red-400">Expert Chefs</h3>
                    <p class="text-gray-300">Our experienced chefs craft each dish with passion and culinary expertise.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="text-center group">
                    <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-blue-400">Cozy Atmosphere</h3>
                    <p class="text-gray-300">Relax and unwind in our warm, inviting atmosphere perfect for any occasion.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Preview Section -->
    <section id="menu" class="py-20 bg-black">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4 text-yellow-400" style="font-family: 'Playfair Display', serif;">Featured Menu</h2>
                <p class="text-gray-300 text-lg">Taste our signature dishes</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- EUT Classic Burger -->
                <div class="bg-gray-900 border border-red-600/30 rounded-xl overflow-hidden hover:transform hover:scale-105 transition duration-300 shadow-lg hover:border-red-500">
                    <div class="h-48 overflow-hidden">
                        <img src="{{ asset('images/hero-burger.jpg') }}" 
                             alt="EUT Classic Burger" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2 text-yellow-400">EUT Classic Burger</h3>
                        <p class="text-gray-300 mb-4">Juicy beef patty, lettuce, tomato, pickles, special sauce on brioche bun</p>
                        <div class="flex justify-between items-center">
                            <span class="text-red-400 text-xl font-bold">₱350</span>
                            <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                Order Now
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Gourmet Cheeseburger -->
                <div class="bg-gray-900 border border-yellow-400/30 rounded-xl overflow-hidden hover:transform hover:scale-105 transition duration-300 shadow-lg hover:border-yellow-400">
                    <div class="h-48 overflow-hidden">
                        <img src="{{ asset('images/gourmet-burger.jpg') }}" 
                             alt="Gourmet Cheeseburger" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2 text-red-400">Gourmet Cheeseburger</h3>
                        <p class="text-gray-300 mb-4">Premium beef with aged cheddar, caramelized onions, bacon</p>
                        <div class="flex justify-between items-center">
                            <span class="text-yellow-400 text-xl font-bold">₱420</span>
                            <button class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                Order Now
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Classic French Fries -->
                <div class="bg-gray-900 border border-blue-600/30 rounded-xl overflow-hidden hover:transform hover:scale-105 transition duration-300 shadow-lg hover:border-blue-500">
                    <div class="h-48 overflow-hidden">
                        <img src="{{ asset('images/french-fries.jpg') }}" 
                             alt="Classic French Fries" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2 text-blue-400">Classic French Fries</h3>
                        <p class="text-gray-300 mb-4">Golden crispy fries with sea salt - perfect side for any meal</p>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-400 text-xl font-bold">₱120</span>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                Order Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <button class="border-2 border-yellow-400 text-yellow-400 hover:bg-yellow-400 hover:text-black px-8 py-3 rounded-full font-semibold transition duration-300">
                        View Full Menu
                    </button>
                    <a href="/menu-pdf" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-full font-semibold transition duration-300 inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Download Menu PDF
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Comprehensive Menu Section -->
    <section class="py-20 bg-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4" style="font-family: 'Playfair Display', serif;">Complete Menu</h2>
                <p class="text-slate-300 text-lg">Explore our full collection of delicious offerings</p>
            </div>

            <!-- Menu Categories -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button class="menu-category-btn active bg-red-600 text-white px-6 py-3 rounded-full font-semibold transition duration-300" data-category="all">
                    All Items
                </button>
                <button class="menu-category-btn text-yellow-400 border border-yellow-400 px-6 py-3 rounded-full font-semibold transition duration-300 hover:bg-yellow-400 hover:text-black" data-category="burgers">
                    Burgers
                </button>
                <button class="menu-category-btn text-red-400 border border-red-400 px-6 py-3 rounded-full font-semibold transition duration-300 hover:bg-red-400 hover:text-white" data-category="sides">
                    Sides
                </button>
                <button class="menu-category-btn text-blue-400 border border-blue-400 px-6 py-3 rounded-full font-semibold transition duration-300 hover:bg-blue-400 hover:text-white" data-category="beverages">
                    Beverages
                </button>
                <button class="menu-category-btn text-yellow-400 border border-yellow-400 px-6 py-3 rounded-full font-semibold transition duration-300 hover:bg-yellow-400 hover:text-black" data-category="combos">
                    Combos
                </button>
            </div>

            <!-- Burgers Section -->
            <div class="menu-section" id="burgers-section">
                <h3 class="text-3xl font-bold mb-8 text-yellow-400" style="font-family: 'Playfair Display', serif;">Premium Burgers</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <!-- EUT Classic Burger -->
                    <div class="menu-item bg-black border border-red-600/30 rounded-lg p-6 hover:border-red-500 transition duration-300" data-category="burgers">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/hero-burger.jpg') }}" alt="EUT Classic Burger" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2 text-yellow-400">EUT Classic Burger</h4>
                                <p class="text-gray-300 text-sm mb-3">Juicy beef patty, lettuce, tomato, pickles, special sauce on brioche bun</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-red-400 text-lg font-bold">₱350</span>
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gourmet Cheeseburger -->
                    <div class="menu-item bg-black border border-yellow-400/30 rounded-lg p-6 hover:border-yellow-400 transition duration-300" data-category="burgers">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/gourmet-burger.jpg') }}" alt="Gourmet Cheeseburger" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2 text-red-400">Gourmet Cheeseburger</h4>
                                <p class="text-gray-300 text-sm mb-3">Premium beef with aged cheddar, caramelized onions, bacon</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-yellow-400 text-lg font-bold">₱420</span>
                                    <button class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Spicy Jalapeño Burger -->
                    <div class="menu-item bg-black border border-red-600/30 rounded-lg p-6 hover:border-red-500 transition duration-300" data-category="burgers">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/delicious-burger-fries.jpg') }}" alt="Spicy Jalapeño Burger" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2 text-yellow-400">Spicy Jalapeño Burger</h4>
                                <p class="text-gray-300 text-sm mb-3">Flame-grilled patty with jalapeños, pepper jack cheese, chipotle mayo</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-red-400 text-lg font-bold">₱380</span>
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mushroom Swiss Burger -->
                    <div class="menu-item bg-black border border-yellow-400/30 rounded-lg p-6 hover:border-yellow-400 transition duration-300" data-category="burgers">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/combo-meal.jpg') }}" alt="Mushroom Swiss Burger" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2 text-red-400">Mushroom Swiss Burger</h4>
                                <p class="text-gray-300 text-sm mb-3">Sautéed mushrooms, Swiss cheese, garlic aioli on artisan bread</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-yellow-400 text-lg font-bold">₱395</span>
                                    <button class="bg-yellow-500 hover:bg-yellow-600 text-black px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BBQ Bacon Burger -->
                    <div class="menu-item bg-black border border-red-600/30 rounded-lg p-6 hover:border-red-500 transition duration-300" data-category="burgers">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/hero-burger.jpg') }}" alt="BBQ Bacon Burger" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2 text-yellow-400">BBQ Bacon Burger</h4>
                                <p class="text-gray-300 text-sm mb-3">Smoky BBQ sauce, crispy bacon, onion rings, cheddar cheese</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-red-400 text-lg font-bold">₱410</span>
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Veggie Delight Burger -->
                    <div class="menu-item bg-black border border-blue-600/30 rounded-lg p-6 hover:border-blue-500 transition duration-300" data-category="burgers">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/gourmet-burger.jpg') }}" alt="Veggie Delight Burger" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2 text-blue-400">Veggie Delight Burger</h4>
                                <p class="text-gray-300 text-sm mb-3">House-made veggie patty with avocado, sprouts, herbed mayo</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-blue-400 text-lg font-bold">₱320</span>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sides Section -->
            <div class="menu-section" id="sides-section">
                <h3 class="text-3xl font-bold mb-8 text-amber-400" style="font-family: 'Playfair Display', serif;">Delicious Sides</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <!-- Classic French Fries -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="sides">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/french-fries.jpg') }}" alt="Classic French Fries" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Classic French Fries</h4>
                                <p class="text-slate-300 text-sm mb-3">Golden crispy fries with sea salt</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱120</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sweet Potato Fries -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="sides">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/french-fries.jpg') }}" alt="Sweet Potato Fries" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Sweet Potato Fries</h4>
                                <p class="text-slate-300 text-sm mb-3">Crispy sweet potato fries with honey mustard dip</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱150</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loaded Cheese Fries -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="sides">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/french-fries.jpg') }}" alt="Loaded Cheese Fries" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Loaded Cheese Fries</h4>
                                <p class="text-slate-300 text-sm mb-3">Fries topped with melted cheese, bacon bits, green onions</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱180</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onion Rings -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="sides">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/combo-meal.jpg') }}" alt="Onion Rings" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Onion Rings</h4>
                                <p class="text-slate-300 text-sm mb-3">Beer-battered onion rings with ranch dipping sauce</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱140</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mozzarella Sticks -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="sides">
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('images/combo-meal.jpg') }}" alt="Mozzarella Sticks" class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Mozzarella Sticks</h4>
                                <p class="text-slate-300 text-sm mb-3">Golden fried mozzarella with marinara sauce</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱160</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Beverages Section -->
            <div class="menu-section" id="beverages-section">
                <h3 class="text-3xl font-bold mb-8 text-amber-400" style="font-family: 'Playfair Display', serif;">Beverages</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                    <!-- Premium Tea Collection -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="beverages">
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-teal-500 rounded-lg flex items-center justify-center">
                                <span class="text-3xl">🍵</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Premium Tea Collection</h4>
                                <p class="text-slate-300 text-sm mb-3">Earl Grey, Jasmine, Oolong, or Chamomile</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱180</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Freshly Brewed Coffee -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="beverages">
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 bg-gradient-to-br from-amber-600 to-orange-700 rounded-lg flex items-center justify-center">
                                <span class="text-3xl">☕</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Freshly Brewed Coffee</h4>
                                <p class="text-slate-300 text-sm mb-3">Single origin coffee, expertly brewed</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱120</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fresh Fruit Smoothies -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="beverages">
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 bg-gradient-to-br from-pink-400 to-purple-500 rounded-lg flex items-center justify-center">
                                <span class="text-3xl">🥤</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Fresh Fruit Smoothies</h4>
                                <p class="text-slate-300 text-sm mb-3">Mango, Strawberry, or Mixed Berry</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱200</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Craft Sodas -->
                    <div class="menu-item bg-slate-900 rounded-lg p-6 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="beverages">
                        <div class="flex items-start gap-4">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-lg flex items-center justify-center">
                                <span class="text-3xl">🥤</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold mb-2">Craft Sodas</h4>
                                <p class="text-slate-300 text-sm mb-3">House-made sodas in various flavors</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-lg font-bold">₱100</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-semibold transition duration-300">Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combo Meals Section -->
            <div class="menu-section" id="combos-section">
                <h3 class="text-3xl font-bold mb-8 text-amber-400" style="font-family: 'Playfair Display', serif;">Value Combo Meals</h3>
                <div class="grid md:grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- EUT Classic Combo -->
                    <div class="menu-item bg-slate-900 rounded-lg p-8 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="combos">
                        <div class="flex items-start gap-6">
                            <img src="{{ asset('images/delicious-burger-fries.jpg') }}" alt="EUT Classic Combo" class="w-32 h-32 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-2xl font-semibold mb-3">EUT Classic Combo</h4>
                                <p class="text-slate-300 mb-4">EUT Classic Burger + French Fries + Drink</p>
                                <div class="text-sm text-slate-400 mb-4">
                                    <p>Includes: EUT Classic Burger, Regular Fries, Soft Drink</p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-2xl font-bold">₱550</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-6 py-3 rounded-full font-semibold transition duration-300">Order Combo</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gourmet Combo -->
                    <div class="menu-item bg-slate-900 rounded-lg p-8 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="combos">
                        <div class="flex items-start gap-6">
                            <img src="{{ asset('images/combo-meal.jpg') }}" alt="Gourmet Combo" class="w-32 h-32 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-2xl font-semibold mb-3">Gourmet Combo</h4>
                                <p class="text-slate-300 mb-4">Gourmet Cheeseburger + Sweet Potato Fries + Premium Tea</p>
                                <div class="text-sm text-slate-400 mb-4">
                                    <p>Includes: Gourmet Cheeseburger, Sweet Potato Fries, Premium Tea</p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-2xl font-bold">₱650</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-6 py-3 rounded-full font-semibold transition duration-300">Order Combo</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Spicy Combo -->
                    <div class="menu-item bg-slate-900 rounded-lg p-8 border border-slate-700 hover:border-amber-500 transition duration-300" data-category="combos">
                        <div class="flex items-start gap-6">
                            <img src="{{ asset('images/delicious-burger-fries.jpg') }}" alt="Spicy Combo" class="w-32 h-32 object-cover rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-2xl font-semibold mb-3">Spicy Combo</h4>
                                <p class="text-slate-300 mb-4">Spicy Jalapeño Burger + Loaded Cheese Fries + Smoothie</p>
                                <div class="text-sm text-slate-400 mb-4">
                                    <p>Includes: Spicy Jalapeño Burger, Loaded Cheese Fries, Fresh Smoothie</p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-amber-400 text-2xl font-bold">₱620</span>
                                    <button class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-6 py-3 rounded-full font-semibold transition duration-300">Order Combo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div>
                    <h2 class="text-4xl font-bold mb-6 text-yellow-400" style="font-family: 'Playfair Display', serif;">About EUT Restaurant</h2>
                    <p class="text-gray-300 text-lg mb-6 leading-relaxed">
                        Founded with a passion for culinary excellence, EUT Restaurant brings together the finest ingredients, 
                        expert culinary techniques, and a warm, welcoming atmosphere where guests can truly eat, unwind, and enjoy tea.
                    </p>
                    <p class="text-gray-300 text-lg mb-8 leading-relaxed">
                        Our menu celebrates both traditional flavors and innovative cuisine, crafted by our team of experienced chefs 
                        who are dedicated to creating memorable dining experiences for every guest.
                    </p>
                    
                    <div class="grid grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-400 mb-2">10+</div>
                            <div class="text-gray-300">Years Experience</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-400 mb-2">50+</div>
                            <div class="text-gray-300">Menu Items</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-400 mb-2">1000+</div>
                            <div class="text-gray-300">Happy Customers</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Image -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-red-600 to-yellow-500 rounded-2xl p-3 shadow-xl">
                        <img src="{{ asset('images/restaurant-interior.jpg') }}" 
                             alt="EUT Restaurant Interior" 
                             class="w-full h-80 object-cover rounded-xl shadow-lg">
                    </div>
                    <!-- Overlay text -->
                    <div class="absolute bottom-6 left-6 right-6 bg-black/80 rounded-lg p-4">
                        <h3 class="text-xl font-semibold text-yellow-400 mb-2">Visit Our Restaurant</h3>
                        <p class="text-gray-300 text-sm">Experience the perfect dining atmosphere</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-black">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4 text-yellow-400" style="font-family: 'Playfair Display', serif;">Visit Us Today</h2>
                <p class="text-gray-300 text-lg">Experience the EUT difference</p>
            </div>
            
            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Contact Info -->
                <div class="space-y-8">
                    <div class="flex items-center space-x-4">
                        <div class="bg-red-600 w-12 h-12 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-yellow-400">Location</h3>
                            <p class="text-gray-300">123 Food Street, Culinary District, City</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="bg-yellow-500 w-12 h-12 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-red-400">Phone</h3>
                            <p class="text-gray-300">+63 912 345 6789</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-600 w-12 h-12 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-blue-400">Hours</h3>
                            <p class="text-gray-300">Mon-Sun: 10:00 AM - 10:00 PM</p>
                        </div>
                    </div>
                </div>
                
                <!-- Reservation Form -->
                <div class="bg-gray-900 border border-red-600/30 rounded-2xl p-8">
                    <h3 class="text-2xl font-semibold mb-6 text-yellow-400">Make a Reservation</h3>
                    <form class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" placeholder="First Name" class="bg-black border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-red-500 transition duration-300">
                            <input type="text" placeholder="Last Name" class="bg-black border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-red-500 transition duration-300">
                        </div>
                        <input type="email" placeholder="Email Address" class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-yellow-400 transition duration-300">
                        <input type="tel" placeholder="Phone Number" class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition duration-300">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="date" class="bg-black border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-red-500 transition duration-300">
                            <input type="time" class="bg-black border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-400 transition duration-300">
                        </div>
                        <select class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition duration-300">
                            <option>Number of Guests</option>
                            <option>1-2 Guests</option>
                            <option>3-4 Guests</option>
                            <option>5-8 Guests</option>
                            <option>8+ Guests</option>
                        </select>
                        <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-yellow-500 hover:from-red-700 hover:to-yellow-600 text-white py-3 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                            Reserve Table
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="bg-gray-950 py-12 border-t border-red-600/30">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-2">
                    <h3 class="text-2xl font-bold text-yellow-400 mb-4" style="font-family: 'Playfair Display', serif;">EUT Restaurant</h3>
                    <p class="text-gray-300 mb-6 max-w-md">
                        Where culinary passion meets exceptional dining. Experience the perfect blend of taste, 
                        ambiance, and service that makes every meal memorable.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-red-600 hover:bg-yellow-500 text-white hover:text-black w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-yellow-500 hover:bg-red-600 text-black hover:text-white w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-blue-600 hover:bg-yellow-500 text-white hover:text-black w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.404-5.940 1.404-5.940s-.358-.72-.358-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.098.119.112.223.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.751-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z.017 0z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-red-600 hover:bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.007 0C5.373 0 0 5.373 0 12.007s5.373 12.007 12.007 12.007 12.007-5.373 12.007-12.007S18.641.001 12.007.001zM8.84 18.32v-6.367H6.005V9.291h2.835V7.431c0-2.82 1.724-4.358 4.246-4.358 1.204 0 2.238.09 2.542.129v2.94l-1.745.001c-1.368 0-1.634.651-1.634 1.604v2.099h3.269l-.427 2.662h-2.842V18.32H8.84z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-red-400">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-300 hover:text-yellow-400 transition duration-300">Home</a></li>
                        <li><a href="#menu" class="text-gray-300 hover:text-yellow-400 transition duration-300">Menu</a></li>
                        <li><a href="#about" class="text-gray-300 hover:text-yellow-400 transition duration-300">About</a></li>
                        <li><a href="#contact" class="text-gray-300 hover:text-yellow-400 transition duration-300">Contact</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition duration-300">Reservations</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-blue-400">Contact Info</h4>
                    <div class="space-y-2 text-gray-300">
                        <p>123 Food Street</p>
                        <p>Culinary District, City</p>
                        <p class="text-yellow-400">+63 912 345 6789</p>
                        <p class="text-red-400">info@eutrestaurant.com</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-red-600/30 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2026 EUT Restaurant. All rights reserved. Built with ❤️ and <span class="text-yellow-400">Tailwind CSS</span>.</p>
            </div>
        </div>
    </footer>

    <!-- Smooth scrolling script -->
    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Menu filtering functionality
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButtons = document.querySelectorAll('.menu-category-btn');
            const menuItems = document.querySelectorAll('.menu-item');
            const menuSections = document.querySelectorAll('.menu-section');

            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Update active button
                    categoryButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-red-600', 'text-white', 'bg-yellow-400', 'text-black', 'bg-red-400', 'bg-blue-400');
                        const category = btn.getAttribute('data-category');
                        if (category === 'all' || category === 'sides') {
                            btn.classList.add('text-red-400', 'border', 'border-red-400');
                        } else if (category === 'burgers' || category === 'combos') {
                            btn.classList.add('text-yellow-400', 'border', 'border-yellow-400');
                        } else if (category === 'beverages') {
                            btn.classList.add('text-blue-400', 'border', 'border-blue-400');
                        }
                    });
                    
                    // Set active button style
                    if (category === 'all' || category === 'sides') {
                        this.classList.add('active', 'bg-red-600', 'text-white');
                        this.classList.remove('text-red-400', 'border', 'border-red-400');
                    } else if (category === 'burgers' || category === 'combos') {
                        this.classList.add('active', 'bg-yellow-400', 'text-black');
                        this.classList.remove('text-yellow-400', 'border', 'border-yellow-400');
                    } else if (category === 'beverages') {
                        this.classList.add('active', 'bg-blue-400', 'text-white');
                        this.classList.remove('text-blue-400', 'border', 'border-blue-400');
                    }
                    
                    // Show/hide items based on category
                    if (category === 'all') {
                        // Show all sections
                        menuSections.forEach(section => {
                            section.style.display = 'block';
                        });
                        menuItems.forEach(item => {
                            item.style.display = 'block';
                        });
                    } else {
                        // Hide all sections first
                        menuSections.forEach(section => {
                            section.style.display = 'none';
                        });
                        
                        // Show only the selected category section
                        const targetSection = document.getElementById(category + '-section');
                        if (targetSection) {
                            targetSection.style.display = 'block';
                        }
                        
                        // Filter items
                        menuItems.forEach(item => {
                            const itemCategory = item.getAttribute('data-category');
                            if (itemCategory === category) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    }
                });
            });
        });

        // Add to cart functionality (you can extend this)
        document.addEventListener('click', function(e) {
            if (e.target.textContent === 'Add to Cart' || e.target.textContent === 'Order Now' || e.target.textContent === 'Order Combo') {
                e.preventDefault();
                
                // Simple notification (you can replace with a proper cart system)
                const button = e.target;
                const originalText = button.textContent;
                
                button.textContent = 'Added!';
                button.style.backgroundColor = '#22c55e';
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.backgroundColor = '';
                }, 1000);
            }
        });
    </script>

</body>
</html>