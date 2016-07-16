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
		echo 1;die;
		$data_arr = array();
		$data_arr[] = $this->school_sel();
		$data_arr[] = $this->plan_sel();
		$data_arr[] = $this->purchase();
		$data_arr[] = $this->finance_type();

		/*$oa_apply = D("oa_apply");
		$arr = array();
		$arr = $oa_apply->where("is_del!=1")->select();
		//echo "<pre>";
		//print_r($class_arr);*/
		echo json_encode($data_arr);
	}

	//校区的下拉列表数据
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
	function purchase(){
		$model = D('property_purchase');
		$array = array();
		$array = $model->select();
		return $array;
	}


	//采购类型
	function finance_type(){
		$model = D('property_finance_type');
		$array = array();
		$array = $model->where(array('is_del'=>0))->select();
		return $array;
	}


}

?>