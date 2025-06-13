<?php

$hasError = false;

/**
 * Custom error handler to display PHP errors in a styled format.
 */
function prettyErrorHandler(int $errno, string $errstr, string $errfile, int $errline): void
{
    global $hasError;
    $hasError = true;
    // Ensure the response is interpreted as HTML
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }

    $errorMessage = <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP Error</title>
        <style>
            body, html {
                padding: 0;
                margin: 0;
                width: 100%;
                height: 100%;
                background: #111;
                color: #fff;
                font-family: Arial, sans-serif;
            }
            .error-container {
                padding: 20px;
            }
            pre {
                background: #222;
                color: #fff;
                padding: 10px;
                border-radius: 5px;
                white-space: pre-wrap;
            }
            .error-title { color: #dc3545; font-weight: bold; }
            .error-file { color: #17a2b8; }
            .error-line { color: #ffc107; }
            .error-code { color: #28a745; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1 class="error-title">PHP Error</h1>
            <pre>
                <span class="error-title">Error:</span> $errstr
                <br>
                <span class="error-file">File:</span> $errfile
                <br>
                <span class="error-line">Line:</span> $errline
                <br>
                <span class="error-code">Error Code:</span> $errno
            </pre>
        </div>
    </body>
    </html>
    HTML;

    echo $errorMessage;
}

/**
 * Custom exception handler to catch and format fatal errors.
 */
function prettyExceptionHandler(Throwable $exception): void
{
    global $hasError;
    $hasError = true;
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }

    $errorMessage = <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Fatal Error</title>
        <style>
            body, html {
                padding: 0;
                margin: 0;
                width: 100%;
                height: 100%;
                background: #111;
                color: #fff;
                font-family: Arial, sans-serif;
            }
            .error-container {
                padding: 20px;
            }
            pre {
                background: #222;
                color: #fff;
                padding: 10px;
                border-radius: 5px;
                white-space: pre-wrap;
            }
            .error-title { color: #dc3545; font-weight: bold; }
            .error-file { color: #17a2b8; }
            .error-line { color: #ffc107; }
            .error-stack { background: #000; color: #bbb; padding: 10px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1 class="error-title">Fatal Error</h1>
            <pre>
                <span class="error-title">Error:</span> {$exception->getMessage()}
                <br>
                <span class="error-file">File:</span> {$exception->getFile()}
                <br>
                <span class="error-line">Line:</span> {$exception->getLine()}
                <br>
                <span class="error-title">Stack Trace:</span>
                <pre class="error-stack">{$exception->getTraceAsString()}</pre>
            </pre>
        </div>
    </body>
    </html>
    HTML;

    echo $errorMessage;
}

/**
 * Custom shutdown function to catch fatal errors (e.g., syntax errors).
 */
function prettyShutdownHandler(): void
{
    global $hasError;
    $hasError = true;
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        prettyErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);
    }
}

// Set PHP to use custom handlers
set_error_handler('prettyErrorHandler');
set_exception_handler('prettyExceptionHandler');
register_shutdown_function('prettyShutdownHandler');

/**
 * Outputs a JSON response.
 *
 * @param mixed $arrayObjectToJson
 *
 * @return void
 */
function echoJson($arrayObjectToJson): void
{
    global $hasError;
    // Check if an error was recorded before outputting JSON
    if ($hasError) {
        return; // Exit function early if there's an error
    }

    header('Content-Type: application/json');
    echo json_encode($arrayObjectToJson);
}
