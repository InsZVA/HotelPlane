<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/10
 * Time: 3:01
 */

require_once ('../Config/MySQL.php');

class PaymentManager
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function createPayment($userId, $data) {
        if (!isset($data->standard) || !isset($data->type)) return false;
        $data->paymentCode = date("ymds") . $data->type . $data->standard . $userId . random_int(0, 9999);
        $t = time();
        switch ($data->type) {
            case 0:
                if (!isset($data->planeId) || !isset($data->contact) || !isset($data->phone) || !isset($data->name) || !isset($data->sex) || !isset($data->idCode) || !isset($data->idType)) break;
                $data->state = 0;
                $stmt = $this->mysqli->prepare("insert into `payment`(`standard`, `type`, `user_id`, `id_type`, `id_code`, `sex`, `contact`, `phone`, `name`, `payment_code`, `create_time`, `plane_id`, `state`) values (?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param('iiiisissssiii', $data->standard, $data->type, $userId, $data->idType, $data->idCode, $data->sex, $data->contact, $data->phone, $data->name, $data->paymentCode, $t, $data->planeId, $data->state);
                $stmt->execute();
                return $stmt->insert_id;
            case 1:
                if (!isset($data->hotelId) || !isset($data->roomId) || !isset($data->contact) || !isset($data->phone) || !isset($data->name) || !isset($data->sex) || !isset($data->idCode) || !isset($data->idType) || !isset($data->startDate) || !isset($data->endDate)) break;
                $data->state = 0;
                $stmt = $this->mysqli->prepare("insert into `payment`(`standard`, `type`, `user_id`, `id_type`, `id_code`, `sex`, `contact`, `phone`, `name`, `payment_code`, `create_time`, `hotel_id`, `room_id`, `state`, `start_date`, `end_date`) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->bind_param('iiiisissssiiiiii', $data->standard, $data->type, $userId, $data->idType, $data->idCode, $data->sex, $data->contact, $data->phone, $data->name, $data->paymentCode, $t, $data->hotelId, $data->roomId, $data->state, $data->startDate, $data->endDate);
                $stmt->execute();
                return $stmt->insert_id;
        }
        return false;
    }

    public function listUserPayments($userId, $offset, $num) {
        $userId = intval($userId);
        $offset = intval($offset);
        $num = intval($num);
        $sql = "select * from `payment` where `user_id`=$userId order by `update_time` desc limit $offset, $num";
        $result = $this->mysqli->query($sql);
        if (!$result) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) $rows[] = $row;
        return $rows;
    }
}