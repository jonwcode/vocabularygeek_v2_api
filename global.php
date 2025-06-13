<?php
require 'vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * @var int UNIXTIME The current Unix timestamp
 * @var object JSON_DATA The current Unix timestamp
 * @var string URI The request URI
 * @var string ROOT_DIR The document root directory
 * @var string CONTROLLERS The document root directory
 * @var string UTILS The document root directory
 * @var string ROUTES The document root directory
 * @var string DB_FILE The document root directory
 * @var string AUTH The document root directory
 * @var string IPADDR The remote IP address
 * @var array URI_PARTS The parts of the URI
 */
define('UNIXTIME', time());
define('JSON_DATA', json_decode(file_get_contents('php://input')));
define('URI', $_SERVER['REQUEST_URI']);
define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
define('CONTROLLERS', ROOT_DIR . '/controllers/');
define('UTILS', ROOT_DIR . '/utils/');
define('ROUTES', ROOT_DIR . '/routes/');
define('DB_FILE', ROOT_DIR . '/lib/db.php');
define('AUTH', ROOT_DIR . '/lib/auth.php');
define('IPADDR', $_SERVER['REMOTE_ADDR']);
define('URI_PARTS', explode('/', trim(URI, '/')));
$uri_parts = explode('/', trim(URI, '/'));

# Ensure the variable is an array before calling array_pop
if (is_array($uri_parts)) {
    $uri_file = array_pop($uri_parts);
    $uri_file = strtok($uri_file, '?');
    define('URI_FILE', $uri_file);
} else {
    # Handle the error or initialize the array
    @define('URI_PARTS', []);
    define('URI_FILE', '');
}

# Require all of the global used functions

require_once(ROOT_DIR . '/global_functions.php');
