<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/11
 * Time: 22:50
 */

require_once ('../Config/MySQL.php');

class Payment
{
    private $mysqli;
    private $id;
    public function __construct($id)
    {
        $id = intval($id);
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
        $sql = "select `payment_id` from `payment` where `payment_id`=$id";
        $result = $this->mysqli->query($sql);
        if (!$result || $result->num_rows == 0) throw new Exception("payment id illegal");
        $this->id = $id;
    }

    public function getData() {
        $sql = "select * from `payment` where `payment_id`=$this->id";
        $result  = $this->mysqli->query($sql);
        if (!$result) return false;
        $row = $result->fetch_assoc();
        return $row;
    }

    public function confirm($waiterId) {
        $data = $this->getData();
        $waiterId = intval($waiterId);
        if ($data['state'] != 0) return false;
        $sql = "update `payment` set `state`=1, `waiter_id`=$waiterId where `payment_id`=$this->id";
        return $this->mysqli->query($sql);
    }

    public function pay() { //TODO: Called By OL Pay Callback
        $data = $this->getData();
        if ($data['state'] != 1) return false;
        $sql = "update `payment` set `state`=2 where `payment_id`=$this->id";
        return $this->mysqli->query($sql);
    }

    public function finish($waiterId) {
        $data = $this->getData();
        if ($data['state'] != 2) return false;
        if ($data['waiter_id'] != $waiterId) return false;
        $sql = "update `payment` set `state`=3 where `payment_id`=$this->id";
        return $this->mysqli->query($sql);
    }

    public function cancel($waiterId) {
        $data = $this->getData();
        $waiterId = intval($waiterId);
        if ($data['state'] != 0) return false;
        $sql = "update `payment` set `state`=-1, `waiter_id`=$waiterId where `payment_id`=$this->id";
        return $this->mysqli->query($sql);
    }
}