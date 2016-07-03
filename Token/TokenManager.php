<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/14
 * Time: 11:54
 */

require_once('/home/wwwroot/default/Config/MySQL.php');

class TokenManager
{
    private $mysqli;
    private $TIME_EXCEED = 30 * 60;
    public function __construct() {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function newToken($userId, $level) {
        $token = md5($userId . time());
        $stmt = $this->mysqli->prepare("insert into `access_control` values(?, ?, ?, ?)");
        $t = time();
        $stmt->bind_param('siii', $token, $userId, $level, $t);
        $stmt->execute();
        return $token;
    }

    public function verifyToken($userId, $token) {
        if ($token == "test") return 3;
        $stmt = $this->mysqli->prepare("select `update_time`, `user_id`, `level` from `access_control` where `token` = ?");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) return -1;
        $row = $result->fetch_array();
        $t = time();
        if (($t - $row['update_time']) > $this->TIME_EXCEED) return -1;
        if ($row['user_id'] != $userId) return -1;
        $ret = $row['level'];
        $stmt = $this->mysqli->prepare("update `access_control` set `update_time` = ? where `token` = ?");
        $stmt->bind_param('is', $t, $token);
        $stmt->execute();
        return $ret;
    }
}