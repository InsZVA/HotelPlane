<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("舒途订单");
$_GET['pid'] = intval($_GET['pid']);
$_GET['ucid'] = intval($_GET['ucid']);
$input->SetAttach("{\"pid\":$_GET[pid], \"ucid\":$_GET[ucid]}");
$mysqli = new mysqli('127.0.0.1:3307', 'root', 'ST-MySQL-610', 'ST');
$price = 0;
$result = $mysqli->query("select * from `payment` where `payment_id`=$_GET[pid]");
if (!$result || $result->num_rows == 0) {
	echo "<script>alert('订单无效！');window.history.go(-1);</script>";
	exit(0);
}
$paymentData = $result->fetch_assoc();
if ($paymentData['state'] >= 2) {
	echo "<script>alert('订单已经付款，请勿重复付款！');window.history.go(-1);</script>";
	exit(0);
}
if ($paymentData['type'] == 0 && $paymentData['standard'] == 0) { //Plane
	$result = $mysqli->query("select * from `plane` where `plane_id`=$paymentData[plane_id]");
	$price = floatval($result->fetch_assoc()['price']) * 100;
} else if ($paymentData['type'] == 0) {
	$price = floatval($paymentData['price']);
} else if ($paymentData['type'] == 1) { // Hotel
	$result = $mysqli->query("select * from `room` where `room_id`=$paymentData[room_id]");
	$price = floatval($result->fetch_assoc()['price']) * 100 * intval(($paymentData['end_date'] - $paymentData['start_date']) / (3600*24));
} else {
	$result = $mysqli->query("select * from `activity` where `activity_id`=$paymentData[activity_id]");
	$price = floatval($result->fetch_assoc()['price']) * 100;
}

$result = $mysqli->query("SELECT * FROM `coupon` join `user_coupon` where `user_coupon`.`uc_id`=$_GET[ucid]");
if (!$result || $result->num_rows == 0) {
	$discount = 0 * 100;
}
$discount = floatval($result->fetch_assoc()['discount']) * 100;
$total = $price - $discount;
if ($total <= 0) $total = 1;
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));

$input->SetTotal_fee("$total");
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("tag");
$input->SetNotify_url("http://wap.xszlv.com/pay/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>订单支付</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				//alert(res.err_msg);
				if (res.err_msg == 'get_brand_wcpay_request:cancel') {
					alert('付款失败！');
					return;
				}
				if (res.err_msg == 'get_brand_wcpay_request:ok') {
					alert('付款成功！请尽快和客服联系~');
					window.location.href="/chat.html?addition={\"paymentId\":$_GET[pid]}";
					return;
				}
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
				
				alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
		);
	}
	
	/*window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};*/
	callpay();
	</script>
</head>
<body>
    <!--<br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
	</div>-->
</body>
</html>