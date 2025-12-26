# EcoTrack Part 2 - Member Features Implementation Plan

## Overview
This document outlines the step-by-step implementation of all logged-in member features for EcoTrack Part 2.

## UI/UX Standards (Based on mockup latest.html)
- Sidebar navigation with emoji icons
- White cards with shadows and borders
- Emerald green (#10b981) as primary color
- Glass effect backgrounds
- Font: Plus Jakarta Sans
- Responsive design with Tailwind CSS

## Database Tables Created
✓ products - Eco-store items
✓ cart - User shopping cart
✓ transactions - Purchase records
✓ transaction_items - Order line items
✓ recycling_logs - Member recycling tracking

## Implementation Steps

### STEP 1: Browse & Shop ✓ COMPLETED
- shop.php - Product listing with category filter and search
- product-details.php - Single product view with add to cart

### STEP 2: Cart System (NEXT)
Files to create:
- actions/cart-add.php - Add item to cart
- actions/cart-update.php - Update quantity
- actions/cart-remove.php - Remove item
- cart.php - View cart with update/remove options

### STEP 3: Checkout & Payment
Files to create:
- checkout.php - Checkout form with order summary
- actions/checkout-process.php - Process dummy payment, create transaction
- receipt.php - On-screen receipt view
- receipt-pdf.php - PDF receipt generator (using Dompdf)

### STEP 4: Transaction History
Files to create:
- transactions.php - List all user transactions
- transaction-details.php - View single transaction

### STEP 5: Email Notifications
Files to create:
- config/mail.php - PHPMailer configuration
- config/mail.example.php - Template for users
- includes/send-email.php - Email sending function
- Integrate with registration and checkout

### STEP 6: Profile Management
Files to create:
- profile.php - View and edit profile
- actions/profile-update.php - Update profile handler
- actions/password-change.php - Change password handler

### STEP 7: Member Dashboard Enhancement
Update dashboard.php with:
- Weekly activity chart (Chart.js)
- Real transaction count
- Carbon savings calculation
- Quick action buttons

### STEP 8: Member Recycling Map
Files to create:
- recycling-map.php - Member-only map with logging feature
- actions/log-recycling.php - Save recycling activity

### STEP 9: Additional Member Features
Files to create:
- log-waste.php - Waste tracking form
- challenges.php - Community challenges
- quiz.php - Eco quizzes
- segregation-guide.php - Waste segregation guide

### STEP 10: Security & Polish
- Add CSRF protection
- Validate all inputs
- Add loading states
- Test all flows
- Create sample data

## Notes
- All member pages must check session and redirect if not logged in
- Use PDO prepared statements for all database queries
- Sanitize all outputs with htmlspecialchars()
- Match UI exactly to mockup latest.html sidebar design
- Keep existing files (login, register, index, how-it-works, impact, find-center) unchanged
