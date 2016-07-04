<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/4
 * Time: 18:03
 */

require_once ("../Config/MySQL.php");
require_once ("../Token/TokenManager.php");

class UserManager
{
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function newUser($data) {
        if (!isset($data->level) || !isset($data->password) ||!isset($data->username)) return false;
        $data->password = md5($data->password . "a978shbv:s91[a");
        $data->create_time = time();
        $data->last_login_time = time();
        if (isset($data->inviterId)) {
            $stmt = $this->mysqli->prepare("insert into `user`(`username`, `level`, `password`, `inviter_id`, `create_time`, `last_login_time`) values(?,?,?,?,?,?)");
            $stmt->bind_param("sisiii", $data->username, $data->level, $data->password, $data->inviterId, $data->create_time, $data->last_login_time);
            $stmt->execute();
            //TODO: User Get Coupon
            //TODO: Inviter Increase Account
            return $stmt->insert_id;
        }
        $stmt = $this->mysqli->prepare("insert into `user`(`username`, `level`, `password`, `create_time`, `last_login_time`) values(?,?,?,?,?)");
        $stmt->bind_param("sisii", $data->username, $data->level, $data->password, $data->create_time, $data->last_login_time);
        $stmt->execute();
        //TODO: User Get Coupon
        return $stmt->insert_id;
    }

    public function login($data) {
        if (!isset($data->username) && !isset($data->password)) return false;
        $stmt = $this->mysqli->prepare("select `user_id`, `level` from `user` where `username`=? and `password`=?");
        $data->password = md5($data->password . "a978shbv:s91[a");
        $stmt->bind_param("ss", $data->username, $data->password);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) return false;
        $row = $result->fetch_assoc();
        $tm = new TokenManager();
        return $tm->newToken($row['user_id'], $row['level']);
    }
}