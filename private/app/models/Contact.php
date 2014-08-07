<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Pcan\Models;

use Phalcon\Mvc\Model;
use Library\Mail;

class Contact extends \Phalcon\Mvc\Model {
    public $id;
    
    public $name;
    
    public $telephone;
    
    public $email;
    
    public $body;
    
    public $sendDate;
    
     /**
     * Send a confirmation e-mail to the user after create the account
     */
    public function afterCreate()
    {
        $di = $this->getDI(); // magic
        
        $mailer = $di->getMail(); // SendMail
        $config = $di->get('config');
        $toName = $config->mail->toName;
        $toEmail = $config->mail->toEmail;
        // send to website contact info.
        // 
        // to, subject, name, params
        
        $templateData = array(
                    'name' => $this->name,
                    'email' => $this->email,
                    'telephone' => $this->telephone,
                    'body' => $this->body,
                    'sendDate' => $this->sendDate,
                    );
        
        $sentOk = $mailer->send(
                array(
                    $toEmail => $toName,
                ),
                'Website Contact',
                'contact',
                $templateData
                );
        
        $notifyOk = $mailer->send(
                array(
                    $this->email => $this->name,
                ),
                'Julies Catering Contact receipt',
                'contactrespond',
                $templateData
                );
      
    }
    
}
