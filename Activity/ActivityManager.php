<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/8
 * Time: 14:33
 */

require_once ('../Config/MySQL.php');

class ActivityManager
{
    public $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function newActivity($data) {
        $stmt = $this->mysqli->prepare("insert into `activity` values(NULL, ?,?,?,?,?,0,0)");
        if (!isset($data->name) || !isset($data->description) || !isset($data->image) || !isset($data->price)) return false;
        if (!isset($data->oldPrice)) $data->oldPrice = 0;
        $stmt->bind_param('sssdd', $data->name, $data->description, $data->image, $data->oldPrice, $data->price);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function listAvailableActivity($offset, $num) {
        $offset = intval($offset);
        $num = intval($num);
        $sql = "select * from `activity` where `state`=0 order by `weight` desc limit $offset, $num";
        $result = $this->mysqli->query($sql);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) $rows[] = $row;
        }
        return $rows;
    }
}