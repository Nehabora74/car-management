# CarDekho Clone - Complete Project

A CarsDekho.com inspired website with Admin Panel for managing content.

## Project Structure

```
cardekho/
├── index.php                 # Homepage
├── car-preference-form.php   # Car inquiry form (Task 1)
├── config/
│   ├── database.php          # Database configuration
│   └── helpers.php           # Helper functions
├── admin/
│   ├── index.php             # Admin Dashboard
│   ├── cars.php              # Manage Cars (Add/Edit/Delete)
│   ├── banners.php           # Manage Banners
│   ├── menu.php              # Manage Menu Items
│   ├── settings.php          # Site Settings (Header/Footer)
│   └── customers.php         # View Customer Inquiries
├── uploads/
│   ├── cars/                 # Car images
│   ├── banners/              # Banner images
│   └── logo/                 # Site logo
└── database/
    └── setup.sql             # Database setup script
```

## Installation Steps

### Step 1: Copy Files to XAMPP
Copy the entire `cardekho` folder to:
```
C:\xampp\htdocs\cardekho\
```

### Step 2: Create Database
1. Start XAMPP (Apache + MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Click "SQL" tab
4. Copy & paste content from `database/setup.sql`
5. Click "Go"

### Step 3: Configure Database
Open `config/database.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Your MySQL username
define('DB_PASS', '');          // Your MySQL password (empty for XAMPP)
define('DB_NAME', 'cardekho_db');
```

### Step 4: Set Folder Permissions
Make sure `uploads` folder is writable:
- Windows: Usually works by default
- Linux/Mac: `chmod -R 777 uploads/`

### Step 5: Access Website
- **Frontend:** `http://localhost/cardekho/`
- **Admin Panel:** `http://localhost/cardekho/admin/`

## Features

### Frontend (Homepage)
- ✅ Responsive Header with navigation
- ✅ Banner Slider (auto-rotating)
- ✅ Search Box (New Cars, Used Cars, Electric)
- ✅ Most Searched Cars Section
- ✅ Latest Cars Section
- ✅ Footer with contact info & social links

### Admin Panel
- ✅ Dashboard with statistics
- ✅ **Banners Management** - Add/Edit/Delete banners
- ✅ **Cars Management** - Add/Edit/Delete cars with images
- ✅ **Menu Management** - Add/Edit/Delete navigation items
- ✅ **Site Settings** - Update logo, contact info, social links
- ✅ **Customers** - View form submissions from Task 1

## Database Tables

| Table | Purpose |
|-------|---------|
| `site_settings` | Header, Footer, Contact info |
| `menu_items` | Navigation menu links |
| `banners` | Homepage banner slides |
| `cars` | Car listings (Most Searched & Latest) |
| `customers` | Form submissions (Task 1) |
| `car_preferences` | Car type preferences (Task 1) |

## Admin Panel Usage

### Adding a New Car
1. Go to Admin → Cars
2. Click "Add New Car"
3. Fill in Name, Price
4. Upload Image
5. Select Type (Most Searched / Latest)
6. Save

### Adding a Banner
1. Go to Admin → Banners
2. Click "Add New Banner"
3. Fill in Title, Subtitle
4. Add Button Text & URL (optional)
5. Upload Banner Image (1920x600px recommended)
6. Save

### Updating Site Settings
1. Go to Admin → Site Settings
2. Update Phone, Email, Address
3. Add Social Media URLs
4. Update Footer Text
5. Save

## Task 1 Integration

The car preference form from Task 1 is integrated at:
`http://localhost/cardekho/car-preference-form.php`

Form submissions appear in Admin → Customers section.

## Responsive Design

The website is fully responsive and works on:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (< 768px)

## 🛠 Technologies Used

- PHP 7.4+
- MySQL / MariaDB
- HTML5 / CSS3
- JavaScript (Vanilla)
- Font Awesome Icons



