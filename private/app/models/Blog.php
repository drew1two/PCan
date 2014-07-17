<?php

namespace Pcan\Models;

use Phalcon\Mvc\Model;

class Blog extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $title;
     
    /**
     *
     * @var string
     */
    public $article;
     
    /**
     *
     * @var string
     */
    public $title_clean;
     
    /**
     *
     * @var integer
     */
    public $author_id;
     
    /**
     *
     * @var string
     */
    public $date_published;
     
    /**
     *
     * @var string
     */
    public $featured;
     
    /**
     *
     * @var string
     */
    public $enabled;
     
    /**
     *
     * @var string
     */
    public $comments_enabled;
     
    /**
     *
     * @var integer
     */
    public $views;
    
    public function initialize()
    {
        $this->belongsTo("author_id", 'Users', "id");
    }
     
}
