<?php

$publicKeyPem = file_get_contents(BASE_PATH . '/private/public.key');

if (!$publicKeyPem) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to load public key"]);
    exit;
}

// Load key using OpenSSL
$res = openssl_pkey_get_public($publicKeyPem);
if (!$res) {
    http_response_code(500);
    echo json_encode(["error" => "Invalid public key format"]);
    exit;
}

$keyDetails = openssl_pkey_get_details($res);
$n = $keyDetails['rsa']['n'];
$e = $keyDetails['rsa']['e'];

// Base64url-encode
function base64url($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

$jwks = [
    "keys" => [[
        "kty" => "RSA",
        "kid" => "1",
        "use" => "sig",
        "alg" => "RS256",
        "n" => base64url($n),
        "e" => base64url($e),
    ]]
];

header('Content-Type: application/json');
echo json_encode($jwks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
