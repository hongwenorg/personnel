<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/7/27
*@Description:人员业绩统计
*/
namespace Home\Controller;
use Think\Controller;
header('Access-Control-Allow-Origin: *');
header('P3P: CP="ALL ADM DEV PSAi COM OUR OTRo STP IND ONL"');
header("Content-type:text/html;charset=utf-8");
class PersonalCountController extends Controller {
    //获取json文件数据
    function Json_file_get(){
        $json_str = file_get_contents("Public/json/BaseDataType.json");
        echo $json_str;
    }

    //存入json文件数据
    function Json_file_add(){
        if(empty($_GET['json_str'])){
            echo json_encode(array('status' => false , 'content' => '数据不存在'));exit;//数据不存在
        }
        $content = $_GET['json_str'];
        $file1 = 'Public/json/BaseDataType.json';
        $fp = fopen($file1, 'w');
        fwrite($fp, $content);
        fclose($fp);
        echo json_encode(array('status' => true , 'content' => '保存成功'));exit;//数据不存在
    }

    //默认显示业绩目标信息
    function Campus_target_find(){
        if(!empty($_GET['date'])){
            $date = $_GET['date'];
        }else{
            $date = date('Y-m',time());
        }
        $model1 = D('oa_campustarget');
        $model2 = D('oa_personaltarget');
        $model3 = D('person_all');
        $model4 = D('oa_foo_info');
        $model5 = D('oa_position');
        $data = array();
        $data2 = array();
        if(empty($_GET['school_id'])){
            $data_arr = $model1->where(array('date'=>$date))->select();
            if($data_arr){
                $data = array();
                foreach($data_arr as $k => $val){
                    $school_id = $val['campus_id'];
                    $campus_arr = $model4->where(array('id'=>$school_id))->find();
                    $data_arr2 = $model2->where(array('date' => $date , 'campus_id' => $school_id))->select();
                    $data2 = array();
                    if($data_arr2){
                        foreach ($data_arr2 as $key => $value) {
                            $user_arr = $model3->where(array('id' => $value['user_id']))->find();
                            $post_arr = $model5->where(array('id' => $value['post_id']))->find();
                            $data2[$key]['id'] = $value['id'];
                            $data2[$key]['name'] = $user_arr['name'];
                            $data2[$key]['post'] = $post_arr['name'];
                            $data2[$key]['post_id'] = $post_arr['id'];
                            $data2[$key]['level'] = $value['level'];
                            $data2[$key]['target'] = $value['target'];
                            $data2[$key]['upgrade'] = $value['upgrade'];
                            $data2[$key]['relegation'] = $value['relegation'];
                        }
                    }else{
                        $data_arr2 = array();
                    }
                    $data[$k]['id'] = $val['id'];
                    $data[$k]['school_id'] = $val['campus_id'];
                    $data[$k]['school_name'] = $campus_arr['name'];
                    $data[$k]['new_target'] = $val['new_target'];
                    $data[$k]['old_target'] = $val['old_target'];
                    $data[$k]['count_target'] = $val['count_target'];
                    $data[$k]['personal_target'] = $data2;
                }
            }else{
                $data = array();
            }
        }else{
            $data_arr = $model1->where(array('date' => $date , 'campus_id' => $_GET['school_id']))->find();
            if($data_arr){
                $data = array();
                $school_id = $data_arr['campus_id'];
                $campus_arr = $model4->where(array('id'=>$school_id))->find();
                $data_arr2 = $model2->where(array('date' => $date , 'campus_id' => $school_id))->select();
                if($data_arr2){
                    $data2 = array();
                    foreach ($data_arr2 as $key => &$value) {
                        $user_arr = $model3->where(array('id' => $value['user_id']))->find();
                        $post_arr = $model5->where(array('id' => $value['post_id']))->find();
                        $data2[$key]['id'] = $value['id'];
                        $data2[$key]['name'] = $user_arr['name'];
                        $data2[$key]['post'] = $post_arr['name'];
                        $data2[$key]['post_id'] = $post_arr['id'];
                        $data2[$key]['level'] = $value['level'];
                        $data2[$key]['target'] = $value['target'];
                        $data2[$key]['upgrade'] = $value['upgrade'];
                        $data2[$key]['relegation'] = $value['relegation'];
                    }
                }else{
                    $data_arr2 = array();
                }
                $data['id'] = $val['id'];
                $data['school_id'] = $data_arr['campus_id'];
                $data['school_name'] = $campus_arr['name'];
                $data['new_target'] = $data_arr['new_target'];
                $data['old_target'] = $data_arr['old_target'];
                $data['count_target'] = $data_arr['count_target'];
                $data['personal_target'] = $data2;
                
            }else{
                $data = array();
            }
        }
        if(empty($data)){
            $campus_arr = $model4->where(array('pid' => 15 , 'is_del' => 0))->select();
            foreach($campus_arr as $val){
                $dats['campus_id'] = $val['id'];
                $dats['date'] = date("Y-m",time());
                $model1->add($dats);
            }
            $this->Campus_target_find();
        }
        echo json_encode($data);
    }



    //财务系统校区录入业绩目标删除
    function Campus_target_del(){
        if(empty($_GET["id"])){
            echo json_encode(array('status' => false , 'content' => '程序出错请联系管理员'));exit;//程序出错请联系管理员
        }
        $model = D('oa_personaltarget');
        $state = $model->where(array('id'=>$_GET['id']))->delete();
        if($state){
            echo json_encode(array('status' => true , 'content' => '删除成功'));exit;//程序出错请联系管理员
        }else{
            echo json_encode(array('status' => false , 'content' => '删除失败'));exit;//程序出错请联系管理员
        }

    }
    //添加业绩目标信息
    function Campus_target_add(){
        if(empty($_GET['data'])){
            echo json_encode(array('status' => false , 'content' => '数据不存在'));exit;//数据不存在
        }else{
            $data_arr = json_decode($_GET['data'],true);
        }
        if(!is_array($data_arr)){
            echo json_encode(array('status' => false , 'content' => '数据出错'));exit;//数据出错
        }
        //echo json_encode($data_arr);exit;
        $model1 = D('oa_campustarget');
        $model2 = D('oa_personaltarget');
        $model3 = D('person_all');
        $data = array();
        $data2 = array();
        $date = date('Y-m',time());
        $name_str = "";
        $error = "";
        foreach($data_arr as $vals){
            $data = array();
            $school_id = $vals['school_id'];
            $school_name = $vals['school_name'];
            $data['campus_id'] = $vals['school_id'];
            $data['new_target'] = $vals['new_target'];
            $data['old_target'] = $vals['old_target'];
            $data['count_target'] = $vals['count_target'];
            $data['date'] = $date;
            $datas = $model1->where(array('date'=>$date,'campus_id'=>$vals['school_id']))->find();
            if(is_array($datas)){
                $model1->where(array('id'=>$datas['id']))->save($data);
                $tatus = 1;
            }else{
                $tatus = $model1->add($data);
            }
            if(!$tatus){
                echo json_encode(array('status' => false , 'content' => '校区数据存入出错'));exit;//数据出错
            }

            $name_str = "";
            $name = "";
            if(is_array($vals['personal_target'])){
                $target_arr = $vals['personal_target'];
                foreach ($target_arr as $key => &$value) {
                    $name = $value['name'];
                    $arr = $model3->where("name like '%".$name."%' and state = 1 and school like '%".$school_name."%'")->find();//
                    if(is_array($arr)){
                        $data2 = array();
                        $data2['user_id'] = $arr['id'];
                        $data2['campus_id'] = $school_id;
                        $data2['post_id'] = $value['post'];
                        $data2['level'] = $value['level'];
                        $data2['target'] = $value['target'];
                        $data2['upgrade'] = $value['upgrade'];
                        $data2['relegation'] = $value['relegation'];
                        $data2['date'] = $date;
                        $datas = $model2->where(array('date'=>$date,'user_id'=>$arr['id'],'campus_id'=>$school_id))->find();
                        if(is_array($datas)){
                            $model2->where(array('id'=>$datas['id']))->save($data2);
                            $tatus = 1;
                        }else{
                            $tatus = $model2->add($data2);
                        }
                        if(!$tatus){
                            echo json_encode(array('status' => false , 'content' => '人员数据存入出错'));exit;//数据出错
                        }
                        
                    }else{
                        $name_str .= $name.",";
                    }

                }
                if(!empty($name_str)){
                    $error .= $school_name."~".rtrim($name_str,',')." | ";
                }
            }
        }
        if(!empty($error)){
            $error_s = $error."查无此人，请确认名字是否正确";
            echo json_encode(array('status' => false , 'content' => $error_s));exit;//数据出错
        }else{
            echo json_encode(array('status' => true , 'content' => "录入成功"));exit;//数据出错
        }
    }


    //升降级业绩统计表
    function level_count(){
        if(empty($_GET['data'])){
            echo json_encode(array('status' => false , 'content' => '程序出错，请联系管理员'));exit;//数据出错
        }else{
            $array = json_decode($_GET['data'],true);
        }
        // echo json_encode($array);exit;
        $where = array();
        // $array['begin_date'] = '2016-07';
        // $array['end_date'] = '2016-08';
        // $array['post_id'] = 18;
        // $array['name'] = '温加宝';
        // $array['school_id'] = 2;
        if(empty($array['post_id'])){
            echo json_encode(array('status' => false , 'content' => '程序出错，请确认职务'));exit;//数据出错
        }else{
            $oa_position = D('oa_position');
            $post_id = $array['post_id'];
            $post_arr = array();
            $post_arr = $oa_position->where(array('id' => $post_id))->find();
            $where['post_id'] = $post_id;
        }
        if(!empty($array['begin_date']) && !empty($array['end_date'])){
            $begin_date = $array['begin_date'];
            $end_date = $array['end_date'];
            $where['date'] = array('between' , $begin_date.','.$end_date);
        }else{
            echo json_encode(array('status' => false , 'content' => '程序出错，请确认开始、结束时间正确'));exit;//数据出错
        }

        $person_all = D('person_all');
        $oa_foo_info = D('oa_foo_info');
        if(!empty($array['school_id'])){
            $school_id = $array['school_id'];
            $school_arr = array();
            $school_arr = $oa_foo_info->where(array('id' => $school_id))->find();
            $where['campus_id'] = $school_id;
        }

        if(!empty($array['name'])){
            $name = $array['name'];
            $user_arr = array();
            $person_arr = $person_all->where(array('name' => $name , 'position' => $post_arr['name'] , 'school' => $school_arr['name']))->find();//
            if(!$person_arr){
                echo json_encode(array('status' => false , 'content' => '查无此人，请确认校区、职务、名称是否一致'));exit;//数据出错
            }
            $where['user_id'] = $person_arr['id'];
        }
        $oa_personaltarget = D('oa_personaltarget');
        $oa_achievement = D('oa_achievement');
        $target_arr = array();
        $target_arr = $oa_personaltarget->where($where)->order('level desc')->select();
        $date_time_arr = $this->year_month_day(strtotime($end_date));
        $month_count = $date_time_arr['month_day'];
        $day_count = $date_time_arr['day_count'];
        $i = 1;
        if(!$target_arr){
            echo json_encode($target_arr);exit;
        }else{
            foreach($target_arr as &$value){
                $value['num'] = $i;
                $person_arrs = $person_all->where(array('id' => $value['user_id']))->find();
                $value['name'] = $person_arrs['name'];
                $campus_arrs = $oa_foo_info->where(array('id' => $value['campus_id']))->find();
                $value['school_name'] = $campus_arrs['name'];
                $value['upgrade_num'] = $value['upgrade']*$value['target'];
                $value['relegation_num'] = $value['relegation']*$value['target'];
                if($value['post_id'] == "18"){
                    $type = '续签';
                    $oa_achievement_num = $oa_achievement->where(array('checkout_date' => array('between' , $begin_date.'-01,'.$end_date."-$day_count") , 'achievement_type' => $type , 'status' => 2 , 'not_curriculum_type' => "" , 'study_userid' => $value['user_id']))->sum('charge_money');
                    $oa_achievement_num1 = $oa_achievement->where(array('checkout_date' => array('between' , $begin_date.','.$end_date) , 'achievement_type' => '转介绍' , 'status' => 2 , 'not_curriculum_type' => "" , 'study_userid' => $value['user_id']))->sum('charge_money');
                }else{
                    $type = '新签';
                    $oa_achievement_num = $oa_achievement->where(array('checkout_date' => array('between' , $begin_date.'-01,'.$end_date."-$day_count") , 'achievement_type' => $type , 'status' => 2 , 'not_curriculum_type' => "" , 'teaching_userid' => $value['user_id']))->sum('charge_money');
                    $oa_achievement_num1 = $oa_achievement->where(array('checkout_date' => array('between' , $begin_date.','.$end_date) , 'achievement_type' => '转介绍' , 'status' => 2 , 'not_curriculum_type' => "" , 'teaching_userid' => $value['user_id']))->sum('charge_money');
                }
                $money_num = sprintf("%.2f", sprintf("%.2f", $oa_achievement_num)+sprintf("%.2f", ($oa_achievement_num1/2)));

                $value['money_num'] = $money_num;
                if($value['money_num'] >= $value['upgrade_num']){
                    $value['upgrade_difference'] = 0;
                }else{
                    $value['upgrade_difference'] = $value['upgrade_num']-$value['money_num'];
                }
                if($value['money_num'] >= $value['relegation_num']){
                    $value['relegation_difference'] = 0;
                }else{
                    $value['relegation_difference'] = $value['relegation_num']-$value['money_num'];
                }
                $value['upgrade_num_num'] = sprintf("%.2f", $value['money_num']/$value['upgrade_num']);
                $value['relegation_num_num'] = sprintf("%.2f", $value['money_num']/$value['relegation_num']);

                $i++;
            }
            // echo $oa_personaltarget->getLastsql();
            // echo "<pre>";
            // print_r($target_arr);die;
            echo json_encode($target_arr);
        }
    }



    //统计表接口
    function target_count(){
        if(empty($_GET['data'])){
            echo json_encode(array('status' => false , 'content' => '程序出错，请联系管理员'));exit;//数据出错
        }else{
            $array = json_decode($_GET['data'],true);
        }
        if(empty($array['type'])){
            echo json_encode(array('status' => false , 'content' => '程序出错，请选择类型'));exit;//数据出错
        }else{
            $type = $array['type'];
        }
        // $array['date'] = '2016-08';
        // $type = '新签';
        if(empty($array['date'])){
            $date = date('Y-m',time());
            $year_day = Intval(date('Y',time()));
            $month_count = date('m',time());
            $day_count = Intval(date('d',time()));

        }else{
            if($array['date'] == date("Y-m",time())){
                $date = date('Y-m',time());
                $year_day = Intval(date('Y',time()));
                $month_count = date('m',time());
                $day_count = Intval(date('d',time()));
            }else{
                $date = $array['date'];
                $date_time_arr = $this->year_month_day(strtotime($date));
                $month_count = $date_time_arr['month_day'];
                $day_count = $date_time_arr['day_count'];
            }
        }
        $oa_foo_info = D('oa_foo_info');
        $oa_campustarget = D('oa_campustarget');
        $oa_achievement = D('oa_achievement');
        $campus_arr = array();
        $data = array();
        $campus_arr = $oa_foo_info->where(array('pid' => 15 , 'is_del' => 0))->select();
        $campus_arr[] = array('id' => 100000 , 'name' => '集团总业绩');
        $top = array(
            'target' =>  array('name' => $type.'总目标' , 'count' => array()) , 
            'achievement' => array('name' => $type.'总业绩(常规)' , 'count' => array()) , 
            'refund' => array('name' => '常规课程退费' , 'count' => array()) , 
            'complete' => array('name' => $type.'完成率(常规)' , 'count' => array()) , 
            'not_refund' => array('name' => '非常规课程退费' , 'count' => array()) , 
            'special' => array('name' => '特训营业绩' , 'count' => array()) , 
            'cooperation' => array('name' => '合作项目业绩' , 'count' => array()) , 
            'achievement_count' => array('name' => '月'.$type.'总业绩' , 'count' => array()));

        $oa_campustarget_arr = $oa_campustarget->where(array('date' => $date))->select();
        if($oa_campustarget_arr){
            foreach($top as &$value){
                foreach($campus_arr as $k=> $val){
                    $school_name = $val['name'];
                    $value['count'][$k]['school_name'] = $val['name'];
                    if($value['name'] == $type.'总目标'){
                        if($val['id'] == '100000'){
                            $count_num = '';
                            if($type == '新签'){
                                $count_num = $oa_campustarget->where(array('date' => $date))->sum('new_target');
                                $value['count'][$k]['count_num'] = $count_num;
                            }else if($type == '续签'){
                                $count_num = $oa_campustarget->where(array('date' => $date))->sum('old_target');
                                $value['count'][$k]['count_num'] = $count_num;
                            }else{
                                $count_num = $oa_campustarget->where(array('date' => $date))->sum('count_target');
                                $value['count'][$k]['count_num'] = $count_num;
                            }
                        }else{
                            foreach($oa_campustarget_arr as $oa_campustarget_val){
                                $count_num = '';
                                if($oa_campustarget_val['campus_id'] == $val['id']){
                                    if($type == '新签'){
                                        $count_num = $oa_campustarget_val['new_target'];
                                        $value['count'][$k]['count_num'] = $count_num;
                                    }else if($type == '续签'){
                                        $count_num = $oa_campustarget_val['old_target'];
                                        $value['count'][$k]['count_num'] = $count_num;
                                    }else{
                                        $count_num = $oa_campustarget_val['count_target'];
                                        $value['count'][$k]['count_num'] = $count_num;
                                    }
                                    
                                }
                            }
                        }
                    }else if($value['name'] == $type.'总业绩(常规)'){
                        if($val['id'] == '100000'){
                            $oa_achievement_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'achievement_type' => $type , 'status' => 2 , 'not_curriculum_type' => ""))->sum('charge_money');
                            $oa_achievement_num1 = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'achievement_type' => '转介绍' , 'status' => 2 , 'not_curriculum_type' => ""))->sum('charge_money');
                            if($type == '新签'){
                                $money_num = sprintf("%.2f", sprintf("%.2f", $oa_achievement_num)+sprintf("%.2f", ($oa_achievement_num1/2)));
                                $value['count'][$k]['count_num'] = $money_num;
                            }else if($type == '续签'){
                                $money_num = sprintf("%.2f", sprintf("%.2f", $oa_achievement_num)+sprintf("%.2f", ($oa_achievement_num1/2)));
                                $value['count'][$k]['count_num'] = $money_num;
                            }else{
                                $oa_achievement_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'status' => 2))->sum('charge_money');
                                $money_num = sprintf("%.2f", sprintf("%.2f", $oa_achievement_num)+sprintf("%.2f", $oa_achievement_num1));
                                $value['count'][$k]['count_num'] = $money_num;
                            }
                        }else{
                            $oa_achievement_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'achievement_type' => $type , 'campus_id' => $val['id'] , 'status' => 2 , 'not_curriculum_type' => ""))->sum('charge_money');
                            $oa_achievement_num1 = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'achievement_type' => '转介绍' , 'campus_id' => $val['id'] , 'status' => 2 , 'not_curriculum_type' => ""))->sum('charge_money');
                            $money_num = '';
                            if($type == '新签'){
                                $money_num = sprintf("%.2f", sprintf("%.2f", $oa_achievement_num)+sprintf("%.2f", ($oa_achievement_num1/2)));
                                $value['count'][$k]['count_num'] = $money_num;
                            }else if($type == '续签'){
                                $money_num = sprintf("%.2f", sprintf("%.2f", $oa_achievement_num)+sprintf("%.2f", ($oa_achievement_num1/2)));
                                $value['count'][$k]['count_num'] = $money_num;
                            }else{
                                $oa_achievement_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'campus_id' => $val['id'] , 'status' => 2))->sum('charge_money');
                                $money_num = sprintf("%.2f", sprintf("%.2f", $oa_achievement_num)+sprintf("%.2f", $oa_achievement_num1));
                                $value['count'][$k]['count_num'] = $money_num;
                            }
                        }
                    }else if($value['name'] == $type.'完成率(常规)'){
                        $value['count'][$k]['count_num'] = sprintf("%.3f", $top['achievement']['count'][$k]['count_num']/$top['target']['count'][$k]['count_num']);
                    }else if($value['name'] == '特训营业绩'){
                        if($val['id'] == '100000'){
                            $special_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'not_curriculum_type' => '特训营' , 'status' => 2))->sum('charge_money');
                        }else{
                            $special_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'not_curriculum_type' => '特训营' , 'campus_id' => $val['id'] , 'status' => 2))->sum('charge_money');
                        }
                        $value['count'][$k]['count_num'] = sprintf("%.2f", $special_num);
                    }else if($value['name'] == '合作项目业绩'){
                        if($val['id'] == '100000'){
                            $cooperation_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'not_curriculum_type' => '合作项目' , 'status' => 2))->sum('charge_money');
                        }else{
                            $cooperation_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'not_curriculum_type' => '合作项目' , 'campus_id' => $val['id'] , 'status' => 2))->sum('charge_money');
                        }
                        $value['count'][$k]['count_num'] = sprintf("%.2f", $cooperation_num);
                    }else if($value['name'] == '月'.$type.'总业绩'){
                        if($val['id'] == '100000'){
                             $achievement_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'status' => 2))->sum('charge_money');
                        }else{
                            $achievement_num = $oa_achievement->where(array('checkout_date' => array('like' , $date.'%') , 'campus_id' => $val['id'] , 'status' => 2))->sum('charge_money');
                            // echo $oa_achievement->getLastsql();
                            // echo "<br>";
                        }
                        if($achievement_num){
                            $value['count'][$k]['count_num'] = sprintf("%.2f", $achievement_num);
                        }else{
                            $value['count'][$k]['count_num'] = sprintf("%.2f", 0);
                        }
                    
                    }
                    
                }
            }
            for($i = 1; $i <= $day_count; $i++){
                if($i<10){
                    $day_num = '0'.$i;
                }else{
                    $day_num = $i;
                }
                $bottom[$i]['name'] = $i.'日'.$type.'总业绩';
                foreach($campus_arr as $key => $val){
                    $school_name = $val['name'];
                    $bottom[$i][$key]['school_name'] = $val['name'];
                    if($val['id'] == '100000'){
                        $num_day_count = $oa_achievement->where(array('checkout_date' => $year_day.'-'.$month_count.'-'.$day_num , 'status' => 2))->sum('charge_money');
                    }else{
                        $num_day_count = $oa_achievement->where(array('checkout_date' => $year_day.'-'.$month_count.'-'.$day_num , 'campus_id' => $val['id'] , 'status' => 2))->sum('charge_money');
                    }
                    $bottom[$i][$key]['num_day_count'] = sprintf("%.2f", $num_day_count);
                }
            }

            $data['top'] = $top;
            $data['bottom'] = $bottom;
            $data['status'] = true;
        }else{
            $data['status'] = false;
        }
        // echo "<pre>";
        // print_r($data);
        $array['target_data_arr'] = $data;
        $array['target_type'] = $type;
        $array['campus_arr'] = $campus_arr;
        $_SESSION['target_array'] = $array;
        echo json_encode($data);
    }


    //每到月一号计算上个月年，月，日，总共多少天
    function year_month_day($time){
        //下面是获取指定月的数据
        //获取月份当前
        $data = array();
        $year_day = Intval(date("Y",$time));
        $month_day = Intval(date("m",$time));
        $data['year_day'] = $year_day;
        $data['month_day'] = $month_day;
        //计算月一共有多少天
        $data['day_count']=date('j',mktime(0,0,1,($month_day==12?1:$month_day+1),1,($month_day==12?$year_day+1:$year_day))-24*3600);
        //var_dump($data);
        return $data;
    }


    //个人业绩状态修改
    function Personal_target_save(){
        if(empty($_GET['data'])){
            echo json_encode(array('status' => false , 'content' => '程序出错，请联系管理员'));exit;//数据出错
        }else{
            $array = json_decode($_GET['data'],true);
        }
        //echo json_encode($array);exit;
        $model = D('oa_achievement');
        if(empty($array['id'])){
            foreach($array as $val){
                $state = $model->where(array('id' => $val['id']))->save(array("status" => $val['status']));
                if(!$state){
                    echo json_encode(array('status' => false , 'content' => '程序出错，请联系管理员'));exit;//数据出错
                }
            }
        }else{
            $state = $model->where(array('id' => $array['id']))->save(array("status" => $array['status']));
            if(!$state){
                echo json_encode(array('status' => false , 'content' => '程序出错，请联系管理员'));exit;//数据出错
            }
        }
        echo json_encode(array('status' => true , 'content' => '成功'));exit;//
    }


    //个人业绩录入页面显示数据程序
    function Personal_target_find(){
        // echo "<pre>";
        // print_r($_GET);die;
        if(empty($_GET['status'])){
            echo json_encode(array('status' => false , 'content' => '程序出错，请联系管理员'));exit;//数据出错
        }else{
            $status = $_GET['status'];
        }
        $oa_foo_info = D('oa_foo_info');
        if(empty($_GET['school_id'])){
            if(empty($_GET['begin_date']) && empty($_GET['end_date'])){
                $date = date("Y-m",time());
                $where_arr = array('checkout_date' => array('like' , '%'.$date.'%') , 'status' => $status);
            }else{
                $begin_date = $_GET['begin_date'];
                $end_date = $_GET['end_date'];
                $where_arr = array('checkout_date' => array('between' , $begin_date.','.$end_date) , 'status' => $status);
            }
            $where_user = array();
        }else{
            $school_name = array();
            $campus_id = $_GET['school_id'];
            if(empty($_GET['begin_date']) && empty($_GET['end_date'])){
                $date = date("Y-m",time());
                $where_arr = array('campus_id' => $campus_id , 'checkout_date' => array('like' , '%'.$date.'%') , 'status' => $status);
            }else{
                $begin_date = $_GET['begin_date'];
                $end_date = $_GET['end_date'];
                $where_arr = array('campus_id' => $campus_id , 'checkout_date' => array('between' , $begin_date.','.$end_date) , 'status' => $status);
            }
            $where_school = array('id' => $campus_id);
            $school_name = $oa_foo_info->where($where_school)->find();
            $where_user = array('school' => $school_name['name']);
        }

        $person_all = D('person_all');

        //收据编号
        if(!empty($_GET['receipt_card'])){
            $where_arr['receipt_card'] = $_GET['receipt_card'];
        }

        //教学主任
        if(!empty($_GET['teaching_userid'])){
            if($_GET['study_userid'] == 'All'){
                $where_arr['teaching_userid'] = array('NEQ','');
            }else{
                $where_user['name'] = $_GET['teaching_userid'];
                $user_arr = $person_all->where($where_user)->find();
                $where_arr['teaching_userid'] = $user_arr['id'];
            }
        }

        //学习管理师
        if(!empty($_GET['study_userid'])){
            if($_GET['study_userid'] == 'All'){
                $where_arr['study_userid'] = array('NEQ','');
            }else{
                $where_user['name'] = $_GET['teaching_userid'];
                $user_arr = $person_all->where($where_user)->find();
                $where_arr['study_userid'] = $user_arr['id'];
            }
        }

        //业绩类型
        if(!empty($_GET['achievement_type'])){
            $where_arr['achievement_type'] = $_GET['achievement_type'];
        }

        //收费类型
        if(!empty($_GET['charge_type'])){
            $where_arr['charge_type'] = $_GET['charge_type'];
        }

        //学员姓名
        if(!empty($_GET['student_name'])){
            $where_arr['student_name'] = $_GET['student_name'];
        }

        //收费日期
        if(!empty($_GET['achievement_date'])){
            $where_arr['achievement_date'] = $_GET['achievement_date'];
        }

        //课程名称
        if(!empty($_GET['curriculum_name'])){
            $where_arr['curriculum_name'] = $_GET['curriculum_name'];
        }

        //讲师姓名
        if(!empty($_GET['teacher_name'])){
            $where_arr['teacher_name'] = $_GET['teacher_name'];
        }

        //收费类型
        if(!empty($_GET['receivables_type'])){
            $where_arr['receivables_type'] = $_GET['receivables_type'];
        }
        
        $model = D('oa_achievement');
        $data = array();
        $user_arrs = array();
        $data = $model->where($where_arr)->order('id desc')->select();

        // echo "<pre>";
        // print_r($where_arr);exit;
        //echo $model->getLastsql();
        //print_r($where_arr);
        $user_arrs = $person_all->where($where_user)->select();//
        foreach($data as &$value){
            foreach($user_arrs as $val){
                if($value['checkout_userid'] == $val['id']){
                    $value['checkout_username'] = $val['name'];
                }
                if($value['teaching_userid'] == $val['id']){
                    $value['teaching_userid'] = $val['name'];
                }
                if($value['study_userid'] == $val['id']){
                    $value['study_userid'] = $val['name'];
                }
            }
            
        }
        // print_R($data);
        echo json_encode($data);exit;
    }

    //个人业绩录入接口程序
    function Personal_target_add(){
        if(empty($_GET['data'])){
            echo json_encode(array('status' => false , 'content' => '请确认数据传输正确'));exit;//数据出错
        }else{
            $array = json_decode($_GET['data'],true);
        }
        if($array['curriculum_type'] == '0' && $array['not_curriculum_type'] == '0'){
            echo json_encode(array('status' => false , 'content' => '请选择课程类型'));exit;//数据出错
        }
        $data = array();
        $date = date('Y-m-d',time());
        $teaching_userid = $array['teaching_userid'];
        $study_userid = $array['study_userid'];
        $person_all = D('person_all');
        $oa_foo_info = D('oa_foo_info');
        $model = D('oa_achievement');
        $school_name = $oa_foo_info->where(array('id' => $array['school_id']))->find();

        if($array['teaching_userid'] == '' && $array['study_userid'] == ''){
            echo json_encode(array('status' => false , 'content' => '请填写学管或者教主'));exit;//数据出错
        }else{
            if($array['teaching_userid']!=""){
                $teaching_user = $person_all->where(array('name' => $teaching_userid , 'school' =>  $school_name['name']))->find();//
                if(!$teaching_user){
                    echo json_encode(array('status' => false , 'content' => $teaching_userid.' | '.$school_name['name'].' 查无此人，请确认名字是否正确'));exit;//数据出错
                }
            }
            if($array['study_userid']!=""){
                $study_user = $person_all->where(array('name' => $study_userid , 'school' =>  $school_name['name']))->find();//
                if(!$study_user){
                    echo json_encode(array('status' => false , 'content' => $study_userid.' | '.$school_name['name'].' 查无此人，请确认名字是否正确'));exit;//数据出错
                }
            }
        }
        
        $data['campus_id'] = $array['school_id'];
        $data['checkout_date'] = $date;
        $data['receipt_card'] = $array['receipt_card'];
        $data['checkout_userid'] = $array['checkout_userid'];
        $data['teaching_userid'] = $teaching_user['id'];
        $data['study_userid'] = $study_user['id'];
        $data['achievement_type'] = $array['achievement_type'];
        $data['charge_type'] = $array['charge_type'];
        $data['student_name'] = $array['student_name'];
        $data['grade'] = $array['grade'];
        $data['achievement_date'] = $array['achievement_date'];
        $data['curriculum_type'] = $array['curriculum_type'];
        $data['not_curriculum_type'] = $array['not_curriculum_type'];
        $data['curriculum_name'] = $array['curriculum_name'];
        $data['teacher_name'] = $array['teacher_name'];
        $data['charge_class_num'] = $array['charge_class_num'];
        $data['charge_money'] = $array['charge_money'];
        $data['new_signing_ratio'] = $array['new_signing_ratio'];
        $data['old_signing_ratio'] = $array['old_signing_ratio'];
        $data['receivables_type'] = $array['receivables_type'];
        $data['content'] = $array['content'];

        if(empty($array['id'])){
            //添加操作（要是没有id传值）
            $state = $model->add($data);
            if($state){
                $array['id'] = $state;
                $array['checkout_date'] = $date;
                $array['status'] = 1;
                echo json_encode(array('status' => true , 'content' => '保存成功' , 'data' => $array));exit;//数据出错
            }else{
                echo json_encode(array('status' => false , 'content' => '保存失败，请联系管理员'));exit;//数据出错
            }
        }else{
            //修改操作（如果有id传值）
            $id = $array['id'];
            $state = $model->where(array("id" => $id))->save($data);
            if($state){
                $array['id'] = $id;
                $array['checkout_date'] = $date;
                $array['status'] = 1;
                echo json_encode(array('status' => true , 'content' => '修改成功' , 'data' => $array));exit;//数据出错 
            }else{
                echo json_encode(array('status' => false , 'content' => '修改失败，请确认数据是否修改'));exit;//数据出错
            }
        }
    }


    //统计表excel生成函数
    function target_excel(){
        if(empty($_SESSION['target_array'])){
            $this->error("生成页面错误，请联系管理员！");exit;
        }
        $content_arr = array();

        $content_arr = $_SESSION['target_array']['target_data_arr'];
        $campus_arr = $_SESSION['target_array']['campus_arr'];
        $target_type = $_SESSION['target_array']['target_type'];
        import("Vendor.PHPExcel");
        //创建对象
        $excel = new \PHPExcel();
        //Excel表格式,这里简略写了8列
        $letter = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U');
        //表头数组
        $tableheader[] = '单位（元）';
        foreach($campus_arr as $value){
            $tableheader[] = $value['name'];
        }
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }
        //表格数组
        $i = 0;
        foreach($content_arr['top'] as $key => $val){
            $data[$i][0] = $val['name'];
            foreach($val['count'] as $k => $v){
                $data[$i][$k+1] = $v["count_num"];
            }
            $i++;
        }
        foreach($content_arr['bottom'] as $key => $val){
            $data[$i][0] = $val['name'];
            foreach($val as $k => $v){
                $data[$i][$k+1] = $v['num_day_count'];
            }
            $i++;
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
        header('Content-Disposition:attachment;filename="'.$target_type.'统计表.xls"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }
    


}
?>