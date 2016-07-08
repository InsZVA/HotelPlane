<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/8
 * Time: 14:43
 */

require_once ('../Config/MySQL.php');

const ACTIVITY_COLUMN_LIST = ['name', 'description', 'image', 'oldPrice', 'price'];
const ACTIVITY_COLUMN_TYPE = ['name' => 's', 'description'=>'s', 'image'=>'s','oldPrice'=>'d','price'=>'d'];

class Activity
{
    public $mysqli;
    public $id;
    public function __construct($id)
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
        $id = intval($id);
        $result = $this->mysqli->query("select `activity_id` from `activity` where `activity_id`=$id");
        if (!$result || $result->num_rows == 0) throw new Exception("activity id illegal");
        $this->id = $id;
    }

    public function edit($data) {
        $set = "set ";
        $params = [''];
        $i = 0;
        foreach (ACTIVITY_COLUMN_LIST as $v) {
            if (isset($data->$v)) {
                if ($i != 0) $set = $set . ",";
                if ($v != 'oldPrice')
                    $set .= " " . $v . "=?";
                else
                    $set .= " old_price=?";
                $params[0] .= ACTIVITY_COLUMN_TYPE[$v];
                $params[] = &$data->$v;
                $i++;
            }
        }
        //echo "update `activity` " . $set . " where `activity_id` = $this->id";
        $stmt = $this->mysqli->prepare("update `activity` " . $set . " where `activity_id` = $this->id");
        call_user_func_array([$stmt, "bind_param"], $params);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function off() {
        $sql = "update `activity` set `state`=-1 where `activity_id`=$this->id";
        $this->mysqli->query($sql);
    }

    public function setWeight($weight) {
        $weight = intval($weight);
        $this->mysqli->query("update `activity` set `weight`=$weight where `activity_id`=$this->id");
    }

    public function getData() {
        $rows = [];
        $result = $this->mysqli->query("select * from `activity` where `activity_id`=$this->id");
        if ($result) {
            while ($row = $result->fetch_assoc()) $rows[] = $row;
        }
        return $rows;
    }
}