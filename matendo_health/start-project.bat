@echo off
echo Starting MCares Medical Monitoring System...
echo.

echo 1. Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo.
echo 2. Running migrations...
php artisan migrate --force

echo.
echo 3. Seeding sample data (if needed)...
php artisan db:seed --class=SampleDataSeeder

echo.
echo 4. Starting Laravel development server...
echo.
echo The application will be available at: http://127.0.0.1:8000
echo.
echo Sample Login Credentials:
echo Admin: admin@mcares.test / password
echo Doctor: doctor1@mcares.test / password  
echo Patient: patient1@mcares.test / password
echo.
echo Press Ctrl+C to stop the server
echo.

php artisan serve
