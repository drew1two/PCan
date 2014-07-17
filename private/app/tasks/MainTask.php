<?php

use \Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use \Phalcon\Db\Result\Pdo as DbResult;

class MainTask extends \Phalcon\CLI\Task
{
    public function mainAction() {
         echo "\nThis is the default task and the default action \n";
    }

    /**
    * @param array $params
    */
   public function testAction (array $params) {
       $len = count($params);
       
       if ($len > 0)
       {
           for($i = 0; $i < $len; $i++)
           {
               echo sprintf(' arg %s: %s', $i, $params[$i]). PHP_EOL;
           }
       }
       
       $importDb =  [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'dpcan_w1sa',
        'password'    => 'LeardF0rr3$t',
        'dbname'      => 'dpcan',
       ];
       
       $exportDb = [
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'sapcan',
        'password'    => 'LeardF0rr3$t',
        'dbname'      => 'pcan',
           
       ];
       $import = new DbAdapter($importDb);
       $export = new DbAdapter($exportDb);
       
       $blogset = $import->query(
             "SELECT  v.title, FROM_UNIXTIME(`v`.`timestamp`) as mod_time, "
             .  "b.body_value FROM node n "
             . " join node_revision v on n.nid = v.nid and n.vid = v.vid "
             . " join field_data_body b on b.entity_id = n.nid and b.revision_id = n.vid "
             . " where n.type='article' and v.title <> 'Meeting Minutes'"
             . " order by mod_time" );
       
       echo "There are " , $blogset->numRows() , " rows" . PHP_EOL;
       
       $stmt = $export->prepare('delete from blog');
       $stmt->execute();
       
       $stmt = $export->prepare(
                   "INSERT INTO blog(title,article,title_clean,author_id, "
                   . " date_published, featured, enabled, comments_enabled,views) "
                   . " VALUES (:title, :article, :title_clean, 1, :date_published, 0, 1, 1, 1)");
       
       $title = $article = $title_clean = $date_published = "";
       
       $stmt->bindParam(':title',$title);
       $stmt->bindParam(':article', $article);
       $stmt->bindParam(':title_clean', $title_clean);
       $stmt->bindParam(':date_published', $date_published);
       
       while ($row = $blogset->fetchArray())
       {
           echo "$row[0] $row[1]" . PHP_EOL;
           $title = $row[0];
           
           // process - verify HTML, translate media
           $doc = new DOMDocument();
          
           $article = $row[2];
           $doc->loadHTML($article);
           $xpath = new DOMXPath($doc);
           $textnodes = $xpath->query('//text()');
           
           $altered = false;
           foreach($textnodes as $text)
           {
               $value = trim($text->nodeValue);
               if (substr($value,0,3) == '[[{' )
               {
                   // vanish this node. (or change it)
                   
                   
                   /*
                   $text->parentNode->removeChild($text);
                       echo "removed $value" . PHP_EOL;
                 
                    */
                   // last characters must be }]]
                   $xlen = strlen($value);
                   if (substr($value,$xlen-2,2)=="]]")
                   {
                       
                       $value = substr($value,2,$xlen-4);
                       echo "JSON $value" . PHP_EOL;
                       $json = json_decode($value);
                       $attributes = $json->attributes;
                       if ($json->type =='media' && $attributes->typeof=='foaf:Image')
                       {
                           $fid = $json->fid; // look up the file name
                           $height = $attributes->height;
                           $width = $attributes->width;
                           $class = $attributes->class;
                           
                           $fst = $import->prepare("select filename, uri, filemime, type from file_managed where fid=" . $fid);
                           $fst->execute();
                           $fresult = $fst->fetch();
                           
                           $uri = $fresult[1];
                           if (substr($uri,0,8)=='public:/')
                           {
                              $uri = '/image' . substr($uri,8); 
                              $altered = true;
                              $img = $doc->createElement('img');
                              $img->setAttribute('src', $uri);
                              $img->setAttribute('width',$width);
                              $img->setAttribute('height',$height);
                              $img->setAttribute('class', $class);
                              $img->setAttribute('typeof','foaf:Image');
                              $parent = $text->parentNode;
                              $parent->insertBefore($img, $text);
                              $parent->removeChild($text);
                           }
                           
                           
                       }
                       
                   }
                   else {
                        
                   }
                   
               }
           }
           if ($altered)
               $article = $doc->saveHTML();
           $date_published = $row[1];
           $title_clean = $row[0];
           $stmt->execute();  
       }
       
   }
}
