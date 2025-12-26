-- EcoTrack Part 2 Database Schema
-- Run this SQL in phpMyAdmin or MySQL client

-- Products table for Eco-Store
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100),
    image_path VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table (persistent cart in database)
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    payment_type ENUM('cash', 'points') DEFAULT 'cash',
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    points_used INT DEFAULT 0,
    points_discount DECIMAL(10,2) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Transaction items table
CREATE TABLE IF NOT EXISTS transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_each DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Recycling logs table (for member recycling tracking)
CREATE TABLE IF NOT EXISTS recycling_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    center_name VARCHAR(255),
    material_type VARCHAR(100),
    weight_kg DECIMAL(5,2),
    points_earned INT DEFAULT 0,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample products
INSERT INTO products (name, description, price, category, image_path, stock) VALUES
('Recycling Bin Set', '3-compartment recycling bin for home use', 120.00, 'Recycling Tools', 'assets/images/products/bin-set.jpg', 50),
('Canvas Tote Bag', 'Reusable eco-friendly shopping bag', 15.00, 'Reusable Products', 'assets/images/products/tote-bag.jpg', 100),
('Bamboo Cutlery Set', 'Portable bamboo utensils with case', 25.00, 'Reusable Products', 'assets/images/products/cutlery.jpg', 75),
('Stainless Steel Water Bottle', 'Insulated 500ml water bottle', 45.00, 'Reusable Products', 'assets/images/products/bottle.jpg', 60),
('Compost Bin', 'Kitchen compost bin with charcoal filter', 85.00, 'Recycling Tools', 'assets/images/products/compost.jpg', 30),
('Beeswax Food Wraps', 'Set of 3 reusable food wraps', 20.00, 'Reusable Products', 'assets/images/products/wraps.jpg', 80),
('LED Bulb Pack', '4-pack energy-saving LED bulbs', 35.00, 'Energy Saving', 'assets/images/products/led.jpg', 40),
('Reusable Produce Bags', 'Set of 5 mesh produce bags', 18.00, 'Reusable Products', 'assets/images/products/produce-bags.jpg', 90),
('Bamboo Toothbrush Set', 'Pack of 4 biodegradable toothbrushes', 22.00, 'Household Items', 'assets/images/products/toothbrush.jpg', 70),
('Eco Cleaning Kit', 'Natural cleaning tools and cloths', 38.00, 'Household Items', 'assets/images/products/cleaning.jpg', 45),
('Recycling Guide Poster', 'Educational wall poster for waste sorting', 12.00, 'Educational Items', 'assets/images/products/poster.jpg', 100),
('Eco Starter Pack', 'Beginner kit with reusable essentials', 55.00, 'Lifestyle Accessories', 'assets/images/products/starter.jpg', 35);
