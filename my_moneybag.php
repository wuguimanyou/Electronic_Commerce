<?php

header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//require('../back_init.php'); 
require('../common/utility_fun.php');
require('../common/utility_shop.php');

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('select_skin.php');

//头文件----start
require('../common/common_from.php');
//头文件----end

//查购物币余额
$isOpen   = 0;
$currency = 0;
$balance  = 0;
$custom   = '';
//检测用户钱包,购物币并初始化,初始化会员卡
$bag_num = check_moneybag($user_id,$customer_id);
$cur_num = check_currency($user_id,$customer_id);

//微信JSK
require('../common/jssdk.php');
$jssdk = new JSSDK($customer_id);
$signPackage = $jssdk->GetSignPackage();
//微信JSK End

//查询用户购物币等详细信息
/*
$query = "SELECT u.currency,c.custom,c.isOpen FROM weixin_commonshop_user_currency u right JOIN weixin_commonshop_currency c ON u.customer_id=c.customer_id WHERE c.isvalid=TRUE AND u.user_id=".$user_id." AND c.customer_id=".$customer_id."  LIMIT 1";
$result = mysql_query($query) or die('Query failed 28: ' . mysql_error());
while($row = mysql_fetch_object($result)){
	$currency = $row->currency;
	$custom   = $row->custom;
	$isOpen   = $row->isOpen;
}*/
$currency = 0;
$query = "SELECT currency FROM weixin_commonshop_user_currency WHERE isvalid=true AND user_id=$user_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 28: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$currency = $row->currency;
}
$custom = "购物币";
$isOpen = 0;
$query = "SELECT isOpen,custom FROM weixin_commonshop_currency WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 49: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$custom = $row->custom;
	$isOpen = $row->isOpen;
}

//echo $query;die;
$query = "SELECT balance FROM moneybag_t WHERE isvalid=true AND user_id=".$user_id." AND customer_id=".$customer_id." LIMIT 1";
$result= mysql_query($query) or die('Query failed 37: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$balance = $row->balance;
}

//显示保留2位小数（不四舍五入）
$balance 		= cut_num($balance,2);
$currency 	    = cut_num($currency,2);

//代金券
$query = "SELECT COUNT(id) as num FROM weixin_commonshop_couponusers WHERE isvalid=true AND type=1 AND is_used=0 AND deadline>=now() AND customer_id=$customer_id AND user_id=$user_id";
$result= mysql_query($query) or die('Query failed 47: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$num = $row->num;
}
//会员卡数
$vip_num = -1;
$query = "select COUNT(1) as num from weixin_card_members m inner join weixin_cards c on m.card_id=c.id where c.isvalid=true and m.isvalid=true and m.user_id=".$user_id." and c.customer_id=".$customer_id;
$result= mysql_query($query) or die('Query failed 54: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$vip_num = $row->num;
}
//vp值
$vp_val = 0;
$query = "select my_vpscore from weixin_user_vp where isvalid = true and customer_id = ".$customer_id." and user_id = ".$user_id;
$result= mysql_query($query) or die('Query failed 61: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$vp_val = $row->my_vpscore;
	if($vp_val==NULL){
		$vp_val = 0;
	}
}

 //红包数量计算开始
$hongbao_num =0;
$query_red="select id from weixin_red_log where isvalid=true and customer_id=".$customer_id." and type in(1,3) and user_id=".$user_id;
$result_red = mysql_query($query_red) or die('Query failed 69: ' . mysql_error());  
$hongbao_num = mysql_num_rows($result_red); 
 //红包数量计算结束

//查询是否允提现
$isOpen_callback = 0;
$query = "SELECT isOpen_callback FROM moneybag_rule WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
$result= mysql_query($query) or die('Query failed 76: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$isOpen_callback = $row->isOpen_callback;
}

//查询是否代理商
$agent_id = -1;
$query = "SELECT id FROM weixin_commonshop_applyagents WHERE isvalid=true AND user_id=$user_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 103: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$agent_id = $row->id;
}
if($agent_id){
	$sql = "select agent_inventory,agent_getmoney from promoters where  status=1 and isvalid=true and user_id=".$user_id." limit 1";
	$res = mysql_query($sql) or die('Query failed 110: ' . mysql_error());
	while( $row2 = mysql_fetch_object($res) ){
		$agent_inventory = cut_num($row2->agent_inventory,2);//代理库存余额
  		$agent_getmoney = cut_num($row2->agent_getmoney,2);//代理得到的金额
	}
}



?>
<!DOCTYPE html>
<html>
<head>
    <title>我的钱包</title>
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
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" /> 

    

    
</head>
<style>
	.everyValue{font-size:16px;padding:3px 9px;}
	.btns{width:33%!important;float:left;margin-top: 63px;}
	.middle{width:33%!important;height:40px;float:left;padding-top: 20px; height: 90px;}
	.gongneng{width:49%!important;border-right:1px solid #eee;height:75px;border-bottom:0px!important;padding:0!important;}
	.gongneng_img{width:35%;margin-left: 4%;display: inline-block;vertical-align: middle;}
	.gongneng_txt{font-size:16px;color:#999;width:45%;display: inline-block;vertical-align: middle;}
	.gongneng_txt span{font-size: 15px;}
	.qianbao_header{height:90px; width:100%;text-align: center;	}
	.gongneng_img img{width:55px!important;height:55px!important;}
	.no_border{border:none;}
	.gongneng_txt span{width:100%;float:left;color: black;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
	.white-list{background-color: white;border-top: 1px solid #eee;border-bottom: 1px solid #eee;}
	.line{margin-left:0px!important;}
	.list-one{padding-top:7px!important;padding-bottom:7px!important;}
	.gray{padding-left:10px;color:#888;}
	.price{width:70%;height:50px;margin: auto;padding:4px;text-align:center;font-size:27px;text-overflow:ellipsis;}
	.price .money{font-size:20px;}
	.warp{
		width:100%;
		height:100%;

	}
	.warp ul {
		_margin-left: 1%;
		padding: 0;
		margin: 0;
	}
	.warp ul li{
		box-sizing:border-box;
		width:50%;
		height:80px;
		float: left;
		list-style-type: none; 
		background: #fff;
		font-size: 0;
		display: table;
	}
	.warp ul li img{
		width:25%;
		margin-top: 25px;
		margin-left: 10%;
		float: left;
		position: center;
		display: inline-block;
	}
	.warp ul li .righttext{
		line-height: 24px;
		font-size: 14px;
		color:#888;
		width: 58%;
		margin-top: 20px;
    	padding-left: 7%;
    	display: inline-block;
	}
	.textline1,.textline2{
		height: 24px;
		overflow : hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 1;
		-webkit-box-orient: vertical;
	}
	.textline1{
		color: #1c1f20;
	}
	.header{
		width: 100%;
		height:50px;
		background: #1d1e20;
		font-size:17px;
		line-height: 50px;
		text-align: center;
		color: #fff;
		border-radius: 2px;
	}
	.warp ul li:nth-child(odd){
		border-right:1px solid #eee;
		border-bottom: 1px solid #eee;
	}
	.warp ul li:nth-child(even){
		border-bottom: 1px solid #eee;
	}

	//ld 点击效果
        .button{ 
        	-webkit-transition-duration: 0.4s; /* Safari */
        	transition-duration: 0.4s;
        }
        .buttonclick:hover{
        	box-shadow:  0 0 6px 0 rgba(0,0,0,0.24);
        }


</style>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>


<link type="text/css" rel="stylesheet" href="./css/basic.css" />

<body data-ctrl=true style="background:#f8f8f8;">
	<!-- <header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">我的钱包</h1>
	</header>
	
	<div class="topDiv"></div> -->  <!-- 暂时屏蔽头部 -->
	<div id="myInfoDiv" >
        <div class="qianbao_header">
            <div class="area-one btns" >
                <span class="everyValue button buttonclick" onclick="showRecord();">记录</span>
            </div>
            <div class="area-one middle">
            	 <img src="./images/info_image/qianbao_img-orange.png" style="width:55px;height:40px;">
	             <div style="color:#fff">零钱</div>
            </div>
            <div class="area-one btns">
                <span class="everyValue button buttonclick" onclick="showTixian();">提现</span>
            </div>
        </div>
        <div style="width:100%;height:50px;text-align: center;">
        	<div class="price" onclick="showRecord();">
        		<span class="money">￥</span><span><?php echo $balance;?></span>
        	</div>
         </div>
    </div>
    <div class="white-list">
        <div class="list-one" style="">
            <div class="center-content"  style="width:60%;"><span class="gray">功能</span></div>
        </div>
        	<div class="line"></div>

	        <div class="warp">
		        <ul>
		        	<?php if($isOpen==1){?>
		        	<li onclick="view_gouwubi();" class="button buttonclick">
			        	<img src="./images/info_image/gouwubi.png" alt="">
			        	<div class="righttext">
				        	<div class="textline1"><?php echo $custom;?></div>
				        	<div class="textline2">(<?php echo $currency;?>)</div>
			        	</div>
			        </li>
					<?php }?>
		        	<li onclick="view_hongbao();" class="button buttonclick">
			        	<img src="./images/info_image/hongbao.png" alt="">
			        	<div class="righttext">
				        	<div class="textline1">红包</div>
				        	<div class="textline2">(<?php echo $hongbao_num; ?> 个)</div>
				        </div>
			        </li>
			 

		        	<li onclick="view_coupon();" class="button buttonclick">
			        	<img src="./images/info_image/youhuiquan.png" alt="">
			        	<div class="righttext">
				        	<div class="textline1">代金券</div>
				        	<div class="textline2">(<?php echo $num;?> 张)</div>
			        	</div>
			        </li>
			    
			        <li onclick="view_huiyuanka();" class="button buttonclick">
			        	<img src="./images/info_image/huiyuanka.png" alt="">
			        	<div class="righttext">
				        	<div class="textline1">会员卡</div>
				        	<div class="textline2">(<?php echo $vip_num; ?> 张)</div>
			        	</div>
			        </li>
			
			        <li onclick="view_vp();" class="button buttonclick">
			        	<img src="./images/info_image/vpzhi.png" alt="">
			        	<div class="righttext">
				        	<div class="textline1">VP值</div>
				        	<div class="textline2">(<?php echo $vp_val; ?>)</div>
				        </div>
			        </li>
			       
			        <?php if($agent_id>0){?>
			        <li onclick="view_agent();" class="button buttonclick">
			        	<img src="./images/goods_image/20160050302.png" alt="">
			        	<div class="righttext"><div class="textline1">代理进账</div>
			        	<div class="textline2">(￥<?php echo $agent_getmoney; ?>)</div><div>
			        </li>
			        <?php }?>

			        <li onclick="view_pw();" class="button buttonclick">
			        	<img src="./images/info_image/modify_password.png" alt="">
			        	<div class="righttext">
				        	<div class="textline1">支付密码管理</div>
				        	<div class="textline2">(设置/修改)</div>
				        </div>
			        </li>
		        </ul> 	
	        </div>
	        
    </div>
    <!--<img src="./images/info_image/wodeqianbao_background.png" style="width:100%;margin-top:50px;">-->

    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
</body>		

<script type="text/javascript">
   var winWidth = $(window).width();	
   var winheight = $(window).height();
   var customer_id_en = '<?php echo $customer_id_en; ?>';
   function showRecord(){

    	window.location.href="my_moneybag_log.php?customer_id="+customer_id_en;
   }
   function showTixian(){
   		<?php if($isOpen_callback == 1){?>
    		window.location.href="money_tocash.php?customer_id="+customer_id_en;
    	<?php }else{?>
    		showAlertMsg("提示","商家不允许提现","确定");
    	<?php }?>
   }

   function view_gouwubi(){
    	window.location.href="my_currency.php?customer_id="+customer_id_en;
   }
   function view_hongbao(){
    	window.location.href="my_hongbao.php?customer_id="+customer_id_en;
   }
   function view_coupon(){
    	window.location.href="coupon.php?customer_id="+customer_id_en;
   }
   function view_huiyuanka(){
    	window.location.href="vip_card_list.php?customer_id="+customer_id_en;
   }
   function view_vp(){
    	window.location.href="my_vp.php?customer_id="+customer_id_en;
   } 
   function view_agent(){
    	window.location.href="my_agent_log.php?customer_id="+customer_id_en;
   }  
   function view_pw(){
   		window.location.href="modify_password.php?customer_id="+customer_id_en;
   } 
</script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script type="text/javascript">
wx.config({
	debug: false,
	appId: '<?php echo $signPackage["appId"];?>',
	timestamp: <?php echo $signPackage["timestamp"];?>,
	nonceStr: '<?php echo $signPackage["nonceStr"];?>',
	signature: '<?php echo $signPackage["signature"];?>',
	jsApiList: [
	// 所有要调用的 API 都要加到这个列表中
	'hideOptionMenu',

	]
});
wx.ready(function () {
	// 在这里调用 API
	wx.hideOptionMenu();

});

//ld 添加记录、自提button按钮点击效果
$("#showRecord").hover(function(){
	$("#showRecord").css("background-color","#d6d6d6");
},
function(){
	$("#showRecord").css("background-color","white");
});

$("#showTixian").hover(function(){
	$("#showTixian").css("background-color","#d6d6d6");
},
function(){
	$("#showTixian").css("background-color","white");
});

/*
//ld 购物币等添加点击效果
$("#view_gouwubi").hover(function(){
  $("#view_gouwubi").css("background-color","#d6d6d6");
},
function(){
  $("#view_gouwubi").css("background-color","white");
});

$("#view_hongbao").hover(function(){
  $("#view_hongbao").css("background-color","#d6d6d6");
},
function(){
  $("#view_hongbao").css("background-color","white");
});

$("#view_pw").hover(function(){
  $("#view_pw").css("background-color","#d6d6d6");
},
function(){
  $("#view_pw").css("background-color","white");
});

$("#view_agent").hover(function(){
  $("#view_agent").css("background-color","#d6d6d6");
},
function(){
  $("#view_agent").css("background-color","white");
});

$("#view_vp").hover(function(){
  $("#view_vp").css("background-color","#d6d6d6");
},
function(){
  $("#view_vp").css("background-color","white");
});

$("#view_huiyuanka").hover(function(){
  $("#view_huiyuanka").css("background-color","#d6d6d6");
},
function(){
  $("#view_huiyuanka").css("background-color","white");
});

$("#view_coupon").hover(function(){
  $("#view_coupon").css("background-color","#d6d6d6");
},
function(){
  $("#view_huiyuanka").css("background-color","white");
});

*/

</script>

<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<?php require('../common/share.php'); ?>
<!--引入侧边栏 end-->
</html>