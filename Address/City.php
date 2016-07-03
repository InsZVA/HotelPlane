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
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, "world");
        $stmt = $this->mysqli->prepare("select `ID` from `City` where `ID`=?");
        $stmt->bind_param('i', $cityId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) throw new Exception("city id illegal");
    }

    public function getCounties() {
        $result = $this->mysqli->query("select * from `County` where `CityID`=$this->cityId");
        if (!$result) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function newCounty($data) {
        if (!isset($data->name)) return false;
        $stmt = $this->mysqli->prepare("insert into `County`(`Name`, `CityID`) values(?, $this->cityId)");
        $stmt->bind_param('s', $data->name);
        $stmt->execute();
        return $stmt->insert_id;
    }
}