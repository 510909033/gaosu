<?php
namespace app\test\controller;

use think\Controller;
use app\common\model\SysLogTmp;
use think\Log;

class IndexController extends Controller
{
    //初始化
    protected function _initialize()
    {


    }

    public function indexAction()
    {
        try {
            $this->responseMsgAction();
        } catch (\Exception $e) {
            SysLogTmp::log('微信api异常', $e->getMessage(), 0, __FILE__.',line='.__LINE__);
        }
        
    }

    /*
     * // 接收事件推送并回复
     * public function reponseMsgAction(){
     * //1.获取到微信推送过来post数据（xml格式）
     * $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
     * $tmpstr = $postArr;
     * //2.处理消息类型，并设置回复类型和内容
     * $postObj = simplexml_load_string( $postArr );
     * //$postObj->ToUserName = '';
     * //$postObj->FromUserName = '';
     * //$postObj->CreateTime = '';
     * //$postObj->MsgType = '';
     * //$postObj->Event = '';
     * // gh_e79a177814ed
     * //判断该数据包是否是订阅的事件推送
     * if( strtolower( $postObj->MsgType) == 'event'){
     *
     * //如果是关注 subscribe 事件
     * if( strtolower($postObj->Event == 'subscribe') ){
     *
     * //回复用户消息(纯文本格式)
     * $toUser = $postObj->FromUserName;
     * $fromUser = $postObj->ToUserName;
     * $time = time();
     * $msgType = 'text';
     *
     * $content = '欢迎关注智慧高速MPS'.PHP_EOL.'OpenID为：'.$postObj->FromUserName;
     *
     * $content = '欢迎关注公众帐号'.$postObj->FromUserName.'-'.$postObj->ToUserName;
     *
     * $template = "<xml>
     * <ToUserName><![CDATA[%s]]></ToUserName>
     * <FromUserName><![CDATA[%s]]></FromUserName>
     * <CreateTime>%s</CreateTime>
     * <MsgType><![CDATA[%s]]></MsgType>
     * <Content><![CDATA[%s]]></Content>
     * </xml>";
     * $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
     *
     * echo $info;
     *
     * }
     * }
     *
     * //用户发送腾放关键字的时候，回复一个单图文
     * if( strtolower($postObj->MsgType) == 'text' && trim($postObj->Content)=='腾放' ){
     * $toUser = $postObj->FromUserName;
     * $fromUser = $postObj->ToUserName;
     * $arr = array(
     * array(
     * 'title'=>'微信',
     * 'description'=>"微信改变生活",
     * 'picUrl'=>'https://res.wx.qq.com/a/wx_fed/weixin_portal/res/static/img/1EqrNF5.png',
     * 'url'=>'https://weixin.qq.com/',
     * ),
     * array(
     * 'title'=>'qq',
     * 'description'=>"每一天，乐在沟通",
     * 'picUrl'=>'http://android-artworks.25pp.com/fs08/2016/06/08/7/1_173210056691eb8d6f8398d53d320fac_con.png',
     * 'url'=>'http://www.qq.com',
     * ),
     * );
     * $template = "<xml>
     * <ToUserName><![CDATA[%s]]></ToUserName>
     * <FromUserName><![CDATA[%s]]></FromUserName>
     * <CreateTime>%s</CreateTime>
     * <MsgType><![CDATA[%s]]></MsgType>
     * <ArticleCount>".count($arr)."</ArticleCount>
     * <Articles>";
     * foreach($arr as $k=>$v){
     * $template .="<item>
     * <Title><![CDATA[".$v['title']."]]></Title>
     * <Description><![CDATA[".$v['description']."]]></Description>
     * <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
     * <Url><![CDATA[".$v['url']."]]></Url>
     * </item>";
     * }
     *
     * $template .="</Articles>
     * </xml> ";
     * echo sprintf($template, $toUser, $fromUser, time(), 'news');
     *
     * //注意：进行多图文发送时，子图文个数不能超过10个
     * }else{
     * $ch = curl_init();
     * $url = 'http://wthrcdn.etouch.cn/weather_mini?city='.urlencode($postObj->Content);
     * $str = file_get_contents($url); //调用接口获得天气数据
     * $result= gzdecode($str); //解压
     * echo $result;
     *
     * //执行HTTP请求
     *
     * $arr = json_decode($result,true);
     * $content = $arr['data']['city'];
     *
     *
     * }
     * }
     */
    public function responseMsgAction()
    {
        
        // get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        
        // extract post data
        if (! empty($postStr)) {
            SysLogTmp::log('微信发送过来的数据', print_r($postStr,true), 0, __FILE__.',line='.__LINE__);
            
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            
            $event = $postObj->Event;
            if ($event && $RX_TYPE){
                $method = $RX_TYPE.'_'.$event;
                //event_subscribe
                //event_VIEW
            }else if ($RX_TYPE){
                $method = $RX_TYPE;
            }
            
            if (method_exists($this, $method)){
                $resultStr = $this->$method($postObj);
            }ELSE{
                //
                SysLogTmp::log('微信API['.$method.']事件方法尚未编写', print_r($postStr,true), 0, __FILE__.',line='.__LINE__);
                $resultStr='';
            }
            
            
//             switch ($RX_TYPE) {
//                 case "text":
//                     $resultStr = $this->handleText($postObj);
//                     break;
//                 case "event":
//                     $resultStr = $this->handleEvent($postObj);
//                     break;
//                 case "location":
//                     $resultStr = $this->handleLocation($postObj);
//                     break;
//                 default:
//                     $resultStr = "Unknow msg type: " . $RX_TYPE;
//                     break;
//             }
            SysLogTmp::log('微信API'.$RX_TYPE.'返回结果', $resultStr, 0, __FILE__.',line='.__LINE__);
            echo $resultStr;
            exit();
        } else {
            echo "";
            exit();
        }
    }

    public function text($postObj)
    {
        

        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $time = time();
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";
        if (!empty($keyword)) {
            $msgType = "text";

            // 天气
            $data = $this->weatherAction($postObj);
            
            if (empty($data['data'])) {
                $contentStr = "抱歉，没有查到\"" . $postObj->Content . "\"的天气信息！";
            } else {
                $contentStr = "【" . $data['data']['city'] . "天气预报】\n" .
                    '当前温度:' . $data['data']['wendu'] . "\n".
                    '温馨提示:'.$data['data']['ganmao']."\n".
                    "【 今日天气】\n" .
                    '最高温度：'.$data['data']['forecast'][0]['high']."\n".
                    '最低温度：'.$data['data']['forecast'][0]['low']."\n".
                    '风力：'.$data['data']['forecast'][0]['fengli']."\n".
                    '风向：'.$data['data']['forecast'][0]['fengxiang']."\n".
                    '天气类型：'.$data['data']['forecast'][0]['type']."\n";
                    
            }
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            SysLogTmp::log('返回结构', $resultStr, 0, __FILE__);
            return $resultStr;
        } else {
            return "Input something...";
        }
    }

    public function event_subscribe($object)
    {
        $contentStr = "";
        $contentStr = "感谢微信公众平台。" . "\n" . "目前平台功能如下：" . "\n" . "【1】 查天气，如输入：长春" . "\n" . " 更多内容，敬请期待...";
        $resultStr = $this->responseTextAction($object, $contentStr);
        return $resultStr;
    }
    
    public function event_VIEW($object)
    {
        $contentStr = "";
       
        $resultStr = $this->responseTextAction($object, $contentStr);
        return $resultStr;
    }
    
    
    
    
    public function location($object)
    {
        //回复内容
        $contentStr = "您的位置："."\n"."纬度: ".$object->Location_X."\n"."经度为：".$object->Location_Y;
        //格式化字符串
        
        //返回XML数据到微信客户端
        $resultStr = $this->responseTextAction($object, $contentStr);
        return $resultStr;
    }
    

    public function responseTextAction($object, $content, $flag = 0)
    {
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>%d</FuncFlag>
					</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }

    private function weatherAction($postObj)
    {
        // include("weather_cityId.php");
        // $c_name=$weather_cityId[$n];
        SysLogTmp::log('weatherAction', (string) $postObj->Content, 0, __LINE__);
        
        if (!empty($postObj->Content)) {
            $json = file_get_contents("compress.zlib://http://wthrcdn.etouch.cn/weather_mini?city=" . $postObj->Content);
            SysLogTmp::log('天气结果', $json, 0, __FILE__);
            
            $json_arr = json_decode($json, true);
            SysLogTmp::log('天气结果', print_r($json_arr, true), 0, __FILE__);
            return $json_arr;
        } else {
            SysLogTmp::log('天气结果-不应该出现这个结果', '', 0, __FILE__);
            return null;
        }
    }
    
    public function getSignPackage(){
        require '/Public/jssdk.php'; //引入jssdk文件
        $jssdk = new \JSSDK($this->weixinConfig['appid'],$this->weixinConfig['appsecret']);
        $signPackage = $jssdk->GetSignPackage();//获取
        return $signPackage;
    }
    
    //控制器方法，获取城市名称
    public function getCityLocation(){
        $latitude=I('post.latitude');//纬度
        $longitude=I('post.longitude');//经度
        $url="http://api.map.baidu.com/geocoder/v2/?ak=7GFnQy48lQeVefYZ3IDGfblcOrpo5Ttd&location=".$latitude.",".$longitude."&output=json&coordtype=gcj02ll";
        $output=file_get_contents($url);
        $address=json_decode($output,true);
        $city_name=$address['result']['addressComponent']['city'];//获取城市名称
        $city_code=$address['result']['cityCode'];//获取城市代码id
        if(!empty($city_name)){//获取到城市的时候返回true
            $this->ajaxReturn(array($city_name));
        }
    }

    public function http_curl($url, $type = 'get', $res = 'json', $arr = '')
    {
        // 1.初始化curl
        $ch = curl_init();
        // 2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        // 3.采集
        $output = curl_exec($ch);
        // 4.关闭
        // curl_close($ch);
        if ($res == 'json') {
            if (curl_errno($ch)) {
                // 请求失败
                return curl_error($ch);
            } else {
                // 请求成功
                return json_decode($output, true);
            }
        }
    }

    function getWxAccessTokenAction()
    {
        // 1.请求url地址
        $appid = 'wx9e1d8fc5ee0c85a1';
        $appsecret = '39ea8dc418b5ab3a03867a5937fe19fd';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
        // 2初始化
        $ch = curl_init();
        // 3.设置参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 4.调用接口
        $res = curl_exec($ch);
        // 5.关闭curl
        
        if (curl_errno($ch)) {
            var_dump(curl_error($ch));
        }
        $arr = json_decode($res, true);
        curl_close($ch);
        var_dump($arr);
        return $arr;
    }
    
    // 返回access_token
    /*
     * public function getWxAccessTokenAction(){
     * //将access_token存在session/cookie中
     * if($_SESSION['access_token'] && $_SESSION['expire_time']>time()){
     * //如果access_token在session并没有过期
     * return $_SESSION['access_token'];
     * }else{
     * //如果access_token不存在或已过期，重新取access_token
     * $appid = 'wx9e1d8fc5ee0c85a1';
     * $appsecret = '39ea8dc418b5ab3a03867a5937fe19fd';
     * $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
     * $this->http_curlAction($url,'get','josn');
     * $access_token = $res['access_token'];
     * //将重新获取到的access_token存到session
     * $_SESSION['access_token'] = $access_token;
     * $_SESSION['expire_time'] = time()+7000;
     * return $access_token;
     * }
     * }
     */
    public function definedItemAction()
    {
        // 创建微信菜单
        // 目前微信接口的调用方式都是通过curl post/get
        header('content-type:text/html;charset=utf-8');
        $access_token = $this->getWxAccessTokenAction();
        
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token['access_token'];
        echo $url;
        /*
         * $auth = new AuthExtend();
         * $accessToken = $auth->getAccessToken(false);
         */
        $postArr = array(
            'button' => array(
                array(
                    'name' => urlencode('用户绑定'),
                    'type' => 'view',
                    'key' => 'item1',
                    'url' => 'http://gs.jltengfang.com/user'
                ), // 第一个一级菜单
                array(
                    'name' => urlencode('菜单二'),
                    'sub_button' => array(
                        array(
                            'type' => 'view',
                            'name' => urlencode('获取地理位置'),
                            'url' => 'http://www.baidu.com'
                        ),
                        array(
                            'type' => 'view',
                            'name' => urlencode('扫码带提示'),
                            'url' => 'http://gs.jltengfang.com/index.php/way/user/bindindex'
                        )
                    )
                ), // 第二个一级菜单
                array(
                    'name' => urlencode('菜单3'),
                    'type' => 'view',
                    'key' => 'item3',
                    'url' => 'http://gs.jltengfang.com/index.php/way/user/bindindex'
                )
            )
        );
        // //第三个一级菜单
        
        
        echo '<hr/>';
        var_dump($postArr);
        echo $postJson = urldecode(json_encode($postArr));
        
        $res = $this->http_curl($url, 'post', 'json', $postJson);
        
        echo '<br/>';
        SysLogTmp::log('返回结果', $res, 0, __FILE__);
        var_dump($res);
    }
}//class end
