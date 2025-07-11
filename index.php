<?php
// Start session
session_start();

/**
 * Define very basic constants
 */
define("ENVIRONMENT", "development"); // [development|production|installation]

/**
 * Check ENVIRONMENT
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
if (ENVIRONMENT == "installation") {
    header("Location: ./install");
    exit;
} else if (ENVIRONMENT == "development") {
    ini_set('display_errors', 1);
} else if (ENVIRONMENT == "production") {
    ini_set('display_errors', 0);
} else {
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo 'Environment is invalid. Please contact developer for more information.';
    exit;
}


/**
 * Define constants
 */
// Path to root directory of app.
define("ROOTPATH", dirname(__FILE__));

// Path to app folder.
define("APPPATH", ROOTPATH."/app");



// Check if SSL enabled.
$ssl = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] && $_SERVER["HTTPS"] != "off" 
     ? true 
     : false;
define("SSL_ENABLED", $ssl);

// URL of the application root. 
// This is not the URL of the app directory.
$app_url = (SSL_ENABLED ? "https" : "http")
         . "://"
         . $_SERVER["SERVER_NAME"]
         . (dirname($_SERVER["SCRIPT_NAME"]) == DIRECTORY_SEPARATOR ? "" : "/")
         . trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");
// define("APPURL", $app_url);
define("APPURL", "http://172.20.10.10:8081");


// Define Base Path (for routing)
$base_path = trim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"])), "/");
$base_path = $base_path ? "/" . $base_path : "";
define("BASEPATH", $base_path);


// Required libraries, config files and helpers...
require_once APPPATH.'/autoload.php';
require_once APPPATH.'/config/config.php';
require_once APPPATH."/helpers/helpers.php";


ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log',dirname(__FILE__).'/error_php_log.log');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// Run the app...
$App = new App;
$App->process();
