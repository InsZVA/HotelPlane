<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/4
 * Time: 18:03
 */

require_once ("../Config/MySQL.php");
require_once ("../Token/TokenManager.php");
require_once ("../Coupon/CouponManager.php");

const USER_ORDER_LIST = ['user_id', 'phone', 'account', 'awardAccount', 'idCode', 'paymentNum'];
const COLUMN = ['user_id'=>'user_id', 'phone'=>'phone', 'account'=>'account', 'awardAccount'=>'award_account', 'idCode'=>'id_code', 'paymentNum'=>'payment_num'];

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
        $cm = new CouponManager();
        if (isset($data->inviterId)) {
            $stmt = $this->mysqli->prepare("insert into `user`(`username`, `level`, `password`, `inviter_id`, `create_time`, `last_login_time`) values(?,?,?,?,?,?)");
            $stmt->bind_param("sisiii", $data->username, $data->level, $data->password, $data->inviterId, $data->create_time, $data->last_login_time);
            $stmt->execute();
            $cm->pushToNewUser($stmt->insert_id, $data->inviterId);
            return $stmt->insert_id;
        }
        $stmt = $this->mysqli->prepare("insert into `user`(`username`, `level`, `password`, `create_time`, `last_login_time`) values(?,?,?,?,?)");
        $stmt->bind_param("sisii", $data->username, $data->level, $data->password, $data->create_time, $data->last_login_time);
        $stmt->execute();
        $cm->pushToNewUser($stmt->insert_id, 0);
        return $stmt->insert_id;
    }

    public function login($data) {
        if (!isset($data->username) && !isset($data->password)) return false;
        $stmt = $this->mysqli->prepare("select `user_id`, `level` from `user` where `username`=? and `password`=?");
        $data->password = md5($data->password . "a978shbv:s91[a");
        $stmt->bind_param("ss", $data->username, $data->password);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result || $result->num_rows == 0) return false;
        $row = $result->fetch_assoc();
        $tm = new TokenManager();
        return ['user_id'=> $row['user_id'], 'token'=> $tm->newToken($row['user_id'], $row['level']), 'level'=> $row['level']];
    }

    public function autoLogin($openId) {
        $stmt = $this->mysqli->prepare("select `user_id`, `level` from `user` where `open_id`=?");
        $stmt->bind_param('s', $openId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result || $result->num_rows == 0) return false;
        $row = $result->fetch_assoc();
        $tm = new TokenManager();
        return ['user_id' => $row['user_id'], 'token'=> $tm->newToken($row['user_id'], $row['level']), 'level'=> $row['level']];
    }

    public function listUsers($offset, $num, $orderBy, $order) {
        if ($order != 'asc' && $order != 'desc') return false;
        if (!in_array($orderBy, USER_ORDER_LIST)) return false;
        $orderBy = COLUMN[$orderBy];
        $offset = intval($offset);
        $num = intval($num);
        $sql = "select `user`.* from `user` order by `$orderBy` $order limit $offset,$num";
        $result = $this->mysqli->query($sql);
        if ($result) {
            $rows = [];
            while($row = $result->fetch_assoc()) {
                unset($row['password']);
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }

    public function findUserByPhone($phone) {
        $sql = "select * from `user` where `phone` like ?";
        $phone = '%' . $phone . '%';
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $rows = [];
            while($row = $result->fetch_assoc()) {
                unset($row['password']);
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }

    public function findUserByIdCode($idType, $idCode) {
        $idType = intval($idType);
        $sql = "select * from `user` where `id_type`=$idType and `id_code` like ?";
        $idCode = '%' . $idCode . '%';
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $idCode);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $rows = [];
            while($row = $result->fetch_assoc()) {
                unset($row['password']);
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }
}