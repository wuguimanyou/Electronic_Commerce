<?php 
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$customer_id = passport_decrypt($customer_id);
require('../back_init.php');

//地区编号
$areaID = 0;
if(!empty($_POST["areaID"])){
$areaID = $configutil->splash_new($_POST["areaID"]);
}

//地区等级
$areaLeave = 0;  //0:区 1:市 2:省
if(!empty($_POST["areaLeave"])){
$areaLeave = $configutil->splash_new($_POST["areaLeave"]);
}

$agentID = 5;
switch($areaLeave){
	case 0: $agentID=5; break;
	case 1: $agentID=6; break;
	case 2: $agentID=7; break;
}

//负责人编号
$location_user = 0;
if(!empty($_POST["location_user"])){
$location_user = $configutil->splash_new($_POST["location_user"]);
}

//页数
$pagenum = 0;  
if(!empty($_POST["pagenum"])){
$pagenum = $configutil->splash_new($_POST["pagenum"]);
}

$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$now_area = 0;
$query_select = "select now_area from weixin_commonshop_team_aplay where isvalid = true and customer_id=".$customer_id." and aplay_user_id =".$location_user;
$result_select = mysql_query($query_select) or die("L30 query error : ".mysql_error());
$now_area = mysql_result($result_select,0,0);
if($now_area>0){
	$errorMsg = "<script>alert('已有负责区域，不能重复添加');window.history.go(-1)</script>";
	mysql_close($link);
	echo $errorMsg;
	return;
}

//检查该区域之前负责人
$area_user = -1;
$query_sarea = "select area_user from weixin_commonshop_team_area where customer_id=".$customer_id." and id =".$areaID;
$result_sarea = mysql_query($query_sarea) or die("L56 query_sarea error : ".mysql_error());
$area_user = mysql_result($result_sarea,0,0);
if($area_user>0){
	//清空该负责人的设置
	$query_user_clera = "update weixin_commonshop_team_aplay set now_area = 0 where isvalid = true and customer_id=".$customer_id." and aplay_user_id =".$area_user;
	mysql_query($query_user_clera) or die (" L61 : QUERY_user_clera ERROR : ".mysql_error());
	//更新promoter状态  5：区代；6：市代；7：省代; 0 : 普通
	$query_promoter = "update promoters set isAgent = 0 where isvalid = true and status=1 and customer_id=".$customer_id." and user_id =".$area_user;
	mysql_query($query_promoter) or die (" L63 : QUERY_promoterr ERROR : ".mysql_error());		
}

//更新区域表-负责人
$query_area = "update weixin_commonshop_team_area set area_user = ".$location_user." where isvalid = true and customer_id=".$customer_id." and id =".$areaID;
mysql_query($query_area) or die (" L55 : QUERY_area ERROR : ".mysql_error());

//更新负责人表-区域
$query_user = "update weixin_commonshop_team_aplay set aplay_grate = ".$areaLeave.",now_area = ".$areaID." where isvalid = true and customer_id=".$customer_id." and aplay_user_id =".$location_user;
mysql_query($query_user) or die (" L59 : QUERY_user ERROR : ".mysql_error());

//更新promoter状态  5：区代；6：市代；7：省代
$query_promoter = "update promoters set isAgent = ".$agentID." where isvalid = true and status=1 and customer_id=".$customer_id." and user_id =".$location_user;
mysql_query($query_promoter) or die (" L63 : QUERY_promoterr ERROR : ".mysql_error());	

mysql_close($link);
$Url = "oneCloud_area.php?customer_id=".passport_encrypt($customer_id)."&pagenum=".$pagenum;
header("Location: ".$Url); 	
exit;
?>