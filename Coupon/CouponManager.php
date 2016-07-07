<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/6
 * Time: 17:26
 */
require_once ('../Config/MySQL.php');

class CouponManager
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function newCoupon($data) {
        if (!isset($data->discount) || !isset($data->minPrice) || !isset($data->endTime) || !isset($data->type)) return false;
        if (!isset($data->startTime)) $data->startTime = time();
        $stmt = $this->mysqli->prepare("insert into `coupon` values(NULL, ?,?,?,?,?)");
        $stmt->bind_param('ddiii', $data->discount, $data->minPrice, $data->startTime, $data->endTime, $data->type);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function pushToNewUser($userId, $inviterId) {
        $userId = intval($userId);
        $sql = "select * from `coupon`";
        $result = $this->mysqli->query($sql);
        while ($row = $result->fetch_assoc()) {
            if ($row['type'] == 0){
                $this->mysqli->query("insert into `user_coupon` values($userId, $row[coupon_id])");
                //echo "insert into `user_coupon` values($userId, $row[coupon_id])";
            }
            if ($row['type'] == 1 && $inviterId != 0)
                $this->mysqli->query("insert into `user_coupon` values($inviterId, $row[coupon_id])");
        }
    }

    public function getUserCoupons($userId) {
        $userId = intval($userId);
        $sql = "select * from `user_coupon` where `user_id`=$userId";
        $result = $this->mysqli->query($sql);
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function offCoupon($couponId) {
        $couponId = intval($couponId);
        return $this->mysqli->query("update `coupon` set `type`=-1 where `couponId`=$couponId");
    }

    public function getCoupons($type) {
        $type = intval($type);
        $result = $this->mysqli->query("select * from `coupon` where `type`=$type");
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}