<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Matendo - Hire Medical Professionals</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f9f9f9; }
        .header { background-color: #f0f0f0; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .header .logo { font-size: 1.5em; font-weight: bold; color: #333; }
        .header nav a { margin-left: 20px; text-decoration: none; color: #333; }
        .header .auth-buttons a { margin-left: 10px; padding: 5px 15px; text-decoration: none; }
        .header .join-btn { background-color: #28a745; color: white; border-radius: 5px; }
        .header .login-btn { background-color: #007bff; color: white; border-radius: 5px; }
        .header .dashboard-btn { background-color: #6c757d; color: white; border-radius: 5px; }
        .hero-section { padding: 50px 20px; max-width: 800px; margin: 0 auto; text-align: center; }
        .hero-section h1 { font-size: 2em; margin: 0 0 20px; }
        .hero-section p { font-size: 1.1em; color: #555; margin-bottom: 30px; }
        .hero-section .btn { background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .divider { border-top: 1px solid #ddd; margin: 30px auto; width: 100px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">MATENDO<sup style="color: red;">&hearts;</sup></div>
        <nav>
            <a href="#">Careers</a>
            <a href="#">About Us</a>
            <a href="#">Contact Us</a>
        </nav>
        <div class="auth-buttons">
            @auth
                <a href="{{ url('/dashboard') }}" class="dashboard-btn">Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="join-btn">Join Matendo</a>
                <a href="{{ route('login') }}" class="login-btn">Login</a>
            @endauth
        </div>
    </div>

    <div class="hero-section">
        <h1>Hire Medical Professionals</h1>
        <p>Matendo provides fast, reliable access to certified doctors and medical professionals, ready to assist with your healthcare needs. Whether you're looking for advice, second opinions, or specialized care, Matendo connects you to the best experts in the field.</p>
        <div class="divider"></div>
        @auth
            <a href="{{ url('/dashboard') }}" class="btn">Go to Dashboard</a>
        @else
            <a href="{{ route('register') }}" class="btn">Get Started</a>
        @endauth
    </div>
</body>
</html>
