-- Complete Setup SQL - Run this in phpMyAdmin

-- 1. Add points column if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS points INT DEFAULT 0 AFTER role;

-- 2. Give users starting points
UPDATE users SET points = 2450 WHERE role = 'member' AND points = 0;

-- 3. Fix product categories to match new category names
UPDATE products SET category = 'Reusable Products' WHERE name IN ('Canvas Tote Bag', 'Bamboo Cutlery Set', 'Stainless Steel Water Bottle', 'Beeswax Food Wraps', 'Reusable Produce Bags');
UPDATE products SET category = 'Recycling Tools' WHERE name IN ('Recycling Bin Set', 'Compost Bin');
UPDATE products SET category = 'Household Items' WHERE name LIKE '%Toothbrush%' OR name LIKE '%Cleaning%';
UPDATE products SET category = 'Energy Saving' WHERE name LIKE '%LED%' OR name LIKE '%Power%';
UPDATE products SET category = 'Educational Items' WHERE name LIKE '%Guide%' OR name LIKE '%Poster%';
UPDATE products SET category = 'Lifestyle Accessories' WHERE name LIKE '%Starter%';

-- 4. Verify categories
SELECT DISTINCT category FROM products;

-- Expected output:
-- Reusable Products
-- Recycling Tools
-- Household Items
-- Energy Saving
-- Educational Items
-- Lifestyle Accessories
