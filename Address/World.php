<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/3
 * Time: 22:10
 */

require_once ('../Config/MySQL.php');

class World
{
    private $mysqli;
    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, "world");
    }

    public function getCountries() {
        $result = $this->mysqli->query("select * from `Country`");
        if (!$result) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}