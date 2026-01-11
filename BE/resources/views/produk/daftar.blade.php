<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Roasty - Daftar Produk</title>
<!-- Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Theme Config -->
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f26c0d",
                        "background-light": "#f8f7f5",
                        "background-dark": "#221710",
                    },
                    fontFamily: {
                        "display": ["Plus Jakarta Sans", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
<!-- Top Navigation -->
<header class="sticky top-0 z-50 bg-white dark:bg-[#1a120b] border-b border-gray-200 dark:border-gray-800 shadow-sm">
<div class="max-w-[1280px] mx-auto px-4 md:px-8 h-20 flex items-center justify-between gap-8">
<!-- Logo -->
<a class="flex items-center gap-2 text-primary hover:opacity-90 transition-opacity" href="{{ route('home') }}">
<span class="material-symbols-outlined !text-[32px] fill-1">local_cafe</span>
<h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Roasty</h1>
</a>
<!-- Search -->
<div class="flex-1 max-w-2xl">
<div class="relative flex items-center w-full h-11 rounded-lg border border-gray-200 dark:border-gray-700 bg-background-light dark:bg-gray-800 overflow-hidden focus-within:ring-2 focus-within:ring-primary focus-within:border-transparent transition-all">
<div class="pl-4 pr-2 text-gray-400 dark:text-gray-500 flex items-center justify-center">
<span class="material-symbols-outlined">search</span>
</div>
<input class="w-full h-full bg-transparent border-none outline-none text-sm text-slate-900 dark:text-white placeholder:text-gray-400 focus:ring-0" placeholder="Cari kopi, alat seduh, atau merchandise..." type="text"/>
</div>
</div>
<!-- Actions -->
<div class="flex items-center gap-6">
<!-- Nav Links -->
<nav class="hidden lg:flex items-center gap-6">
<a class="text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-primary transition-colors" href="{{ route('produk.index') }}">Kategori</a>
<a class="text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-primary transition-colors" href="{{ route('produk.index') }}">Promo</a>
</nav>
<div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-2 hidden lg:block"></div>
<!-- Icons -->
<div class="flex items-center gap-3">
<!-- Admin Button - Only for Admin Users -->
<a id="admin-button" href="{{ route('admin.dashboard') }}" class="hidden items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 text-white rounded-lg hover:shadow-lg transition-all text-sm font-semibold whitespace-nowrap">
<span class="material-symbols-outlined text-lg">admin_panel_settings</span>
<span class="hidden md:inline">Admin Panel</span>
</a>

<!-- User Navigation Icons -->
<div id="user-icons" class="flex items-center gap-3">
<!-- Notification Icon -->
<button id="notification-button" class="hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition-colors">
<span class="material-symbols-outlined">notifications</span>
<span id="notification-badge" class="absolute top-1 right-1 w-2 h-2 bg-primary rounded-full hidden"></span>
</button>

<!-- Cart Icon -->
<button id="cart-button" class="hidden relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-300 transition-colors">
<span class="material-symbols-outlined">shopping_cart</span>
<span id="cart-badge" class="absolute top-1 right-1 w-5 h-5 bg-primary text-white text-xs rounded-full flex items-center justify-center text-[10px] font-bold hidden">0</span>
</button>
</div>

<!-- Profile Button -->
<button id="profile-button" class="flex items-center gap-2 p-1 pr-3 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 border border-transparent hover:border-gray-200 dark:hover:border-gray-700 transition-all">
<div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden" id="profile-avatar" data-alt="User avatar profile picture">
</div>
<span class="text-sm font-medium hidden sm:block" id="profile-name">User</span>
</button>
</div>
</div>
</div>
</header>
<!-- Main Content -->
<main class="flex-grow w-full max-w-[1280px] mx-auto px-4 md:px-8 py-6">
<!-- Breadcrumbs -->
<nav class="flex mb-6 text-sm text-gray-500 dark:text-gray-400">
<ol class="flex items-center gap-2">
<li><a class="hover:text-primary" href="{{ route('home') }}">Home</a></li>
<li><span class="material-symbols-outlined text-[16px]">chevron_right</span></li>
<li><a class="hover:text-primary" href="{{ route('produk.index') }}">Kopi</a></li>
<li><span class="material-symbols-outlined text-[16px]">chevron_right</span></li>
<li class="font-semibold text-slate-900 dark:text-white">Biji Kopi</li>
</ol>
</nav>
<div class="flex flex-col lg:flex-row gap-8">
<!-- Sidebar Filters -->
<aside class="w-full lg:w-64 flex-shrink-0 space-y-8">
<!-- Filter Group: Category -->
<div class="bg-white dark:bg-[#1a120b] p-5 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
<h3 class="font-bold text-slate-900 dark:text-white mb-4">Kategori</h3>
<ul class="space-y-3 text-sm">
<li>
<a class="font-bold text-primary flex justify-between items-center group" href="#">
                                Biji Kopi
                                <span class="material-symbols-outlined text-[18px] opacity-100">check</span>
</a>
</li>
<li><a class="text-gray-600 dark:text-gray-400 hover:text-primary block" href="#">Kopi Bubuk</a></li>
<li><a class="text-gray-600 dark:text-gray-400 hover:text-primary block" href="#">Drip Bag</a></li>
<li><a class="text-gray-600 dark:text-gray-400 hover:text-primary block" href="#">Cold Brew</a></li>
<li><a class="text-gray-600 dark:text-gray-400 hover:text-primary block" href="#">Green Bean</a></li>
</ul>
</div>
<!-- Filter Group: Location -->
<div class="bg-white dark:bg-[#1a120b] p-5 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
<h3 class="font-bold text-slate-900 dark:text-white mb-4">Lokasi</h3>
<div class="space-y-3">
<label class="flex items-center gap-3 cursor-pointer group">
<input class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary" type="checkbox"/>
<span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-slate-900 dark:group-hover:text-white">Jabodetabek</span>
</label>
<label class="flex items-center gap-3 cursor-pointer group">
<input class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary" type="checkbox"/>
<span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-slate-900 dark:group-hover:text-white">Jawa Barat</span>
</label>
<label class="flex items-center gap-3 cursor-pointer group">
<input class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary" type="checkbox"/>
<span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-slate-900 dark:group-hover:text-white">Jawa Timur</span>
</label>
<label class="flex items-center gap-3 cursor-pointer group">
<input class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary" type="checkbox"/>
<span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-slate-900 dark:group-hover:text-white">Bali</span>
</label>
<button class="text-primary text-sm font-semibold hover:underline mt-2">Lihat Selengkapnya</button>
</div>
</div>
<!-- Filter Group: Price -->
<div class="bg-white dark:bg-[#1a120b] p-5 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
<h3 class="font-bold text-slate-900 dark:text-white mb-4">Harga</h3>
<div class="space-y-3">
<div class="relative">
<span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">Rp</span>
<input class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary dark:bg-gray-800 dark:text-white" placeholder="Harga Minimum" type="text"/>
</div>
<div class="relative">
<span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">Rp</span>
<input class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary dark:bg-gray-800 dark:text-white" placeholder="Harga Maksimum" type="text"/>
</div>
</div>
</div>
<!-- Filter Group: Rating -->
<div class="bg-white dark:bg-[#1a120b] p-5 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
<h3 class="font-bold text-slate-900 dark:text-white mb-4">Rating</h3>
<div class="space-y-3">
<label class="flex items-center gap-3 cursor-pointer group">
<input class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary" type="checkbox"/>
<div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
<span class="material-symbols-outlined text-[18px] text-yellow-400 fill-1" style="font-variation-settings: 'FILL' 1;">star</span>
                                4 Ke atas
                            </div>
</label>
</div>
</div>
</aside>
<!-- Product Grid Area -->
<section class="flex-1">
<!-- Page Title & Sort -->
<div class="mb-6">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Biji Kopi Pilihan</h2>
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-[#1a120b] p-3 rounded-lg border border-gray-100 dark:border-gray-800 shadow-sm">
<div class="text-sm text-gray-500 dark:text-gray-400 px-2">
                            Menampilkan <span class="font-bold text-slate-900 dark:text-white">1 - 24</span> dari <span class="font-bold text-slate-900 dark:text-white">3.450</span> produk
                        </div>
<!-- Sort Chips -->
<div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0 hide-scrollbar">
<span class="text-sm font-medium text-gray-500 whitespace-nowrap mr-1">Urutkan:</span>
<button class="px-4 py-1.5 rounded-full bg-primary/10 text-primary border border-primary text-sm font-bold whitespace-nowrap transition-colors">
                                Paling Sesuai
                            </button>
<button class="px-4 py-1.5 rounded-full bg-background-light dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium whitespace-nowrap transition-colors">
                                Terbaru
                            </button>
<button class="px-4 py-1.5 rounded-full bg-background-light dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium whitespace-nowrap transition-colors">
                                Terlaris
                            </button>
<button class="px-4 py-1.5 rounded-full bg-background-light dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-sm font-medium whitespace-nowrap transition-colors">
                                Harga
                                <span class="material-symbols-outlined text-[16px] align-middle ml-1">unfold_more</span>
</button>
</div>
</div>
</div>
<!-- Grid -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
<!-- Product Cards will be rendered here by JS -->
</div>
<!-- Pagination -->
<div class="mt-12 flex justify-center">
<div class="flex items-center gap-2">
<button class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:border-primary hover:text-primary transition-colors disabled:opacity-50">
<span class="material-symbols-outlined">chevron_left</span>
</button>
<button class="w-10 h-10 rounded-lg bg-primary text-white font-bold flex items-center justify-center shadow-md shadow-primary/30">1</button>
<button class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-700 text-slate-900 dark:text-white font-medium flex items-center justify-center hover:border-primary hover:text-primary transition-colors">2</button>
<button class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-700 text-slate-900 dark:text-white font-medium flex items-center justify-center hover:border-primary hover:text-primary transition-colors">3</button>
<span class="text-gray-400">...</span>
<button class="w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-700 text-slate-900 dark:text-white font-medium flex items-center justify-center hover:border-primary hover:text-primary transition-colors">10</button>
<button class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 text-slate-900 dark:text-white hover:border-primary hover:text-primary transition-colors">
<span class="material-symbols-outlined">chevron_right</span>
</button>
</div>
</div>
</section>
</div>
</main>
<!-- Footer Simple -->
<footer class="bg-white dark:bg-[#1a120b] border-t border-gray-200 dark:border-gray-800 mt-12 py-8">
<div class="max-w-[1280px] mx-auto px-4 md:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
<div class="flex items-center gap-2 text-primary">
<span class="material-symbols-outlined !text-[24px] fill-1">local_cafe</span>
<span class="text-xl font-bold text-slate-900 dark:text-white">Roasty</span>
</div>
<p class="text-sm text-gray-500">© 2024 Roasty Marketplace. All rights reserved.</p>
</div>
</footer>

<script src="{{ asset('config.js') }}"></script>
<script src="{{ asset('navbar-helper.js') }}"></script>
<script>
const API_URL = CONFIG.API_BASE_URL;
let allProducts = [];

// Normalize image URL helper function
function normalizeImageUrl(imageUrl) {
    if (!imageUrl) return 'https://via.placeholder.com/200?text=No+Image';

    imageUrl = String(imageUrl).trim();

    // If it's a data URI, use directly (don't prepend anything)
    if (imageUrl.startsWith('data:')) {
        return imageUrl;
    }

    // If it's already a full HTTP URL, use as-is
    if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
        return imageUrl;
    }

    // If it's a relative path from /storage, clean it and prepend assets URL
    if (imageUrl.startsWith('/storage/') || imageUrl.startsWith('storage/')) {
        const cleanUrl = imageUrl.replace(/^\/storage\//, '');
        return `${CONFIG.assets}/${cleanUrl}`;
    }

    // Otherwise assume it's a relative path and prepend assets URL
    return `${CONFIG.assets}/${imageUrl}`;
}
// Update user profile display
async function updateUserProfile() {
    try {
        const token = localStorage.getItem('token');
        if (!token) return;

        const user = JSON.parse(localStorage.getItem('user') || '{}');

        // Update user name di header
        const userNameElement = document.querySelector('button.flex.items-center.gap-2.p-1 span.hidden');
        if (userNameElement && user.name) {
            userNameElement.textContent = user.name;
        }
    } catch (err) {
        console.error('Error updating user profile:', err);
    }
}

// Load products from backend
async function loadProducts() {
    try {
        const token = localStorage.getItem('token');
        const res = await fetch(`${API_URL}/products`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        if (!res.ok) throw new Error('Gagal memuat produk');

        const data = await res.json();
        console.log('API Response full structure:', data);

        // Handle paginated response from Laravel
        // API returns: { success, message, data: { data: [...], meta, links } }
        let productArray = [];

        if (data.data) {
            if (Array.isArray(data.data)) {
                // data.data is array directly
                productArray = data.data;
            } else if (data.data.data && Array.isArray(data.data.data)) {
                // data.data is pagination object with nested .data array
                productArray = data.data.data;
            }
        } else if (Array.isArray(data)) {
            productArray = data;
        }

        allProducts = productArray;
        console.log('Parsed products array:', allProducts);
        console.log('Sample product IDs:', allProducts.slice(0, 3).map(p => ({ id: p.id, name: p.name })));

        // Render products ke halaman
        renderProducts(allProducts);

        // Setup event listeners setelah render selesai
        // Gunakan requestAnimationFrame untuk tunggu DOM update
        requestAnimationFrame(() => {
            setupExistingProductCards();
        });

    } catch (err) {
        console.error('Error loading products:', err);
        alert('Gagal memuat produk. Silakan refresh halaman.');
    }
}

// Render products to the page
function renderProducts(products) {
    // Find the grid container - look for grid with gap-4 class
    // QuerySelector doesn't support Tailwind's : so we use a simpler selector
    let productGrid = document.querySelector('div.grid.gap-4');

    // If not found, try finding parent main and then grid inside
    if (!productGrid) {
        const main = document.querySelector('main');
        if (main) {
            productGrid = main.querySelector('div.grid.gap-4');
        }
    }

    if (!productGrid) {
        console.error('Product grid container not found. Trying alternative method...');
        // Last resort: find by finding all grids and taking the one with most direct grid-cols
        const grids = document.querySelectorAll('div.grid');
        console.log('Found grids:', grids.length);
        for (let grid of grids) {
            if (grid.className.includes('gap-4')) {
                productGrid = grid;
                break;
            }
        }
    }

    if (!productGrid) {
        console.error('Product grid container not found after all attempts');
        return;
    }

    console.log('Found product grid:', productGrid);

    // If we have products from API, replace all cards
    if (products && products.length > 0) {
        // Log first product structure to debug
        console.log('First product structure:', products[0]);
        console.log('Product ID values:', products.map(p => ({ id: p.id, name: p.name })));

        // CLEAR ALL existing cards completely
        productGrid.innerHTML = '';

        console.log('Cleared grid, now rendering', products.length, 'products');

        // Create new cards from API data
        products.forEach((product, index) => {
            const card = document.createElement('div');
            card.className = 'group bg-white dark:bg-[#1a120b] rounded-lg border border-gray-100 dark:border-gray-800 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden flex flex-col h-full';

            // Set data attribute BEFORE setting innerHTML
            card.setAttribute('data-product-id', String(product.id));

            const price = parseInt(product.price);
            const originalPrice = Math.floor(price * 1.2); // Assume 20% discount
            const discount = Math.round(((originalPrice - price) / originalPrice) * 100);

            const fallbackImage = "/images/lakopi.jpg";

            // Get image from image_urls array
            const imageUrl = (product.image_urls && product.image_urls.length > 0)
                ? normalizeImageUrl(product.image_urls[0])
                : fallbackImage;

            card.innerHTML = `
                <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-800">
                    <img alt="${product.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         src="${imageUrl}" onerror="this.onerror=null;this.src='${fallbackImage}'">
                </div>
                <div class="p-3 flex flex-col flex-1">
                    <h3 class="text-sm font-medium text-slate-900 dark:text-white line-clamp-2 mb-1 group-hover:text-primary transition-colors">
                        ${product.name}
                    </h3>
                    <div class="mt-auto">
                        <p class="text-lg font-bold text-slate-900 dark:text-white">Rp ${price.toLocaleString('id-ID')}</p>
                        <p class="text-xs text-gray-400 line-through mb-1">Rp ${originalPrice.toLocaleString('id-ID')}</p>
                        <div class="flex items-center gap-1 mb-2">
                            <span class="px-1 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] font-bold rounded">Official</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate ml-1">${product.category || 'Kopi'}</span>
                        </div>
                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="material-symbols-outlined text-[14px] text-yellow-400 fill-1" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="text-slate-900 dark:text-white font-medium">4.8</span>
                            <span>|</span>
                            <span>Terjual 100+</span>
                        </div>
                    </div>
                </div>
            `;

            productGrid.appendChild(card);
        });

        console.log(`Rendered ${products.length} products from API`);
    }
}

// Setup product cards in grid to be clickable
function setupExistingProductCards() {
    // Find the grid container first - use selector without :
    let productGrid = document.querySelector('div.grid.gap-4');

    if (!productGrid) {
        const main = document.querySelector('main');
        if (main) {
            productGrid = main.querySelector('div.grid.gap-4');
        }
    }

    if (!productGrid) {
        // Fallback: find by looking for grid with gap-4
        const grids = document.querySelectorAll('div.grid');
        for (let grid of grids) {
            if (grid.className.includes('gap-4')) {
                productGrid = grid;
                break;
            }
        }
    }

    if (!productGrid) {
        console.error('Product grid not found in setupExistingProductCards');
        return;
    }

    // Find product cards only within the grid
    const productCards = productGrid.querySelectorAll('div.group.bg-white');

    console.log('setupExistingProductCards: Found', productCards.length, 'cards');
    console.log('setupExistingProductCards: allProducts has', allProducts.length, 'items');

    productCards.forEach((card, index) => {
        const productId = card.getAttribute('data-product-id');
        console.log(`Card ${index}: data-product-id = "${productId}"`);

        if (!productId) {
            console.warn(`Card ${index} has no data-product-id attribute, skipping...`);
            return;
        }

        card.style.cursor = 'pointer';
        console.log(`Setting up click listener for card ${index} with product ID: ${productId}`);

        card.addEventListener('click', function(e) {
            // Don't navigate if clicking button or link
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }

            e.preventDefault();

            const clickedProductId = this.getAttribute('data-product-id');
            console.log('Product clicked, ID:', clickedProductId);

            if (!clickedProductId) {
                console.error('No product ID in clicked card');
                alert('Produk tidak ditemukan');
                return;
            }

            // Save to sessionStorage
            sessionStorage.setItem('selectedProductId', clickedProductId);

            // Also find and store full product data
            const product = allProducts.find(p => String(p.id) === String(clickedProductId));
            if (product) {
                sessionStorage.setItem('currentProduct', JSON.stringify(product));
                console.log('Stored product data:', product);
            }

            // Navigate to detail page
            window.location.href = "{{ route('produk.detail') }}";
        });
    });
}

// Setup all header and navigation buttons
function setupAllButtons() {
    console.log('Setting up buttons...');

    // Check auth
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = "{{ route('login') }}";
        return;
    }

    // ===== SEARCH BUTTON =====
    const searchInput = document.querySelector('input[placeholder*="Cari"]');

    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = searchInput.value.trim();
                if (query) {
                    sessionStorage.setItem('searchQuery', query);
                    location.reload();
                }
            }
        });
    }

    // ===== HEADER - Cart, Notifications, Profile Buttons =====
    // Find the flex container with gap-3 that contains icon buttons
    const iconButtonContainer = document.querySelector('div.flex.items-center.gap-3');

    if (iconButtonContainer) {
        const buttons = iconButtonContainer.querySelectorAll('button');
        console.log('Header buttons found:', buttons.length);

        buttons.forEach((btn, idx) => {
            const icon = btn.querySelector('.material-symbols-outlined');
            const iconText = icon ? icon.textContent.trim() : '';

            console.log(`Button ${idx}: ${iconText}`);

            if (iconText === 'shopping_cart') {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Cart clicked');
                    window.location.href = "{{ route('keranjang') }}";
                });
            } else if (iconText === 'notifications') {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Notifications clicked');
                    alert('Tidak ada notifikasi baru');
                });
            }
        });

        // Profile button - find the button with flex and gap-2 that has an image
        const profileBtn = iconButtonContainer.querySelector('button.flex.items-center.gap-2');
        if (profileBtn) {
            profileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Profile clicked');
                window.location.href = "{{ route('profil') }}";
            });
        }
    }

    // ===== LOGO BUTTON =====
    const logoBtn = document.querySelector('header a.flex.items-center.gap-2');
    if (logoBtn) {
        logoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Logo clicked');
            window.location.href = "{{ route('home') }}";
        });
    }

    // ===== NAVIGATION LINKS (Kategori, Promo) =====
    const navLinks = document.querySelectorAll('nav.hidden a[href="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Nav link clicked');
            window.location.href = "{{ route('produk.index') }}";
        });
    });

    // ===== BREADCRUMB LINKS =====
    const breadcrumbs = document.querySelectorAll('nav.flex.mb-6 a');
    breadcrumbs.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const text = link.textContent.trim();
            console.log('Breadcrumb clicked:', text);

            if (text.toLowerCase() === 'home') {
                window.location.href = "{{ route('home') }}";
            } else {
                window.location.href = "{{ route('produk.index') }}";
            }
        });
    });

    // ===== SIDEBAR CATEGORY LINKS =====
    const sidebarLinks = document.querySelectorAll('aside a[href="#"]');
    console.log('Sidebar links found:', sidebarLinks.length);
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryName = link.textContent.trim();
            console.log('Category clicked:', categoryName);

            if (categoryName && !categoryName.includes('Lihat')) {
                sessionStorage.setItem('selectedCategory', categoryName);
            }
        });
    });

    console.log('Button setup complete');
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded');

    const token = localStorage.getItem('token');
    if (!token) {
        console.log('No token, redirecting to login');
        window.location.href = "{{ route('login') }}";
        return;
    }

    // Load data and setup
    setTimeout(() => {
        updateUserProfile();
        loadProducts();
        setupAllButtons();
    }, 100);
});
</script>

</body></html>
