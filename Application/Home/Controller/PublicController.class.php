<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:定时程序
*/
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
class PublicController extends Controller {

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

	//计算每个月有多少个周？参数$b是最早日期，$e最晚日期，$num是周几
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


	//通过日期时间戳来判断周几
	function weekday($time){
		$weekday = array('7','1','2','3','4','5','6');
		return $weekday[date('w', $time)];
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







	//定时小程序，每到月一号计算上个月考勤数据导入,顺序执行  1
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




	//定时小程序，每到月一号计算上个月请假记录,顺序执行  2
	function leave_pro(){
		die;
		//下面是获取上一月的数据
		$data = $this->year_month_day();
		$now_time = $data['now_time'];
		$year_day = $data['year_day'];//哪一年
		$month_day = $data['month_day'];//哪个月份
		$check_content = $data['check_content'];//查询条件，格式：“2016-05-”
		$day_count = $data['day_count'];//每个月一共多少天

		$url = "http://i.ihongwen.com/oa/weixin.php/weixin/getWeixinAskInfo?time=".$check_content;
		//".$check_content."01&time2=".$check_content.$day_count;2016-06-01&time2=2016-06-28";//
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



	//定时小程序，每到月一号计算上个月考勤记录表数据处理添加,顺序执行  3
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
		$hour = " 04:00:00";
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
			$mintime = strtotime($check_content.$minday.$hour);//每天最早打卡时间戳（记录在第二天4点之后）
			if($i==$day_count){
				$maxtime = strtotime(date("Y-m",time())."-01".$hour);//每天最晚打卡时间戳（记录到第二天4点之前）
			}else{
				$maxtime = strtotime($check_content.$maxday.$hour);//每天最晚打卡时间戳（记录到第二天4点之前）
			}
			//echo $mintime."           ".$maxtime;
			//echo "<br>";
			//echo $check_content.$day;
			$time_str = strtotime(date("Y-m-d 15:00:00",$mintime));
			$time_str_sss = strtotime(date("Y-m-d 15:00:00",$mintime));
			$where_data = "rule_id!='0' and rule_id!='' and status!=0 and id!=1";
			$wheres = "user_basic.rule_id!='0' and user_basic.rule_id!='' and user_basic.status!=0 and user_basic.id!=1 and (checking.atten_time_str between '$mintime' and '$maxtime')";
			$user_data_no = $user_basic->query("select user_id,atten_uid,name,campus,post,rule_id,week from user_basic where ".$where_data." and id not in (SELECT user_basic.id FROM `user_basic` INNER JOIN checking ON user_basic.atten_uid = checking.atten_uid WHERE ( ".$wheres." ) ) GROUP BY name");
			foreach($user_data_no as &$user_data_val){
				$user_data_val['min_times'] = 0;
				$user_data_val['max_times'] = 0;
				$user_data_val['atten_date'] = date("Y-m-d",$mintime);
				$user_data_val['check'] = '不合格';
				$user_data_val['check_content'] = '旷工，因为无打卡记录所以记为无故旷工';
			}
			$user_data = $user_basic->join('checking ON user_basic.atten_uid = checking.atten_uid')->field('user_basic.user_id,checking.atten_uid,user_basic.name,user_basic.campus,user_basic.post,user_basic.rule_id,MAX(checking.atten_time_str) as max_time,MIN(checking.atten_time_str) as min_time,checking.atten_date,user_basic.week')->where($wheres)->group("checking.name")->select();
			//echo $user_basic->getLastsql();die;
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
			$check_data_data = array_merge($user_data_no,$user_data);
			$check_arr[$i] = $check_data_data;
			//echo $user_basic->getLastsql();
			//echo "<br><pre>";
			//print_r($user_data);
		
		}
		//echo $this->weekday('1459517797');
		//echo "<pre>";
		//print_r($check_arr);die;
		$user_rule = M('user_rule');
		$check_record = D('check_record');
		$rule_arr = $user_rule->select();
		$check_rules_name = D('check_rules_name');
		$rules_week_arr = $check_rules_name->join("check_rules_week ON check_rules_name.id = check_rules_week.rule_name_id")->field('check_rules_name.id,check_rules_name.rules_name,check_rules_name.post_id,check_rules_name.campus_id,check_rules_name.level,check_rules_week.week_num')->select();
		$campus_class_post = D('campus_class_post');
		$campus_post_arr = $campus_class_post->select();

		$rules_week_str = $check_rules_name->join("campus_class_post ON check_rules_name.campus_id = campus_class_post.id")->where("campus_id!=''")->group("campus_id")->select();

		foreach ($rules_week_str as $key => $value) {
			$week_campus_str .= $value['class'];
		}

		foreach ($rules_week_arr as $key => &$value) {
			$value['week_num'] = explode(",",$value['week_num']);
			foreach($campus_post_arr as $campus_post_val){
				if($campus_post_val['id'] == $value['post_id']){
					$value['post_name'] = $campus_post_val['class'];
				}else if($campus_post_val['id'] == $value['campus_id']){
					$value['campus_name'] = $campus_post_val['class'];
				}
			}
		}
		// echo $week_campus_str;
		//print_r($rules_week_str);
		// die;
		
		//如果有值则添加考勤记录合格表
		if(!empty($check_arr)){
			foreach($check_arr as $check_key => &$check_val){
				foreach($check_val as $keys => &$value){
					if(empty($value['check'])){
						foreach($value as $key => &$val){
							if($key == "max_time"){
								$value['max_month'] = Intval(date("m",$val));
								$value['max_day'] = Intval(date("d",$val));
								$value['max_hour'] = Intval(date("H",$val));
								$value['max_minute'] = Intval(date("i",$val));
								$value['max_times'] = date("Y-m-d H:i:s",$val);

							}elseif($key == "min_time"){
								$value['min_month'] = Intval(date("m",$val));
								$value['min_day'] = Intval(date("d",$val));
								$value['min_hour'] = Intval(date("H",$val));
								$value['min_minute'] = Intval(date("i",$val));
								$value['min_times'] = date("Y-m-d H:i:s",$val);
							}
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
								$value['rule_time'] = $rules_arr;
							}
						}
					}

					
				}
				//循环添加考勤是否合格
				//print_r($check_val);
				foreach($check_val as $keys=>&$value){
					$check_data = array();
					if(empty($value['check'])){
						foreach($value['rule_time'] as $rules_arr_val){
							//print_r($rules_arr_val);
							if($value['max_times']!="0" && $value['min_times']!="0" && $value['min_month'] < $value['max_month']){
								$value['check'] = "合格";
								$value['check_content'] = "";
								continue;
							}else if($value['max_times']!="0" && $value['min_times']!="0" && ($value['min_day'] < $value['max_day'] || ($value['max_hour'] > $rules_arr_val['rule_maxhour'] || ($value['max_hour'] == $rules_arr_val['rule_maxhour'] && $value['max_minute']>=$rules_arr_val['rule_maxminute']))) && ($value['min_hour'] < $rules_arr_val['rule_minhour'] || ($value['min_hour'] == $rules_arr_val['rule_minhour'] && $value['min_minute']<=$rules_arr_val['rule_minminute']))){
								$value['check'] = "合格";
								$value['check_content'] = "";
								continue;
							}else if($value['max_times']!="0" && $value['min_times']!="0" && empty($value['check_content']) && (($value['max_hour'] > $rules_arr_val['rule_maxhour'] || ($value['max_hour'] == $rules_arr_val['rule_maxhour'] || $value['min_day'] < $value['max_day'] && $value['max_minute']>=$rules_arr_val['rule_maxminute']))) && ($value['min_hour'] > $rules_arr_val['rule_minhour'] || ($value['min_hour'] == $rules_arr_val['rule_minhour'] && $value['min_minute']>$rules_arr_val['rule_minminute']))){
								if($value['check']!='合格'){
									if($value['min_hour'] > $rules_arr_val['rule_minhour'] && $value['min_minute'] > $rules_arr_val['rule_minminute'] && $value['min_day'] == $value['max_day']){
										$value['check'] = "不合格";
										$value['check_content'] = "旷工，原因：打卡时间与规定时间晚一个小时以上。";
										continue;
									}else{
										$value['check'] = "不合格";
										$value['check_content'] = "迟到";
										continue;
									}
								}else{
									$value['check'] = "合格";
									$value['check_content'] = "";
									continue;
								}
							}else if($value['max_times']!="0" && $value['min_times']!="0" && empty($value['check_content']) && $value['min_day'] == $value['max_day'] &&(($value['max_hour'] < $rules_arr_val['rule_maxhour'] || ($value['max_hour'] == $rules_arr_val['rule_maxhour'] && $value['max_minute']<$rules_arr_val['rule_maxminute'])))){
								if($value['check']!='合格'){
									if(($rules_arr_val['rule_maxhour']-$value['max_hour'] == 1 && $value['max_minute'] < $rules_arr_val['rule_maxminute']) || $rules_arr_val['rule_maxhour']-$value['max_hour'] > 1 && $value['min_day'] == $value['max_day']){
										$value['check'] = "不合格";
										$value['check_content'] = "旷工，原因：打卡时间与规定时间早一个小时以上。";
										continue;
									}else{
										$value['check'] = "不合格";
										$value['check_content'] = "早退";
										continue;
									}
								}else{
									$value['check'] = "合格";
									$value['check_content'] = "";
									continue;
								}
							}else{
								$value['check'] = '不合格';
								continue;
							}
							
						}
					}

					$week_day_day = $this->weekday(strtotime($value['atten_date']));
					if($value['week'] != '' || $value['week'] != null){
						$week_arr = explode(",",$value['week']);
						foreach($week_arr as $week_arr_val){
							if($week_arr_val == $week_day_day){
								$value['min_times'] = 0;
								$value['max_times'] = 0;
								$value['check'] = '休息';
								$value['check_content'] = '正常休息';
							}
						}
					}else{
						foreach($rules_week_arr as $rules_week_val){
							if(strstr($week_campus_str,$value['campus'])){
								if($rules_week_val['level'] == '2' && strstr($value['post'],$rules_week_val['post_name']) && $value['campus'] == $rules_week_val['campus_name']){
									foreach($rules_week_val['week_num'] as $week_arr_val){
										if($week_arr_val == $week_day_day){
											$value['min_times'] = 0;
											$value['max_times'] = 0;
											$value['check'] = '休息';
											$value['check_content'] = '正常休息';
										}
									}
								}
							}else{
								if($rules_week_val['level'] == '1' && $value['post'] == $rules_week_val['post_name']){
									foreach($rules_week_val['week_num'] as $week_arr_val){
										if($week_arr_val == $week_day_day){
											$value['min_times'] = 0;
											$value['max_times'] = 0;
											$value['check'] = '休息';
											$value['check_content'] = '正常休息';
										}
									}
								}
							}
						}
					}

					// if($week_day_day == '1' && strstr($value['post'],"讲师")){
					// 	$value['min_times'] = 0;
					// 	$value['max_times'] = 0;
					// 	$value['check'] = '休息';
					// 	$value['check_content'] = '休息，周一休息';
					// }
					// if($week_day_day == '2' && strstr($value['post'],"讲师")){
					// 	$value['min_times'] = 0;
					// 	$value['max_times'] = 0;
					// 	$value['check'] = '休息';
					// 	$value['check_content'] = '休息，周二休息';
					// }
					$check_data['user_id'] = $value['user_id'];
					$check_data['atten_uid'] = $value['atten_uid'];
					$check_data['check_date'] = $value['atten_date'];
					$check_data['check_name'] = $value['name'];
					$check_data['check_maxtime'] = $value['max_times'];
					$check_data['check_mintime'] = $value['min_times'];
					$check_data['check'] = $value['check'];
					$check_data['check_content'] = $value['check_content'];
					//echo "<pre>";
					//print_r($check_data);exit;
					$res = $check_record->add($check_data);
					if(!$res){
						$this->error("程序出错！");exit;
					}
				}
			}

			//echo "<pre>";
			//print_r($check_arr);die;
			//print_r($check_arr);
			//die;

			$users = M('users'); 

			$leaveing = M('leaveing'); 

			$leave_where = " and time_date like '%$check_content%'";

			$leave_data = $leaveing->query("select * from leaveing where ((class='意外事项' and (info like '上班未打卡%'  or  info like '下班未打卡%')) or  class='灵活作息' or  class='请假') and state = '审核通过' ".$leave_where." ORDER BY class");
			$check_record = M('check_record');

			$time_num = 7.5;

			foreach($leave_data as $leave_value){
				if($leave_value['class'] == '灵活作息'){
					//处理灵活作息情况
					$check_arr_arr = $check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$leave_value['time_date']."'")->find();
					$check_maxtime = strtotime($check_arr_arr['check_maxtime']);
					$check_mintime = strtotime($check_arr_arr['check_mintime']);
					if(($check_maxtime-$check_mintime) >= (floatval($time_num)*3600)){
						$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$leave_value['time_date']."'")->save(array("check"=>"合格","check_content"=>"由于灵活作息申请，".$leave_value['info']));
					}else{
						$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$leave_value['time_date']."'")->save(array("check"=>"不合格","check_content"=>"灵活打卡异常，虽然灵活作息申请，但是打卡时间不满".$time_num."个小时"));
					}
				} else if($leave_value['class'] == '请假'){
					//处理请假情况
					$time_dates_num = strtotime($leave_value['time_begin']);
					$time_months_num = date("Y-m-",$time_dates_num);
					$time_days_num =  date("d",$time_dates_num);
					$dates_num = date("Y-m-d",$time_dates_num);
					$days_num = $leave_value['count_day1'];
					//当存在0.5天请假时
					if(is_float($days_num) && floatval($days_num)==floatval(0.5)){
						$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$dates_num."'")->save(array("check"=>$leave_value['property'],"check_content"=>$leave_value['property']."半天，".$leave_value['info']));
					} else if(is_float($days_num) && floatval($days_num)!=floatval(0.5)){
						//当超过一天并且是有半天情况
						$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$dates_num."'")->save(array("check"=>$leave_value['property'],"check_content"=>$leave_value['property']."半天，".$leave_value['info']));
						$num = Intval($days_num)-0.5;
						for($i=0;$i<$num;$i++){
							$time_day_nums = Intval($time_days_num)+$i;
							if($time_day_nums<10){
								$time_day_nums = "0".$time_day_nums;
							}
							$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$time_months_num.$time_day_nums."'")->save(array("check"=>$leave_value['property'],"check_content"=>$leave_value['property']."全天，".$leave_value['info']));
						}
					} else {
						//当超过一天并没有半天情况
						for($i=0;$i<$days_num;$i++){
							$time_day_nums = Intval($time_days_num)+$i;
							if($time_day_nums<10){
								$time_day_nums = "0".$time_day_nums;
							}
							$check_self = $check_record->where(array("check_date"=>$time_months_num.$time_day_nums,"atten_uid"=>$leave_value['atten_uid']))->find();
							if(!$check_self || ($check_self['check_mintime']==0 && $check_self['check_maxtime']==0) || ($check_self['check_mintime']=='' and $check_self['check_maxtime']=='')){
								
								$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$time_months_num.$time_day_nums."'")->save(array("check"=>$leave_value['property'],"check_content"=>$leave_value['property']."全天，".$leave_value['info']));
							}
						}
						
					}
				} else if($leave_value['class'] == '意外事项' && strstr($leave_value['info'],"下班未打卡")){
					//处理意外事项下班未打卡情况
					$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$leave_value['time_date']."'")->save(array("check"=>"合格","check_content"=>"由于意外事项申请，".$leave_value['info']));
				} else if($leave_value['class'] == '意外事项' && strstr($leave_value['info'],"上班未打卡，下班未打卡")){
					//处理意外事项上下班未打卡情况
					$check_self = $check_record->where(array("check_date"=>$leave_value['time_date'],"atten_uid"=>$leave_value['atten_uid']))->find();
					if(!$check_self){
						$user_arr = $users->where(array('user' => $leave_value['atten_uid']))->find();
						$check_datas = array();
						$check_datas['user_id'] = $user_arr['id'];
						$check_datas['atten_uid'] = $leave_value['atten_uid'];
						$check_datas['check_date'] = $leave_value['time_date'];
						$check_datas['check_name'] = $leave_value['name'];
						$check_datas['check_maxtime'] = 0;
						$check_datas['check_mintime'] = 0;
						$check_datas['check'] = "合格";
						$check_datas['check_content'] = "由于意外事项申请，".$leave_value['info'];

						$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$leave_value['time_date']."'")->save($check_datas);
					}
				} else {
					//处理意外事项上班未打卡事项
					$check_record->where("atten_uid='".$leave_value['atten_uid']."' and check_date='".$leave_value['time_date']."'")->save(array("check"=>"合格","check_content"=>"由于意外事项申请，".$leave_value['info']));
				}
			}

		}
		echo 1;

	}


	//导入oa人员信息
	function check_ycs(){
		die;
		$url = "http://i.ihongwen.com/oa_old/weixin.php/userService/listAllUserRecords?time1=2016-05-01&time2=2016-07-26";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$file_contents = curl_exec ( $ch );
		$data = json_decode($file_contents,true);
		$user_basic = D('user_basic');
		$users = D('users');
		$num = 684;
		foreach($data as &$val){

			$results = $users->add(array("id"=>$num,'user'=>$val['atten_uid']));
			if(!$results){
				$this->error("程序出错！");exit;
			}
			$val['user_id'] = $num;
			$results = $user_basic->add($val);
			if(!$results){
				$this->error("程序出错！");exit;
			}
			$num++;
		}
		echo 1;
		curl_close ( $ch );
			
	}
}
?>