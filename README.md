# Foodie Order - Food Ordering System

A RESTful API-based food ordering application built with PHP and MySQL. This system allows users to register, log in, Explore meals, send orders, and manage their account.

## Features

- **User Authentication**: User registration and login functionality
- **Meal Management**: Explore and manage meals with descriptions, prices, and images
- **Order Management**: Create, view, update, and delete orders
- **User Profiles**: Get user information and order history
- **CORS Support**: API endpoints support cross-origin requests
- **RESTful API**: Clean, standard HTTP endpoints for all operations



## Requirements

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher
- **XAMPP** (or similar Apache + MySQL setup)
- **PDO** PHP extension (for database connectivity)

## Installation

### 1. Database Setup

Create a MySQL database named `foodie_order`:

```sql
CREATE DATABASE foodie_order;
```

### 2. Configure Database Connection

Edit `db_connect.php` and update the database credentials:

```php
$host = 'localhost';
$dbname = 'foodie_order';
$username = 'root';      // Your MySQL username
$password = '';          // Your MySQL password
```

### 3. Place Files in Web Root

Copy the entire project folder to your XAMPP web root:
```
C:\xampp\htdocs\foodie_order\
```

### 4. Access the Application

The API endpoints are available at:
```
http://localhost/foodie_order/
```

## API Endpoints

### Authentication

- **POST** `/register.php` - Register a new user
  - Required: `username`, `email`, `password`
  - Returns: User ID and success message

- **POST** `/login.php` - User login
  - Required: `username`, `password`
  - Returns: User ID and authentication token

### Meals

- **GET** `/list_meals.php` - Get all available meals
- **POST** `/add_meals.php` - Add a new meal (admin only)
- **POST** `/edit_meal.php` - Update meal details (admin only)
- **POST** `/delete_meal.php` - Delete a meal (admin only)

### Orders

- **GET** `/list_orders.php` - Get user's orders
- **GET** `/get_orders.php` - Get specific order details
- **POST** `/add_orders.php` - Create a new order
- **POST** `/edit_order.php` - Update order details
- **POST** `/update_order_info.php` - Update order status
- **POST** `/delete_order.php` - Cancel an order

### User

- **GET** `/get_user_id.php` - Get user profile information



## Configuration

### CORS Headers
All endpoints include CORS headers to allow cross-origin requests:
```php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
```

### File Uploads
Images are stored in the `/uploads/` directory. Ensure this folder has proper write permissions:
```bash
chmod 755 uploads/
```
