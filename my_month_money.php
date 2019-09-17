 <?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php');
require('../common/utility.php');

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('my_collect.php Could not select database');
date_default_timezone_set('PRC'); //设置中国时区 
$user_id=334452;
$query="select p.isAgent,u.generation from promoters  as p inner join weixin_users as u on u.id=p.user_id where  u.isvalid =true and  p.isvalid=true and  p.user_id=".$user_id ." and u.customer_id=".$customer_id;
file_put_contents("123fr.txt", "5.GetMoney_Common=======".$query."\r\n",FILE_APPEND);
$result=mysql_query($query);
$isAgent=0;
$generation=1;
while($row=mysql_fetch_object($result)){
    $isAgent=$row->isAgent;
	$generation=$row->generation;
	
}

$generation_o=$generation+3;

$start=strtotime(date("Y-m-01",time()));
$begin_start=date(date("Y-m-01",time()));
$end=strtotime(date('Y-m-d', strtotime("$begin_start +1 month")));
$total_price=0;

if($isAgent==1){
	$query_1="select sum(p.NoExpPrice) as NoExpPrice from weixin_commonshop_order_prices as p where p.isvalid =1 and p.batchcode in (select DISTINCT o.batchcode from weixin_commonshop_orders as o INNER JOIN weixin_users as u on o.user_id=u.id where u.isvalid=1 and o.isvalid=1 and u.gflag like '%,".$user_id.",%'  and o.paystatus =1 and o.sendstatus!=6 and u.customer_id=".$customer_id." and u.generation<=".$generation_o." and UNIX_TIMESTAMP(o.createtime)>=".$start." and UNIX_TIMESTAMP(o.createtime)<=".$end.")";
	echo $query_1;
    
	$result_1=mysql_query($query_1) or die("Query failed :".mysql_error());
	while($row_1=mysql_fetch_object($result_1)){
	$total_price=$row_1->NoExpPrice;
	
	break;
	}
}
if($total_price==''){
	$total_price=0;
}
$query="select agent_name,agent_discount,agent_price from weixin_commonshop_applyagents where isvalid=true and status and user_id=".$user_id;
$result=mysql_query($query);
$agent_price=0;
while($row=mysql_fetch_object($result)){
	$agent_price=$row->agent_price;
}
//如果三级一个月内总消费小于代理价格
if($agent_price>$total_price){
	//就降权
}
	



?>
