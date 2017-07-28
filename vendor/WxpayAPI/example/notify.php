<?php
use think\Log;
use app\order\model\SysOrder;
use think\Db;

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
		Log::order_log(json_encode($data),'回调记录');		
		$notfiyOutput = array();
		$update = array();
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			Log::order_log($msg,'参数校验');
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			Log::order_log($msg,'订单校验');
			return false;
		}
        $orderdata =  SysOrder::get(['out_trade_no'=>$data['out_trade_no']]);
        if (!$orderdata){
            $msg = '本地订单不存在'.$data['out_trade_no'];
            Log::order_log($msg,'订单校验');
            return false;
        }
        if ($orderdata['trade_state']=='SUCCESS')
            return  true;
        
        if ($orderdata['total_fee']!=$data['total_fee']){
        	Log::order_log('本地单号:'.$orderdata['out_trade_no'].' 微信单号:'.$data['out_trade_no'],'金额异常');
            $update['remark'] = '金额异常';
            $update['trade_state'] = 'PAYERROR';
        }else{
            $update['trade_state'] = 'SUCCESS';
        }
        
        $update['time_end'] = strtotime($data['time_end']);
        $update['return_cache'] = json_encode($data);
        $update['attach'] = $data['attach'];
        $update['transaction_id'] = $data['transaction_id'];
        

        Db::startTrans();
        try {
            $res    = SysOrder::update($update,['out_trade_no'=>$data['out_trade_no']]);
            $logres = WayLog::update(['is_need_pay'=>0,'is_pay'=>1,'pay_time'=>time()],['id'=>$orderdata['log_id']]);
            if ($res&&$logres){
                Db::commit();
                Log::order_log('订单更新成功'.$orderdata['out_trade_no'],'成功');
                return true;
            }
            else
            {
                Db::rollback();
                Log::order_log('订单更新数据库失败'.$orderdata['out_trade_no'],'失败');
                return false;
            }
        } catch (Exception $e) {
            Log::order_log(json_encode($e),'抛异常');
            Db::rollback();            
            return false;

        }        
	}
}
