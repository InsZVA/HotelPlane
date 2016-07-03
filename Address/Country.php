<?php

/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/3
 * Time: 22:16
 */

require_once ('../Config/MySQL.php');

class Country
{
    private $mysqli;
    private $countryCode;
    public function __construct($countryCode)
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, "world");
        $stmt = $this->mysqli->prepare("select `Code` from `Country` where `Code`=?");
        $stmt->bind_param('s', $countryCode);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) throw new Exception("country code illegal");
        $this->countryCode = $countryCode;
    }

    public function getCities()
    {
        $result = $this->mysqli->query("select * from `City` where `CountryCode`='$this->countryCode'");
        if (!$result) return false;
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
}