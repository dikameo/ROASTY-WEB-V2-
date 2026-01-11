<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Roasty - Marketplace</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f26c0d",
                        "background-light": "#f8f7f5",
                        "background-dark": "#221710",
                        "surface-light": "#ffffff",
                        "surface-dark": "#2a1d15",
                        "text-main": "#1c130d",
                        "text-muted": "#9c6c49",
                    },
                    fontFamily: {
                        "display": ["Plus Jakarta Sans", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "2xl": "1rem", "full": "9999px"},
                },
            },
        }
    </script>
<style>.no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-card-entering {
            animation: slideUp 0.3s ease-out forwards;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-white font-display overflow-x-hidden min-h-screen flex flex-col">
<header class="sticky top-0 z-50 bg-surface-light dark:bg-surface-dark border-b border-[#f4ece7] dark:border-[#3a2e26] shadow-sm">
<div class="max-w-[1200px] mx-auto px-4 lg:px-8 py-3">
<div class="flex items-center justify-between gap-8">
<!-- Logo -->
<a class="flex items-center gap-2 text-primary hover:opacity-90 transition-opacity" href="{{ route('home') }}">
    <span class="material-symbols-outlined !text-[32px] fill-1">
        local_cafe
    </span>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
        Roasty
    </h1>
</a>
<div class="flex items-center gap-4">
<button class="md:hidden p-2 text-text-main dark:text-white">
<span class="material-symbols-outlined">search</span>
</button>
<!-- Admin Button - Only for Admin Users -->
<a id="admin-button" href="{{ route('admin.dashboard') }}" class="hidden flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 text-white rounded-lg hover:shadow-lg dark:hover:shadow-orange-900/50 transition-all text-sm font-semibold whitespace-nowrap">
<span class="material-symbols-outlined text-lg">admin_panel_settings</span>
<span class="hidden md:inline">Admin Panel</span>
</a>

<!-- User Navigation Icons -->
<div id="user-icons" class="flex items-center gap-3">
</div>
</div>
<div class="flex items-center gap-6 mt-4 text-sm font-medium overflow-x-auto no-scrollbar pb-1">
<a class="text-primary whitespace-nowrap border-b-2 border-primary pb-1" href="#">Rekomendasi</a>
<div class="ml-auto flex items-center gap-3">
<!-- Notification Icon - Only for Non-Admin Users -->
<button id="notification-button" class="p-2 text-text-main dark:text-white hover:bg-gray-50 dark:hover:bg-[#3a2e26] rounded-lg transition-colors relative hidden">
<span class="material-symbols-outlined">notifications</span>
<span id="notification-badge" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full hidden"></span>
</button>

<!-- Cart Icon - Only for Non-Admin Users -->
<button id="cart-button" class="p-2 text-text-main dark:text-white hover:bg-gray-50 dark:hover:bg-[#3a2e26] rounded-lg transition-colors relative hidden">
<span class="material-symbols-outlined">shopping_cart</span>
<span id="cart-badge" class="absolute top-1 right-1 w-5 h-5 bg-primary text-white text-xs rounded-full flex items-center justify-center text-[10px] font-bold hidden">0</span>
</button>

<!-- Profile Button - For All Users -->
<div class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-[#3a2e26] p-1.5 rounded-lg pr-3 transition-colors" id="profile-button">
<div class="size-8 rounded-full bg-cover bg-center border border-gray-200 dark:border-gray-700" id="profile-avatar" data-alt="User profile picture thumbnail">
</div>
<span class="text-sm font-semibold hidden lg:block" id="profile-name">Rina Barista</span>
</div>
</div>
</div>
</div>
</header>

<!-- Hero Section - Full Width -->
<section class="w-full h-auto lg:h-[360px] relative">
<div class="w-full h-full rounded-none lg:rounded-none overflow-hidden relative group shadow-sm">
<div class="w-full h-full bg-cover bg-center transition-transform duration-700 hover:scale-105" data-alt="Close up of roasted coffee beans in a scoop" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCkVm11dlfkKe174ltqyAh89707J5WuvIzZmo_DRltpCQ9PK05rqBungRAFosG0HSW31WvbN81CMyTV2I2atXWLdmKPvTl4Sw1uD_COAMSgRMnErPlO2gMvmrKMtmfmHpkmW5EPN2hdQAkGEJmPb_IqRp7hn4UL_yBNad_5xaNvWEE3Bac2IhJ1kfmOXtcPNeaurImGWkH1oKOsmJ7YGsecR6Xxd2ctSKWwApY8StTKPwhjXrVOeG6pSIj3M7Xxc5FcDSbwzhDoXg');">
<div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex flex-col justify-center px-8 lg:px-12 text-white">
<div class="max-w-[1200px] mx-auto w-full">
<span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded w-fit mb-3">PROMO SPESIAL</span>
<h2 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Waktunya Ngopi <br/>Lebih Hemat</h2>
<p class="mb-6 text-gray-200 max-w-md">Dapatkan diskon hingga 50% untuk pembelian mesin espresso pilihan.</p>
<button class="bg-primary hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-bold w-fit transition-colors">
</button>
</div>
</div>
</div>
<div class="absolute bottom-4 right-8 lg:right-12 flex gap-2">
<div class="w-2 h-2 rounded-full bg-white/50 hover:bg-white cursor-pointer transition-colors"></div>
<div class="w-8 h-2 rounded-full bg-primary cursor-pointer transition-colors"></div>
<div class="w-2 h-2 rounded-full bg-white/50 hover:bg-white cursor-pointer transition-colors"></div>
</div>
</div>
</section>

<!-- Rekomendasi Section -->
<section id="recommendations-section" class="w-full bg-white dark:bg-[#1a1410] py-12">
<div class="max-w-[1200px] mx-auto px-4 lg:px-8">
<!-- Search Bar -->
<div class="mb-8">
<div class="relative w-full group focus-within:ring-2 focus-within:ring-primary/50 rounded-lg transition-all duration-200">
<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-text-muted">search</span>
</div>
<input class="block w-full pl-10 pr-3 py-3 rounded-lg border-none bg-[#f4ece7] dark:bg-[#3a2e26] text-text-main dark:text-white placeholder-text-muted focus:ring-0 focus:bg-white dark:focus:bg-[#4a3b32] transition-colors" placeholder="Cari kopi, alat seduh, atau roaster..." type="text" id="main-search-input"/>
<div class="absolute inset-y-0 right-1.5 flex items-center">
<button id="main-search-btn" class="bg-primary hover:bg-orange-600 text-white p-1.5 rounded-md transition-colors">
<span class="material-symbols-outlined text-[20px]">arrow_forward</span>
</button>
</div>
</div>
</div>

<div class="mb-6 flex items-center gap-2">
<div class="w-1 h-6 bg-primary rounded-full"></div>
<h2 class="text-xl font-bold text-gray-900 dark:text-white">Rekomendasi Untukmu</h2>
</div>

<!-- Loading State -->
<div id="loading-indicator" class="flex justify-center items-center py-12">
<div class="flex flex-col items-center gap-3">
<div class="animate-spin">
<span class="material-symbols-outlined text-5xl text-primary fill-1">local_fire_department</span>
</div>
<p class="text-gray-500 dark:text-gray-400">Memuat produk...</p>
</div>
</div>

<!-- Error State -->
<div id="error-container" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
<div class="flex items-start gap-3">
<span class="material-symbols-outlined text-red-600 dark:text-red-400 flex-shrink-0 mt-1">error</span>
<div>
<h3 class="font-semibold text-red-800 dark:text-red-300">Gagal Memuat Produk</h3>
<p id="error-message" class="text-red-700 dark:text-red-400 text-sm mt-1">Terjadi kesalahan saat mengambil data produk.</p>
</div>
</div>
</div>

<!-- Empty State -->
<div id="empty-state" class="hidden text-center py-12">
<span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-700 block mb-4 fill-1">shopping_bag</span>
<h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400 mb-2">Tidak ada produk tersedia</h3>
<p class="text-gray-500 dark:text-gray-500 text-sm">Silakan coba lagi nanti atau cari produk lain.</p>
</div>

<!-- Products Grid -->
<div id="products-grid" class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
</div>

<!-- Pagination -->
<div id="pagination-container" class="flex justify-center items-center gap-2 mt-12 flex-wrap">
</div>
</div>
</section>

<footer class="bg-white dark:bg-[#221710] border-t border-gray-200 dark:border-[#3a2e26] mt-10 pt-12 pb-6">
<div class="max-w-[1200px] mx-auto px-4 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
<div class="col-span-1 md:col-span-1">
<div class="flex items-center gap-2 text-primary mb-4">
<span class="material-symbols-outlined text-3xl filled">local_fire_department</span>
<h2 class="text-xl font-bold">Roasty</h2>
</div>
<p class="text-sm text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">
                        Platform marketplace nomor satu untuk kebutuhan kopi Anda. Dari biji pilihan hingga mesin profesional.
                    </p>
<div class="flex gap-3">
<div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-primary hover:text-white transition-colors cursor-pointer">
<span class="text-xs font-bold">FB</span>
</div>
<div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-primary hover:text-white transition-colors cursor-pointer">
<span class="text-xs font-bold">IG</span>
</div>
<div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-primary hover:text-white transition-colors cursor-pointer">
<span class="text-xs font-bold">TW</span>
</div>
</div>
</div>
<div>
<h3 class="font-bold text-text-main dark:text-white mb-4">Tentang Roasty</h3>
<ul class="space-y-3 text-sm text-gray-500 dark:text-gray-400">
<li><a class="hover:text-primary" href="#">Tentang Kami</a></li>
<li><a class="hover:text-primary" href="#">Karir</a></li>
<li><a class="hover:text-primary" href="#">Blog</a></li>
<li><a class="hover:text-primary" href="#">Mitra Seller</a></li>
</ul>
</div>
<div>
<h3 class="font-bold text-text-main dark:text-white mb-4">Bantuan</h3>
<ul class="space-y-3 text-sm text-gray-500 dark:text-gray-400">
<li><a class="hover:text-primary" href="#">Pusat Bantuan</a></li>
<li><a class="hover:text-primary" href="#">Syarat &amp; Ketentuan</a></li>
<li><a class="hover:text-primary" href="#">Kebijakan Privasi</a></li>
<li><a class="hover:text-primary" href="#">Komplain Pesanan</a></li>
</ul>
</div>
<div>
<h3 class="font-bold text-text-main dark:text-white mb-4">Download App</h3>
<div class="flex flex-col gap-3">
<button class="bg-black text-white px-4 py-2 rounded-lg flex items-center gap-2 w-fit hover:bg-gray-800 transition-colors">
<span class="material-symbols-outlined">android</span>
<div class="flex flex-col items-start leading-none">
<span class="text-[10px] uppercase">Get it on</span>
<span class="text-sm font-bold">Google Play</span>
</div>
</button>
<button class="bg-black text-white px-4 py-2 rounded-lg flex items-center gap-2 w-fit hover:bg-gray-800 transition-colors">
<span class="material-symbols-outlined">phone_iphone</span>
<div class="flex flex-col items-start leading-none">
<span class="text-[10px] uppercase">Download on the</span>
<span class="text-sm font-bold">App Store</span>
</div>
</button>
</div>
</div>
</div>
<div class="border-t border-gray-200 dark:border-gray-800 pt-6 text-center text-sm text-gray-500">
                © 2024 Roasty Indonesia. All rights reserved.
            </div>
</div>
</footer>

<script src="{{ asset('config.js') }}"></script>
<script src="{{ asset('navbar-helper.js') }}"></script>
<script>
const API_URL = CONFIG.API_BASE_URL;
let allProducts = []; // Store products globally
let filteredProducts = []; // Store filtered products for search
let currentPage = 1;
const ITEMS_PER_PAGE = 10;

document.addEventListener('DOMContentLoaded', function() {
    // Check authentication
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = "{{ route('login') }}";
        return;
    }

    // Load user profile from localStorage
    const userStr = localStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : {};

    console.log('User data loaded:', user);

    // Update profile display
    if (user && user.name) {
        const nameEl = document.getElementById('profile-name');
        if (nameEl) {
            nameEl.textContent = user.name;
            console.log('Profile name updated:', user.name);
        }
    }

    if (user && user.photo) {
        const avatarEl = document.getElementById('profile-avatar');
        if (avatarEl) {
            avatarEl.style.backgroundImage = `url('${user.photo}')`;
            console.log('Profile avatar updated:', user.photo);
        }
    }

    // Setup profile button click
    const profileBtn = document.getElementById('profile-button');
    if (profileBtn) {
        profileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('profil') }}";
        });
    }

    console.log('DOM Content Loaded - Starting to load products...');
    console.log('API_URL:', API_URL);

    // Add small delay to ensure DOM is fully ready
    setTimeout(function() {
        loadProducts(); // Load products from API
        setupAllButtons();
    }, 100);
});

// Load products from API
async function loadProducts() {
    try {
        const token = localStorage.getItem('token');

        console.log('🔄 Starting loadProducts...');
        console.log('Token:', token ? 'exists' : 'missing');
        console.log('API URL:', API_URL);

        // Show loading indicator
        const loadingIndicator = document.getElementById('loading-indicator');
        const errorContainer = document.getElementById('error-container');
        const emptyState = document.getElementById('empty-state');

        console.log('Loading indicator found:', !!loadingIndicator);
        console.log('Error container found:', !!errorContainer);
        console.log('Empty state found:', !!emptyState);

        if (loadingIndicator) loadingIndicator.classList.remove('hidden');
        if (errorContainer) errorContainer.classList.add('hidden');
        if (emptyState) emptyState.classList.add('hidden');

        const fetchUrl = `${API_URL}/products?limit=1000`;
        console.log('Fetching from:', fetchUrl);

        const res = await fetch(fetchUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        });

        console.log('Response status:', res.status);
        console.log('Response ok:', res.ok);

        if (!res.ok) {
            let errorMsg = `HTTP ${res.status}`;
            try {
                const errorData = await res.json();
                if (errorData.message) errorMsg = errorData.message;
                if (errorData.error?.details) errorMsg += ` - ${errorData.error.details}`;
            } catch (e) {
                // Ignore parsing error
            }
            throw new Error(errorMsg);
        }

        const data = await res.json();
        console.log('✅ API Response received:', data);

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

        if (!productArray || productArray.length === 0) {
            console.warn('⚠️ No products returned from API');
        }

        allProducts = productArray;
        console.log('✅ Parsed products:', allProducts.length, 'items');
        console.log('Sample products:', allProducts.slice(0, 2).map(p => ({ id: p.id, name: p.name, image_urls: p.image_urls })));

        // Hide loading indicator
        if (loadingIndicator) loadingIndicator.classList.add('hidden');

        // Render products ke halaman
        console.log('Calling renderProducts...');
        renderProducts(allProducts);

        // Setup event listeners setelah render selesai
        requestAnimationFrame(() => {
            console.log('Setting up product cards...');
            setupProductCards();
        });

    } catch (err) {
        console.error('❌ Error loading products:', err);
        console.error('Error stack:', err.stack);

        // Show error container
        const loadingIndicator = document.getElementById('loading-indicator');
        const errorContainer = document.getElementById('error-container');
        const emptyState = document.getElementById('empty-state');

        if (loadingIndicator) loadingIndicator.classList.add('hidden');
        if (errorContainer) {
            errorContainer.classList.remove('hidden');
            const errorMsg = document.getElementById('error-message');
            if (errorMsg) {
                errorMsg.textContent = err.message || 'Terjadi kesalahan saat mengambil data produk.';
            }
        }
        if (emptyState) emptyState.classList.add('hidden');
    }
}

// Render products to recommendation section dengan pagination
function renderProducts(products, pageNum = 1) {
    const productGrid = document.getElementById('products-grid');
    const emptyState = document.getElementById('empty-state');

    if (!productGrid) {
        console.error('❌ Product grid not found');
        return;
    }

    currentPage = pageNum;

    // ALWAYS hide empty state saat render products
    if (emptyState) emptyState.classList.add('hidden');

    if (products && products.length > 0) {
        productGrid.innerHTML = '';

        // Calculate pagination
        const startIdx = (pageNum - 1) * ITEMS_PER_PAGE;
        const endIdx = startIdx + ITEMS_PER_PAGE;
        const paginatedProducts = products.slice(startIdx, endIdx);

        paginatedProducts.forEach((product, index) => {
            const card = document.createElement('div');
            card.className = 'bg-surface-light dark:bg-surface-dark border border-gray-100 dark:border-gray-800 rounded-xl overflow-hidden hover:shadow-lg transition-all cursor-pointer flex flex-col product-card-entering';
            card.style.animationDelay = `${index * 0.05}s`;
            card.setAttribute('data-product-id', String(product.id));

            const price = parseInt(product.price);
            const originalPrice = Math.floor(price * 1.2);

            // Normalize image URL
            let imageUrl = '';
            
            // Helper function (same as in admin dashboard)
            const normalizeImageUrl = (url) => {
                if (!url) return '';
                url = String(url).trim();
                if (url.startsWith('data:')) return url;
                if (url.startsWith('http://') || url.startsWith('https://')) return url;
                
                // If it's a relative path from /storage, clean it and prepend assets URL
                if (url.startsWith('/storage/') || url.startsWith('storage/')) {
                    const cleanUrl = url.replace(/^\/?storage\//, '');
                    return `${CONFIG.assets}/${cleanUrl}`;
                }
                
                // Otherwise assume it's a relative path and prepend assets URL
                return `${CONFIG.assets}/${url}`;
            };

            if (product.image_urls && Array.isArray(product.image_urls) && product.image_urls.length > 0) {
                imageUrl = normalizeImageUrl(product.image_urls[0]);
                console.log(`[${product.name}] Final URL:`, imageUrl);
            } else if (product.image_url) {
                 imageUrl = normalizeImageUrl(product.image_url);
            }

            const fallbackImage = "/images/lakopi.jpg";

            card.innerHTML = `
                <div class="relative w-full aspect-square overflow-hidden bg-gray-100 dark:bg-gray-800">
                    <img src="${imageUrl || fallbackImage}" alt="${product.name}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105" loading="lazy" onerror="this.onerror=null;this.src='${fallbackImage}'">
                </div>
                <div class="p-3 flex flex-col flex-1 justify-between">
                    <h3 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2">
                        ${product.name}
                    </h3>
                    <div>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="font-bold text-primary">Rp ${price.toLocaleString('id-ID')}</span>
                            <span class="text-xs text-gray-400 line-through">Rp ${originalPrice.toLocaleString('id-ID')}</span>
                        </div>

        
                    </div>
                </div>
            `;

            productGrid.appendChild(card);

            // If image exists, log when it loads or fails
            if (imageUrl) {
                const img = card.querySelector('img');
                if (img) {
                    img.onload = () => console.log(`✅ Loaded: ${product.name}`);
                    img.onerror = () => console.error(`❌ Failed: ${product.name}`);
                }
            }
        });

        console.log('✅ Rendered page', pageNum, 'with', paginatedProducts.length, 'products');

        // Render pagination buttons
        renderPagination(products);
    } else {
        if (emptyState) emptyState.classList.remove('hidden');
        console.log('⚠️ No products to display');
        // Clear pagination jika kosong
        const paginationContainer = document.getElementById('pagination-container');
        if (paginationContainer) paginationContainer.innerHTML = '';
    }
}

// Render pagination buttons
function renderPagination(products) {
    console.log('🎯 renderPagination called with', products.length, 'products');

    const paginationContainer = document.getElementById('pagination-container');
    if (!paginationContainer) {
        console.error('❌ Pagination container not found!');
        return;
    }

    console.log('✓ Pagination container found');
    paginationContainer.innerHTML = '';

    const totalPages = Math.ceil(products.length / ITEMS_PER_PAGE);
    console.log('📄 Total pages calculated:', totalPages, 'from', products.length, 'products, ITEMS_PER_PAGE:', ITEMS_PER_PAGE);

    // Show pagination even if only 1 page (untuk testing atau UX consistency)
    if (totalPages < 1) {
        console.log('⚠️ No pages to show');
        return;
    }

    // Create pagination buttons
    console.log('📌 Creating buttons for pages 1 to', totalPages);
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `px-3 py-2 rounded-lg font-medium transition-colors ${
            i === currentPage
                ? 'bg-primary text-white'
                : 'bg-gray-100 dark:bg-[#3a2e26] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#4a3b32]'
        }`;

        btn.addEventListener('click', () => {
            console.log('🖱️ Pagination button clicked:', i);
            console.log('filteredProducts.length:', filteredProducts.length, 'allProducts.length:', allProducts.length);
            const productsToShow = filteredProducts.length > 0 ? filteredProducts : allProducts;
            console.log('Using products:', productsToShow.length, 'items');
            renderProducts(productsToShow, i);
            // Scroll ke top
            const section = document.querySelector('section#recommendations-section');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
            setupProductCards();
        });

        paginationContainer.appendChild(btn);
        console.log('✓ Added button for page', i);
    }

    console.log('✅ Pagination rendered with', totalPages, 'buttons');
    console.log('Pagination container innerHTML:', paginationContainer.innerHTML.substring(0, 200));
}

// Setup product card click handlers
function setupProductCards() {
    const productGrid = document.getElementById('products-grid');
    if (!productGrid) return;

    const productCards = productGrid.querySelectorAll('div[data-product-id]');

    console.log('setupProductCards: Found', productCards.length, 'cards');

    productCards.forEach((card, index) => {
        const productId = card.getAttribute('data-product-id');
        console.log(`Card ${index}: data-product-id = "${productId}"`);

        if (!productId) {
            console.warn(`Card ${index} has no data-product-id, skipping...`);
            return;
        }

        card.addEventListener('click', function(e) {
            if (e.target.closest('button')) {
                return;
            }

            e.preventDefault();

            const clickedProductId = this.getAttribute('data-product-id');
            console.log('Product clicked, ID:', clickedProductId);

            if (!clickedProductId) {
                alert('Produk tidak ditemukan');
                return;
            }

            sessionStorage.setItem('selectedProductId', clickedProductId);

            const product = allProducts.find(p => String(p.id) === String(clickedProductId));
            if (product) {
                sessionStorage.setItem('currentProduct', JSON.stringify(product));
                console.log('Stored product data:', product);
            }

            window.location.href = "{{ route('produk.detail') }}";
        });
    });
}


function setupAllButtons() {
    // Check if user is authenticated
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = "{{ route('login') }}";
        return;
    }

    // Update user profile display
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.name) {
        const userNameElement = document.querySelector('div.flex.items-center.gap-3 span.hidden');
        if (userNameElement) {
            userNameElement.textContent = user.name;
        }
    }

    try {
        // ===== HEADER BUTTONS =====
        // Logo Button
        const logo = document.querySelector('header a.flex.items-center.gap-2[class*="text-primary"]');
        if (logo) {
            logo.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route('home') }}";
            });
        }

        // Search Bar Container (new location - above recommendations)
        const mainSearchInput = document.getElementById('main-search-input');
        const mainSearchBtn = document.getElementById('main-search-btn');

        if (mainSearchBtn) {
            mainSearchBtn.addEventListener('click', handleSearch);
        }

        if (mainSearchInput) {
            mainSearchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearch();
                }
            });
        }

        // Mobile Search Button
        const mobileSearch = document.querySelector('button.md\\:hidden');
        if (mobileSearch && mobileSearch.querySelector('.material-symbols-outlined')?.textContent === 'search') {
            mobileSearch.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route('produk.index') }}";
            });
        }

        // Cart, Notifications, Mail Buttons (in header)
        const headerActionsDiv = document.querySelector('div.flex.items-center.gap-2.border-r');
        if (headerActionsDiv) {
            const buttons = headerActionsDiv.querySelectorAll('button.relative');

            buttons.forEach(btn => {
                const icon = btn.querySelector('.material-symbols-outlined');
                if (icon) {
                    const iconText = icon.textContent.trim();

                    if (iconText === 'shopping_cart') {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            const token = localStorage.getItem('token');
                            if (!token) {
                                window.location.href = "{{ route('login') }}";
                            } else {
                                window.location.href = "{{ route('keranjang') }}";
                            }
                        });
                    } else if (iconText === 'notifications') {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            alert('Anda tidak memiliki notifikasi baru');
                        });
                    } else if (iconText === 'mail') {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            alert('Tidak ada pesan baru');
                        });
                    }
                }
            });
        }

        // Profile Button (User Info Area)
        const profileBtn = document.querySelector('div.flex.items-center.gap-3.cursor-pointer');
        if (profileBtn) {
            profileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const token = localStorage.getItem('token');
                if (!token) {
                    window.location.href = "{{ route('login') }}";
                } else {
                    window.location.href = "{{ route('profil') }}";
                }
            });
        }

        // ===== NAVIGATION CATEGORY LINKS =====
        const navLinks = document.querySelectorAll('div.flex.items-center.gap-4.mt-4 a[href="#"]');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const text = link.textContent.trim();
                if (text && text !== 'Rekomendasi') {
                    sessionStorage.setItem('selectedCategory', text);
                }
                window.location.href = "{{ route('produk.index') }}";
            });
        });

        // ===== PROMO BANNER BUTTON =====
        const promoBanner = document.querySelector('div.lg\\:col-span-8');
        if (promoBanner) {
            const promoBtn = promoBanner.querySelector('button.bg-primary.hover\\:bg-orange-600');
            if (promoBtn) {
                promoBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('produk.index') }}";
                });
            }
        }

        // ===== CATEGORY PILLS =====
        const categorySection = document.querySelector('div.grid.grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-6');
        if (categorySection) {
            const pills = categorySection.querySelectorAll('div.flex.items-center.gap-3.p-4');
            pills.forEach(pill => {
                pill.style.cursor = 'pointer';
                pill.addEventListener('click', function(e) {
                    e.preventDefault();
                    const categoryName = pill.querySelector('span.font-semibold')?.textContent.trim() || '';
                    if (categoryName) {
                        sessionStorage.setItem('selectedCategory', categoryName);
                    }
                    window.location.href = "{{ route('produk.index') }}";
                });
            });
        }

        // ===== DISCOUNT SECTION =====
        const discountSection = document.querySelector('section.bg-surface-light.dark\\:bg-surface-dark.rounded-2xl');
        if (discountSection) {
            // Lihat Semua link
            const lihatSemuaLink = discountSection.querySelector('a.text-primary.font-bold');
            if (lihatSemuaLink) {
                lihatSemuaLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = "{{ route('produk.index') }}";
                });
            }

            // Product cards in discount section
            const discountCards = discountSection.querySelectorAll('div.min-w-\\[180px\\].md\\:min-w-\\[200px\\]');
            discountCards.forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productName = card.querySelector('h3')?.textContent.trim() || 'Unknown';
                    sessionStorage.setItem('selectedProductName', productName);
                    window.location.href = "{{ route('produk.detail') }}";
                });
            });

            // Arrow buttons in discount section
            const arrowBtns = discountSection.querySelectorAll('button.size-8.rounded-full');
            arrowBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Arrow navigation clicked');
                });
            });
        }

        // ===== RECOMMENDATION SECTION =====
        const recSection = document.querySelector('section:last-of-type');
        if (recSection && recSection.querySelector('h2')?.textContent.includes('Rekomendasi')) {
            // Product cards in recommendation section
            const recCards = recSection.querySelectorAll('div.bg-surface-light.dark\\:bg-surface-dark.border.rounded-xl.overflow-hidden');
            recCards.forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('button')) {
                        e.preventDefault();
                        const productName = card.querySelector('h3')?.textContent.trim() || 'Unknown';
                        sessionStorage.setItem('selectedProductName', productName);
                        window.location.href = "{{ route('produk.detail') }}";
                    }
                });
            });

            // Add to cart buttons in recommendation
            const addToCartBtns = recSection.querySelectorAll('button.bg-white.dark\\:bg-\\[\\#3a2e26\\].p-2.rounded-full');
            addToCartBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const token = localStorage.getItem('token');
                    if (!token) {
                        window.location.href = "{{ route('login') }}";
                    } else {
                        // Get product name from nearest card
                        const card = btn.closest('div.bg-surface-light');
                        const productName = card?.querySelector('h3')?.textContent.trim() || 'Unknown';
                        // Add to cart and show feedback
                        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                        const existingItem = cart.find(item => item.name === productName);
                        if (existingItem) {
                            existingItem.quantity += 1;
                        } else {
                            cart.push({ name: productName, quantity: 1, price: 0 });
                        }
                        localStorage.setItem('cart', JSON.stringify(cart));
                        alert(`${productName} ditambahkan ke keranjang!`);
                    }
                });
            });

            // Load More button
            const loadMoreBtn = recSection.querySelector('button.border.border-primary');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Load more clicked');
                });
            }
        }

        // ===== FOOTER LINKS =====
        const footerLinks = document.querySelectorAll('footer a[href="#"]');
        footerLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = link.textContent.trim();
                console.log('Footer link clicked:', href);
            });
        });

        // ===== SOCIAL BUTTONS =====
        const socialBtns = document.querySelectorAll('div.w-8.h-8.rounded-full.bg-gray-100');
        socialBtns.forEach(btn => {
            btn.style.cursor = 'pointer';
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Social button clicked');
            });
        });

    } catch (error) {
        console.error('Error setting up buttons:', error);
    }
}

function handleSearch() {
    const searchInput = document.getElementById('main-search-input') || document.querySelector('input[placeholder*="Cari"]');
    const query = searchInput ? searchInput.value.trim().toLowerCase() : '';

    if (!query) {
        // Jika query kosong, tampilkan semua produk
        filteredProducts = allProducts;
    } else {
        // Filter produk berdasarkan nama atau kategori
        filteredProducts = allProducts.filter(product => {
            const name = (product.name || '').toLowerCase();
            const category = (product.category || '').toLowerCase();
            const description = (product.description || '').toLowerCase();
            return name.includes(query) || category.includes(query) || description.includes(query);
        });
    }

    console.log('Search query:', query);
    console.log('Filtered products:', filteredProducts.length);

    // Reset ke page 1 dan render hasil search
    currentPage = 1;
    renderProducts(filteredProducts, 1);
    setupProductCards();

    // Scroll ke atas
    document.querySelector('section#recommendations-section').scrollIntoView({ behavior: 'smooth' });
}
</script>

</body></html>
