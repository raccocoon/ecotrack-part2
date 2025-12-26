# EcoTrack Part 2 - Setup Instructions

## Database Setup

### Step 1: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `ecotrack_db`
3. Select the database

### Step 2: Run Schema
1. Go to the SQL tab
2. Copy and paste the contents of `database/schema.sql`
3. Click "Go" to execute

This will create:
- `products` table with 8 sample products
- `cart` table for shopping cart
- `transactions` table for orders
- `transaction_items` table for order details
- `recycling_logs` table for member activities

### Step 3: Verify Tables
Check that all tables are created:
- users (should already exist from Part 1)
- products
- cart
- transactions
- transaction_items
- recycling_logs

## Testing the E-commerce Flow

### 1. Register/Login
- Go to http://localhost/ecotrack-part2/
- Register a new account or login with existing credentials
- You should be redirected to dashboard.php

### 2. Browse Products
- Click "Eco-Store" in the sidebar OR
- Go to http://localhost/ecotrack-part2/shop.php
- Try filtering by category
- Try searching for products

### 3. Add to Cart
- Click "View" on any product
- Select quantity
- Click "Add to Cart"
- You should see success message

### 4. View Cart
- Click "My Cart" in sidebar OR
- Go to http://localhost/ecotrack-part2/cart.php
- Try updating quantities with +/- buttons
- Try removing an item

### 5. Checkout
- Click "Checkout Now" from cart
- Fill in shipping address
- Select payment method (dummy)
- Click "Complete Order"

### 6. View Receipt
- You should be redirected to receipt page
- Click "Download PDF" to get printable version
- Click "View All Orders" to see transaction history

### 7. Transaction History
- Click "View All Orders" OR
- Go to http://localhost/ecotrack-part2/transactions.php
- See all your past orders
- Click "View Receipt" on any order

## Troubleshooting

### "Database connection failed"
- Check `config/db.php` settings
- Ensure MySQL is running
- Verify database name is `ecotrack_db`

### "Table doesn't exist"
- Run the schema.sql file in phpMyAdmin
- Check that all tables were created successfully

### "No products showing"
- Verify products were inserted from schema.sql
- Check phpMyAdmin products table has 8 rows

### Cart not working
- Ensure you're logged in
- Check browser console for JavaScript errors
- Verify cart table exists in database

### Checkout fails
- Check that products have sufficient stock
- Verify transactions and transaction_items tables exist
- Check PHP error logs

## File Structure

```
ecotrack-part2/
├── actions/
│   ├── cart-add.php
│   ├── cart-update.php
│   ├── cart-remove.php
│   └── checkout-process.php
├── assets/
│   └── images/
│       ├── ecotrack-logo.png
│       └── products/
├── config/
│   └── db.php
├── database/
│   └── schema.sql
├── partials/
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── cart.php
├── checkout.php
├── product-details.php
├── receipt.php
├── receipt-pdf.php
├── shop.php
├── transactions.php
└── (other existing files)
```

## Next Steps

After testing the e-commerce flow:
1. Setup email notifications (PHPMailer)
2. Add profile management
3. Enhance dashboard with charts
4. Add member recycling map
5. Implement waste logging
6. Add challenges and quizzes

## Notes

- All pages require login except shop.php (guests can browse)
- Guests are redirected to login when trying to add to cart
- Stock is automatically deducted after successful checkout
- Cart is cleared after successful order
- All prices are in Malaysian Ringgit (RM)
- Shipping is fixed at RM 10.00

## Support

If you encounter any issues:
1. Check PHP error logs
2. Check browser console
3. Verify database tables and data
4. Ensure all files are in correct locations
5. Check file permissions (if on Linux/Mac)
