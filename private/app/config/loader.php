<?php

$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
    'Pcan\Models' => $config->application->modelsDir,
    'Pcan\Controllers' => $config->application->controllersDir,
    'Pcan\Forms' => $config->application->formsDir,
    'Pcan' => $config->application->libraryDir
));

$loader->register();
