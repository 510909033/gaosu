<?php
namespace app\way\controller\data;

use think\Controller;
use app\way\controller\AdminController;
use extend\crypt\CryptExtend;
use crypt\driver\Rsa;
use app\common\model\SysUser;
use app\common\model\WayUserBindCar;
use app\way\controller\func\UserBindCarFuncController;

class CreateTestDataController extends AdminController
{
    
    /**
     * @var \Redis
     */
    private $redis;
    private $config_id_key = 'data_userid';
    
    protected function _initialize(){
        parent::_initialize();
        $this->redis = new \Redis();
        $this->redis->pconnect('127.0.0.1',6379);
    }
    
  

    public function initAction($step=0,$count=10){

        
        if (1 == $step){
            $maxid = SysUser::max('id') + 1;
            $this->redis->set($this->config_id_key,$maxid);
            
            return 'success';
        }else if (2 == $step){
            for ($i=0;$i<$count;$i++){
                $this->createSysUser();
            }
        }else{
            exit('step error');
        }
    }
    
    
    private function createSysUser()
    {
        

        $id = $this->redis->incr($this->config_id_key   );
        $solt = rand(10000,99999);
        $regtime = time() + ( (rand(1,1440)-720)*86400 );
        $data = array(
                'uni_account' => 'otmgKwBaD_I0cw.'.rand(0,9).$id,
                'password' => sha1($id.$solt),
                'solt' => $solt,
                'regtime' => $regtime,
                'type' => '1',
                'create_time' => $regtime + rand(1,1000),
                'update_time' => '0',
                'phone' => '0',
                'mobile' => '',
                'email' => '',
                'sex' => '0',
                'subscribe' => '0',
                'nickname' => '',
                'city' => '',
                'country' => '',
                'province' => '',
                'language' => '',
                'headimgurl' => '',
                'subscribe_time' => '0',
                'unionid' => '',
                'remark' => '',
                'groupid' => '',
                'tabid_list' => '',
                'user_type' => '1',
                'qrcode_path' => '',
                'scene' => '0'
        );
        
        $user = SysUser::regApi($data);
     
        if (0 === $user['err']){
            $this->createWayUserBindCar($user['user_model']);
        }else{
           
        }
    }
    

    public function createWayUserBindCar(Sysuser $sysUser)
    {
        $time = strtotime($sysUser->create_time);
        $way_user_bind_car = array(
            'user_id' => $sysUser->id,
            'openid' => $sysUser->uni_account,
            'status' => rand(0,1),
            'verify' => rand(0,3),
            'qrcode_version' => rand(0,10000),
            'create_time' => $time,
            'update_time' => '0',
            'car_number' => '吉'.strtoupper(str_pad( base_convert ($sysUser->id ,10,36) , 6,'A')),
            'car_color' => '0',
            'username' => $this->getUsername(rand(2,8)),
            'identity_card' => str_pad($sysUser->id, 18,'0'),
            'phone' => str_pad($sysUser->id, 11,'0'),
            'car_type_id' => rand(1,1000),
            'engine' => \util\Str::randString(11,0).$sysUser->id,
            'brand' => $this->getBrand(rand(2,7)),
            'reg_time' => $time - rand(0,8640000),
            'chassis_number' => \util\Str::randString(12,0).$sysUser->id,
            'car_qrcode_path' => ''
        );
        $wayUserBindCar =WayUserBindCar::create($way_user_bind_car);
        $func = new UserBindCarFuncController();
        $wayUserBindCar->car_qrcode_path = $func->createQrcode($wayUserBindCar);
        $wayUserBindCar->save();
    }
    
    private function getUsername($count){
        $num = mb_strlen($this->str,'utf-8');
        $str = '';
        for ($i=0;$i<$count;$i++){
            $str .= mb_substr($this->str , rand(0,$num-1) , 1);
        }
        
        return $str;
    }
    
    private function getBrand($count){
        $num = 10;
        $str = '';
        for ($i=0;$i<$count;$i++){
            $str .= mb_substr($this->str , rand(0,$num-1) , 1);
        }
        
        return $str;
    }
    
    
    
    
    
    
    
    
    
    
    private $str = '过去常讲汉字始于商代的甲骨文但其实甲骨文已经是成熟文字在此之前汉字应该存在一段从产生到成熟的发展过程因此有人主张推至夏末；也有人主张推至夏以前各执己见郭沫若在《古代文字之辩证的发展》指出：“汉字究竟源始于何时呢？我认为这可以从西安半坡村遗址距今的年代为指标”“半坡遗址年代距今有六千年左右”“半坡遗址是新石器时代仰韶文化的典型”“半坡彩陶上每每有一些类似文字的简单刻划和器上的花纹判然不同”“虽然刻划的意义至今尚未阐明但无疑是具有文字性质的符号”“可以肯定地说就是中国文字的起源或者中国原始文字的孑（jié）遗”如按此说中国文明则应算成近六千年中国文字之源始究竟在何时？最古老的文字产生于什么时代？分别代表什么含义？至今仍未达成一致意见有待于大量的材料来说明
数量
汉字的数量并没有准确数字大约将近十万个（北京国安咨讯设备公司汉字字库收入有出处汉字91251个）日常所使用的汉字只有几千字据统计1000个常用字能覆盖约92%的书面资料2000字可覆盖98%以上3000字则已到99%简体与繁体的统计结果相差不大
关于汉字的数量根据古代的字书和词书的记载可以看出其发展情况
秦代的《仓颉》、《博学》、《爰历》三篇共有3300字；汉代扬雄作《训纂篇》有5340字到许慎作《说文解字》[2]  就有9353字了；据唐代封演《闻见记·文字篇》所记晋吕忱作《字林》有12824字后魏杨承庆作《字统》有13734字南朝时顾野王所撰的《玉篇》据记载共收16917字在此基础上修订的《大广益会玉篇》则据说有22726字；唐代孙强增字本《玉篇》有22561字宋代司马光修《类篇》多至31319字宋朝官修的《集韵》中收字53525个曾经是收字最多的一部书；清代《康熙字典》有47000多字了；1915年欧阳博存等编著的《中华大字典》有48000多字；1959年日本诸桥辙次主编的《大汉和辞典》有49964字；1971年张其昀主编的《中文大辞典》有49888字；1990年徐仲舒主编的《汉语大字典》有54678字；1994年冷玉龙等编著的《中华字海》有85000字台湾地区教育主管机关编撰的《异体字字典》第五版内容含正字与异体字共106230字是收录最多汉字的字典
历史上出现过的汉字总数有8万多（也有6万多的说法）其中多数为异体字和罕用字绝大多数异体字和罕用字已被规范掉除古文之外一般只在人名、地名中偶尔出现此外继第一批简化字后还有一批“二简字”已被废除但仍有少数字在社会上流行
如果学习和使用汉字真的需要掌握七八万个汉字的音形义的话那汉字将是世界上没人能够也没人愿意学习和使用的文字了但是《中华字海》一类字书里收录的汉字绝大部分是“死字”也就是历史上存在过而今天的书面语里已经废置不用的字据统计十三经（《易经》、《尚书》、《公羊传》、《论语》、《孟子》等13部典籍）全部字数为589283个字其中不相同的单字字数为6544个字因此实际上人们在日常使用的汉字不过六千多而已
在汉字计算机编码标准中最大的汉字编码是台湾地区的CNS116435.0版全字库可供查询的字共87,047个汉字、10771个拼音文字及894个符号台港民间通用的大五码收录繁体汉字13053个GB18030是中华人民共和国现时最新的内码字集GBK收录汉字简体、繁体及20912个而早期的GB2312收录简体汉字6763个Unicode的中日朝（韩）统一表意文字基本字集则收录汉字20902个总数亦高达七万多字
初期的汉字系统字数不足很多事物以通假字表示使文字的表述存在较大歧义为完善表述的明确性汉字经历了逐步复杂、字数大量增加的阶段汉字数量的过度增加又引发了汉字学习的困难单一汉字能表示的意义有限于是有许多单一的汉语意义是用汉语词语表示例如常见的双字词汉语书写的发展多朝向造新词而非造新字殷墟的甲骨文更早、与汉字起源有关的出土资料这些资料主要是指原始社会晚期及有史社会早期出现在陶器上面的刻画或彩绘符号另外还包括少量的刻写在甲骨、玉器、石器等上面的符号可以说它们共同为解释汉字的起源提供了新的依据';
}
