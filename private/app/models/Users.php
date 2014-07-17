<?php
namespace Pcan\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Validator\Uniqueness;


/**
 * Vokuro\Models\Users
 * All the users registered in the application
 */
class Users extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $mustChangePassword;

    /**
     *
     * @var string
     */
    public $profilesId;

    /**
     *
     * @var string
     */
    public $banned;

    /**
     *
     * @var string
     */
    public $suspended;

    /**
     *
     * @var string
     */
    public $active;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        if (empty($this->password)) {

            // Generate a plain temporary password
            $tempPassword = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));

            // The user must change its password in first login
            $this->mustChangePassword = 'Y';

            // Use this password as default
            $this->password = $this->getDI()
                ->getSecurity()
                ->hash($tempPassword);
        } else {
            // The user must not change its password in first login
            $this->mustChangePassword = 'N';
        }

        // The account must be confirmed via e-mail
        $this->active = 'N';

        // The account is not suspended by default
        $this->suspended = 'N';

        // The account is not banned by default
        $this->banned = 'N';
    }

    /**
     * Send a confirmation e-mail to the user if the account is not active
     */
    public function afterSave()
    {
        if ($this->active != 'Y') {

            $emailConfirmation = new EmailConfirmations();

            $emailConfirmation->usersId = $this->id;
            
             $f = $this->getDI()->getFlash();
             
             $f->notice("User id is " . $this->id);
            
            if ($emailConfirmation->save()) {
                $f->notice('A confirmation mail has been sent to ' . $this->email);
            }
            else {
                foreach ($emailConfirmation->getMessages() as $message) {
                      echo "Message: ", $message->getMessage();
                      echo "Field: ", $message->getField();
                      echo "Type: ", $message->getType();
                  }              
            }
        }
    }

    /**
     * Validate that emails are unique across users
     */
    public function validation()
    {
        $this->validate(new Uniqueness(array(
            "field" => "email",
            "message" => "The email is already registered"
        )));

        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {

        $this->hasMany('id', 'Pcan\Models\Blog', 'author_id', array(
             'alias' => 'userBlog'
        ));
    
        $this->belongsTo('profilesId', 'Pcan\Models\Profiles', 'id', array(
            'alias' => 'profile',
            'reusable' => true
        ));

        $this->hasMany('id', 'Pcan\Models\SuccessLogins', 'usersId', array(
            'alias' => 'successLogins',
            'foreignKey' => array(
                'message' => 'User cannot be deleted because he/she has activity in the system'
            )
        ));

        $this->hasMany('id', 'Pcan\Models\PasswordChanges', 'usersId', array(
            'alias' => 'passwordChanges',
            'foreignKey' => array(
                'message' => 'User cannot be deleted because he/she has activity in the system'
            )
        ));

        $this->hasMany('id', 'Pcan\Models\ResetPasswords', 'usersId', array(
            'alias' => 'resetPasswords',
            'foreignKey' => array(
                'message' => 'User cannot be deleted because he/she has activity in the system'
            )
        ));
    }
}
