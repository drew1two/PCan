<?php

namespace Pcan\Controllers;

/**
 * Provide Canonical Access based on title to blog articles
 *
 * @author Michael Rynn
 */

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Db\Adaptor\Pdo\MySql;
use Phalcon\Db\Adaptor\Pdo;
use Phalcon\Db;

use Pcan\Models\BlogComment;
use Pcan\Forms\CommentForm;
use Pcan\Models\Blog;

use Pcan\Models\PageInfo as PageInfo;


class TitleController  extends ControllerBase{
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
    
    public function byTitleAction($name)
    {
        /* look up name in canonical metatag */
        $db = $this->getDb();
        $sql = "select * from blog where title_clean = :tc";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':tc', $name, \PDO::PARAM_STR);
        $stmt->execute();
        $blog = new Blog();
        $stmt->setFetchMode(\PDO::FETCH_INTO, $blog);
        
        $result = $stmt->fetch();
        
        $this->view->pick('read/article');
        $this->view->title = "ParraCAN: ".$blog->title;
        $this->view->blog = $blog;
    }
}
