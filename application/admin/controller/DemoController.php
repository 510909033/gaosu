//用户的展示页面
    public function index(){
    //数据库连接
        #dump(config('database'));
        
        #dsn配置
            //$res = Db::connect("mysql://root:root@127.0.0.1:3306/gs#utf8");
            
    //数据库的查询
        # 使用sql语句操作数据库
            //1.$res = Db::query("select * from sys_user");
            //2.$res = Db::execute("insert into sys_user set phone=?,email=?",['123456789','123456@qq.com']);
        
        # 1.select返回所有记录，返回的结果是一个二维数组，如果结果不存在，返回一个空数组
            //$res = Db::table('sys_user')->select();
            //$res = db('sys_user',[],false)->select();每一次都要实例化类
            //$res = name('sys_user')->select();

        # 2.find 需要添加where条件，返回一条结果，结果为二维数组,结果不存在返回NULL
            //$res = Db::table('sys_user')->find();
            //$res = db('sys_user')->find();
        
        # 3.value 返回一条记录，并且是这条记录的某个字段值，如果结果不存在，返回NULL
            //$res = Db::table('sys_user')->value('phone');
        
        # 4.column 返回一个一维数组，数组中的value值就是我们要获取的列的值，如果存在第二个参数，就返回这个数组，并且用第二个参数的值座位数组的key值，如果结果不存在，返回空数组
            //$res = Db::table('sys_user')->column('phone','email');
        



       /* $db = Db::name('sys_user');

        $res = $db->insert([
            'phone' => '789456',
            'email' => '789456@qq.com'
            ]);
        dump($res); */   

        
    }
