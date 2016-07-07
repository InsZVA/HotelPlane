<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/1
 * Time: 15:30
 */
class City
{
    private $mysqli;
    private $cityId;
    public function __construct($cityId)
    {
        $cityId = intval($cityId);
        $this->cityId = $cityId;
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, "address");
        $stmt = $this->mysqli->prepare("select `city_id` from `city` where `city_id`=?");
        $stmt->bind_param('i', $cityId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result || $result->num_rows == 0) throw new Exception("city id illegal");
    }

    public function getCounties() {
        $result = $this->mysqli->query("select * from `county` where `city_id`=$this->cityId");
        if (!$result || $result->num_rows == 0) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function newCounty($data) {
        if (!isset($data->name) || !isset($data->letter)) return false;
        $stmt = $this->mysqli->prepare("insert into `county`(`name`, `letter`, `city_id`) values(?, ?, $this->cityId)");
        $stmt->bind_param('ss', $data->name, $data->letter);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function deleteCounty($countyId) {
        $countyId = intval($countyId);
        return $this->mysqli->query("delete from `county` where `county_id`=$countyId and `city_id`=$this->cityId");
    }

    public function getData() {
        $result = $this->mysqli->query("select * from `city` where `city_id`=$this->cityId");
        if (!$result) return false;
        return $result->fetch_assoc();
    }
}