<?php

require_once (UTILS . 'randomString.php');
require_once (UTILS . 'generateUUID.php');

function generateToken($length = 50)
{
    $secretKey  = $_ENV['UUIDKEY'];  // 🔥 Keep this secret (not in the codebase!)
    $userUUID   = generateUUID();
    // Generate a secure random string
    $randomPart = randomString($length, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.$-_!');

    // Create an HMAC signature to prevent tampering
    $signature = hash_hmac('sha256', $randomPart . $userUUID, $secretKey);

    return $randomPart . '-' . $userUUID . '-' . $signature;
}
