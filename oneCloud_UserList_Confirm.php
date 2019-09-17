<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$keyid =  $configutil->splash_new($_POST["keyid"]);
$user_id =  $configutil->splash_new($_POST["user_id"]);
$customer_id =  $configutil->splash_new($_GET["customer_id"]);
$customer_id = passport_decrypt((string)$customer_id);
$type = $configutil->splash_new($_POST["type"]);//type 1:通过审核；2:驳回申请 3：删除记录
	switch($type){
		case 1:// 1:升级消费无限级奖励；
			$sql = "update weixin_commonshop_team_aplay set status=1  where isvalid=true and customer_id=".$customer_id." and aplay_user_id=".$user_id." and id=".$keyid;
			mysql_query($sql)or die('W_1 Query failed: ' . mysql_error()); 
			$query_teamaplay = "SELECT status FROM weixin_commonshop_team_aplay where isvalid=true and customer_id=".$customer_id." and aplay_user_id=".$user_id." and id=".$keyid;
			$status = -1;
		    $result_teamaplay = mysql_query($query_teamaplay) or die('Query_teamaplay W_1 failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result_teamaplay)) {
				$status = $row->status;			//申请状态 0：审核  1：确认
			}
			if($status==1){
				exit('{"status": 1001,"keyid": '.$keyid.', "errorMsg":"代理资格审核通过", "status_str":"审核通过"}'); 
			}else{
				exit('{"status": 1004,"keyid": '.$keyid.', "errorMsg":"代理资格审核失败", "status_str":"审核中"}'); 
			} 
		break; 
		
		case 2:	//2:驳回省市区代理资格;
			$sql = "update weixin_commonshop_team_aplay set status=-1  where isvalid=true and customer_id=".$customer_id." and aplay_user_id=".$user_id." and id=".$keyid;
			mysql_query($sql)or die('W_2 Query failed: ' . mysql_error()); 
			exit('{"status": 1002,"keyid": '.$keyid.',  "errorMsg":"代理资格驳回申请", "status_str":"驳回申请"}'); 
			$query_teamaplay = "SELECT status FROM weixin_commonshop_team_aplay where isvalid=true and customer_id=".$customer_id." and aplay_user_id=".$user_id." and id=".$keyid;
			$status = -1;
		    $result_teamaplay = mysql_query($query_teamaplay) or die('Query_teamaplay W_1 failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result_teamaplay)) {
				$status = $row->status;			//申请状态 0：审核  1：确认
			}
			if($status==1){
				exit('{"status": 1002,"keyid": '.$keyid.', "errorMsg":"代理资格驳回成功", "status_str":"驳回申请"}'); 
			}else{
				exit('{"status": 1004,"keyid": '.$keyid.', "errorMsg":"代理资格驳回失败", "status_str":"审核中"}'); 
			} 
		break;
		 
		 case 3:	//删除
			$now_area = 0;
			$query_area = "select now_area from weixin_commonshop_team_aplay where isvalid=true and customer_id=".$customer_id." and id=".$keyid;
			$result_area = mysql_query($query_area) or die("W_3_1 Query_area error : ".mysql_error());
			$now_area = mysql_result($result_area,0,0);
			if($now_area>0){
				exit('{"status": 1005,"keyid": '.$keyid.',  "errorMsg":"分配区域状态不能删除", "status_str":""}'); 
			}
		 
			$sql = "update weixin_commonshop_team_aplay set isvalid=0 and status=-1 where isvalid=true and customer_id=".$customer_id." and aplay_user_id=".$user_id." and id=".$keyid;
			mysql_query($sql)or die('W_3 Query failed: ' . mysql_error()); 
			
			$query_teamaplay = "SELECT isvalid FROM weixin_commonshop_team_aplay where customer_id=".$customer_id." and aplay_user_id=".$user_id." and id=".$keyid;
			$isvalid = true;
		    $result_teamaplay = mysql_query($query_teamaplay) or die('Query_teamaplay W_1 failed: ' . mysql_error());
			while ($row = mysql_fetch_object($result_teamaplay)) {
				$isvalid = $row->isvalid;			//申请状态 0：审核  1：确认
			}
			if($isvalid){
				exit('{"status": 1004,"keyid": '.$keyid.',  "errorMsg":"记录删除失败", "status_str":""}'); 
			}else{
				exit('{"status": 1003,"keyid": '.$keyid.',  "errorMsg":"记录删除成功", "status_str":""}'); 
			} 
		 break;
	}	
mysql_close($link);	
?>

	