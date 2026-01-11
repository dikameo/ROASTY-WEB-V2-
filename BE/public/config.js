const CONFIG = {
    // Dynamic API URL based on current origin (handles localhost vs 127.0.0.1)
    API_BASE_URL: `${window.location.origin}/api`,

    // Supabase Storage URL for product images
    assets: 'https://fiyodlfgfbcnatebudut.supabase.co/storage/v1/object/public/product-images',

    // App info
    appName: 'Roasty Coffee',
    version: '2.0.0'
};
