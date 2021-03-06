<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:员工模板、生成Excel~打印控制器
*/
namespace Home\Controller;
use Think\Controller;
header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
header("Content-type:text/html;charset=utf-8");
header('Access-Control-Allow-Origin: *');
require_once( "/Public/public.php" );
class StaffController extends CommonController {
	//员工信息excel生成函数
	public function php_excel(){
		if(empty($_SESSION['select_content'])){
			$this->error("生成页面错误，请联系管理员！");exit;
		}
		$content_arr = array();
		$content_arr = $_SESSION['select_content'];
		import("Vendor.PHPExcel");
		//创建对象
		$excel = new \PHPExcel();
		//Excel表格式,这里简略写了8列
		$letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V');
		//表头数组
		$tableheader = array('序号','员工号','姓名','性别','民族','身份证号','婚姻状况','出生日期','政治面貌','籍贯','应急联系人','应急联系电话','毕业院校','专业','资格证书','单位部门','职务','入职时间', '联系电话','QQ','微信','E-mail');
		//填充表头信息
		for($i = 0;$i < count($tableheader);$i++) {
			$excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
		}
		//表格数组
		foreach($content_arr as $key => $val){
			$data[$key][0] = ($key+1);
			$data[$key][1] = $val["user"];
			$data[$key][2] = $val["name"];
			$data[$key][3] = $val["sex"];
			$data[$key][4] = $val["nation"];
			$data[$key][5] = $val["card"];
			$data[$key][6] = $val["marriage"];
			$data[$key][7] = $val["birthday"];
			$data[$key][8] = $val["polity"];
			$data[$key][9] = $val["residence_booklet"];
			$data[$key][10] = $val["contacts"];
			$data[$key][11] = $val["urgency_telephone"];
			$data[$key][12] = $val["school"];
			$data[$key][13] = $val["mjor"];
			$data[$key][14] = $val["seniority"];
			$data[$key][15] = $val["campus"];
			$data[$key][16] = $val["post"];
			$data[$key][17] = $val["entry_date"];
			$data[$key][18] = $val["telephone"];
			$data[$key][19] = $val["qq"];
			$data[$key][20] = $val["wechat"];
			$data[$key][21] = $val["email"];
		}
		//填充表格信息
		for ($i = 2;$i <= count($data) + 1;$i++) {
			$j = 0;
			foreach ($data[$i - 2] as $key=>$value) {
				$excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
				$j++;
			}
		}
		//创建Excel输入对象
		$write = new \PHPExcel_Writer_Excel5($excel);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename="鸿文员工档案表.xls"');
		header("Content-Transfer-Encoding:binary");
		$write->save('php://output');
	}


	//考勤记录excel生成函数
	public function check_excel(){
		if(empty($_SESSION['select_content'])){
			$this->error("生成页面错误，请联系管理员！");exit;
		}
		$content_arr = array();
		$content_arr = $_SESSION['select_content'];
		import("Vendor.PHPExcel");
		//创建对象
		$excel = new \PHPExcel();
		//Excel表格式,这里简略写了8列
		$letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T');
		//表头数组
		$tableheader = array('序号','姓名','单位部门','职务','讲师级别','元老补助','绩效系数','迟到','早退','事假','事假核算','病假','病假核算','加班','旷工','未打卡','应出勤天数','核算出勤天数','是否满勤','详细信息');
		//填充表头信息
		for($i = 0;$i < count($tableheader);$i++) {
			$excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
		}
		$check_record = D('check_record');
		//表格数组
		foreach($content_arr as $key => $val){
			$data[$key][0] = ($key+1);
			$data[$key][1] = $val["name"];
			$data[$key][2] = $val["campus"];
			$data[$key][3] = $val["post"];
			$data[$key][4] = $val["level"];
			$data[$key][5] = $val["allowance"];
			$data[$key][6] = 1;
			$data[$key][7] = $val["late"];
			$data[$key][8] = $val["early"];
			$data[$key][9] = $val["personal_leave"];
			$data[$key][10] = $val["personal_leave_count"];
			$data[$key][11] = $val["sick_leave"];
			$data[$key][12] = $val["sick_leave_count"];
			$data[$key][13] = $val["overtime"];
			$data[$key][14] = $val["absenteeism"];
			$data[$key][15] = $val["noclock"];
			$data[$key][16] = $val["over_count_day"];
			$data[$key][17] = $val["count_yes"];
			$data[$key][18] = $val["is_no"];

			$detailed_arr = $check_record->query("SELECT check_date,check_content FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '不合格' ) ".$val["date"]);
			$detascy_arr = $check_record->where("user_id='".$val['user_id']."' and ( check_content like '%事假%' or check_content like '%病假%' )".$val["date"])->select();
			$str = "";
			foreach($detailed_arr as $detailed_val){
				if(strstr($detailed_val['check_content'],"旷工") && strstr($detailed_val['check_content'],"晚一个小时")){
					$str .= date("d",strtotime($detailed_val['check_date']))."迟到为旷工；";
				}else if(strstr($detailed_val['check_content'],"旷工") && strstr($detailed_val['check_content'],"早一个小时")){
					$str .= date("d",strtotime($detailed_val['check_date']))."早退为旷工；";
				}else if(strstr($detailed_val['check_content'],"旷工") && strstr($detailed_val['check_content'],"无打卡记录")){
					$str .= date("d",strtotime($detailed_val['check_date']))."旷工；";
				}else if(strstr($detailed_val['check_content'],"灵活打卡异常")){
					$str .= date("d",strtotime($detailed_val['check_date']))."灵活打卡异常；";
				}else{
					$str .= date("d",strtotime($detailed_val['check_date'])).$detailed_val['check_content']."；";
				}
				
			}

			foreach($detascy_arr as $detascy_val){
				if(strstr($detascy_val['check_content'],"事假")){
					$str .= date("d",strtotime($detascy_val['check_date']))."事假；";
				}else if(strstr($detascy_val['check_content'],"病假")){
					$str .= date("d",strtotime($detascy_val['check_date']))."病假；";
				}
			}
			$detailed = rtrim($str,'；');
			
			$val['detailed'] = $detailed;
			$data[$key][19] = $val["detailed"];
		}
		//填充表格信息
		for ($i = 2;$i <= count($data) + 1;$i++) {
			$j = 0;
			foreach ($data[$i - 2] as $key=>$value) {
				$excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
				$j++;
			}
		}
		//创建Excel输入对象
		$write = new \PHPExcel_Writer_Excel5($excel);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename="'.$_SESSION['time_date'].'鸿文员工考勤表.xls"');
		header("Content-Transfer-Encoding:binary");
		$write->save('php://output');
	}


	//导出请假信息
	function leaveing_find(){
		$leaveing = D('leaveing');
		$leave_arr = array();
		//下面是获取上一月的数据
		$data = $this->year_month_day();
		$check_content = $data['check_content'];//查询条件，格式：“2016-05-”

		$leave_where = "state='审核通过' and time_date like '%".$check_content."%' or (time_begin like '%".$check_content."%' and class='加班')";
		$leave_arr = $leaveing->where($leave_where)->order("class,property")->select();
		if(empty($leave_arr)){
			$this->error("生成页面错误，请联系管理员！");exit;
		}
		import("Vendor.PHPExcel");
		//创建对象
		$excel = new \PHPExcel();
		//Excel表格式,这里简略写了8列
		$letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
		//表头数组
		$tableheader = array('序号','考勤号','姓名','校区','部门','职务','申请类型','审核状态','请假类型','申请时间','开始时间','结束时间','申请天数','情况说明');
		//填充表头信息
		for($i = 0;$i < count($tableheader);$i++) {
			$excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
		}
		//表格数组
		foreach($leave_arr as $key => $val){
			$data[$key][0] = ($key+1);
			$data[$key][1] = $val["atten_uid"];
			$data[$key][2] = $val["name"];
			$data[$key][3] = $val["campus"];
			$data[$key][4] = $val["part"];
			$data[$key][5] = $val["post"];
			$data[$key][6] = $val["class"];
			$data[$key][7] = $val["state"];
			$data[$key][8] = $val["property"];
			$data[$key][9] = $val["time_date"];
			$data[$key][10] = $val["time_begin"];
			$data[$key][11] = $val["time_stop"];
			$data[$key][12] = $val["count_day1"];
			$data[$key][13] = $val["info"];
		}
		//填充表格信息
		for ($i = 2;$i <= count($data) + 1;$i++) {
			$j = 0;
			foreach ($data[$i - 2] as $key=>$value) {
				$excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
				$j++;
			}
		}
		//创建Excel输入对象
		$write = new \PHPExcel_Writer_Excel5($excel);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename="'.$check_content.'鸿文请假记录.xls"');
		header("Content-Transfer-Encoding:binary");
		$write->save('php://output');
		
	}



	//每到月一号计算上个月年，月，日，总共多少天
	function year_month_day(){
		//下面是获取上一月的数据
		//获取月份当前
		$data = array();
		$now_time = Intval(date("m",time()));
		//如果当前月份不等于一月并且小于等于十月则当前月份-1前加0
		if($now_time !=1 && $now_time<=10){
			$year_day = date("Y",time());
			$month_day = "0".($now_time-1);
			$check_content = $year_day."-".$month_day."-";
		//如果当前月份不等于一月并且大于十月则当前月份-1不加0
		}elseif($now_time !=1 && $now_time>10){
			$year_day = date("Y",time());
			$month_day = $now_time-1;
			$check_content = $year_day."-".$month_day."-";
		//如果当前月份等于一月则当前年份-1当前月份定位12月
		}elseif($now_time ==1){
			$year_day = Intval(date("Y",time()))-1;
			$month_day = 12;
			$check_content = $year_day."-12-";
		}
		$data['now_time'] = $now_time;
		$data['year_day'] = $year_day;
		$data['month_day'] = $month_day;
		$data['check_content'] = $check_content;
		//计算每月一共有多少天
		$data['day_count']=date('j',mktime(0,0,1,($month_day==12?1:$month_day+1),1,($month_day==12?$year_day+1:$year_day))-24*3600);
		//var_dump($data);
		return $data;
	}


    //地区动态获取
	function area_school(){
        $area = M("area_school");
        $area_arr = $area->where("level = 1")->select();
        echo json_encode($area_arr);
	}


	//员工信息获取
	function staff_user(){
		$data = array();
		if(!empty($_POST['user'])){
			$users = M("users");
			$user_basic = M("user_basic");
			$user_insure = M("user_insure");
			$user_work_undergo = M("user_work_undergo");
			$user_arr = $users->where(array("user"=>$_POST['user']))->find();
			$basic_arr = $user_basic->where(array("user_id" => $user_arr['id']))->find();
			$insure_arr = $user_insure->where(array("user_id" => $user_arr['id']))->select();
			$work_undergo_arr = $user_work_undergo->where(array("user_id" => $user_arr['id']))->select();
			$data["basic"] = $basic_arr;
			$data["insure"] = $insure_arr;
			$data["work_undergo"] = $work_undergo_arr;
			$data["status"] = 1;
			echo json_encode($data);
		}else{
			$data["status"] = 2;
			echo json_encode($data);
		}
	}


	//职位调动的显示
	function staff_post_leave(){
		$user_card = $_POST['user'];
		$users = M("users");
		$user_arr = $users->where(array("user" =>$user_card))->find();
		$user_basic = M('user_basic');
		$data_arr = $user_basic->where(array('user_id' => $user_arr['id']))->find();
		if($data_arr){
			$data_arr['status'] = "1";
			echo json_encode($data_arr);exit;
		}else{
			$data_arr['status'] = "2";
			echo json_encode($data_arr);exit;
		}
		
	}



	//头像剪裁页面
	function imgs_prune(){
		$this->display();
	}


	//奖惩记录操作
	function staff_record(){
		$data = array();
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $data[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		$user_record = M("user_record");
		$users = M("users");
		$user_arr = $users->where(array("user" =>$_POST['user_card']))->find();
		$data["user_id"] = $user_arr['id'];
		$result = $user_record->add($data);
		if($result){
			echo 1;exit;//状态码1：保存成功
		}else{
			echo 2;exit;//状态码2：保存失败
		}
	}



	//岗位调动操作
	function export_fold_pro(){
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		$data = array();
		$basic_data = array();
		$data = $arr;
		$basic_data['campus'] = $arr['fold_campus']; 
		$basic_data['post'] = $arr['fold_post']; 
		$user_post_change = M("user_post_change");
		$users = M("users");
		$user_basic = M("user_basic");
		$user_arr = $users->where(array("user" =>$_POST['user_card']))->find();
		$data["user_id"] = $user_arr['id'];
		$result = $user_post_change->add($data);
		if(!$result){
			echo 2;exit;//状态码2：保存失败
		}
		$res = $user_basic->where(array('user_id' => $user_arr['id']))->save($basic_data);
		echo 1;exit;//状态码1：保存成功
	}



 

	//离职页面复职操作
	function leave_fu_pro(){
		if(!empty($_POST['user_card'])){
			$user = $_POST['user_card'];
			$users = M('users');
			$user_basic = M('user_basic');
			$user_important = M('user_important');
			$user_arr = $users->where(array("user" => $user))->find();
			$result = $user_important->where(array("user_id"=>$user_arr['id']))->save(array("leave_type"=>"","leave_reason"=>"","leave_date"=>"","tail_after_value"=>""));
			$users->where(array("id"=>$user_arr['id']))->save(array("status"=>1));
			$user_basic->where(array("user_id"=>$user_arr['id']))->save(array("status"=>1));
			echo 1;exit;//状态码1：操作成功
		}else{
			echo 2;exit;//状态码2：操作失败
		}
	}


	//入职离职操作
	function leave_pro(){
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		$basic_data = array();
		$important_data = array();
		$important_data = $arr;
		unset($important_data['contract_date']);
		$basic_data['contract_date'] = $arr['contract_date'];
		$basic_data['entry_date'] = $arr['entry_date'];
		$basic_data['campus'] = $arr['campus_post'];
		$basic_data['post'] = $arr['post_post'];
		$users = M("users");
		$user_basic = M("user_basic");
		$user_important = M("user_important");
		$user_arr = $users->where(array("user" =>$_POST['user_card']))->find();
		$important_data["user_id"] = $user_arr['id'];
		if($user_important->where(array("user_id"=>$user_arr['id']))->find()){
			$result = $user_important->where(array("user_id"=>$user_arr['id']))->save($important_data);
			if($important_data["leave_type"]!='请选择'){
				$users->where(array("id"=>$user_arr['id']))->save(array("status"=>0));
				$user_basic->where(array("user_id"=>$user_arr['id']))->save(array("status"=>0));
			}
		}else{
			if($result = $user_important->add($important_data)){
				if($important_data["leave_type"]!='请选择'){
					$users->where(array("id"=>$user_arr['id']))->save(array("status"=>0));
					$user_basic->where(array("user_id"=>$user_arr['id']))->save(array("status"=>0));
				}
				
				echo 1;exit;//状态码1：保存成功
			}else{
				echo 2;exit;//状态码2：保存失败
			}
		}
		$user_basic->where(array("user_id"=>$user_arr['id']))->save($basic_data);
		echo 1;exit;//状态码1：保存成功
	}


	//入职离职信息的显示
	function staff_entry_leave(){
		$user_card = $_POST['user'];
		$users = M("users");
		$user_arr = $users->where(array("user" =>$user_card))->find();
		$user_basic = M('user_basic');
		$user_important = M('user_important');
		$basic_arr = $user_basic->where(array('user_id' => $user_arr['id']))->find(); 
		$important_arr = $user_important->where(array('user_id' => $user_arr['id']))->find(); 
		$data_arr = $important_arr;
		$data_arr['contract_date'] = $basic_arr['contract_date'];
		$data_arr['campus'] = $basic_arr['campus'];
		$data_arr['post'] = $basic_arr['post'];
		echo json_encode($data_arr);
	}


	//员工操作处理（添加、修改）
	function staff_pro(){
		//echo $_POST['work_data'];die;
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		$data = array();
		$data = $arr;
		$work_data = array();
		$work_data = json_decode($_POST['work_data'],1);
		$insure_data = array();
		$insure_data = json_decode($_POST['insure_arr'],1);
		if(empty($arr['check'])){
			echo 3;
			exit;//状态码3：程序出错！
		}else{
			$arr['atten_uid'] = $arr['check'];
			$arr['user'] = $arr['check'];
		}
		if(empty($arr['name'])){
			echo 3;
			exit;//状态码3：程序出错！
		}
		if(empty($arr['nation'])){
			echo 3;
			exit;//状态码3：程序出错！
		}
		if(empty($arr['card'])){
			echo 3;
			exit;//状态码3：程序出错！
		}
		if(empty($arr['telephone'])){
			echo 3;
			exit;//状态码3：程序出错！
		}
		unset($data['user_id']);
		unset($data['insure_data']);
		unset($data['work_data']);
		if(!empty($arr["user_id"])){
			$user_id = $arr["user_id"];
			//员工基本信息修改
			$user_basic = M("user_basic");
			$data["atten_uid"] = $user_arr['user'];
			$data["user_id"] = $user_id;
			$basic_arr = $user_basic->where(array("user_id"=>$user_id))->save($data);
			//员工保险信息修改
			if($insure_data["is_no"]=="是"){
				$user_insure = M("user_insure");
				$insure_data["user_id"] = $user_id;
				$insure_arr = $user_insure->where(array("user_id"=>$user_id))->save($insure_data);
				if(!$insure_arr){
					$insure_arr = $user_insure->add($insure_data);
				}
			}

			//员工工作经历修改
			if(!empty($work_data)){
				$user_work_undergo = M("user_work_undergo");
				foreach ($work_data as &$val) {
					$val["user_id"] = $user_id;
					if(!empty($val['duties'])){
						if(empty($val['id']) && $val['check']=="true"){
							$user_work_undergo->add($val);
					   	} elseif (!empty($val['id']) && $val['check']=="true") {
							$user_work_undergo->where(array("id"=>$val['id']))->save($val);
						} elseif (!empty($val['id']) && $val['check']=="false") {
							$user_work_undergo->where("id=".$val['id'])->delete();
						}
					}
				}
			}
			echo 2;exit;//状态码2：修改成功！
		}else{
			//员工添加
			$users = M("users");
			$user_arr = $users->where(array("user" =>$arr['user']))->find();
			if(!$user_arr){
				if($users->add(array("user" =>$arr['user']))){
					$user_arr = $users->where(array("user" =>$arr['user']))->find();
					$user_id = $user_arr["id"];
				}
			}else{
				echo 4;exit;//状态码4：不可重复添加数据
			}
			
			//员工基本信息添加
			$user_basic = M("user_basic");
			$data["atten_uid"] = $user_arr['user'];
			$data["user_id"] = $user_id;
			$basic_arr = $user_basic->add($data);
			if(!$basic_arr){
				echo 3;exit;//状态码3：程序出错！
			}
			//员工保险信息添加
			if($insure_data["is_no"]=="是"){
				$user_insure = M("user_insure");
				$insure_data["user_id"] = $user_id;
				$insure_arr = $user_insure->add($insure_data);
				if(!$insure_arr){
					echo 3;exit;//状态码3：程序出错！
				}
			}
			
			//员工工作经历添加
			if(!empty($work_data)){
				$user_work_undergo = M("user_work_undergo");
				foreach ($work_data as &$val) {
					$val["user_id"] = $user_id;
					if(!empty($val['duties']) && $val['duties'] && $val['company_name']){
						$work_result = $user_work_undergo->add($val);
						if(!$work_result){
					        echo 3;exit;//状态码3：程序出错！
					    }
					}
				}
			}
			echo 1;exit;//状态码1：添加成功！
		}
	}


	/*//员工操作处理（生成员工号）暂时不用
	function create_user(){
		$user_card = "HW".createRandomStr(8);
		$user_model = M("users");
		if($user_model->where(array("user" => $user_card))->find()){
			$this->create_user();exit;
		}
		echo $user_card;
	}*/


	//打印功能
	function dayin(){
		if(!empty($_SESSION['select_content'])){
			$str = "<br><br><table border=1 class='tb' cellpadding='2' cellspacing='0' id='tb'>
					<tr>
					<th>序号</th>
					<th>员工号</th>
					<th>姓名</th>
					<th>性别</th>
					<th>民族</th>
					<th>身份证号</th>
					<th>婚姻状况</th>
					<th>出生日期</th>
					<th>政治面貌</th>
					<th>籍贯</th>
					<th>应急联系人</th>
					<th>应急联系电话</th>
					<th>毕业院校</th>
					<th>专业</th>
					<th>资格证书</th>
					<th>单位部门</th>
					<th>职务</th>
					<th>入职时间</th>
					<th>联系电话</th>
					<th>QQ</th>
					<th>微信</th>
					<th>E-mail</th>
					</tr>";
			foreach($_SESSION['select_content'] as $key => $val){
				$str .= "<tr class='print_tr'>
				<td class='td_num'>" . ($key+1) . "</td>
				<td class='td_user'>" . $val['user'] . "</td>
				<td class='td_name'>" . $val['name'] . "</td>
				<td class='td_sex'>" . $val['sex'] . "</td>
				<td class='td_nation'>" . $val['nation'] . "</td>
				<td class='td_card'>" . $val['card'] . "</td>
				<td class='td_marriage'>" . $val['marriage'] . "</td>
				<td class='td_birthday'>" . $val['birthday'] . "</td>
				<td class='td_polity'>" . $val['polity'] . "</td>
				<td class='td_residence_booklet'>" . $val['residence_booklet'] . "</td>
				<td class='td_contacts'>" . $val['contacts'] . "</td>
				<td class='td_urgency_telephone'>" . $val['urgency_telephone'] . "</td>
				<td class='td_school'>" . $val['school'] . "</td>
				<td class='td_major'>" . $val['major'] . "</td>
				<td class='td_seniority'>" . $val['seniority'] . "</td>
				<td class='td_campus'>" . $val['campus'] . "</td>
				<td class='td_post'>" . $val['post'] . "</td>
				<td class='td_entry_date'>" . $val['entry_date'] . "</td>
				<td class='td_telephone'>" . $val['telephone'] . "</td>
				<td class='td_qq'>" . $val['qq'] . "</td>
				<td class='td_wechat'>" . $val['wechat'] . "</td>
				<td class='td_email'>" . $val['email'] . "</td>
				</tr>";
				if(($key+1)%30==0){
					$str .= "</table><div class='PageNext'></div><br><br><table border=1 class='tb' cellpadding='2' cellspacing='0' id='tb'><tr>
					<th>序号</th>
					<th>员工号</th>
					<th>姓名</th>
					<th>性别</th>
					<th>民族</th>
					<th>身份证号</th>
					<th>婚姻状况</th>
					<th>出生日期</th>
					<th>政治面貌</th>
					<th>籍贯</th>
					<th>应急联系人</th>
					<th>应急联系电话</th>
					<th>毕业院校</th>
					<th>专业</th>
					<th>资格证书</th>
					<th>单位部门</th>
					<th>职务</th>
					<th>入职时间</th>
					<th>联系电话</th>
					<th>QQ</th>
					<th>微信</th>
					<th>E-mail</th>
					</tr>";
				}
			}
			$str .= "</table>";
		
			$this->assign("content",$str);
			$this->display();
		}else{
			$this->error("打印页面错误，请联系管理员！");exit;
		}
		
	}


	//head头页面显示信息
	function head_con(){
		$data = array();
		if(!empty($_POST['user'])){
			$users = M("users");
			$user_basic = M("user_basic");
			$power = M("power");
			$user_arr = $users->where(array("user"=>$_POST['user']))->find();
			$basic_arr = $user_basic->where(array("user_id" => $user_arr['id']))->find();
			$power_arr = $power->where(array("id"=>$_SESSION['power_id']))->find();
			$data["basic"] = $basic_arr;
			$data["basic"]['user'] = $user_arr['user'];
			$data["basic"]['group_name'] = $power_arr['power_name'];
			$data["status"] = 1;
			echo json_encode($data);
		}else{
			$data["status"] = 2;
			echo json_encode($data);
		}
	}

	//个人信息个人修改操作
	function personel_pro(){
		if(!empty($_POST['user'])){
			$user = $_POST['user'];
		}else{
			echo 2;exit;
		}
		$perfs = explode("&", $_POST['data']);
		foreach($perfs as $perf) {
		    $perf_key_values = explode("=", $perf);
		    $arr[urldecode($perf_key_values[0])] = urldecode($perf_key_values[1]);
		}
		$data = array();
		$data = $arr;
		//员工基本信息修改

		$users = M("users");
		$user_arr = $users->where(array("user"=>$_POST['user']))->find();
		$user_basic = M("user_basic");
		$sulet = $user_basic->where(array("user_id"=>$user_arr['id']))->save($data);
		if($sulet){
			echo 1;
		}else{
			echo 2;
		}
	}



	//个人头像上传
	function photo_upload(){
		$base64_max = explode(",", $_POST['base_arr'][0]);
		$base64_small = explode(",", $_POST['base_arr'][1]);
		$head = '/';
		$file_max = "Public/upload/".$_POST['user']."_max.jpg";
		$file_small = "Public/upload/".$_POST['user']."_small.jpg";
		$img_max = base64_decode($base64_max[1]);
		$img_small = base64_decode($base64_small[1]);
		$max = file_put_contents($file_max, $img_max);
		$small = file_put_contents($file_small, $img_small);
		$users = M("users");
		$user_arr = $users->where(array("user"=>$_POST['user']))->find();
		$user_basic = M("user_basic");
		$user_basic->where(array("user_id"=>$user_arr['id']))->save(array('photo_max_url'=>$head.$file_max,'photo_small_url'=>$head.$file_small));
		echo 1;exit;
	}
}
?>