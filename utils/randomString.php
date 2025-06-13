<?php

function randomString($length, $chars)
{
    if (!$chars) {
        throw new Exception("Argument 'chars' is undefined");
    }

    $charsLength = strlen($chars);
    if ($charsLength > 256) {
        throw new Exception("Argument 'chars' should not have more than 256 characters, otherwise unpredictability will be broken");
    }

    $randomBytes = random_bytes($length);
    $result      = '';

    for ($i = 0; $i < $length; $i++) {
        $index   = ord($randomBytes[$i]) % $charsLength;
        $result .= $chars[$index];
    }

    return $result;
}
