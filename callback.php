<?php
require_once 'config.php';
require_once 'db.php';

if (isset($_GET['error'])) {
    die("Google Authentication Error: " . htmlspecialchars($_GET['error']));
}

if (!isset($_GET['code'])) {
    header("Location: index.php");
    exit();
}

$code = $_GET['code'];

// Exchange code for token
$token_url = 'https://oauth2.googleapis.com/token';
$post_data = [
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Depending on local XAMPP setup, SSL verification might fail. 
// For local development only, we can set this to false, but be careful in production.
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
$response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($response, true);

if (isset($token_data['error'])) {
    die("Error fetching access token: " . htmlspecialchars($token_data['error_description'] ?? $token_data['error']));
}

$access_token = $token_data['access_token'];

// Get user profile info
$user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $user_info_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$user_response = curl_exec($ch);
curl_close($ch);

$google_user = json_decode($user_response, true);

if (!isset($google_user['email'])) {
    die("Could not retrieve email address from Google.");
}

$email = $google_user['email'];

// Check for domain or admin
if (strtolower($email) !== strtolower(ADMIN_EMAIL)) {
    $domain = substr(strrchr($email, "@"), 1);
    if ($domain !== ALLOWED_DOMAIN) {
        die("<h1>Access Denied</h1><p>You must use an @" . ALLOWED_DOMAIN . " email address to access this portal.</p><a href='index.php'>Go Back</a>");
    }
}

// User is allowed, log them in / insert into db
$google_id = $google_user['id'];
$name = $google_user['name'];
$picture = $google_user['picture'] ?? null;
$role = (strtolower($email) === strtolower(ADMIN_EMAIL)) ? 'admin' : 'student';

// Check if user exists
$stmt = $pdo->prepare("SELECT id, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    // Update user info
    $stmt = $pdo->prepare("UPDATE users SET google_id = ?, name = ?, picture = ?, role = ? WHERE email = ?");
    $stmt->execute([$google_id, $name, $picture, $role, $email]);
    $user_id = $user['id'];
} else {
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (google_id, email, name, picture, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$google_id, $email, $name, $picture, $role]);
    $user_id = $pdo->lastInsertId();
}

$_SESSION['user_id'] = $user_id;
$_SESSION['email'] = $email;
$_SESSION['name'] = $name;
$_SESSION['role'] = $role;

if ($role === 'admin') {
    header("Location: admin.php");
} else {
    header("Location: dashboard.php");
}
exit();
?>
