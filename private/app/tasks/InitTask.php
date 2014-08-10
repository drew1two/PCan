<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use \Phalcon\Db\DbResult\Pdo as DbResult;
use Pcan\Acl\Acl;

class InitTask extends \Phalcon\CLI\Task {

    public function initAction() {
        echo "\nThis is the default task and the default action \n";
    }
    /**
     * want : name, data_limit, template (will be quoted on cmdline), 
     * @param array $params
     * @return type
     */
    public function metatagAction(array $params)
    {
        $len = count($params);

        if ($len != 3) {
            echo "metatag attr_value attr_name comment_type";
            return;
        }  
        $meta_name = $params[0];
        $data_limit = $params[1];
        $template = $params[2];
        
        $di = Phalcon\DI::getDefault();
        $config = $di->get('config');
        
        $db = get_object_vars($config->database);

        try {

            $connect = new DbAdapter($db);
            $connect->begin();
            $sql = "insert into meta (meta_name, template, data_limit)"
                    . " values (:meta_name, :template, :data_limit)";
            
            $stm = $connect->prepare($sql);
            $stm->bindParam(':meta_name', $meta_name, PDO::PARAM_STR);
            $stm->bindParam(':template', $template, PDO::PARAM_STR);
            $stm->bindParam(':data_limit', $data_limit, PDO::PARAM_STR);
            
            $stm->execute();  
            $connect->commit();
        }
        catch (Exception $ex) {
            echo $ex->getMessage() . PHP_EOL;
        }
        
        
    }
    /**
     * @param array $params
     * profile #id ALL | resource-name*
     */
    public function profileAction(array $params) {
        $len = count($params);

        if ($len < 2) {
            echo "profile #id [All] | [resource-name ]*";
            return;
        }
        $di = Phalcon\DI::getDefault();
        $config = $di->get('config');
        $profile = $params[0];
        

        $acl = new Acl();
        $resources = $acl->getResources();
        $subset = array();
        if ($params[1] === "ALL")
        {
            $subset = array_keys($resources);
        }
        else {
            $subset = array();
            for($i = 1; $i < $len; $i++)
            {
                $subset[] = $params[$i];
            }
        }
        

        
        $db = get_object_vars($config->database);

        try {

            $connect = new DbAdapter($db);
            $connect->begin();
            if (!is_int($profile))
            {
                $sel = $connect->prepare("select id from profiles where name=:profile");
                $sel->bindParam(':profile',$profile,PDO::PARAM_STR);
                $sel->setFetchMode(PDO::FETCH_NUM);
                $sel->execute();
                $result = $sel->fetch();
                if ($result)
                {
                    $profile = $result[0];
                }
                else {
                    echo "Error: Unknown profile " . $profile . PHP_EOL;
                    return;
                }
            }
            $stmt = $connect->prepare(
                    "REPLACE INTO permissions (profilesId, resource, action)"
                    . " VALUES (:profilesId, :resource, :action)");
            $del = $connect->prepare(
                    "DELETE from permissions where profilesId = :profilesId and resource = :resource"
                    );
            foreach ($resources as $cont => $actions) 
                {
                if (!in_array($cont,$subset))
                {
                    $del->bindParam(':profilesId', $profile, PDO::PARAM_INT);
                    $del->bindParam(':resource', $cont, PDO::PARAM_STR);
                    $del->execute();    
                }
                else {
                    $stmt->bindParam(':profilesId', $profile, PDO::PARAM_INT);
                    $stmt->bindParam(':resource', $cont, PDO::PARAM_STR);
                    foreach ($actions as $act) {
                        echo "set $cont $act" . PHP_EOL;
                        $stmt->bindParam(':action', $act, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                }
            }
            $connect->commit();
        } catch (Exception $ex) {
            echo $ex->getMessage() . PHP_EOL;
        }
        
        // delete permissions cache
        $path = APP_DIR . '/cache/acl/data.txt';
        if (file_exists($path))
        {
            unlink($path);
        }
    }

}
