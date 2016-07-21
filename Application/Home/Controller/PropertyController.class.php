<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/7/13
*@Description:固定资产系统
*/
namespace Home\Controller;
use Think\Controller;
header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
header("Content-type:text/html;charset=utf-8");
header('Access-Control-Allow-Origin: *');
require_once( "/Public/public.php" );
class PropertyController extends CommonController {
	function purchase(){
		$this->display();
	}

	//计划申请页面信息
	function plan_list(){
		$data_arr = array();
		$data_arr['school'] = $this->school_sel();
		$data_arr['plan'] = $this->plan_sel();
		$data_arr['purchase'] = $this->purchase_sel();
		$data_arr['finance_type'] = $this->finance_type_sel();
		echo json_encode($data_arr);
	}

	//页面列表信息
	function plan_table(){
		$where = '';
		if(!empty($_POST['month'])){
			$month = $_POST['month'];
			$where .= " and month = '".$month."'";
		}
		if(!empty($_POST['status']) && !empty($_POST['content'])){
			$status = $_POST['status'];
			$content = $_POST['content'];
			$where .= " and ".$status."='".$content."'";
		}
		if(empty($_POST['status_name'])){
			$array['status'] = 2;
			echo json_encode($array);exit;
		}else{
			$status_name = $_POST['status_name'];
		}
		//echo $_GET['status_name'];die;
		//$status_name = '资金申请';
		$model = D('property_apply');
		$class_post_array = $this->school_sel();
		$plan_array = $this->plan_sel();
		$purchase_array = $this->purchase_sel();
		$finance_type_array = $this->finance_type_sel();
		$check_array = $this->check_sel();
		$array = array();
		$user_basic = D("user_basic");
		//echo $_SESSION['userid'];die;
		foreach($check_array as $check_val){
			if($check_val['check_name'] == $status_name){
				$check_id =  $check_val['id'];
				$check_name = $check_val['check_name'];
			}
		}
		$array = $model->where("check_id = $check_id and is_del=0 and add_user='".$_SESSION['userid']."'".$where)->select();
		if(!empty($array)){
			foreach($array as &$val){
				$val['check_name'] = $check_name;
				foreach($class_post_array as $class_post_val){
					if($val['class_post_id'] == $class_post_val['id']){
						$val['class_post_plan'] =  $class_post_val['class'];
					}
					if($val['receive_school'] == $class_post_val['id']){
						$val['receive_school'] =  $class_post_val['class'];
					}
				}
				
				foreach($plan_array as $plan_val){
					if($val['plan_id'] == $plan_val['id']){
						$val['plan_name'] =  $plan_val['plan_name'];
					}
				}
				foreach($purchase_array as $purchase_val){
					if($val['purchase_id'] == $purchase_val['id']){
						$val['purchase_name'] =  $purchase_val['purchase_name'];
					}
				}
				foreach($finance_type_array as $finance_type_val){
					if($val['finance_type_id'] == $finance_type_val['id']){
						$val['finance_type_name'] =  $finance_type_val['name'];
					}
				}
			}
			$array['status'] = 1;
		}else{
			$array['status'] = 2;
		}
		//echo "<pre>";
		//print_r($array);
		echo json_encode($array);exit;
	}


	//页面分页显示列表信息
	function plan_table_page(){
		$where = '';
		if(!empty($_POST['month'])){
			$month = $_POST['month'];
			$where .= " and month = '".$month."'";
		}
		if(!empty($_POST['status']) && !empty($_POST['content'])){
			$status = $_POST['status'];
			$content = $_POST['content'];
			$where .= " and ".$status."='".$content."'";
		}
		if(empty($_POST['status_name'])){
			$array['status'] = 2;
			echo json_encode($array);exit;
		}else{
			$status_name = $_POST['status_name'];
		}
		$model = D('property_apply');
		//echo $_GET['status_name'];die;
		//$status_name = '资金申请';
		$class_post_array = $this->school_sel();
		$plan_array = $this->plan_sel();
		$purchase_array = $this->purchase_sel();
		$finance_type_array = $this->finance_type_sel();
		$check_array = $this->check_sel();
		$array = array();
		$user_basic = D("user_basic");
		//echo $_SESSION['userid'];die;
		foreach($check_array as $check_val){
			if($check_val['check_name'] == $status_name){
				$check_id =  $check_val['id'];
				$check_name = $check_val['check_name'];
			}
		}
		/*
		* 以下是分页程序
		*/
		//第几页
		if(empty($_POST['page'])){
			$page = 1;
		}else{
			$page = $_POST['page'];
		}
		$page_num = 10;//每页多少条
		$count = $model->where("check_id = $check_id and is_del=0 and add_user='".$_SESSION['userid']."'".$where)->count(); //获得记录总数
		$totalPage = ceil($count/$page_num); //计算出总页数
		$startCount = ($page-1)*$page_num; //分页开始,根据此方法计算出开始的记录
		/*
		* 分页程序结束
		*/
		
		$array = $model->where("check_id = $check_id and is_del=0 and add_user='".$_SESSION['userid']."'".$where)->limit($startCount,$page_num)->select();
		if(!empty($array)){
			foreach($array as &$val){
				$val['check_name'] = $check_name;
				foreach($class_post_array as $class_post_val){
					if($val['class_post_id'] == $class_post_val['id']){
						$val['class_post_plan'] =  $class_post_val['class'];
					}
					if($val['receive_school'] == $class_post_val['id']){
						$val['receive_school'] =  $class_post_val['class'];
					}
				}
				
				foreach($plan_array as $plan_val){
					if($val['plan_id'] == $plan_val['id']){
						$val['plan_name'] =  $plan_val['plan_name'];
					}
				}
				foreach($purchase_array as $purchase_val){
					if($val['purchase_id'] == $purchase_val['id']){
						$val['purchase_name'] =  $purchase_val['purchase_name'];
					}
				}
				foreach($finance_type_array as $finance_type_val){
					if($val['finance_type_id'] == $finance_type_val['id']){
						$val['finance_type_name'] =  $finance_type_val['name'];
					}
				}
			}
			$array['status'] = 1;
			$array['page_arr']['count'] = $count;
			$array['page_arr']['page_count'] = $totalPage;
			$array['page_arr']['page'] = $page;
		}else{
			$array['status'] = 2;
		}
		//echo "<pre>";
		//print_r($array);
		echo json_encode($array);exit;
	}


	//退回申请
	function plan_return(){
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		//echo json_encode($arr);exit;
		if(!empty($arr)){
			$check_array = $this->check_sel();
			$model = D('property_apply');
			foreach($arr as $val){
				$array = array();
				$user_basic = D("user_basic");
				//echo $_SESSION['userid'];die;
				$apply_arr = $model->where("id='".$val."'")->find();
				foreach($check_array as $check_val){
					if($check_val['id'] == $apply_arr['check_id']){
						if($check_val['check_name'] == '校长审核'){
							$check_name = '计划退回';
						}else if($check_val['check_name'] == '部门审核'){
							$check_name = '计划退回';
						}else if($check_val['check_name'] == '中心审核'){
							$check_name = '计划退回';
						}else if($check_val['check_name'] == '总裁审核'){
							$check_name = '计划退回';
						}else if($check_val['check_name'] == '资金申请'){
							$check_name = '申请退回';
						}else if($check_val['check_name'] == '资金审批'){
							$check_name = '申请退回';
						}else if($check_val['check_name'] == '校长确认'){
							$check_name = '退回报销';
						}else if($check_val['check_name'] == '部门确认'){
							$check_name = '退回报销';
						}else if($check_val['check_name'] == '中心确认'){
							$check_name = '退回报销';
						}else if($check_val['check_name'] == '总裁确认'){
							$check_name = '退回报销';
						}else if($check_val['check_name'] == '费用确认'){
							$check_name = '退回报销';
						}else if($check_val['check_name'] == '入账确认'){
							$check_name = '退回报销';
						}
					}
				}
				foreach($check_array as $check_val){
					if($check_val['check_name'] == $check_name){
						$check_id = $check_val['id'];
					}
				}

				$stult = $model->where("id='".$val."'")->save(array("check_id"=>$check_id,'project_type'=>$check_name));
			}
			echo 1;exit;
		
		}else{
			echo 3;exit;//提交失败
		}

	}

	//提交申请
	function plan_add(){
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		//echo json_encode($arr);exit;
		if(!empty($arr)){
			$check_array = $this->check_sel();
			$model = D('property_apply');
			foreach($arr as $val){
				$array = array();
				$user_basic = D("user_basic");
				//echo $_SESSION['userid'];die;
				$apply_arr = $model->where("id='".$val."'")->find();
				foreach($check_array as $check_val){
					if($check_val['id'] == $apply_arr['check_id']){
						if($check_val['check_name'] == '校长审核'){
							$check_name = '部门审核';
						}else if($check_val['check_name'] == '部门审核'){
							$check_name = '中心审核';
						}else if($check_val['check_name'] == '中心审核'){
							$check_name = '总裁审核';
						}else if($check_val['check_name'] == '总裁审核'){
							$check_name = '计划通过';
						}else if($check_val['check_name'] == '计划通过'){
							$check_name = '资金申请';
						}else if($check_val['check_name'] == '资金申请'){
							$check_name = '资金审批';
						}else if($check_val['check_name'] == '资金审批'){
							$check_name = '审批通过';
						}else if($check_val['check_name'] == '审批通过'){
							$check_name = '报销申请';
						}else if($check_val['check_name'] == '报销申请'){
							$check_name = '校长确认';
						}else if($check_val['check_name'] == '校长确认'){
							$check_name = '部门确认';
						}else if($check_val['check_name'] == '部门确认'){
							$check_name = '中心确认';
						}else if($check_val['check_name'] == '中心确认'){
							$check_name = '总裁确认';
						}else if($check_val['check_name'] == '总裁确认'){
							$check_name = '费用确认';
						}else if($check_val['check_name'] == '费用确认'){
							$check_name = '入账确认';
						}else if($check_val['check_name'] == '入账确认'){
							$check_name = '审核完成';
						}
					}
				}
				foreach($check_array as $check_val){
					if($check_val['check_name'] == $check_name){
						$check_id = $check_val['id'];
					}
				}

				$stult = $model->where("id='".$val."'")->save(array("check_id"=>$check_id,'project_type'=>$check_name));
			}
			echo 1;exit;
		
		}else{
			echo 3;exit;//提交失败
		}

	}


	//删除操作
	function plan_del(){
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		//echo json_encode($arr);exit;
		if(!empty($arr)){
			$model = D('property_apply');
			foreach($arr as $val){
				$array = array();
				$user_basic = D("user_basic");
				//echo $_SESSION['userid'];die;
				$stult = $model->where("id='".$val."'")->save(array("is_del"=>1));
			}
			echo 1;exit;
		
		}else{
			echo 3;exit;//提交失败
		}

	}


	function update_list(){
		if(empty($_POST['id'])){
			echo 2;exit;
		}
		$model = D('property_apply');
		$array = array();
		$array = $model->where(array('id'=>$_POST['id']))->find();
		echo json_encode($array);
	}


	//校区的数据
	function school_sel(){
		$model = D('campus_class_post');
		$array = array();
		$array = $model->where(array('status'=>1,'type'=>1))->select();
		return $array;
	}


	//计划类型的数据
	function plan_sel(){
		$model = D('property_plan');
		$array = array();
		$array = $model->select();
		return $array;
	}


	//科目类别的数据
	function purchase_sel(){
		$model = D('property_purchase');
		$array = array();
		$array = $model->select();
		return $array;
	}


	//审核状态的数据
	function check_sel(){
		$model = D('property_check');
		$array = array();
		$array = $model->select();
		return $array;
	}


	//采购类型
	function finance_type_sel(){
		$model = D('property_finance_type');
		$array = array();
		$array = $model->where(array('is_del'=>0))->select();
		return $array;
	}

	//计划申请的页面操作（提交、修改）
	function plan_pro(){
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		if(empty($arr['class_post_id'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['purchase_id'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['plan_id'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['receive_school'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['finance_type_id'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['info'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['unit'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['unit_price'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['count'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['receive_user'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		if(empty($arr['receive_card'])){
			echo 3;
			exit;//状态码3：请填写完整信息
		}
		$user_id = $_SESSION['userid'];
		$user_basic = D('user_basic');
		$user_basic_arr = $user_basic->where("user_id = $user_id")->find();
		$arr['add_user'] = $user_id;
		$arr['add_user_name'] = $user_basic_arr['name'];

		if(empty($_POST['status_name'])){
			$array['status'] = 2;
			echo json_encode($array);exit;
		}else{
			$status_name = $_POST['status_name'];
		}
		$check_array = $this->check_sel();
		foreach($check_array as $check_val){
			if($check_val['check_name'] == $status_name){
				$check_id =  $check_val['id'];
				$check_name = $check_val['check_name'];
			}
		}
		$arr['project_type'] = $status_name;
		$arr['check_id'] = $check_id;
		if(!empty($arr["id"])){
			//计划申请修改
			$property_apply = M("property_apply");
			$property_apply_arr = $property_apply->where(array("id"=>$arr["id"]))->save($arr);
			echo 2;exit;//状态码2：修改成功！
		}else{
			//计划申请添加
			$property_apply = M("property_apply");
			$property_apply_arr = $property_apply->add($arr);
			if(!$property_apply_arr){
				echo 4;exit;//状态码4：数据添加失败
			}
			echo 1;exit;//状态码1：添加成功！
		}
	}


}

?>