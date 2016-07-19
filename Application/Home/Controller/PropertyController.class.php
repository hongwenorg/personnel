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

	//计划申请页面列表信息
	function plan_table(){
		$model = D('property_apply');
		$class_post_array = $this->school_sel();
		$plan_array = $this->plan_sel();
		$purchase_array = $this->purchase_sel();
		$finance_type_array = $this->finance_type_sel();
		$check_array = $this->check_sel();
		$array = array();
		$user_basic = D("user_basic");
		//echo $_SESSION['userid'];die;
		$array = $model->where("check_id = 2 and is_del=0 and add_user='".$_SESSION['userid']."'")->select();
		if(!empty($array)){
			foreach($array as &$val){
				$user_basic_array = $user_basic->where("user_id='".$_SESSION['userid']."'")->find();
				$val['user_name'] = $user_basic_array['name'];
				foreach($class_post_array as $class_post_val){
					if($val['class_post_id'] == $class_post_val['id']){
						$val['class_post_plan'] =  $class_post_val['class'];
					}
					if($val['receive_school'] == $class_post_val['id']){
						$val['receive_school'] =  $class_post_val['class'];
					}
				}
				foreach($check_array as $check_val){
					if($val['check_id'] == $check_val['id']){
						$val['check_name'] =  $check_val['check_name'];
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

	//添加计划申请的提交申请
	function plan_add(){
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		//echo json_encode($arr);exit;
		if(!empty($arr)){
			$model = D('property_apply');
			foreach($arr as $val){
				$stult = $model->where("id='".$val."'")->save(array("check_id"=>4));
				if($stult){
					echo 1;exit;//提交成功
				}else{
					echo 2;exit;//提交失败
				}
			}
		
		}else{
			echo 3;exit;//提交失败
		}

	}

	//删除计划申请的操作
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
				$stult = $model->where("id='".$val."'")->delete();
				if($stult){
					echo 1;exit;//删除成功
				}else{
					echo 2;exit;//删除失败
				}
			}
		
		}else{
			echo 3;exit;//删除失败
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
		$arr['add_user'] = $user_id;
		$arr['project_type'] = "计划申请";
		$arr['check_id'] = 2;
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