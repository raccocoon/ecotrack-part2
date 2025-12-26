-- Add points columns to transactions table
ALTER TABLE transactions ADD COLUMN points_used INT DEFAULT 0 AFTER total_amount;
ALTER TABLE transactions ADD COLUMN points_discount DECIMAL(10,2) DEFAULT 0 AFTER points_used;

-- Update existing transactions to have 0 points
UPDATE transactions SET points_used = 0, points_discount = 0 WHERE points_used IS NULL;
