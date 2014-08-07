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
    public $attr_value;
     
    /**
     *
     * @var string
     */
    public $attr_name;
     
    /**
     *
     * @var string
     */
    public $content_type;
     
    /**
     *
     * @var boolean
     */
    public $auto_filled;
     
  
     
}