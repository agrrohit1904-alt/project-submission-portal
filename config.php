<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Google OAuth 2.0 Settings (loaded from Environment Variables or fall back to placeholders)
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET');

// Calculate Redirect URI
if (getenv('GOOGLE_REDIRECT_URI')) {
    define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI'));
} else {
    // Autodetect scheme handling reverse proxies (Render SSL termination)
    $scheme = 'http';
    if ((!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        ($_SERVER['SERVER_PORT'] ?? 80) == 443) {
        $scheme = 'https';
    }
    
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = dirname($_SERVER['PHP_SELF'] ?? '/');
    $dir = str_replace(' ', '%20', $dir);
    $dir = rtrim($dir, '/\\');
    
    define('GOOGLE_REDIRECT_URI', $scheme . '://' . $host . $dir . '/callback.php');
}

// Allowed Domain & Admin Settings
define('ALLOWED_DOMAIN', getenv('ALLOWED_DOMAIN') ?: 'skit.ac.in');
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'B251692@skit.ac.in');
?>
