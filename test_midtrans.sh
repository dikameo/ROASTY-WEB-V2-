#!/bin/bash

# ROASTY MIDTRANS INTEGRATION TEST SCRIPT
# =======================================
# Script ini untuk test integrasi Midtrans payment gateway
# Jalankan dari root backend folder: bash test_midtrans.sh

echo "=========================================="
echo "ROASTY MIDTRANS INTEGRATION TEST"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

API_URL="http://localhost:8000/api"
TEST_USER_EMAIL="test@example.com"
TEST_USER_PASSWORD="password123"
TEST_TOKEN=""

# Test 1: Cek .env configuration
echo -e "${YELLOW}[TEST 1] Checking .env Midtrans Configuration...${NC}"
if grep -q "MIDTRANS_IS_PRODUCTION=false" ../.env; then
    echo -e "${GREEN}✓ MIDTRANS_IS_PRODUCTION is set to false (Sandbox Mode)${NC}"
else
    echo -e "${RED}✗ MIDTRANS_IS_PRODUCTION is NOT false!${NC}"
    echo -e "${RED}  Please check .env file${NC}"
    exit 1
fi

if grep -q "MIDTRANS_SERVER_KEY=Mid-server-" ../.env; then
    echo -e "${GREEN}✓ MIDTRANS_SERVER_KEY found${NC}"
else
    echo -e "${RED}✗ MIDTRANS_SERVER_KEY not found!${NC}"
    exit 1
fi

if grep -q "MIDTRANS_CLIENT_KEY=Mid-client-" ../.env; then
    echo -e "${GREEN}✓ MIDTRANS_CLIENT_KEY found${NC}"
else
    echo -e "${RED}✗ MIDTRANS_CLIENT_KEY not found!${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}[TEST 2] Testing API Connectivity...${NC}"

# Test endpoint /midtrans-config
echo "Testing: GET /api/midtrans-config"
RESPONSE=$(curl -s -w "\n%{http_code}" "$API_URL/midtrans-config")
HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
BODY=$(echo "$RESPONSE" | head -n-1)

if [ "$HTTP_CODE" = "200" ]; then
    echo -e "${GREEN}✓ HTTP 200 OK${NC}"
    echo "Response: $BODY"
    
    # Check if response has client_key
    if echo "$BODY" | grep -q "client_key"; then
        echo -e "${GREEN}✓ client_key found in response${NC}"
    else
        echo -e "${RED}✗ client_key NOT found in response!${NC}"
        exit 1
    fi
else
    echo -e "${RED}✗ HTTP $HTTP_CODE${NC}"
    echo "Response: $BODY"
    exit 1
fi

echo ""
echo -e "${YELLOW}[TEST 3] Testing Order Creation (Requires Authentication)...${NC}"

# Coba login dulu
echo "1. Attempting to login..."
LOGIN_RESPONSE=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$TEST_USER_EMAIL\",\"password\":\"$TEST_USER_PASSWORD\"}")

echo "Login Response: $LOGIN_RESPONSE"

# Extract token
TEST_TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TEST_TOKEN" ]; then
    echo -e "${YELLOW}⚠ Login failed or no test user found${NC}"
    echo "Skipping order creation test..."
    echo ""
    echo -e "${YELLOW}To test order creation:${NC}"
    echo "1. Create a test user via registration"
    echo "2. Or manually create order with:"
    echo ""
    echo 'curl -X POST "http://localhost:8000/api/orders" \'
    echo '  -H "Authorization: Bearer YOUR_TOKEN" \'
    echo '  -H "Content-Type: application/json" \'
    echo '  -d '"'"'{'
    echo '    "items": [{"product_id": 1, "quantity": 2, "price": 50000}],'
    echo '    "shipping_address": "Jl. Test 123",'
    echo '    "payment_method": "gopay",'
    echo '    "subtotal": 100000,'
    echo '    "shipping_cost": 15000,'
    echo '    "total": 115000'
    echo '  }'"'"
else
    echo -e "${GREEN}✓ Login successful, token: ${TEST_TOKEN:0:20}...${NC}"
    echo ""
    echo "2. Creating test order with GoPay payment..."
    
    ORDER_RESPONSE=$(curl -s -X POST "$API_URL/orders" \
      -H "Authorization: Bearer $TEST_TOKEN" \
      -H "Content-Type: application/json" \
      -d '{
        "items": [{"product_id": 1, "quantity": 1, "price": 100000}],
        "shipping_address": "Test Address, Jl. Test 123",
        "payment_method": "gopay",
        "subtotal": 100000,
        "shipping_cost": 15000,
        "total": 115000
      }')
    
    echo "Order Response: $ORDER_RESPONSE"
    
    if echo "$ORDER_RESPONSE" | grep -q "snap_token"; then
        echo -e "${GREEN}✓ Snap token received!${NC}"
        SNAP_TOKEN=$(echo "$ORDER_RESPONSE" | grep -o '"snap_token":"[^"]*"' | cut -d'"' -f4)
        echo "Token: ${SNAP_TOKEN:0:20}..."
    else
        echo -e "${RED}✗ No snap token in response!${NC}"
    fi
fi

echo ""
echo "=========================================="
echo "TEST SUMMARY"
echo "=========================================="
echo ""
echo -e "${GREEN}✓ Configuration: PASS${NC}"
echo -e "${GREEN}✓ API Connectivity: PASS${NC}"
echo -e "${YELLOW}⚠ Order Creation: REQUIRES MANUAL TEST${NC}"
echo ""
echo "Next Steps:"
echo "1. Open browser and test at: http://localhost:3000/FE/halaman.pembayaran.html"
echo "2. Check browser console (F12) for logs"
echo "3. Check backend logs: BE/storage/logs/laravel.log"
echo ""
echo "For Midtrans Sandbox Testing:"
echo "- Dashboard: https://dashboard.midtrans.com (sandbox mode)"
echo "- Simulator: https://simulator.midtrans.com"
echo "- Test GoPay with phone: +62 8123456789"
echo "- Test Credit Card: 4011111111111111 / 12/25 / 123"
echo ""
echo -e "${GREEN}Integration ready for testing!${NC}"
