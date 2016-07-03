<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/29
 * Time: 21:24
 */

require_once ('../Config/MySQL.php');

class Hotel
{
    private $mysqli, $id;
    public function __construct($id) {
        $id = intval($id);
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
        $this->id = $id;
        $result = $this->mysqli->query("select `hotel_id` from `hotel` where `hotel_id` = " . intval($id));
        if (!$result) throw new Exception("hotel id is illegal");
    }

    public function delete() {
        $this->mysqli->query("delete from `room` where `hotel_id` = $this->id");
        $this->mysqli->query("delete from `hotel` where `hotel_id` = $this->id");
    }

    public function edit($data) {
        $set = "set ";
        $i = 0;
        $params = [''];
        foreach (COLUMN_LIST as $k => $v) {
            $inputK = $v;
            if ($v == 'region_code') $inputK = 'regionCode';
            if (isset($data->$inputK)) {
                if ($i != 0) $set .= ", ";
                $set .= "`$v`=?";
                $params[0] .= COLUMN_TYPE_LIST[$k];
                if ($v != 'images')
                    $params[] = &$data->$inputK;
                else {
                    $encoded = json_encode($data->$inputK);
                    $params[] = &$encoded;
                }
                $i++;
            }
        }
        $stmt = $this->mysqli->prepare("update `hotel` " . $set . " where `hotel_id` = $this->id");
        call_user_func_array([$stmt, "bind_param"], $params);
        $stmt->execute();
    }

    public function getRooms() {
        $sql = "select * from `room` where `hotel_id` = $this->id";
        $result = $this->mysqli->query($sql);
        $rows = [];
        while($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function newRoom($data) {
        if (!isset($data->name)) return false;
        if (!isset($data->image)) return false;
        if (!isset($data->description)) return false;
        if (!isset($data->price)) return false;
        $stmt = $this->mysqli->prepare("insert into `room`(`hotel_id`, `name`, `image`, `description`, `price`) values($this->id, ?, ?, ?, ?)");
        $stmt->bind_param('sssd', $data->name, $data->image, $data->description, $data->price);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function deleteRoom($roomId) {
        $roomId = intval($roomId);
        $this->mysqli->query("delete from `room` where `room_id` = $roomId");
    }
}