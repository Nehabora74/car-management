-- =====================================================
-- CarsDekho Clone - Database Setup
-- =====================================================

CREATE DATABASE IF NOT EXISTS cardekho_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE cardekho_db;

-- =====================================================
-- Table: site_settings (Header & Footer content)
-- =====================================================
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'CarDekho'),
('site_logo', 'uploads/logo.png'),
('phone', '1800-258-5656'),
('email', 'contact@cardekho.com'),
('address', 'Jaipur, Rajasthan, India'),
('facebook', 'https://facebook.com/cardekho'),
('twitter', 'https://twitter.com/cardekho'),
('instagram', 'https://instagram.com/cardekho'),
('youtube', 'https://youtube.com/cardekho'),
('footer_text', '© 2026 CarDekho. All Rights Reserved.');

-- =====================================================
-- Table: menu_items (Navigation menu)
-- =====================================================
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default menu items
INSERT INTO menu_items (title, url, sort_order) VALUES
('New Cars', '#new-cars', 1),
('Used Cars', '#used-cars', 2),
('Sell Car', '#sell-car', 3),
('Compare', '#compare', 4),
('News', '#news', 5),
('Reviews', '#reviews', 6);

-- =====================================================
-- Table: banners (Hero banners)
-- =====================================================
CREATE TABLE IF NOT EXISTS banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    button_text VARCHAR(100),
    button_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- Table: cars (All cars - most searched & latest)
-- =====================================================
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    price VARCHAR(50) NOT NULL,
    image VARCHAR(255) NOT NULL,
    car_type ENUM('most_searched', 'latest') NOT NULL DEFAULT 'latest',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample cars (Most Searched)
INSERT INTO cars (name, price, image, car_type, sort_order) VALUES
('Maruti Suzuki Swift', '₹6.49 - 9.64 Lakh', 'uploads/cars/swift.jpg', 'most_searched', 1),
('Hyundai Creta', '₹11.00 - 20.15 Lakh', 'uploads/cars/creta.jpg', 'most_searched', 2),
('Tata Nexon', '₹8.10 - 15.50 Lakh', 'uploads/cars/nexon.jpg', 'most_searched', 3),
('Mahindra Thar', '₹11.35 - 17.60 Lakh', 'uploads/cars/thar.jpg', 'most_searched', 4);

-- Insert sample cars (Latest)
INSERT INTO cars (name, price, image, car_type, sort_order) VALUES
('Maruti Suzuki Fronx', '₹7.51 - 13.04 Lakh', 'uploads/cars/fronx.jpg', 'latest', 1),
('Hyundai Exter', '₹6.13 - 10.28 Lakh', 'uploads/cars/exter.jpg', 'latest', 2),
('Tata Punch', '₹6.13 - 10.20 Lakh', 'uploads/cars/punch.jpg', 'latest', 3),
('Kia Seltos', '₹10.90 - 20.35 Lakh', 'uploads/cars/seltos.jpg', 'latest', 4);

-- =====================================================
-- Previous tables from Task 1 (Car Preferences)
-- =====================================================
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS car_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    car_type ENUM('Hatchback', 'Sedan', 'SUV') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);
