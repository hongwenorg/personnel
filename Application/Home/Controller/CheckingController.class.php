<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:调用考勤系统接口
*/
namespace Home\Controller;
use Think\Controller;
header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
header("Content-type:text/html;charset=utf-8");
header('Access-Control-Allow-Origin: *');
require_once( "/Public/public.php" );
class CheckingController extends CommonController {
	//首页（打卡记录）
	function index(){
		$this->display();
	}


	//考勤记录页面
	function check(){
		$this->display();
	}


	//考勤规则页面
	function rule_select(){
		if(!empty($_POST['content'])){
			$content = $_POST['content'];
		}else{
			$this->error("未知错误，请联系管理员！");
			exit;
		}
		$basic_arr = array();
		$rule_arr = array();
		$user_basic = D("users");
		$user_rule = D("user_rule");
		$basic_where = "users.id!=1 and users.status!=0 and user_basic.status!=0 and user_basic.campus like '%$content%'";
		$rule_where = "";
		$basic_arr = $user_basic->join('user_basic ON users.id = user_basic.user_id')->field('user_basic.id,user_basic.user_id,users.user,user_basic.name,user_basic.sex,user_basic.campus,user_basic.post,user_basic.rule_id')->where($basic_where)->select();
		$rule_arr = $user_rule->where($rule_where)->select();
		foreach($basic_arr as &$value){
			foreach($value as $key => $val){
				if($key == 'rule_id'){
					$rule_id_arr = explode(',',$val);
					$rules_arr = array();
					foreach($rule_id_arr as $id_val){
						foreach($rule_arr as $rule_arr_val){
							if($id_val == $rule_arr_val['id']){
								$rules_arr[] = $rule_arr_val;
							}
						}
					}
					$value['rule_arr'] = $rules_arr;
					for($i=0;$i<6;$i++){
						if(!empty($rules_arr[$i])){
							$value["rule_min$i"] = $rules_arr[$i]['rule_mintime'];
							$value["rule_max$i"] = $rules_arr[$i]['rule_maxtime'];
						}else{
							$value["rule_min$i"] = "";
							$value["rule_max$i"] = "";
						}
					}
				}
			}
		}
		// echo "<pre>";
		// print_r($basic_arr);die;
		if($basic_arr){
			$basic_arr['status'] = 1;
			echo json_encode($basic_arr);
		}else{
			$basic_arr['status'] = 2;
			echo json_encode($basic_arr);
		}
	}



	//考勤规则查询
	function check_rule_find(){
		if(!empty($_POST['basic_id'])){
			$basic_id = $_POST['basic_id'];
		}else{
			$this->error("未知错误，请联系管理员！");
			exit;
		}
		$basic_arr = array();
		$rule_arr = array();
		$rule_data = array();
		$user_basic = D("user_basic");
		$user_rule = D("user_rule");
		$basic_where = "id=$basic_id and status!=0";
		$rule_where = "";
		$basic_arr = $user_basic->field('rule_id,rule_disable_id')->where($basic_where)->find();
		$rule_arr = $user_rule->where($rule_where)->select();
		$rules_arr = array();
		foreach($basic_arr as $key => $val){
			$rule_id_arr = explode(',',$val);
			foreach($rule_id_arr as $id_val){
				foreach($rule_arr as $rule_arr_val){
					if($id_val == $rule_arr_val['id'] && $key!='rule_disable_id'){
						$rules_arr['rule'][] = $rule_arr_val;
					}elseif($id_val == $rule_arr_val['id'] && $key=='rule_disable_id'){
						$rules_arr['rule_disable'][] = $rule_arr_val;
					}
				}
			}
		}
		// echo "<pre>";
		// print_r($rules_arr);die;
		if($rules_arr){
			$rules_arr['status'] = 1;
			echo json_encode($rules_arr);
		}else{
			$rules_arr['status'] = 2;
			echo json_encode($rules_arr);
		}
	}


	//规则页面加载
	function rule_load(){
		$campus_arr = array();
		$campus_class_post = D("campus_class_post");
		$campus_where = "type='1'";
		$campus_arr = $campus_class_post->where($campus_where)->select();
		// echo "<pre>";
		// print_r($campus_arr);die;
		if($campus_arr){
			$campus_arr['status'] = 1;
			echo json_encode($campus_arr);
		}else{
			$campus_arr['status'] = 2;
			echo json_encode($campus_arr);
		}
	}



	//修改考勤规则
	function check_rule_pro(){
		$data = array();
		if(!empty($_POST['data'])){
			$content_arr = json_decode($_POST['data']);
		}else{
			echo 2;exit;//状态码2：保存失败
		}
		if(empty($_POST['user_id'])){
			echo 2;exit;//状态码2：保存失败
		}
		$user_basic = M("user_basic");
		$user_rule = M("user_rule");

		$rule_arr = $user_rule->select();
		//得到数据，状态码为0时是默认，为1时是启用，为2时是禁用
		foreach($content_arr as $data_val){
			if($data_val[1] == '0' || $data_val[1] == '1'){
				$data['rule_id'] .= $data_val[0].",";//可用的时间规则
			}
			if($data_val[1] == '2'){
				$data['rule_disable_id'] .= $data_val[0].",";//不可用的时间规则
			}
			$data['rule_all_id'] .= $data_val[0].",";//所有的时间规则
		}
		foreach($data as &$val){
			$val = rtrim($val,",");
		}
		$user_basic->where(array("id"=>$_POST['user_id']))->save($data);
		echo 1;exit;//状态码1：保存成功
	}


	//制定时间规则的查询(监听onclick事件)
	function rule_all_select(){
		$user_rule = M("user_rule");
		$rule_arr = $user_rule->select();
		echo json_encode($rule_arr);exit;
	}


	//制定时间规则的修改
	function rule_update(){
		if(!empty($_POST['id'])){
			$id = $_POST['id'];
		}else{
			echo 2;exit;
		}

		if(!empty($_POST['rule_mintime'])){
			$rule_mintime = $_POST['rule_mintime'];
		}else{
			echo 2;exit;
		}
		if(!empty($_POST['rule_maxtime'])){
			$rule_maxtime = $_POST['rule_maxtime'];
		}else{
			echo 2;exit;
		}
		$data = array();
		$data['rule_mintime'] = $rule_mintime;
		$data['rule_maxtime'] = $rule_maxtime;

		$rule_min_arr = explode(":",$rule_mintime);
		$data['rule_minhour'] = $rule_min_arr[0];
		$data['rule_minminute'] = $rule_min_arr[1];

		$rule_max_arr = explode(":",$rule_maxtime);
		$data['rule_maxhour'] = $rule_max_arr[0];
		$data['rule_maxminute'] = $rule_max_arr[1];

		$user_rule = M("user_rule");
		$rule_result = $user_rule->where(array("id" => $id))->save($data);
		echo 1;exit;
	}


	//指定时间规则的删除
	function rule_delete(){
		if(!empty($_POST['id'])){
			$id = $_POST['id'];
		}else{
			echo 2;exit;
		}
		$user_rule = M("user_rule");
		$rule_result = $user_rule->where(array("id" => $id))->delete();
		if($rule_result){
			echo 1;exit;
		}else{
			echo 2;exit;
		}
	}


	//指定时间规则的提交
	function rule_submit(){
		if(!empty($_POST['rule_mintime'])){
			$rule_mintime = $_POST['rule_mintime'];
		}else{
			echo 2;exit;
		}
		if(!empty($_POST['rule_maxtime'])){
			$rule_maxtime = $_POST['rule_maxtime'];
		}else{
			echo 2;exit;
		}
		$data = array();
		$data['rule_mintime'] = $rule_mintime;
		$data['rule_maxtime'] = $rule_maxtime;

		$rule_min_arr = explode(":",$rule_mintime);
		$data['rule_minhour'] = $rule_min_arr[0];
		$data['rule_minminute'] = $rule_min_arr[1];

		$rule_max_arr = explode(":",$rule_maxtime);
		$data['rule_maxhour'] = $rule_max_arr[0];
		$data['rule_maxminute'] = $rule_max_arr[1];

		$user_rule = M("user_rule");
		$rule_result = $user_rule->add($data);
		if($rule_result){
			echo 1;exit;
		}else{
			echo 2;exit;
		}
	}



	//打卡信息搜索结果
	function index_select(){
		//die;
		$data_arr=array();
		$users=M("users");
		$where = "users.id!=1 and users.status!=0 and user_basic.status!=0";//下面是获取上一月的数据
		$data = $this->year_month_day();
		$now_time = $data['now_time'];
		$year_day = $data['year_day'];//哪一年
		$month_day = $data['month_day'];//哪个月份
		$check_content = $data['check_content'];//查询条件
		$day_count = $data['day_count'];//每个月一共多少天

		if(!empty($_POST['status'])){
			$status = $_POST['status'];
		}else{
			$status = "";
			$where .= "";
		}
		if(!empty($_POST['content'])){
			$content = $_POST['content'];
		}else{
		}
		if($status=="campus"){
				$where .= " and user_basic.campus like '%$content%'";
		}elseif($status == "time") {
			//如果时间前台选择了则执行下面的
			$check_content = $content."-";
			$time_arr = explode("-",$check_content);
			$year_day = $time_arr[0];
			$month_day = $time_arr[1];
			$now_time = $time_arr[1]+1;
		}elseif($status == "name") {
			$where .= " and user_basic.name like '%$content%'";
		}elseif($status == "user") {
			$where .= " and users.user = '$content'";
		}
		$hour = " 06:00:00";
		$mintime = strtotime($check_content."01".$hour);//每天最早打卡时间戳
		$maxtime = strtotime(date("Y-m",time())."-01".$hour);//每天最晚打卡时间戳（记录到第二天6点之前）
		//查询条件
		$where .= " and (checking.atten_time_str between '$mintime' and '$maxtime')";

		//echo $where;die;
		$data_arr=$users->join('user_basic ON users.id = user_basic.user_id')->join('checking ON users.user = checking.atten_uid')->field('users.id,user_basic.user_id,users.user,user_basic.name,user_basic.sex,user_basic.campus,user_basic.post,checking.atten_time')->where($where)->order("checking.atten_time")->select();
		//print_r($data_arr);die;
		if(!empty($data_arr)){
			foreach($data_arr as &$value){
				foreach($value as $key => &$val){
					if($key == "atten_time_str"){
						$val = date("Y-m-d H:i:s",$val);
					}
				}
			}
			$data_arr['status'] = 1;
			echo json_encode($data_arr);
		}else{
			$data_arr['status'] = 2;
			echo json_encode($data_arr);
		}
	}




	//计算每个月有多少周日
	function get_weekend_days($start_date,$end_date){ 
		if (strtotime($start_date) > strtotime($end_date)) list($start_date, $end_date) = array($end_date, $start_date); 
		$start_reduce = $end_add = 0; 
		$start_N = date('N',strtotime($start_date)); 
		$start_reduce = ($start_N == 7) ? 1 : 0; 
		$end_N = date('N',strtotime($end_date)); 
		in_array($end_N,array(6,7)) && $end_add = ($end_N == 7) ? 2 : 1; 
		$days = abs(strtotime($end_date) - strtotime($start_date))/86400 + 1; 
		//返回计算周日天数
		return floor(($days + $start_N - 1 - $end_N) / 7);//* 2 - $start_reduce + $end_add; 加上此处是计算多少周六日
	} 



	//计算每个月有多少个周？
	function get_one_days($b,$e,$num){
		$n=0;
		$btime=strtotime($b);
		$etime=strtotime($e);
		for($i=$btime;$i<=$etime;$i=$i+86400){
			if(date("N",$i)==$num){
				$n++;
			}
		}
		return $n;
		//echo $n;
	}


	//通过日期时间戳来判断周几
	function weekday($time){
		if(is_numeric($time)){
			$weekday = array('7','1','2','3','4','5','6');
			return $weekday[date('w', $time)];
		}
		return false;
	}
	



	//考勤信息统计结果
	function check_count(){
		$data_arr=array();
		$num_con = 6.5;
		$users=D("users");
		$leaveing = M("leaveing");
		$where = "users.id!=1 and users.status!=0 and user_basic.status!=0 and user_basic.rule_id!='0'";
		//下面是获取上一月的数据
		$data = $this->year_month_day();
		$now_time = $data['now_time'];
		$year_day = $data['year_day'];//哪一年
		$month_day = $data['month_day'];//哪个月份
		$check_content = $data['check_content'];//查询条件
		$day_count = $data['day_count'];//每个月一共多少天

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
		}elseif($status == "time") {
			$check_content = $content;
			$time_arr = explode("-",$check_content);
			$year_day = $time_arr[0];
			$month_day = $time_arr[1];
			$now_time = $time_arr[1]+1;
			$where .= "";
		}elseif($status == "name") {
			$where .= " and user_basic.name like '%$content%'";
		}elseif($status == "user") {
			$where .= " and users.user = '$content'";
		}
		$leave_where = " and time_date like '%$check_content%'";
		$check_where = " and check_date like '%$check_content%'";
		$month_arr = array(1=>"一",2=>"二",3=>"三",4=>"四",5=>"五",6=>"六",7=>"七",8=>"八",9=>"九",10=>"十",11=>"十一",12=>"十二");//定义十二个月数组
		$day_day = date("t",strtotime("$year_day-$month_day"));
		$week_day = $this->get_weekend_days("$year_day-$month_day-01","$year_day-$month_day-$day_day");//获取指定月份周日的天数
		$week_one = $this->get_one_days("$year_day-$month_day-01","$year_day-$month_day-$day_day",1);//获取指定月份周一的天数
		$week_two = $this->get_one_days("$year_day-$month_day-01","$year_day-$month_day-$day_day",2);//获取指定月份周二的天数
		$week_six = $this->get_one_days("$year_day-$month_day-01","$year_day-$month_day-$day_day",6);//获取指定月份周六的天数
		$data_arr=$users->join('user_basic ON users.id = user_basic.user_id')->field('users.id,user_basic.user_id,user_basic.atten_uid,user,name,sex,campus,post,user_basic.entry_date')->where($where)->select();
		//echo $users->getLastsql();
		//请假数据数组
		$leave_data = $leaveing->where("class='请假' and  state = '审核通过' and time_date like '%$check_content%'")->select();
		
		//加班数据数组
		$overtime_data = $leaveing->where("class='加班' and state = '审核通过' and time_begin like '%$check_content%'")->select();

		//上下班未打卡数据数组
		$all_noclock_data = $leaveing->query("SELECT COUNT(*) as count,atten_uid from leaveing where class='意外事项' and state = '审核通过' and info like '上班未打卡，下班未打卡%'".$leave_where." GROUP BY name");

		//echo "<pre>";
		//print_r($leave_data);
		//print_r($data_arr);die;

		if(!empty($data_arr)){
			$check_record = M('check_record');
			
			foreach($data_arr as &$val){
				
				$noclock = 0;//未打卡天数变量
				$late = 0;//迟到天数变量
				$early = 0;//早退天数变量
				$sick_leave = 0;//病假天数变量
				$personal_leave = 0;//事假天数变量
				$personal_leave_count = 0;//事假核算天数变量
				$sick_leave_count = 0;//病假核算天数变量

				//计算未打卡天数
				$noclock_data = $check_record->query("SELECT COUNT(*) AS tp_count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '不合格' ) AND (`check_content` like '%未打卡') ".$check_where);
				$noclock = $noclock_data[0]['tp_count'];

				$val['noclock'] = $noclock;


				//计算灵活打卡异常天数
				$error_arr = $check_record->query("SELECT COUNT(*) AS tp_count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '不合格' ) AND (`check_content` like '灵活打卡异常%') ".$check_where);
				$errors = $error_arr[0]['tp_count'];

				$val['errors'] = $errors;


				
				//计算迟到天数
				$late_data = $check_record->query("SELECT COUNT(*) AS tp_count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '不合格' ) AND (`check_content` = '迟到') ".$check_where);
				$late = $late_data[0]['tp_count'];
				$val['late'] = $late;

				//计算早退天数
				$early_data = $check_record->query("SELECT COUNT(*) AS tp_count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '不合格' ) AND (`check_content` = '早退') ".$check_where);
				$early = $early_data[0]['tp_count'];
				$val['early'] = $early;
				
				//计算每个月应出勤天数
				if($val['post'] == '助理' || (strstr($val['campus'],"校区") && strstr($val['post'],"财务"))){
					$over_count_day = $day_day-$week_day;
				}elseif($val['post']=="学习管理师" || $val['post'] == '教学主任'){
					if($week_one>$week_two){
						$over_count_day = $day_day-$week_one;
					}else{
						$over_count_day = $day_day-$week_two;
					}

				}elseif(strstr($val['post'],"校长")){
					$over_count_day = $day_day-$week_one;
				}elseif(strstr($val['post'],"讲师")){
					$over_count_day = $day_day-$week_one-$week_two;
				}elseif(strstr($val['campus'],"校区") && strstr($val['post'],"教研员")){
					$over_count_day = $day_day-$week_one-$week_two;
				}elseif(strstr($val['campus'],"集团") && strstr($val['post'],"教研员")){
					$over_count_day = $day_day-$week_one;
				}elseif($val['campus'] == '集团财务行政中心'){
					$over_count_day = $day_day-$week_six-$week_day;
				}elseif(strstr($val['campus'],"水木清华") && strstr($val['post'],"教务")){
					$over_count_day = $day_day-$week_one;
				}elseif(strstr($val['campus'],"天丽家园校区") && strstr($val['post'],"教务")){
					$over_count_day = $day_day-$week_six-$week_day;
				}elseif(strstr($val['campus'],"松原江北") && strstr($val['post'],"教务")){
					$over_count_day = $day_day-$week_two;
				}else{
					$over_count_day = $day_day-$week_day;
				}
				$val['over_count_day'] = $over_count_day;

				//定义出勤的变量
				if($over_count_day!="未定"){
					$count_yes = $over_count_day;
				}else{
					$count_yes = '未定';
				}


				
				foreach($leave_data as $leave_key=>$leave_val){
					if($leave_val['property']=='病假' && $val['atten_uid']==$leave_val['atten_uid']){
						$sick_leave+=floatval($leave_val['count_day1']);
					}elseif($leave_val['property']=='事假' && $leave_val['atten_uid']==$val['atten_uid']){
						$personal_leave+=floatval($leave_val['count_day1']);
					}elseif($leave_val['property']=='产假' && $leave_val['atten_uid']==$val['atten_uid']){
						$count_yes+=floatval($leave_val['count_day1']);
					}elseif($leave_val['property']=='婚假' && $leave_val['atten_uid']==$val['atten_uid']){
						$count_yes+=floatval($leave_val['count_day1']);
					}elseif($leave_val['property']=='丧假' && $leave_val['atten_uid']==$val['atten_uid']){
						$count_yes+=floatval($leave_val['count_day1']);
					}
				}


				//计算加班小时数
				$overtime_count = 0; 
				foreach($overtime_data as $overtime_data_val){
					if($overtime_data_val['atten_uid']==$val['atten_uid']){
						$overtime_count = floatval($overtime_count) + floatval($overtime_data_val['count_day1']);
					}
				}

				//echo $personal_leave;
				$val['overtime'] = $overtime_count;
				$val['sick_leave'] = $sick_leave;
				$val['personal_leave'] = $personal_leave;

				//根据事假核算天数规则核算结果
				if($personal_leave<='3'){
					//每月事假3天以内，事假核算天数=实际请假天数；
					$personal_leave_count = $personal_leave;
				}elseif($personal_leave == '4' || $personal_leave == '5'){
					//每月事假4-5天，事假核算天数=3+(4-3)*2或3+(5-3)*2；
					$personal_leave_count = 3+($personal_leave-3)*2;
				}elseif($personal_leave == '6' || $personal_leave == '7'){
					//每月事假6-7天，事假核算天数=7+(6-5)*3；或7+(7-5)*3；
					$personal_leave_count = 7+($personal_leave-5)*3;
				}elseif($personal_leave>='8'){
					//每月事假n天(n>=8)，事假核算天数=13+(n-7)*4；
					$personal_leave_count = 13+($personal_leave-7)*4;
				}
				$val['personal_leave_count'] = $personal_leave_count;

				//根据病假核算天数规则核算结果
				$user_year = substr($val['entry_date'],0,4);
				$user_month = substr($val['entry_date'],5,2);
				if(($year_day-$user_year) < 2){
					//连续工龄不满2年的，病假核算天数=病假*0.4；
					$sick_leave_count = $sick_leave*0.4;
				}elseif(($year_day-$user_year) >= 2 && ($year_day-$user_year) < 4){
					//连续工龄满2年不满4年的，病假核算天数=病假*0.3；
					if(($year_day-$user_year) == 2 && $user_month < $month_day){
						$sick_leave_count = $sick_leave*0.4;
					}else{
						$sick_leave_count = $sick_leave*0.3;
					}
				}elseif(($year_day-$user_year) >= 4 && ($year_day-$user_year) < 6){
					//连续工龄满4年不满6年的，病假核算天数=病假*0.2；
					if(($year_day-$user_year) == 4 && $user_month < $month_day){
						$sick_leave_count = $sick_leave*0.3;
					}else{
						$sick_leave_count = $sick_leave*0.2;
					}
				}elseif(($year_day-$user_year) >= 6 && ($year_day-$user_year) < 8){
					//连续工龄满6年不满8年的，病假核算天数=病假*0.1；
					if(($year_day-$user_year) == 6 && $user_month < $month_day){
						$sick_leave_count = $sick_leave*0.2;
					}else{
						$sick_leave_count = $sick_leave*0.1;
					}
				}elseif(($year_day-$user_year) >= 8){
					//连续工龄满8年及以上的，病假核算天数=病假*0；
					if(($year_day-$user_year) == 8 && $user_month < $month_day){
						$sick_leave_count = $sick_leave*0.1;
					}else{
						$sick_leave_count = 0;
					}
				}
				$val['sick_leave_count'] = $sick_leave_count;

				
				//查询旷工次数
				$absenteeism_data = $check_record->query("SELECT COUNT(*) AS count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) ".$check_where);
				$absenteeism_count = $check_record->query("SELECT COUNT(*) AS count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) and (`check_content` like '%旷工%') ".$check_where);
				
				//定义旷工次数
				$absenteeism = $over_count_day-$absenteeism_data[0]['count']-$personal_leave-$sick_leave;
				$absenteeism = $absenteeism+$absenteeism_count[0]['count'];
				foreach($all_noclock_data as $all_noclock_val){
					if($val['atten_uid'] == $all_noclock_val['atten_uid']){
						//如果上下班未打卡有申请则减去申请天数
						$absenteeism = $absenteeism-$all_noclock_val['count'];
					}
				}
				if($absenteeism<0){
					$absenteeism = 0;
				}
				$val['absenteeism'] = $absenteeism;//Intval($absenteeism);



				//计算加班算作天数
				$total = $overtime_count/$num_con;  
				$total = sprintf('%.1f', (float)$total);

				//计算出勤合格天数
				if($count_yes != '未定'){
					$val['count_yes'] = $count_yes-($absenteeism*3)-$sick_leave_count-$personal_leave_count-$noclock-$late-$early+$total;
				}else{
					$val['count_yes'] = '未定';
				}

				if($absenteeism == $over_count_day){
					$val['count_yes'] = 0;
				}
				if($val['count_yes'] < 0){
					$val['count_yes'] = 0;
				}
				


				//是否满勤
				if($val['count_yes'] >= $over_count_day){
					$val['is_no'] = '是';
				}else{
					$val['is_no'] = '否';
				}
			}
			$data_arr['month'] = $month_arr[Intval($now_time)-1];
			$data_arr['count_day'] = $day_day;
			$data_arr['week_day'] = $week_day;
			$data_arr['over_count_day'] = $day_day-$week_day;
			//echo $month_arr[Intval($now_time)-1];die;
			//echo "<pre>";
			$data_arr['status'] = 1;
			//print_r($data_arr);die;
			$_SESSION['select_content'] = $data_arr;
			echo json_encode($data_arr);
		}else{
			$data_arr['status'] = 2;
			unset($_SESSION['select_content']);
			echo json_encode($data_arr);
		}
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

}

?>