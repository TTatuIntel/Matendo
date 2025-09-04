@echo off
echo ================================================================
echo              MCares Admin Users - Quick Test
echo ================================================================
echo.

echo Starting Laravel server...
start /B php artisan serve --host=127.0.0.1 --port=8000
timeout /t 3 > nul

echo.
echo Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear

echo.
echo Running migrations...
php artisan migrate --force

echo.
echo Creating admin users...
php artisan db:seed --class=AdminUserSeeder

echo.
echo ================================================================
echo                     TESTING INSTRUCTIONS
echo ================================================================
echo.
echo 1. Open: http://127.0.0.1:8000/login
echo 2. Login with: admin@mcares.com / admin123
echo 3. Navigate to: Admin Dashboard > Users
echo 4. Test these features:
echo    - Create new user (click Add New User)
echo    - Edit existing user (click edit icon)
echo    - Toggle user status (click status icon)
echo    - Delete user (click delete icon)
echo    - Search users (use search box)
echo    - Filter users (use role/status dropdowns)
echo    - Export users (click Export Users)
echo.
echo ================================================================
echo                     EXPECTED RESULTS
echo ================================================================
echo.
echo ✅ All modals should open properly
echo ✅ Forms should submit with success messages
echo ✅ Loading states should show during operations
echo ✅ Toast notifications should appear
echo ✅ Data should persist after operations
echo ✅ Search and filters should work instantly
echo.
echo If you see any errors, check the browser console (F12)
echo.
pause
