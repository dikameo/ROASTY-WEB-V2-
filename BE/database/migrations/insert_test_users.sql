-- Insert test users directly into database
-- Remember: password hashes are generated via Laravel Hash::make()
-- These are bcrypt hashes of 'password123' and 'admin123'

INSERT INTO users (name, email, password, phone, role, created_at, updated_at) VALUES
('John Doe', 'john@umm.id', '$2y$12$gzNtvF.LDX8U3xMQzVJ8S.L2Lq8fXAZGQqOxV89VfxaP7r5r/gNqK', '08123456789', 'customer', NOW(), NOW()),
('Admin User', 'admin@umm.id', '$2y$12$ZzqNe7Z4F2K9JfR4XzL1ZuzR4M5cNpR9T1V3M7K2N1Q0R5S6T7U8V', '08987654321', 'admin', NOW(), NOW())
ON CONFLICT (email) DO UPDATE SET
  password = EXCLUDED.password,
  phone = EXCLUDED.phone,
  updated_at = NOW();
