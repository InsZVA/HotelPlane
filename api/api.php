<?php
/**
 * Created by PhpStorm.
 * User: InsZVA
 * Date: 2016/6/29
 * Time: 22:43
 */

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
header("Content-Type: application/json");

require_once ('../Token/TokenManager.php');
require_once ('../Hotel/HotelManager.php');
require_once ('../Hotel/Hotel.php');
require_once ('../Address/China.php');
require_once ('../Address/Province.php');
require_once('../Address/City.php');

function PermissionDenied() {
    echo '{"code": -2, "msg": "permission denied"}';
    exit(0);
}

function OKResponse() {
    echo '{"code": 0, "msg": "success"}';
    exit(0);
}

$postRaw = file_get_contents("php://input");
$postData = json_decode($postRaw);

if (!isset($postData->userId) && !isset($postData->token)) PermissionDenied();

$tm = new TokenManager();
$level = $tm->verifyToken($postData->userId, $postData->token);
if ($level == -1) PermissionDenied();

switch ($postData->requestMethod) {
    //Hotel
    case "newHotel":
        if ($level != 3) PermissionDenied();
        if (isset($postData->data)) {
            $hm = new HotelManager();
            $id = $hm->newHotel($postData->data);
            if ($id == false) break;
            echo "{\"inserted_id\": $id}";
            exit(0);
        }
        break;
    case "listHotels":
        if (!isset($postData->offset)) $postData->offset = 0;
        if (!isset($postData->num)) $postData->num = 30;
        if (!isset($postData->orderBy)) $postData->orderBy = "hotel_id";
        if (!isset($postData->order)) $postData->order = "asc";
        if (isset($postData->orderBy) && isset($postData->num) && isset($postData->offset) && isset($postData->order)) {
            $hm = new HotelManager();
            $data = $hm->listHotels($postData->offset, $postData->num, $postData->orderBy, $postData->order);
            if ($data == false) break;
            echo json_encode($data);
            exit(0);
        }
        break;
    case "findHotels":
        if (!isset($postData->offset)) $postData->offset = 0;
        if (!isset($postData->num)) $postData->num = 30;
        if (isset($postData->regionCode) && isset($postData->range) && isset($postData->num) && isset($postData->offset)) {
            $hm = new HotelManager();
            $data = $hm->findHotelsByRegionCode($postData->regionCode, $postData->range, $postData->offset, $postData->num);
            if ($data == false) break;
            echo json_encode($data);
            exit(0);
        }
        break;
    case "editHotel":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->data) || !isset($postData->hotelId)) break;
        $hotel = new Hotel($postData->hotelId);
        $hotel->edit($postData->data);
        OKResponse();
        break;
    case "deleteHotel":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->hotelId)) break;
        $hotel = new Hotel($postData->hotelId);
        $hotel->delete();
        OKResponse();
        break;
    case "newRoom":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->hotelId) || !isset($postData->data)) break;
        $hotel = new Hotel($postData->hotelId);
        $result = $hotel->newRoom($postData->data);
        if (!$result) break;
        echo "{\"inserted_id\": $result}";
        exit(0);
    case "getRooms":
        if (!isset($postData->hotelId)) break;
        $hotel = new Hotel($postData->hotelId);
        $result = $hotel->getRooms();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
    case "deleteRoom":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->hotelId) || !isset($postData->roomId)) break;
        $hotel = new Hotel($postData->hotelId);
        $hotel->deleteRoom($postData->roomId);
        OKResponse();
        break;
    //Address
    case "getProvinces":
        $china = new China();
        $result = $china->getProvinces();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "getCities":
        if (!isset($postData->regionId)) break;
        $province = new Province($postData->regionId);
        $result = $province->getCities();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "getCounties":
        if (!isset($postData->regionId)) break;
        $city = new City($postData->regionId);
        $result = $city->getCounties();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "getHotCities":
        if (!isset($postData->num)) $postData->num = 30;
        $china = new China();
        $result = $china->getHotCities($postData->num);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
}


echo '{"code": -1, "msg": "fail"}';
exit(0);

