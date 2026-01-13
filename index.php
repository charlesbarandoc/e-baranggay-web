<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Bacsay Mapula-pula | Official Portal</title>
    
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/lucide@latest"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .scroll-active { background-color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .nav-default { background-color: transparent; padding-top: 1rem; padding-bottom: 1rem; }
        .text-scroll { color: #1e293b; } /* slate-800 */
        .text-default { color: white; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <?php

    
    $news_data = [
        [
            'id' => 1,
            'title' => "Distribution of Certified Seeds",
            'date' => "Dec 27, 2025",
            'category' => "Agriculture",
            'urgent' => false,
            'excerpt' => "Farmers listed in the RSBSA can now claim their certified seeds at the MP Hall."
        ],
        [
            'id' => 2,
            'title' => "Typhoon Signal No. 1 Advisory",
            'date' => "Dec 26, 2025",
            'category' => "Urgent",
            'urgent' => true,
            'excerpt' => "All residents are advised to secure their roofs and livestock. Emergency response team is on standby."
        ],
        [
            'id' => 3,
            'title' => "Brgy. Assembly (Pulong-Pulong)",
            'date' => "Jan 05, 2026",
            'category' => "Events",
            'urgent' => false,
            'excerpt' => "Agenda includes the 2026 Budget Presentation and Road Widening Projects."
        ],
        [
            'id' => 4,
            'title' => "Free Anti-Rabies Vaccination",
            'date' => "Jan 10, 2026",
            'category' => "Health",
            'urgent' => false,
            'excerpt' => "Bring your pets to the covered court. Starts at 8:00 AM."
        ]
    ];
    ?>

    <!-- NAVIGATION -->
    <nav id="navbar" class="fixed w-full z-50 transition-all duration-300 nav-default">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-red-700 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                    BM
                </div>
                <div id="nav-text" class="flex flex-col text-white transition-colors duration-300">
                    <span class="font-bold leading-tight">BACSAY MAPULA-PULA</span>
                    <span class="text-xs opacity-90">Claveria, Cagayan</span>
                </div>
            </div>

            <!-- Desktop Menu -->
            <div id="nav-links" class="hidden md:flex space-x-8 font-medium text-white transition-colors duration-300">
                <a href="#home" class="hover:text-red-500 transition-colors">Home</a>
                <a href="#services" class="hover:text-red-500 transition-colors">Services</a>
                <a href="#news" class="hover:text-red-500 transition-colors">Balitaan</a>
                <a href="#contact" class="hover:text-red-500 transition-colors">Contact</a>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-white">
                <i data-lucide="menu"></i>
            </button>
        </div>

        <!-- Mobile Dropdown -->
        <div id="mobile-menu" class="hidden bg-white absolute top-full left-0 w-full shadow-xl border-t">
            <div class="flex flex-col p-4 space-y-4">
                <a href="#home" class="text-slate-700 hover:text-red-600 font-medium">Home</a>
                <a href="#services" class="text-slate-700 hover:text-red-600 font-medium">Services</a>
                <a href="#news" class="text-slate-700 hover:text-red-600 font-medium">Balitaan</a>
                <a href="#contact" class="text-slate-700 hover:text-red-600 font-medium">Contact</a>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="home" class="relative h-screen flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="image/baranggayhall.png" alt="Rice Fields" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-slate-900/90"></div>
        </div>

        <div class="container mx-auto px-4 z-10 text-center text-white mt-16">
            <div class="inline-block px-4 py-1 mb-4 border border-white/30 rounded-full bg-white/10 backdrop-blur-sm">
                <span class="text-sm font-medium tracking-wider uppercase">Official Digital Portal</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-extrabold mb-4 tracking-tight">
                Naragsak a <span class="text-red-500">Ya-ayyo!</span>
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-2xl mx-auto text-slate-200 font-light">
                Welcome to Barangay Bacsay Mapula-pula. A progressive community in the heart of Claveria, Cagayan.
            </p>
            
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                    <i data-lucide="file-text"></i> Request Clearance
                </button>
                <button class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white rounded-lg font-bold transition-all flex items-center justify-center gap-2">
                    <i data-lucide="alert-circle"></i> Emergency Hotlines
                </button>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION -->
    <section id="services" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-red-600 font-bold tracking-widest uppercase text-sm mb-2">E-Services</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-slate-900">What do you need today?</h3>
                <p class="text-slate-500 mt-4 max-w-xl mx-auto">Skip the line at the Barangay Hall. Request your documents online.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="group bg-white rounded-xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:border-red-100 transition-all duration-300 cursor-pointer">
                    <div class="mb-6 transform group-hover:scale-110 transition-transform duration-300 inline-block bg-slate-50 p-4 rounded-full">
                        <i data-lucide="file-text" class="text-red-500 w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Barangay Clearance</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">Requirement for employment, business permit, or postal ID applications.</p>
                    <div class="flex items-center text-red-600 font-bold text-sm group-hover:translate-x-2 transition-transform">
                        Request Now <i data-lucide="chevron-right" class="ml-1 w-4 h-4"></i>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="group bg-white rounded-xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:border-red-100 transition-all duration-300 cursor-pointer">
                    <div class="mb-6 transform group-hover:scale-110 transition-transform duration-300 inline-block bg-slate-50 p-4 rounded-full">
                        <i data-lucide="users" class="text-blue-500 w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Certificate of Indigency</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">For educational assistance, medical help, or legal aid requirements.</p>
                    <div class="flex items-center text-red-600 font-bold text-sm group-hover:translate-x-2 transition-transform">
                        Request Now <i data-lucide="chevron-right" class="ml-1 w-4 h-4"></i>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="group bg-white rounded-xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:border-red-100 transition-all duration-300 cursor-pointer">
                    <div class="mb-6 transform group-hover:scale-110 transition-transform duration-300 inline-block bg-slate-50 p-4 rounded-full">
                        <i data-lucide="map-pin" class="text-green-500 w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Certificate of Residency</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">Proof of domicile for voters registration or bank account opening.</p>
                    <div class="flex items-center text-red-600 font-bold text-sm group-hover:translate-x-2 transition-transform">
                        Request Now <i data-lucide="chevron-right" class="ml-1 w-4 h-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWS FEED (PHP + JS Filter) -->
    <section id="news" class="py-20 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <h2 class="text-red-600 font-bold tracking-widest uppercase text-sm mb-2">Balitaan</h2>
                    <h3 class="text-3xl md:text-4xl font-bold text-slate-900">Latest Updates</h3>
                </div>
                
                <!-- Filter Buttons -->
                <div class="flex space-x-2 mt-4 md:mt-0 overflow-x-auto pb-2 md:pb-0" id="filter-buttons">
                    <button onclick="filterNews('All')" class="filter-btn active px-4 py-2 rounded-full text-sm font-medium transition-colors bg-red-600 text-white shadow-md" data-category="All">All</button>
                    <button onclick="filterNews('Urgent')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-colors bg-white text-slate-600 hover:bg-slate-200" data-category="Urgent">Urgent</button>
                    <button onclick="filterNews('Agriculture')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-colors bg-white text-slate-600 hover:bg-slate-200" data-category="Agriculture">Agriculture</button>
                    <button onclick="filterNews('Health')" class="filter-btn px-4 py-2 rounded-full text-sm font-medium transition-colors bg-white text-slate-600 hover:bg-slate-200" data-category="Health">Health</button>
                </div>
            </div>

            <!-- News Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="news-grid">
                <?php foreach ($news_data as $item): ?>
                    <div class="news-item bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-slate-100 flex flex-col h-full" 
                         data-category="<?php echo $item['category']; ?>"
                         data-urgent="<?php echo $item['urgent'] ? 'true' : 'false'; ?>">
                        
                        <div class="h-2 w-full <?php echo $item['urgent'] ? 'bg-red-500' : 'bg-green-500'; ?>"></div>
                        
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xs font-bold text-slate-400 flex items-center">
                                    <i data-lucide="calendar" class="w-3 h-3 mr-1"></i> <?php echo $item['date']; ?>
                                </span>
                                <?php if ($item['urgent']): ?>
                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-bold animate-pulse">ALERT</span>
                                <?php endif; ?>
                            </div>
                            <h4 class="font-bold text-lg mb-2 text-slate-800 leading-tight"><?php echo $item['title']; ?></h4>
                            <p class="text-slate-500 text-sm mb-4 flex-grow"><?php echo $item['excerpt']; ?></p>
                            <button class="text-red-600 font-bold text-sm hover:underline mt-auto self-start flex items-center">
                                Read Full Story <i data-lucide="arrow-right" class="w-3 h-3 ml-1"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer id="contact" class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-red-700 rounded-full flex items-center justify-center text-white font-bold text-sm">BM</div>
                        <span class="text-white font-bold text-lg">BACSAY MAPULA-PULA</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        An active partner in nation-building, committed to serving the people of Claveria with integrity.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Official Contact</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start">
                            <i data-lucide="map-pin" class="w-4 h-4 mr-2 mt-1"></i>
                            <span>Multi-Purpose Hall, Brgy. Bacsay Mapula-pula, Claveria, Cagayan</span>
                        </li>
                        <li class="flex items-center">
                            <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                            <span>+63 917 123 4567</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Office Hours</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between"><span>Mon - Fri</span><span class="text-white">8:00 AM - 5:00 PM</span></li>
                        <li class="flex justify-between"><span>Sunday</span><span class="text-red-400">Closed</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-12 pt-8 text-center text-xs">
                <p>&copy; <?php echo date("Y"); ?> Barangay Bacsay Mapula-pula. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT LOGIC (No Build Tools needed) -->
    <script>
        // 1. Initialize Icons
        lucide.createIcons();

        // 2. Navbar Scroll Effect
        const nav = document.getElementById('navbar');
        const navText = document.getElementById('nav-text');
        const navLinks = document.getElementById('nav-links');
        const mobileBtn = document.getElementById('mobile-menu-btn');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                nav.classList.remove('nav-default');
                nav.classList.add('scroll-active');
                
                // Change text color to dark
                navText.classList.remove('text-white');
                navText.classList.add('text-scroll');
                
                navLinks.classList.remove('text-white');
                navLinks.classList.add('text-scroll');
                
                mobileBtn.classList.remove('text-white');
                mobileBtn.classList.add('text-scroll');
            } else {
                nav.classList.add('nav-default');
                nav.classList.remove('scroll-active');
                
                // Change text color back to white
                navText.classList.add('text-white');
                navText.classList.remove('text-scroll');
                
                navLinks.classList.add('text-white');
                navLinks.classList.remove('text-scroll');
                
                mobileBtn.classList.add('text-white');
                mobileBtn.classList.remove('text-scroll');
            }
        });

        // 3. Mobile Menu Toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // 4. News Filter Logic
        function filterNews(category) {
            const items = document.querySelectorAll('.news-item');
            const buttons = document.querySelectorAll('.filter-btn');

            // Update Buttons style
            buttons.forEach(btn => {
                if(btn.dataset.category === category) {
                    btn.classList.remove('bg-white', 'text-slate-600', 'hover:bg-slate-200');
                    btn.classList.add('bg-red-600', 'text-white', 'shadow-md');
                } else {
                    btn.classList.add('bg-white', 'text-slate-600', 'hover:bg-slate-200');
                    btn.classList.remove('bg-red-600', 'text-white', 'shadow-md');
                }
            });

            // Filter Items
            items.forEach(item => {
                const itemCat = item.dataset.category;
                const isUrgent = item.dataset.urgent === 'true';

                if (category === 'All') {
                    item.style.display = 'flex';
                } else if (category === 'Urgent') {
                    item.style.display = isUrgent ? 'flex' : 'none';
                } else {
                    item.style.display = itemCat === category ? 'flex' : 'none';
                }
            });
        }
    </script>
</body>
</html>