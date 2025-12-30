/**
 * LOGIN & ADMIN REDIRECT VERIFICATION SCRIPT
 * Gunakan ini untuk testing dan debugging login flow
 * 
 * Cara penggunaan:
 * 1. Buka login.html di browser
 * 2. Buka DevTools (F12)
 * 3. Copy paste commands di bawah ke console
 */

// ============================================
// 1. VERIFY API ENDPOINT
// ============================================
console.log('========================================');
console.log('1. VERIFY API ENDPOINT');
console.log('========================================');
console.log('API URL:', CONFIG.API_BASE_URL);

// Test API connection
async function testApiConnection() {
  console.log('\n🔍 Testing API connection...');
  try {
    const response = await fetch(`${CONFIG.API_BASE_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: 'test@test.com',
        password: 'wrong'
      })
    });
    console.log('✅ API Connection:', response.status);
    return response.status;
  } catch (err) {
    console.error('❌ API Connection Error:', err.message);
    return null;
  }
}

// ============================================
// 2. VERIFY ADMIN LOGIN RESPONSE
// ============================================
async function testAdminLogin() {
  console.log('\n========================================');
  console.log('2. VERIFY ADMIN LOGIN RESPONSE');
  console.log('========================================');
  
  // Ganti dengan admin credentials yang sebenarnya
  const adminEmail = 'admin@example.com';
  const adminPassword = 'password123';
  
  console.log(`Testing admin login with: ${adminEmail}`);
  
  try {
    const response = await fetch(`${CONFIG.API_BASE_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: adminEmail,
        password: adminPassword
      })
    });

    const data = await response.json();
    console.log('📊 Response Status:', response.status);
    console.log('📦 Full Response:', data);
    
    if (data.data && data.data.user) {
      console.log('\n👤 User Data:');
      console.log('  ID:', data.data.user.id);
      console.log('  Name:', data.data.user.name);
      console.log('  Email:', data.data.user.email);
      console.log('  Role:', data.data.user.role);
      console.log('  Photo:', data.data.user.photo);
      
      if (data.data.user.role === 'admin') {
        console.log('\n✅ ROLE VERIFIED: Admin');
      } else {
        console.log('\n⚠️ WARNING: Role is not admin:', data.data.user.role);
      }
    }
    
    if (data.data && data.data.token) {
      console.log('\n🔐 Token Present: YES');
      console.log('  Token (first 50 chars):', data.data.token.substring(0, 50) + '...');
    } else {
      console.log('\n❌ ERROR: Token not found in response');
    }
    
    return data;
  } catch (err) {
    console.error('❌ Login Error:', err.message);
    return null;
  }
}

// ============================================
// 3. VERIFY USER LOGIN RESPONSE
// ============================================
async function testUserLogin() {
  console.log('\n========================================');
  console.log('3. VERIFY USER LOGIN RESPONSE');
  console.log('========================================');
  
  // Ganti dengan user credentials yang sebenarnya
  const userEmail = 'user@example.com';
  const userPassword = 'password123';
  
  console.log(`Testing user login with: ${userEmail}`);
  
  try {
    const response = await fetch(`${CONFIG.API_BASE_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: userEmail,
        password: userPassword
      })
    });

    const data = await response.json();
    console.log('📊 Response Status:', response.status);
    console.log('📦 Full Response:', data);
    
    if (data.data && data.data.user) {
      console.log('\n👤 User Data:');
      console.log('  ID:', data.data.user.id);
      console.log('  Name:', data.data.user.name);
      console.log('  Email:', data.data.user.email);
      console.log('  Role:', data.data.user.role);
      console.log('  Photo:', data.data.user.photo);
      
      if (data.data.user.role === 'user') {
        console.log('\n✅ ROLE VERIFIED: User');
      } else if (data.data.user.role === 'admin') {
        console.log('\n⚠️ WARNING: User has admin role');
      } else {
        console.log('\n⚠️ WARNING: Unknown role:', data.data.user.role);
      }
    }
    
    if (data.data && data.data.token) {
      console.log('\n🔐 Token Present: YES');
      console.log('  Token (first 50 chars):', data.data.token.substring(0, 50) + '...');
    } else {
      console.log('\n❌ ERROR: Token not found in response');
    }
    
    return data;
  } catch (err) {
    console.error('❌ Login Error:', err.message);
    return null;
  }
}

// ============================================
// 4. VERIFY LOCALSTORAGE
// ============================================
function verifyLocalStorage() {
  console.log('\n========================================');
  console.log('4. VERIFY LOCALSTORAGE');
  console.log('========================================');
  
  const token = localStorage.getItem('token');
  const user = localStorage.getItem('user');
  
  console.log('\n📦 localStorage.token:');
  if (token) {
    console.log('  ✅ Present (length:', token.length, ')');
    console.log('  First 50 chars:', token.substring(0, 50) + '...');
  } else {
    console.log('  ❌ NOT FOUND');
  }
  
  console.log('\n📦 localStorage.user:');
  if (user) {
    const userData = JSON.parse(user);
    console.log('  ✅ Present');
    console.log('  User:', userData);
    console.log('  Role:', userData.role);
  } else {
    console.log('  ❌ NOT FOUND');
  }
}

// ============================================
// 5. SIMULATE LOGIN FLOW (FOR ADMIN)
// ============================================
async function simulateAdminLogin() {
  console.log('\n========================================');
  console.log('5. SIMULATE ADMIN LOGIN FLOW');
  console.log('========================================');
  
  const data = await testAdminLogin();
  
  if (data && data.data && data.data.user && data.data.token) {
    console.log('\n🔄 Simulating local storage save...');
    localStorage.setItem('token', data.data.token);
    localStorage.setItem('user', JSON.stringify(data.data.user));
    console.log('✅ localStorage updated');
    
    console.log('\n🔄 Simulating redirect logic...');
    if (data.data.user.role === 'admin') {
      console.log('✅ Would redirect to: admin_dashboard.html');
    } else {
      console.log('✅ Would redirect to: Halaman.beranda.html');
    }
  }
}

// ============================================
// 6. SIMULATE LOGIN FLOW (FOR USER)
// ============================================
async function simulateUserLogin() {
  console.log('\n========================================');
  console.log('6. SIMULATE USER LOGIN FLOW');
  console.log('========================================');
  
  const data = await testUserLogin();
  
  if (data && data.data && data.data.user && data.data.token) {
    console.log('\n🔄 Simulating local storage save...');
    localStorage.setItem('token', data.data.token);
    localStorage.setItem('user', JSON.stringify(data.data.user));
    console.log('✅ localStorage updated');
    
    console.log('\n🔄 Simulating redirect logic...');
    if (data.data.user.role === 'admin') {
      console.log('✅ Would redirect to: admin_dashboard.html');
    } else {
      console.log('✅ Would redirect to: Halaman.beranda.html');
    }
  }
}

// ============================================
// 7. CLEAR ALL DATA
// ============================================
function clearAllData() {
  console.log('\n========================================');
  console.log('7. CLEARING ALL LOGIN DATA');
  console.log('========================================');
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  console.log('✅ Cleared localStorage.token');
  console.log('✅ Cleared localStorage.user');
  console.log('\n✅ All data cleared');
}

// ============================================
// QUICK START GUIDE
// ============================================
console.log('\n');
console.log('╔════════════════════════════════════════╗');
console.log('║   LOGIN VERIFICATION SCRIPT LOADED     ║');
console.log('╚════════════════════════════════════════╝');
console.log('\n📝 AVAILABLE COMMANDS:\n');
console.log('1. testApiConnection()        - Test API endpoint');
console.log('2. testAdminLogin()           - Test admin login response');
console.log('3. testUserLogin()            - Test user login response');
console.log('4. verifyLocalStorage()       - Check localStorage data');
console.log('5. simulateAdminLogin()       - Full admin login simulation');
console.log('6. simulateUserLogin()        - Full user login simulation');
console.log('7. clearAllData()             - Clear all localStorage');
console.log('\n🚀 RECOMMENDED TESTING ORDER:\n');
console.log('1. testApiConnection()');
console.log('2. testAdminLogin()');
console.log('3. testUserLogin()');
console.log('4. verifyLocalStorage()');
console.log('5. simulateAdminLogin()');
console.log('6. simulateUserLogin()');
console.log('\n💡 TIP: Update email/password variables inside functions');
console.log('        to match your actual test credentials\n');
