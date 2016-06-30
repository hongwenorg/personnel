<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:登陆后模板控制器
*/
namespace Home\Controller;
use Think\Controller;
header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
header("Content-type:text/html;charset=utf-8");
header('Access-Control-Allow-Origin: *');
require_once( "/Public/public.php" );
class ContentController extends CommonController {
	public function index_in(){
		$this->display();
	}
	//员工信息页面
	function staff(){
		$this->display();
	}

	//员工调动页面
	function staff_move(){
		$this->display();
	}

	//引用头文件
	function head(){
		$this->display();
	}

	//权限首页显示
	function authority_index(){
		if(empty($_SESSION['power_id'])){
			$modules_arr['status'] = 2;
			echo json_encode($modules_arr);
		}
		$power_id = $_SESSION['power_id'];
		$power = D('power');
		$modules = D('modules');
		$power_arr = $power->where(array("id" => $power_id))->find();
		$modules_arr = $modules->where("id in (".$power_arr['modules_first_id'].")")->select();
		$modules_arr['status'] = 1;
		echo json_encode($modules_arr);
	}

	//单位部门职务等信息
	function post(){
		$post = D("campus_class_post");
		$arr = array();
		$class_arr = array();
		$arr1 = $post->where("type!=3")->select();
		$arr2 = $post->where("type=3")->select();
		$arr3 = $post->where("type!=1")->select();
		foreach($arr1 as $key => $val){
			if($val['type']==1){
				$class_arr['campus'][]=$val['class'];
			}elseif($val['type']==2){
				$group_arr = array();
				$group_arr['class'] = $val['class'];
				$group_arr['is_group'] = 0;
				foreach($arr2 as $key2 => $val2){
					if($val2['up_id'] == $val['id'] && $val['is_group']==1){
						$group_arr['is_group'] = 1;
						$group_arr['group_val'][] = $val2['class'];
					}
				}
				$class_arr['class'][] = $group_arr;
			}
		}
		foreach($arr3 as $val){
			if($val['is_group']!=1 && $val['up_id']==2){
				$class_arr['school_post'][]=$val['class'];
			}else{
				$class_arr['campus_post'][]=$val['class'];
			}
		}

			
		//echo "<pre>";
		//print_r($class_arr);
		echo json_encode($class_arr);
	}

	//员工档案管理信息
	function page_con(){
		$basic_arr=array();
		$user_basic=D("users");
		$where = "users.id!=1 and users.status!=0 and user_basic.status!=0";
		if(!empty($_GET['status'])){
			$status = $_GET['status'];
			
		}else{
			$status = "";
			$where .= "";
		}
		if(!empty($_GET['content'])){
			$content = $_GET['content'];
		}else{
			$content = "";
			$where .= "";
		}
		if($status=="campus"){
				$where .= " and user_basic.campus like '%$content%'";
		}elseif($status=="teacher"){
			$where .= " and user_basic.post like '%$content%'";
		}elseif($status=="post"){
			$where .= " and user_basic.post like '%$content%'";
		}elseif($status == "time") {
			$content_arr = explode(",",$content);
			$where .= " and (user_basic.entry_date between '" . $content_arr[0] . "' and '" . $content_arr[1] . "')";
		}elseif($status == "name") {
			$where .= " and user_basic.name = '$content'";
		}elseif($status == "user") {
			$where .= " and users.user = '$content'";
		}

		//$basic_arr['where'] = $content_arr[0];
		$basic_arr=$user_basic->join('user_basic ON users.id = user_basic.user_id')->where($where)->select();
		//$basic_arr['getLastSql'] = $user_basic->getLastSql();
		if($basic_arr){
			$_SESSION['select_content'] = $basic_arr;
			$basic_arr['status'] = 1;
			echo json_encode($basic_arr);
		}else{
			$basic_arr['status'] = 2;
			echo json_encode($basic_arr);
		}
		
	}


	//员工调动页面搜索
	function change_find(){
		$post_arr=array();
		$users=D("users");
		$where = "users.id!=1 and users.status!=0 and user_basic.status!=0";
		if(!empty($_GET['status'])){
			$status = $_GET['status'];
			
		}else{
			$status = "";
			$where .= "";
		}
		if(!empty($_GET['content'])){
			$content = $_GET['content'];
		}else{
			$content = "";
			$where .= "";
		}
		if($status=="export_campus"){
				$where .= " and user_post_change.export_campus like '%$content%'";
		}elseif($status=="export_post"){
			$where .= " and user_post_change.export_post like '%$content%'";
		}elseif($status=="fold_campus"){
			$where .= " and user_post_change.fold_campus like '%$content%'";
		}elseif($status=="fold_post"){
			$where .= " and user_post_change.fold_post like '%$content%'";
		}elseif($status == "time") {
			$content_arr = explode(",",$content);
			$where .= " and (user_post_change.time between '" . $content_arr[0] . "' and '" . $content_arr[1] . "')";
		}elseif($status == "name") {
			$where .= " and user_basic.name = '$content'";
		}elseif($status == "user") {
			$where .= " and users.user = '$content'";
		}

		$post_arr=$users->join('user_basic ON users.id = user_basic.user_id')->join('user_post_change ON users.id = user_post_change.user_id')->where($where)->select();
		if(!empty($post_arr)){
			$post_arr['status'] = 1;
			echo json_encode($post_arr);
		}else{
			$post_arr['status'] = 2;
			echo json_encode($post_arr);
		}
	}


	//奖惩记录页面搜索
	function record_find(){
		$post_arr=array();
		$users=D("users");
		$where = "users.id!=1 and users.status!=0";
		if(!empty($_GET['status'])){
			$status = $_GET['status'];
			
		}else{
			$status = "";
			$where .= "";
		}
		if(!empty($_GET['content'])){
			$content = $_GET['content'];
		}else{
			$content = "";
			$where .= "";
		}
		if($status=="shape"){
				$where .= " and user_record.shape like '%$content%'";
		}elseif($status == "time") {
			$content_arr = explode(",",$content);
			$where .= " and (user_record.time between '" . $content_arr[0] . "' and '" . $content_arr[1] . "')";
		}elseif($status == "name") {
			$where .= " and user_basic.name = '$content'";
		}elseif($status == "user") {
			$where .= " and users.user = '$content'";
		}

		$post_arr=$users->join('user_basic ON users.id = user_basic.user_id')->join('user_record ON users.id = user_record.user_id')->where($where)->select();
		if(!empty($post_arr)){
			$post_arr['status'] = 1;
			echo json_encode($post_arr);
		}else{
			$post_arr['status'] = 2;
			echo json_encode($post_arr);
		}
	}





	//离职页面搜索
	function leave_find(){
		$post_arr=array();
		$users=D("users");
		$where = "(users.id!=1 and users.status=0 or user_basic.status=0)";
		if(!empty($_GET['status'])){
			$status = $_GET['status'];
			
		}else{
			$status = "";
			$where .= "";
		}
		if(!empty($_GET['content'])){
			$content = $_GET['content'];
		}else{
			$content = "";
			$where .= "";
		}
		//echo $status."<br>".$content;die;
		if($status=="campus"){
				$where .= " and user_basic.campus like '%$content%'";
		}elseif($status=="post"){
			$where .= " and user_basic.post like '%$content%'";
		}elseif($status=="place"){
			$where .= " and user_important.tail_after_value like '%$content%'";
		}elseif($status == "time") {
			$content_arr = explode(",",$content);
			$where .= " and (user_important.leave_date between '" . $content_arr[0] . "' and '" . $content_arr[1] . "')";
		}elseif($status == "name") {
			$where .= " and user_basic.name like '$content'";
		}

		$post_arr=$users->join('user_basic ON users.id = user_basic.user_id')->join('user_important ON users.id = user_important.user_id')->where($where)->select();
		//echo $users->getLastSql();die;
		if(!empty($post_arr)){
			$post_arr['status'] = 1;
			echo json_encode($post_arr);
		}else{
			$post_arr['status'] = 2;
			echo json_encode($post_arr);
		}
	}



	//五险一金页面搜索
	function insure_find(){
		$post_arr=array();
		$users=D("users");
		$where = "users.id!=1 and users.status!=0 and user_basic.status!=0";
		if(!empty($_GET['status'])){
			$status = $_GET['status'];
			
		}else{
			$status = "";
			$where .= "";
		}
		if(!empty($_GET['content'])){
			$content = $_GET['content'];
		}else{
			$content = "";
			$where .= "";
		}
		if($status=="place"){
				$where .= " and user_insure.place like '%$content%'";
		}elseif($status == "time") {
			$content_arr = explode(",",$content);
			$where .= " and (user_insure.begin_time between '" . $content_arr[0] . "' and '" . $content_arr[1] . "')";
		}elseif($status == "name") {
			$where .= " and user_basic.name = '$content'";
		}elseif($status == "user") {
			$where .= " and users.user = '$content'";
		}

		$post_arr=$users->join('user_basic ON users.id = user_basic.user_id')->join('user_insure ON users.id = user_insure.user_id')->where($where)->select();
		if(!empty($post_arr)){
			$post_arr['status'] = 1;
			echo json_encode($post_arr);
		}else{
			$post_arr['status'] = 2;
			echo json_encode($post_arr);
		}
	}

}
?>