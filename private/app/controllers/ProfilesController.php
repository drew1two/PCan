<?php
namespace Pcan\Controllers;

use Phalcon\Tag;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Pcan\Forms\ProfilesForm;
use Pcan\Models\Profiles;
use Phalcon\Mvc\View;

use Pcan\Models\PageInfo as PageInfo;

class ProfilesController extends ControllerBase
{

   /**
     * Index action
     */
    public function indexAction()
    {
        return $this->defaultAction();
    }

    /**
     * Default action, shows the search form
     */
    public function searchAction()
    {
        $this->persistent->conditions = null;
        $this->view->form = new ProfilesForm();
    }

    public function defaultAction()
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
        $sql = "select  SQL_CALC_FOUND_ROWS p.* from profiles p"
                . " order by p.name"
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
     * lists profiles
     */
    public function olddefaultAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Pcan\Models\Profiles', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = array();
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $profiles = Profiles::find($parameters);
        if (count($profiles) == 0) {

            $this->flash->notice("The search did not find any profiles");

            return $this->dispatcher->forward(array(
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $profiles,
            "limit" => 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Creates a new Profile
     */
    public function newAction()
    {
        if ($this->request->isPost()) {

            $profile = new Profiles();

            $profile->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'active' => $this->request->getPost('active')
            ));

            if (!$profile->save()) {
                $this->flash->error($profile->getMessages());
            } else {
                $this->flash->success("Profile was created successfully");
            }

            Tag::resetInput();
        }

        $this->view->form = new ProfilesForm(null);
    }

    /**
     * Edits an existing Profile
     *
     * @param int $id
     */
    public function editAction($id)
    {
        $profile = Profiles::findFirstById($id);
        if (!$profile) {
            $this->flash->error("Profile was not found");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if ($this->request->isPost()) {

            $profile->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'active' => $this->request->getPost('active')
            ));

            if (!$profile->save()) {
                $this->flash->error($profile->getMessages());
            } else {
                $this->flash->success("Profile was updated successfully");
            }

            Tag::resetInput();
        }

        $this->view->form = new ProfilesForm($profile, array(
            'edit' => true
        ));

        $this->view->profile = $profile;
    }

    /**
     * Deletes a Profile
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $profile = Profiles::findFirstById($id);
        if (!$profile) {

            $this->flash->error("Profile was not found");

            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if (!$profile->delete()) {
            $this->flash->error($profile->getMessages());
        } else {
            $this->flash->success("Profile was deleted");
        }

        return $this->dispatcher->forward(array(
            'action' => 'index'
        ));
    }
}
