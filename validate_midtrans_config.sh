#!/bin/bash

# MIDTRANS CONFIGURATION VALIDATION
# ==================================
# Script ini mengecek konfigurasi Midtrans untuk mencegah error & looping

echo "🔍 VALIDASI KONFIGURASI MIDTRANS"
echo "=================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

ERRORS=0
WARNINGS=0

# Check 1: .env file exists
echo -e "${BLUE}[CHECK 1]${NC} File .env ada?"
if [ -f "BE/.env" ]; then
    echo -e "${GREEN}✓ File .env ditemukan${NC}"
else
    echo -e "${RED}✗ File .env TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 2: MIDTRANS_IS_PRODUCTION
echo ""
echo -e "${BLUE}[CHECK 2]${NC} MIDTRANS_IS_PRODUCTION setting?"
if grep -q "MIDTRANS_IS_PRODUCTION=false" BE/.env; then
    echo -e "${GREEN}✓ SANDBOX MODE (false) - BENAR${NC}"
elif grep -q "MIDTRANS_IS_PRODUCTION=true" BE/.env; then
    echo -e "${YELLOW}⚠ PRODUCTION MODE (true) - Pastikan credentials benar!${NC}"
    WARNINGS=$((WARNINGS+1))
elif grep -q "MIDTRANS_IS_PRODUCTION" BE/.env; then
    PROD_VALUE=$(grep "MIDTRANS_IS_PRODUCTION" BE/.env | cut -d'=' -f2)
    echo -e "${YELLOW}⚠ Nilai tidak standard: '$PROD_VALUE'${NC}"
    WARNINGS=$((WARNINGS+1))
else
    echo -e "${RED}✗ MIDTRANS_IS_PRODUCTION TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 3: Server Key exists
echo ""
echo -e "${BLUE}[CHECK 3]${NC} MIDTRANS_SERVER_KEY ada?"
if grep -q "MIDTRANS_SERVER_KEY=Mid-server-" BE/.env; then
    KEY=$(grep "MIDTRANS_SERVER_KEY" BE/.env | cut -d'=' -f2 | cut -c1-25)
    echo -e "${GREEN}✓ Server Key ditemukan: ${KEY}...${NC}"
elif grep -q "MIDTRANS_SERVER_KEY=" BE/.env; then
    echo -e "${RED}✗ Server Key tidak valid (bukan format Midtrans)${NC}"
    ERRORS=$((ERRORS+1))
else
    echo -e "${RED}✗ MIDTRANS_SERVER_KEY TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 4: Client Key exists
echo ""
echo -e "${BLUE}[CHECK 4]${NC} MIDTRANS_CLIENT_KEY ada?"
if grep -q "MIDTRANS_CLIENT_KEY=Mid-client-" BE/.env; then
    KEY=$(grep "MIDTRANS_CLIENT_KEY" BE/.env | cut -d'=' -f2 | cut -c1-25)
    echo -e "${GREEN}✓ Client Key ditemukan: ${KEY}...${NC}"
elif grep -q "MIDTRANS_CLIENT_KEY=" BE/.env; then
    echo -e "${RED}✗ Client Key tidak valid (bukan format Midtrans)${NC}"
    ERRORS=$((ERRORS+1))
else
    echo -e "${RED}✗ MIDTRANS_CLIENT_KEY TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 5: Merchant ID exists
echo ""
echo -e "${BLUE}[CHECK 5]${NC} MIDTRANS_MERCHANT_ID ada?"
if grep -q "MIDTRANS_MERCHANT_ID=" BE/.env; then
    MERCHANT=$(grep "MIDTRANS_MERCHANT_ID" BE/.env | cut -d'=' -f2)
    echo -e "${GREEN}✓ Merchant ID: ${MERCHANT}${NC}"
else
    echo -e "${RED}✗ MIDTRANS_MERCHANT_ID TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 6: OrderController exists
echo ""
echo -e "${BLUE}[CHECK 6]${NC} OrderController ada?"
if [ -f "BE/app/Http/Controllers/Api/OrderController.php" ]; then
    echo -e "${GREEN}✓ File ditemukan${NC}"
    
    # Check if getMidtransConfig method exists
    if grep -q "public function getMidtransConfig" BE/app/Http/Controllers/Api/OrderController.php; then
        echo -e "${GREEN}  ✓ Method getMidtransConfig() exists${NC}"
    else
        echo -e "${RED}  ✗ Method getMidtransConfig() MISSING!${NC}"
        ERRORS=$((ERRORS+1))
    fi
    
    # Check if handleMidtransWebhook method exists
    if grep -q "public function handleMidtransWebhook" BE/app/Http/Controllers/Api/OrderController.php; then
        echo -e "${GREEN}  ✓ Method handleMidtransWebhook() exists${NC}"
    else
        echo -e "${RED}  ✗ Method handleMidtransWebhook() MISSING!${NC}"
        ERRORS=$((ERRORS+1))
    fi
    
    # Check if store method exists
    if grep -q "public function store" BE/app/Http/Controllers/Api/OrderController.php; then
        echo -e "${GREEN}  ✓ Method store() exists${NC}"
    else
        echo -e "${RED}  ✗ Method store() MISSING!${NC}"
        ERRORS=$((ERRORS+1))
    fi
else
    echo -e "${RED}✗ OrderController TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 7: API routes
echo ""
echo -e "${BLUE}[CHECK 7]${NC} Routes API sudah configured?"
if [ -f "BE/routes/api.php" ]; then
    echo -e "${GREEN}✓ File routes/api.php ditemukan${NC}"
    
    # Check midtrans-config route
    if grep -q "midtrans-config" BE/routes/api.php; then
        echo -e "${GREEN}  ✓ Route /midtrans-config exists${NC}"
    else
        echo -e "${RED}  ✗ Route /midtrans-config MISSING!${NC}"
        ERRORS=$((ERRORS+1))
    fi
    
    # Check midtrans-webhook route
    if grep -q "midtrans-webhook" BE/routes/api.php; then
        echo -e "${GREEN}  ✓ Route /midtrans-webhook exists${NC}"
    else
        echo -e "${RED}  ✗ Route /midtrans-webhook MISSING!${NC}"
        ERRORS=$((ERRORS+1))
    fi
else
    echo -e "${RED}✗ File routes/api.php TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 8: Frontend payment methods
echo ""
echo -e "${BLUE}[CHECK 8]${NC} Frontend payment methods configured?"
if [ -f "FE/halaman.pembayaran.html" ]; then
    echo -e "${GREEN}✓ File halaman.pembayaran.html ditemukan${NC}"
    
    # Check payment method IDs
    declare -a methods=("gopay" "bca_va" "bni_va" "mandiri_va" "permata_va" "credit_card")
    for method in "${methods[@]}"; do
        if grep -q "id: '$method'" FE/halaman.pembayaran.html || grep -q "id: \"$method\"" FE/halaman.pembayaran.html; then
            echo -e "${GREEN}  ✓ Payment method '$method' configured${NC}"
        else
            echo -e "${YELLOW}  ⚠ Payment method '$method' might not be in correct format${NC}"
            WARNINGS=$((WARNINGS+1))
        fi
    done
else
    echo -e "${RED}✗ File halaman.pembayaran.html TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 9: Order Model
echo ""
echo -e "${BLUE}[CHECK 9]${NC} Order Model?"
if [ -f "BE/app/Models/Order.php" ]; then
    echo -e "${GREEN}✓ File Order.php ditemukan${NC}"
    
    if grep -q "protected \$keyType = 'string'" BE/app/Models/Order.php; then
        echo -e "${GREEN}  ✓ Primary key type = string (untuk text PK)${NC}"
    else
        echo -e "${YELLOW}  ⚠ Primary key type mungkin perlu dicheck${NC}"
        WARNINGS=$((WARNINGS+1))
    fi
else
    echo -e "${RED}✗ File Order.php TIDAK DITEMUKAN!${NC}"
    ERRORS=$((ERRORS+1))
fi

# Check 10: Config file
echo ""
echo -e "${BLUE}[CHECK 10]${NC} Config/midtrans.php?"
if [ -f "BE/config/midtrans.php" ]; then
    echo -e "${GREEN}✓ File config/midtrans.php ditemukan${NC}"
else
    echo -e "${YELLOW}⚠ File config/midtrans.php TIDAK DITEMUKAN (opsional, bisa langsung dari .env)${NC}"
fi

# Summary
echo ""
echo "=================================="
echo -e "${BLUE}SUMMARY${NC}"
echo "=================================="
echo -e "Errors:   ${RED}${ERRORS}${NC}"
echo -e "Warnings: ${YELLOW}${WARNINGS}${NC}"
echo ""

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✅ SEMUA KONFIGURASI BENAR!${NC}"
    echo ""
    echo "Sistem siap untuk:"
    echo "1. Login user"
    echo "2. Tambah produk ke keranjang"
    echo "3. Checkout & pilih payment method"
    echo "4. Pembayaran melalui Midtrans Snap"
    echo ""
    echo "🎯 LANGKAH SELANJUTNYA:"
    echo "1. Jalankan: php artisan serve"
    echo "2. Buka: http://localhost:8000/FE/halaman.pembayaran.html"
    echo "3. Check browser console (F12) untuk debug logs"
    echo "4. Check backend logs: BE/storage/logs/laravel.log"
    exit 0
else
    echo -e "${RED}❌ ADA ${ERRORS} ERROR YANG HARUS DIPERBAIKI!${NC}"
    echo ""
    echo "Silakan lihat error di atas dan perbaiki."
    exit 1
fi
