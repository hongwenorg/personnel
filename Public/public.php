<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/4/1
*@Description:公共方法
*/
	/*
	 *防止sql注入，验证字符串
	*/
	function inject_check($sql_str) { 
		return eregi('select|insert|and|or|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
	} 
	

	/*
	 *验证后提示
	*/
	function verify_id($id=null) { 
		if(!$id){
			exit('没有提交参数！'); 
		}elseif(inject_check($id)) { 
			exit('提交的参数非法！');
		}elseif(!is_numeric($id)) { 
			exit('提交的参数非法！'); 
		} 
		$id = intval($id); 
		 
		return $id; 
	} 


	/*
	 *随机生成字符串（员工号）
	*/
	function createRandomStr($length){ 
		//$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符 
		$str = '0123456789';//62个字符 
		$strlen = 62; 
		while($length > $strlen){ 
			$str .= $str; 
			$strlen += 62; 
		} 
		$str = str_shuffle($str); 
		return substr($str,0,$length); 
	} 
	 
	
	/*
	 *字符串验证
	*/
	function str_check( $str ) { 
		if(!get_magic_quotes_gpc()) { 
			$str = addslashes($str); // 进行过滤 
		} 
		$str = str_replace("_", "\_", $str); 
		$str = str_replace("%", "\%", $str); 
		 
	   return $str; 
	} 
	
	
	/*
	 *post字符串验证
	*/
	function post_check($post) { 
		if(!get_magic_quotes_gpc()) { 
			$post = addslashes($post);
		} 
		$post = str_replace("_", "\_", $post); 
		$post = str_replace("%", "\%", $post); 
		$post = nl2br($post); 
		$post = htmlspecialchars($post); 
		 
		return $post; 
	}

	/*
	 *写入操作日志
	*/
	function handle_log($user_id,$user,$module,$handle){
		$path = "Public/log";
		if (!is_dir($path)){
			mkdir($path,0777);  // 创建文件夹log,并给777的权限（所有权限）
		}
		$content = "用户id：$user_id|用户名：$user|操作的模块：$module|执行的操作：$handle\r\n";  // 写入的内容
		$file = $path."/".date("Y-m-d",time()).".txt";    // 写入的文件
		$sss = file_put_contents($file,$content,FILE_APPEND);  // 最简单的快速的以追加的方式写入写入方法
		return $content;
	}
?>