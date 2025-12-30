

-- Create database
CREATE DATABASE IF NOT EXISTS assessment_project CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE assessment_project;

-- =====================================================
-- Categories Table
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    color VARCHAR(7) DEFAULT '#667eea',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Transactions Table
-- =====================================================
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    category_id INT,
    description TEXT,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_date (transaction_date),
    INDEX idx_currency (currency),
    INDEX idx_category (category_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert Default Categories
-- =====================================================
INSERT INTO categories (name, color) VALUES 
    ('Food & Dining', '#ef4444'),
    ('Transportation', '#3b82f6'),
    ('Shopping', '#8b5cf6'),
    ('Entertainment', '#ec4899'),
    ('Bills & Utilities', '#f59e0b'),
    ('Healthcare', '#10b981'),
    ('Other', '#6b7280')
ON DUPLICATE KEY UPDATE name=name;

-- =====================================================
-- Insert Sample Data
-- =====================================================
INSERT INTO transactions (title, amount, currency, category_id, description, transaction_date) VALUES
    ('Grocery Shopping', 125.50, 'USD', 1, 'Weekly groceries from supermarket', '2024-12-28'),
    ('Gas Station', 45.00, 'USD', 2, 'Fuel for car', '2024-12-27'),
    ('Online Shopping', 89.99, 'USD', 3, 'New headphones from Amazon', '2024-12-26'),
    ('Restaurant Dinner', 65.00, 'USD', 1, 'Dinner with friends', '2024-12-25'),
    ('Movie Tickets', 30.00, 'USD', 4, 'Cinema night', '2024-12-24'),
    ('Electricity Bill', 85.75, 'USD', 5, 'Monthly utility bill', '2024-12-23'),
    ('Pharmacy', 42.30, 'USD', 6, 'Prescription medication', '2024-12-22'),
    ('Coffee Shop', 15.50, 'EUR', 1, 'Morning coffee', '2024-12-21'),
    ('Uber Ride', 22.00, 'USD', 2, 'Ride to airport', '2024-12-20'),
    ('Gym Membership', 50.00, 'USD', 7, 'Monthly membership fee', '2024-12-19');


-- =====================================================
-- Success Message
-- =====================================================
SELECT 'Database setup completed successfully!' as Status;