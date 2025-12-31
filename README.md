# ElectroPalestine.com

<p align="center">
  <strong>E-commerce Platform for Electronics Store</strong>
</p>

A modern, bilingual (Arabic/English) e-commerce platform built with Laravel 12, featuring a comprehensive product catalog, shopping cart, order management, and advanced admin dashboard with analytics and reporting capabilities.

## Features

### 🛍️ Store Frontend
- **Bilingual Support**: Full Arabic and English language support
- **Product Catalog**: Browse products by categories, types, and companies
- **Shopping Cart**: Add, update, and manage cart items
- **Product Reviews & Ratings**: Customers can review and rate products
- **User Accounts**: Registration, login, and account management
- **Order Management**: Track orders and download PDF invoices
- **Contact Form**: Customer support and inquiries

### 👨‍💼 Admin Dashboard
- **Product Management**: Create, update, and manage products with categories, types, and companies
- **Catalog Builder**: Advanced catalog management with relational data
- **Order Management**: Process and manage customer orders
- **User Management**: Manage users, customers, and role assignments
- **Campaign Management**: Create and manage promotional campaigns
- **Analytics**: Product analytics and performance tracking
- **Reports**: 
  - Sales by date (Excel/PDF export)
  - Sales by category (Excel export)
  - Profit by period (Excel/PDF export)
- **Role-Based Pricing**: Configure different prices for different user roles

### 📄 Additional Features
- **PDF Invoice Generation**: Generate invoices with Arabic font support
- **Excel Exports**: Export reports and data to Excel format
- **Email Notifications**: Contact form and invoice email support
- **Best Sellers**: Highlight best-selling products
- **Stock Management**: Track product inventory
- **Points/Rewards System**: Product points reward system

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade templates with Tailwind CSS 4
- **Build Tool**: Vite
- **PDF Generation**: DomPDF (with Arabic font support) & TCPDF
- **Excel Export**: Maatwebsite Excel
- **Database**: SQLite (default) / MySQL / PostgreSQL
- **Authentication**: Laravel Breeze (built-in)

## Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- SQLite (or MySQL/PostgreSQL)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/electropalestine.com.git
   cd electropalestine.com
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your `.env` file**
   - Set your database connection
   - Configure mail settings (if needed)
   - Set `APP_NAME` and other application settings

5. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## Quick Setup Script

The project includes a setup script that handles the basic installation:

```bash
composer run setup
```

## Development

For development with hot reloading:

```bash
composer run dev
```

This command runs:
- Laravel development server
- Queue worker
- Log viewer (Pail)
- Vite dev server

## Project Structure

```
app/
├── Exports/          # Excel export classes
├── Http/
│   ├── Controllers/  # Application controllers
│   ├── Middleware/   # Custom middleware
│   └── Requests/     # Form request validation
├── Models/           # Eloquent models
├── Services/         # Business logic services
└── Providers/        # Service providers

resources/
├── views/            # Blade templates
│   ├── admin/        # Admin panel views
│   ├── store/        # Storefront views
│   └── emails/       # Email templates
├── css/              # Stylesheets
└── js/               # JavaScript files

database/
├── migrations/       # Database migrations
├── seeders/          # Database seeders
└── factories/        # Model factories
```

## Key Models

- **Product**: Products with categories, types, companies, pricing, and inventory
- **Category**: Product categories
- **Type**: Product types
- **Company**: Product manufacturers/companies
- **Order**: Customer orders
- **OrderItem**: Order line items
- **User**: Application users
- **Review**: Product reviews and ratings
- **Campaign**: Promotional campaigns
- **Role**: User roles with permissions

## Arabic Font Support

The application includes Arabic font support for PDF generation. See the following documentation files for setup:

- `DOMPDF_ARABIC_SOLUTION.md` - Arabic font solutions for DomPDF
- `FONT_INSTALLATION_COMPLETE.md` - Font installation guide
- `INSTALL_ARABIC_FONT.md` - Additional font setup instructions

## Testing

Run the test suite:

```bash
composer run test
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For support, email support@electropalestine.com or create an issue in the repository.
