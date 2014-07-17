cd ..<?php
# This is included from public/index.php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => '127.0.0.1',
        'username'    => 'sapcan',
        'password'    => 'LeardF0rr3$t',
        'dbname'      => 'pcan',
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
        'publicUrl' => 'parracan.net',
        'timezone' => 'Australia/Sydney',
        'cryptSalt' => 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D',
        'loginCaptcha' => false,
        'signupCaptcha' => true,
        'captchaPublic' =>  
            //$publickey = "6LcWY_YSAAAAAHHB_DP2MkVmPel1LiFwpWg7Dadk"; // parracan.net
            "6LcXY_YSAAAAANqIqp6BVqM_qc9crpXslivHepwh", // localhost
    ),
    'mail' => array(
        'fromName' => 'ParraCAN',
        'fromEmail' => 'admin@parracan.net',
        'smtp' => array(
            'server' => 'mail.parracan.net',
            'port' => 465,
            'security' => 'ssl',
            'username' => 'admin@parracan.net',
            'password' => 'nDAS43'
        )
    ),
    'amazon' => array(
        'AWSAccessKeyId' => '',
        'AWSSecretKey' => ''
    )
));
