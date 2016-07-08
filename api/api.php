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
require_once ('../Coupon/CouponManager.php');
require_once ('../Activity/ActivityManager.php');
require_once ('../Activity/Activity.php');


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
    case "getHotelData":
        if (!isset($postData->hotelId)) break;
        $data = (new Hotel($postData->hotelId))->getData();
        if (!$data) break;
        echo json_encode($data);
        exit(0);
        break;
    //Address
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
    case "getCityData":
        if (!isset($postData->cityId)) break;
        $city = new City($postData->cityId);
        $data = $city->getData();
        if (!$data) break;
        echo json_encode($data);
        exit(0);
    case "getCity":
        if (!isset($postData->countyId)) break;
        $cm = new CityManager();
        $data = $cm->getCity($postData->countyId);
        if (!$data) break;
        echo json_encode($data);
        exit(0);
    //User
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
    case "listUsers":
        if (!isset($postData->offset)) $postData->offset = 0;
        if (!isset($postData->num)) $postData->num = 30;
        if (!isset($postData->orderBy)) $postData->orderBy = "user_id";
        if (!isset($postData->order)) $postData->order = "asc";
        if (isset($postData->orderBy) && isset($postData->num) && isset($postData->offset) && isset($postData->order)) {
            $um = new UserManager();
            $data = $um->listUsers($postData->offset, $postData->num, $postData->orderBy, $postData->order);
            if ($data == false) break;
            echo json_encode($data);
            exit(0);
        }
        break;
    case "findUserByPhone":
        if (isset($postData->phone)) {
            $um = new UserManager();
            $data = $um->findUserByPhone($postData->phone);
            if ($data == false) break;
            echo json_encode($data);
            exit(0);
        }
        break;
    case "findUserByIdCode":
        if (isset($postData->idCode) && isset($postData->idType)) {
            $um = new UserManager();
            $data = $um->findUserByIdCode($postData->idType, $postData->idCode);
            if ($data == false) break;
            echo json_encode($data);
            exit(0);
        }
        break;
    case "getUserData":
        $user = new User($postData->userId);
        $data = $user->getData();
        if ($data == false) break;
        echo json_encode($data);
        exit(0);
        break;
    //Coupon
    case "newCoupon":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->data)) break;
        $cm = new CouponManager();
        if (!$cm->newCoupon($postData->data)) break;
        OKResponse();
        break;
    case "getUserCoupons":
        if (!isset($postData->userId)) break;
        $cm = new CouponManager();
        $result = $cm->getUserCoupons($postData->userId);
        echo json_encode($result);
        exit(0);
        break;
    case "offCoupon":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->couponId)) break;
        $cm = new CouponManager();
        $result = $cm->offCoupon($postData->couponId);
        if (!$result) break;
        OKResponse();
        break;
    case "getCoupons":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->type)) break;
        $cm = new CouponManager();
        $result = $cm->getCoupons($postData->type);
        echo json_encode($result);
        exit(0);
        break;
    //Activity
    case "newActivity":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->data)) break;
        $am = new ActivityManager();
        $id = $am->newActivity($postData->data);
        if (!$id) break;
        echo json_encode(['inserted_id' => $id]);
        exit(0);
        break;
    case "listAvailableActivity":
        if (!isset($postData->offset)) $postData->offset = 0;
        if (!isset($postData->num)) $postData->num = 4;
        $am = new ActivityManager();
        $result = $am->listAvailableActivity($postData->offset, $postData->num);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "editActivity":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->data)) break;
        if (!isset($postData->activityId)) break;
        $activity = new Activity($postData->activityId);
        $result = $activity->edit($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "offActivity":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->activityId)) break;
        $activity = new Activity($postData->activityId);
        $activity->off();
        OKResponse();
        break;
    case "setActivityWeight":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->activityId)) break;
        if (!isset($postData->weight)) break;
        $activity = new Activity($postData->activityId);
        $activity->setWeight($postData->weight);
        OKResponse();
        break;
    case "getActivityData":
        if (!isset($postData->activityId)) break;
        $activity = new Activity($postData->activityId);
        $result = $activity->getData();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
}


echo '{"code": -1, "msg": "fail"}';
exit(0);

