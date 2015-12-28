<?php
require("inc/jwt.php");

$auth_issuer = "/auth";
$auth_url = "/backend/auth.php";

$clients = [
    "d6d2b510d18471d2e22aa202216e86c42beac80f9a6ac2da505dcb79c7b2fd99" => [
        "/frontend/app/real.html" => [
            "token" => [
                "key" => "12345678",
                "aud" => "/app/frontend/"
            ]
        ],
    ]    
];

$users = [
    "tlb test" => [
        "sub" => "tlb",
    ]
];


if(startsWith($_SERVER["REQUEST_URI"], $auth_url . "/oauth/authorize")) {
    // Try to look up client to see if we have this client_id and redirect uri 
    if(   isset($_GET["client_id"])
       && isset($_GET["redirect_url"])
       && isset($_GET["response_type"])
       && isset($clients[$_GET["client_id"]][$_GET["redirect_uri"]][$_GET["response_type"]])) {
       $client = $clients[$_GET["client_id"]][$_GET["redirect_uri"]][$_GET["response_type"]];

        $scope = isset($_GET["scope"]) ? $_GET["scope"]) : '';
        $state = isset($_GET["state"]) ? $_GET["state"] : '';

$form = <<<SIGNINFORM
<html>
  <body>
    <form accept-charset="UTF-8" action="$auth_url/oauth/authorize" method="post" >
      <input name="viewstate" type="hidden" value="TODO:___viewstate___">
      <input name="client_id" type="hidden" value="{$_GET["client_id"]}">
      <input name="redirect_uri" type="hidden" value="{$_GET["redirect_uri"]}">
      <input name="response_type" type="hidden" value="{$_GET["response_type"]}">
      <label for="username">Username</label>
      <input id="username" name="username" size="30" type="text" value="" autocomplete="off">
      <label for="password">Password</label>
      <input id="password" name="password" size="30" type="password" autocomplete="off">
      <input name="commit" type="submit" value="Sign in">
    </form>
  </body>
</html>
SIGNINFORM;
        echo $form;
        
    } elseif(
	   isset($_POST["client_id"])
        && isset($_POST["redirect_uri"])
        && isset($_POST["response_type"])
        && isset($client = $clients[$_POST["client_id"]][$_POST["redirect_uri"]][$_POST["response_type"]])) {
	
	$client = $clients[$_POST["client_id"]][$_POST["redirect_uri"]][$_POST["response_type"]];
        $scope = isset($_GET["scope"]) ? $_GET["scope"]) : (isset($_POST["scope"]) ? $_POST["scope"] : '');
        $state = isset($_GET["state"]) ? $_GET["state"] : (isset($_POST["state"]) ? $_POST["state"] : '');

        // Validate username passwor,d, TODO: Passwords should be hashed
        if($user = $users[$_POST["username"]." ".$_POST["password"]]) {
            
            // HS256 (HmacSHA256) with shared key
            $jwt_token = jwt_encode(
                [
                    "typ" => "JWT",
                    "alg" => "HS256"
                ],
                [
                    // Authority information
                    "jti" => uniqid("a:"), // Create a unique ID and add a prefix to for the node creating it
                    "iss" => $auth_issuer,
                    "aud" => $client["aud"],
                    "iat" => time(),
                    "nbf" => time() - 60* 5, // 5 minutes skrew
                    "exp" => time() + 60 * 10, // expires in 10 minutes
                    // User information
                    "sub" => $user["sub"]
                ],
            $client["key"]);
            
            //$token = jwt_decode($jwt_token, "12345678", "http://localhost/app/frontend/");
            //var_dump($token);
            
            header("Location: {$_POST["redirect_uri"]}#access_token={$jwt_token}&token_type=bearer&expires_in=600&state=/");
            
        } else {
            die("Unknown username or password");
        }
        
    } else {
        die("Did not find client_id or redirect_uri");
    }
} else {
    die("Unknown request");
}

/*
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
        "iss" => "/auth",
        "aud" => "/roprotokol",
        "iat" => time(),
        "nbf" => time() - 60* 5, // 5 minutes skrew
        "exp" => time() + 60 * 10, // expires in 10 minutes
        // User information
        "sub" => "admin"
    ],
$pkeyid);
var_dump($jwt_token);
$pubkey = openssl_get_publickey(file_get_contents("file://".dirname(__FILE__)."/certs/example.org.crt"));
$token = jwt_decode($jwt_token, $pubkey, "/roprotokol");
var_dump($token);
*/

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
