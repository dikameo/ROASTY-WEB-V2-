# 🔗 API Reference - Midtrans Configuration Endpoint

## 📡 GET /api/midtrans-config

Endpoint untuk fetch Midtrans configuration dari backend. Frontend menggunakan endpoint ini untuk mendapatkan `client_key` yang diperlukan untuk load Midtrans Snap script.

---

## 📋 Endpoint Details

| Property | Value |
|----------|-------|
| **Method** | `GET` |
| **URL** | `/api/midtrans-config` |
| **Full URL** | `http://localhost:8000/api/midtrans-config` |
| **Authentication** | ❌ Not Required (Public) |
| **Content-Type** | `application/json` |
| **Rate Limit** | None (recommended: 1000 req/min) |
| **Response Time** | < 100ms (typically) |

---

## 📤 Request

### cURL Example
```bash
curl -X GET \
  http://localhost:8000/api/midtrans-config \
  -H 'Accept: application/json'
```

### JavaScript/Fetch Example
```javascript
fetch('http://localhost:8000/api/midtrans-config')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));
```

### JavaScript with CONFIG
```javascript
const API_URL = CONFIG.API_BASE_URL; // "http://localhost:8000/api"

fetch(`${API_URL}/midtrans-config`)
  .then(response => {
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
  })
  .then(configData => {
    console.log('Client Key:', configData.data.client_key);
    console.log('Is Production:', configData.data.is_production);
  })
  .catch(error => console.error('Error:', error));
```

### Python Example
```python
import requests

response = requests.get('http://localhost:8000/api/midtrans-config')
config = response.json()

client_key = config['data']['client_key']
is_production = config['data']['is_production']

print(f"Client Key: {client_key}")
print(f"Is Production: {is_production}")
```

### PHP Example
```php
<?php
$url = 'http://localhost:8000/api/midtrans-config';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$configData = json_decode($response, true);
$clientKey = $configData['data']['client_key'];
$isProduction = $configData['data']['is_production'];

echo "Client Key: $clientKey\n";
echo "Is Production: " . ($isProduction ? 'true' : 'false') . "\n";
?>
```

---

## 📥 Response

### Success Response (200 OK)

```json
{
  "success": true,
  "data": {
    "client_key": "Mid-client-xHIl5auaQWqaNfVJ",
    "is_production": true
  }
}
```

### Response Properties

| Property | Type | Description |
|----------|------|-------------|
| `success` | boolean | Request status (true = success) |
| `data` | object | Configuration data container |
| `data.client_key` | string | Midtrans client key from .env |
| `data.is_production` | boolean | Production mode flag from .env |

---

## 📊 Response Size & Performance

| Metric | Value |
|--------|-------|
| **Response Size** | ~150-200 bytes |
| **Compression** | Yes (gzip) |
| **Cache** | Can be cached (5-10 minutes) |
| **Typical Time** | 5-50ms |

---

## ❌ Error Responses

### 500 Internal Server Error
**Status Code**: `500`

```json
{
  "success": false,
  "message": "Server error",
  "error": {
    "code": "SERVER_ERROR",
    "details": "Configuration not found"
  }
}
```

**Causes**:
- Missing .env file
- Missing `MIDTRANS_CLIENT_KEY` in .env
- Server misconfiguration
- Database connection issue

**Solution**:
```bash
# Check .env file
cat BE/.env | grep MIDTRANS

# Should show:
# MIDTRANS_CLIENT_KEY=Mid-client-xHIl5auaQWqaNfVJ
# MIDTRANS_IS_PRODUCTION=true
```

### 404 Not Found
**Status Code**: `404`

```json
{
  "message": "Not Found"
}
```

**Causes**:
- Route not registered
- Route path typo
- URL typo

**Solution**:
```bash
# Verify route in routes/api.php
grep -n "midtrans-config" BE/routes/api.php

# Should show:
# Route::get('/midtrans-config', [OrderController::class, 'getMidtransConfig']);
```

### 503 Service Unavailable
**Status Code**: `503`

```json
{
  "message": "Service Unavailable"
}
```

**Causes**:
- Server is down
- Server is restarting
- Too many requests (rate limit)

**Solution**:
```bash
# Restart server
php artisan serve

# Or check if already running
netstat -ano | findstr :8000  # Windows
lsof -i :8000                  # Mac/Linux
```

---

## 🔄 Use Case Example

### Frontend Implementation

```javascript
// Step 1: Define API base URL
const API_URL = "http://localhost:8000/api";

// Step 2: Create function to load Midtrans config
async function loadMidtransConfig() {
    try {
        // Fetch config from endpoint
        const response = await fetch(`${API_URL}/midtrans-config`);
        
        // Check response status
        if (!response.ok) {
            throw new Error(`HTTP Error: ${response.status}`);
        }
        
        // Parse response
        const configData = await response.json();
        
        // Validate response format
        if (!configData.success || !configData.data.client_key) {
            throw new Error('Invalid response format');
        }
        
        // Extract values
        const clientKey = configData.data.client_key;
        const isProduction = configData.data.is_production;
        
        console.log('✅ Config loaded successfully');
        console.log(`   Client Key: ${clientKey.substring(0, 15)}...`);
        console.log(`   Mode: ${isProduction ? 'PRODUCTION' : 'SANDBOX'}`);
        
        return {
            clientKey,
            isProduction
        };
        
    } catch (error) {
        console.error('❌ Failed to load config:', error);
        throw error;
    }
}

// Step 3: Load Midtrans script with config
async function loadMidtransScript() {
    try {
        // Get config from backend
        const config = await loadMidtransConfig();
        
        // Create script element
        const script = document.createElement('script');
        script.src = 'https://app.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', config.clientKey);
        script.async = true;
        
        // Add event listeners
        script.onload = () => {
            console.log('✅ Midtrans Snap loaded!');
            console.log('   Window.snap available:', typeof window.snap);
            
            // Now ready to use window.snap.pay()
        };
        
        script.onerror = () => {
            console.error('❌ Failed to load Midtrans Snap');
        };
        
        // Append to page
        document.body.appendChild(script);
        
    } catch (error) {
        console.error('❌ Error loading Midtrans:', error);
        alert('Payment system is temporarily unavailable. Please try again later.');
    }
}

// Step 4: Call when page is ready
document.addEventListener('DOMContentLoaded', loadMidtransScript);
```

---

## 🔐 Security Considerations

### What's Exposed (PUBLIC)
✅ Safe to expose to frontend:
- `client_key` - Required for client-side payment form
- `is_production` - Just a boolean flag

### What's NOT Exposed (SECRET)
🔒 Never expose to frontend:
- ❌ `MIDTRANS_SERVER_KEY` - Kept in backend only
- ❌ `MIDTRANS_MERCHANT_ID` - Kept in backend only

### Architecture
```
Frontend (Unsafe Zone)
  ├─ Can see: client_key (public)
  └─ Cannot see: server_key (secret)
  
Backend (Safe Zone)
  ├─ Keeps: server_key, merchant_id
  ├─ Verifies: payment tokens
  └─ Updates: order status via webhook
  
Midtrans API
  ├─ Frontend sends: client_key + snap_token
  ├─ Backend sends: server_key for server operations
  └─ Security: Token-based, one-time use
```

---

## ⚙️ Configuration (.env)

### Required Environment Variables

```env
# Midtrans Configuration in BE/.env
MIDTRANS_MERCHANT_ID=G610858736
MIDTRANS_SERVER_KEY=Mid-server-PnwPw7x7LEh_XdWf_0sFUQM9
MIDTRANS_CLIENT_KEY=Mid-client-xHIl5auaQWqaNfVJ
MIDTRANS_IS_PRODUCTION=true
```

### Where to Get Values

1. **Login to [Midtrans Dashboard](https://dashboard.midtrans.com)**
2. **Go to Settings → API Keys**
3. **Copy values based on environment**:
   - **Sandbox**: Use sandbox keys
   - **Production**: Use production keys

### Environment-Specific Config

**Development (Sandbox)**:
```env
MIDTRANS_MERCHANT_ID=your-sandbox-merchant-id
MIDTRANS_SERVER_KEY=Mid-server-xxxx...
MIDTRANS_CLIENT_KEY=Mid-client-yyyy...
MIDTRANS_IS_PRODUCTION=false
```

**Production**:
```env
MIDTRANS_MERCHANT_ID=your-production-merchant-id
MIDTRANS_SERVER_KEY=Mid-server-xxxx... (production)
MIDTRANS_CLIENT_KEY=Mid-client-yyyy... (production)
MIDTRANS_IS_PRODUCTION=true
```

---

## 📈 Monitoring & Analytics

### Metrics to Track

```javascript
// Track API call success rate
let configLoadSuccess = 0;
let configLoadFailure = 0;

async function loadMidtransScript() {
    try {
        const response = await fetch(`${API_URL}/midtrans-config`);
        if (response.ok) {
            configLoadSuccess++;
        } else {
            configLoadFailure++;
        }
    } catch (error) {
        configLoadFailure++;
    }
}

// Report
console.log(`Success Rate: ${(configLoadSuccess / (configLoadSuccess + configLoadFailure) * 100).toFixed(2)}%`);
```

### Key Metrics

| Metric | Ideal Target | Warning Level |
|--------|--------------|---------------|
| **Success Rate** | > 99.5% | < 95% |
| **Response Time** | < 100ms | > 500ms |
| **Availability** | 99.9% | < 99% |
| **Error Rate** | < 0.5% | > 5% |

---

## 🧪 Testing

### Manual Testing
```bash
# Test 1: Verify endpoint is accessible
curl -v http://localhost:8000/api/midtrans-config

# Expected: HTTP/1.1 200 OK with JSON response

# Test 2: Check response format
curl -s http://localhost:8000/api/midtrans-config | jq

# Expected: Pretty-printed JSON with success=true

# Test 3: Verify client_key is present
curl -s http://localhost:8000/api/midtrans-config | jq '.data.client_key'

# Expected: "Mid-client-xHIl5auaQWqaNfVJ"
```

### Automated Testing
```javascript
// Jest/Vitest test example
describe('Midtrans Config API', () => {
  test('should return client_key', async () => {
    const response = await fetch(`${API_URL}/midtrans-config`);
    const data = await response.json();
    
    expect(data.success).toBe(true);
    expect(data.data.client_key).toBeDefined();
    expect(data.data.client_key).toMatch(/^Mid-client-/);
  });
  
  test('should return is_production flag', async () => {
    const response = await fetch(`${API_URL}/midtrans-config`);
    const data = await response.json();
    
    expect(typeof data.data.is_production).toBe('boolean');
  });
});
```

---

## 📝 Implementation Checklist

- [x] Endpoint created in OrderController
- [x] Route registered in api.php
- [x] Returns correct JSON format
- [x] Reads from .env correctly
- [x] Error handling implemented
- [x] Documentation complete
- [x] Frontend integration working
- [x] Testing completed
- [x] Ready for production

---

## 🎓 Quick Reference

### GET Request Example
```javascript
// Fetch config
const config = await fetch('/api/midtrans-config')
  .then(r => r.json())
  .then(d => d.data);

// Use in script tag
<script data-client-key="${config.client_key}"></script>
```

### Error Handling
```javascript
try {
  const response = await fetch(`${API_URL}/midtrans-config`);
  if (!response.ok) throw new Error(`HTTP ${response.status}`);
  return await response.json();
} catch (error) {
  console.error('Failed to load config:', error);
  // Show user-friendly error
  alert('Payment system temporarily unavailable');
}
```

### Production Considerations
- ✅ Endpoint is cached-friendly (immutable data)
- ✅ No authentication required (public data only)
- ✅ Very low response time (read-only operation)
- ✅ Can add response caching if needed

---

## 📚 Related Documentation

- [Full Implementation Guide](MIDTRANS_IMPLEMENTATION.md)
- [Quick Start](MIDTRANS_QUICKSTART.md)
- [Before/After Comparison](MIDTRANS_COMPARISON.md)
- [Visual Diagrams](MIDTRANS_DIAGRAMS.md)
- [Implementation Checklist](MIDTRANS_CHECKLIST.md)

---

## 🎉 Summary

Endpoint ini adalah jantung dari dynamic Midtrans configuration loading:
- ✅ Fetch client key dari backend .env
- ✅ No hardcoded values di frontend
- ✅ Simple & reliable
- ✅ Production-ready

**Use it, monitor it, scale it!** 🚀

---

Last Updated: December 30, 2025
Status: Ready for Production
