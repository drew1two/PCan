<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// Give read-only access to blogs and comments //

namespace Pcan\Controllers;

use Phalcon\Logger;
use Phalcon\DI;


use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Pcan\Models\Blog;
use Pcan\Models\PageInfo;
use Pcan\Controllers\ControllerBase;
use Pcan\Models\BlogComment; 
use Pcan\Forms\CommentForm;

class ReadController extends ControllerBase
{
    

    /**
     * Index action
     */

    /**
     * Searches for blog
     */
    public function searchAction()
    {
 
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "\Pcan\Models\Blog", $this->request->getPost());
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }

        $parameters["order"] = "date_published desc";

        $blog = Blog::find($parameters);
        if (count($blog) == 0) {
            $this->flash->notice("The search did not find any blog");

            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $blog,
            "limit"=> 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }
 
    private function setFromBlog($blog)
    {
        
       $logger =  \Phalcon\DI::getDefault()->get('logger');
       $logger->log("setFromBlog", \Phalcon\Logger::DEBUG);
       /* $this->tag->setDefault("id", $blog->id);
        $this->tag->setDefault("title", $blog->title);
        $this->tag->setDefault("article", $blog->article);
        $this->tag->setDefault("title_clean", $blog->title_clean);
        $this->tag->setDefault("author_id", $blog->author_id);
        $this->tag->setDefault("date_published", $blog->date_published);
        $this->tag->setDefault("featured", $blog->featured);
        $this->tag->setDefault("enabled", $blog->enabled);
        $this->tag->setDefault("comments_enabled", $blog->comments_enabled);
        $this->tag->setDefault("views", $blog->views);  */     
        
    }
    
    private function getBlog($id)
    {
           
        $blog = Blog::findFirstByid($id);
        if (!$blog) {

            $this->flash->error("blog was not found");

            $this->dispatcher->forward(array(
                "controller" => "index",
                "action" => "index"
            ));
            return false;
        }
        else {
            $this->ablog = $blog;
            return true;
            
        }
    }
    public function pageAction()
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
        $sql = "select  SQL_CALC_FOUND_ROWS b.id, b.title, b.article, "
                . " b.date_published, u.name as author_name from blog b"
                . " left join users u on u.id = b.author_id"
                . " where b.enabled = 1"
                . " order by b.date_published desc"
                . " limit " . $start . ", " . $grabsize ;
         
         
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
        /*
        ob_clean();
        var_dump($paginator);
        $s = ob_get_clean();
        $this->flash->notice($s);   */
        $this->view->page = $paginator; 
    }
    /**
     * View a blog
     *
     * @param string $id
     */
    public function indexAction()
    {
       return $this->pageAction();
    }
    
    public function newCommentAction()
    {
        if (!$this->request->isPost()) {
            // default to new.volt
            return;
        }
    }
    public function articleAction()
    {
        
       $id = $this->dispatcher->getParam("id");
       //$logger =  \Phalcon\DI::getDefault()->get('logger');
       //$logger->log("Read:indexAction " . $id, \Phalcon\Logger::DEBUG);
       
       if (is_null($id))
       {
           //$logger->log("null id ", \Phalcon\Logger::DEBUG);
           $this->searchAction();
           return;
       }
       $bid = intval($id);
       
       $this->getBlog($bid);
       $this->view->blog = $this->ablog;
       if($this->ablog->enabled != true)
       {
           $this->view->pick("read/unavailable");
           return;
       }
       
       
       // comments listing pages
       
        $numberPage = $this->request->getQuery("page", "int");
        
        if (is_null($qnumber))
        {
            $numberPage = 1;
        }
        else {
            $numberPage = intval($numberPage);
        }
        $grabsize = 16;

  
        $paginator = BlogComment::getComments($numberPage,$grabsize,$bid);
        
        // set  up some fields
        $this->view->page = $paginator; 
        $comment = new BlogComment();
                // default to current user
        $identity = $this->session->get('auth-identity');
        $user_id = null;
        if (isset($identity) && isset($identity['id'])) {
            $user_id = $identity['id'];
            $comment->user_id = $user_id;
        }
        
        $this->view->user_id = $user_id;
        $comment->blog_id = $id;
        $this->view->form = new CommentForm($comment, array(
        'edit' => true
        ));
    }
}
