<?php
$issell 	    = 0; 	//是否开启分销
$is_team  		= 0;	//是否开启团队奖励
$is_shareholder = 0;	//是否开启股东分红奖励
$isOpenInstall  = 0;	//是否开启技师
$query = "select issell,is_team,is_shareholder,isOpenInstall from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('query failed3'.mysql_error());
while($row3 = mysql_fetch_object($result)){
	$issell 		= $row3->issell;
	$is_team  		= $row3->is_team;
	$is_shareholder = $row3->is_shareholder;
	$isOpenInstall  = $row3->isOpenInstall;
}
//判断渠道是否开启代理商功能---start
$is_disrcount = 0;
$is_agent 	  = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城代理模式' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_agent Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_agent = 1;
}
//判断渠道是否开启代理商功能---end
//判断渠道是否开启供应商功能---start
$is_disrcount = 0;
$is_supply 	  = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城供应商模式' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_supply Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_supply = 1;
}
//判断渠道是否开启供应商功能---end	
//判断渠道是否开启区域团队功能---start
$is_disrcount = 0;
$is_areaAgent = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城区域团队奖励' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_areaAgent Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_areaAgent = 1;
}
//判断渠道是否开启区域团队功能---end	
//判断渠道是否开启股东分红功能---start
$is_disrcount 	= 0;
$is_OpenShareholder = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城股东分红奖励' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_OpenShareholder Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_OpenShareholder = 1;
}
//判断渠道是否开启股东分红功能---end

	
?>