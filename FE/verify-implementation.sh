#!/bin/bash
# ROASTY MARKETPLACE - IMPLEMENTATION VERIFICATION SCRIPT
# Run this to verify all files are in place

echo "=================================================="
echo "  ROASTY MARKETPLACE - IMPLEMENTATION CHECK"
echo "=================================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counter
TOTAL=0
FOUND=0

# Function to check file
check_file() {
    TOTAL=$((TOTAL + 1))
    if [ -f "$1" ]; then
        echo -e "${GREEN}✅${NC} Found: $1"
        FOUND=$((FOUND + 1))
    else
        echo -e "${RED}❌${NC} Missing: $1"
    fi
}

# Function to check file content
check_content() {
    TOTAL=$((TOTAL + 1))
    if grep -q "$2" "$1" 2>/dev/null; then
        echo -e "${GREEN}✅${NC} $3"
        FOUND=$((FOUND + 1))
    else
        echo -e "${RED}❌${NC} $3"
    fi
}

echo "📋 CHECKING NAVBAR IMPLEMENTATION"
echo "=================================="
echo ""
check_file "navbar-helper.js"
check_file "README_NAVBAR.md"
check_file "NAVBAR_IMPLEMENTATION.md"
check_file "IMPLEMENTATION_SUMMARY.md"

echo ""
echo "🧪 Checking navbar-helper.js functions..."
check_content "navbar-helper.js" "initializeNavbar" "  - initializeNavbar() function exists"
check_content "navbar-helper.js" "updateCartBadge" "  - updateCartBadge() function exists"
check_content "navbar-helper.js" "dispatchCartUpdateEvent" "  - dispatchCartUpdateEvent() function exists"

echo ""
echo "📝 CHECKING UPDATED HTML FILES"
echo "================================"
echo ""
check_content "Halaman.beranda.html" "navbar-helper.js" "Halaman.beranda.html - navbar-helper included"
check_content "halaman.detail.produk.html" "navbar-helper.js" "halaman.detail.produk.html - navbar-helper included"
check_content "halaman.keranjang.belanja.html" "navbar-helper.js" "halaman.keranjang.belanja.html - navbar-helper included"
check_content "halaman.daftar.produk.html" "navbar-helper.js" "halaman.daftar.produk.html - navbar-helper included"
check_content "halaman.profil.html" "navbar-helper.js" "halaman.profil.html - navbar-helper included"
check_content "halaman.pembayaran.html" "navbar-helper.js" "halaman.pembayaran.html - navbar-helper included"

echo ""
echo "🔐 CHECKING LOGIN IMPLEMENTATION"
echo "=================================="
echo ""
check_file "login-verification.js"
check_file "LOGIN_ADMIN_REDIRECT_CHECKLIST.md"
check_file "LOGIN_REDIRECT_SUMMARY.md"
check_file "BACKEND_API_REQUIREMENTS.md"
check_file "ADMIN_REDIRECT_FINAL_SUMMARY.md"

echo ""
echo "🧪 Checking login.html enhancements..."
check_content "login.html" "console.log" "  - Console logging added"
check_content "login.html" "validation" "  - Response validation added"
check_content "login.html" "role === 'admin'" "  - Admin redirect logic"
check_content "login-verification.js" "testAdminLogin" "  - Verification script ready"

echo ""
echo "📚 CHECKING DOCUMENTATION"
echo "=========================="
echo ""
check_file "DOCUMENTATION_INDEX.md"

echo ""
echo "=================================================="
echo ""
echo "SUMMARY:"
echo "--------"
echo -e "Total checks: $TOTAL"
echo -e "Passed: ${GREEN}$FOUND${NC}"
echo -e "Failed: ${RED}$((TOTAL - FOUND))${NC}"
echo ""

if [ $FOUND -eq $TOTAL ]; then
    echo -e "${GREEN}✅ ALL FILES IN PLACE - READY FOR TESTING!${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Verify backend API returns 'role' field"
    echo "2. Test admin login → admin_dashboard.html"
    echo "3. Test user login → Halaman.beranda.html"
    echo "4. Check console logs for debugging"
    echo ""
    exit 0
else
    echo -e "${YELLOW}⚠️  SOME FILES MISSING - CHECK ABOVE${NC}"
    echo ""
    exit 1
fi
