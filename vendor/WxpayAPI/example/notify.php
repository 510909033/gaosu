<?php
use think\Log;

ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once PAY_PATH."/lib/WxPay.Api.php";
require PAY_PATH.'/lib/WxPay.Notify.php';
//require PAY_PATH.'/example/log.php';

//初始化日志


class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
	    Log::write(__LINE__);
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
		    Log::write(__LINE__);
			return true;
		}
		Log::write(__LINE__);
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		Log::write(__LINE__);
		if(!array_key_exists("transaction_id", $data)){
		    Log::write(__LINE__);
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
		    Log::write(__LINE__);
			$msg = "订单查询失败";
			return false;
		}
		$log = new Log();
		$log->write(json_encode($data));
		return true;
	}
}

Log::write('111111');
//Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
Log::write('22222');
$notify->Handle(false);
Log::write('333333');
