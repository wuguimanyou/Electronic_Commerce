<?php

header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//require('../back_init.php'); 

require('../common/utility_fun.php');

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('select_skin.php');
//头文件----start
require('../common/common_from.php');
//头文件----end

// //个人数据文件----start
// require('../common/own_data.php');
// //个人数据文件----end
// $info = new my_data();//own_data.php my_data类
// $total_money  = $info->my_total_commission_money($customer_id,$user_id);//
// $total_profiy = $info->my_total_profiy_money($customer_id,$user_id);//



?>
<!DOCTYPE html>
<html>
<head>
    <title>累积收益</title>
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


    <link type="text/css" rel="stylesheet" href="./css/extends_css/extends.css" />
    <link rel="stylesheet" id="wp-pagenavi-css" href="./css/list_css/pagenavi-css.css" type="text/css" media="all">
    <link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">
    
    <link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    <!-- <link href="./css/goods_css/mobiscroll/bootstrap.min.css" rel="stylesheet" type="text/css">  -->
    <!-- Mobiscroll JS and CSS Includes -->
    <!-- <link href="./css/goods_css/mobiscroll/mobiscroll.custom-2.17.1.min.css" rel="stylesheet" type="text/css">
    <script src="./js/goods_js/mobiscroll/mobiscroll.custom-2.17.1.min.js" type="text/javascript"></script> -->
	
    
<style>
	#middle-tab .area-one {float:left;width:25%!important;padding-bottom: 0px !important; padding-top: 0px !important;border-bottom:none!important;}
	#middle-tab .area-one .item{ height:50px;line-height:50px;margin-top:0px !important;font-size:15px!important;margin-top: 0px!important;}
	#middle-tab .area-one div{margin-top: 0px!important;}
	.menu_selected{position:absolute;font-size:15px;top:47px;left:45%;}
	.my_info{width:100%;height:65px;line-height:65px;background-color:white;padding-left:10px;border-bottom:1px solid #d1d1d1;}
	.content-base-size{float: left;width: 60px;height: 30px;line-height: 27px;margin-left: 3px;margin-top: 5px;border: 1px solid #c4c4c4;background-color: white;color: #707070;font-size: 12px;}
	.info_left{width:40%;float:left;}
	.info_left .up{width:100%;float:left;text-align:left;line-height: 30px;color:#1c1f20;height:30px;}
	.info_left .down{width:100%;float:left;text-align:left;line-height: 15px;color:#a1a1a1;}
	.info_middle{width:20%;float:left;color:rgb(183, 183, 183);font-size:15px;}
	.info_right{width:40%;float:right;color:black;text-align:right;padding-right:10px;font-size:23px;}
    .area-line{height:40px;width:1%;float:left;margin-top: 25px;padding-top: 20px;border-left:1px solid #fff;}
    .area-one{width:49%;float:left;color:white;padding-top: 10px;}
     #detail-count{height:90px; width:100%;text-align: center;}
    .big_number{font-size:25px;color:white;}
    .big_txt{font-size:15px;color:white;margin-top: 10px;}
    .period{width:100%;height:50px;text-align: center;}
    .period_left{width: 55%;height: 40px;float: left;line-height: 40px;text-align: left;padding-left: 10px;}
    .period_right{width:45%;height:50px;float:right;padding:10px;text-align:right;}
    .period_left img{margin-left: 5px;width: 17px;vertical-align: middle;}
    .period_right img{height:14px;vertical-align:middle;float: right;padding-left: 10px;}
    .tis{text-align: center;font-family: "微软雅黑";font-size: 18px;color:#ccc;margin-top: 10%;display: none}
    .loading{text-align: center;font-family: "微软雅黑";font-size: 18px;color:#ccc;margin-top: 20px;}
	/*.am-calendar {position: relative;margin-top:-200px;}*/
    .data_box p{text-align:center;}
    .big_txt{color:#efefef;padding-bottom: 10px;font-size:14px;}
    .big_txt span{color:#efefef}
</style>

</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#fff;">

    <div id="myInfoDiv" >
        <div class="period">
        	<div class="period_left" onclick="showSearch();">
        		<img src="./images/center/nav_home.png" />
        		<span id="search_type" style="vertical-align: middle;">全部</span>
        	</div>
        	<div  class="period_right" >
                 <img src="./images/info_image/arrow.png" />
                <span>
                    <div class="time" onclick="showtime();">选择日期</div>
                </span>
               
        	</div>
        </div>
        <div class="data_box">
            <p style="font-size:18px;padding-bottom:10px" class="Mon"></p>
            <p style="font-size:30px;letter-spacing: 2px;" class="all_money"></p>
        </div>
        <div style="overflow:hidden">
        	<div style="width:100%;height:60px;float:right;padding-top:40px;text-align:center;" onclick="showDetailGraph();">
        		<span style="vertical-align: middle;font-size:15px;margin-left: 14px;">查看具体参数与报表</span>
                <img src="./images/info_image/arrow.png" style="margin-right:5px;margin-left:10px;height:14px;vertical-align:middle;"/>
        	</div>
        </div>        
        <div id="detail-count">
            <div class="area-one">
                <div class="big_txt">待结算</div>            
                <div class="big_number" id="send_money"></div>
            </div>
            <div class="area-one">
                <div class="big_txt">已结算</div>            
                <div class="big_number" id="hold_money"></div>
            </div>
        </div>
    </div>
    <div class="contentlist">

    </div>
    <div class="tis" style="padding-bottom:10px;">---暂无更多数据---</div>
    <div class="loading" style="padding-bottom:10px;">---正在加载中---</div>
    <!-- Marsk Start-->
	 <div id="leftmask" style="display:none;z-index:999;" data-role="none"></div>
	 <div class="search_new" id="seardiv"  style="display:none;z-index:1000;" data-role="none">	
		    <!-- 价格区间 -->
			<ul  class="div_mo"  id="modiv" style="width:100%;">    
				<div id="buts" style="text-align:center;padding:12px;float:left;">
				    
	            </div>
			    <div style="padding:16px;margin-top:30px;width:100%;">
	                <button class="small-type-button6"  type="button" onclick="popClose();" style="width:100%;">确定</button>
	            </div>
			</ul>
	  
	 </div>
    <!-- Marsk End-->
    <input type="hidden" id="IsEnd" value="0">
    <input type="hidden" class="Search" value="1">
    <input type="hidden" id="date" value="">


    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
</body>
<script src="./js/extends_js/monthCtrl_profit.js" type="text/javascript" charset="utf-8"></script><!--日历-->

<script type="text/javascript">
    var customer_id_en = "<?php echo $customer_id_en;?>";
</script>
<script type="text/javascript" src="./js/my_profit.js"></script>    
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>