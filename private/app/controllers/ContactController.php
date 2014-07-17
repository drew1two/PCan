<?php
namespace Pcan\Controllers;

use Phalcon\Logger;
use Phalcon\DI;
/**
 * Display the "About" page.
 */
class ContactController extends ControllerBase
{

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {    
        $this->view->pick("about/contact");
    }

}