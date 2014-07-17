<?php
namespace Pcan\Controllers;
use Phalcon\Logger;
use Phalcon\DI;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends \Phalcon\Mvc\Controller
{
    /**
     * 
     * @return type null
     * Set the template according to identity profile, or public.
     */
    public function initialize()
    {

        $this->posted = false;
        $identity = $this->auth->getIdentity();
        if (!is_array($identity))
        {
            $this->view->setTemplateBefore('public');
            return;
        }
        $profile = $identity['profile'];

        if ($profile == 'Users')
        {
            $this->view->setTemplateBefore('users1');
        }
        elseif ($profile == 'Editors') 
        {
             $this->view->setTemplateBefore('editors1');
        }
        else {
            $this->view->setTemplateBefore('private');
        }
        
    }    
   /**
     * Execute before the router so we can determine if this is a provate controller, and must be authenticated, or a
     * public controller that is open to all.
     *
     * @param Dispatcher $dispatcher
     * @return boolean
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $controllerName = $dispatcher->getControllerName();
        $logger =  \Phalcon\DI::getDefault()->get('logger');

         // Only check permissions on private controllers
        if ($this->acl->isPrivate($controllerName)) {
            // If there is no identity available the user is redirected to index/index
            $logger->log('Private page ' . $controllerName . ' in ' . __CLASS__, \Phalcon\Logger::DEBUG);
            $identity = $this->auth->getIdentity();
            if (!is_array($identity)) 
            {
                $logger->log('No identity found for private ' . $controllerName . ' in ' . __CLASS__, \Phalcon\Logger::DEBUG);

                $dispatcher->forward(array(
                    'controller' => 'index',
                    'action' => 'index'
                ));
                return false;
            }
                    
            // Check if the user have permission to the current option
            $actionName = $dispatcher->getActionName();
            //$this->flash->notice("Private : " . $controllerName . " : " . $actionName);
            if (!$this->acl->isAllowed($identity['profile'], $controllerName, $actionName)) {

                $this->flash->notice('You don\'t have access to this module: ' . $controllerName . ':' . $actionName);

                if ($this->acl->isAllowed($identity['profile'], $controllerName, 'index')) {
                    $this->flash->notice('Returned to ' . $controllerName . ':' . 'index');
                    $dispatcher->forward(array(
                        'controller' => $controllerName,
                        'action' => 'index'
                    ));
                } else {
                    $this->flash->notice('Return to user_control:index');
                    $dispatcher->forward(array(
                        'controller' => 'user_control',
                        'action' => 'index'
                    ));
                }

                return false;
            }
        }
        else {
            $logger->log('Public page ' . $controllerName . ' in ' . __CLASS__, \Phalcon\Logger::DEBUG);
            
        }
        return true;
    }

}
