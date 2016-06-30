<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:Index(登陆,退出)模板控制器
*/
namespace Home\Controller;
use Think\Controller;
header('Access-Control-Allow-Origin: *');
header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
require_once( "/Public/public.php" );
class IndexController extends Controller {
	//登陆后首页（如果未登陆则进入登陆页面）
    public function index(){
		$this->display("login");
    }
	//登陆
	public function login(){
		$this->display();
    }

	//退出
	public function goout(){
        if($_COOKIES['check']!=1){
            setcookie("username","1",time()-1);
            setcookie("post", "1",time()-1);
            setcookie("name", "1",time()-1);
        }
		if($_SESSION['userid']) {
            $_SESSION = array();
            $this->display("login");
        }else {
            $this->success('已经登出！',"login");
        }
    }

	//异常页面
	public function exceptions(){
		$this->display( 'Public:exception' );
	}
	

	//登陆处理
	function login_rtt(){
		$data = array();//定义json类型字符串的数组
		if(!empty($_GET['user']) && !empty($_GET['pwd'])){
			$user = $_GET['user'];
			$pwd = md5($_GET['pwd']);
		}else{
			$data['status'] = 0;
			$_SESSION = array();
			echo json_encode($data);
		}
		$users = D("users");//用户表
		$user_basic = D("user_basic");//用户基本信息表
		$modules = D("modules");//模块表
		$authority = D("authority");//权限表
		$allocation = D('allocation_user');
		session_start();
		$allocation_arr = array();
		if($user_arr = $users->where(array("user"=>$user,"pwd"=>$pwd))->find()){
			$data['status'] = 1;
			$data['userid'] = $user_arr['id'];
			$data['user'] = $user_arr['user'];
			$authority_arr = $authority->where(array("user_id"=>$user_arr['id']))->select();
			foreach($authority_arr as $key=>$val){
				$modules_arr = $modules->where(array("id"=>$val['module_id']))->find();
				if($modules_arr['level']==2){
					$superior = $modules->where(array("id"=>$modules_arr['superior']))->find();
					$data_arr[$key]['superior_id'] = $modules_arr['superior'];
					$data_arr[$key]['superior_name'] = $superior['module_name'];
				}
				$data_arr[$key]['module_id'] = $modules_arr['id'];
				$data_arr[$key]['module_name'] = $modules_arr['module_name'];
			}
			$user_basic_arr = $user_basic->where(array("user_id"=>$user_arr['id']))->find();
			$allocation_arr = $allocation->join("power on allocation_user.power_id = power.id")->where(array("user_id"=>$user_arr['id']))->find();
			$data["authority"] = $data_arr;
			$data["post"] = $user_basic_arr['post'];
			$data["name"] = $user_basic_arr['name'];
			$data["group_name"] = $allocation_arr['power_name'];
			$data["photo_max_url"] = $user_basic_arr['photo_max_url'];
			$data["photo_small_url"] = $user_basic_arr['photo_small_url'];
			$_SESSION['userid'] = $user_arr['id'];
			$_SESSION['status'] = 1;
			session('user' , $user_arr['user']);
			session('power_id' , $$authority_arr['power_id']);
			//return $data;exit;
			//handle_log($user_arr['id'],$user,'aaa','select');
			//echo $_SESSION['user'];die;
			echo json_encode($allocation_arr);exit;
		}
		//$data['last_sql'] = $model->getLastSql();
		$data['status'] = 0;
		$_SESSION = array();
		echo json_encode($data);
		
	}


}
?>