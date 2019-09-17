<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$customer_id = passport_decrypt($customer_id);
require('../back_init.php');

$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

//方法
if(!empty($_GET["op"])){
$op = $configutil->splash_new($_GET["op"]);
}

//区域编号
$areaID = -1;
if(!empty($_GET["keyid"])){
(int)$areaID = $configutil->splash_new($_GET["keyid"]);
}

if($areaID<0){
	$errorMsg = "<script>alert('参数不正确');window.history.go(-1)</script>";
	echo $errorMsg;
	return;
}

//页数
$pagenum = 0;  
if(!empty($_POST["pagenum"])){
$pagenum = $configutil->splash_new($_POST["pagenum"]);
}

switch($op){
	case "cancle":
		//查询负责人编号
		$query_pro = "select area_user  from weixin_commonshop_team_area where isvalid = true and customer_id=".$customer_id." and id =".$areaID;
		$result_pro = mysql_query($query_pro) or die("L38 query error : ".mysql_error());
		$userID = mysql_result($result_pro,0,0);	
	
		//撤销区域表-负责人
		$query_area = "update weixin_commonshop_team_area set area_user = -1 where isvalid = true and customer_id=".$customer_id." and id =".$areaID;
		mysql_query($query_area) or die (" L43 : QUERY_area ERROR : ".mysql_error());

		//撤销负责人表-区域
		$query_user = "update weixin_commonshop_team_aplay set now_area = 0 where isvalid = true and customer_id=".$customer_id." and now_area =".$areaID;
		mysql_query($query_user) or die (" L47 : QUERY_user ERROR : ".mysql_error());

		//撤销promoter状态  0,普通推广员 5：区代；6：市代；7：省代
		$query_promoter = "update promoters set isAgent =0 where isvalid = true and status=1 and customer_id=".$customer_id." and user_id =".$userID;
		mysql_query($query_promoter) or die (" L51 : QUERY_promoterr ERROR : ".mysql_error());
		
	break;
	case "del":
		//查询是否有下级
		$exist = 0;
		$query_exist = "select count(1) as exist from weixin_commonshop_team_area where isvalid = true and parent_id = ".$areaID;
		$result_exist = mysql_query($query_exist) or die("L42 QUERY_exist error : ".mysql_error());
		$exist = mysql_result($result_exist,0,0);		
		if($exist>0){
			$errorMsg = "<script>alert('存在下级区域，不能删除');window.history.go(-1)</script>";
			echo $errorMsg; 
			return;		
		}

		//查询负责人编号
		$query_pro = "select area_user  from weixin_commonshop_team_area where isvalid = true and customer_id=".$customer_id." and id =".$areaID;
		$result_pro = mysql_query($query_pro) or die("L68 query error : ".mysql_error());
		$userID = mysql_result($result_pro,0,0);	
		
		//撤销区域表-负责人
		$query_area = "update weixin_commonshop_team_area set isvalid = false where isvalid = true and customer_id=".$customer_id." and id =".$areaID;
		mysql_query($query_area) or die (" L73 : QUERY_area ERROR : ".mysql_error());

		//撤销负责人表-区域
		$query_user = "update weixin_commonshop_team_aplay set now_area = 0 where isvalid = true and customer_id=".$customer_id." and now_area =".$areaID;
		mysql_query($query_user) or die (" L77 : QUERY_user ERROR : ".mysql_error());		

		if($userID>0){
		//撤销promoter状态  0,普通推广员 5：区代；6：市代；7：省代
		$query_promoter = "update promoters set isAgent =0 where isvalid = true and status=1 and customer_id=".$customer_id." and user_id =".$userID;
		mysql_query($query_promoter) or die (" L81 : QUERY_promoterr ERROR : ".mysql_error());		
		}
	
	break;
	default:
	mysql_close($link);
	echo "未知方法，请联系管理员！";
	break;
}


mysql_close($link);
$Url = "oneCloud_area.php?customer_id=".passport_encrypt($customer_id)."&pagenum=".$pagenum;
header("Location: ".$Url); 	
exit;
?>