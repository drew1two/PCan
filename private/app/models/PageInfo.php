<?php

/** fake Pagination class to stuff values into. 
   When using limit in SQL fetch, the maximum record count is unknown,
   the total possible pages is unknown,
   and last page is detected by getting less than total number of records, 
   including the potential number of records in result,
   or getting total number in separate count(*) query. 
   My sql can use SQL_CALC_FOUND_ROWS !! 
 */
namespace Pcan\Models;

class PageInfo {
    public $before;
    public $next;
    public $last;
    public $current;
    public $dataRows;
    public $items;
    public $pageRows;
    
    /** 
     * $pagenum current page
     * $limit   number of rows in a page
     * $results reference to the page rows as object array
     * $maxrows is number of rows in query without the LIMIT
     */
    public function __construct($pageNum, $pageSize, &$results, $maxrows)
    {
        $this->items = $results;      
        $this->pageRows = $pageSize; 
        $this->dataRows = $maxrows;
        $this->before = ($pageNum > 1) ? $pageNum - 1 : 1;
        $this->current = $pageNum;
        $this->last = (int) (($maxrows-1) / $pageSize + 1);
        $this->next = ($pageNum < $this->last) ? $pageNum + 1 : $this->last;
       
    }
}
