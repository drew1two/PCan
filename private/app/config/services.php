<?php
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Crypt;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Files as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;

use Pcan\Auth\Auth;
use Pcan\Acl\Acl;
use Pcan\Mail\Mail;


Use Phalcon\Logger,
    Phalcon\Db\Adapter\Pdo\Mysql as Connection,
    Phalcon\Events\Manager as EventsManager,
    Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}, true);

/**
 * Loading routes from the routes.php file
 */
$di->set('router', function () {
    return require __DIR__ . '/routes.php';
});

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new \Phalcon\Mvc\View();

    $view->setViewsDir($config->application->viewsDir);
    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir . 'volt/',
                'compiledSeparator' => '_'
            ));

            return $volt;
        }
    ));

    
    //$view->registerEngines(array('.volt' => '\Phalcon\Mvc\View\Engine\Volt'));
    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    
    
    $eventsManager = null;
    $eventsManager = new EventsManager();
    $dolog = $config->application->debuglog;
    if ($dolog)
    {
        
        $logger = new FileAdapter($config->application->logDir . "debug.log");

        //Listen all the database events
        $eventsManager->attach('db', function($event, $connection) use ($logger) {
            if ($event->getType() == 'beforeQuery') {
                $logger->log($connection->getSQLStatement(), Logger::INFO);
            }
        });
    }
    

    $connection = new Connection(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ));

    //Assign the eventsManager to the db adapter instance

    $connection->setEventsManager($eventsManager);

    return $connection;
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () use ($config) {
    return new MetaDataAdapter(array(
        'metaDataDir' => $config->application->cacheDir . 'metaData/'
    ));
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});

/**
 * Crypt service
 */
$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();
    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});

/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('Pcan\Controllers');
    return $dispatcher;
});

$di->set('logger', array(
    'className' => 'Phalcon\Logger\Adapter\File',
    'arguments' => array(
        array(
            'type' => 'parameter',
            'value' => $config->application->logDir . 'error.log'
        )       
    )
));
            


/**
 * Flash service with custom CSS classes
 */
$di->set('flash', function () {
    return new Flash(array(
        'error' => 'alert alert-error',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info'
    ));
});

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});

/**
 * Mail service uses GMail
 * 
 */
$di->set('mail', function () {
    return new Mail();
});

/**
 * Access Control List
 */
$di->set('acl', function () {
    return new Acl();
});

/* Already set 
$di->set('modelsManager', function() { 
    return new \Phalcon\Mvc\Model\Manager();
});
 */