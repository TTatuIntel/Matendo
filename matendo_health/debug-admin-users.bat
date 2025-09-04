@echo off
echo ================================================================
echo            MCares Admin Users - Debug Script
echo ================================================================
echo.

echo Starting Laravel server...
start /B php artisan serve --host=127.0.0.1 --port=8000
timeout /t 3 > nul

echo.
echo Testing route list for admin users...
php artisan route:list --name=admin.users

echo.
echo Testing database connection...
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database: Connected' . PHP_EOL; } catch(Exception $e) { echo 'Database Error: ' . $e->getMessage() . PHP_EOL; }"

echo.
echo Testing user model...
php artisan tinker --execute="echo 'User Count: ' . App\Models\User::count() . PHP_EOL;"

echo.
echo Testing admin middleware...
php artisan route:list --name=admin.users | findstr "admin.users"

echo.
echo ================================================================
echo                    MANUAL TEST INSTRUCTIONS
echo ================================================================
echo.
echo 1. Open browser: http://127.0.0.1:8000/login
echo 2. Login with: admin@mcares.com / admin123
echo 3. Go to: http://127.0.0.1:8000/admin/users
echo 4. Open browser console (F12) and check for errors
echo 5. Try clicking each action button and check console for:
echo    - JavaScript errors
echo    - AJAX request failures
echo    - Network errors
echo.
echo 6. Expected behavior:
echo    ✅ View button: Should redirect to user details
echo    ✅ Edit button: Should open modal with user data
echo    ✅ Status button: Should toggle user status with notification
echo    ✅ Delete button: Should open confirmation modal
echo.
echo ================================================================
echo                      COMMON ISSUES & FIXES
echo ================================================================
echo.
echo ISSUE: "Function not defined" errors
echo FIX: Check browser console, functions should be global
echo.
echo ISSUE: "CSRF token mismatch" 
echo FIX: Ensure meta tag exists in layout head
echo.
echo ISSUE: "404 Not Found" on actions
echo FIX: Check route:list output above for correct routes
echo.
echo ISSUE: Modal not opening
echo FIX: Check if modal div exists in DOM
echo.
echo ISSUE: Buttons not responding
echo FIX: Check onclick attributes in generated HTML
echo.
echo ================================================================
echo.
pause
