<?php

use \Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use \Phalcon\Db\Result\Pdo as DbResult;

require_once __DIR__ . ' /../library/Text/Html.php';

class BlogTask extends ImportDb {

    /**
     * 
     * @param array $params Clear all blogs
     */
    public function clearAction(array $params) {
        $len = count($params);
        if ($len == 0) {
            echo "blog clear [all | id]" . PHP_EOL;
            return;
        }
        $db = $this->getDb();
        if ($params[0] == "all") {
            $stmt = $db->prepare("delete from blog");
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
        $updateCount = 0;
        $insertCount = 0;
        $failCount = 0;

        $import = $this->getImportDb();


        $qurl = $import->prepare('select alias from url_alias where source=:source');

        $blogset = $import->query(
                "SELECT  n.title, FROM_UNIXTIME(`n`.`created`) as mod_time,"
                . "b.body_value, n.nid FROM node n"
                . " join field_data_body b on b.entity_id = n.nid"
                . " where ((n.type='article') or (n.type='news_article')) and n.title <> 'Meeting Minutes'"
                . " order by mod_time");

        echo "There are ", $blogset->numRows(), " rows" . PHP_EOL;

        $stmt = $export->prepare(
                "INSERT INTO blog(title,article,title_clean,author_id, "
                . " date_published, featured, enabled, comments) "
                . " VALUES (:title, :article, :title_clean, 1, :date_published, 0, 1, 1)");

        $stmt_update = $export->prepare(
                "UPDATE  blog set article = :article where id = :id");

        $check_stmt = $export->prepare('select id from blog where title_clean = :tc and date_published = :pub');
        $unique_stmt = $export->prepare('select count(*) from blog where title_clean = :tc and date_published <> :pub' );

        $title = $article = $title_clean = $date_published = "";
        $blogid = 0;
        $stmt_update->bindParam(':article', $article);
        $stmt_update->bindParam(':id', $blogid, \PDO::PARAM_INT);

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':article', $article);
        $stmt->bindParam(':title_clean', $title_clean);
        $stmt->bindParam(':date_published', $date_published);

        while ($row = $blogset->fetchArray()) {

            $title = $row[0];
            $date_published = $row[1];
            // process - verify HTML, translate media

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

            $check_stmt->execute();
            $checkrow = $check_stmt->fetch(PDO::FETCH_NUM);
            $success = false;
            if ($checkrow) {
                // already exists, do an update of article text
                $blogid = $checkrow[0];
                $success = $stmt_update->execute();
                $updateCount += 1;
            } else {
                // do insert

                $success = $stmt->execute();
                $insertCount += 1;
            }
            if (!$success) {
                $failCount += 1;
                echo "***** IMPORT FAILED **** :  " . $candidate . PHP_EOL;
            }
        }
        echo "Updates $updateCount Inserts $insertCount Fails $failCount" . PHP_EOL;
    }

}
