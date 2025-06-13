<?php
function pretty_error($errorMessage, $errorHeader = "Fatal Error")
{
    // Store raw message before applying HTML escaping
    $rawError = $errorMessage;

    // Escape HTML for web display (DO NOT escape for console)
    $escapedError = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8');

    // Detect if it's a MySQL error
    $isMySQLError = preg_match('/SQLSTATE\[\w+\]/', $errorMessage);

    // Apply styling for web display
    $escapedError = preg_replace('/(SQLSTATE\[\w+\])/', '<span style="color: #e83e8c; font-weight: bold;">$1</span>', $escapedError);
    $escapedError = preg_replace('/(\/[\/\w\.-]+\.php)(?::(\d+))?/', '<span style="color: #17a2b8;">$1</span>:<span style="color: #ffc107;">$2</span>', $escapedError);
    $escapedError = preg_replace('/(Fatal error|Uncaught \w+:)/', '<span style="color: #dc3545; font-weight: bold;">$1</span>', $escapedError);
    $escapedError = preg_replace('/(#\d+)/', '<span style="color: #28a745; font-weight: bold;">$1</span>', $escapedError);

    // Extract SQLSTATE error code and message
    if ($isMySQLError) {
        preg_match('/SQLSTATE\[(\w+)\]: (.*)/', $errorMessage, $matches);
        $sqlStateCode = $matches[1] ?? 'Unknown';
        $sqlErrorMessage = $matches[2] ?? 'No additional information.';

        $escapedError = "
        <div style='padding: 10px; background: #222; border-radius: 5px; display:flex; flex-direction: column;'>
            <p style='color: #ffc107;'>SQLSTATE Code: <strong>$sqlStateCode</strong></p>
            <p style='color:rgb(110, 166, 205);'>SQL Message: <div style='background:rgb(28, 27, 27); border-radius: 10px; padding: 10px; border:1px solid #111111;'>$sqlErrorMessage</div></p>
        </div>";
    }

    // Output the formatted error message
    echo <<<HTML
    <html>
    <head>
        <style>

        body, html {
            padding: 0;
            margin: 0;
            width: 100%;
            height: 100%;
            overflow:auto;
            overflow-x: hidden;
            background: #000000;
            font-family: Arial, sans-serif;
        }

        .error-container {
            width: 100%;
            height: 100vh;
            background: #111;
            color: #fff;
            padding: 5px 20px 20px 5px;
            box-sizing: border-box;
        }

        .error-box {
            background: #222;
            padding: 5px;
            border-radius: 5px;
            font-size: 14px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-wrap: break-word;
            border-radius: 5px;
        }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1 style="color:rgb(113, 43, 41); text-align: center;"><? echo $errorHeader; ?></h1>
            <div class="error-box">$escapedError</div>            
        </div>
    </body>
    </html>
    HTML;

    // **Fix:** Log raw error to console with `html_entity_decode()` to prevent HTML encoding issues
    echo '<script>console.error(' . json_encode(html_entity_decode($rawError), JSON_UNESCAPED_SLASHES) . ');</script>';
}
