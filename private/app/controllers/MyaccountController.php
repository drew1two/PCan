<?php

namespace Pcan\Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Pcan\Forms\MyAccountForm;
use Phalcon\Tag;

use Phalcon\Mvc\View;
use Pcan\Forms\ChangePasswordForm;
use Pcan\Models\Users;
use Pcan\Models\PasswordChanges;

/**
 * Description of MyaccountController
 *
 * @author http
 */
class MyaccountController extends ControllerBase
{
    //put your code here
    public $identity;
    public $id;
    
    public function getId()
    {
        if (!isset($this->id))
        {
                $identity = $this->auth->getIdentity();
                if (!is_array($identity))
                {             
                    return;
                }
                $this->id = $identity['id'];
        }  
        return $this->id;
    }
    public function indexAction()
    {

        return $this->editAction();

    }
    /**
     * Edits own user details with restrictions
     *
     * @param string $id
     */
    public function editAction()
    {
        $id = $this->getId();
        $user = isset($id) ? Users::findFirstById($id) : null;
        if (!$user) {
            $this->view->pick("index/route404");
            $this->flash->error("User was not found");
            return;
        }

        if ($this->request->isPost()) {

            $user->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                //'profilesId' => $this->request->getPost('profilesId', 'int'),
                'email' => $this->request->getPost('email', 'email'),
                //'banned' => $this->request->getPost('banned'),
                //'suspended' => $this->request->getPost('suspended'),
                //'active' => $this->request->getPost('active')
            ));

            if (!$user->save()) {
                $this->flash->error($user->getMessages());
            } else {

                $this->flash->success("User was updated successfully");

                Tag::resetInput();
            }
        }

        $this->view->user = $user;

        $this->view->form = new MyAccountForm($user, array('myAccount'=>true,
            'edit' => true
        ));
    }

}
