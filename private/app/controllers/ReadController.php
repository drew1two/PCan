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
    private function getMetaTags($id)
    {
             // setup metatag info
        $sql = "select m.id, m.attr_value, m.attr_name, m.content_type, b.content"
                . " from meta m"
                . " left join blog_meta b on b.meta_id = m.id"
                . " and b.blog_id = " . $id;
        // form with m_attr_value as labels, content as edit text.

        $di = \Phalcon\DI::getDefault();
        $db = $di->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(\Phalcon\Db::FETCH_OBJ);    
        $results = $stmt->fetchAll();
        return $results;  
    }   

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
        
       //$logger =  \Phalcon\DI::getDefault()->get('logger');
       //$logger->log("setFromBlog", \Phalcon\Logger::DEBUG);
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
        $this->view->title = 'ParraCAN Article Index';
        $orderby = $this->request->getQuery('orderby');
        if (is_null($orderby))
        {
            $orderby = 'date';
        }
        $order_list = array(  
            'title-alt' => 'b.title desc',
            'title' => 'b.title asc',
            'date-alt' => 'b.date_published asc',
            'date' => 'b.date_published desc',
            'author' => 'author_name asc',
            'author-alt' => 'author_name desc',
        );
        $alt_list = array(
            'date' => 'date',
            'title' => 'title',
            'author' => 'author',
        );
        if ($orderby=='title')
        {
            $alt_list['title'] = 'title-alt';
        }
        else if($orderby=='date')
        {
            $alt_list['date'] = 'date-alt';
        }
        else if($orderby=='author')
        {
            $alt_list['author'] = 'author-alt';        
        }
        $this->view->orderalt = $alt_list;
        $this->view->orderby = $orderby;
        
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
                . " b.title_clean, b.date_published, u.name as author_name from blog b"
                . " left join users u on u.id = b.author_id"
                . " where b.enabled = 1"
                . " order by " . $order_list[$orderby]
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
       $this->view->title = "ParraCAN: " . $this->ablog->title;
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
        
        $this->view->meta = $this->getMetaTags($id);
        $this->view->user_id = $user_id;
        $comment->blog_id = $id;
        $this->view->form = new CommentForm($comment, array(
        'edit' => true
        ));
    }
}
