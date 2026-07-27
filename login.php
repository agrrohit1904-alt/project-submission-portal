<?php
require_once 'config.php';

$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth';
$params = [
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'email profile',
    'access_type'   => 'online',
    'prompt'        => 'select_account' // Force account selection
];

$redirect_url = $auth_url . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

header("Location: $redirect_url");
exit();
?>
