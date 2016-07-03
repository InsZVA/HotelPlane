<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/14
 * Time: 12:06
 */

require_once ('TokenManager.php');

$tm = new TokenManager();
$token = $tm->newToken(1, 1);
echo $tm->verifyToken(1, $token);