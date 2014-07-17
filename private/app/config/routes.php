<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router(false);

$router->add('/', array(
    'controller' => 'index',
    'action' => 'index',
    'redirect' => true,
));

$router->add('/confirm/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'confirmEmail'
));

$router->add('/reset-password/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'resetPassword'
));
$router->add('/users/view/{id}', array(
    'controller' => 'users',
    'action' => 'view',
));
$router->add('/users/edit/{id}', array(
    'controller' => 'users',
    'action' => 'edit',
));
$router->add('/users/delete/{id}', array(
    'controller' => 'users',
    'action' => 'delete',
));
$router->add('/blog/edit/{id}', array(
    'controller' => 'blog',
    'action' => 'edit',
));
$router->add('/blog/delete/{id}', array(
    'controller' => 'blog',
    'action' => 'delete',
));
$router->add('/blog/comment/{id}', array(
    'controller' => 'blog',
    'action' => 'comment',
));
$router->add('/read/article/{id}', array(
    'controller' => 'read',
    'action' => 'article',
));

$router->add('/contact/', array(
        'controller' => 'about',
        'action' => 'contact'
));

$router->add('/:controller/:action', array(
    'controller' => 1,
    'action' => 2,
));

$router->notFound(array(
    'controller' => "index",
    'action' => "route404", 
));
return $router;
