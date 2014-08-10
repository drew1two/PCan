<?php

/* 
 *  secrets.php - Define a function addSecrets($config) that does a merge $config
 *  according to application->publicUrl  and returns  $config
 */
require_once 'secrets.php';

return addSecrets(new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'dbname'      => '',
    ),
    'application' => array(
        'appDir' => APP_DIR . '/',
        'controllersDir' => APP_DIR . '/controllers/',
        'formsDir' => APP_DIR . '/forms/',
        'modelsDir'      => APP_DIR . '/models/',
        'viewsDir'       => APP_DIR . '/views/',
        'pluginsDir'     => APP_DIR . '/plugins/',
        'libraryDir'     => APP_DIR . '/library/',
        'cacheDir'       => APP_DIR . '/cache/',
        'logDir'         => APP_DIR . '/log/',
        'baseUri'        => '/',
        'publicUrl' => 'localhost.localdomain',
        'timezone' => '',
        'cryptSalt' => '',
        'loginCaptcha' => false,
        'signupCaptcha' => true,
        'recaptcha' =>true,
        'captchaPublic' =>  "", 
        'captchaPrivate' => "",
        
    ),
    'mail' => array(
        'fromName' => '',
        'fromEmail' => '',
        'smtp' => array(
            'server' => 'mail.mydomain.net',
            'port' => 465,
            'security' => 'ssl',
            'username' => '',
            'password' => ''
        )
    ),

)));
