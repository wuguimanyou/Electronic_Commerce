<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$customer_id = passport_decrypt($customer_id);
require('../back_init.php');

//团队申请协议
$agreement = 0;
if(!empty($_POST["agreement"])){
	$agreement = $configutil->splash_new($_POST["agreement"]); 
}

//消费奖励最低金额
$consume_money = 0;
if(!empty($_POST["consume_money"])){
$consume_money = $configutil->splash_new($_POST["consume_money"]);
}

//消费奖励比例
$consume_percent = 0;
if(!empty($_POST["consume_percent"])){
$consume_percent = $configutil->splash_new($_POST["consume_percent"]);
}

//团队总奖励比例
$team_all = 0;
if(!empty($_POST["team_all"])){
$team_all = $configutil->splash_new($_POST["team_all"]);
}

//区代团队订单数
$a_order = 0;
if(!empty($_POST["a_order"])){
$a_order = $configutil->splash_new($_POST["a_order"]);
}

//区代直推人数
$a_people = 0;
if(!empty($_POST["a_people"])){
$a_people = $configutil->splash_new($_POST["a_people"]);
}

//区代奖励比例
$a_percent = 0;
if(!empty($_POST["a_percent"])){
$a_percent = $configutil->splash_new($_POST["a_percent"]);
}

//市代团队订单数
$c_order = 0;
if(!empty($_POST["c_order"])){
$c_order = $configutil->splash_new($_POST["c_order"]);
}

//市代直推人数
$c_people = 0;
if(!empty($_POST["c_people"])){
$c_people = $configutil->splash_new($_POST["c_people"]);
}

//市代奖励比例
$c_percent = 0;
if(!empty($_POST["c_percent"])){
$c_percent = $configutil->splash_new($_POST["c_percent"]);
}

//省代团队订单数
$p_order = 0;
if(!empty($_POST["p_order"])){
$p_order = $configutil->splash_new($_POST["p_order"]);
}

//省代直推人数
$p_people = 0;
if(!empty($_POST["p_people"])){
$p_people = $configutil->splash_new($_POST["p_people"]);
}

//省代奖励比例
$p_percent = 0;
if(!empty($_POST["p_percent"])){
$p_percent = $configutil->splash_new($_POST["p_percent"]);
}

$percents = $consume_percent+$p_percent + $c_percent + $a_percent;
if($percents>1){
	echo "<script>alert('奖励总比例不能大于1');window.history.go(-1)</script>";
}

$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$exist = 0;
$sql_exist = 'select count(1) as num from weixin_commonshop_team where isvalid=true and customer_id='.(int)$customer_id;
$result_exist = mysql_query($sql_exist) or die (" Wrong_1 : QUERY ERROR : ".mysql_error());
if($row_exist = mysql_fetch_object($result_exist)){
	$exist=$row_exist->num;
}

if($exist ==1){
	
	 $sql_update="update weixin_commonshop_team set 
	 agreement ='" . $agreement . "',
	 consume_money=".$consume_money.",
	 consume_percent=".$consume_percent.",
	 team_all=".$team_all.",
	 a_order=".$a_order.",
	 a_people='".$a_people."', 
	 a_percent='".$a_percent."',
	 c_order='".$c_order."',
	 c_people=".$c_people.",
	 c_percent='".$c_percent."',
	 p_order='".$p_order."',
	 p_people='".$p_people."',
	 p_percent='".$p_percent."'
	 where customer_id=".(int)$customer_id;
	//echo $sql_update;
	mysql_query($sql_update) or die (" Wrong_2 : QUERY ERROR : ".mysql_error());
	 
	}else if($exist == 0){
	
	$sql_insert="insert into weixin_commonshop_team(agreement,isvalid,createtime,customer_id,consume_money,consume_percent,team_all,a_order,a_people,a_percent,c_order,c_people,c_percent,p_order,p_people,p_percent) values ('".$agreement."',true,now(),".$customer_id.",".$consume_money.",".$consume_percent.",".$team_all.",".$a_order.",".$a_people.",".$a_percent.",".$c_order.",".$c_people.",".$c_percent.",".$p_order.",".$p_people.",".$p_percent.")";
	mysql_query($sql_insert) or die (" Wrong_3 : QUERY ERROR : ".mysql_error());
	
}else{
	
	echo"<script>alert('数据异常，请联系管理员');window.history.go(-1)</script>";
	return;
	 
}

mysql_close($link);

$Url = "oneCloud.php?customer_id=".passport_encrypt($customer_id);
header("Location: ".$Url); 	
exit;
?>
