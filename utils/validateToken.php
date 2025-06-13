<?php
function validateToken($token)
{
    $secretKey = $_ENV['UUIDKEY'];

    $parts = explode('-', $token);
    if (count($parts) < 3) {
        return false;  // Invalid format
    }

    list($randomPart, $userUUID, $providedSignature) = $parts;

    // Recalculate the expected signature
    $expectedSignature = hash_hmac('sha256', $randomPart . $userUUID, $secretKey);

    // Compare signatures to detect tampering
    if (!hash_equals($expectedSignature, $providedSignature)) {
        return false;  // Token has been modified or is invalid
    }

    return $userUUID;  // Successfully validated, return user UUID
}
