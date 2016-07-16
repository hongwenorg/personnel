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

		/*$oa_apply = D("oa_apply");
		$arr = array();
		$arr = $oa_apply->where("is_del!=1")->select();
		echo "<pre>";
		print_r($data_arr);*/
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
	function purchase_sel(){
		$model = D('property_purchase');
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


}

?>