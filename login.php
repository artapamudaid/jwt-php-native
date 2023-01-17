<?php
//import script autoload
require_once('vendor/autoload.php');
//import library
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//response type
header('Content-Type: application/json');

//check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit();
}

//Get JSON
$json = file_get_contents('php://input');

//decode the JSON
$input = json_decode($json);

//check email and password
if (!isset($input->email) || !isset($input->password)) {
    http_response_code(400);
    exit();
}

//user data (for example)
$user = [
    'email' => 'arta@mail.com',
    'password' => 'rahasia123'
];

//if doesn't match email or password 
if ($input->email !== $user['email'] || $input->password !== $user['password']) {
    echo json_encode(
        [
            'success' => 'false',
            'data'    => null,
            'message' => 'Wrong email or password',
        ]
    );
    exit();
}

//create expire time
$expire_time = time() + (15 * 60);

$payload = [
    'email' => $input->email,
    'exp' => $expire_time,
];

//generate access token
$access_token = JWT::encode($payload, $_ENV['ACCESS_SECRET_TOKEN'], 'HS256');

//json response
echo json_encode([
    'success' => true,
    'accessToken' => $access_token,
    'exp' => date(DATE_ISO8601, $expire_time),
]);

//change expire time additional time 1 hour
$payload['exp'] = time() + (60 * 60);
$refresh_token = JWT::encode($payload, $_ENV['REFRESH_SECRET_TOKEN'], 'HS256');

//save refresh token in http-only cookie
setcookie('refreshToken', $refresh_token, $payload['exp'], '', '', false, true);
