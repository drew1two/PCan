<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pcan\Models;

use Phalcon\Mvc\Model;

class FileUpload extends Model {
    
    public $id;
    
    public $name;
    
    public $path;
    
    public $mime_type;
    
    public $date_upload;
    
    public $blog_id;
    
    public $file_size;
    
    private $sizestr;
    
    public function afterFetch()
    {
        $this->sizestr = formatBytes($this->file_size,1);
    }
    
    public function getSizeStr()
    {
        return $this->sizestr;
    }
    
}