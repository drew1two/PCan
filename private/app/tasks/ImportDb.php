<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use \Phalcon\Db\Result\Pdo as DbResult;

class ImportDb extends \Phalcon\CLI\Task {

    public $importDb;
    public $config;
    private $blogDb;
    
    public function getConfig()
    {
        if (!isset($this->config))
        {
            $di = $this->getDI();
            $this->config = $di->get('config');
        }
        return $this->config;
    }
    public function getDb() {
        if (isset($this->blogDb))
            return $this->blogDb;
        $config = $this->getConfig();
        $exportDb = get_object_vars($this->config->database);

        $this->blogDb = new DbAdapter($exportDb);
        return $this->blogDb;
    }

    public function newImportDb() {
        $config = $this->getConfig();
        $info = get_object_vars($this->config->importdb);
        return new DbAdapter($info);
    }

    public function getImportDb() {
        if (!isset($this->importDb)) {
            $this->importDb = $this->newImportDb();
        }
        return $this->importDb;
    }

    public static function replaceNode($oldNode, $newNode) {
        $parent = $oldNode->parentNode;
        $parent->insertBefore($newNode, $oldNode);
        $parent->removeChild($oldNode);
    }

    public static function nodeImage($uri, $doc) {
        $img = $doc->createElement('img');
        $img->setAttribute('src', $uri);
        $img->setAttribute('class', 'image');
        $img->setAttribute('typeof', 'foaf:Image');
        return $img;
    }

    public static function nodeYoutube($uri, $doc) {
        $img = $doc->createElement('iframe');
        $img->setAttribute('type', 'text/html');
        $img->setAttribute('class', 'youtube-player');
        $img->setAttribute('frameborder', '0');
        $img->setAttribute('allowfullscreen', '');
        $img->setAttribute('src', 'http://www.youtube.com/embed/' . $uri);
        return $img;
    }

    public static function nodeVimeo($uri, $doc) {//<iframe src="webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        $img = $doc->createElement('iframe');
        $img->setAttribute('type', 'text/html');
        $img->setAttribute('class', 'youtube-player');
        $img->setAttribute('frameborder', '0');
        $img->setAttribute('allowfullscreen', '');
        $img->setAttribute('webkitallowfullscreen', '');
        $img->setAttribute('mozallowfullscreen', '');
        $img->setAttribute('src', '//player.vimeo.com/video/' . $uri);
        return $img;
    }

    /**
     * fix up links, alter DOM as required
     * @param DOMDocument $doc
     * @return true if alteration required
     */
    public function cleanArticle($doc) {
        $import = $this->getImportDb();
        $fid = '';
        $fst = $import->prepare('select filename, uri, filemime, type from file_managed where fid= :fid');
        $fst->bindParam(':fid', $fid);
        
        $altered = false;
        $links = $doc->getElementsByTagName('a');
        foreach($links as $link)
        {
            $href = $link->getAttribute('href');
            if (substr($href,0,7)=='images/')
            {
                $href = '/image/' . $href;
                $link->setAttribute('href', $href);
                $altered = true;
            }
            else if (substr($href,7)=='/images/')
            {
                $href = '/image' . $href;
                $link->setAttribute('href', $href);
                $altered = true;
            }
        }
        $xpath = new DOMXPath($doc);
        $textnodes = $xpath->query('//text()');
        
        foreach ($textnodes as $text) {
            $value = $text->nodeValue;
            $xlen = strlen($value);
            $value = ltrim($value);

            $startPos = $xlen - strlen($value);
            $xpos = strpos($value, '[[{');
            if ($xpos >= 0) {
                if ($xpos > 0) {
                    $startPos += $xpos;
                    $value = substr($value, $xpos);
                }
                // last characters must be }]]
                $replaced = False;
                $xpos = strpos($value, "}]]");

                if ($xpos > 2) {
                    $zpos = $xpos + 3;
                    $afterText = substr($value, $zpos);
                    $value = substr($value, 2, $xpos - 1);
                    $json = json_decode($value);
                    $attributes = $json->attributes;
                    if ($json->type == 'media' && $attributes->typeof == 'foaf:Image') {
                        $fid = $json->fid; // look up the file name
                        $hasSize = property_exists($attributes, 'height') && property_exists($attributes, 'width');
                        if ($hasSize) {
                            $height = $attributes->height;
                            $width = $attributes->width;
                        }

                        $fst->execute();
                        $fresult = $fst->fetch(\PDO::FETCH_NUM);

                        $uri = $fresult[1];
                        $newNode = null;
                        if (substr($uri, 0, 8) == 'public:/') {
                            $uri = '/image' . substr($uri, 8);
                            $newNode = $this::nodeImage($uri, $doc);
                        } else if (substr($uri, 0, 9) == 'youtube:/') {
                            $uri = substr($uri, 9);
                            if (substr($uri, 0, 3) == '/v/') {
                                $newNode = $this::nodeYoutube(substr($uri, 3), $doc);
                            }
                        } else if (substr($uri, 0, 7) == 'vimeo:/') {
                            $uri = substr($uri, 7);
                            if (substr($uri, 0, 3) == '/v/') {
                                $newNode = $this::nodeVimeo(substr($uri, 3), $doc);
                            }
                        }
                        if (isset($newNode)) {
                            $altered = true;
                            $replaced = true;
                            if ($hasSize) {
                                $newNode->setAttribute('width', $width);
                                $newNode->setAttribute('height', $height);
                            }
                            $this::replaceNode($text, $newNode);
                        } else {
                            echo "Unknown URI: " . $uri . PHP_EOL;
                        }
                    }
                    if (!$replaced) {
                        // put a comment around it to avoid display
                        $value = substr($text->nodeValue, 0, $startPos) . "<!--[[" . $value . "]]-->" . $afterText;
                        echo "original: " . $text->nodeValue . PHP_EOL;
                        $text->nodeValue = $value;
                        echo "*** : " . $value . PHP_EOL;
                    }
                }
            }
        }
        return $altered;
    }

}
