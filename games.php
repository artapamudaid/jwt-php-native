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
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit();
}

$headers = getallheaders();

//check if header authorization is exist
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    exit();
}

//get token
list(, $token) = explode(' ', $headers['Authorization']);

try {
    //decode token and verify
    JWT::decode($token, $_ENV['ACCESS_SECRET_TOKEN'], ['HS256']);
    //get data if token is valid
    $games = [
        [
            'title' => 'Dota 2',
            'genre' => 'Strategy'
        ],
        [
            'title' => 'Ragnarok',
            'genre' => 'Role Playing Game'
        ]
    ];
    echo json_encode($games);
} catch (Exception $e) {
    //if error
    http_response_code(401);
    exit();
}
