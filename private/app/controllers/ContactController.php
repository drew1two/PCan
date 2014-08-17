<?php
namespace Pcan\Controllers;

use Phalcon\Logger;
use Phalcon\DI;
use Pcan\Forms\ContactForm;
use Pcan\Models\Contact;
use Pcan\Captcha\Recaptcha;
use Phalcon\Exception;

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
        
        $this->setupForm();
    }

    private function setupForm()
    {
        $request = $this->request;
        $cform = new ContactForm();
        $this->view->form = $cform;
        $this->view->title = "Contact ParraCAN";
        
        if ($request->isPost()==false)
        {
            $this->view->pick("about/contact");
            return;
        }

        try {
            $config = \Phalcon\DI::getDefault()->get('config');
            if ($config->application->recaptcha)
            {
                $resp = Recaptcha::checkCaptcha($this->request,$config);
                if (!$resp->is_valid)
                {
                    throw new Exception("Recaptcha failed: " . $resp->error);
                }
            }              
            if ($cform->isValid($this->request->getPost()) != false) {  
                // isValid does not assign data?
                $postdata = array(
                    'name' => $this->request->getPost('name', 'striptags'),
                    'telephone' => $this->request->getPost('telephone', 'striptags'),
                    'email' => $this->request->getPost('email', 'email'),
                    'body' => $this->request->getPost('body', 'striptags')
                );
                $crecord = new Contact();
                $crecord->assign($postdata); // remember data?
                $crecord->sendDate = date('Y-m-d H:i:s');
                if ($crecord->save())
                {
                    $this->flash->notice("Message Sent!"); 
                } 
                else {
                    $this->flash->notice("Unable to Save");   
                    $this->flash->notice($crecord->getMessages());
                }
                $this->view->pick("about/contact");
            }
            else {
                $this->view->pick("about/contact");
                $collect = '';
                foreach($cform->getMessages() as $message )
                {
                    if (strlen($collect) > 0)
                        $collect .= PHP_EOL;
                    $collect .= "Problem with field " .$message->getField() . ': ' .  $message->getMessage();
                }
                $this->flash->notice($collect); 
                $this->view->errors = $cform->getMessages();
            }
        }
        catch(Exception $ex)
        {
            $this->flash->notice($ex->getMessage());
        }

    }
}