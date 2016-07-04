<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/4
 * Time: 18:52
 */
class User
{
    private $mysqli;
    private $id;

    public function __construct($id)
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
        $id = intval($id);
        $result = $this->mysqli->query("select `user_id` from `user` where `user_id`=$id");
        if (!$result) throw new Exception("user id illegal");
    }

    public function getAddress() {
        $sql = "select `county_id`, `city_id` from `user` where `user_id` = $this->id";
        $result = $this->mysqli->query($sql);
        if (!$result) return false;
        $row = $result->fetch_assoc();
        return $row;
    }

    public function setAddress($data) {
        if (!isset($data->countyId) || !isset($data->cityId)) return false;
        $stmt = $this->mysqli->prepare("update `county_id`=?, `city_id`=? where `user_id`=$this->id");
        $stmt->bind_param($data->countyId, $data->cityId);
        $stmt->get_result();
        return $stmt->affected_rows;
    }

    public function getAvatar() {
        $sql = "select `avatar` from `user` where `user_id` = $this->id";
        $result = $this->mysqli->query($sql);
        if (!$result) return false;
        $row = $result->fetch_assoc();
        return $row['avatar'];
    }

    public function setAvatar($avatar) {
        $stmt = $this->mysqli->prepare("update `avatar`=? where `user_id`=$this->id");
        $stmt->bind_param($avatar);
        $stmt->get_result();
        return $stmt->affected_rows;
    }

    public function verify($data) {
        if (!isset($data->realname) || !isset($data->phone)) return false;
        $stmt = $this->mysqli->prepare("update `realname`=?, `phone=? where `user_id`=$this->id");
        $stmt->bind_param($data->realname, $data->phone);
        $stmt->get_result();
        return $stmt->affected_rows;
    }

    public function isVerified() {
        $sql = "select `verified` from `user` where `user_id` = $this->id";
        $result = $this->mysqli->query($sql);
        if (!$result) return false;
        $row = $result->fetch_assoc();
        return $row['verified'];
    }
}