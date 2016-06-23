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


	//打卡信息搜索结果
	function index_select(){
		//die;
		$data_arr=array();
		$users=M("users");
		$where = "users.id!=1 and users.status!=0";//下面是获取上一月的数据
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
			$where .= " and user_basic.name = '$content'";
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


/*	//考勤信息搜索结果
	function check_select(){
		$data_arr=array();
		$users=D("users");
		$where = "users.id!=1 and users.status!=0 and user_basic.atten_uid!=0";
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
			//查询条件
			$status = "";
			$where .= "";
		}
		if($status=="campus"){
				$where .= " and user_basic.campus like '%$content%'";
		}elseif($status == "time") {
			//如果时间前台选择了则执行下面的
			$check_content = $content;
			$time_arr = explode("-",$check_content);
			$year_day = $time_arr[0];
			$month_day = $time_arr[1];
			$now_time = $time_arr[1]+1;
		}elseif($status == "name") {
			$where .= " and user_basic.name = '$content'";
		}elseif($status == "user") {
			$where .= " and users.user = '$content'";
		}
		$where .= " and (checking.atten_date like '%$content%')";
		$data_arr=$users->join('user_basic ON users.id = user_basic.user_id')->join('check_record ON users.id = check_record.user_id')->where($where)->select();
		if(!empty($data_arr)){
			$data_arr['status'] = 1;
			echo json_encode($data_arr);
		}else{
			$data_arr['status'] = 2;
			echo json_encode($data_arr);
		}
	}*/




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



	//计算每个月有多少个周一
	function get_one_days($b,$e,$num){
		$n=0;
		$btime=strtotime($b);
		$etime=strtotime($e);
		for($i=$btime;$i<$etime;$i=$i+86400){
			if(date("N",$i)==$num){
				$n++;
			}
		}
		return $n;
		//echo $n;
	}
	



	//考勤信息统计结果
	function check_count(){
		$data_arr=array();
		$num_con = 7.5;
		$users=D("users");
		$leaveing = M("leaveing");
		$where = "users.id!=1 and users.status!=0 and user_basic.rule_id!='0'";
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
			$where .= " and user_basic.name = '$content'";
		}elseif($status == "user") {
			$where .= " and users.user = '$content'";
		}
		$check_where = " and check_date like '%$check_content%'";
		$month_arr = array(1=>"一",2=>"二",3=>"三",4=>"四",5=>"五",6=>"六",7=>"七",8=>"八",9=>"九",10=>"十",11=>"十一",12=>"十二");
		$day_day = date("t",strtotime("$year_day-$month_day"));
		$week_day = $this->get_weekend_days("$year_day-$month_day-01","$year_day-$month_day-$day_day");
		$week_one = $this->get_one_days("$year_day-$month_day-01","$year_day-$month_day-$day_day",1);
		$week_two = $this->get_one_days("$year_day-$month_day-01","$year_day-$month_day-$day_day",2);
		$week_six = $this->get_one_days("$year_day-$month_day-01","$year_day-$month_day-$day_day",6);
		$data_arr=$users->join('user_basic ON users.id = user_basic.user_id')->field('users.id,user_basic.user_id,user_basic.atten_uid,user,name,sex,campus,post,user_basic.entry_date')->where($where)->select();
		//echo $users->getLastsql();
		$leave_data = $leaveing->where("class='请假' and  state = '审核通过' and time_date like '%$check_content%'")->select();
		

		$overtime_data = $leaveing->where("class='加班' and state = '审核通过' and time_begin like '%$check_content%'")->select();

		$all_noclock_data = $leaveing->query("SELECT COUNT(*) as count,atten_uid from leaveing where class='意外事项' and state = '审核通过' and info like '上班未打卡，下班未打卡%'".$leave_where." GROUP BY name");

		//echo "<pre>";
		//print_r($leave_data);
		//print_r($data_arr);die;

		if(!empty($data_arr)){
			$check_record = M('check_record');
			
			foreach($data_arr as &$val){


				//计算未打卡天数
				$noclock_data = $check_record->query("SELECT COUNT(*) AS tp_count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '不合格' ) AND (`check_content` like '%未打卡') ".$check_where);
				$noclock = $noclock_data[0]['tp_count'];

				$val['noclock'] = $noclock;


				//计算出勤合格的天数
				$count_yes = $check_record->query("SELECT COUNT(check_name) AS tp_count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '合格' )".$check_where);
				$val['count_yes'] = $count_yes[0]['tp_count'];

				
				//计算迟到天数
				$late_data = $check_record->query("SELECT COUNT(*) AS tp_count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '不合格' ) AND (`check_content` = '迟到') ".$check_where);
				$val['late'] = $late_data[0]['tp_count'];

				//计算早退天数
				$early_data = $check_record->query("SELECT COUNT(*) AS tp_count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) AND ( `check` = '不合格' ) AND (`check_content` = '早退') ".$check_where);
				$val['early'] = $early_data[0]['tp_count'];

				
				//计算每个月应出勤天数
				if($val['post'] == '助理' || (strstr($val['campus'],"校区") && strstr($val['post'],"财务"))){
					$val['over_count_day'] = $day_day-$week_day;
				}elseif($val['post']=="学习管理师" || $val['post'] == '教学主任'){
					if($week_one>$week_two){
						$val['over_count_day'] = $day_day-$week_one;
					}else{
						$val['over_count_day'] = $day_day-$week_two;
					}

				}elseif(strstr($val['post'],"校长")){
					$val['over_count_day'] = $day_day-$week_one;
				}elseif(strstr($val['post'],"讲师")){
					$val['over_count_day'] = $day_day-$week_one-$week_two;
				}elseif(strstr($val['campus'],"校区") && strstr($val['post'],"教研员")){
					$val['over_count_day'] = $day_day-$week_one-$week_two;
				}elseif(strstr($val['campus'],"集团") && strstr($val['post'],"教研员")){
					$val['over_count_day'] = $day_day-$week_one;
				}elseif($val['campus'] == '集团财务行政中心'){
					$val['over_count_day'] = $day_day-$week_six-$week_day;
				}else{
					$val['over_count_day'] = "未定";
				}


				$sick_leave_count = 0;
				$personal_leave_count = 0;
				foreach($leave_data as $leave_key=>$leave_val){
					if($leave_val['property']=='病假' && $val['atten_uid']==$leave_val['atten_uid']){
						$sick_leave_count+=floatval($leave_val['count_day1']);
					}elseif($leave_val['property']=='事假' && $leave_val['atten_uid']==$val['atten_uid']){
						$personal_leave_count+=floatval($leave_val['count_day1']);
					}elseif($leave_val['property']=='产假' && $leave_val['atten_uid']==$val['atten_uid']){
						$val['count_yes']+=floatval($leave_val['count_day1']);
					}elseif($leave_val['property']=='婚假' && $leave_val['atten_uid']==$val['atten_uid']){
						$val['count_yes']+=floatval($leave_val['count_day1']);
					}elseif($leave_val['property']=='丧假' && $leave_val['atten_uid']==$val['atten_uid']){
						$val['count_yes']+=floatval($leave_val['count_day1']);
					}
				}
				//计算加班小时数
				$overtime_count = 0; 
				foreach($overtime_data as $overtime_data_val){
					if($overtime_data_val['atten_uid']==$val['atten_uid']){
						$overtime_count = floatval($overtime_count) + floatval($overtime_data_val['count_day1']);
					}
				}
				//计算加班算作天数
				$total = $overtime_count/$num_con;  
				$total = sprintf('%.1f', (float)$total); 
				$val['count_yes']+=($total);

				//echo $personal_leave_count;
				$val['overtime'] = $overtime_count;
				$val['sick_leave'] = $sick_leave_count;
				$val['personal_leave'] = $personal_leave_count;

				//根据事假核算天数规则核算结果
				if($personal_leave_count<='3'){
					//每月事假3天以内，事假核算天数=实际请假天数；
					$val['personal_leave_count'] = $personal_leave_count;
				}elseif($personal_leave_count == '4' || $personal_leave_count == '5'){
					//每月事假4-5天，事假核算天数=3+(4-3)*2或3+(5-3)*2；
					$val['personal_leave_count'] = 3+($personal_leave_count-3)*2;
				}elseif($personal_leave_count == '6' || $personal_leave_count == '7'){
					//每月事假6-7天，事假核算天数=7+(6-5)*3；或7+(7-5)*3；
					$val['personal_leave_count'] = 7+($personal_leave_count-5)*3;
				}elseif($personal_leave_count>='8'){
					//每月事假n天(n>=8)，事假核算天数=13+(n-7)*4；
					$val['personal_leave_count'] = 13+($personal_leave_count-7)*4;
				}

				//根据病假核算天数规则核算结果
				$user_year = substr($val['entry_date'],0,4);
				$user_month = substr($val['entry_date'],5,2);
				if(($year_day-$user_year) < 2){
					//连续工龄不满2年的，病假核算天数=病假*0.4；
					$val['sick_leave_count'] = $sick_leave*0.4;
				}elseif(($year_day-$user_year) >= 2 && ($year_day-$user_year) < 4){
					//连续工龄满2年不满4年的，病假核算天数=病假*0.3；
					if(($year_day-$user_year) == 2 && $user_month < $month_day){
						$val['sick_leave_count'] = $sick_leave*0.4;
					}else{
						$val['sick_leave_count'] = $sick_leave*0.3;
					}
				}elseif(($year_day-$user_year) >= 4 && ($year_day-$user_year) < 6){
					//连续工龄满4年不满6年的，病假核算天数=病假*0.2；
					if(($year_day-$user_year) == 4 && $user_month < $month_day){
						$val['sick_leave_count'] = $sick_leave*0.3;
					}else{
						$val['sick_leave_count'] = $sick_leave*0.2;
					}
				}elseif(($year_day-$user_year) >= 6 && ($year_day-$user_year) < 8){
					//连续工龄满6年不满8年的，病假核算天数=病假*0.1；
					if(($year_day-$user_year) == 6 && $user_month < $month_day){
						$val['sick_leave_count'] = $sick_leave*0.2;
					}else{
						$val['sick_leave_count'] = $sick_leave*0.1;
					}
				}elseif(($year_day-$user_year) >= 8){
					//连续工龄满8年及以上的，病假核算天数=病假*0；
					if(($year_day-$user_year) == 8 && $user_month < $month_day){
						$val['sick_leave_count'] = $sick_leave*0.1;
					}else{
						$val['sick_leave_count'] = 0;
					}
				}

				
				//查询旷工次数
				$absenteeism_data = $check_record->query("SELECT COUNT(*) AS count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) ".$check_where);
				$absenteeism_count = $check_record->query("SELECT COUNT(*) AS count FROM `check_record` WHERE ( `user_id` = ".$val['user_id']." ) and (`check_content` like '旷工') ".$check_where);
				
				
				$absenteeism = $val['over_count_day']-$absenteeism_data[0]['count']-$personal_leave_count-$sick_leave_count+$absenteeism_count[0]['count'];
				foreach($all_noclock_data as $all_noclock_val){
					if($val['atten_uid'] == $all_noclock_val['atten_uid']){
						$absenteeism = $absenteeism-$all_noclock_val['count'];
						$val['count_yes'] = $val['count_yes']+$all_noclock_val['count'];
					}
				}

				if($absenteeism<0){
					$absenteeism = 0;
				}
				$val['absenteeism'] = Intval($absenteeism);
				if($val['count_yes'] >= $val['over_count_day']){
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



	//定时小程序，每到月一号计算上个月请假记录
	function leave_pro(){
		die;
		//下面是获取上一月的数据
		$data = $this->year_month_day();
		$now_time = $data['now_time'];
		$year_day = $data['year_day'];//哪一年
		$month_day = $data['month_day'];//哪个月份
		$check_content = $data['check_content'];//查询条件，格式：“2016-05-”
		$day_count = $data['day_count'];//每个月一共多少天

		$url = "http://192.168.5.105/oa/weixin.php/Weixin/getWeixinAskInfo?time1=2016-03-01&time2=2016-05-01";//".$check_content."01&time2=".$check_content.$day_count;
		//echo $url;die;
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$file_contents = curl_exec ( $ch );
		$data = json_decode($file_contents,true);
		$leaveing = D('leaveing');
		foreach($data as $val){
			$results = $leaveing->add($val);
			if(!$results){
				$this->error("程序出错！");exit;
			}
		}
		echo 1;
		curl_close ( $ch );
	}



	//定时小程序，每到月一号计算上个月考勤记录表数据处理添加
	function check_record_fun(){
		die;
		//下面是获取上一月的数据
		$data = $this->year_month_day();
		$now_time = $data['now_time'];
		$year_day = $data['year_day'];//哪一年
		$month_day = $data['month_day'];//哪个月份
		$check_content = $data['check_content'];//查询条件
		$day_count = $data['day_count'];//每个月一共多少天

		//echo $day_count;
		$checking = D('checking');
		$user_basic = D('user_basic');
		$hour = " 06:00:00";
		$check_arr = array();
		$check_data =array();
		for($i=1;$i<=$day_count;$i++){
			$minday = $i;
			$maxday = $i+1;
			if($minday<10){
				$minday = '0'.$minday;
			}
			if($maxday<10){
				$maxday = '0'.$maxday;
			}
			$mintime = strtotime($check_content.$minday.$hour);//每天最早打卡时间戳
			if($i==$day_count){
				$maxtime = strtotime(date("Y-m",time())."-01".$hour);//每天最晚打卡时间戳（记录到第二天6点之前）
			}else{
				$maxtime = strtotime($check_content.$maxday.$hour);//每天最晚打卡时间戳（记录到第二天6点之前）
			}
			//echo $mintime."           ".$maxtime;
			//echo "<br>";
			//echo $check_content.$day;
			$time_str = strtotime(date("Y-m-d 15:00:00",$mintime));
			$time_str_sss = strtotime(date("Y-m-d 15:00:00",$mintime));
			$wheres = "user_basic.rule_id!='0' and user_basic.status!=0 and user_basic.id!=1 and (checking.atten_time_str between '$mintime' and '$maxtime')";
			$user_data = $user_basic->join('checking ON user_basic.atten_uid = checking.atten_uid')->field('user_basic.user_id,checking.atten_uid,user_basic.name,user_basic.campus,user_basic.post,user_basic.rule_id,MAX(checking.atten_time_str) as max_time,MIN(checking.atten_time_str) as min_time,checking.atten_date')->where($wheres)->group("checking.name")->select();
			foreach($user_data as &$user_data_val){
				if($user_data_val['min_time']<$maxtime && $user_data_val['min_time']>=$time_str){
					$user_data_val['atten_date'] = date("Y-m-d",$mintime);
					if(date("H",$user_data_val['min_time']) == date("H",$user_data_val['max_time'])){
						$user_data_val['min_time'] = "";
						$user_data_val['check_content'] = "上班未打卡";
					}
				}
				if($user_data_val['max_time']>$mintime && $user_data_val['max_time']<=$time_str_sss){
					if(date("H",$user_data_val['min_time']) == date("H",$user_data_val['max_time'])){
						$user_data_val['max_time'] = "";
						$user_data_val['check_content'] = "下班未打卡";
					}
				}
			}
			 
			$check_arr[$i] = $user_data;
			//echo $user_basic->getLastsql();
			//echo "<br><pre>";
			//print_r($user_data);
		
		}
		//echo "<pre>";
		//print_r($check_arr);die;
		$user_rule = M('user_rule');
		$check_record = D('check_record');
		$rule_arr = $user_rule->select();
		//如果有值则添加考勤记录合格表
		if(!empty($check_arr)){
			foreach($check_arr as $check_key => &$check_val){
				foreach($check_val as $keys => &$value){
					foreach($value as $key => &$val){
						if($key == "max_time"){
							$value['max_day'] = Intval(date("d",$val));
							$value['max_hour'] = Intval(date("H",$val));
							$value['max_minute'] = Intval(date("i",$val));
							$value['max_times'] = date("Y-m-d H:i:s",$val);

						}elseif($key == "min_time"){
							$value['min_day'] = Intval(date("d",$val));
							$value['min_hour'] = Intval(date("H",$val));
							$value['min_minute'] = Intval(date("i",$val));
							$value['min_times'] = date("Y-m-d H:i:s",$val);
						}
						if($key == 'rule_id'){
							$rule_id_arr = explode(',',$val);
							$rules_arr = array();
							foreach($rule_id_arr as $id_val){
								$rules_arr[] = $rule_arr[$id_val-1];
							}
							$value['rule_arr'] = $rules_arr;
						}
					}

					
				}
				//循环添加考勤是否合格
				foreach($check_val as $keys=>&$value){
					foreach($value as $key=>&$val){
						if($key == "rule_arr"){
							foreach($val as $rules_arr_val){
								/*print_r($rules_arr_val);*/
								if($value['max_times']!="0" && $value['min_times']!="0" && ($value['min_day'] < $value['max_day'] || ($value['max_hour'] > $rules_arr_val['rule_maxhour'] || ($value['max_hour'] == $rules_arr_val['rule_maxhour'] && $value['max_minute']>=$rules_arr_val['rule_maxminute']))) && ($value['min_hour'] < $rules_arr_val['rule_minhour'] || ($value['min_hour'] == $rules_arr_val['rule_minhour'] && $value['min_minute']<=$rules_arr_val['rule_minminute']))){
									$value['check'] = "合格";
									$value['check_content'] = "";
									continue;
								}elseif($value['max_times']!="0" && $value['min_times']!="0" && ($value['min_day'] < $value['max_day'] || ($value['max_hour'] < $rules_arr_val['rule_maxhour'] || ($value['max_hour'] == $rules_arr_val['rule_maxhour'] && $value['max_minute']<$rules_arr_val['rule_maxminute'])))){
									if($value['max_hour'] < $rules_arr_val['rule_maxhour'] && $value['max_minute']<$rules_arr_val['rule_maxminute']){
										$value['check'] = "不合格";
										$value['check_content'] = "旷工，原因：打卡时间与规定时间早一个小时以上。";
										continue;
									}else{
										$value['check'] = "不合格";
										$value['check_content'] = "早退";
									}
								}elseif($value['max_times']!="0" && $value['min_times']!="0" && ($value['min_day'] < $value['max_day'] || ($value['max_hour'] > $rules_arr_val['rule_maxhour'] || ($value['max_hour'] == $rules_arr_val['rule_maxhour'] && $value['max_minute']>=$rules_arr_val['rule_maxminute']))) && ($value['min_hour'] > $rules_arr_val['rule_minhour'] || ($value['min_hour'] == $rules_arr_val['rule_minhour'] && $value['min_minute']>$rules_arr_val['rule_minminute']))){
									if($value['min_hour'] > $rules_arr_val['rule_minhour'] && $value['min_minute']>$rules_arr_val['rule_minminute']){
										$value['check'] = "不合格";
										$value['check_content'] = "旷工，原因：打卡时间与规定时间晚一个小时以上。";
										continue;
									}else{
										$value['check'] = "不合格";
										$value['check_content'] = "迟到";
									}
								}else{
									$value['check'] = "不合格";
								}
								
							}

						}
					}
					$check_data['user_id'] = $value['user_id'];
					$check_data['atten_uid'] = $value['atten_uid'];
					$check_data['check_date'] = $value['atten_date'];
					$check_data['check_name'] = $value['name'];
					$check_data['check_maxtime'] = $value['max_times'];
					$check_data['check_mintime'] = $value['min_times'];
					$check_data['check'] = $value['check'];
					$check_data['check_content'] = $value['check_content'];
					//print_r($check_data);
					$res = $check_record->add($check_data);
					if(!$res){
						$this->error("程序出错！");exit;
					}
				}
			}
			//print_r($check_arr);die;

			$users = M('users'); 

			$leaveing = M('leaveing'); 

			$data_arr=$users->join('user_basic ON users.id = user_basic.user_id')->field('users.id,user_basic.user_id,user_basic.atten_uid,user,name,sex,campus,post,user_basic.entry_date')->select();

			$leave_where = " and time_date like '%$check_content%'";

			$all_noclock_data = $leaveing->query("SELECT COUNT(*) as count,atten_uid from leaveing where class='意外事项' and state = '审核通过' and info like '上班未打卡，下班未打卡%'".$leave_where." GROUP BY name");
			//print_r($all_noclock_data);die;
			//下班未打卡的申请
			$max_noclock_data = $leaveing->query("SELECT atten_uid,time_date,info from leaveing where class='意外事项' and state = '审核通过' and  info like '下班未打卡%'".$leave_where);
			
			//上班未打卡的申请
			$min_noclock_data = $leaveing->query("SELECT atten_uid,time_date,info from leaveing where class='意外事项' and state = '审核通过' and id NOT IN(select id from leaveing where class='意外事项' and info like '上班未打卡，下班未打卡%') and info like '上班未打卡%'".$leave_where);

			$agile_data = $leaveing->query("SELECT atten_uid,time_date,info from leaveing where class='灵活作息' and state = '审核通过'".$leave_where);

			$check_record = M('check_record');
		
			foreach($data_arr as &$val){

				foreach($min_noclock_data as $min_noclock_val){
					if($val['atten_uid'] == $min_noclock_val['atten_uid']){
						$check_record->where("atten_uid='".$min_noclock_val['atten_uid']."' and check_date='".$min_noclock_val['time_date']."' and check_mintime='0'")->save(array("check"=>"合格","check_content"=>"由于意外事项申请，".$min_noclock_val['info']));
					}
				}
				foreach($max_noclock_data as $max_noclock_val){
					if($val['atten_uid'] == $max_noclock_val['atten_uid']){
						$check_record->where("atten_uid='".$max_noclock_val['atten_uid']."' and check_date='".$max_noclock_val['time_date']."' and check_maxtime='0'")->save(array("check"=>"合格","check_content"=>"由于意外事项申请，".$max_noclock_val['info']));
					}
				}


				foreach($agile_data as $agile_val){
					if($val['atten_uid'] == $agile_val['atten_uid']){
						$check_record->where("atten_uid='".$agile_val['atten_uid']."' and check_date='".$agile_val['time_date']."'")->save(array("check"=>"合格","check_content"=>"由于灵活作息申请，".$agile_val['info']));
					}
				}
			}

		}

	}





	//定时小程序，每到月一号计算上个月考勤数据导入
	function checksecpro(){
		die;
		// 创建一个新cURL资源
		$ch = curl_init();
		$time = time();
		$start = date("Y-m-d",strtotime("-1 day"));
		$end = date("Y-m-d",strtotime("-1 day"));
		//公共必传参数
		$data = array(
		'account'=>'21c4a357f585a1a50ea794fcf96fad73',//API帐号
		'requesttime'=>$time,//请求时间，与服务器时间差不能超过60秒
		);
		//接口参数
		$data['start'] = $start;
		$data['end'] = $end;
		$data['page'] = '1';
		//按key排序
		ksort($data);
		//生成签名，先转成utf-8编码再生成签名
		$sign = md5(join('',$data).'hongwenhr001');
		$data['sign'] = $sign;
		// 设置URL和相应的选项，请通过GET方式传参，以下是获取考勤记录接口
		curl_setopt($ch, CURLOPT_URL, "http://kq.qycn.com/index.php/Api/Api/recordlog?".http_build_query($data));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 抓取URL并把它传递给浏览器
		$retjson = curl_exec($ch); //返回的数据，json格式
		$resarr = json_decode($retjson,true);
		//echo "<pre>";
		//print_r($resarr);
		//echo $retjson;
		//die;
		// 关闭cURL资源，并且释放系统资源
		if($resarr['status'] == '1'){
			for($i=1; $i<=$resarr['data']['totalpage']; $i++){
				$checking = M('checking');
				$chs = curl_init();
				//公共必传参数
				$data = array(
				'account'=>'21c4a357f585a1a50ea794fcf96fad73',//API帐号
				'requesttime'=>$time,//请求时间，与服务器时间差不能超过60秒
				);
				//接口参数
				$data['start'] = $start;
				$data['end'] = $end;
				$data['page'] = $i;
				//按key排序
				ksort($data);
				//生成签名，先转成utf-8编码再生成签名
				$sign = md5(join('',$data).'hongwenhr001');
				$data['sign'] = $sign;
				// 设置URL和相应的选项，请通过GET方式传参，以下是获取考勤记录接口
				curl_setopt($chs, CURLOPT_URL, "http://kq.qycn.com/index.php/Api/Api/recordlog?".http_build_query($data));
				curl_setopt($chs, CURLOPT_HEADER, 0);
				curl_setopt($chs, CURLOPT_RETURNTRANSFER, 1);
				// 抓取URL并把它传递给浏览器
				$res_json = curl_exec($chs); //返回的数据，json格式
				$res_arr = json_decode($res_json,true);
				$checking_arr = $res_arr['data']['attendata'];
				$arr_data = array();
				foreach ($checking_arr as $value) {
					$arr_data['atten_uid'] = $value['atten_uid'];
					$arr_data['atten_device'] = $value['atten_device'];
					$arr_data['atten_time_str'] = $value['atten_time'];
					$arr_data['atten_time'] = date("Y-m-d H:i:s",$value['atten_time']);
					$arr_data['atten_date'] = $value['atten_date'];
					$arr_data['name'] = $value['realname'];
					$arr_data['departname'] = $value['departname'];
					$arr_data['remark'] = $value['remark'];
					$reques = $checking->add($arr_data);
				}
				echo 1;
				curl_close($chs);
			}
		}else{
			$this->error('程序错误！');
		}
		curl_close($ch);
		
	}
}

?>