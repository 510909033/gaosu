<?php
namespace app\common\tool;

use app\common\tool\CFormat;

class Verifier {
	/**
	 * 数据验证规则数组
	 *
	 * 格式
	 * 		其中{}括起来的为一组, 需要同时指定
	 * 		array(
	 * 			array( 键名列表
	 *              [, 'name:{key}' => 'key name']
	 *              [, 'accept']
	 *              [, 'required']
	 *              [, 'noempty']
	 *              [, 'int']
	 *              [, 'number']
	 *              [, 'boolean']
	 *              [, 'array']
	 *              [, 'date']
	 *              [, 'time']
	 *              [, 'datetime']
	 *              [, 'idcard']
	 *              [, 'email']
	 *              [, 'url']
	 *              {[, 'length'][, 'min' => 10][, 'max' => 128]}
	 *              [, 'method' => '方法名']
	 *              [, 'regexp' => '正则表达式']
	 *              [, 'values' => 'value1|value2|value3|...']
	 *              [, 'mobile']
	 *          ),
	 *          ...
	 * 		)
	 *
	 * 含义
	 *      name:{key} 参数的实际名称, {key}表示参数名
	 *      accept 表示只允许提交指定的键名
	 *      required 必须
	 *      noempty 不允许为空
	 *      int 必须是整数
	 *      number 必须是数字
	 *      boolean 必须是布尔值
	 *      array 必须是数组
	 *      date 必须是日期
	 *      time 必须是时间
	 *      datetime 必须是日期时间
	 *      idcard 必须是有效的身份证号
	 *      email 必须是合法的Email地址
	 *      url 必须是URL
	 *      length 限制字符串长度, 此时应至少指定以下两值中的一个
	 *          min 最小长度
	 *          max 最大长度
	 *      method 自定义校验方法, 校验成功应明确返回布尔值 true, 否则返回字符串错误信息
	 *      regexp 用户验证数据的正则表达式
	 *      values 用|分隔的值串, 表示参数的值只能是其中的某一个
	 *      mobile 是否为一个有效的手机号码
	 *
	 * 如果使用自定义方法, 那么在验证成功时应返回true, 否则必须返回一个包含key与msg两个键名的数组
	 *
	 * @access protected
	 * @var array
	 */
	protected static $validation_rules = array();

	/**
	 * 名称映射表
	 *
	 * @access protected
	 * @var array
	 */
	protected static $name_map = array();

	/**
	 * 获取验证规则
	 *
	 * @access protected
	 */
	final public static function getValidationRules() {
		return self::$validation_rules;
	}

	protected static $category = array(
		'verifier' => array(
			'{key} is required' => '必须指定 {key}',
			'{key} is not allowed' => '{key} 不是一个有效的参数',
			'{key} can not be empty' => '{key} 不能为空',
			'{key} must be an integer' => '{key} 必须是整数',
			'{key} must be an number' => '{key} 必须是数字',
			'{key} must be a boolean' => '{key} 必须是布尔值',
			'{key} must be an array' => '{key} 必须是数组',
			'{key} must be a email' => '{key} 必须是一个有效的E-mail地址',
			'{key} must be an url' => '{key} 必须是一个有效的URL地址',
			'{key} format must be YYYY-MM-DD' => '{key} 必须是一个 YYYY-MM-DD 格式的日期',
			'{key} format must be HH:ii:ss' => '{key} 必须是一个 HH:ii:ss 格式的时间',
			'{key} format must be YYYY-MM-DD HH:ii:ss' => '{key} 必须是一个 YYYY-MM-DD HH:ii:ss 格式的时间',
			'{key} is not a valid id card' => '{key} 不是一个有效的身份证号',
			'{key} is not a valid id mobile phone number' => '{key} 不是一个有效的手机号码',
			'{key} length cannot be less than {limit}' => '{key} 的长度不能小于 {limit} 个字符',
			'The length of the {key} is not greater than {limit}' => '{key} 的长度不能大于 {limit} 个字符',
			'{key} failed to match the regular expression' => '{key} 未能与正则表达式相匹配',
			'{key} was not a valid value' => '{key} 不是一个有效的值'
		),
	);
	/**
	 * 设置验证规则
	 *
	 * @access protected
	 * @param array $rules
	 */
	final public static function setValidationRules($rules = array()) {
		self::$validation_rules = $rules;
	}

	/**
	 * 使用一个规则对数据进行验证
	 *
	 * @access public
	 * @param array $data 数据数组
	 * @param array $rules 验证规则, 如果为空则会使用SELF::$validation_rule
	 * @return true|array 成功返回true, 如果有验证错误的项目则返回数组, array('key' => 验证失败的键名, 'msg' => 错误原因)
	 */
	final public static function validation(array $data, array $rules = array()) {
		if (empty($rules)) {
			if (empty(self::$validation_rules))
				return true;
			$rules = self::$validation_rules;
		}

		if (!is_array($rules[0]))
			$rules = array($rules);

		foreach ($rules as $rule) {
			foreach ($rule as $k => $v) {
				if (0 === strncasecmp('name:', $k, 5))
					self::$name_map[substr($k, 5)] = $v;
			}
			if (isset($rule[0]) && count($rule) > 1) {
				if (true !== ($result = self::validationByRule($rule[0], array_slice($rule, 1), $data)))
					return $result;
			}
		}

		return true;
	}

	/**
	 * 对单一规则进行验证
	 *
	 * @access public
	 * @param string|array $attrs 属性列表
	 * @param array $rule 验证规则
	 * @param array $data 需要验证的数据
	 * @return true|array 成功返回true, 如果有验证错误的项目则返回数组, array('key' => 验证失败的键名, 'msg' => 错误原因)
	 */
	final private static function validationByRule($attrs, $rule, $data) {
		if (is_string($attrs))
			$attrs = preg_split('/[\s,]+/', $attrs, -1, PREG_SPLIT_NO_EMPTY);
		if (isset($rule['method'])) {
			$custom_method = $rule['method'];
			unset($rule['method']);
		}
		$params = array_flip($rule);

		//必须输入
		if (isset($params['required'])) {
			foreach ($attrs as $key) {
				if (!isset($data[$key]))
					return array('key' => $key, 'rule' => 'required', 'msg' => self::t('verifier', '{key} is required', array('{key}' => self::getParamRealName($key))));
			}
		}

		//检测是否含有不接受的字段
		if (isset($params['accept'])) {
			foreach ($data as $key => $val) {
				if (!in_array($key, $attrs)) {
					return array('key' => $key, 'rule' => 'accept', 'msg' => self::t('verifier', '{key} is not allowed', array('{key}' => self::getParamRealName($key))));
				}
			}
		}

		//自定义验证方法
		if (isset($custom_method) && is_callable($custom_method)) {
			foreach ($attrs as $key) {
				if (isset($data[$key])) {
					if (true !== ($result = call_user_func_array($custom_method, array($data[$key]))))
						return array('key' => $key, 'rule' => 'method', 'msg' => $result);
				}
			}
		}

		//正则表达式
		if (isset($rule['regexp'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key])) {
					if (!preg_match('/' . $rule['regexp'] . '/', $data[$key]))
						return array('key' => $key, 'rule' => 'regexp', 'msg' => self::t('verifier', '{key} failed to match the regular expression', array('{key}' => self::getParamRealName($key))));
				}
			}
		}

		//指定的某个值
		if (isset($rule['values'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key])) {
					if (!in_array($data[$key], explode('|', $rule['values'])))
						return array('key' => $key, 'rule' => 'values', 'msg' => self::t('verifier', '{key} was not a valid value', array('{key}' => self::getParamRealName($key))));
				}
			}
		}

		//不能为空
		if (isset($params['noempty'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && empty($data[$key]))
					return array('key' => $key, 'rule' => 'noempty', 'msg' => self::t('verifier', '{key} can not be empty', array('{key}' => self::getParamRealName($key))));
			}
		}

		//必须是整数
		if (isset($params['int'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !CFormat::isInt($data[$key]))
					return array('key' => $key, 'rule' => 'int', 'msg' => self::t('verifier', '{key} must be an integer', array('{key}' => self::getParamRealName($key))));
			}
		}

		//必须是数字
		if (isset($params['number'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !CFormat::isNum($data[$key], 1))
					return array('key' => $key, 'rule' => 'number', 'msg' => self::t('verifier', '{key} must be an number', array('{key}' => self::getParamRealName($key))));
			}
		}

		//必须是布尔值
		if (isset($params['boolean'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !CFormat::isBoolean($data[$key]))
					return array('key' => $key, 'rule' => 'boolean', 'msg' => self::t('verifier', '{key} must be a boolean', array('{key}' => self::getParamRealName($key))));
			}
		}

		//必须是数组
		if (isset($params['array'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !is_array($data[$key]))
					return array('key' => $key, 'rule' => 'array', 'msg' => self::t('verifier', '{key} must be an array', array('{key}' => self::getParamRealName($key))));
			}
		}

		//必须是E-mail
		if (isset($params['email'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !filter_var($data[$key], FILTER_VALIDATE_EMAIL))
					return array('key' => $key, 'rule' => 'email', 'msg' => self::t('verifier', '{key} must be a email', array('{key}' => self::getParamRealName($key))));
			}
		}

		//必须是URL
		if (isset($params['url']) && 'values' != $params['url']) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !filter_var($data[$key], FILTER_VALIDATE_URL))
					return array('key' => $key, 'rule' => 'url', 'msg' => self::t('verifier', '{key} must be an url', array('{key}' => self::getParamRealName($key))));
			}
		}

		//验证日期
		if (isset($params['date'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !CFormat::isDateTime($data[$key], 1))
					return array('key' => $key, 'rule' => 'date', 'msg' => self::t('verifier', '{key} format must be YYYY-MM-DD', array('{key}' => self::getParamRealName($key))));
			}
		}

		//验证时间
		if (isset($params['time'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !CFormat::isDateTime($data[$key], 2))
					return array('key' => $key, 'rule' => 'time', 'msg' => self::t('verifier', '{key} format must be HH:ii:ss', array('{key}' => self::getParamRealName($key))));
			}
		}

		//验证日期时间
		if (isset($params['datetime'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && !CFormat::isDateTime($data[$key], 0))
					return array('key' => $key, 'rule' => 'datetime', 'msg' => self::t('verifier', '{key} format must be YYYY-MM-DD HH:ii:ss', array('{key}' => self::getParamRealName($key))));
			}
		}

		//验证身份证号码
		if (isset($params['idcard'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && true !== CFormat::isIdCard($data[$key]))
					return array('key' => $key, 'rule' => 'idcard', 'msg' => self::t('verifier', '{key} is not a valid id card', array('{key}' => self::getParamRealName($key))));
			}
		}

		//验证手机号
		if (isset($params['mobile'])) {
			foreach ($attrs as $key) {
				if (isset($data[$key]) && true !== CFormat::isMobile($data[$key]))
					return array('key' => $key, 'rule' => 'mobile', 'msg' => self::t('verifier', '{key} is not a valid id mobile phone number', array('{key}' => self::getParamRealName($key))));
			}
		}

		//指定了限制长度
		if (isset($params['length'])) {
			if (isset($rule['min'])) {
				foreach ($attrs as $key) {
					if (isset($data[$key]) && strlen($data[$key]) < $rule['min'])
						return array('key' => $key, 'rule' => 'min', 'msg' => self::t('verifier', '{key} length cannot be less than {limit}', array('{key}' => self::getParamRealName($key), '{limit}' => $rule['min'])));
				}
			}

			if (isset($rule['max'])) {
				foreach ($attrs as $key) {
					if (isset($data[$key]) && strlen($data[$key]) > $rule['max'])
						return array('key' => $key, 'rule' => 'max', 'msg' => self::t('verifier', 'The length of the {key} is not greater than {limit}', array('{key}' => self::getParamRealName($key), '{limit}' => $rule['max'])));
				}
			}
		}

		return true;
	}

	/**
	 * 获取参数的实际名称
	 *
	 * @access public
	 * @param string $param
	 * @return string
	 */
	public static function getParamRealName($param) {
		return isset(self::$name_map[$param]) ? self::$name_map[$param] : $param;
	}
	public static function t($category, $message, $params = array()) {
		$_arr = self::$category;
		
		if (isset($_arr[$category])) {
			if (isset($_arr[$category][$message]))
				$message = $_arr[$category][$message];
		}
		return array() !== $params ? strtr($message, $params) : $message;
	}

}