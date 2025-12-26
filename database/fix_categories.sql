-- Fix product categories to match the new category names
UPDATE products SET category = 'Reusable Products' WHERE name IN ('Canvas Tote Bag', 'Bamboo Cutlery Set', 'Stainless Steel Water Bottle', 'Beeswax Food Wraps', 'Reusable Produce Bags');
UPDATE products SET category = 'Recycling Tools' WHERE name IN ('Recycling Bin Set', 'Compost Bin');
UPDATE products SET category = 'Household Items' WHERE name LIKE '%Toothbrush%' OR name LIKE '%Cleaning%';
UPDATE products SET category = 'Energy Saving' WHERE name LIKE '%LED%' OR name LIKE '%Power%';
UPDATE products SET category = 'Educational Items' WHERE name LIKE '%Guide%' OR name LIKE '%Poster%';
UPDATE products SET category = 'Lifestyle Accessories' WHERE name LIKE '%Starter%';
