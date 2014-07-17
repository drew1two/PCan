<?php
namespace Pcan\Models;

use Phalcon\Mvc\Model;

/**
 * EmailConfirmations
 * Stores the reset password codes and their evolution
 */
class EmailConfirmations extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $usersId;

    public $code;

    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     *
     * @var integer
     */
    public $modifiedAt;

    public $confirmed;

    /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->createdAt = date('Y-m-d H:i:s');
        $this->modifiedAt = $this->createdAt;
        
        // Generate a random confirmation code
        $this->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        // Set status to non-confirmed
        $this->confirmed = 'N';
        
    }
    public function afterValidationOnCreate()
    {
        $this->getDI()
                    ->getFlash()
                    ->notice('After Validation ' . $this->modifiedAt);      
    }
    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        // Timestamp the confirmaton
        $this->modifiedAt = date('Y-m-d H:i:s');
    }

    /**
     * Send a confirmation e-mail to the user after create the account
     */
    public function afterCreate()
    {
        $mailer = $this->getDI()->getMail();
        
        $sentOk = $mailer->send(array(
                $this->user->email => $this->user->name
                ), "Please confirm your email", 'confirmation', array(
            'confirmUrl' => '/confirm/' . $this->code . '/' . $this->user->email
        ));
        
    }
    

    public function initialize()
    {
        $this->belongsTo('usersId', 'Pcan\Models\Users', 'id', array(
            'alias' => 'user'
        ));
    }
}
