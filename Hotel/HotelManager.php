<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/29
 * Time: 21:24
 */

require_once ("../Config/MySQL.php");
require_once ("../Hotel/HotelManager.php");
require_once ("const.php");

class HotelManager
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function newHotel($data) {
        if(!isset($data->name)) return false;
        if(!isset($data->address)) return false;
        if(!isset($data->star)) $data->star = 5;
        if(!isset($data->remarks)) $data->remarks = "";
        if(!isset($data->images)) return false;
        $data->images = json_encode($data->images);
        if(!isset($data->cityId)) return false;
        if(!isset($data->countyId)) return false;
        if(!isset($data->type)) $data->type = HOTEL_NOT_STANDARD;
        if(!isset($data->description)) $data->description = "";
        $stmt = $this->mysqli->prepare("insert into hotel(`name`, `address`, `star`, `remarks`, `images`, `city_id`, `county_id`, `type`, `description`) values(?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('ssissiiis', $data->name, $data->address, $data->star, $data->remarks, $data->images, $data->cityId, $data->countyId, $data->type, $data->description);
        $stmt->execute();
        $stmt->get_result();
        return $this->mysqli->insert_id;
    }

    public function findHotelsByCityId($cityId, $orderBy, $order, $offset, $num) {
        if (!in_array($orderBy, ORDER_LIST)) return false;
        $sql = "select `hotel`.*, MIN(`room`.`price`) as price from `hotel` join `room` on `room`.`hotel_id` = `hotel`.`hotel_id` where `city_id`=? group by `hotel`.`hotel_id` order by `$orderBy` $order limit ?,?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iii', $cityId, $offset, $num);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $rows = [];
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }

    public function findHotelsByCountyId($countyId, $orderBy, $order, $offset, $num) {
        if (!in_array($orderBy, ORDER_LIST)) return false;
        $sql = "select `hotel`.*, MIN(`room`.`price`) as price from `hotel` join `room` on `room`.`hotel_id` = `hotel`.`hotel_id` where `county_id`=? group by `hotel`.`hotel_id` order by `$orderBy` $order limit ?,?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('iii', $countyId, $offset, $num);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $rows = [];
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }

    public function listHotels($offset, $num, $orderBy, $order) {
        if (!in_array($orderBy, ORDER_LIST)) return false;
        $sql = "select `hotel`.*, MIN(`room`.`price`) as price from `hotel` join `room` on `room`.`hotel_id` = `hotel`.`hotel_id` group by `hotel`.`hotel_id` order by `$orderBy` $order limit ?,?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ii', $offset, $num);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $rows = [];
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }
}