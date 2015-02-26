<?php
require("inc/jwt.php");
header('Content-type: application/json');

if(isset($_GET["username"])) {
    $username = $_GET("username");
}

// HS256 (HmacSHA256) with shared key
$jwt_token = jwt_encode(
    [
        "typ" => "JWT",
        "alg" => "HS256"
    ],
    [
        // Authority information
        "jti" => uniqid("a:"), // Create a unique ID and add a prefix to for the node creating it
        "iss" => "http://localhost/auth",
        "aud" => "http://localhost/roprotokol",
        "iat" => time(),
        "nbf" => time() - 60* 5, // 5 minutes skrew
        "exp" => time() + 60 * 10, // expires in 10 minutes
        // User information
        "sub" => "admin"
    ],
"12345678");
var_dump($jwt_token);
$token = jwt_decode($jwt_token, "12345678");
var_dump($token);

// RS256 (SHA256withRSA RSA2048bit:z4) with private key
$pkeyid = openssl_pkey_get_private("file://".dirname(__FILE__)."/certs/example.org.pem");
$jwt_token = jwt_encode(
    [
        "typ" => "JWT",
        "alg" => "RS256"
    ],
    [
        // Authority information
        "jti" => uniqid("a:"), // Create a unique ID and add a prefix to for the node creating it
        "iss" => "http://localhost/auth",
        "aud" => "http://localhost/roprotokol",
        "iat" => time(),
        "nbf" => time() - 60* 5, // 5 minutes skrew
        "exp" => time() + 60 * 10, // expires in 10 minutes
        // User information
        "sub" => "admin"
    ],
$pkeyid);
var_dump($jwt_token);
$pubkey = openssl_get_publickey(file_get_contents("file://".dirname(__FILE__)."/certs/example.org.crt"));
$token = jwt_decode($jwt_token, $pubkey, "http://localhost/roprotokol");
var_dump($token);

