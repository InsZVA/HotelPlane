<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/6
 * Time: 20:16
 */
class CityManager
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, "address");
    }

    public function getCities($chinese) {
        $chinese = intval($chinese);
        $sql = "select * from `city` where `chinese`=$chinese";
        $result = $this->mysqli->query($sql);
        if (!$result) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function newCity($data) {
        if (!isset($data->name) || !isset($data->letter) || !isset($data->letter)) return false;
        $stmt = $this->mysqli->prepare("insert into city(`name`, `letter`, `chinese`) values(?,?,?)");
        $stmt->bind_param('ssi', $data->name, $data->letter, $data->chinese);
        $stmt->execute();
        $stmt->get_result();
        return $this->mysqli->insert_id;
    }

    public function deleteCity($cityId) {
        $cityId = intval($cityId);
        $result = $this->mysqli->query("delete from `city` where `city_id`=$cityId");
        return $result;
    }

    public function getCity($countyId) {
        $countyId = intval($countyId);
        $result = $this->mysqli->query("select `city_id` from `county` where `county_id`=$countyId");
        if (!$result) return false;
        $row = $result->fetch_assoc();
        return $row;
    }

    public function getCityId($cityName) {
        $cityName = $this->mysqli->escape_string($cityName);
        $result = $this->mysqli->query("select `city_id` from `city` where `name`='$cityName'");
        if (!$result || $result->num_rows == 0) return false;
        $row = $result->fetch_assoc();
        return $row;
    }
}