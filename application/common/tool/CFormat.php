<?php
namespace app\common\tool;

class CFormat {
	private static $pinyin_dict;

	/**
	 * 安全的ip2long 解决负数问题
	 * 
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param string $ip IP地址
	 * @param boolean $unsigned 是否转为无符号整数
	 * @return integer
	 */
	public static function ip2long($ip, $unsigned = true) {
		$ip = ip2long($ip);
		if ($unsigned)
			return bindec(decbin($ip));
		return ($ip > 0x7FFFFFFF) ? $ip - 0x100000000 : $ip;
	}
	
	/**
	 * 判断字符串是否为整数
	 *
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param string $str 要检查的字符
	 * @return boolean
	 */
	public static function isInt($str) {
		return 0 === strcmp($str, (int) $str);
	}

	/**
	 * 判断字符串是否为正整数
	 *
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param array|int|string $param 要检查的字符
	 * @param int $limit 限定需要满足的最小值
	 * @return boolean
	 */
	public static function isPint($param, $limit = 0) {
		if (is_array($param)) {
			if (2 != count($param) || !isset($param[0][$param[1]]))
				return false;
			$param = $param[0][$param[1]];
		}
		return self::isInt($param) && $param >= $limit;
	}

	/**
	 * gbk转utf-8
	 *
	 * @author XingDongHai (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param string $data 要转换的数据
	 * @return string
	 */
	public static function g2u($data) {
		if (is_array($data)) {
			foreach ((array)$data as $k => $v ) {
				if (is_array($v))
					$data[$k] = self::g2u($v);
				else
					$data[$k] = iconv('gbk', 'UTF-8//IGNORE', $v);
			}
			return $data;
		} else {
			return iconv('gbk', 'UTF-8//IGNORE', $data);
		}
	}
	
	/**
	 * utf-8转gbk
	 *
	 * @author XingDongHai (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param string $data 要转换的数据
	 * @return string
	 */
	public static function u2g($data) {
		if (is_array($data)) {
			foreach ((array)$data as $k => $v ) {
				if (is_array($v))
					$data[$k] = self::u2g($v);
				else
					$data[$k] = iconv('UTF-8', 'gbk//IGNORE', $v);
			}
			return $data;
		} else {
			return iconv('UTF-8', 'gbk//IGNORE', $data);
		}
	}

	/**
	 * 将字符串转为Unicode编码
	 *
	 * @author Internet
	 * @version 0.10
	 * @param string $str 要转换的字符串
	 * @param string $encoding 源字符串的编码
	 * @return string
	 */
	public static function str2Unicode($str, $encoding = 'UTF-8') {
		$str = iconv($encoding, 'UCS-2', $str);
		$arrstr = str_split($str, 2);
		$unistr = '';
		for ($i = 0, $len = count($arrstr); $i < $len; $i++) {
			$dec = hexdec(bin2hex($arrstr[$i]));
			$unistr .= str_pad($dec, 4, '0', STR_PAD_LEFT) . ' ';
		}
		return $unistr;
	}

	/**
	 * 解码带有Emoji字符的json字符串
	 * 添加于2015-05-20, 目前为测试版本, 尚未经过大规模测试
	 *
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param string $data 要检查的字符
	 * @param bool $assoc 是否要返回关联数组
	 * @return array|null
	 */
	public static function jsonDecodeWithEmoji($data, $assoc = true) {
		return json_decode($data, $assoc);
		/*$data = json_encode(array('tmp' => $data));
		$data = str_replace('\\"', '\"', $data);
		$data = str_replace('\\\\', '', $data);
		$data = json_decode($data, $assoc);
		return $data;*/
	}

	/**
	 * 过滤字符串中的Emoji字符
	 *
	 * @author Internet
	 * @param string $text 要过滤的字符串
	 * @return string
	 */
	public static function removeEmoji($text) {
		return preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $text);

		/*$clean_text = '';

		// Match Emoticons
		$regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
		$clean_text = preg_replace($regex_emoticons, '', $text);

		// Match Miscellaneous Symbols and Pictographs
		$regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
		$clean_text = preg_replace($regex_symbols, '', $clean_text);

		// Match Transport And Map Symbols
		$regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
		$clean_text = preg_replace($regex_transport, '', $clean_text);

		// Match Miscellaneous Symbols
		$regex_misc = '/[\x{2600}-\x{26FF}]/u';
		$clean_text = preg_replace($regex_misc, '', $clean_text);

		// Match Dingbats
		$regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
		$clean_text = preg_replace($regex_dingbats, '', $clean_text);

		return $clean_text;*/
	}
	
	/**
	 * 正则方式检查参数是否为数字
	 *
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param string $str 要检查的字符
	 * @param boolean $is_decimal  是否为小数
	 * @return boolean
	 */
	public static function isNum($str, $is_decimal = false) {
		if ('' == $str)
			return false;
	
		$pattern = $is_decimal ? '[^0-9.]' : '[^0-9]';
	
		if (preg_match('/' . $pattern . '/', $str))
			return false;
	
		if ($is_decimal) {
			if (substr_count($str, '.') > 1)
				return false;
				
			if (preg_match('/(^\.|\.$)/', $str))
				return false;
		}
	
		return true;
	}
	
	/**
	 * 判断是否为boolean
	 *
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param mixed $value
	 * @return boolean
	 */
	public static function isBoolean($value) {
		if (false === $value || true === $value || 1 === $value || 0 === $value)
			return true;
		return in_array($value, array('true', 'false', '1', '0', 'yes', 'no', 'on', 'off'));
	}
	
	/**
	 * 验证时间格式是否正确
	 *
	 * @author XingDongHai (http://www.xingdonghai.cn)
	 * @version 0.11 (Last update at 2013-01-11)
	 * @param string $str 要检查的字符串
	 * @param int $item 要检查的项目。0为检查日期与时间；1为检查日期；2为检查时间
	 * @param boolean $strict 是否为严格的检查模式
	 * @return boolean
	 */
	public static function isDateTime($str, $item = 0, $strict = false) {
		$str = trim($str);
		if (0 == $item) {
			if (false === strpos($str, ' ')) {
				if ($strict)
					return false;
				$arr = array($str, '00:00:00');
			} else {
				$arr = explode(' ', $str);
			}
		} else {
			$arr = array($str, $str);
		}

		if (0 == $item || 1 == $item) {
			if (!preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $arr[0]))
				return false;
			$d = explode('-', $arr[0]);
			if (!checkdate($d[1], $d[2], $d[0]))
				return false;
		}
	
		if (0 == $item || 2 == $item) {
			if (!preg_match('/^[\d]{1,2}:[\d]{1,2}(?::[\d]{1,2}|)$/', $arr[1]))
				return false;
	
			$t = explode(':', $arr[1]);
			if ($t[0] < 0 || $t[0] > 23 || $t[1] < 0 || $t[1] > 59)
				return false;
			if (isset($t[2]) && ($t[2] < 0 || $t[2] > 59))
				return false;
		}
	
		return true;
	}
	
	/**
	 * 验证是身份证是否正确
	 * 
	 * @author XingDongHai (http://www.xingdonghai.cn)
	 * @version 0.2 (Last update at 2013-11-07)
	 * @param string $idcard 要检查的身份证号
	 * @param bool $allow_len15 是否允许15位老身份证号码通过验证
	 * @return boolean
	 */
	public static function isIdCard($idcard, $allow_len15 = false) {
		$area = array(
			11 => '北京',
			12 => '天津',
			13 => '河北',
			14 => '山西',
			15 => '内蒙古',
			21 => '辽宁',
			22 => '吉林',
			23 => '黑龙江',
			31 => '上海',
			32 => '江苏',
			33 => '浙江',
			34 => '安徽',
			35 => '福建',
			36 => '江西',
			37 => '山东',
			41 => '河南',
			42 => '湖北',
			43 => '湖南',
			44 => '广东',
			45 => '广西',
			46 => '海南',
			50 => '重庆',
			51 => '四川',
			52 => '贵州',
			53 => '云南',
			54 => '西藏',
			61 => '陕西',
			62 => '甘肃',
			63 => '青海',
			64 => '宁夏',
			65 => '新疆',
			71 => '台湾',
			81 => '香港',
			82 => '澳门',
			91 => '国外'
		);

		if (!isset($area[substr($idcard, 0, 2)]) || preg_match('/[^\dXx]/', $idcard))
			return false;
		
		//如果是15位身份证
		if (15 == strlen($idcard) && $allow_len15) {
			// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
			if (false !== array_search(substr($idcard, 12, 3), array('996', '997', '998', '999'))) {
				$idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
			} else {
				$idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
			}
			$idcard = $idcard . self::idcardVerifyNumber($idcard);
		}
		if (18 != strlen($idcard))
			return false;

		if (!self::checkIdcardFormat($idcard))
			return false;

		return self::idcardVerifyNumber(substr($idcard, 0, 17)) != strtoupper(substr($idcard, 17, 1)) ? false : true;
	}

	/**
	 * 检查身份证号码的格式
	 * 针对近期出现身份证号码使用210000000000000000注册的用户
	 * 验证地区码(3-6位)以及生日(6-14)位
	 *
	 * @param $idcard
	 *
	 * @return bool
	 */
	public static function checkIdcardFormat($idcard) {
		$code = (int) substr($idcard, 3, 3);
		if (0 == $code)
			return false;

		if (18 == strlen($idcard)) {
			$birth_year = (int) substr($idcard, 6, 4);
			$birth_mondth = (int) substr($idcard, 10, 2);
			$birth_day = (int) substr($idcard, 12, 2);
			if ($birth_year < 1900 || (0 == $birth_mondth || $birth_mondth > 12) || (0 == $birth_day || $birth_day > 31))
				return false;
			return true;
		}
		return false;
	}
	
	/**
	 * 计算身份证校验码，根据国家标准GB 11643-1999
	 * 
	 * @access private
	 * @param string $idcard_base
	 * @return boolean|string
	 */
	private static function idcardVerifyNumber($idcard_base) {
		if (17 != strlen($idcard_base))
			return false;
	
		//加权因子
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		// 校验码对应值
		$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
	
		$checksum = 0;
		for ($i = 0, $len = strlen($idcard_base); $i < $len; $i++) {
			$checksum += ((int) substr($idcard_base, $i, 1)) * $factor[$i];
		}
		$mod = $checksum % 11;
		$verify_number = $verify_number_list[$mod];
		return $verify_number;
	}
	
	/**
	 * 模拟JavaScript的escape函数
	 *
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10 (Last update at 2012-12-21)
	 * @param string $str 要处理的字符串
	 * @param string $charset 字符编码
	 * @return string 处理后的结果
	 */
	public static function escape($str, $charset = 'utf8') {
		if ('gbk' == $charset) {
			preg_match_all('/[\x80-\xff].|[\x01-\x7f]+/', $str, $r);
			$arr = $r[0];
			foreach ($arr as $k => $v){
				if (ord($v[0]) < 128)
					$arr[$k] = rawurlencode($v);
				else
					$arr[$k] = '%u' . bin2hex(iconv('GBK', 'UCS-2', $v));
			}
		} else {
			preg_match_all('/[\xc2-\xdf][\x80-\xbf]+|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]+/e', $str, $r);
			$arr = $r[0];
			foreach ($arr as $k => $v) {
				if (ord($v[0]) < 223)
					$arr[$k] = rawurlencode(utf8_decode($v));
				else
					$arr[$k] = '%u' . bin2hex(iconv('UTF-8', 'UCS-2', $v));
			}
		}
		return join('', $arr);
	}

	/**
	 * 此函数是escape()扩充，支持数组
	 *
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param string|array $data
	 * @internal param string $str 要处理的字符串
	 * @return string 处理后的结果
	 */
	public static function escapeAry($data) {
		if (is_array($data)) {
			foreach ($data as $k => $v)
				$data[$k] = self::escapeAry($v);
			return $data;
		} else {
			return self::escape($data);
		}
	}
	
	/**
	 * 模拟JavaScript的unescape函数
	 *
	 * @author DongHai Hsing (http://www.xingdonghai.cn)
	 * @version 0.10
	 * @param string $str 要处理的字符串
	 * @return string 处理后的结果
	 */
	public static function unescape($str) {
		$str = rawurldecode($str);
		preg_match_all('/(?:%u.{4})|.+/', $str, $r);
		$arr = $r[0];
		foreach ($arr as $k => $v) {
			if ('%u' == substr($v, 0, 2) && 6 == strlen($v))
				$arr[$k] = iconv('UCS-2', 'UTF-8', pack('H4', substr($v, -4)));
		}
		return join('', $arr);
	}
	
	/**
	 * utf-8中文截取
	 *
	 * @access public
	 * @param string $str
	 * @param int $length
	 * @param int $start
	 * @return string
	 */
	public static function substrCN($str, $length, $start = 0) {
		if (strlen($str) < $start + 1)
			return '';
	
		preg_match_all('/./su', $str, $ar);
		$str = $tstr = '';

		for ($i = 0; isset($ar[0][$i]); $i++) {
			if (strlen($tstr) < $start) {
				$tstr .= $ar[0][$i];
			} else {
				if (strlen($str) < $length + strlen($ar[0][$i]))
					$str .= $ar[0][$i];
				else
					break;
			}
		}
		return $str;
	}

	/**
	 * 计算字符串长度, 中文按2个字符计
	 *
	 * @access public
	 * @param string $str
	 * @param string $charset 字符集
	 * @return integer
	 */
	public static function strlenCN2($str, $charset = 'UTF8') {
		return (strlen($str) + mb_strlen($str, $charset)) / 2;
	}

	/**
	 * 判断字符串编码是否是utf-8
	 *
	 * @access public
	 * @param string $str 目标字符串
	 * @param integer $method 验证方式
	 *      1 : mb_check_encoding
	 *      2 : mb_detect_encoding
	 *      3 : RegExp1
	 *      4 : RegExp2
	 *      Other : serialize + RegExp
	 * @return boolean
	 */
	public static function isUtf($str, $method = 1) {
		if (1 == $method && function_exists('mb_check_encoding')) {

			return mb_check_encoding($str, 'UTF-8');

		} elseif (2 == $method) {

			if (function_exists('mb_detect_encoding')) {
				$charset = mb_detect_encoding($str, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
				if ('UTF-8' == $charset)
					return 1;
				elseif ('ASCII' == $charset)
					return 2;
				else
					return 0;
			} else {
				if (strlen($str) < 3)
					return false;

				$lastch = 0;
				$begin = 0;
				$BOM = true;
				$BOMchs = array(0xEF, 0xBB, 0xBF);
				$good = 0;
				$bad = 0;
				$notAscii = 0;
				for ($i = 0; $i < strlen($str); $i++) {
					$ch = ord($str[$i]);
					if ($begin < 3) {
						$BOM = ($BOMchs[$begin] == $ch);
						$begin += 1;
						continue;
					}

					if (4 == $begin && $BOM)
						break;

					if ($ch >= 0x80)
						$notAscii++;

					if (0x80 == ($ch & 0xC0)) {
						if (0xC0 == ($lastch & 0xC0))
							$good += 1;
						elseif (0 == ($lastch & 0x80))
							$bad += 1;
					} elseif (0xC0 == ($lastch & 0xC0)) {
						$bad += 1;
					}

					$lastch = $ch;
				}

				if (4 == $begin && $BOM)
					return 1;
				elseif (0 == $notAscii)
					return 2;
				elseif ($good >= $bad)
					return 1;
				else
					return 0;
			}

		} elseif (3 == $method) {

			return preg_match('%^(?:
				[\x09\x0A\x0D\x20-\x7E]					# ASCII
				| [\xC2-\xDF][\x80-\xBF]				# non-overlong 2-byte
				|  \xE0[\xA0-\xBF][\x80-\xBF]			# excluding overlongs
				| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}		# straight 3-byte
				|  \xED[\x80-\x9F][\x80-\xBF]			# excluding surrogates
				|  \xF0[\x90-\xBF][\x80-\xBF]{2}		# planes 1-3
				| [\xF1-\xF3][\x80-\xBF]{3}				# planes 4-15
				|  \xF4[\x80-\x8F][\x80-\xBF]{2}		# plane 16
			)*$%xs', $str);

		} elseif (4 == $method) {

			return (true == preg_match('/^([' . chr(228) . '-' . chr(233) . ']{1}[' . chr(128) . '-' . chr(191) . ']{1}[' . chr(128) . '-' . chr(191) . ']{1}){1}/', $str)
				|| true == preg_match('/([' . chr(228) . '-' . chr(233) . ']{1}[' . chr(128) . '-' . chr(191) . ']{1}[' . chr(128) . '-' . chr(191) . ']{1}){1}$/', $str)
				|| true == preg_match('/([' . chr(228) . '-' . chr(233) . ']{1}[' . chr(128) . '-' . chr(191) . ']{1}[' . chr(128) . '-' . chr(191) . ']{1}){2,}/', $str)
			);

		} elseif (5 == $method) {

			$c = 0;
			$b = 0;
			$bits = 0;
			$len = strlen($str);
			for ($i = 0; $i < $len; $i++) {
				$c = ord($str[$i]);
				if ($c > 128) {
					if (($c >= 254))
						return false;
					elseif ($c >= 252)
						$bits = 6;
					elseif ($c >= 248)
						$bits = 5;
					elseif ($c >= 240)
						$bits = 4;
					elseif ($c >= 224)
						$bits = 3;
					elseif ($c >= 192)
						$bits = 2;
					else
						return false;
					if (($i + $bits) > $len)
						return false;
					while ($bits > 1) {
						$i++;
						$b = ord($str[$i]);
						if ($b < 128 || $b > 191)
							return false;
						$bits--;
					}
				}
			}
			return true;

		} else {

			return (bool) preg_match('//u', serialize($str));

		}
	}

	/**
	 * 清除UTF8字符串前的Bom
	 *
	 * @access public
	 * @param string $str
	 * @return string
	 */
	public static function removeUTF8Bom($str) {
		if (substr($str, 0, 3) == pack('CCC', 239, 187, 191))
			return substr($str, 3);
		return $str;
	}
	
	/**
	 * 对字符串进行严格过滤
	 * 
	 * @access public
	 * @param string $s
	 * @return string
	 */
	public static function filterStr($s) {
		if (function_exists('filter_var'))
			return filter_var($s, FILTER_SANITIZE_STRING);
		else 
			return htmlspecialchars(strip_tags($s));
	}

	/**
	 * 对数组中的字符串值严格过滤
	 *
	 * @access public
	 * @param array $arr
	 * @return $arr
	 */
	public static function filterArr($arr) {
		foreach ($arr as $k => $v) {
			if (is_array($v))
				$arr[$k] = self::filterArr($v);
			elseif (is_string($v))
				$arr[$k] = self::filterStr($v);
		}
		return $arr;
	}

	/**
	 * 检查字符串是否为一个URL
	 *
	 * @access public
	 * @param string $url
	 * @return boolean
	 */
	public static function isUrl($url) {
		return (bool) filter_var($url, FILTER_VALIDATE_URL);
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
	 * 获取手机号运营商归属
	 *
	 * @access public
	 * @param string $str
	 * @return string
	 */
	public static function getMobileOperator($str) {
		$str = substr($str, 0, 3);
		if (in_array($str, array('134', '135', '136', '137', '138', '139', '147', '150', '151', '152', '157', '158', '159', '178', '182', '183', '184', '187', '188'))) //移动
			return 'mobile';
		elseif (in_array($str, array('130', '131', '132', '145', '155', '156', '171', '176', '185', '186'))) //联通
			return 'unicom';
		elseif (in_array($str, array('133', '153', '173', '177', '180', '181', '189'))) //电信
			return 'telecom';
		elseif (in_array($str, array('170'))) //阿里
			return 'alitongxin';
		else
			return 'unknown';
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
	 * 获取带有声调的汉语拼音
	 *
	 * @param string $str 目标汉字字符串
	 * @param string $delimiter 转换之后拼音之间分隔符
	 * @param boolean $ignore_without 是否忽略非汉字内容
	 * @return string
	 */
	public static function getPinyinWithTone($str, $delimiter = '', $ignore_without = false) {
		if (!class_exists('Pinyin', false))
			Dh::import('system.library.Pinyin.Pinyin');
		return Pinyin::trans($str, array('delimiter' => $delimiter, 'only_chinese' => $ignore_without, 'accent' => true));

		/*if (empty(self::$pinyin_dict)) {
			$dict_file = Dh::getPathOfAlias('system.data.dict') . DIRECTORY_SEPARATOR . 'pinyin.dat';
			if (!file_exists($dict_file))
				return 'pinyin dict file does not exist.';
			if (!is_readable($dict_file))
				return 'pinyin dict file does not readable.';
			self::$pinyin_dict = file_get_contents($dict_file);
		}

		$ret = '';
		for ($i = 0, $len = mb_strlen($str, 'utf-8'); $i < $len; $i++) {
			$word = mb_substr($str, $i, 1, 'utf-8');
			if (preg_match('/^[\x{4e00}-\x{9fa5}]$/u', $word) && preg_match('/\,' . preg_quote($word) . '(.*?)\,/', self::$pinyin_dict, $matches)) {
				$ret .= $matches[1] . $delimiter;
			} else if (!$ignore_without) {
				$ret .= $word;
			}
		}

		return trim($ret);*/
	}

	/**
	 * 获取不带有声调的汉语拼音
	 *
	 * @param string $str 目标汉字字符串
	 * @param string $delimiter 转换之后拼音之间分隔符
	 * @param boolean $ignore_without 是否忽略非汉字内容
	 * @return string
	 */
	public static function getPinyinWithoutTone($str, $delimiter = '', $ignore_without = false) {
		if (!class_exists('Pinyin', false))
			Dh::import('system.library.Pinyin.Pinyin');
		return Pinyin::trans($str, array('delimiter' => $delimiter, 'only_chinese' => $ignore_without, 'accent' => false));

		/*return strtr(self::getPinyinWithTone($str, $delimiter, $ignore_without), array(
			'ā' => 'a',
			'á' => 'a',
			'ǎ' => 'a',
			'à' => 'a',
			'ō' => 'o',
			'ó' => 'o',
			'ǒ' => 'o',
			'ò' => 'o',
			'ē' => 'e',
			'é' => 'e',
			'ě' => 'e',
			'è' => 'e',
			'ī' => 'i',
			'í' => 'i',
			'ǐ' => 'i',
			'ì' => 'i',
			'ū' => 'u',
			'ú' => 'u',
			'ǔ' => 'u',
			'ù' => 'u',
			'ǖ' => 'v',
			'ǘ' => 'v',
			'ǚ' => 'v',
			'ǜ' => 'v',
			'ü' => 'v'
		));*/
	}

	/**
	 * 获取汉语拼音首字母
	 *
	 * @param string $str 目标汉字字符串
	 * @param string $delimiter 转换之后拼音之间分隔符
	 * @return string
	 */
	public static function getPinyinUcwords($str, $delimiter = '') {
		if (!class_exists('Pinyin', false))
			Dh::import('system.library.Pinyin.Pinyin');
		return Pinyin::letter($str, array('delimiter' => $delimiter));

		/*$pinyin_without_tone = ucwords(self::getPinyinWithoutTone($str, ' ', true));
		$ucwords = preg_replace('/[^A-Z]/', '', $pinyin_without_tone);
		if (!empty($delimiter))
			$ucwords = implode($delimiter, str_split($ucwords));
		return $ucwords;*/
	}

	/**
	 * 格式化数字
	 *
	 * @access public
	 * @param number $num 要格式化的数字
	 * @param integer $decimals 保留小数的位数
	 * @param string $decimalpoint 规定用作小数点的字符串
	 * @param string $separator 规定用作分隔符的字符串
	 * @param integer $bit 规定整数部分分割的位数
	 * @return string|integer
	 */
	public static function numberFormat($num, $decimals = 0, $decimalpoint = '.', $separator = ',', $bit = 3) {
		if (3 == $bit) {
			return number_format($num, $decimals, $decimalpoint, $separator);
		} else {
			$symbol = '';
			if ($num != abs($num)) {
				$symbol = '-';
				$num = substr($num, 1);
			}

			if (false === strpos($num, '.')) {
				$int = (string) $num;
				$decimal = '';
			} else {
				list($int, $decimal) = explode('.', $num);
			}

			$s = '';
			for ($i = 1, $len = strlen($int); $i <= $len; $i++)
				$s = $int{($len - $i)} . ($i > 1 && 0 == ($i - 1) % 4 ? $separator : '') . $s;

			return $symbol . $s . ($decimals ? $decimalpoint . substr(sprintf('%.' . $decimals . 'f', '0.' . $decimal), 2) : '');
		}
	}

	/**
	 * 将SimpleXMLElement数据转为数组
	 *
	 * @param SimpleXMLElement|string $xml_object_or_xml_string 要转换的SimpleXMLElement对象, 或XML文档对象
	 * @return array
	 */
	public static function xml2array($xml_object_or_xml_string) {
		return json_decode( json_encode( is_string($xml_object_or_xml_string) ? simplexml_load_string($xml_object_or_xml_string, 'SimpleXMLElement', LIBXML_NOCDATA) : (array) $xml_object_or_xml_string ) , true );
	}


	/**
	 * 将一个字符串部分字符用*替代隐藏
	 *
	 * @param string$string 待转换的字符串
	 * @param integer $bengin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
	 * @param integer $len 需要转换成*的字符个数，当$type=4时，表示右侧保留长度
	 * @param integer $type 转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
	 * @param string $glue 分割符
	 * @return string
	 */
	public static function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = '@') {
		if (empty($string))
			return false;
		$array = array();
		if (0 == $type || 1 == $type || 4 == $type) {
			$strlen = $length = mb_strlen($string);
			while ($strlen) {
				$array[] = mb_substr($string, 0, 1, 'utf8');
				$string = mb_substr($string, 1, $strlen, 'utf8');
				$strlen = mb_strlen($string);
			}
		}
		if (0 == $type) {
			for ($i = $bengin; $i < ($bengin + $len); $i++) {
				if (isset($array[$i]))
					$array[$i] = '*';
			}
			$string = implode('', $array);
		}
		else if (1 == $type) {
			$array = array_reverse($array);
			for ($i = $bengin; $i < ($bengin + $len); $i++) {
				if (isset($array[$i]))
					$array[$i] = '*';
			}
			$string = implode('', array_reverse($array));
		}
		else if (2 == $type) {
			$array = explode($glue, $string);
			$array[0] = hideStr($array[0], $bengin, $len, 1);
			$string = implode($glue, $array);
		}
		else if (3 == $type) {
			$array = explode($glue, $string);
			$array[1] = hideStr($array[1], $bengin, $len, 0);
			$string = implode($glue, $array);
		}
		else if (4 == $type) {
			$left = $bengin;
			$right = $len;
			$tem = array();
			for ($i = 0; $i < ($length - $right); $i++) {
				if (isset($array[$i]))
					$tem[] = $i >= $left ? '*' : $array[$i];
			}
			$array = array_chunk(array_reverse($array), $right);
			$array = array_reverse($array[0]);
			for ($i = 0; $i < $right; $i++)
				$tem[] = $array[$i];
			$string = implode('', $tem);
		}
		return $string;
	}

	/**
	 * json_encode别名方法
	 *
	 * @param array $value
	 * @param int $options
	 * @param int $depth
	 * @return string
	 */
	public static function jsonEncode($value, $options = JSON_UNESCAPED_UNICODE) {
		return json_encode($value, $options);
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
