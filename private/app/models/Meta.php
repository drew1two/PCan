<?php

namespace Pcan\Models;

use Phalcon\Mvc\Model;

class Meta extends \Phalcon\Mvc\Model
{

    
    /**
     * Autoincrement
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $meta_name;
     
    /**
     *
     * @var string
     */
    public $template;
    /**
     *
     * @var int
     */
    public $data_limit;
     
    /**
     *
     * @var boolean
     */
    public $auto_filled;
     
  
     
}