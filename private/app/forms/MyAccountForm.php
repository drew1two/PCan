<?php
namespace Pcan\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Pcan\Models\Profiles;

class MyAccountForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        // In edition the id is hidden
        /* $isMyAccount = isset($options['myAccount']) && $options['myAccount'];
        if ($isMyAccount) {
            //$id = new Text('id', array(
            //    'readonly'=>'readonly', 'placeholder' => 'Id'
        ));
            //$id = new Hidden('id');
        } else {
            $id = new Text('id');
        }*/
        $id = new Hidden('id');
        $this->add($id);

        $name = new Text('name', array(
            'placeholder' => 'Name',
           'size' => 50,
        ));

        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'The name is required'
            ))
        ));

        $this->add($name);

        $email = new Text('email', array(
            'placeholder' => 'Email',
            'size' => 50,

        ));

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'The e-mail is required'
            )),
            new Email(array(
                'message' => 'The e-mail is not valid'
            ))
        ));

        $this->add($email);

        $this->add(new Select('profilesId', Profiles::find('active = "Y"'), array(
            'using' => array(
                'id',
                'name'
            ),
            'readonly'=>'readonly',
            'useEmpty' => true,
            'emptyText' => '...',
            'emptyValue' => ''
        )));

        $this->add(new Select('banned', array(
            'Y' => 'Yes',
            'N' => 'No'
                )
                ,array('readonly'=>'readonly',)
                ));

        $this->add(new Select('suspended', array(
            'Y' => 'Yes',
            'N' => 'No'
                )
                ,array('readonly'=>'readonly',)
                ));


        $this->add(new Select('active', array(
            'Y' => 'Yes',
            'N' => 'No'
                )
                ,array('readonly'=>'readonly',)
                ));

    }
}
