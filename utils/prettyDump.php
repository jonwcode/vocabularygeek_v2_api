<?php
function pretty_dump($json) {
    // Ensure input is encoded correctly if it's an array or object
    if (is_array($json) || is_object($json)) {
        $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            echo "<pre style='background: #222; color: #fff; padding: 10px; border-radius: 5px;'>Error: Invalid JSON data.</pre>";
            return;
        }
    } elseif (!is_scalar($json)) {
        echo "<pre style='background: #222; color: #fff; padding: 10px; border-radius: 5px;'>Error: Unsupported data type.</pre>";
        return;
    }

    // Escape special HTML characters
    $json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

    // Regex-based syntax highlighting
    $json = preg_replace([
        '/(&quot;([^&]+?)&quot;)/', // Strings
        '/\b(-?\d+(\.\d+)?)\b/',    // Numbers (including negatives and decimals)
        '/(:\s*[\[{])/',            // Opening brackets (arrays/objects)
        '/([\]}])/',                // Closing brackets
        '/\b(true|false|null)\b/i'  // Booleans and null (case-insensitive)
    ], [
        '<span style="color: #e83e8c;">$1</span>', // Pink for strings
        '<span style="color: #17a2b8;">$1</span>', // Blue for numbers
        '<span style="color: #ffc107;">$1</span>', // Yellow for punctuation (objects/arrays)
        '<span style="color: #dc3545;">$1</span>', // Red for closing brackets
        '<span style="color: #28a745; font-weight: bold;">$1</span>' // Green for booleans/null
    ], $json);

    // Display JSON with styling
    echo "<pre style='background: #222; color: #fff; padding: 10px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word;'>" . $json . "</pre>";
}
