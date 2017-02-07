<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/8/3
 * Time: 15:48
 */
include ('TopSdk.php');
require_once ("../Config/MySQL.php");

class Sms {
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli(MySQLConfig::$db_address, MySQLConfig::$db_user, MySQLConfig::$db_password, MySQLConfig::$db_name);
    }

    public function SendCode($phone) {
        $phone = intval($phone);
        return $this->send($phone);
    }

    private function send($phone) {
        $code = rand(1000, 9999);
        $c = new TopClient;
        $c->appkey = '23423059';
        $c->secretKey = 'c50325f5ec26b21eb0dd24bb85c7917d';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("123456");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("测试");
        $req->setSmsParam("{\"number\":\"$code\"}");
        $req->setRecNum("$phone");
        $req->setSmsTemplateCode("SMS_12906039");
        $resp = $c->execute($req);
        $t = time();
        $this->mysqli->query("delete from `code` where `phone` = '$phone'");
        $this->mysqli->query("insert into `code` values($phone, $code, $t)");
        //var_dump($resp);
        return $resp;
    }

    public function Verify($phone, $code) {
        $phone = intval($phone);
        $result = $this->mysqli->query("select * from `code` where `phone` = '$phone'");
        if (!$result || $result->num_rows == 0) {
            return false;
        } else {
            $row = $result->fetch_assoc();
            if ($row['code'] == $code && $row['update_time'] - time() < 600) return true;
            return false;
        }
    }
}