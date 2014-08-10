<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router(false);



$router->add('/:controller/:action', array(
    'controller' => 1,
    'action' => 2,
));


$router->add('/', array(
    'controller' => 'index',
    'action' => 'index',
));

$router->add('/myaccount/{any}',array(
    'controller' => 'myaccount',
    'action' => 'edit',
));

$router->add('/index',array(
    'controller' => 'index',
    'action' => 'index',
));
        
$router->add('/contact/', array(
        'controller' => 'about',
        'action' => 'contact'
));


$router->add('/article/{name}',array(
   'controller' => 'title',
   'action' => 'byTitle'
));

$router->add('/event/{name}', array(
   'controller' => 'event',
   'action' => 'byTitle'    
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
/*
$router->add('/blog/upload', array(
    'controller' => 'blog',
    'action' => 'upload',
));
*/
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


// catch all
$router->notFound(array(
    'controller' => "index",
    'action' => "route404", 
));




return $router;
