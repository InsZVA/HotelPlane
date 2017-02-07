<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		Log::DEBUG($data['result_code']);
		if($data['result_code']=='SUCCESS') {
			//处理订单
			$pdata = json_decode($data['attach']);
			$mysqli = new mysqli('127.0.0.1:3307', 'root', 'ST-MySQL-610', 'ST');
			$pdata->pid = intval($pdata->pid);
			$pdata->ucid = intval($pdata->ucid);
			//$mysqli->query("insert into `trade` values($pdata->pid, $pdata->ucid)");
			$mysqli->query("update `payment` set `state`=2 where `payment_id`=$pdata->pid");
			$mysqli->query("delete from `coupons` where `uc_id`=$pdata->ucid");
		}
		return true;
	}
}

Log::DEBUG("begin notify~");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
