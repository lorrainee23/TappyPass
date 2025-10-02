# TappyPass - Bus Booking System

A comprehensive bus booking management system built with Laravel 10 (backend + admin web) and Ionic Vue (mobile app).

## Features

### Admin Web Panel
- 🔐 Secure admin authentication
- 📊 Dashboard with statistics and analytics
- 👥 User management
- 🎫 Booking management and confirmation
- 💳 Payment verification (GCash receipts)
- 🔲 QR code generation for confirmed bookings
- ⚙️ Settings (GCash QR code upload)

### Mobile App (Ionic Vue)
- 📱 User registration and login
- 🚌 Easy bus ticket booking
- 💰 GCash payment integration
- 📸 Receipt upload functionality
- 📋 Transaction history
- 🔲 QR code display for confirmed bookings
- 🔔 Real-time booking status updates

## Tech Stack

### Backend
- Laravel 10
- MySQL Database
- Laravel Sanctum (API Authentication)
- Simple QR Code Package

### Admin Web
- Blade Templates
- Tailwind CSS
- Font Awesome Icons

### Mobile App
- Ionic 8
- Vue 3
- TypeScript
- Axios
- Capacitor

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js & npm
- MySQL
- Ionic CLI (`npm install -g @ionic/cli`)

### Backend Setup

1. **Install PHP dependencies**
```bash
composer install
```

2. **Install QR Code package**
```bash
composer require simplesoftwareio/simple-qrcode
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Update `.env` file with your database credentials**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tappypass
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Seed admin user**
```bash
php artisan db:seed --class=AdminSeeder
```

Default admin credentials:
- Email: `admin@tappypass.com`
- Password: `password`

7. **Create storage link**
```bash
php artisan storage:link
```

8. **Start the development server**
```bash
php artisan serve
```

The backend will be available at `http://localhost:8000`

### Mobile App Setup

1. **Navigate to mobile app directory**
```bash
cd TappyPass-mobile
```

2. **Install dependencies**
```bash
npm install
```

3. **Update API URL** (if needed)

Edit `src/services/api.ts` and update the `API_URL`:
```typescript
const API_URL = 'http://localhost:8000/api';
```

4. **Run the development server**
```bash
ionic serve
```

or

```bash
npm run dev
```

The mobile app will be available at `http://localhost:8100`

### Building for Mobile

**For Android:**
```bash
ionic capacitor add android
ionic capacitor build android
ionic capacitor open android
```

**For iOS:**
```bash
ionic capacitor add ios
ionic capacitor build ios
ionic capacitor open ios
```

## Usage

### Admin Panel

1. Access the admin panel at `http://localhost:8000/admin/login`
2. Login with admin credentials
3. Upload GCash QR code in Settings
4. Monitor bookings and verify payments
5. Confirm bookings to generate QR codes

### Mobile App

1. Register a new account or login
2. Create a new booking with travel details
3. View the GCash QR code and make payment
4. Upload payment receipt
5. Wait for admin confirmation
6. View QR code for confirmed bookings

## Project Structure

```
TappyPass/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin web controllers
│   │   │   └── Api/            # API controllers for mobile
│   │   └── Middleware/
│   └── Models/                 # Eloquent models
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── resources/
│   └── views/
│       └── admin/              # Admin blade templates
├── routes/
│   ├── api.php                 # API routes
│   └── web.php                 # Web routes
└── TappyPass-mobile/           # Ionic Vue mobile app
    ├── src/
    │   ├── services/           # API services
    │   ├── views/              # Vue pages
    │   └── router/             # Vue router
    └── capacitor.config.ts     # Capacitor configuration
```

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get authenticated user

### Bookings
- `GET /api/bookings` - Get user bookings
- `POST /api/bookings` - Create new booking
- `GET /api/bookings/{id}` - Get booking details
- `POST /api/bookings/{id}/upload-receipt` - Upload payment receipt
- `POST /api/bookings/{id}/cancel` - Cancel booking

### Settings
- `GET /api/settings/gcash-qr` - Get GCash QR code

## Database Schema

### Users
- id, name, email, password, role (admin/customer), phone

### Bookings
- id, user_id, booking_number, passenger_name, phone, from_location, to_location, travel_date, travel_time, seats, amount, status, qr_code

### Transactions
- id, booking_id, user_id, transaction_number, amount, payment_method, receipt_image, payment_status, admin_notes

### Settings
- id, key, value

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
