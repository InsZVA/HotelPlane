<?php
/**
 * Created by PhpStorm.
 * User: Welkin Ni
 * Date: 2016/7/9
 * Time: 17:50
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
require_once ('../Plane/Plane.php');
require_once ('../Payment/PaymentManager.php');
require_once ('../Payment/Payment.php');
require_once ('../Weixin/Weixin.php');
require_once ('../Statistics/Statistics.php');
require_once ('../Display/Display.php');


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

//if (!isset($postData->userId) && !isset($postData->token) && $postData->requestMethod != "login" && $postData->requestMethod != "autoLogin" && $postData->requestMethod != "newUser") PermissionDenied();
$level = -1;
//if ($postData->requestMethod != "login" && $postData->requestMethod != "autoLogin" && $postData->requestMethod != "newUser") {
if (isset($postData->userId) && isset($postData->token)) {

    $tm = new TokenManager();
    $level = $tm->verifyToken($postData->userId, $postData->token);
}
    //if ($level == -1) PermissionDenied();
//}

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
        if ($postData->data->level > $level && $postData->data->level != 1) PermissionDenied();
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
        if ($level < 1) PermissionDenied();
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
        if ($level < 1) PermissionDenied();
        $user = new User($postData->userId);
        $result = $user->isVerified();
        if (!$result) break;
        echo json_encode(['verified' => $result]);
        exit(0);
    case "verify":
        if ($level < 1) PermissionDenied();
        if (!isset($postData->data)) break;
        $user = new User($postData->userId);
        $result = $user->verify($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "getID":
        if ($level < 1) PermissionDenied();
        $user = new User($postData->userId);
        $result = $user->getID();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "setID":
        if ($level < 1) PermissionDenied();
        if (!isset($postData->data)) break;
        $user = new User($postData->userId);
        $result = $user->setID($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "bindOpenId":
        if ($level < 1) PermissionDenied();
        if (!isset($postData->openId)) break;
        $user = new User($postData->userId);
        $result = $user->bindOpenId($postData->openId);
        if (!$result) break;
        OKResponse();
        break;
    case "changePassword":
        if ($level < 1) PermissionDenied();
        if (!isset($postData->data)) break;
        $user = new User($postData->userId);
        $result = $user->changePassword($postData->data);
        if (!$result) break;
        OKResponse();
        break;
    case "listUsers":
        if ($level < 2) PermissionDenied();
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
        if ($level < 2) PermissionDenied();
        if (isset($postData->phone)) {
            $um = new UserManager();
            $data = $um->findUserByPhone($postData->phone);
            if ($data == false) break;
            echo json_encode($data);
            exit(0);
        }
        break;
    case "findUserByIdCode":
        if ($level < 2) PermissionDenied();
        if (isset($postData->idCode) && isset($postData->idType)) {
            $um = new UserManager();
            $data = $um->findUserByIdCode($postData->idType, $postData->idCode);
            if ($data == false) break;
            echo json_encode($data);
            exit(0);
        }
        break;
    case "getUserData":
        if ($level < 1) PermissionDenied();
        $user = new User($postData->userId);
        $data = $user->getData();
        if ($data == false) break;
        echo json_encode($data);
        exit(0);
        break;
    case "isVIP":
        if ($level < 1) PermissionDenied();
        $user = new User($postData->userId);
        $data = $user->isVIP();
        if ($data == false) break;
        echo json_encode($data);
        exit(0);
        break;
    case "setVIP":
        if ($level < 1) PermissionDenied();
        $user = new User($postData->userId);
        $user->setVIP();
        OKResponse();
        $weixin = new Weixin();
        $data = new stdClass();
        $data->userId = $postData->userId;
        $data->template_id = 2;
        $data->data =
        exit(0);
        break;
    case "autoLogin":
        if (!isset($postData->code)) break;
        $weixin = new Weixin();
        $userData = $weixin->getUserData($postData->code);
        if (isset($userData->errcode)) break;
        $loginData = ((new UserManager())->autoLogin($userData->openid));
        if ($loginData) {
            echo json_encode($loginData);
            exit(0);
        } else {
            echo json_encode($userData);
            exit(0);
        }
    //Coupon
    case "newCoupon":
        if ($level != 3) PermissionDenied();
        if (!isset($postData->data)) break;
        $cm = new CouponManager();
        if (!$cm->newCoupon($postData->data)) break;
        OKResponse();
        break;
    case "getUserCoupons":
        if ($level < 1) PermissionDenied();
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
    //Plane
    case "newPlane":
        $plane=new Plane();
        if(!isset($postData->data)) break;
        $result=$plane->newPlane($postData->data);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "deletePlane":
        if ($level != 3) PermissionDenied();
        $plane=new Plane();
        if(!isset($postData->planeId))
            break;
        if($plane->deletePlane($postData->planeId))
            OKResponse();
        break;
    case "editPlane":
        if ($level != 3) PermissionDenied();
        $plane=new Plane();
        if(!isset($postData->data)) break;
        $result=$plane->editPlane($postData->data);
        if ($result) OKResponse();
        break;
    case "listPlanes":
        $plane=new Plane();
        if(!isset($postData->offset)) $postData->offset=0;
        if(!isset($postData->num)) $postData->num=10;
        if(!isset($postData->orderBy)) $postData->orderBy='start_time';
        if(!isset($postData->order)) $postData->order='asc';
        if(!isset($postData->standard)) break;
        $result=$plane->listPlanes($postData->offset,$postData->num,$postData->orderBy,
            $postData->order,$postData->standard);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "search":
        $plane=new Plane();
        if(!isset($postData->keyword)) break;
        if(!isset($postData->offset)) $postData->offset=0;
        if(!isset($postData->num)) $postData->num=10;
        $result=$plane->search($postData->keyword,$postData->offset,$postData->num);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "findPlanes":
        $plane=new Plane();
        if(!isset($postData->data)) break;
        $result=$plane->findPlanes($postData->data);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "getPlaneData":
        if (!isset($postData->planeId)) break;
        $plane = new Plane();
        $result = $plane->getData($postData->planeId);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    //Payment
    case "createPayment":
        if ($level < 1) PermissionDenied();
        if (!isset($postData->data)) break;
        if ($level < 2 && $postData->userId != $postData->data->userId) PermissionDenied();
        $id = (new PaymentManager())->createPayment($postData->data->userId, $postData->data);
        if (!$id) break;
        OKResponse();
        exit(0);
        break;
    case "listUserPayments":
        if ($level < 1) PermissionDenied();
        if (!isset($postData->offset)) $postData->offset = 0;
        if (!isset($postData->num)) $postData->num = 30;
        $result = (new PaymentManager())->listUserPayments($postData->userId, $postData->offset, $postData->num);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "listPayments":
        if ($level < 1) PermissionDenied();
        if (!isset($postData->offset)) $postData->offset = 0;
        if (!isset($postData->num)) $postData->num = 30;
        $result = (new PaymentManager())->listPayments($postData->offset, $postData->num);
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    case "confirmPayment":
        if ($level < 2) PermissionDenied();
        if (!isset($postData->waiterId)) break;
        if (!isset($postData->paymentId)) break;
        $payment = new Payment($postData->paymentId);
        $result = $payment->confirm($postData->waiterId);
        if (!$result) break;
        OKResponse();
        exit(0);
        break;
    case "finishPayment":
        if ($level < 2) PermissionDenied();
        if (!isset($postData->waiterId)) break;
        if (!isset($postData->paymentId)) break;
        $payment = new Payment($postData->paymentId);
        $result = $payment->finish($postData->waiterId);
        if (!$result) break;
        OKResponse();
        exit(0);
        break;
    case "getPaymentData":
        if ($level < 1) PermissionDenied();
        if (!isset($postData->paymentId)) break;
        $payment = new Payment($postData->paymentId);
        $result = $payment->getData();
        if (!$result) break;
        echo json_encode($result);
        exit(0);
        break;
    //Weixin
    case "sendMessage":
        if ($level < 2) PermissionDenied();
        if(!isset($postData->data))
            break;
        $weixin=new Weixin();
        $result=$weixin->sendMessage($postData);
        if(!$result)
            break;
        echo $result;
        exit(0);
        break;
    //Statistics
    case "getStatistics":
        if ($level < 2) PermissionDenied();
        $statistics=new Statistics();
        $result=$statistics->getStatistics();
        if(!$result)
            break;
        echo $result;
        exit(0);
        break;
    //Display
    case "getDisplay":
    if ($level < 2) PermissionDenied();
    $display=new Display();
    $result=$display->getDisplay();
    if(!$result)
        break;
    echo $result;
    exit(0);
    break;
    case "changeDisplay":
        if ($level < 2) PermissionDenied();
        if(!isset($postData->data))
            break;
        $display=new Display();
        $result=$display->changeDisplay($postData->data);
        if(!$result)
            break;
        OKResponse();
        exit(0);
        break;
}


echo '{"code": -1, "msg": "fail"}';
exit(0);