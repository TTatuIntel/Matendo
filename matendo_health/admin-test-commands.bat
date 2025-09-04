@echo off
echo ================================================================
echo              MCares Admin System Testing Commands
echo ================================================================
echo.

echo 1. Starting Laravel Development Server...
echo.
start /B php artisan serve --host=127.0.0.1 --port=8000
timeout /t 3 > nul

echo 2. Clearing Application Cache...
echo.
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo 3. Running Database Migrations...
echo.
php artisan migrate --force

echo.
echo 4. Creating Admin Users (Seeding)...
echo.
php artisan db:seed --class=AdminUserSeeder

echo.
echo 5. Optimizing Application...
echo.
php artisan config:cache
php artisan route:cache

echo.
echo 6. Checking Database Connection...
echo.
php artisan tinker --execute="echo 'Database Connected: ' . (DB::connection()->getPdo() ? 'YES' : 'NO') . PHP_EOL;"

echo.
echo 7. Verifying Admin Users...
echo.
php artisan tinker --execute="echo 'Admin Users Count: ' . App\Models\User::where('role', 'admin')->count() . PHP_EOL;"

echo.
echo ================================================================
echo                     Testing URLs Available
echo ================================================================
echo.
echo Main Application: http://127.0.0.1:8000
echo Admin Login: http://127.0.0.1:8000/login
echo Admin Dashboard: http://127.0.0.1:8000/admin/dashboard
echo.
echo ================================================================
echo                      Admin Login Credentials
echo ================================================================
echo.
echo System Administrator:
echo   Email: admin@mcares.com
echo   Password: admin123
echo.
echo Test Administrator:
echo   Email: test.admin@mcares.com  
echo   Password: test123
echo.
echo Sample Doctor:
echo   Email: doctor@mcares.com
echo   Password: doctor123
echo.
echo Sample Patient:
echo   Email: patient@mcares.com
echo   Password: patient123
echo.
echo ================================================================
echo                    Admin Features to Test
echo ================================================================
echo.
echo 1. Dashboard Analytics and Real-time Data
echo 2. User Management (Create, Read, Update, Delete)
echo 3. Doctor Management and Verification
echo 4. Patient Management and Medical Records
echo 5. System Reports and Analytics
echo 6. Security Management and Audit Logs
echo 7. System Settings and Configuration
echo 8. Backup and Restore Functionality
echo 9. Alert Management and Monitoring
echo 10. External Access Management
echo.
echo ================================================================
echo                      System Status Check
echo ================================================================
echo.

echo Checking Laravel Version...
php artisan --version

echo.
echo Checking Database Tables...
php artisan tinker --execute="
try {
    \$tables = ['users', 'doctors', 'patients', 'appointments', 'vital_signs', 'documents', 'alerts', 'activity_logs'];
    foreach (\$tables as \$table) {
        \$count = DB::table(\$table)->count();
        echo \"Table: {\$table} - Records: {\$count}\" . PHP_EOL;
    }
    echo 'All core tables verified!' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Database Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo ================================================================
echo                       Ready for Testing!
echo ================================================================
echo.
echo The MCares Admin System is now ready for comprehensive testing.
echo Please open your browser and navigate to: http://127.0.0.1:8000
echo.
echo Press any key to open the application in your default browser...
pause > nul

start http://127.0.0.1:8000

echo.
echo Testing environment is now active!
echo Press Ctrl+C to stop the server when testing is complete.
echo.
