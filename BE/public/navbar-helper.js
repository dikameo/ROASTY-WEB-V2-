/**
 * Navbar Helper - Manages role-based navbar display and cart badge updates
 * Import this file in all pages to maintain consistent navbar behavior
 */

/**
 * Initialize navbar based on user role
 * Shows admin panel button only for admin users
 * Shows cart and notification icons only for regular users
 */
function initializeNavbar() {
    const token = localStorage.getItem('token');
    const userStr = localStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : null;

    if (!token) {
        console.log('No token found, user not authenticated');
        return;
    }

    const adminBtn = document.getElementById('admin-button');
    const notificationBtn = document.getElementById('notification-button');
    const cartBtn = document.getElementById('cart-button');
    const profileBtn = document.getElementById('profile-button');

    console.log('Initializing navbar for user:', user?.name, 'Role:', user?.role);

    // Check if user is admin
    if (user && user.role === 'admin') {
        // Show admin button
        if (adminBtn) {
            adminBtn.classList.remove('hidden');
            adminBtn.style.display = 'flex';
            console.log('✅ Admin button shown');
        }
        // Hide user navigation icons
        if (notificationBtn) notificationBtn.classList.add('hidden');
        if (cartBtn) cartBtn.classList.add('hidden');
    } else {
        // Hide admin button
        if (adminBtn) {
            adminBtn.classList.add('hidden');
            console.log('❌ Admin button hidden');
        }
        // Show user navigation icons
        if (notificationBtn) {
            notificationBtn.classList.remove('hidden');
            notificationBtn.style.display = 'flex';
        }
        if (cartBtn) {
            cartBtn.classList.remove('hidden');
            cartBtn.style.display = 'flex';
        }
    }

    // Setup profile button
    if (profileBtn) {
        profileBtn.addEventListener('click', handleProfileClick);
    }

    // Setup cart button for regular users
    if (cartBtn && user && user.role !== 'admin') {
        cartBtn.addEventListener('click', () => {
            window.location.href = 'halaman.keranjang.belanja.html';
        });
    }

    // Setup notification button for regular users
    if (notificationBtn && user && user.role !== 'admin') {
        notificationBtn.addEventListener('click', handleNotificationClick);
    }

    // Initial cart badge update
    updateCartBadge();

    // Listen for storage changes
    window.addEventListener('storage', updateCartBadge);

    // Listen for custom cart update event
    window.addEventListener('cartUpdated', updateCartBadge);
}

/**
 * Update cart badge count
 * Shows the number of items in cart in the badge
 */
function updateCartBadge() {
    const cartBadge = document.getElementById('cart-badge');
    if (!cartBadge) return;

    try {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);

        if (totalItems > 0) {
            cartBadge.classList.remove('hidden');
            cartBadge.textContent = totalItems;
            console.log(`Cart badge updated: ${totalItems} items`);
        } else {
            cartBadge.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error updating cart badge:', error);
    }
}

/**
 * Update notification badge
 * You can customize this based on your notification logic
 */
function updateNotificationBadge(hasNotifications = false) {
    const notificationBadge = document.getElementById('notification-badge');
    if (!notificationBadge) return;

    if (hasNotifications) {
        notificationBadge.classList.remove('hidden');
    } else {
        notificationBadge.classList.add('hidden');
    }
}

/**
 * Handle profile button click
 * Shows profile menu or navigates to profile page
 */
function handleProfileClick(e) {
    e.preventDefault();
    console.log('Profile button clicked');
    // You can add profile menu functionality here
    // For now, just navigate to profile
    window.location.href = 'halaman.profil.html';
}

/**
 * Handle notification button click
 */
function handleNotificationClick(e) {
    e.preventDefault();
    console.log('Notification button clicked');
    // Add your notification logic here
    // You can show a notification panel, navigate to a notifications page, etc.
}

/**
 * Dispatch custom event when cart is updated
 * Call this function whenever cart is modified
 */
function dispatchCartUpdateEvent() {
    const event = new CustomEvent('cartUpdated', {
        detail: { timestamp: Date.now() }
    });
    window.dispatchEvent(event);
    console.log('Cart update event dispatched');
}

/**
 * Add item to cart and update badge
 * @param {number} productId - Product ID
 * @param {number} quantity - Quantity to add (default 1)
 * @returns {boolean} - Whether the item was added successfully
 */
function addToCart(productId, quantity = 1) {
    try {
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        const existingItem = cart.find(item => item.id == productId);
        if (existingItem) {
            existingItem.quantity += quantity;
            console.log(`Updated cart item ${productId}, new quantity: ${existingItem.quantity}`);
        } else {
            cart.push({
                id: productId,
                quantity: quantity
            });
            console.log(`Added new item ${productId} to cart with quantity: ${quantity}`);
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        dispatchCartUpdateEvent();
        return true;
    } catch (error) {
        console.error('Error adding item to cart:', error);
        return false;
    }
}

/**
 * Clear cart
 */
function clearCart() {
    try {
        localStorage.setItem('cart', JSON.stringify([]));
        dispatchCartUpdateEvent();
        console.log('Cart cleared');
        return true;
    } catch (error) {
        console.error('Error clearing cart:', error);
        return false;
    }
}

/**
 * Get cart items
 * @returns {Array} - Array of cart items
 */
function getCartItems() {
    try {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    } catch (error) {
        console.error('Error getting cart items:', error);
        return [];
    }
}

/**
 * Get total items in cart
 * @returns {number} - Total number of items
 */
function getCartTotal() {
    const cart = getCartItems();
    return cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
}

// Initialize navbar when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeNavbar);
} else {
    initializeNavbar();
}
