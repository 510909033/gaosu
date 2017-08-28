<?php
namespace app\common\tool;

class AjaxTool{
	/**
	 * 输出
	 *
	 * @access public
	 * @param string $msg 提示信息
	 * @param integer $state 状态
	 * @param array $data 数据数组
	 */
	public static function output($msg = '', $state = 51, $data = array()) {
		self::json(array(
			'state' => $state,
			'msg' => $msg,
			'data' => $data
		));
	}

	/**
	 * 输出Json提示信息
	 *
	 * @access public
	 * @param string $msg 提示信息
	 * @param integer $state 状态
	 * @param array $data 数据数组
	 */
	public static function outputJson($msg = '', $state = 621, $data = array()) {
		self::json(array(
			'state' => $state,
			'msg' => $msg,
			'data' => $data
		));
	}
	/*
	*验证图片类型
	*/
	public static function getFileExt($mime_type) {
		$ext = '';
		switch ($mime_type) {
			case 'image/png' :
				$ext = 'png';
				break;
			case 'image/jpg' :
			case 'image/jpeg' :
				$ext = 'png';
				break;
			case 'image/gif' :
				$ext = 'gif';
		}
		return $ext;
	}
	/**
	 * 输出成功信息
	 *
	 * @access public
	 * @param array $data 数据数组
	 * @param string $msg 提示信息
	 */
	public static function outputDone($data = array(), $msg = 'done') {
		self::outputJson($msg, 0, $data);
	}

	/**
	 * 输出失败信息
	 *
	 * @access public
	 * @param array $data 数据数组
	 * @param string $msg 提示信息
	 */
	public static function outputFailed($data = array(), $msg = 'failed') {
		self::outputJson($msg, 611, $data);
	}

	/**
	 * 输出错误信息
	 *
	 * @access public
	 * @param string $msg 错误信息
	 * @param array $data 数据数组
	 */
	public static function outputError($msg = 'error', $data = array()) {
		self::json(array(
			'state' => 621,
			'msg' => $msg,
			'data' => $data
		));
	}

	/**
	 * 输出Json数据
	 *
	 * @access public
	 * @param array $data
	 * @param string $jsonp_callback_var jsonp回调函数名称, null表示非jsonp请求
	 */
	public static function json($data = array(), $jsonp_callback_var = null, $options = JSON_UNESCAPED_UNICODE) {
		die(json_encode($data, $options));
	}
	/**
	 * 检查字符串是否为正确的手机号
	 *
	 * @access public
	 * @param string $str
	 * @return boolean
	 */
	public static function isMobile($str) {
		return (bool) preg_match('/^((13[\d])|(147)|(15[\d])|17[\d]|(18[\d]))[0-9]{8}$/', $str);
	}
	/**
	 * 检查字符串是否为一个正确的Email地址
	 *
	 * @access public
	 * @param string $url
	 * @return boolean
	 */
	public static function isEmail($url) {
		return (bool) filter_var($url, FILTER_VALIDATE_EMAIL);
	}
	/**
	 * 检查数组是否为一个关联数组
	 *
	 * @param array $arr
	 * @return bool
	 */
	public static function arrayIsAssoc(array $arr) {
		if (array() === $arr)
			return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}			
}