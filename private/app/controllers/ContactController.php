<?php
namespace Pcan\Controllers;

use Phalcon\Logger;
use Phalcon\DI;
use Pcan\Forms\ContactForm;
use Pcan\Models\Contact;
use Pcan\Captcha\Recaptcha;

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
        $this->setupForm();
    }

    
    private function getPost()
    {
        $request = $this->request;
        $postdata = array(
           'name' => $request->getPost('name', 'striptags'),
           'telephone' => $request->getPost('telephone', 'striptags'),
           'email' => $request->getPost('email', 'email'),
           'body' => $request->getPost('body', 'striptags')
        );    
        $crecord = new Contact();
        $crecord->assign($postdata); // remember data?
        $crecord->sendDate = date('Y-m-d H:i:s');

        $config = Phalcon\DI::getDefault()->get('config');
        if ($config->application->recaptcha)
        {
            ContactController::captchaCheck($this->request);
        }
        if ($crecord->save())
        {
            $this->flash->notice("Message Sent!"); 
        } 
        else {
            $this->flash->error("Unable to Save");       
        }       
        
    }
    private function setupForm()
    {
        $request = $this->request;
        $cform = new ContactForm();
        $this->view->form = $cform;
        $this->view->title = "Contact ParraCAN";
        
        if ($request->isPost()==true)
        {
            try {
                $crecord = new Contact();
                $postdata = array(
                    'name' => $this->request->getPost('name', 'striptags'),
                    'telephone' => $this->request->getPost('telephone', 'striptags'),
                    'email' => $this->request->getPost('email', 'email'),
                    'body' => $this->request->getPost('body', 'striptags')
                );
                
                $crecord->assign($postdata); // remember data?
       
                $crecord->sendDate = date('Y-m-d H:i:s');
                
                $config = \Phalcon\DI::getDefault()->get('config');
                if ($config->application->recaptcha)
                {
                    Recaptcha::checkCaptcha($this->request,$config);
                }
                if ($crecord->save())
                {
                    $this->flash->notice("Message Sent!"); 
                } 
                else {
                    $this->flash->error("Unable to Save");       
                }
            }
            catch(Exception $ex)
            {
                $this->flash->error($ex->getMessage());
            }
        }
    }
}