# TappyPass Deployment Guide

## 📋 Pre-Deployment Checklist

### Backend Requirements
- [ ] PHP 8.1+ installed on server
- [ ] MySQL 5.7+ or MariaDB
- [ ] Composer installed
- [ ] Web server (Apache/Nginx)
- [ ] SSL certificate
- [ ] Domain name configured

### Mobile App Requirements
- [ ] Node.js 16+ and npm
- [ ] Ionic CLI installed globally
- [ ] Android Studio (for Android build)
- [ ] Xcode (for iOS build, macOS only)
- [ ] Google Play Developer account (for Android)
- [ ] Apple Developer account (for iOS)

---

## 🚀 Backend Deployment (Laravel)

### Option 1: Shared Hosting (cPanel)

1. **Upload Files**
   ```bash
   # Zip your project (exclude node_modules, vendor)
   zip -r tappypass.zip . -x "node_modules/*" "vendor/*" ".git/*"
   ```
   - Upload via cPanel File Manager
   - Extract in public_html or subdirectory

2. **Install Dependencies**
   ```bash
   cd /home/username/public_html/tappypass
   composer install --optimize-autoloader --no-dev
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Update `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=AdminSeeder
   ```

5. **Set Permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

6. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

7. **Optimize for Production**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

8. **Configure .htaccess** (if using Apache)
   
   In `public/.htaccess`, ensure:
   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ index.php [QSA,L]
   </IfModule>
   ```

### Option 2: VPS/Cloud Server (Ubuntu)

1. **Install Dependencies**
   ```bash
   sudo apt update
   sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-gd php8.1-zip
   sudo apt install mysql-server nginx composer
   ```

2. **Clone Repository**
   ```bash
   cd /var/www
   git clone your-repo-url tappypass
   cd tappypass
   ```

3. **Install & Configure**
   ```bash
   composer install --optimize-autoloader --no-dev
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Nginx**
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com;
       root /var/www/tappypass/public;
   
       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";
   
       index index.php;
   
       charset utf-8;
   
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
   
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   
       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

5. **Set Permissions**
   ```bash
   sudo chown -R www-data:www-data /var/www/tappypass
   sudo chmod -R 755 /var/www/tappypass/storage
   sudo chmod -R 755 /var/www/tappypass/bootstrap/cache
   ```

6. **Setup SSL with Let's Encrypt**
   ```bash
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d yourdomain.com
   ```

7. **Run Migrations**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=AdminSeeder
   php artisan storage:link
   ```

8. **Optimize**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 📱 Mobile App Deployment

### Update Configuration

1. **Update API URL**
   
   Edit `TappyPass-mobile/src/services/api.ts`:
   ```typescript
   const API_URL = 'https://yourdomain.com/api';
   ```

2. **Update App Information**
   
   Edit `TappyPass-mobile/capacitor.config.ts`:
   ```typescript
   const config: CapacitorConfig = {
     appId: 'com.yourcompany.tappypass',
     appName: 'TappyPass',
     webDir: 'dist',
     server: {
       androidScheme: 'https'
     }
   };
   ```

3. **Update package.json**
   ```json
   {
     "name": "tappypass-mobile",
     "version": "1.0.0",
     "description": "TappyPass Bus Booking App"
   }
   ```

### Build for Android

1. **Build Web Assets**
   ```bash
   cd TappyPass-mobile
   npm install
   npm run build
   ```

2. **Add Android Platform**
   ```bash
   ionic capacitor add android
   ```

3. **Sync Changes**
   ```bash
   ionic capacitor sync android
   ```

4. **Open in Android Studio**
   ```bash
   ionic capacitor open android
   ```

5. **Configure App**
   - Update `android/app/build.gradle`:
     ```gradle
     android {
         defaultConfig {
             applicationId "com.yourcompany.tappypass"
             versionCode 1
             versionName "1.0.0"
         }
     }
     ```

6. **Generate Signed APK**
   - In Android Studio: Build → Generate Signed Bundle/APK
   - Create keystore if needed
   - Select release build variant
   - Sign with your keystore

7. **Upload to Google Play**
   - Go to Google Play Console
   - Create new app
   - Upload APK/AAB
   - Fill in store listing
   - Submit for review

### Build for iOS

1. **Build Web Assets**
   ```bash
   npm run build
   ```

2. **Add iOS Platform**
   ```bash
   ionic capacitor add ios
   ```

3. **Sync Changes**
   ```bash
   ionic capacitor sync ios
   ```

4. **Open in Xcode**
   ```bash
   ionic capacitor open ios
   ```

5. **Configure App**
   - Update Bundle Identifier
   - Set Team (Apple Developer account)
   - Configure signing certificates
   - Update version and build number

6. **Archive and Upload**
   - Product → Archive
   - Distribute App → App Store Connect
   - Upload to App Store

7. **Submit to App Store**
   - Go to App Store Connect
   - Create new app
   - Fill in app information
   - Upload screenshots
   - Submit for review

---

## 🔧 Post-Deployment Configuration

### Backend

1. **CORS Configuration**
   
   Update `config/cors.php`:
   ```php
   'paths' => ['api/*', 'sanctum/csrf-cookie'],
   'allowed_origins' => [
       'https://yourdomain.com',
       'capacitor://localhost', // For mobile app
       'ionic://localhost',
   ],
   'supports_credentials' => true,
   ```

2. **File Upload Limits**
   
   Update `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

3. **Setup Cron Jobs** (if needed)
   ```bash
   * * * * * cd /var/www/tappypass && php artisan schedule:run >> /dev/null 2>&1
   ```

4. **Setup Queue Workers** (if needed)
   ```bash
   sudo nano /etc/supervisor/conf.d/tappypass-worker.conf
   ```
   ```ini
   [program:tappypass-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/tappypass/artisan queue:work --sleep=3 --tries=3
   autostart=true
   autorestart=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/www/tappypass/storage/logs/worker.log
   ```

### Mobile App

1. **Test on Real Devices**
   - Install on Android device
   - Install on iOS device
   - Test all features
   - Check API connectivity

2. **Monitor Crashes**
   - Set up Firebase Crashlytics (optional)
   - Monitor Google Play Console
   - Monitor App Store Connect

---

## 🔒 Security Hardening

### Backend

1. **Hide Laravel Version**
   ```bash
   # Remove X-Powered-By header
   # In nginx config:
   fastcgi_hide_header X-Powered-By;
   ```

2. **Disable Directory Listing**
   ```apache
   Options -Indexes
   ```

3. **Protect .env File**
   ```apache
   <Files .env>
       Order allow,deny
       Deny from all
   </Files>
   ```

4. **Enable Rate Limiting**
   
   Already configured in `routes/api.php` with throttle middleware

5. **Regular Updates**
   ```bash
   composer update
   php artisan migrate
   ```

### Mobile App

1. **Enable SSL Pinning** (Advanced)
2. **Obfuscate Code** (ProGuard for Android)
3. **Secure Storage** for tokens
4. **Regular Updates** via app stores

---

## 📊 Monitoring & Maintenance

### Backend Monitoring

1. **Error Logging**
   - Check `storage/logs/laravel.log`
   - Set up log rotation
   - Consider external logging (Papertrail, Loggly)

2. **Performance Monitoring**
   - Use Laravel Telescope (dev only)
   - Monitor database queries
   - Check server resources

3. **Backup Strategy**
   ```bash
   # Database backup
   mysqldump -u username -p database_name > backup.sql
   
   # File backup
   tar -czf backup.tar.gz /var/www/tappypass
   ```

### Mobile App Monitoring

1. **Analytics**
   - Google Analytics for Firebase
   - Track user engagement
   - Monitor conversion rates

2. **Crash Reporting**
   - Firebase Crashlytics
   - Sentry

3. **User Feedback**
   - In-app feedback form
   - App store reviews
   - Support email

---

## 🆘 Troubleshooting

### Common Issues

**500 Internal Server Error**
- Check storage permissions
- Clear cache: `php artisan cache:clear`
- Check error logs

**CORS Errors**
- Verify CORS configuration
- Check allowed origins
- Ensure credentials support

**Database Connection Failed**
- Verify database credentials
- Check database server status
- Test connection manually

**Mobile App Can't Connect**
- Verify API URL is correct
- Check SSL certificate
- Test API endpoints with Postman

**File Upload Fails**
- Check PHP upload limits
- Verify storage permissions
- Check disk space

---

## 📞 Support & Resources

- Laravel Documentation: https://laravel.com/docs
- Ionic Documentation: https://ionicframework.com/docs
- Capacitor Documentation: https://capacitorjs.com/docs

---

**Last Updated:** 2025-10-02
**Version:** 1.0.0
