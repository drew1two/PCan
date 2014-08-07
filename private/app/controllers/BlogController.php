<?php
namespace Pcan\Controllers;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Db\Adaptor\Pdo\MySql;
use Phalcon\Db\Adaptor\Pdo;
use Phalcon\Db;

use Pcan\Models\BlogComment;
use Pcan\Forms\CommentForm;
use Pcan\Models\Blog;

use Pcan\Models\PageInfo as PageInfo;

require_once __DIR__ . ' /../library/Text/Html.php';


class BlogController extends ControllerBase
{
    public $posted;
    private $connect;
    
    private function getDb()
    {
        if (is_null($this->connect))
        {
            $di = \Phalcon\DI::getDefault();
            $this->connect = $di->get('db');
            $this->connect->connect();
        }
        return $this->connect;
    }
    /**
     * Make title_clean unique by appending more characters.
     * Do not need to do this if title has not changed.
     * @param type $base_name
     * @param type $existingid
     */
    private function unique_title_url($blog,$slug)
    {
        $sql = 'select count(*) from blog where title_clean = :tc';
        $isUpdate = !is_null($blog->id) && ($blog->id > 0);
        
        if ($isUpdate)
        {
            // exclude self from search, in case of no change?
            $sql .= ' and id <> :bid';
        }
        $db = $this->getDb();
        $stmt = $db->prepare($sql);
        $tryCount = 0;
        
        $candidate = $slug;
        $date = new \DateTime($blog->date_published);
        if ($isUpdate)
        {
            $stmt->bindValue(':bid', $blog->id, \PDO::PARAM_INT);
        }
        while ($tryCount < 5)
        {
            $stmt->bindValue(':tc',$candidate, \PDO::PARAM_STR);
            $stmt->execute();
            $count_star = $stmt->fetch(\PDO::FETCH_NUM);
            if ($count_star[0] == 0)
            {
                break;
            }
            else {
                if (tryCount == 0)
                {
                    $slug .= '-' . date('Ymd',$date->getTimestamp());
                    $candidate = $slug;
                }
                else {
                    $candidate = $slug . '-' . $tryCount;
                }
            }
            $tryCount += 1;
        }
        $blog->title_clean = $candidate;
    }
    private function getMetaTags($id)
    {
             // setup metatag info
        $sql = "select m.id, m.attr_value, m.attr_name, m.content_type, b.content, b.blog_id"
                . " from meta m"
                . " left join blog_meta b on b.meta_id = m.id"
                . " and b.blog_id = " . $id;
        // form with m_attr_value as labels, content as edit text.
        $db = $this->getDb();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(\Phalcon\Db::FETCH_OBJ);    
        $results = $stmt->fetchAll();
        return $results;  
    }
    
    public function initialize()
    {
        parent::initialize();
        $this->posted = false;
        
    }

    public function indexAction()
    {
         return $this->pageAction();
    }
    /**
     * Index action
     */
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
        $sql = "select  SQL_CALC_FOUND_ROWS b.*, u.name as author_name from blog b"
                . " left join users u on u.id = b.author_id"
                . " order by b.date_published desc"
                . " limit " . $start . ", " . $grabsize ;
         
        $db = $this->getDb();
        
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
        $this->view->user_id = null;
        $canEdit = array("Editor","Administrator");
        $identity = $this->session->get('auth-identity');
        if (isset($identity) && isset($identity['id'])) {
            $this->view->user_id = $identity['id'];
            $profile = $identity['profile'];
            $this->view->isEditor = in_array($profile,$canEdit);
        }
    }

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
        $parameters["order"] = "id";

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
    /* return true any value at all came back from checkbox */
    private function int_bool($pvar)
    {
        if (is_null($this->request->getPost($pvar)))
            return 0;
        else
            return 1;
    }
    
    private function checked_bool($pvar)
    {
        $chk = $this->int_bool($pvar);
        if ($chk > 0) 
            return "checked";
        else
            return;
    }
    /**
     * Displayes the creation form
     */
    public function newAction()
    {
        if (!$this->request->isPost()) {
            // default to new.volt
            return;
        }

        $blog = new Blog();

        //$blog->id = $this->request->getPost("id");
         
        // default to current user
        $identity = $this->session->get('auth-identity');
        if (isset($identity) && isset($identity['id'])) {
            $blog->author_id = $identity['id'];
        }
        else {
            $this->flash->error("No logged in identity");
            return;
            
        }
        $blog->views = 0;
        
        // default to current date
        $blog->date_published = date('Y-m-d H:i:s');
        $blog->date_updated = date('Y-m-d H:i:s');
        $this->setBlogFromPost($blog);
       
        if (!$blog->save()) {
            foreach ($blog->getMessages() as $message) {
                $this->flash->error($message);
            }

            return false;
        }

        $this->flash->success("blog was created successfully");

        return $this->dispatcher->forward(array(
            "controller" => "blog",
            "action" => "index"
        ));

    }
    // standard updates from edit or new.
    private function setBlogFromPost(&$blog)
    {
        $newTitle = $this->request->getPost('title','striptags');
        $titleChanged = ($blog->title !== $newTitle);
        $blog->title = $newTitle;
        $newUrl = '';
        $autoUrl = True;
        if (!is_null($blog->id))
        {
            $newUrl = $this->request->getPost('title_clean','striptags');
            
            if ($newUrl != $blog->title_clean)
            {
                $this->unique_title_url($blog,$newUrl); 
                $autoUrl = False;
            }
        }
        
        if ($titleChanged && $autoUrl)
        {
            $this->unique_title_url($blog,url_slug($blog->title));   
        }
        $blog->article = $this->request->getPost("article");
       
        $blog->featured = $this->int_bool('featured');
        $blog->enabled = $this->int_bool('enabled');
        $blog->comments = $this->int_bool('comments'); 
        $blog->date_updated = date('Y-m-d H:i:s');
    }
    private function setTagFromBlog($blog)
    {
        $this->view->id = $blog->id;
        $this->tag->setDefault("id", $blog->id);
        $this->tag->setDefault("title", $blog->title);
        $this->tag->setDefault("article", $blog->article);
        $this->tag->setDefault("title_clean", $blog->title_clean);
        $this->tag->setDefault("author_id", $blog->author_id);
        $this->tag->setDefault("date_published", $blog->date_published);
        $this->tag->setDefault("date_updated", $blog->date_updated);
        $this->tag->setDefault("views", $blog->views);        
        // Checkbox field needs a default
        $this->tag->setDefault("featured", 1);  
        $this->tag->setDefault("enabled", 1);  
        $this->tag->setDefault("comments", 1);
    }
    
    private function getView($id)
    {
        $blog = Blog::findFirstByid($id);
        if (!$blog) {
            $this->flash->error("blog was not found");

            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "index"
            ));
        }
        $this->setTagFromBlog($blog);
        $this->view->blog = $blog;
    }
    /**
     * Make an edit form for blog $id
     * @param type $id
     * @return type null
     */
    private function editForm($id)
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity) && isset($identity['id'])) {
           $user_id = $identity['id'];
        }
        else {
           $user_id = null;
        }
        $this->getView($id);
        $blog = $this->view->blog;
        $canEdit = array("Editor", "Administrator");
        $profile = $identity['profile'];

        $isApprover = in_array($profile, $canEdit) && ($blog->author_id !== $user_id);
        $this->view->isApprover = $isApprover;
        if (!$canEdit)
        {
           $this->response->redirect("blog/comment/".$id);
           return;
        }
        // setup metatag info

        $this->view->metatags = $this->getMetaTags($id);       
    }
    /**
     * Edits a blog
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if ($this->posted)
            return true;
        
        if (!$this->request->isPost()) {
            $this->editForm($id);
        }
        else {
            return $this->updatePost($id);
        }
    }
    /**
     * View a blog
     *
     * @param string $id
     */
    public function commentAction($id)
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
       
       
       $this->view->blog = Blog::findFirstByid($bid);

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
    /**
     * Creates a new blog
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "index"
            ));
        }

        $blog = new Blog();

        $blog->id = $this->request->getPost("id");
        
        setBlogFromPost($blog);  
        
        $blog->author_id = $this->request->getPost("author_id");
        $blog->date_published = date('Y-m-d H:i:s');
        $blog->views = $this->request->getPost("views");
              
        if (!$blog->save()) {
            foreach ($blog->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "new"
            ));
        }

        $this->flash->success("blog was created successfully");
        $this->posted = true;
        return $this->dispatcher->forward(array(
            "controller" => "blog",
            "action" => "edit"
        ));

    }

    /**
     * Saves a blog edited
     *
     */
    public function updatePost($id)
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "index"
            ));
        }
        // check match?
        $check_id = $this->request->getPost("id");
        $blog = Blog::findFirstByid($id);
        if (!$blog) {
            $this->flash->error("blog does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "index"
            ));
        }
        // set updatable things
        $this->setBlogFromPost($blog);

        if (!$blog->save()) {

            foreach ($blog->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "edit",
                "params" => array($blog->id)
            ));
        }
        $metatags = BlogController::getMetaTags($id);
        $db = $this->getDb();
        $inTrans = false;
        foreach($metatags as $mtag)
        {
            // key = meta.#id.value
            $key = 'metatag-'. $mtag->id . '-' . $mtag->attr_value;
            
            $content = $this->request->getPost($key,'striptags');
            
            if (is_null($content) || empty($content))
            {
                // link content record needs deleting ? 
                if (!is_null($mtag->blog_id))
                {
                    if (!$inTrans)
                    {
                        $db->begin();
                        $inTrans = True;
                        
                    }
                    $sql = "delete from blog_meta where blog_id = :blogid"
                            . " and meta_id = :metaid";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':blogid', (int) $blog->id, \PDO::PARAM_INT);
                    $stmt->bindValue(':metaid', (int) $mtag->id, \PDO::PARAM_INT);  
                    $stmt->execute();
                }
            }
            else {
                if (!$inTrans)
                {
                    $db->begin();
                    $inTrans = True;
                }
                $sql = "replace into blog_meta (blog_id, meta_id, content)"
                        . " values(:blogid, :metaid, :content)";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':blogid', (int) $blog->id, \PDO::PARAM_INT);
                $stmt->bindValue(':metaid', (int) $mtag->id, \PDO::PARAM_INT);
                $stmt->bindValue(':content', $content, \PDO::PARAM_STR);
                $stmt->execute();
            }
            
        }
        if ($inTrans)
        {
             $db->commit();
        }

        // get the metatag info from the POST
        // reconstruct the view
        $this->editForm($id);

    }


    /**
     * Deletes a blog
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $blog = Blog::findFirstByid($id);
        if (!$blog) {
            $this->flash->error("blog was not found");

            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "index"
            ));
        }

        if (!$blog->delete()) {

            foreach ($blog->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "blog",
                "action" => "search"
            ));
        }

        $this->flash->success("blog was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "blog",
            "action" => "index"
        ));
    }

}
