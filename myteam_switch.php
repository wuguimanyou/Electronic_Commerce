<?php
$issell 	    = 0; 	//�Ƿ�������
$is_team  		= 0;	//�Ƿ����Ŷӽ���
$is_shareholder = 0;	//�Ƿ����ɶ��ֺ콱��
$isOpenInstall  = 0;	//�Ƿ�����ʦ
$query = "select issell,is_team,is_shareholder,isOpenInstall from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('query failed3'.mysql_error());
while($row3 = mysql_fetch_object($result)){
	$issell 		= $row3->issell;
	$is_team  		= $row3->is_team;
	$is_shareholder = $row3->is_shareholder;
	$isOpenInstall  = $row3->isOpenInstall;
}
//�ж������Ƿ��������̹���---start
$is_disrcount = 0;
$is_agent 	  = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='�̳Ǵ���ģʽ' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_agent Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_agent = 1;
}
//�ж������Ƿ��������̹���---end
//�ж������Ƿ�����Ӧ�̹���---start
$is_disrcount = 0;
$is_supply 	  = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='�̳ǹ�Ӧ��ģʽ' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_supply Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_supply = 1;
}
//�ж������Ƿ�����Ӧ�̹���---end	
//�ж������Ƿ��������Ŷӹ���---start
$is_disrcount = 0;
$is_areaAgent = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='�̳������Ŷӽ���' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_areaAgent Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_areaAgent = 1;
}
//�ж������Ƿ��������Ŷӹ���---end	
//�ж������Ƿ����ɶ��ֺ칦��---start
$is_disrcount 	= 0;
$is_OpenShareholder = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='�̳ǹɶ��ֺ콱��' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_OpenShareholder Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_OpenShareholder = 1;
}
//�ж������Ƿ����ɶ��ֺ칦��---end

	
?>