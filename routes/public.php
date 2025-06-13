<?php

# Check for any public routes

$file = null;

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    switch(URI_FILE) {
        case 'fetchWords':
            $file = "fetchVocabWords.php";
        break;

    }

}

require_once($file ? CONTROLLERS . $file : ROOT_DIR . '404.php');