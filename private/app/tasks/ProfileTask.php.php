<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use \Phalcon\Db\DbResult\Pdo as DbResult;
use Pcan\Acl\Acl;

class UserTask extends \Phalcon\CLI\Task {
    /**
     * main addUser email, name, profile, password
     * @param array $params
     */
    public function addAction(array $params) {
        $len = count($params);

        if ($len < 4) {
            echo "user add email name profile password";
            return;
        }

        $di = Phalcon\DI::getDefault();
        $config = $di->get('config');
        $email = $params[0];
        $name = $params[1];
        $profile = $params[2];
        $password = $params[3];

        try {

            $password = $this->security->hash($password);

            $db = get_object_vars($config->database);

            $connect = new DbAdapter($db);

            if (!is_int($profile)) {
                $sel = $connect->prepare("select id from profiles where name=:profile");
                $sel->bindParam(':profile', $profile, PDO::PARAM_STR);
                $sel->setFetchMode(PDO::FETCH_NUM);
                $sel->execute();
                $result = $sel->fetch();
                if ($result) {
                    $profile = $result[0];
                } else {
                    echo "Error: Unknown profile " . $profile . PHP_EOL;
                    return;
                }
            }
            $stmt = $connect->prepare(
                    "INSERT INTO users (name,email,password,profilesId, banned, suspended, active)"
                    . " VALUES (:name, :email, :password, :profilesId, 'N','N','Y')");

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':profilesId', $profile, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage() . PHP_EOL;
        }
    }
}