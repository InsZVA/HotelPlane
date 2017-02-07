<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/24
 * Time: 14:25
 */
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');

header("Content-Type: application/json");
$raw = file_get_contents("php://input");
$postData = json_decode($raw);

switch($postData->requestMethod) {
    case "upload":
        if (isset($postData->base64)) {
            $data = base64_decode($postData->base64);
            $path = "./upload/";
            $filename = md5(time() . $postData->token) . ".png";
            $f = fopen($path . $filename, "w");
            fwrite($f, $data);
            fclose($f);
            echo json_encode(['image_path' => "http://api.xszlv.com/Images/upload/" . $filename]);
            exit(0);
        } else {
            echo json_encode(['code' => '-1', 'msg' => 'fail']);
            exit(0);
        }
}

echo json_encode(['code' => '-1', 'msg' => 'fail']);
exit(0);