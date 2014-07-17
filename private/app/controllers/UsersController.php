<?php
namespace Pcan\Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Pcan\Forms\UsersForm;
use Phalcon\Tag;

use Phalcon\Mvc\View;
use Pcan\Forms\ChangePasswordForm;
use Pcan\Models\Users;
use Pcan\Models\PasswordChanges;

use Pcan\Models\PageInfo as PageInfo;

class UsersController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $identity = $this->auth->getIdentity();
        if (!is_array($identity))
        {             
            return;
        }
        return $this->viewAction($identity['id']);
    }
    
    public function viewAction($id)
    {
        if (is_null($id))
        {
                $identity = $this->auth->getIdentity();
                if (!is_array($identity))
                {             
                    $this->flash->error("Current user unknown");
                    return;
                }
                $id = $identity['id'];
        }
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error("User was not found");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if ($this->request->isPost()) {

            $user->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'profilesId' => $this->request->getPost('profilesId', 'int'),
                'email' => $this->request->getPost('email', 'email'),
                'banned' => $this->request->getPost('banned'),
                'suspended' => $this->request->getPost('suspended'),
                'active' => $this->request->getPost('active')
            ));

            if (!$user->save()) {
                $this->flash->error($user->getMessages());
            } else {

                $this->flash->success("User was updated successfully");

                Tag::resetInput();
            }
        }

        $this->view->user = $user;

        $this->view->form = new UsersForm($user, array(
            'edit' => false
        ));       
        
    }
    public function listAction()
    { 
        $numberPage = $this->request->getQuery("page", "int");
        
        if (is_null($numberPage))
        {
            $numberPage = 1;
        }
        else {
             $numberPage = intval($numberPage);
        }
            
        $grabsize = 16;
        $start = ($numberPage-1) * $grabsize;
        //SQL_CALC_FOUND_ROWS
        $sql = "select  SQL_CALC_FOUND_ROWS u.* from users u"
                . " order by u.name"
                . " limit " . $start . ", " . $grabsize ;
         
         
        $di = \Phalcon\DI::getDefault();
        $db = $di->get('db');
        $db->connect();
        
        $stmt = $db->query($sql);
        $stmt->setFetchMode(\Phalcon\Db::FETCH_OBJ);    
        $results = $stmt->fetchAll();
    
        $cquery = $db->query("SELECT FOUND_ROWS()");
        $cquery->setFetchMode(\Phalcon\Db::FETCH_NUM);
        $maxrows = $cquery->fetch();
  
        $paginator = new PageInfo($numberPage, $grabsize, $results, $maxrows[0]);
        /*
        ob_clean();
        var_dump($paginator);
        $s = ob_get_clean();
        $this->flash->notice($s);   */
        $this->view->page = $paginator; 
    

    }
    /**
     * Searches for users
     */
    public function oldAction()
    {
       $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Pcan\Models\Users', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = array();
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $users = Users::find($parameters);
        $k = count($users);
        if ($k == 0) {
            $this->flash->notice("The search did not find any users");
        }
        else {
            $this->flash->notice($k . " Users");
        }
        $paginator = new Paginator(array(
            "data" => $users,
            "limit" => 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();

    }

    /**
     * Displayes the creation form
     */
    public function newAction()
    {
        $this->persistent->conditions = null;
        $this->view->form = new UsersForm();   
    }

    /**
     * Edits a user
     *
     * @param string $id
     */
    public function editAction($id)
    {
        
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error("User was not found");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if ($this->request->isPost()) {

            $user->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'profilesId' => $this->request->getPost('profilesId', 'int'),
                'email' => $this->request->getPost('email', 'email'),
                'banned' => $this->request->getPost('banned'),
                'suspended' => $this->request->getPost('suspended'),
                'active' => $this->request->getPost('active')
            ));

            if (!$user->save()) {
                $this->flash->error($user->getMessages());
            } else {

                $this->flash->success("User was updated successfully");

                Tag::resetInput();
            }
        }

        $this->view->user = $user;

        $this->view->form = new UsersForm($user, array(
            'edit' => true
        ));
    }
    /**
     *  create new user Vokuro
     */
    public function createAction()
    {
        if ($this->request->isPost()) {

            $user = new Users();

            $user->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'profilesId' => $this->request->getPost('profilesId', 'int'),
                'email' => $this->request->getPost('email', 'email')
            ));

            if (!$user->save()) {
                $this->flash->error($user->getMessages());
            } else {

                $this->flash->success("User was created successfully");

                Tag::resetInput();
            }
        }

        $this->view->form = new UsersForm(null);
    }
    /**
     * Creates a new user scaffold
     */
    public function createAdminAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $user = new Users();

        $user->id = $this->request->getPost("id");
        $user->name = $this->request->getPost("name");
        $user->email = $this->request->getPost("email", "email");
        $user->password = $this->request->getPost("password");
        $user->mustChangePassword = $this->request->getPost("mustChangePassword");
        $user->profilesId = $this->request->getPost("profilesId");
        $user->banned = $this->request->getPost("banned");
        $user->suspended = $this->request->getPost("suspended");
        $user->active = $this->request->getPost("active");
        $user->password_changes_id = $this->request->getPost("password_changes_id");
        $user->profiles_id = $this->request->getPost("profiles_id");
        

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "new"
            ));
        }

        $this->flash->success("user was created successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));

    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $user = Users::findFirstByid($id);
        if (!$user) {
            $this->flash->error("user does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $user->id = $this->request->getPost("id");
        $user->name = $this->request->getPost("name");
        $user->email = $this->request->getPost("email", "email");
        $user->password = $this->request->getPost("password");
        $user->mustChangePassword = $this->request->getPost("mustChangePassword");
        $user->profilesId = $this->request->getPost("profilesId");
        $user->banned = $this->request->getPost("banned");
        $user->suspended = $this->request->getPost("suspended");
        $user->active = $this->request->getPost("active");
        $user->password_changes_id = $this->request->getPost("password_changes_id");
        $user->profiles_id = $this->request->getPost("profiles_id");
        

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "edit",
                "params" => array($user->id)
            ));
        }

        $this->flash->success("user was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));

    }

    /**
     * Deletes a user
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        if ($id==1)
        {
            $this->flash->error("This user cannot be deleted");
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));          
        }
        $user = Users::findFirstByid($id);
        if (!$user) {
            $this->flash->error("user was not found");

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "search"
            ));
        }

        $this->flash->success("user was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));
    }

    /**
     * Users must use this action to change its password
     */
    public function changePasswordAction()
    {
        $form = new ChangePasswordForm();

        if ($this->request->isPost()) {

            if (!$form->isValid($this->request->getPost())) {

                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {

                $user = $this->auth->getUser();

                $user->password = $this->security->hash($this->request->getPost('password'));
                $user->mustChangePassword = 'N';

                $passwordChange = new PasswordChanges();
                $passwordChange->user = $user;
                $passwordChange->ipAddress = $this->request->getClientAddress();
                $passwordChange->userAgent = $this->request->getUserAgent();

                if (!$passwordChange->save()) {
                    $this->flash->error($passwordChange->getMessages());
                } else {

                    $this->flash->success('Your password was successfully changed');

                    Tag::resetInput();
                }
            }
        }

        $this->view->form = $form;
    }

}
