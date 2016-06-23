<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:员工模板控制器
*/
namespace Home\Controller;
use Think\Controller;
header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
header("Content-type:text/html;charset=utf-8");
header('Access-Control-Allow-Origin: *');
require_once( "/Public/public.php" );
class StaffController extends CommonController {
	//excel生成函数
	public function staff_excel(){
		if(empty($_SESSION['select_content'])){
			$this->error("生成页面错误，请联系管理员！");exit;
		}
		$content_arr = array();
		$content_arr = $_SESSION['select_content'];
		import("Vendor.PHPExcel");
		//创建对象
		$excel = new \PHPExcel();
		//Excel表格式,这里简略写了8列
		$letter = array('A','B','C','D','E','F','F','G');
		//表头数组
		$tableheader = array('序号','员工号','姓名','入职时间','单位部门','职务','联系电话','QQ');
		//填充表头信息
		for($i = 0;$i < count($tableheader);$i++) {
			$excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
		}
		//表格数组
		foreach($content_arr as $key => $val){
			$data[$key][0] = ($key+1);
			$data[$key][1] = $val["user"];
			$data[$key][2] = $val["name"];
			$data[$key][3] = $val["entry_date"];
			$data[$key][4] = $val["campus"];
			$data[$key][5] = $val["post"];
			$data[$key][6] = $val["telephone"];
			$data[$key][7] = $val["qq"];
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


	//excel生成函数
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
		$letter = array('A','B','C','D','E','F','F','G','H','I','J','K','L','M','N','O','P','Q');
		//表头数组
		$tableheader = array('序号','姓名','单位部门','职务','讲师级别','绩效系数','元老补助','迟到','早退','事假','病假','加班','旷工','未打卡','应出勤天数','核算出勤天数','是否满勤');
		//填充表头信息
		for($i = 0;$i < count($tableheader);$i++) {
			$excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
		}
		//表格数组
		foreach($content_arr as $key => $val){
			$data[$key][0] = ($key+1);
			$data[$key][1] = $val["name"];
			$data[$key][2] = $val["campus"];
			$data[$key][3] = $val["post"];
			$data[$key][4] = $val["level"];
			$data[$key][5] = $val["factor"];
			$data[$key][6] = $val["allowance"];
			$data[$key][7] = $val["late"];
			$data[$key][8] = $val["early"];
			$data[$key][9] = $val["personal_leave"];
			$data[$key][10] = $val["sick_leave"];
			$data[$key][11] = $val["overtime"];
			$data[$key][12] = $val["absenteeism"];
			$data[$key][13] = $val["noclock"];
			$data[$key][14] = $val["over_count_day"];
			$data[$key][15] = $val["count_yes"];
			$data[$key][16] = $val["is_no"];
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
		header('Content-Disposition:attachment;filename="鸿文员工考勤表.xls"');
		header("Content-Transfer-Encoding:binary");
		$write->save('php://output');
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
					<th>入职时间</th>
					<th>单位部门</th>
					<th>职务</th>
					<th>联系电话</th>
					<th>QQ</th></tr>";
			foreach($_SESSION['select_content'] as $key => $val){
				$str .= "<tr class='print_tr'>
				<td class='td_num'>" . ($key+1) . "</td>
				<td class='td_user'>" . $val['user'] . "</td>
				<td class='td_name'>" . $val['name'] . "</td>
				<td class='td_date'>" . $val['entry_date'] . "</td>
				<td class='td_campus'>" . $val['campus'] . "</td>
				<td class='td_post'>" . $val['post'] . "</td>
				<td class='td_telephone'>" . $val['telephone'] . "</td>
				<td class='td_qq'>" . $val['qq'] . "</td>
				</tr>";
				if(($key+1)%30==0){
					$str .= "</table><div class='PageNext'></div><br><br><table border=1 class='tb' cellpadding='2' cellspacing='0' id='tb'><tr>
							<th>序号</th>
							<th>员工号</th>
							<th>姓名</th>
							<th>入职时间</th>
							<th>单位部门</th>
							<th>职务</th>
							<th>联系电话</th>
							<th>QQ</th></tr>";
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
			$user_arr = $users->where(array("user"=>$_POST['user']))->find();
			$basic_arr = $user_basic->where(array("user_id" => $user_arr['id']))->find();
			$data["basic"] = $basic_arr;
			$data["basic"]['user'] = $user_arr['user'];
			$data["status"] = 1;
			echo json_encode($data);
		}else{
			$data["status"] = 2;
			echo json_encode($data);
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