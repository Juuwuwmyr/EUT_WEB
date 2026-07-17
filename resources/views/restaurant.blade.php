<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EUT Restaurant - Eat Unwind Tea Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&family=Pacifico&family=Satisfy&family=Sacramento&display=swap" rel="stylesheet">
</head>
<body class="bg-black text-white font-sans">
    
    <!-- Navigation -->
    <nav class="fixed w-full top-0 z-50 bg-black/95 backdrop-blur-md border-b border-white/10">
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
                </div>

                <!-- Right Side Icons & Button -->
                <div class="flex items-center space-x-4">
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

                    @auth
                        <!-- Logged in: show avatar + name + logout -->
                        <div class="flex items-center gap-3">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" 
                                     alt="{{ auth()->user()->name }}"
                                     class="w-8 h-8 rounded-full border-2 border-yellow-400 object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center text-black font-bold text-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-gray-300 text-sm font-medium hidden md:block">{{ auth()->user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-full font-semibold text-sm transition duration-300">
                                Logout
                            </button>
                        </form>
                    @else
                        <!-- Guest: show Login + Reserve -->
                        <button onclick="openModal('login')" class="bg-yellow-500 hover:bg-yellow-600 text-black px-6 py-2 rounded-full font-semibold transition duration-300 transform hover:scale-105">
                            Login
                        </button>
                        <button onclick="openModal('signup')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-full font-semibold transition duration-300">
                            Sign Up
                        </button>
                    @endauth
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
        <!-- Background photo -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-bg.jpg') }}" 
                 alt="" 
                 class="w-full h-full object-cover object-center"
                 style="opacity: 0.25;">
        </div>
        <!-- Dark overlay gradient on top of photo -->
        <div class="absolute inset-0 bg-gradient-to-br from-black/90 via-red-950/60 to-black/90"></div>
        <!-- Extra bottom fade to black -->
        <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-black to-transparent"></div>
        <!-- Circular vignette effect — subtle/smooth -->
        <div class="absolute inset-0" style="background: radial-gradient(ellipse at center, transparent 40%, rgba(0,0,0,0.25) 65%, rgba(0,0,0,0.55) 85%, rgba(0,0,0,0.75) 100%);"></div>
        
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center relative z-10">
            <!-- Left Content -->
            <div class="space-y-8">
                <div>
                    <h1 class="mb-4">
                        <span class="text-7xl lg:text-9xl font-bold text-yellow-400 block" style="font-family: 'Sacramento', cursive; letter-spacing: 0.02em; text-shadow: 3px 3px 6px rgba(0,0,0,0.6);">EUT</span>
                        <span class="text-3xl lg:text-4xl font-light text-red-500 block -mt-4" style="font-family: 'Inter', sans-serif; letter-spacing: 0.15em; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Restaurant</span>
                    </h1>
                    <h2 class="text-2xl lg:text-3xl text-gray-300 mb-6 font-light" style="font-family: 'Inter', sans-serif;">
                        Eat • Unwind • Tea
                    </h2>
                </div>
                
                <p class="text-lg text-gray-300 leading-relaxed max-w-md">
                    Experience culinary excellence where traditional flavors meet modern innovation. 
                    Savor exquisite dishes, unwind in our cozy atmosphere, and discover our premium tea collection.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('shop.home') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-full font-semibold text-lg transition duration-300 transform hover:scale-105 text-center">
                        Order Now
                    </a>
                    <a href="{{ route('shop.home') }}" class="border-2 border-yellow-400 text-yellow-400 hover:bg-yellow-400 hover:text-black px-8 py-4 rounded-full font-semibold text-lg transition duration-300 text-center">
                        View Menu
                    </a>
                </div>
                
                <!-- Dots indicator -->
                <div class="flex space-x-2 pt-8">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                    <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                </div>
            </div>
            
            <!-- Right Content - Premium Food Photography Style -->
            <div class="relative h-[500px] flex items-center justify-center">
                <!-- Dramatic gradient spotlight background -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <!-- Red-to-black radial gradient for dramatic lighting -->
                    <div class="absolute top-0 left-1/4 w-96 h-96 bg-gradient-radial from-red-900/40 via-red-950/20 to-transparent blur-3xl"></div>
                    <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-gradient-radial from-yellow-600/30 via-yellow-900/10 to-transparent blur-3xl"></div>
                    <!-- Vignette effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-black/60"></div>
                </div>
                
                <!-- Main composition container -->
                <div class="relative flex items-center justify-center gap-8">
                    <!-- Burger - large hero piece on the left -->
                    <div class="relative z-30 transform -rotate-2 hover:scale-105 hover:rotate-0 transition-all duration-700"
                         style="filter: drop-shadow(0 30px 60px rgba(139,0,0,0.7)) drop-shadow(0 10px 30px rgba(0,0,0,0.8)) drop-shadow(0 5px 15px rgba(220,38,38,0.6));">
                        <img src="{{ asset('images/burger-cutout.png') }}" 
                             alt="Premium EUT Double Cheeseburger" 
                             class="w-[26rem] lg:w-[34rem] object-contain select-none">
                    </div>
                    
                    <!-- Fries - on the right side, partially behind burger, clearly visible -->
                    <div class="relative z-20 transform rotate-8 hover:scale-105 hover:rotate-6 transition-all duration-700"
                         style="filter: drop-shadow(0 25px 50px rgba(180,83,9,0.7)) drop-shadow(0 10px 30px rgba(0,0,0,0.8)) drop-shadow(0 5px 12px rgba(234,179,8,0.5)); margin-left: -120px;">
                        <img src="{{ asset('images/fries-cutout.png') }}" 
                             alt="Crispy Golden Fries" 
                             class="w-64 lg:w-80 object-contain select-none"
                             style="transform: translateY(30px);">
                    </div>
                </div>
                
                <!-- Floating seasoning particles effect -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden">
                    <div class="absolute top-1/4 left-1/3 w-1 h-1 bg-yellow-300 rounded-full opacity-60 animate-float-slow"></div>
                    <div class="absolute top-1/3 right-1/3 w-1.5 h-1.5 bg-red-400 rounded-full opacity-50 animate-float-slower"></div>
                    <div class="absolute bottom-1/3 left-2/5 w-1 h-1 bg-yellow-200 rounded-full opacity-70 animate-float-fast"></div>
                    <div class="absolute top-2/5 right-2/5 w-0.5 h-0.5 bg-white rounded-full opacity-80 animate-float-slow"></div>
                </div>
                
                <!-- Subtle rim light effect -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-yellow-500/20 rounded-full blur-3xl pointer-events-none"></div>
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

    <!-- Divider -->
    <div class="w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

    <!-- Features Section -->
    <section class="py-20 bg-gray-900 relative overflow-hidden">
        <!-- Decorative cutout left -->
        <div class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-12 opacity-20 pointer-events-none select-none rotate-12">
            <img src="{{ asset('images/deco-burger2.png') }}" class="w-96" alt="">
        </div>
        <!-- Decorative cutout right -->
        <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 opacity-20 pointer-events-none select-none -rotate-12">
            <img src="{{ asset('images/deco-fries2.png') }}" class="w-80" alt="">
        </div>
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
                    <h3 class="text-xl font-semibold mb-4 text-yellow-400">Expert Chefs</h3>
                    <p class="text-gray-300">Our experienced chefs craft each dish with passion and culinary expertise.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="text-center group">
                    <div class="bg-gray-700 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4 text-yellow-400">Cozy Atmosphere</h3>
                    <p class="text-gray-300">Relax and unwind in our warm, inviting atmosphere perfect for any occasion.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div class="w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

    <!-- Menu Preview Section -->
    <section id="menu" class="py-20 bg-black relative overflow-hidden">
        <!-- Decorative cutout left -->
        <div class="absolute left-0 bottom-10 -translate-x-10 opacity-20 pointer-events-none select-none -rotate-6">
            <img src="{{ asset('images/deco-fries2.png') }}" class="w-80" alt="">
        </div>
        <!-- Decorative cutout right -->
        <div class="absolute right-0 top-10 translate-x-10 opacity-20 pointer-events-none select-none rotate-6">
            <img src="{{ asset('images/deco-burger2.png') }}" class="w-96" alt="">
        </div>
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
                <div class="bg-gray-900 border border-yellow-500/30 rounded-xl overflow-hidden hover:transform hover:scale-105 transition duration-300 shadow-lg hover:border-yellow-400">
                    <div class="h-48 overflow-hidden">
                        <img src="{{ asset('images/french-fries.jpg') }}" 
                             alt="Classic French Fries" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2 text-yellow-400">Classic French Fries</h3>
                        <p class="text-gray-300 mb-4">Golden crispy fries with sea salt - perfect side for any meal</p>
                        <div class="flex justify-between items-center">
                            <span class="text-yellow-400 text-xl font-bold">₱120</span>
                            <button class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded-full text-sm font-semibold transition duration-300">
                                Order Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div class="w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-900 relative overflow-hidden">
        <!-- Decorative cutout left -->
        <div class="absolute left-0 top-10 -translate-x-10 opacity-20 pointer-events-none select-none rotate-6">
            <img src="{{ asset('images/deco-pepper.png') }}" class="w-72" alt="">
        </div>
        <!-- Decorative cutout right -->
        <div class="absolute right-0 bottom-10 translate-x-10 opacity-20 pointer-events-none select-none -rotate-6">
            <img src="{{ asset('images/fries-cutout.png') }}" class="w-80" alt="">
        </div>
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
                            <div class="text-3xl font-bold text-gray-300 mb-2">1000+</div>
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


    <!-- Divider -->
    <div class="w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

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
                        <a href="#" class="bg-gray-700 hover:bg-yellow-500 text-white hover:text-black w-10 h-10 rounded-full flex items-center justify-center transition duration-300">
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
                    <h4 class="text-lg font-semibold mb-4 text-gray-300">Contact Info</h4>
                    <div class="space-y-2 text-gray-300">
                        <p>123 Food Street</p>
                        <p>Culinary District, City</p>
                        <p class="text-yellow-400">+63 912 345 6789</p>
                        <p class="text-red-400">info@eutrestaurant.com</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-red-600/30 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2026 EUT Restaurant. All rights reserved.</p>
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

    <!-- ===================== AUTH MODAL ===================== -->
    <div id="authModal" class="fixed inset-0 z-[9999] flex items-center justify-center hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/85 backdrop-blur-sm" onclick="closeModal()"></div>

        <!-- Modal Box -->
        <div class="relative w-full max-w-md mx-4 rounded-2xl overflow-hidden shadow-2xl" style="background: #0a0a0a;">

            <!-- Close button -->
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-white transition z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Tab Switcher -->
            <div class="flex border-b border-white/10">
                <button id="loginTab" onclick="switchTab('login')"
                    class="flex-1 py-4 text-sm font-semibold tracking-widest uppercase transition duration-300 text-yellow-400 border-b-2 border-yellow-400">
                    Login
                </button>
                <button id="signupTab" onclick="switchTab('signup')"
                    class="flex-1 py-4 text-sm font-semibold tracking-widest uppercase transition duration-300 text-gray-500 border-b-2 border-transparent hover:text-gray-300">
                    Sign Up
                </button>
            </div>

            <!-- Error/Success alert -->
            <div id="authAlert" class="hidden mx-8 mt-6 px-4 py-3 rounded-xl text-sm font-medium"></div>

            <div class="p-8 pt-4">

                <!-- ---- LOGIN PANEL ---- -->
                <div id="loginPanel">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-1" style="font-family:'Playfair Display',serif;">Welcome Back</h2>
                    <p class="text-gray-400 text-sm mb-6">Sign in to your EUT account</p>

                    <!-- Google Login -->
                    <a href="{{ route('auth.google') }}"
                       class="w-full flex items-center justify-center gap-3 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white font-semibold py-3 rounded-xl mb-4 transition duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continue with Google
                    </a>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-1 h-px bg-white/10"></div>
                        <span class="text-gray-600 text-xs">or continue with email</span>
                        <div class="flex-1 h-px bg-white/10"></div>
                    </div>

                    <!-- Login Form -->
                    <form id="loginForm" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-gray-400 text-xs mb-1 uppercase tracking-wider">Email</label>
                            <input id="loginEmail" type="email" name="email" placeholder="you@example.com"
                                class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-yellow-400 transition"/>
                        </div>
                        <div>
                            <label class="block text-gray-400 text-xs mb-1 uppercase tracking-wider">Password</label>
                            <input id="loginPassword" type="password" name="password" placeholder="••••••••"
                                class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-yellow-400 transition"/>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <label class="flex items-center gap-2 cursor-pointer hover:text-gray-300 transition">
                                <input id="loginRemember" type="checkbox" class="accent-yellow-400"> Remember me
                            </label>
                            <a href="#" class="hover:text-yellow-400 transition">Forgot password?</a>
                        </div>
                        <button type="submit" id="loginBtn"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition duration-300 transform hover:scale-105">
                            Login
                        </button>
                    </form>
                    <p class="text-center text-gray-500 text-sm mt-6">
                        Don't have an account?
                        <button onclick="switchTab('signup')" class="text-yellow-400 hover:text-yellow-300 font-semibold transition">Sign up</button>
                    </p>
                </div>

                <!-- ---- SIGN UP PANEL ---- -->
                <div id="signupPanel" class="hidden">
                    <h2 class="text-2xl font-bold text-yellow-400 mb-1" style="font-family:'Playfair Display',serif;">Create Account</h2>
                    <p class="text-gray-400 text-sm mb-6">Join EUT Restaurant today</p>

                    <!-- Google Signup -->
                    <a href="{{ route('auth.google') }}"
                       class="w-full flex items-center justify-center gap-3 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white font-semibold py-3 rounded-xl mb-4 transition duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Sign up with Google
                    </a>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex-1 h-px bg-white/10"></div>
                        <span class="text-gray-600 text-xs">or sign up with email</span>
                        <div class="flex-1 h-px bg-white/10"></div>
                    </div>

                    <!-- Signup Form -->
                    <form id="signupForm" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-gray-400 text-xs mb-1 uppercase tracking-wider">First Name</label>
                                <input id="signupFirstName" type="text" name="first_name" placeholder="Juan"
                                    class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-yellow-400 transition"/>
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs mb-1 uppercase tracking-wider">Last Name</label>
                                <input id="signupLastName" type="text" name="last_name" placeholder="Dela Cruz"
                                    class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-yellow-400 transition"/>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-400 text-xs mb-1 uppercase tracking-wider">Email</label>
                            <input id="signupEmail" type="email" name="email" placeholder="you@example.com"
                                class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-yellow-400 transition"/>
                        </div>
                        <div>
                            <label class="block text-gray-400 text-xs mb-1 uppercase tracking-wider">Password</label>
                            <input id="signupPassword" type="password" name="password" placeholder="••••••••"
                                class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-yellow-400 transition"/>
                        </div>
                        <div>
                            <label class="block text-gray-400 text-xs mb-1 uppercase tracking-wider">Confirm Password</label>
                            <input id="signupPasswordConfirm" type="password" name="password_confirmation" placeholder="••••••••"
                                class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:border-yellow-400 transition"/>
                        </div>
                        <button type="submit" id="signupBtn"
                            class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-3 rounded-xl transition duration-300 transform hover:scale-105">
                            Create Account
                        </button>
                    </form>
                    <p class="text-center text-gray-500 text-sm mt-6">
                        Already have an account?
                        <button onclick="switchTab('login')" class="text-yellow-400 hover:text-yellow-300 font-semibold transition">Login</button>
                    </p>
                </div>

            </div>
        </div>
    </div>
    <!-- ===================== END MODAL ===================== -->

    @if(session('success'))
    <div id="flashSuccess" class="fixed top-6 right-6 z-[99999] bg-green-600 text-white px-6 py-4 rounded-xl shadow-lg font-semibold text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div id="flashError" class="fixed top-6 right-6 z-[99999] bg-red-600 text-white px-6 py-4 rounded-xl shadow-lg font-semibold text-sm">
        {{ session('error') }}
    </div>
    @endif

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        // ── Modal open/close ──────────────────────────────────────────
        function openModal(tab) {
            document.getElementById('authModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            hideAlert();
            switchTab(tab || 'login');
        }

        function closeModal() {
            document.getElementById('authModal').classList.add('hidden');
            document.body.style.overflow = '';
            hideAlert();
        }

        // ── Tab switch ────────────────────────────────────────────────
        function switchTab(tab) {
            const loginPanel  = document.getElementById('loginPanel');
            const signupPanel = document.getElementById('signupPanel');
            const loginTab    = document.getElementById('loginTab');
            const signupTab   = document.getElementById('signupTab');
            hideAlert();

            if (tab === 'login') {
                loginPanel.classList.remove('hidden');
                signupPanel.classList.add('hidden');
                loginTab.classList.add('text-yellow-400', 'border-yellow-400');
                loginTab.classList.remove('text-gray-500', 'border-transparent');
                signupTab.classList.add('text-gray-500', 'border-transparent');
                signupTab.classList.remove('text-yellow-400', 'border-yellow-400');
            } else {
                signupPanel.classList.remove('hidden');
                loginPanel.classList.add('hidden');
                signupTab.classList.add('text-yellow-400', 'border-yellow-400');
                signupTab.classList.remove('text-gray-500', 'border-transparent');
                loginTab.classList.add('text-gray-500', 'border-transparent');
                loginTab.classList.remove('text-yellow-400', 'border-yellow-400');
            }
        }

        // ── Alert helpers ─────────────────────────────────────────────
        function showAlert(msg, type = 'error') {
            const el = document.getElementById('authAlert');
            el.textContent = msg;
            el.className = 'mx-8 mt-4 px-4 py-3 rounded-xl text-sm font-medium ' +
                (type === 'success'
                    ? 'bg-green-600/20 border border-green-500/40 text-green-300'
                    : 'bg-red-600/20 border border-red-500/40 text-red-300');
        }

        function hideAlert() {
            const el = document.getElementById('authAlert');
            el.className = 'hidden';
            el.textContent = '';
        }

        function setLoading(btnId, loading) {
            const btn = document.getElementById(btnId);
            btn.disabled = loading;
            btn.textContent = loading ? 'Please wait…' : (btnId === 'loginBtn' ? 'Login' : 'Create Account');
        }

        // ── LOGIN form submit ─────────────────────────────────────────
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            hideAlert();
            setLoading('loginBtn', true);

            try {
                const res = await fetch('{{ route("auth.login") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        email:    document.getElementById('loginEmail').value,
                        password: document.getElementById('loginPassword').value,
                        remember: document.getElementById('loginRemember').checked,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    showAlert('Login successful! Redirecting…', 'success');
                    setTimeout(() => { window.location.href = data.redirect; }, 800);
                } else {
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Invalid credentials.');
                    showAlert(msg);
                    setLoading('loginBtn', false);
                }
            } catch (err) {
                showAlert('Something went wrong. Please try again.');
                setLoading('loginBtn', false);
            }
        });

        // ── SIGNUP form submit ────────────────────────────────────────
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            hideAlert();
            setLoading('signupBtn', true);

            const password = document.getElementById('signupPassword').value;
            const confirm  = document.getElementById('signupPasswordConfirm').value;

            if (password !== confirm) {
                showAlert('Passwords do not match.');
                setLoading('signupBtn', false);
                return;
            }

            try {
                const res = await fetch('{{ route("auth.signup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        first_name:            document.getElementById('signupFirstName').value,
                        last_name:             document.getElementById('signupLastName').value,
                        email:                 document.getElementById('signupEmail').value,
                        password:              password,
                        password_confirmation: confirm,
                    }),
                });

                const data = await res.json();

                if (data.success) {
                    showAlert('Account created! Redirecting…', 'success');
                    setTimeout(() => { window.location.href = data.redirect; }, 800);
                } else {
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Signup failed.');
                    showAlert(msg);
                    setLoading('signupBtn', false);
                }
            } catch (err) {
                showAlert('Something went wrong. Please try again.');
                setLoading('signupBtn', false);
            }
        });

        // ── Auto-dismiss flash messages ───────────────────────────────
        ['flashSuccess','flashError'].forEach(id => {
            const el = document.getElementById(id);
            if (el) setTimeout(() => el.remove(), 4000);
        });

        // ── Close on ESC ──────────────────────────────────────────────
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
    </script>

</body>
</html>
</html>
