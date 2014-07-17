<?php

 use Phalcon\DI\FactoryDefault\CLI as CliDI,
     Phalcon\CLI\Console as ConsoleApp;

 define('VERSION', '1.0.0');

 //Using the CLI factory default services container
 $di = new CliDI();

 // Define path to application directory
 defined('APP_DIR')
 || define('APP_DIR', realpath(dirname(__FILE__)));

 /**
  * Register the autoloader and tell it to register the tasks directory
  */
 $loader = new \Phalcon\Loader();
 $loader->registerDirs(
     array(
         APP_DIR . '/tasks'
     )
 );
 $loader->register();

 // Load the configuration file (if any)
 if(is_readable(APP_DIR . '/config/config.php')) {
     $config = include APP_DIR . '/config/config.php';
     $di->set('config', $config);
 }

 //Create a console application
 $console = new ConsoleApp();
 $console->setDI($di);

 /**
 * Process the console arguments
 */
 $arguments = array();
 $extra = array();
 foreach($argv as $k => $arg) {
     if($k == 1) {
         $arguments['task'] = $arg;
     } elseif($k == 2) {
         $arguments['action'] = $arg;
     } elseif($k > 3) {
         $extra[] = $arg;
     } elseif ($k==3) {
         $extra[] = $arg;
         $arguments[] =& $extra;
     }
 }
 // PHP does copy on write, so only do this at the end
 


 // define global constants for the current task and action
 define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
 define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

 try {
     // handle incoming arguments
     $console->handle($arguments);
 }
 catch (\Phalcon\Exception $e) {
     echo $e->getMessage();
     exit(255);
 }
