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
        $model3 = D('oa_user');
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
                    if($data_arr2){
                        $data2 = array();
                        foreach ($data_arr2 as $key => $value) {
                            $user_arr = $model3->where(array('id' => $value['user_id']))->find();
                            $post_arr = $model5->where(array('id' => $value['post_id']))->find();
                            $data2[$key]['name'] = $user_arr['name'];
                            $data2[$key]['post'] = $post_arr['name'];
                            $data2[$key]['level'] = $value['level'];
                            $data2[$key]['target'] = $value['target'];
                            $data2[$key]['upgrade'] = $value['upgrade'];
                            $data2[$key]['relegation'] = $value['relegation'];
                        }
                    }else{
                        $data_arr2 = array();
                    }
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
                        $data2[$key]['name'] = $user_arr['name'];
                        $data2[$key]['post'] = $post_arr['name'];
                        $data2[$key]['level'] = $value['level'];
                        $data2[$key]['target'] = $value['target'];
                        $data2[$key]['upgrade'] = $value['upgrade'];
                        $data2[$key]['relegation'] = $value['relegation'];
                    }
                }else{
                    $data_arr2 = array();
                }
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
        echo json_encode($data);
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
                    $arr = $model3->where("name like '%".$name."%' and school like '%".$school_name."%' and state = 1")->find();
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
    


}
?>