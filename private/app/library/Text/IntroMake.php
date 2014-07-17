<?php

/*
 * PCAN php website model based on Phalcon and Vokura
 */

function visitNodes(DOMNode $p, &$textnodes)
{
    
    foreach($p->childNodes as $node)
    {
        if ($node->nodeType == XML_TEXT_NODE)
            $textnodes[] = $node;
        else
        if ($node->childNodes)
            visitNodes($node, $textnodes);
        
    }
}

/** Strip the html text, as a DOM, of all nodes after text limit breached */

function IntroText(&$htmlText, $limitSize) {
    $doc = new DOMDocument();
    $doc->loadHTML($htmlText);
    $totallen = 0;
    $strip = false;
    $textnodes = array();
    $sizes = array();
    visitNodes($doc->documentElement, $textnodes);
    $result = " " . count($textnodes) . " ";
    foreach ($textnodes as $text) {
        if ($strip==false) {
            $v = $text->nodeValue;
            
            $xlen = strlen($v);
            $sizes[] = $xlen;
            $totallen = (int) $xlen + $totallen;
            if ($totallen > $limitSize) {
                $strip = true;
                
            }
        } else {
            // removing rest of tree and break
            $parent = $text->parentNode;
            $nextSibling = $text->nextSibling;
            $lastnode = $parent;
            while ($lastnode) {
                if (!$nextSibling) {
                    $nextSibling = $lastnode->nextSibling;
                    $parent = $lastnode->parentNode;
                    $lastnode = $parent;
                } else {
                    $sibling = $nextSibling;
                    $nextSibling = $sibling->nextSibling;
                    $parent->removeChild($sibling);
                }
            }
            $result = $result . print_r($sizes,true);
            break;
        }
    }
   
    return $doc->saveHTML();
}
