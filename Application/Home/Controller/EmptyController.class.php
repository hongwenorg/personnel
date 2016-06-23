<?php
/**
*@Author:温加宝
*@Email:wen8555955@163.com
*@DateTime:2016/3/17
*@Description:空模板控制器,直接继承\Common\Controller\EmptyBaseController即可
*/
namespace Common\Controller;
useThink\Controller;
class EmptyController extends \Common\Controller\EmptyBaseController{
	function _initialize(){
		parent::_initialize();
	}
}