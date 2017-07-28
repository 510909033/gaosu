<?php
use think\Log;

/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
class WxPayException extends Exception {
	public function errorMessage()
	{
	    
	    Log::order_log($this->getMessage(), '异常错误');
		return $this->getMessage();
	}
}
