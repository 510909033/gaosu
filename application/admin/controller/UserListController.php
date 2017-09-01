<?php
namespace app\admin\controller;

use think\Controller;//引入控制器
use think\Request;//引入request
use think\Paginator;
use think\Db;
use app\admin\model\User;//引入模型层

use app\common\tool\Verifier;
use app\common\tool\AjaxTool;
use app\common\model\WayUserBindCar;

header("content-type:text/html;charset=UTF-8");//防乱码

class UserListController extends Controller
{
     //表单页面
    public function indexAction()
    {
        $user = new WayUserBindCar();//实例化model

        $res = $user->order('verify asc')->paginate(9);


        
        if(empty($_POST['contents']))
        {

           $_POST['contents'] = "username";

           $contents = $_POST['contents'];

           $search_name = input('search_name');

           $this->assign('res', $res);

            $select = [];
            $select['username'] = "";
            $select['phone'] = "";
            $select['car_number'] = "";
            $select['brand'] = "";

           $this->assign('select' , $select)  ;
           return $this->fetch('show');

        }
        
    }

    public function aaAction(){



        $user = User::get($where);
        
        echo $user?$user->status:''; // 例如输出“正常”
    }

    public function index1Action()
    {
        $search_name = input('search_name');

        $search = ['query'=>[]];

        $search['query']['search_name'] = $search_name;

        if( input('contents') )
        {
            $contents = input('contents');

            $res = Db::name('way_user_bind_car')->order('verify asc')->where("$contents",'like',"%{$search_name}%")->paginate(9,false,['query' => request($contents)->param(),]);
            
            $this -> assign('res',$res);

            $this -> assign('search_name',$search_name);

            $var = [];
            $select = [];
            $select['username'] = "";
            $select['phone'] = "";
            $select['car_number'] = "";
            $select['brand'] = "";

            $select[$contents] ='selected';

            $this->assign('select' , $select);

            return $this->fetch('show');

        }

    }

    //添加
    public function insertAction()
    {
        
        $Request = Request::instance();//调用Request中instance方法
        $data = $Request->post();//接值
     
        $user = new User;//实例化model
        $result = $user->insertData($data);//执行添加语句
       
        if($result)
        {   
            //return Redirect('UserList/index');
            //echo "<script>alert('新增成功');localtion.href='{:url('Index/show')}'</script>";
            $this->success('新增成功','UserList/show');
        }else{
            $this->error('新增失败');
        }

    }

   //删除
   public function deleteAction()
   {
       $Request=Request::instance();
       $id=$Request->get('id');
       $user=new User;
       $result=$user->deleteData($id);
       if ($result) 
          AjaxTool::outputDone($result);
       else
          AjaxTool::outputError('失败');
   }

   //修改页面
   public function update()
   {
        $Request=Request::instance();
        $id=$Request->get('id');
        $user=new User;
        $res=$user->findData($id);//查询单条
        //print_r($res);die;
        return view('update',['res'=>$res]);
   }

   //执行修改
   public function saveAction()
   {
        $Request = Request::instance();

        $id = $Request->post('id');      
    
        $Request = Request::instance();

        $data = $Request->post();//接修改之后的数据

        $user = new User;

        $result = $user->updateData($data,$id);//修该语句


        if($result)
        {
          echo "<script>
          alert('新增成功');
          window.history.back(-1);
          </script>";
        //$this->success('提交成功','UserList/index');

        }else{

           echo "<script>
          alert('新增失败');
          return false;
          </script>";
        }
   }

   //AJAX传值
   public function getDetailAction(){

      $id = $_GET['id'];
       $new = [];
      
      if($id == NULL){
        $new['errcode'] = 1;
        $new['html'] = '数据不存在';
        return \json($new);

      }else{

        $data = WayUserBindCar::get($id);

         // $data = Db::name('way_user_bind_car')->find(array('id'=>$id));
       
      if ($data){     

            $new = $data->toArray();
               $new['errcode'] = 0;

            $new['create_time'] = date('Y-m-d H:i:s',$data->getData('create_time'));  
            $new['dis_verify'] =  $data->dis_verify;

            $new['dis_status'] =  $data->dis_status;

            /*$verify = config('verify');

            $status = config('status');

            */
      }
            return \json($new);

          //var_dump($data);die;

      }

    }

//更新状态

    public function updatestateAction(){

        $verify = Verifier::validation($_POST, array(
          array('name:id' => '数据id','name:status'=>'状态'),
          array('id','noempty'),
          array('id,status','required'),
          array('id,status', 'int'),
        )); 
        if (true !== $verify)
          AjaxTool::outputError($verify['msg']);

        $data['status'] = ($_POST['status']==1) ? 0 : 1; 

        $res = model('User')->updateData($data,$_POST['id']);

        if ($res) 
          AjaxTool::outputDone($res);
        else
          AjaxTool::outputError('失败');

    }

//更新审核状态

    public function updateverifyAction(){
        $data = array();
        $msgType = array(1=>'充值问题',2=>'注册问题');

        if (!isset($_POST['verify'])&&empty($_POST['verify'])) 
            AjaxTool::outputError('数据异常，操作失败');


        if($_POST['verify']==1){
            $verify = Verifier::validation($_POST, array(
              array('name:id' => '数据id','name:verify'=>'审核状态'),
              array('id,verify','noempty'),
              array('id,verify','required'),
              array('id,verify', 'int'),
            )); 
        }else{
            $verify = Verifier::validation($_POST, array(
              array('name:id' => '数据id','name:verify'=>'审核状态','name:type'=>'问题类型','name:msg'=>'问题描述'),
              array('id,verify','noempty'),
              array('id,verify,type,msg','required'),
              array('id,verify,type', 'int'),
            ));             
        }
        if (true !== $verify)
          AjaxTool::outputError($verify['msg']);


        if ($_POST['verify']==1) {
            $data['verify']     = $_POST['verify'];
            
            //var_dump($data);die();
        }else{
            $data['verify'] = $_POST['verify'];
            $data['verify_reason']  = $_POST['type']==0 ? $_POST['msg'] : (!empty($_POST['msg']) ? $msgType[$_POST['type']].$_POST['msg'] : $msgType[$_POST['type']]);
            //var_dump($data);die();
        }


        $res = model('User')->updateData($data,$_POST['id']);

        if ($res) 
          AjaxTool::outputDone($res);
        else
          AjaxTool::outputError('失败');

    }
  
  

}

