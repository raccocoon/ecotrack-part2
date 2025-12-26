-- Add points column to users table
ALTER TABLE users ADD COLUMN points INT DEFAULT 0 AFTER role;

-- Give existing users some starting points
UPDATE users SET points = 2450 WHERE role = 'member';

-- Add points_cost column to products (optional - for items that can be bought with points only)
ALTER TABLE products ADD COLUMN points_cost INT DEFAULT 0 AFTER price;

-- Update some products to have points cost
UPDATE products SET points_cost = 500 WHERE name = 'Recycling Guide Poster';
UPDATE products SET points_cost = 1000 WHERE name = 'Eco Starter Pack';
