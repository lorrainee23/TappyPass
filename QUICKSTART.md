# TappyPass Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Backend Setup

```bash
# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Update .env with your database
DB_DATABASE=tappypass
DB_USERNAME=root
DB_PASSWORD=

# Run migrations and seed admin
php artisan migrate
php artisan db:seed --class=AdminSeeder

# Create storage link
php artisan storage:link

# Start server
php artisan serve
```

**Admin Login:**
- URL: http://localhost:8000/admin/login
- Email: admin@tappypass.com
- Password: password

### Step 2: Mobile App Setup

```bash
# Navigate to mobile app
cd TappyPass-mobile

# Install dependencies
npm install

# Start development server
ionic serve
# or
npm run dev
```

**Mobile App:** http://localhost:8100

---

## 📱 Testing the Complete Flow

### 1. Admin Setup (First Time)
1. Login to admin panel: http://localhost:8000/admin/login
2. Go to Settings
3. Upload a GCash QR code image (any QR code image for testing)

### 2. Customer Booking Flow
1. Open mobile app: http://localhost:8100
2. Register a new account
3. Create a new booking:
   - Fill in passenger details
   - Select route and date
   - Enter amount (e.g., 500)
4. View GCash QR code
5. Upload a receipt image (any image for testing)
6. Submit booking

### 3. Admin Verification
1. Go to admin Bookings page
2. Click on the new booking
3. View the uploaded receipt
4. Update payment status to "Paid"
5. Click "Confirm Booking" to generate QR code

### 4. Customer Confirmation
1. Go back to mobile app
2. View bookings
3. See confirmed booking with QR code

---

## 🔧 Common Issues

### CORS Error
If you get CORS errors, add this to `config/cors.php`:
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:8100'],
```

### Storage Link Error
If images don't show, run:
```bash
php artisan storage:link
```

### Database Connection Error
Make sure MySQL is running and database exists:
```bash
mysql -u root -p
CREATE DATABASE tappypass;
```

### Mobile App Can't Connect to API
Update the API URL in `TappyPass-mobile/src/services/api.ts`:
```typescript
const API_URL = 'http://localhost:8000/api';
// For mobile device testing, use your computer's IP:
// const API_URL = 'http://192.168.1.100:8000/api';
```

---

## 📦 Building for Production

### Backend
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Mobile App
```bash
cd TappyPass-mobile
npm run build

# For Android
ionic capacitor build android

# For iOS
ionic capacitor build ios
```

---

## 🎯 Key Features to Test

- ✅ User Registration & Login
- ✅ Create Booking
- ✅ GCash Payment Flow
- ✅ Receipt Upload
- ✅ Admin Payment Verification
- ✅ QR Code Generation
- ✅ Booking Status Updates
- ✅ Transaction History
- ✅ User Management

---

## 📞 Support

For issues or questions:
1. Check the main README.md
2. Review the API endpoints documentation
3. Check Laravel logs: `storage/logs/laravel.log`
4. Check browser console for mobile app errors

---

## 🔐 Security Notes

**Important for Production:**
1. Change admin password immediately
2. Update `.env` with strong APP_KEY
3. Use HTTPS for production
4. Set proper CORS origins
5. Enable rate limiting
6. Use environment-specific API URLs
7. Secure file upload validation

---

Happy Coding! 🚀
