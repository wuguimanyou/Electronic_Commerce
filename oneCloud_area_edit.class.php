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
$location_p = $configutil->splash_new($_POST["location_p"]);
}
//市
$location_c = 0;
if(!empty($_POST["location_c"])){
$location_c = $configutil->splash_new($_POST["location_c"]);
}
//区
$location_a = 0;
if(!empty($_POST["location_a"])){
$location_a = $configutil->splash_new($_POST["location_a"]);
}	

$errorCode = 0;
$errorMsg = "";

switch($area_leave){
	case 2:		
		
		$lnum = 0;
		//查询本 省级 是否已经添加
		$query_province = "select count(1) as lnum from weixin_commonshop_team_area where isvalid = true  and areaname ='".$location_p."' and customer_id = ".$customer_id;
		$result_province = mysql_query($query_province) or die (" L28 : QUERY_province ERROR : ".mysql_error());
		if($row_province = mysql_fetch_object($result_province)){
			$lnum=$row_province->lnum;
		}
		if($lnum>0){
			$errorCode = 1001;				
			$errorMsg = "<script>alert('本区域  ".$location_p."  已存在，不能重复添加');window.history.go(-1)</script>";
		}else{
			//否则添加
			$query_add = "insert into weixin_commonshop_team_area(grade,isvalid,createtime,customer_id,areaname,area_user,parent_id,all_areaname) values (2,true,now(),".$customer_id.",'".$location_p."',-1,-1,'".$location_p."')";
		}
	break;
	case 1:
		$lnum2 = 0;
		//查询本 市级 是否已经添加
		$query_city = "select count(1) as lnum2 from weixin_commonshop_team_area where isvalid = true  and all_areaname ='".$location_p.$location_c."' and customer_id = ".$customer_id;
		$result_city = mysql_query($query_city) or die (" L28 : QUERY_city ERROR : ".mysql_error());
		if($row_city = mysql_fetch_object($result_city)){
			$lnum2=$row_city->lnum2;
		}
		if($lnum2>0){
			$errorCode = 2001;				
			$errorMsg = "<script>alert('本区域  ".$location_p.$location_c."  已存在，不能重复添加');window.history.go(-1)</script>";
		}else{	
			//查询本 省级 是否已经添加
			$query_find_province =  "select id from weixin_commonshop_team_area where isvalid = true  and areaname ='".$location_p."' and customer_id = ".$customer_id." limit 0,1";
			$provinceID = -1;
			$result_find_province = mysql_query($query_find_province) or die (" L51 : QUERY_find_province ERROR : ".mysql_error());
			//否则添加，是则读取编号id
			if($row_find_province = mysql_fetch_object($result_find_province)){
				$provinceID=$row_find_province->id;
			}else{
				$query_add2 = "insert into weixin_commonshop_team_area(grade,isvalid,createtime,customer_id,areaname,area_user,parent_id,all_areaname) values (2,true,now(),".$customer_id.",'".$location_p."',-1,-1,'".$location_p."')";
				mysql_query($query_add2) or die (" L64 : QUERY_add2 ERROR : ".mysql_error());
				$provinceID = mysql_insert_id(); 
			}
			$query_add = "insert into weixin_commonshop_team_area(grade,isvalid,createtime,customer_id,areaname,area_user,parent_id,all_areaname) values (1,true,now(),".$customer_id.",'".$location_c."',-1,".$provinceID.",'".$location_p.$location_c."')";
		}
	break;
	case 0:
		$lnum3 = 0;
		//查询本 区级 是否已经添加
		$query_area = "select count(1) as lnum3 from weixin_commonshop_team_area where isvalid = true  and all_areaname ='".$location_p.$location_c.$location_a."' and customer_id = ".$customer_id;
		$result_area = mysql_query($query_area) or die (" L84 : QUERY_area ERROR : ".mysql_error());
		if($row_area = mysql_fetch_object($result_area)){
			$lnum3=$row_area->lnum3;
		}
		if($lnum3>0){
			$errorCode = 3001;				
			$errorMsg = "<script>alert('本区域  ".$location_p.$location_c.$location_a."  已存在，不能重复添加');window.history.go(-1)</script>";
		}else{	
			//查询本 省级 是否已经添加
			$query_find_province =  "select id from weixin_commonshop_team_area where isvalid = true  and areaname ='".$location_p."' and customer_id = ".$customer_id." limit 0,1";
			$provinceID = -1;
			$result_find_province = mysql_query($query_find_province) or die (" L95 : QUERY_find_province ERROR : ".mysql_error());
			//否则添加，是则读取编号id
			if($row_find_province = mysql_fetch_object($result_find_province)){
				$provinceID=$row_find_province->id;
			}else{
				$query_add2 = "insert into weixin_commonshop_team_area(grade,isvalid,createtime,customer_id,areaname,area_user,parent_id,all_areaname) values (2,true,now(),".$customer_id.",'".$location_p."',-1,-1,'".$location_p."')";
				mysql_query($query_add2) or die (" L101 : QUERY_add2 ERROR : ".mysql_error());
				$provinceID = mysql_insert_id(); 
			}
			
			//查询本 市级 是否已经添加
			$query_find_city =  "select id from weixin_commonshop_team_area where isvalid = true  and areaname ='".$location_c."' and customer_id = ".$customer_id." limit 0,1";
			$cityID = -1;
			$result_find_city = mysql_query($query_find_city) or die (" L108 : QUERY_find_city ERROR : ".mysql_error());
			//否则添加，是则读取编号id
			if($row_find_city = mysql_fetch_object($result_find_city)){
				$cityID=$row_find_city->id;
			}else{
				$query_add3 = "insert into weixin_commonshop_team_area(grade,isvalid,createtime,customer_id,areaname,area_user,parent_id,all_areaname) values (1,true,now(),".$customer_id.",'".$location_c."',-1,".$provinceID.",'".$location_p.$location_c."')";
				mysql_query($query_add3) or die (" L114 : QUERY_add3 ERROR : ".mysql_error());
				$cityID = mysql_insert_id(); 
			}			
			$query_add = "insert into weixin_commonshop_team_area(grade,isvalid,createtime,customer_id,areaname,area_user,parent_id,all_areaname) values (0,true,now(),".$customer_id.",'".$location_a."',-1,".$cityID.",'".$location_p.$location_c.$location_a."')";
		}
	break;
}

if($errorCode == 0){
echo $query_add;
mysql_query($query_add) or die (" L124 : QUERY ERROR : ".mysql_error());
}else{
	mysql_close($link);
	echo $errorMsg;
	return;
}


mysql_close($link);
$Url = "oneCloud_area.php?customer_id=".passport_encrypt($customer_id);
header("Location: ".$Url); 	
exit;
?>
