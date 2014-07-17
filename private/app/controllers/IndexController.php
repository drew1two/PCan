<?php

namespace Pcan\Controllers;
use Phalcon\Db as PDO;

require_once __DIR__ . '/../library/Text/IntroMake.php';

class IndexController extends ControllerBase
{
    
    public function indexAction($redirect)
    {
            if (!is_null($redirect))
            {
                return $this->response->redirect("index/index");
            }       
            else
                return $this->defaultAction();
    }
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function defaultAction()
    {
        $db =  \Phalcon\DI::getDefault()->get('db');
        $db->connect();
        
        $mquery = $db->query(
                "select b.id, b.title, b.date_published from blog b where b.enabled=1 "
                . "order by date_published desc limit 12"
                );
        $mquery->setFetchMode(PDO::FETCH_OBJ);
        $blog = $mquery->fetchAll(); 

        $this->view->recent = $blog;
        
        $fquery = $db->query(
            "select b.id, b.title, b.date_published, b.article from blog b "
            . " where b.featured=1" 
            . " order by b.date_published desc limit 3"
        );
        $fquery->setFetchMode(PDO::FETCH_OBJ);
        $feature = $fquery->fetchAll();
        // reduce article size using introMake
        foreach($feature as $blog)
        {
            //echo $blog->article;
            $blog->article = IntroText($blog->article, 150);
        }
        $this->view->feature = $feature;   
    }
    
    public function route404Action()
    {
        
    }

}

