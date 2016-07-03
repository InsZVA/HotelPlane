<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/1
 * Time: 15:26
 */
class Province
{
    private $mysqli;
    private $regionId;
    public function __construct($regionId)
    {
        $regionId = intval($regionId);
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
        $this->regionId = $regionId;
        $result = $this->mysqli->query("select `REGION_ID` from `region` where `REGION_ID`=$regionId AND MOD(`REGION_CODE`, 10000) = 0");
        if (!$result) throw new Exception("region id illegal");
    }

    public function getCities() {
        $result = $this->mysqli->query("select * from `region` where `PARENT_ID`=$this->regionId");
        if (!$result) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}