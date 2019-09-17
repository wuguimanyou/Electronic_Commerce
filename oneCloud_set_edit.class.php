<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$customer_id = passport_decrypt($customer_id);
require('../back_init.php');

$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$area_leave = 2;  //等级 省、市、区
if($_POST["area_leave"] != ""){
	(int)$area_leave = $configutil->splash_new($_POST["area_leave"]); 
}

//省
$location_p = 0;
if(!empty($_POST["location_p"])){
	(int)$location_p = $configutil->splash_new($_POST["location_p"]);
}
//市
$location_c = 0;
if(!empty($_POST["location_c"])){
	(int)$location_c = $configutil->splash_new($_POST["location_c"]);
}
//区
$location_a = 0;
if(!empty($_POST["location_a"])){
	(int)$location_a = $configutil->splash_new($_POST["location_a"]);
}

//负责人编号
$aplayID = 0;
if(!empty($_POST["aplayID"])){
	(int)$aplayID = $configutil->splash_new($_POST["aplayID"]);
}	

//升级前,将之前区域负责人清空
//查询之前负责区域
$areaID = 0;
$query_apl = "select now_area from weixin_commonshop_team_aplay where isvalid = true and customer_id=".$customer_id." and aplay_user_id = ".$aplayID;
$result_apl = mysql_query($query_apl) or die("L41 Query_apl error : ".mysql_error());
$areaID = mysql_result($result_apl,0,0);
if($areaID>0){
	//清空区域表-负责人
	$query_area_c = "update weixin_commonshop_team_area set area_user = -1 where isvalid = true and customer_id=".$customer_id." and id =".$areaID;
	mysql_query($query_area_c) or die (" L47 : QUERY_area_c ERROR : ".mysql_error());	
}

switch($area_leave){
	case 2:
	
	//更新区域表-负责人
	$query_area = "update weixin_commonshop_team_area set area_user = ".$aplayID." where isvalid = true and customer_id=".$customer_id." and id =".$location_p;
	mysql_query($query_area) or die (" L55 : QUERY_area ERROR : ".mysql_error());

	//更新负责人表-区域
	$query_user = "update weixin_commonshop_team_aplay set aplay_grate = 2,now_area = ".$location_p." where isvalid = true and customer_id=".$customer_id." and aplay_user_id =".$aplayID;
	mysql_query($query_user) or die (" L59 : QUERY_user ERROR : ".mysql_error());	

	//更新promoter状态  5：区代；6：市代；7：省代
	$query_promoter = "update promoters set isAgent = 7 where isvalid = true and status=1 and customer_id=".$customer_id." and user_id =".$aplayID;
	mysql_query($query_promoter) or die (" L63 : QUERY_promoterr ERROR : ".mysql_error());	
	
	break;
	case 1:
	
	//更新区域表-负责人
	$query_area = "update weixin_commonshop_team_area set area_user = ".$aplayID." where isvalid = true and customer_id=".$customer_id." and id =".$location_c;
	mysql_query($query_area) or die (" L70 : QUERY_area ERROR : ".mysql_error());

	//更新负责人表-区域
	$query_user = "update weixin_commonshop_team_aplay set aplay_grate = 1,now_area = ".$location_c." where isvalid = true and customer_id=".$customer_id." and aplay_user_id =".$aplayID;
	mysql_query($query_user) or die (" L74 : QUERY_user ERROR : ".mysql_error());	

	//更新promoter状态  5：区代；6：市代；7：省代
	$query_promoter = "update promoters set isAgent =6 where isvalid = true and status=1 and customer_id=".$customer_id." and user_id =".$aplayID;
	mysql_query($query_promoter) or die (" L78 : QUERY_promoterr ERROR : ".mysql_error());	
	
	break;
	case 0:
	
	//更新区域表-负责人
	$query_area = "update weixin_commonshop_team_area set area_user = ".$aplayID." where isvalid = true and customer_id=".$customer_id." and id =".$location_a;
	mysql_query($query_area) or die (" L85 : QUERY_area ERROR : ".mysql_error());

	//更新负责人表-区域
	$query_user = "update weixin_commonshop_team_aplay set aplay_grate = 0,now_area = ".$location_a." where isvalid = true and customer_id=".$customer_id." and aplay_user_id =".$aplayID;
	mysql_query($query_user) or die (" L88 : QUERY_user ERROR : ".mysql_error());		

	//更新promoter状态  5：区代；6：市代；7：省代
	$query_promoter = "update promoters set isAgent = 5 where isvalid = true and status=1 and customer_id=".$customer_id." and user_id =".$aplayID;
	mysql_query($query_promoter) or die (" L93 : QUERY_promoterr ERROR : ".mysql_error());	
	
	break;
	default:
		$errorMsg = "<script>alert('未知方法，请联系管理员');window.history.go(-1)</script>";
		mysql_close($link);
		echo $errorMsg;
		return;
}


mysql_close($link);
$Url = "oneCloud_area.php?customer_id=".passport_encrypt($customer_id);
header("Location: ".$Url); 	
exit;
?>