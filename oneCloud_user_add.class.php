<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$customer_id = passport_decrypt($customer_id);
require('../back_init.php');

//推广员账号
$promoter = -1;
if(!empty($_POST["promoter"])){
	(int)$promoter = $configutil->splash_new($_POST["promoter"]); 
}
//推广员姓名
$aplay_name = -1;
if(!empty($_POST["aplay_name"])){
	$aplay_name = $configutil->splash_new($_POST["aplay_name"]); 
}
//推广员电话
$aplay_phone = -1;
if(!empty($_POST["aplay_phone"])){
	$aplay_phone = $configutil->splash_new($_POST["aplay_phone"]); 
}


$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$exist = 0;
$sql_exist = 'select count(1) as num from weixin_commonshop_team_aplay where isvalid=true and customer_id='.(int)$customer_id . " and aplay_user_id=".$promoter;
$result_exist = mysql_query($sql_exist) or die (" Wrong_1 : QUERY ERROR : ".mysql_error());
if($row_exist = mysql_fetch_object($result_exist)){
	$exist=$row_exist->num;
}

if($exist ==0){
	//插入区域代理表
	$sql_team="insert into weixin_commonshop_team_aplay(aplay_user_id,aplay_grate,aplay_name,aplay_phone,isvalid,createtime,status,customer_id)values(".$promoter.",0,'".$aplay_name."',".$aplay_phone.",true,now(),0,".$customer_id.")";
	mysql_query($sql_team)or die('O31 Query_team failed: ' . mysql_error()); 	
}else{
	
	echo"<script>alert('此推广员已经申请/成为区域代理');window.history.go(-1)</script>";
	return;
	 
}
 
mysql_close($link);
$Url = "oneCloud_UserList.php?customer_id=".passport_encrypt($customer_id);
echo "<script>location.href='". $Url ."';</script>"
?>
