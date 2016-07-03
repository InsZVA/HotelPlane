<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/1
 * Time: 15:22
 */

require_once ('../Config/MySQL.php');

class China
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function getProvinces() {
        $result = $this->mysqli->query("select * from `region` where `PARENT_ID`=1");
        if (!$result) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getHotCities($num) {
        $result = $this->mysqli->query("select * from `region` where MOD(`REGION_CODE`, 10000) <> 0 and MOD(`REGION_CODE`, 100) = 0 order by `REGION_ORDER` desc limit 0, $num");
        if (!$result) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}