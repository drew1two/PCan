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
     * @var datetime
     */
    public $date_published;
     
    public $data_updated;
    
    /**
     *
     * @var string
     */
    public $title;
    /**
     *
     * @var string
     */
    public $title_clean;
     
    /**
     *
     * @var string
     */
    public $article;
     
    
     
    /**
     *
     * @var integer
     */
    public $author_id;
     

    
    /**
     *
     * @var string
     */
    public $enabled;
     
    /**
     *
     * @var string
     */
    public $comments;
     
    /**
     *
     * @var string
     */
    public $featured;
    
    /**
     *
     * @var string
     */
    public $bundle_type;
    
    /**
     *
     * @var integer
     */
    public $bundle_id;
    /**
    
    public function initialize()
    {
        $this->belongsTo("author_id", 'Users', "id");
    }* 
     */
     
}
     
