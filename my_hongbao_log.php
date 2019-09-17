<?php
header("Content-type: text/html; charset=utf-8");
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//include_once("./WeChatPay/weipay_new/WxPayPubHelper/WxPayPubHelper_redbag.php");
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');


//头文件----start
require('../common/common_from.php');
//头文件----end
//$user_id = 300647;
if(!empty($_GET["id"])){
    $red_id = $configutil->splash_new($_GET["id"]);
}
$id              = -1;
$red_money       = -1;
$createtime      = "";
$remark          = "";
$type            = -1;
$deal_id         = "";
$weixin_red_id   = "";
$customer_red_id = "";
$query = "select id,red_money,createtime,remark,type,deal_id,weixin_red_id,customer_red_id from weixin_red_log where isvalid=true and user_id=".$user_id." and customer_id=".$customer_id." and id=".$red_id;

$result= mysql_query($query) or die('Query failed 551: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$id              = $row->id;
	$red_money       = $row->red_money;
	$createtime      = $row->createtime;
	$remark          = $row->remark;
	$type            = $row->type;
	$deal_id         = $row->deal_id;
	$weixin_red_id   = $row->weixin_red_id;
	$customer_red_id = $row->customer_red_id;
}
//echo $query;die;
if($type==1){
	$type = "佣金红包";
}else if($type==2){
	$type = "用户组红包";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>红包明细</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="no" name="apple-touch-fullscreen">
    <meta name="MobileOptimized" content="320"/>
    <meta name="format-detection" content="telephone=no">
    <meta name=apple-mobile-web-app-capable content=yes>
    <meta name=apple-mobile-web-app-status-bar-style content=black>
    <meta http-equiv="pragma" content="nocache">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">
    
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
    <link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />    
    
    
<style>
	.my_info{width:100%;height:60px;line-height:60px;background-color:white;padding-left:10px;border-bottom:1px solid #eee;}
	.detail{width:90%;height:50px;line-height:50px;float:left;margin-left:5%;border-bottom:1px solid #ececec;}
	.detail_left{width:35%;float:left;padding-left:15px;color:#707070;}
	.detail_right{line-height:50px;width:65%;float:right;color:black;text-align:right;padding-right:15px;}
	.container{margin-top:10px;border-top:1px solid #eee;border-bottom:1px solid #eee;float:left;background-color:white;}
	.detail_left span{line-height:50px;letter-spacing: 25px;}
	.detail_right span{color:#1c1f20;font-weight:200;}
	.red{color:red;font-size:30px;font-weight:200;}
	.left{width:60%;float:left;padding-left:22px;color:#707070;font-size:16px;text-align: left;}
	.right{width:40%;float:right;color:black;text-align:right;padding-right:15px;font-size:30px;}
	.beizhu{border-bottom:none;height:auto;line-height:30px;}
	.danhao{overflow: hidden; text-overflow:ellipsis}
</style>

</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#f8f8f8;">
    
    <div class="my_info">
        <div class="left" ><span>红包金额</span></div>
    	<div class="right" ><span class="red">￥<?php echo $red_money;?></span></div>
    </div>
    <div class="container">
    	<div class="detail">
        	<div class="detail_left"><span>状态</span></div>
    		<div class="detail_right"><span><?php echo $type;?></span></div>
    	</div>
    	<div class="detail">
        	<div class="detail_left"><span>时间</span></div>
    		<div class="detail_right"><span><?php echo $createtime; ?></span></div>
    	</div>
    	<div class="detail danhao">
        	<div class="detail_left"><span style="letter-spacing: 0px;">交易单号</span></div>
    		<div class="detail_right"><span><?php echo $weixin_red_id;?></span></div>
    	</div>
    	<div class="detail">
        	<div class="detail_left"><span style="letter-spacing: 0px;">红包单号</span></div>
    		<div class="detail_right"><span><?php echo $customer_red_id;?></span></div>
    	</div>
    	<div class="detail">
        	<div class="detail_left"><span style="letter-spacing: 0px;">领取时间</span></div>
    		<div class="detail_right"><span><?php echo $createtime;?></span></div>
    	</div>
    	<div class="detail beizhu">
        	<div class="detail_left"><span>备注</span></div>
    		<div class="detail_right"><?php echo $remark;?></div>
    	</div>
    </div>
    
    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
</body>		

<script type="text/javascript">
    var winWidth = $(window).width();
    var winheight = $(window).height();
    function gotoHongBao(){	  //红包单号
    	alert("红包单号");
    }
    function lingqian(){   //零钱交易单号
    	alert("零钱交易单号");
    }
</script>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>