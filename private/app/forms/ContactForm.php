<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Pcan\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Element\Check;

use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Email;

class ContactForm extends Form 
{
    
    public function initialize($entity = null, $options = null)
    {
        $id = new Hidden('id');
        $this->add($id);
        
        $id = new Text('name', array('size' => 60, 'maxlength'=>60));
        $id->setLabel('Name');
        $this->add($id);
        
        
        $id = new Text('telephone', array('size' => 15, 'maxlength'=>15));
        $id->setLabel('Telephone');
        $this->add($id);
        
        
        $id = new Text('email', array('size' => 50, 'maxlength'=>45));
        $id->setLabel('Email');
        $id->addValidators(array(
            new PresenceOf(array(
                'message' => 'The email is required to get back to you'
            )),
            new Email(array(
                'message' => 'The e-mail is not valid'
            ))
        ));
        $this->add($id); 
        
        
        $comment = new TextArea('body',array('rows'=> 12, 'cols' => 60));
        $comment->setLabel('Content');
        $this->add($comment);
                 
    }
}