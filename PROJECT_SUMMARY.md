# TappyPass - Project Summary

## 🎯 Project Overview

**TappyPass** is a complete bus booking management system with three main components:
1. **Laravel 10 Backend API** - RESTful API for mobile app
2. **Admin Web Panel** - Booking and user management
3. **Ionic Vue Mobile App** - Customer booking interface

---

## 📁 Project Structure

```
TappyPass/
├── Backend (Laravel 10)
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── Admin/              # Admin web controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── SettingController.php
│   │   │   └── Api/                # Mobile API controllers
│   │   │       ├── AuthController.php
│   │   │       ├── BookingController.php
│   │   │       └── SettingController.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Booking.php
│   │   │   ├── Transaction.php
│   │   │   └── Setting.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   ├── database/
│   │   ├── migrations/
│   │   │   ├── add_role_to_users_table.php
│   │   │   ├── create_bookings_table.php
│   │   │   ├── create_transactions_table.php
│   │   │   └── create_settings_table.php
│   │   └── seeders/
│   │       └── AdminSeeder.php
│   ├── resources/views/admin/
│   │   ├── layout.blade.php
│   │   ├── login.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── bookings/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── users/
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   └── settings/
│   │       └── index.blade.php
│   └── routes/
│       ├── api.php                 # Mobile API routes
│       └── web.php                 # Admin web routes
│
└── TappyPass-mobile/ (Ionic Vue)
    ├── src/
    │   ├── services/
    │   │   ├── api.ts              # Axios configuration
    │   │   ├── auth.ts             # Authentication service
    │   │   └── booking.ts          # Booking service
    │   ├── views/
    │   │   ├── auth/
    │   │   │   ├── LoginPage.vue
    │   │   │   └── RegisterPage.vue
    │   │   ├── TabsPage.vue
    │   │   ├── HomePage.vue
    │   │   ├── BookingsPage.vue
    │   │   ├── ProfilePage.vue
    │   │   ├── NewBookingPage.vue
    │   │   └── BookingDetailPage.vue
    │   └── router/
    │       └── index.ts            # Vue router with guards
    └── capacitor.config.ts
```

---

## 🗄️ Database Schema

### users
- id (PK)
- name
- email (unique)
- password
- role (enum: 'admin', 'customer')
- phone (nullable)
- timestamps

### bookings
- id (PK)
- user_id (FK → users)
- booking_number (unique)
- passenger_name
- phone
- from_location
- to_location
- travel_date
- travel_time
- seats
- amount
- status (enum: 'pending', 'confirmed', 'cancelled')
- qr_code (nullable)
- timestamps

### transactions
- id (PK)
- booking_id (FK → bookings)
- user_id (FK → users)
- transaction_number (unique)
- amount
- payment_method (enum: 'gcash', 'cash')
- receipt_image (nullable)
- payment_status (enum: 'pending', 'paid', 'rejected')
- admin_notes (nullable)
- timestamps

### settings
- id (PK)
- key (unique)
- value (text, nullable)
- timestamps

---

## 🔌 API Endpoints

### Authentication
```
POST   /api/register              - Register new user
POST   /api/login                 - User login
POST   /api/logout                - User logout (auth)
GET    /api/user                  - Get authenticated user (auth)
```

### Bookings
```
GET    /api/bookings              - Get user's bookings (auth)
POST   /api/bookings              - Create new booking (auth)
GET    /api/bookings/{id}         - Get booking details (auth)
POST   /api/bookings/{id}/upload-receipt  - Upload receipt (auth)
POST   /api/bookings/{id}/cancel  - Cancel booking (auth)
```

### Settings
```
GET    /api/settings/gcash-qr     - Get GCash QR code (public)
```

---

## 🎨 Admin Web Routes

```
GET    /admin/login               - Admin login page
POST   /admin/login               - Process login
POST   /admin/logout              - Logout

GET    /admin/dashboard           - Dashboard with stats
GET    /admin/bookings            - List all bookings
GET    /admin/bookings/{id}       - View booking details
POST   /admin/bookings/{id}/confirm  - Confirm booking & generate QR
POST   /admin/bookings/{id}/payment-status  - Update payment status

GET    /admin/users               - List all users
GET    /admin/users/{id}          - View user details

GET    /admin/settings            - Settings page
POST   /admin/settings/gcash-qr   - Upload GCash QR code
```

---

## 🔄 Complete Booking Flow

### 1. Customer Creates Booking (Mobile)
```
Customer → Fill booking form → Submit
↓
API creates booking with status: 'pending'
API creates transaction with payment_status: 'pending'
↓
Return booking ID and transaction details
```

### 2. Payment Process (Mobile)
```
Customer → View GCash QR code (from admin settings)
↓
Customer pays via GCash
↓
Customer uploads receipt screenshot
↓
API saves receipt image to transaction
```

### 3. Admin Verification (Web)
```
Admin → View booking details
↓
Admin views uploaded receipt
↓
Admin updates payment_status to 'paid' or 'rejected'
↓
If paid → Admin clicks "Confirm Booking"
↓
System generates QR code for booking
System updates booking status to 'confirmed'
```

### 4. Customer Confirmation (Mobile)
```
Customer → View booking details
↓
If confirmed → Display QR code
Customer shows QR code when boarding
```

---

## 🔐 Authentication & Authorization

### Mobile App (Laravel Sanctum)
- Token-based authentication
- Tokens stored in localStorage
- Auto-logout on 401 response
- Route guards in Vue Router

### Admin Web (Session)
- Session-based authentication
- AdminMiddleware checks role
- CSRF protection enabled
- Redirect to login if unauthorized

---

## 📦 Key Dependencies

### Backend
- `laravel/framework: ^10.10` - Core framework
- `laravel/sanctum: ^3.3` - API authentication
- `simplesoftwareio/simple-qrcode: ^4.2` - QR code generation

### Mobile App
- `@ionic/vue: ^8.0.0` - Ionic framework
- `vue: ^3.3.0` - Vue 3
- `axios: ^1.6.0` - HTTP client
- `ionicons: ^7.0.0` - Icons
- `@capacitor/core: ^7.4.3` - Native functionality

---

## 🎯 Key Features Implemented

### Admin Panel ✅
- ✅ Secure login with role-based access
- ✅ Dashboard with real-time statistics
- ✅ Booking management (view, confirm, cancel)
- ✅ Payment verification with receipt viewing
- ✅ QR code generation for confirmed bookings
- ✅ User management and viewing
- ✅ GCash QR code upload and management
- ✅ Responsive design with Tailwind CSS

### Mobile App ✅
- ✅ User registration and login
- ✅ Multi-step booking creation
- ✅ GCash QR code display for payment
- ✅ Receipt image upload
- ✅ Booking list with filtering
- ✅ Booking detail view with QR code
- ✅ Transaction history
- ✅ Profile management
- ✅ Real-time status updates
- ✅ Modern UI with Ionic components

---

## 🚀 Deployment Checklist

### Backend
- [ ] Set up production database
- [ ] Update `.env` for production
- [ ] Run migrations on production
- [ ] Seed admin user
- [ ] Configure storage permissions
- [ ] Set up SSL certificate
- [ ] Configure CORS for mobile app
- [ ] Enable caching (config, routes, views)
- [ ] Set up queue workers (if needed)
- [ ] Configure backup system

### Mobile App
- [ ] Update API URL to production
- [ ] Build production version
- [ ] Test on actual devices
- [ ] Submit to Google Play Store
- [ ] Submit to Apple App Store
- [ ] Set up push notifications (optional)
- [ ] Configure app icons and splash screens

---

## 📊 Statistics & Metrics

The dashboard tracks:
- Total bookings
- Pending bookings
- Confirmed bookings
- Total users (customers)
- Pending payments
- Total revenue (from paid transactions)

---

## 🔒 Security Features

- Password hashing (bcrypt)
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection
- File upload validation
- Role-based access control
- API rate limiting
- Token-based authentication

---

## 🎨 UI/UX Highlights

### Admin Panel
- Clean, modern design with Tailwind CSS
- Sidebar navigation
- Color-coded status badges
- Responsive tables
- Image preview for receipts and QR codes

### Mobile App
- Native mobile experience with Ionic
- Tab-based navigation
- Pull-to-refresh functionality
- Step-by-step booking wizard
- Visual status indicators
- Smooth transitions and animations

---

## 📝 Notes

- All monetary values stored as decimal(10,2)
- Booking numbers auto-generated (format: BK{date}{random})
- Transaction numbers auto-generated (format: TXN{date}{random})
- QR codes stored in `storage/app/public/qrcodes/`
- Receipts stored in `storage/app/public/receipts/`
- GCash QR stored in `storage/app/public/gcash/`

---

## 🎓 Learning Resources

- Laravel Documentation: https://laravel.com/docs/10.x
- Ionic Vue Documentation: https://ionicframework.com/docs/vue/overview
- Vue 3 Documentation: https://vuejs.org/guide/introduction.html
- Laravel Sanctum: https://laravel.com/docs/10.x/sanctum

---

**Project Created:** 2025-10-02
**Version:** 1.0.0
**Status:** Complete ✅
