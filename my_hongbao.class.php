<?php
header("Content-type: text/html; charset=utf-8"); //svn
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../proxy_info.php');
//require('../common/common_from.php');
$user_id = -1;
if(!empty($_POST["user_id"])){
	$user_id     =  $configutil->splash_new($_POST["user_id"]);
	$user_id     =  passport_decrypt((string)$user_id);
}
//echo $user_id;die;

$info = array();
$id   =  -1;
		$red_money =   0;
		$createtime=  "";
		$remark    =  "";
	    $query = "SELECT id,red_money,createtime,remark FROM weixin_red_log WHERE customer_id=".$customer_id." AND user_id=".$user_id;
        $result = mysql_query($query);
        while($row=mysql_fetch_object($result)){
          $id        = $row->id;
          $red_money = $row->red_money;
          $createtime= $row->createtime;
          $remark    = $row->remark;
          $remark    = mb_substr($remark,0,4,'utf-8');
		  $arr = array(
			'id'=>$id,
			'red_money'=>$red_money,
			'createtime'=>$createtime,
			'remark'=>$remark,
		  );
		  array_push($info,$arr);
		}
echo json_encode($info);
?>