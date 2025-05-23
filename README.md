# FoodieHub - Food Marketplace Platform

FoodieHub is a web-based food marketplace platform that connects local food vendors with customers. The platform allows vendors to register, manage their menus, and track orders, while customers can browse food items, place orders, and track deliveries.

## Features

### User Roles
- **Customer**: Browse food, place orders, track delivery
- **Vendor**: Manage listings, track orders, respond to customers
- **Admin**: Manage platform users, vendors, categories, and monitor performance

### Core Modules
1. **Authentication & Authorization**
   - User registration/login (Vendor, Customer, Admin)
   - Role-based access control
   - Password reset

2. **Product (Food) Management**
   - Create/update/delete menu items
   - Add descriptions, images, prices, and availability
   - Tag items with categories

3. **Order Management**
   - Add items to cart
   - Checkout and make payment
   - View order history
   - Receive and update order statuses

4. **Search & Filter**
   - Filter food by category, price range, rating, vendor
   - Keyword search

5. **Rating & Reviews**
   - Rate and review food items and vendors

6. **Dashboard**
   - Customer dashboard: Orders, favorites, profile
   - Vendor dashboard: Sales stats, orders, inventory
   - Admin dashboard: Platform stats, users, vendors, top-selling items

## Technology Stack
- **Backend**: PHP (MVC Architecture)
- **Frontend**: HTML, CSS, JavaScript
- **CSS Framework**: Bootstrap 5
- **Icons**: Font Awesome
- **Templating**: Blade (with duncan3dc/blade)
- **Database**: MySQL
- **Authentication**: Session-based

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer

### Setup Instructions
1. Clone the repository:
   ```
   git clone https://github.com/yourusername/foodiehub.git
   cd foodiehub
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Set up the database:
   ```
   php database/setup.php
   ```

4. Start the development server:
   ```
   php -S localhost:8000 -t public
   ```

5. Access the application:
   Open your browser and navigate to `http://localhost:8000`

## Project Structure
```
foodiehub/
├── app/
│   ├── controllers/    # Application controllers
│   ├── core/           # Core framework classes
│   ├── models/         # Data models
│   └── config/         # Configuration files
├── database/           # Database schema and migrations
├── public/             # Publicly accessible files
│   ├── css/            # CSS files
│   ├── js/             # JavaScript files
│   ├── uploads/        # Uploaded files
│   └── index.php       # Entry point
├── vendor/             # Composer dependencies
└── views/              # Blade templates
    ├── layouts/        # Layout templates
    ├── home/           # Home page templates
    ├── products/       # Product templates
    ├── auth/           # Authentication templates
    ├── cart/           # Cart templates
    ├── orders/         # Order templates
    ├── vendor/         # Vendor dashboard templates
    └── admin/          # Admin dashboard templates
```

## Default Credentials
- **Admin**:
  - Email: admin@foodiehub.com
  - Password: password

## License


## Acknowledgements
- Bootstrap for the responsive UI components
- Font Awesome for the icons
- duncan3dc/blade for the templating engine
