<?php


/**
 * @return array
 */
function getDeviceInfo(): object{
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    $osName = "Unknown";
    $osBuildId = "Unknown";
    $deviceType = "Browser";

    // Detect OS and Version
    if (preg_match('/Android/', $userAgent)) {
        $osName = "Android";
        if (preg_match('/Build\/([\w\d\.\-]+)/', $userAgent, $matches)) {
            $osBuildId = $matches[1];
        }
    } elseif (preg_match('/iPhone|iPad|iPod/', $userAgent)) {
        $osName = "iOS"; // Recognizing iOS properly
        if (preg_match('/OS (\d+[_\d]*) like Mac OS X/', $userAgent, $matches)) {
            $osBuildId = str_replace('_', '.', $matches[1]);
        }
    } elseif (preg_match('/Mac OS X/', $userAgent)) {
        $osName = "Mac";
        if (preg_match('/Mac OS X ([\d_]+)/', $userAgent, $matches)) {
            $osBuildId = str_replace('_', '.', $matches[1]);
        }
    } elseif (preg_match('/Windows NT/', $userAgent)) {
        $osName = "Windows";
        if (preg_match('/Windows NT ([\d\.]+)/', $userAgent, $matches)) {
            $osBuildId = "Windows " . $matches[1];
        }
    } elseif (preg_match('/Linux/', $userAgent)) {
        $osName = "Linux";
    }

    // Determine Device Type
    if (preg_match('/iPhone|Android.*Mobile/', $userAgent)) {
        $deviceType = "Phone";
    } elseif (preg_match('/iPad|Tablet|Nexus 7|Nexus 10/', $userAgent) ||
              (preg_match('/Android/', $userAgent) && !preg_match('/Mobile/', $userAgent))) {
        $deviceType = "Tablet";
    }

    return (object)[
        'osName' => $osName,
        'osBuildId' => $osBuildId,
        'deviceType' => $deviceType
    ];
}
