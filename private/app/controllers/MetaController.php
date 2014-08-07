<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pcan\Controllers;

use Phalcon\Tag;
use Phalcon\Mvc\Model\Criteria;

use Phalcon\Db\Adaptor\Pdo\MySql;
use Phalcon\Db as PDO;

use Pcan\Models\Meta;
use Pcan\Models\PageInfo as PageInfo;
use Pcan\Forms\MetaForm;

/**
 * Description of MetaController
 *
 * @author http
 */
class MetaController extends ControllerBase {
    //put your code here
    
    public function indexAction()
    {
        $numberPage = $this->request->getQuery("page", "int");
        
        if (is_null($numberPage))
        {
            $numberPage = 1;
        }
        else {
             $numberPage = intval($numberPage);
        }
            
        $grabsize = 10;
        $start = ($numberPage-1) * $grabsize;
        //SQL_CALC_FOUND_ROWS
        $sql = "select  SQL_CALC_FOUND_ROWS m.id, m.attr_value, "
                . " m.attr_name,"
                . " m.content_type,"
                . " m.auto_filled from meta m order by m.attr_value"
                . " limit " . $start . ", " . $grabsize;

         
        $di = \Phalcon\DI::getDefault();
        $mm = $di->get('db');
        $mm->connect();
        
        $stmt = $mm->query($sql);
        $stmt->setFetchMode(\Phalcon\Db::FETCH_OBJ);    
        $results = $stmt->fetchAll();
    
        $cquery = $mm->query("SELECT FOUND_ROWS()");
        $cquery->setFetchMode(\Phalcon\Db::FETCH_NUM);
        $maxrows = $cquery->fetch();
        
        $paginator = new PageInfo($numberPage, $grabsize, $results, $maxrows[0]);
        
        $this->view->page = $paginator; 
        $this->view->user_id = null;
        $canEdit = array("Administrator");
        $identity = $this->session->get('auth-identity');
        if (isset($identity) && isset($identity['id'])) {
            $this->view->user_id = $identity['id'];
            $profile = $identity['profile'];
            $this->view->isEditor = in_array($profile,$canEdit);
        }        
    }
    
    public function editAction()
    {
        if ($this->request->isPost())
        {
            return doPost();
            
        }
        $id = $this->request->getQuery("id", "int");
        if (is_null($id) || $id===0)
        {
            return newAction();
        }
        $meta = Meta::findFirstById($id);
        $this->view->form = new MetaForm($meta,null);  
        
    }
    
    function doPost()
    {  
        $id = $this->request->getPost('id', 'int');
        if (!is_null($id) && $id > 0)
        {
            $meta = Meta::findFirstById($id);
        }
        else {
             $meta = new Meta();
        }

        $meta->assign(array(
            'attr_name' => $this->request->getPost('attr_name', 'striptags'),
            'attr_value' => $this->request->getPost('attr_value', 'striptags'),
            'auto_filled' => $this->request->getPost('auto_filled'),
            'content_type' => $this->request->getPost('content_type', 'striptags'),
        ));

        if (is_null($meta->id) || $meta->id===0)
        {
            $meta->id = null;
            if (!$meta->save()) {
                $this->flash->error($meta->getMessages());

            } else {
                $this->flash->success("Meta record was created successfully");
            }

            // get the id?
        }
        else { // exists already
            if (!$meta->save()) {
                $this->flash->error($meta->getMessages());

            } else {
                $this->flash->success("Meta record was updated successfully");
            }                
        }
        Tag::resetInput();
        $this->view->form = $meta;            
    }
    public function newAction()
    {
        
        if ($this->request->isPost()) {
            doPost();
        }
        else {
            $this->view->form = new MetaForm(null);        
        }
    }
}
