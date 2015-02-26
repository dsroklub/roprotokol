<?php

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

function jwt_decode($jwt, $key, $aud = null) {
    if(substr_count($jwt, '.') !== 2) {
        return [ "error" => 'Not a valid JWT token as it does not contain two dots' ];
    }

    // Disable token and deserialize
    $elements = explode('.', $jwt);
    $header = json_decode(base64url_decode($elements[0]), true, 2, JSON_BIGINT_AS_STRING);
    $body = json_decode(base64url_decode($elements[1]), true, 2, JSON_BIGINT_AS_STRING);
    $signature = base64url_decode($elements[2]);
    $base64_token = $elements[0].".".$elements[1];

    // HASH validation
    if(substr($header['alg'], 0, 2) === "HS") {
        $checksignature = jwt_sign($base64_token, $header['alg'], $key);
        if($signature === false || $checksignature === false || !strcmp_timesafe($signature, $checksignature)) {
            $body["error"] = "Validation failed";
            return $body;
        }

    // Priv/Pub key validation
    } elseif(substr($header['alg'], 0, 2) === "RS") {
        if($header['alg'] === "RS256") {
            if(!openssl_verify($base64_token, $signature, $key, "SHA256")) {
                $body["error"] = "Validation failed";
                return $body;
            }

        } else {
            $body["error"] = "Unknown algorithm used";
            return $body;
        }

    } else {
        $body["error"] = "Unknown algorithm used";
        return $body;
    }

    // Validate body
    if(isset($aud) && (!isset($body['aud']) || $aud != $body['aud'])) {
        $body["error"] = "Audience not set in token or did not match : $aud";
        return $body;
    }
    if(isset($body['nbf']) && $body['nbf'] >= time()) {
        $body["error"] = "Token is not valid yet";
        return $body;
    }
    if(isset($body['exp']) && $body['exp'] <= time()) {
        $body["error"] = "Token has expired";
        return $body;
    }

    return $body;
}

function jwt_encode($header, $body, $key) {
    // Encode header and body
    $base64_token = base64url_encode(json_encode($header)) . "." . base64url_encode(json_encode($body));
    // Sign token and assemble the final jwt
    return $base64_token . "." . base64url_encode(jwt_sign($base64_token, $header['alg'], $key));
}

function jwt_sign($base64_token, $algo, $key) {
    switch($algo) {
    case 'HS256':
        return hash_hmac("SHA256", $base64_token, $key, true);
    case 'HS384':
        return hash_hmac("SHA384", $base64_token, $key, true);
    case 'HS512':
        return hash_hmac("SHA512", $base64_token, $key, true);
    case 'RS256':
        $signature = '';
        $success = openssl_sign($base64_token, $signature, $key, "SHA256");
        return $signature;
    default:
        return false;
    }
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function strcmp_timesafe($a, $b) {
    if (!is_string($a) || !is_string($b)) {
        return false;
    }

    $len = strlen($a);
    if ($len !== strlen($b)) {
        return false;
    }

    $status = 0;
    for ($i = 0; $i < $len; $i++) {
        $status |= ord($a[$i]) ^ ord($b[$i]);
    }
    return $status === 0;
}
