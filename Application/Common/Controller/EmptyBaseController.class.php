<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:空模板控制器
*@use:其他项目添加EmptyController文件并继承该类即可
*/
namespace Common\Controller;
useThink\Controller;
class EmptyBaseController extendsController{
	function _initialize(){//项目配置文件中的配置项
		$emptyPath=C("EMPTY_PATH");//如果未配置默认的地址
		if(!$emptyPath || empty($emptyPath))
			$emptyPath="/index.php/Home/Index/exceptions";
		header("Location:".$emptyPath);
		exit();
	}
}