<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$customer_id = $_SESSION["customer_id"];

require('../customer_id_decrypt.php');
require('../common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

//头文件----start
require('../common/common_from.php');
require('select_skin.php');
//头文件----end

?>
<!DOCTYPE html>
<html>
<head>
    <title>我的足迹</title>
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
    
    

    <link rel="stylesheet" id="wp-pagenavi-css" href="./css/list_css/pagenavi-css.css" type="text/css" media="all">
	<link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">
	<link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    
<style>  
	.am-header-icon-custom{height:16px;margin-left:2px;}
	.white-list{background-color: white;border-top: 1px solid #DEDBD5;border-bottom: 1px solid #DEDBD5;}
	.right-actionImg{width: 14px;height: 19px;}
	.cost-title{background:transparent;padding: 7px;}
	.pinterestUl{padding:4px !important;margin-bottom:-64px !important;transition: height 1s !important; }
	.list{padding:2px;margin-top:2px;width:97%;background: #fff;height: auto;}
	.listImg{width: 30%;float: left;min-width:70px;}
	.listImgBlow{width: 67%;float: right;margin-top: 5px;padding: 5Px;margin-right: 6px;}
	.listTitle{margin-top: -10px;}
	.pinterest_title{overflow: hidden;max-height: 38px;line-height: 19px;font-size:13px;color: #1c1f20;text-indent: 36px;padding-top: 2px;}	
	  #topDivSel2{width:100%;height:45px;line-height:45px;padding:0px 10px;;background-color:white;}
	  .plus-tag-add{width:100%; overflow:auto;min-width:100px;line-height:50px;}
	  .plus-tag-add-left{float:left;color: #5f6366;}
	  .plus-tag-add-right{float:right; margin-right:10px;height:45px;line-height:45px;}
	  .font-red{color:red;}
	  .plus-tag-add-right-button{padding: 5px 10px; border: 1px solid #2e2e2e; color:#2e2e2e;}
	  .my-entry-content{clear:both;}
	  #pinterestList{background:#f8f8f8;}
	  .list dd{margin-bottom:0px !important;}
	  #product{width:50%;height:45px;line-height:45px;float:left;text-align:center;}
	  #shop{width:50%;height:45px;line-height:45px;float:left;text-align:center;}
	  .topDivSel{width:100%;height:45px;padding-top:0px;background-color:#f8f8f8;}
	  #touming{background-color: rgba(255, 255, 255, 0)!important;border-color: rgba(255, 255, 255, 0)!important;}
	  .h25{height:25px;}
	  .out{opacity:0.3;}
      .middleinDiv span{line-height:12px}	  
      .am-btn{background-color:#F37B1D;border-color:#F37B1D;}
</style>


</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop' style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#fff;">
	<!-- <header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">我的足迹</h1>
	    <div class="am-header-right am-header-nav" onclick="bianjiShangpin();">
			<img class="am-header-icon-custom" src="./images/center/nav_home.png" />
		</div>
	</header>
	<div class="topDiv"></div> --> <!-- 暂时屏蔽头部 -->
	    <div class="topDivSel" style="">
		    <div class="plus-tag-add" style="color:rgb(174, 174, 174);padding-left:0px;">
				<div id="product" class="selected" onclick="viewMyFavourite('product');">商品</div>
				<div id="shop" onclick="viewMyFavourite('shop');">店铺</div>
			</div>
	    </div>
	    <div style="height:45px;"></div>  <!-- 占据选项框的位置 -->
	    <div class="topDivSel" id = "topDivSel2">
		    <div class="plus-tag-add" id = "plus-tag-add1">
		    	<div class = "plus-tag-add-left" ><span>共浏览<font class = "font-red">0</font>个商品</span></div>
		    	<div class = "plus-tag-add-right"><span class = "plus-tag-add-right-button" ty='1' id = "bianji-btn1"> 编辑</span></div>	
		    </div>
		    <div class="plus-tag-add" id = "plus-tag-add2" style="display:none;">
		    	<div class = "plus-tag-add-left"><span>共浏览<font class = "font-red">0</font>个店铺</span></div> 
		    	<div class = "plus-tag-add-right"><span class = "plus-tag-add-right-button" id = "bianji-btn2"> 编辑</span></div>	
		    </div>
	    </div>
    	<div style="height:45px;"></div>  <!-- 占据选项框的位置 -->
    <!--- <div style="text-align: center;width:100%;">            
        <div style="text-align:center;font-weight: bold;padding:10px 0px 5px 0px;"></div>            
    </div> -->
    <!-- 浏览商品列表 start -->
    <div class="productDiv" id="productDiv">
		<!-- 商品列表 start -->
	    	<div class="entry-content my-entry-content">
				<ul class="pinterestUl col" id="pinterestList" fixcols="1" >
					<!-- 商品列表-->
				</ul>
				<p id="pinterestMore" style="display: block;">----- 向下滚动加载更多 -----</p>
				<p id="pinterestDone">----- 加载完毕 -----</p>
			</div><!-- .entry-content -->
			<!--<script src="./js/r_pinterest1.js" type="text/javascript"></script>-->
    	</div>
    	<!-- 商品列表 end -->
	</div>
    <!-- <div id="productContainerDiv" style="width:100%;margin-top:101px;">
        
    	
    </div> -->
    <!-- 浏览店铺列表 start -->
    <!-- 推荐商品列表 end -->

    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
	<script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
	<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
    </body>	
<script>
//初始化
var config = {
	customer_id:'<?php echo $customer_id?>',
	customer_id_en:'<?php echo $customer_id_en?>',
	user_id:'<?php echo $user_id?>',
}
</script>	
<script type="text/javascript" src="./js/my_visit.js"></script>

</body>
</html>