<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/7/29
 * Time: 12:17
 */
include ('TopSdk.php');
$phone = $_GET['phone'];

$c = new TopClient;
$c->appkey = '23423059';
$c->secretKey = 'c50325f5ec26b21eb0dd24bb85c7917d';
$req = new AlibabaAliqinFcSmsNumSendRequest;
$req->setExtend("123456");
$req->setSmsType("normal");
$req->setSmsFreeSignName("测试");
$req->setSmsParam("{\"number\":\"1234\"}");
$req->setRecNum($phone);
$req->setSmsTemplateCode("SMS_12906039");
$resp = $c->execute($req);