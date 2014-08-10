<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

try {
    define('BASE_DIR', dirname(__DIR__));
    define('APP_DIR',  BASE_DIR . '/private/app');
   
    //echo APP_DIR . "<br/>";
    /**
     * Read the configuration
     */
    $config = include APP_DIR . "/config/config.php";

    /**
     * Read auto-loader
     */
    include APP_DIR . "/config/loader.php";

    /**
     * Read services
     */
    include APP_DIR . "/config/services.php";

    date_default_timezone_set($config->application->timezone);
    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
}
