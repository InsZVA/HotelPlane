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
require_once ('../Address/CityManager.php');
require_once ('../Address/City.php');
require_once ('../User/UserManager.php');
require_once ('../User/User.php');


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
if (!isset($postData->userId) && !isset($postData->token) && $postData->requestMethod != "login") PermissionDenied();
$level = -1;
if ($postData->requestMethod != "login") {
    $tm = new TokenManager();
    $level = $tm->verifyToken($postData->userId, $postData->token);
    if ($level == -1) PermissionDenied();
}


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
    case "findHotelsByCity":
        if (!isset($postData->offset)) $postData->offset = 0;
        if (!isset($postData->num)) $postData->num = 30;
        if (!isset($postData->orderBy)) $postData->orderBy = "hotel_id";
        if (!isset($postData->order)) $postData->order = "asc";
        if (isset($postData->cityId) && isset($postData->num) && isset($postData->offset)) {
            $hm = new HotelManager();
            $data = $hm->findHotelsByCityId($postData->cityId, $postData->orderBy, $postData->order, $postData->offset, $postData->num);
            if ($data == false) break;
            echo json_encode($data);
            exit(0);
        }
        break;
    case "findHotelsByCounty":
        if (!isset($postData->offset)) $postData->offset = 0;
        if (!isset($postData->num)) $postData->num = 30;
        if (!isset($postData->orderBy)) $postData->orderBy = "hotel_id";
        if (!isset($postData->order)) $postData->order = "asc";
        if (isset($postData->countyId) && isset($postData->num) && isset($postData->offset)) {
            $hm = new HotelManager();
            $data = $hm->findHotelsByCountyId($postData->countyId, $postData->orderBy, $postData->order, $postData->offset, $postData->num);
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
    /*
    case "getCountries":
        $china = new World();
        $result = $china->getCountries();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "getCities":
        if (!isset($postData->countryCode)) break;
        $country = new Country($postData->countryCode);
        $result = $country->getCities();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "getCounties":
        if (!isset($postData->cityId)) break;
        $city = new City($postData->cityId);
        $result = $city->getCounties();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "newCounty":
        if (!isset($postData->cityId) || !isset($postData->data)) break;
        $city = new City($postData->cityId);
        $result = $city->newCounty($postData->data);
        if (!$result) break;
        echo "{\"inserted_id\": $result}";
        exit(0);
        break;*/
    case "getCities":
        if (!isset($postData->chinese)) break;
        $cm = new CityManager();
        $result = $cm->getCities($postData->chinese);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "newCity":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->data)) break;
        $cm = new CityManager();
        $result = $cm->newCity($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "deleteCity":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->cityId)) break;
        $cm = new CityManager();
        $result = $cm->deleteCity($postData->cityId);
        if (!$result) break;
        OKResponse();
        break;
    case "getCounties":
        if (!isset($postData->cityId)) break;
        $cm = new City($postData->cityId);
        $result = $cm->getCounties();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "newCounty":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->data)) break;
        $cm = new City($postData->cityId);
        $result = $cm->newCounty($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "deleteCounty":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->cityId) || !isset($postData->countyId)) break;
        $city = new City($postData->cityId);
        $result = $city->deleteCounty($postData->countyId);
        if (!$result) break;
        OKResponse();
        break;
    case "newUser":
        if (!isset($postData->data)) break;
        $um = new UserManager();
        $id = $um->newUser($postData->data);
        if (!$id) break;
        echo "{\"inserted_id\": $id}";
        exit(0);
        break;
    case "login":
        if (!isset($postData->data)) break;
        $um = new UserManager();
        $result = $um->login($postData->data);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "getAvatar":
        $user = new User($postData->userId);
        $result = $user->getAvatar();
        if (!$result) break;
        echo json_encode(['avatar' => $result]);
        exit(0);
        break;
    case "setAvatar":
        if (!isset($postData->avatar)) break;
        $user = new User($postData->userId);
        $result = $user->setAvatar($postData->avatar);
        if (!$result) break;
        OKResponse();
        break;
    case "getAddress":
        $user = new User($postData->userId);
        $result = $user->getAddress();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "setAddress":
        if (!isset($postData->data)) break;
        $user = new User($postData->userId);
        $result = $user->setAddress($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "isVerified":
        $user = new User($postData->userId);
        $result = $user->isVerified();
        if (!$result) break;
        echo json_encode(['verified' => $result]);
        exit(0);
    case "verify":
        if (!isset($postData->data)) break;
        $user = new User($postData->userId);
        $result = $user->verify($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "getID":
        $user = new User($postData->userId);
        $result = $user->getID();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "setID":
        if (!isset($postData->data)) break;
        $user = new User($postData->userId);
        $result = $user->setID($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "bindOpenId":
        if (!isset($postData->openId)) break;
        $user = new User($postData->userId);
        $result = $user->bindOpenId($postData->openId);
        if (!$result) break;
        OKResponse();
        break;
    case "changePassword":
        if (!isset($postData->data)) break;
        $user = new User($postData->userId);
        $result = $user->changePassword($postData->data);
        if (!$result) break;
        OKResponse();
        break;
}


echo '{"code": -1, "msg": "fail"}';
exit(0);

