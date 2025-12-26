# Points System Setup Guide

## Step 1: Update Database

Go to phpMyAdmin and run this SQL:

```sql
-- Add points column to users table
ALTER TABLE users ADD COLUMN points INT DEFAULT 0 AFTER role;

-- Give existing users starting points
UPDATE users SET points = 2450 WHERE role = 'member';

-- Add points_cost column to products
ALTER TABLE products ADD COLUMN points_cost INT DEFAULT 0 AFTER price;

-- Add payment_type to cart
ALTER TABLE cart ADD COLUMN payment_type ENUM('cash', 'points') DEFAULT 'cash' AFTER quantity;

-- Update some products to have points cost
UPDATE products SET points_cost = 500 WHERE name = 'Recycling Guide Poster';
UPDATE products SET points_cost = 1000 WHERE name = 'Eco Starter Pack';
```

## How It Works

### 1. **User Points Balance**
- Every user has a points balance (default: 2,450 pts)
- Displayed at top of Eco-Store page
- Points can be earned through activities (future feature)

### 2. **Products with Points Option**
- Some products can be bought with EITHER cash OR points
- Products show: "RM 12.00 or 500 pts"
- If product has `points_cost > 0`, points option is available

### 3. **Adding to Cart**
- User selects payment method: 💵 Cash or ⭐ Points
- System validates user has enough points
- Cart stores payment type for each item

### 4. **Cart Display**
- Shows payment method for each item
- Separate totals for cash and points
- Example:
  - Total (Cash): RM 150.00
  - Total (Points): 1,500 pts

### 5. **Checkout**
- Deducts cash amount from payment
- Deducts points from user balance
- User can mix cash and points items in one order

## Features Implemented

✅ Points balance display
✅ Products with points pricing
✅ Payment method selection
✅ Points validation (insufficient points check)
✅ Mixed cart (cash + points items)
✅ Separate totals in cart
✅ Points deduction on checkout (next step)

## Testing

1. Login to your account
2. Go to Eco-Store
3. Find "Recycling Guide Poster" (500 pts) or "Eco Starter Pack" (1,000 pts)
4. Click product → Select "Points" payment
5. Add to cart
6. View cart → See points total
7. Checkout → Points will be deducted

## Next Steps

- Checkout process needs to deduct points from user balance
- Add points earning system (e.g., +10 pts per recycling log)
- Transaction history should show points used
