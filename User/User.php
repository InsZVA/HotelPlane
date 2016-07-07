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
        $this->id = $id;
    }

    public function getAddress() {
        $sql = "select `county_id`, `city_id` from `user` where `user_id` = $this->id";
        $result = $this->mysqli->query($sql);
        if (!$result || $result->num_rows == 0) return false;
        $row = $result->fetch_assoc();
        return $row;
    }

    public function setAddress($data) {
        if (!isset($data->countyId) || !isset($data->cityId)) return false;
        $stmt = $this->mysqli->prepare("update `user` set `county_id`=?, `city_id`=? where `user_id`=$this->id");
        $stmt->bind_param('ii', $data->countyId, $data->cityId);
        $stmt->execute();
        $stmt->get_result();
        return $stmt->affected_rows;
    }

    public function getAvatar() {
        $sql = "select `avatar` from `user` where `user_id` = $this->id";
        $result = $this->mysqli->query($sql);
        if (!$result || $result->num_rows == 0) return false;
        $row = $result->fetch_assoc();
        return $row['avatar'];
    }

    public function setAvatar($avatar) {
        $stmt = $this->mysqli->prepare("update `user` set `avatar`=? where `user_id`=$this->id");
        $stmt->bind_param('s', $avatar);
        $stmt->execute();
        $stmt->get_result();
        return $stmt->affected_rows;
    }

    public function verify($data) {
        if (!isset($data->realname) || !isset($data->phone)) return false;
        $stmt = $this->mysqli->prepare("update `user` set `realname`=?, `phone`=?, `verified`=1 where `user_id`=$this->id");
        $stmt->bind_param('ss', $data->realname, $data->phone);
        $stmt->execute();
        $stmt->get_result();
        return $stmt->affected_rows;
    }

    public function isVerified() {
        $sql = "select `verified` from `user` where `user_id` = $this->id";
        $result = $this->mysqli->query($sql);
        if (!$result || $result->num_rows == 0) return false;
        $row = $result->fetch_assoc();
        return $row['verified'];
    }
    
    public function bindOpenId($openId) {
        $stmt = $this->mysqli->prepare("update `user` set `open_id`=? where `user_id`=$this->id");
        $stmt->bind_param('s', $openId);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function getID() {
        $sql = "select `id_type`, `id_code` from `user` where `user_id` = $this->id";
        $result = $this->mysqli->query($sql);
        if (!$result || $result->num_rows == 0) return false;
        $row = $result->fetch_assoc();
        return $row;
    }

    public function setID($data) {
        if (!isset($data->idType) || !isset($data->idCode)) return false;
        $stmt = $this->mysqli->prepare("update `user` set `id_type`=?, `id_code`=? where `user_id`=$this->id");
        $stmt->bind_param('is', $data->idType, $data->idCode);
        $stmt->execute();
        $stmt->get_result();
        return $stmt->affected_rows;
    }

    public function changePassword($data) {
        if (!isset($data->oldPassword) || !isset($data->newPassword)) return false;
        $stmt = $this->mysqli->prepare("select `user_id` from `user` where `user_id`=$this->id and `password`=?");
        $data->oldPassword = md5($data->oldPassword . "a978shbv:s91[a");
        $data->newPassword = md5($data->newPassword . "a978shbv:s91[a");
        $stmt->bind_param("s", $data->oldPassword);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result || $result->num_rows == 0) return false;
        $stmt = $this->mysqli->prepare("update `user` set `password`=? where `user_id`=$this->id");
        $stmt->bind_param('s', $data->newPassword);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function getData() {
        $result = $this->mysqli->query("select * from `user` where `user_id`=$this->id");
        if (!$result) return false;
        $row = $result->fetch_assoc();
        unset($row['password']);
        return $row;
    }
}