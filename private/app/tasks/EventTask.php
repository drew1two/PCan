<?php

use \Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use \Phalcon\Db\Result\Pdo as DbResult;

require_once __DIR__ . ' /../library/Text/Html.php';

class EventTask extends ImportDb {


    /**
     * 
     * @param array $params Clear all blogs
     */
    public function clearAction(array $params) {
        $len = count($params);
        if ($len == 0) {
            echo "event clear [all | id]" . PHP_EOL;
            return;
        }
        $db = $this->getDb();
        if ($params[0] == "all") {
            $stmt = $db->prepare("delete from event");
            $stmt->execute();
            $stmt = $db->prepare("delete from blog where bundle_type='event'");
            $stmt->execute();

            echo "Doing all" . PHP_EOL;
            return;
        }
    }

    /**
     * @param array $params
     */
    public function importAction(array $params) {
        $len = count($params);

        if ($len > 0) {
            for ($i = 0; $i < $len; $i++) {
                echo sprintf(' arg %s: %s', $i, $params[$i]) . PHP_EOL;
            }
        }
        $export = $this->getDb();

        $import = $this->getImportDb();
        
        $updateCount = $insertCount = $failCount = 0;
        
        $qurl = $import->prepare('select alias from url_alias where source=:source');
        $blogset = $import->query(
                "SELECT  n.title, FROM_UNIXTIME(`n`.`created`) as mod_time, "
                . "b.body_value, n.nid,"
                . " ed.field_event_date_value AS fromTime, ed.field_event_date_value2 AS toTime"
                . " FROM node n"
                . " join field_data_body b on b.entity_id = n.nid"
                . " join field_data_field_event_date ed ON ed.entity_id = n.nid"
                . " where n.type='event'"
                . " order by mod_time");

        echo "There are ", $blogset->numRows(), " rows" . PHP_EOL;


        $stmt = $export->prepare(
                "INSERT INTO blog(title,article,title_clean,author_id, "
                . " date_published, featured, enabled, comments, bundle_type) "
                . " VALUES (:title, :article, :title_clean, 1, :date_published, 0, 1, 1, 'event')");

        $stmt_update = $export->prepare(
                "UPDATE  blog set article = :article where id = :id");

        $check_stmt = $export->prepare('select id from blog where title_clean = :tc and date_published = :pub');
        $unique_stmt = $export->prepare('select count(*) from blog where title_clean = :tc and date_published <> :pub' );

        $title = $article = $title_clean = $date_published = "";
        $blogid = 0;        

        $fromTime = $toTime = null;
        $enabled = True;
        

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':article', $article);
        $stmt->bindParam(':title_clean', $title_clean);
        $stmt->bindParam(':date_published', $date_published);

        $event = $export->prepare(
                "INSERT INTO event( fromTime, toTime, blogId, enabled) "
                . " VALUES (:fromTime, :toTime, :blogId, 1)"
                );
        $event->bindParam(':fromTime', $fromTime);
        $event->bindParam(':toTime', $toTime);
        $event->bindParam(':blogId', $blogid);
              
        
        
        while ($row = $blogset->fetchArray()) {
            echo "$row[0] $row[1]" . PHP_EOL;
            $title = $row[0];
            $article = $row[2];
            
            $errors = array();
            $doc = html_utf8_doc($article,$errors);
            
            if (count($errors) > 0)
            {
                echo "Errors in " . $title_clean . PHP_EOL;
                foreach($errors as $e)
                    echo $e;
            }
            
            if ($this->cleanArticle($doc))
            {
                 $article = $doc->saveHTML();
            }            
            $date_published = $row[1];
            // id for blog which does not exist yet
            // get existing URL
            $qurl->bindValue(':source', 'node/' . $row[3]); // use node/id
            $qurl->execute();
            $tresult = $qurl->fetch(\PDO::FETCH_NUM);
            if (!$tresult) {
                $title_clean = url_slug($row[0]);
            } else {
                $title_clean = $tresult[0];

                $xpos = strpos($title_clean, '/');
                if ($xpos > 0) {
                    $title_clean = substr($title_clean, $xpos + 1);
                }
                echo "tresult = " . $title_clean . PHP_EOL;
            }
            $tryCount = 0;
            $candidate = $title_clean;
            // unique article/url
            while ($tryCount < 10) {

                $unique_stmt->bindValue(':tc', $candidate, PDO::PARAM_STR);
                $unique_stmt->bindValue(':pub', $date_published, PDO::PARAM_STR);
                $unique_stmt->execute();
                $checkrow = $unique_stmt->fetch(PDO::FETCH_NUM);
                if ($checkrow[0] > 0) {
                    $candidate = $title_clean . '-' . ($checkrow[0] + $tryCount);
                } else {
                    $title_clean = $candidate;
                    break;
                }
                $tryCount += 1;
            }

            $check_stmt->bindValue(':tc', $title_clean, PDO::PARAM_STR);
            $check_stmt->bindValue(':pub', $date_published, PDO::PARAM_STR);

            echo "Check " . $date_published . " " . $title_clean . PHP_EOL;
            $check_stmt->execute();
            $checkrow = $check_stmt->fetch(PDO::FETCH_NUM);
            $success = false;
            $doInsert = false;
            $export->begin();
            $rollback = false;
            if ($checkrow) {
                // already exists, do an update of article text
                $blogid = (int) $checkrow[0];
                $stmt_update->bindValue(':article', $article);
                $stmt_update->bindValue(':id', $blogid, \PDO::PARAM_INT);
                $success = $stmt_update->execute();
                if ($success) $updateCount += 1;
            } else {
                // do insert

                $success = $stmt->execute();
                $doInsert = $success;
                if ($success) $insertCount += 1;
            }
            if (!$success) {
                echo "***** IMPORT UPDATE FAILED **** :  " . $candidate . PHP_EOL;
                $rollback = true;
                $failCount += 1;
            }              
            else if ($doInsert)
            {

                $blogId = $export->lastInsertId();
                $fromTime = $row[4];
                $toTime = $row[5];
                if (!$event->execute())
                    $rollback = true; 
            }
            if ($rollback)
            {
                $export->rollback();
            }
            else {
                $export->commit();
            }
        }
        echo "Updates $updateCount Inserts $insertCount Fails $failCount" . PHP_EOL;
    }


}
