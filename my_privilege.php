<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../proxy_info.php');
require('../common/utility.php');
require('select_skin.php');
/*require('../common/jssdk.php');
$jssdk = new JSSDK($customer_id);
$signPackage = $jssdk->GetSignPackage();*/
//头文件----start
require('../common/common_from.php');
//头文件----end
// if(!empty($_GET["user_id"])){
    // $user_id=$configutil->splash_new($_GET["user_id"]);
    // $user_id = passport_decrypt($user_id);
// }else{
    // if(!empty($_SESSION["user_id_".$customer_id])){
        // $user_id=$_SESSION["user_id_".$customer_id];
    // }
// }
$query = "select name,weixin_headimgurl,weixin_name from weixin_users where isvalid=true and customer_id=".$customer_id." and id=".$user_id." limit 0,1";
$result = mysql_query($query) or die('query failed'.mysql_error());
$promoter_name	   = '';	//用户姓名
$weixin_headimgurl = '';	//微信头像
$weixin_name	   = '';	//微信名
while($row = mysql_fetch_object($result)){
	$promoter_name 	   = $row->name;
	$weixin_headimgurl = $row->weixin_headimgurl;
	$weixin_name 	   = $row->weixin_name;
}
if($weixin_headimgurl == NULL || $weixin_headimgurl == ''){
	$weixin_headimgurl = './images/my_headimgurl.png';
}
if(empty($weixin_name) && !empty($promoter_name)){
	$weixin_name = $promoter_name;
}
// echo $weixin_headimgurl;
$query2 = "select id,status,commision_level,is_consume from promoters where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id;
$result2 = mysql_query($query2) or die('query failed2'.mysql_error());
$status		 	 = -1;	//推广员状态 1:审核通过 0:审核中
$promoter_id 	 = -1;	//推广员ID
$commision_level =  1;	//推广员等级
$is_consume      =  0;	//股东等级
while($row2 = mysql_fetch_object($result2)){
   $promoter_id 	= $row2->id;
   $status 			= $row2->status;
   $commision_level = $row2->commision_level;
   $is_consume 		= $row2->is_consume;
   break;
}

$exp_name 	    = '推广员';
$is_ncomission  = 0; 	//是否开启3*3
$isOpenAgent    = 0;	//是否开启代理商申请
$isOpenSupply   = 0;	//是否开启供应商申请
$is_team  		= 0;	//是否开启团队奖励
$is_shareholder = 0;	//是否开启股东分红奖励
$query3 = "select exp_name,isOpenAgent,isOpenSupply,is_ncomission,is_team,is_shareholder from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result3 = mysql_query($query3) or die('query failed3'.mysql_error());
while($row3 = mysql_fetch_object($result3)){
	$exp_name 	   	= $row3->exp_name;
	$exp_name_1 	=$exp_name;
	$is_ncomission 	= $row3->is_ncomission;
	$isOpenAgent   	= $row3->isOpenAgent;
	$isOpenSupply  	= $row3->isOpenSupply;
	$is_team  		= $row3->is_team;
	$is_shareholder = $row3->is_shareholder;
}

$query4="select isAgent from promoters where  status=1 and isvalid=true and user_id=".$user_id." and  customer_id=".$customer_id;
$result4 = mysql_query($query4) or die('query failed4'.mysql_error());
$isAgent = 0;	//0:普通推广员；1：代理；2：顶级推广员；3：供应商；4：技师；5：区代；6：市代；7：省代;8:自定义区域
while($row4 = mysql_fetch_object($result4)){
	$isAgent = $row4->isAgent;
	break;
}

//区域代理自定义
$query_team="select is_showuplevel,is_showcustomer,p_customer,c_customer,a_customer,diy_customer,is_diy_area from weixin_commonshop_team where isvalid=true and customer_id=".$customer_id;
$is_showuplevel_t = 1;				//是否开启区域代理升级
$is_showcustomer  = 1;				//开启自定义名称:0关，1开
$is_diy_area 	  = 0;				//开启自定义区域:0关，1开
$p_customer		  ="省级代理/";		//省代自定义名称
$c_customer		  ="市级代理/";		//市代自定义名称
$a_customer		  ="区级代理/";		//区代自定义名称
$diy_customer	  ="自定义区域代理/";	//自定义级别自定义名称
$result_team = mysql_query($query_team) or die('query_team failed'.mysql_error());  
while($row = mysql_fetch_object($result_team)){
	$is_showuplevel_t  	  = $row->is_showuplevel;
	$is_showcustomer 	  = $row->is_showcustomer;	
	$p_name_customer      = $row->p_customer;
	$c_name_customer      = $row->c_customer;
	$a_name_customer      = $row->a_customer;
	$diy_name_customer 	  = $row->diy_customer;
	$is_diy_area 	  	  = $row->is_diy_area;
}
if(true==$is_showcustomer){
	$p_customer = $p_name_customer;
	$c_customer = $c_name_customer;
	$a_customer = $a_name_customer;
	if(1 == $is_diy_area){
		$diy_customer = $diy_name_customer;
	}
}

/* 推广员等级 start*/
if(1 == $is_ncomission){
	$query5 = "select exp_name from weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." and level=".$commision_level." limit 0,1";
	$result5 = mysql_query($query5) or die('query failed5'.mysql_error());
	while($row5 = mysql_fetch_object($result5)) {	
		$exp_name = $row5->exp_name;
	}
}
/* 推广员等级 end*/
$identity_name = "";
switch($isAgent){
	case 1:
		$identity_name = "代理商";
		break;
	case 3:
		$identity_name = "供应商";
		break;
	case 5:
		$identity_name = $a_customer;  
		break;
	case 6:
		$identity_name = $c_customer;
		break;
	case 7:
		$identity_name = $p_customer;
		break;
	case 8:
		$identity_name = $diy_customer;
		break;						
}

//股东等级
$query6 = "select a_name,b_name,c_name,d_name,is_showuplevel from weixin_commonshop_shareholder where isvalid=true and customer_id=".$customer_id;
$result6 = mysql_query($query6) or die('query failed6'.mysql_error());
$a_name = '';			//股东名称
$b_name = '';			//总代理名称
$c_name = '';			//渠道名称
$d_name = '';			//代理名称
$is_showuplevel_s = 1;	//是否开启股东升级
while($row6 = mysql_fetch_object($result6)){
	$a_name 		  = $row6->a_name;
	$b_name 		  = $row6->b_name;
	$c_name 		  = $row6->c_name;
	$d_name 		  = $row6->d_name;
	$is_showuplevel_s = $row6->is_showuplevel;
}
$shareholder_name = '';
switch($is_consume){
	case 0:
		break;
	case 1:
		$shareholder_name = $d_name;
		break;
	case 2:
		$shareholder_name = $c_name;
		break;
	case 3:
		$shareholder_name = $b_name;
		break;
	case 4:
		$shareholder_name = $a_name;
		break;
}

$agent_id	  = -1;
$agent_status = 0;
$agent_name	  = '';
$query7 = "select id,status,agent_name from weixin_commonshop_applyagents where isvalid=true and user_id=".$user_id;
$result7 = mysql_query($query7) or die('Query failed7' . mysql_error());
while ($row7 = mysql_fetch_object($result7)) {
	$agent_id 	  = $row7->id;			//判断是否已经提交过申请;
	$agent_status = $row7->status;		//判断申请状态
	$agent_name	  = $row7->agent_name;	//代理商级别
	break;
}

$supply_id	   = -1;
$supply_status = 0;
$shopName	   = '';
$query8 = "select id,status,shopName from weixin_commonshop_applysupplys where isvalid=true and user_id=".$user_id;
$result8 = mysql_query($query8) or die('Query failed8' . mysql_error());
while ($row8 = mysql_fetch_object($result8)) {
	$supply_id	   = $row8->id;			//判断是否已经提交过申请;
	$supply_status = $row8->status;		//判断申请状态
	$shopName 	   = $row8->shopName;	//供应商名称
	break;
}

$team_id 	 = -1;		//区域代理申请ID
$aplay_grate = -1;		//0：区代；1：市代；2：省代 3：自定义
$team_status = -1;		//状态：0审核，1确认
$apply_name  = '';		//申请区域代理名称
$query3 = "select id,aplay_grate,status from weixin_commonshop_team_aplay where isvalid=true and customer_id=".$customer_id." and aplay_user_id=".$user_id." limit 0,1";
$result3 = mysql_query($query3) or die('query failed3'.mysql_error());
while($row3 = mysql_fetch_object($result3)){
	$team_id 	 = $row3->id;
	$aplay_grate = $row3->aplay_grate;
	$team_status = $row3->status;
}
if(0 == $aplay_grate){
	$apply_name = $a_customer;
}else if(1 == $aplay_grate){
	$apply_name = $c_customer;
}else if(2 == $aplay_grate){
	$apply_name = $p_customer;
}else if(3 == $aplay_grate){
	$apply_name = $diy_customer;
}

//判断渠道是否开启代理商功能---start
$is_disrcount = 0;
$is_agent 	  = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城代理模式' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_agent Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_agent = 1;
}
//判断渠道是否开启代理商功能---end
//判断渠道是否开启供应商功能---start
$is_disrcount = 0;
$is_supply 	  = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城供应商模式' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_supply Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_supply = 1;
}
//判断渠道是否开启供应商功能---end	
//判断渠道是否开启区域团队功能---start
$is_disrcount = 0;
$is_areaAgent = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城区域团队奖励' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_areaAgent Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_areaAgent = 1;
}
//判断渠道是否开启区域团队功能---end	
//判断渠道是否开启股东分红功能---start
$is_disrcount 	= 0;
$is_OpenShareholder = 0;
$query1="select count(1) as is_disrcount from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.sys_name='商城股东分红奖励' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('W228 is_OpenShareholder Query failed: ' . mysql_error());  
while ($row = mysql_fetch_object($result1)) {
	$is_disrcount = $row->is_disrcount;
	break;
}
if($is_disrcount>0){
	$is_OpenShareholder = 1;
}
//判断渠道是否开启股东分红功能---end	
	
?>
<!DOCTYPE html>
<html>
<head>
    <title>我的特权</title>
    <!-- 模板 -->
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
  
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/vic.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/global.css" />
    <link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />
	<?php if($status!=1){?>
    <link type="text/css" rel="stylesheet" href="./css/goods/tuiguangyuan1-1fensi.css" />
	<?php }else{?>
	<link type="text/css" rel="stylesheet" href="./css/goods/tuiguangyuan1-1tuiguangyuan.css" />
	<?php }?>

	<link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" /> 
    <style>
        .item-left2-row1{
            line-height:20px
        }
        .item-left2-row2{
            line-height:20px;
			word-break:break-all;
        }
        .item-left2{
            padding-top:12px;
            width: 70%;
        }
        .content-intro-row3 img{
        	width: 16px;
        	height: 16px;
        	vertical-align: middle;
        	background-size: cover;
        }

        //ld 点击效果
        .button{ 
        	-webkit-transition-duration: 0.4s; /* Safari */
        	transition-duration: 0.4s;
        }

        .buttonclick:hover{
        	box-shadow:  0 0 16px 0 rgba(0,0,0,0.24);
        }
        .buttonclick_wrapper1:hover{
        	box-shadow:  0 0 16px 10px rgba(0,0,0,0.24);
        }
    </style>
    <!-- 页联系style-->
</head>
<body data-ctrl=true>
	<!-- header部门-->
	<!-- <header data-am-widget="header" class="am-header am-header-default header">
		<div class="am-header-left am-header-nav header-btn">
			<img class="am-header-icon-custom"  src="./images/center/nav_bar_back.png"/><span>返回</span>
		</div>
	    <h1 class="header-title">我的特权</h1>
	    <div class="am-header-right am-header-nav">
		</div>
	</header> -->   <!-- 暂时屏蔽头部 -->
	<!-- header部门-->
	<!-- content-->
    <div class = "content" id="containerDiv" style="margin-top:0;">
    	<div class = "content-intro">
    		<div class = "content-intro-row1">
    			<img class = "am-img-thumbnail" src = "<?php echo $weixin_headimgurl;?>">
    		</div>
    		<div class = "content-intro-row2">
    			<span><?php echo $weixin_name;?></span>
    		</div>
    		<div class = "content-intro-row3">
			<?php if($status==1){?>
    			<img src = "./images/info_image/wode_icon4.png">
			<?php }else{?>
				<img src = "./images/info_image/wode_icon6.png">
			<?php }?>
    			<span class = "subtitle_span"><?php if($status==1){echo $exp_name;}else{echo '粉丝';}?></span>
			<?php
				if(($isAgent==1 and $is_agent==1) || ($isAgent==3 and $is_supply==1) || ($isAgent<=8 and $isAgent>=5 and $is_team == 1 and $is_areaAgent == 1)){	//判断是否显示代理商、供应商、区代身份
					if($isAgent==1){
			?>
				<img src = "./images/info_image/wode_icon5.png">
			<?php
					}else if($isAgent==3){
			?>
				<img src = "./images/info_image/wode_icon3.png">
			<?php
					}else if($isAgent<=8 && $isAgent>=5){
			?>
				<img src = "./images/info_image/wode_icon1.png">
			<?php
					}
			?>
    			<span class = "subtitle_span"><?php echo $identity_name;?></span>
			<?php }
				if($is_consume>0 and $is_shareholder == 1 and $is_OpenShareholder == 1){	//判断是否显示股东身份
			?>
				<img src = "./images/info_image/wode_icon2.png">
    			<span class = "subtitle_span"><?php echo $shareholder_name;?></span>
			<?php }?>
    		</div>
    	</div>
    	<?php if($status!=1){	//粉丝?>
    	<div class = "content-main">
    		<ul class = "content-main-wrapper" id="resultData">
    			<li class="itemWrapper itemWrapper-selected button buttonclick_wrapper1" id = "itemWrapper1" >
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050402.png" width= "30">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-row1">
							<span><?php echo $exp_name;?></span>	
						</div>
						<div class = "item-left2-row2">
							<span>先成为<?php echo $exp_name_1;?>才能选择其它</span>	
						</div>
					</div>
					<div class="right-action item-right">
						<span>申请</span><img src="./images/vic/right_arrow.png" width="8" height="13">
					</div>
				</li>
				<?php if(1 == $is_agent){?>
				<li class="itemWrapper button buttonclick"  id = "itemWrapper2" >
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050301.png">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-row1" >
							<span>代理商</span>	
						</div>
						<div class = "item-left2-row2">
							<span>先成为<?php echo $exp_name_1;?>才能选择其它</span>	
						</div>
					</div>
					<div class="right-action item-right">
					</div>
				</li>
				<?php }
					  if(1 == $is_supply){
				?>
				<li class="itemWrapper button buttonclick" id = "itemWrapper3" >
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050403.png" width= "30">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-row1">
							<span >产品供应商</span>	
						</div>
						<div class = "item-left2-row2">
							<span>先成为<?php echo $exp_name_1;?>才能选择其它</span>	
						</div>
					</div>
					<div class="right-action item-right">
					</div>
				</li>
				<?php
					}
					if(1 == $is_areaAgent){
				?>
				<li class="itemWrapper button buttonclick" id = "itemWrapper4" >
					<div class = "item-left1">
						<img  class = "item-left1-img2"  src = "./images/goods_image/20160050303.png" width= "27">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-row1">
							<span>区域代理</span>	
						</div>
						<div class = "item-left2-row2">
							<span>先成为<?php echo $exp_name_1;?>才能选择其它</span>	
						</div>
					</div>
					<div class="right-action item-right">
					</div>
				</li>
				<?php
					}
					if(1 == $is_OpenShareholder){
				?>
				<li class="itemWrapper button buttonclick" id = "itemWrapper5" > 
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050404.png" width= "30">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-row1">
							<span>股东</span>	
						</div>
						<div class = "item-left2-row2">
							<span>先成为<?php echo $exp_name_1;?>才能选择其它</span>	
						</div>
					</div>
					<div class="right-action item-right">
					</div>
				</li>
				<?php }?>
			</ul>
    	</div>
		<?php }else{	//推广员?>
		<div class = "content-main">
    		<ul class = "content-wrapper" id="resultData">
    			<li class="itemWrapper button buttonclick" id = "itemWrapper1" >
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050402.png" width= "30">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-top1">
							<span><?php echo $exp_name_1;?></span>	
						</div>
						<div class = "item-left2-top2">
							<span style="color:#333;">您已成为<?php echo $exp_name;?></span>	
						</div>
					</div>
					<div class="right-action item-right">
						<img src="./images/vic/right_arrow.png" width="8" height="13">
					</div>
				</li>
				<?php
				if(1 == $is_agent){
					if(1 == $isOpenAgent && (1 == $isAgent || 0 == $isAgent)){
				?>
				<li class="itemWrapper button buttonclick"  id = "itemWrapper2" >
					<div class = "item-left1" >
						<img  class = "li-select"  src = "./images/goods_image/20160050405.png" width= "27">
					</div>
					<div class = "item-left2" >
						<div class = "item-left2-top1">
							<span>代理商</span>	
						</div>
						<div class = "item-left2-top2">
							<?php 
								if($agent_id>0 && 1==$agent_status){
							?>
							<span style="color:#333;"><?php echo '您已成为'.$agent_name;?></span>
							<?php
								}else if($agent_id>0 && 0==$agent_status){
							?>
							<span style="color:#333;"><?php echo '您已申请'.$agent_name;?></span>
							<?php }else{?>
							<span>申请成为代理商</span>
							<?php }?>
						</div>
					</div>
					<div class="right-action item-right">
						<img src="./images/vic/right_arrow.png" width="8" height="13">
					</div>
				</li>
				<?php
					}else{
				?>
				<li class="itemWrapper button buttonclick">
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050301.png">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-top1" >
							<span>代理商</span>	
						</div>
						<div class = "item-left2-top2">
							<span>申请成为代理商</span>	
						</div>
					</div>
					<div class="right-action item-right">
					</div>
				</li>
				<?php
					}
				}
				if(1 == $is_supply){
					if(1 == $isOpenSupply && (3 == $isAgent || 0 == $isAgent)){
				?>
				<li class="itemWrapper button buttonclick" id = "itemWrapper3" >
					<div class = "item-left1">
						<img  class = "li-select"  src = "./images/goods_image/20160050406.png" width= "30">
					</div>
					<div class = "item-left2" >
						<div class = "item-left2-top1">
							<span>产品供应商</span>	
						</div>
						<div class = "item-left2-top2">
							<?php 
								if($supply_id>0 && 1==$supply_status){
							?>
							<span style="color:#333;">您已成为产品供应商</span>
							<?php
								}else if($supply_id>0 && 0==$supply_status){
							?>
							<span style="color:#333;">您已申请产品供应商</span>
							<?php }else{?>
							<span>申请成为产品供应商</span>
							<?php }?>
						</div>
					</div>
					<div class="right-action item-right">
						<img src="./images/vic/right_arrow.png" width="8" height="13">
					</div>
				</li>
				<?php
					}else{
				?>
				<li class="itemWrapper button buttonclick">
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050403.png" width= "30">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-top1">
							<span >产品供应商</span>	
						</div>
						<div class = "item-left2-top2">
							<span>申请成为产品供应商</span>	
						</div>
					</div>
					<div class="right-action item-right">
					</div>
				</li>
				<?php
					}
				}
				if(1 == $is_areaAgent){
					if($isAgent != 1 && $isAgent != 3 && $is_team>0){
				?>
				<li class="itemWrapper button buttonclick" id = "itemWrapper4" >
					<div class = "item-left1">
						<img class  = "item-left1-img1" src = "./images/goods_image/20160050407.png" width= "27">
					</div>
					<div class = "item-left2" >
						<div  class = "item-left2-top1">
							<span>区域代理</span>	
						</div>
						<div  class = "item-left2-top2">
							<?php 
								if($isAgent<=8 && $isAgent>=5){
									/*** 分配区域 start***/
									$area_name = '未分配区域';	//当前分配区域名称
									$all_areaname = '';
									$query_teamarea = "select all_areaname from weixin_commonshop_team_area where isvalid=true and area_user=".$user_id." and customer_id=".$customer_id;
									$result_teamarea = mysql_query($query_teamarea) or die('Query_teamarea failed:'.mysql_error());
									while($row_teamarea = mysql_fetch_object($result_teamarea)){
										$area_name = $row_teamarea->all_areaname;
										$all_areaname = $all_areaname.$area_name.' ,';
									}
									$all_areaname = substr($all_areaname,0,-1);
									/*** 分配区域 end***/
							?>
							<span style="color:#333;">
								<?php echo '您已成为'.$identity_name;?><?php if(!empty($all_areaname)){echo '（'.$all_areaname.'）';}?><?php if($team_id>0 && 0 == $team_status){echo '，正在申请'.$apply_name.'中';}?>
							</span>
							<?php
								}else if($team_id>0 && 0 == $team_status){
							?>
							<span style="color:#333;"><?php echo '您已申请'.$apply_name;?></span>
							<?php }else{?>
							<span>申请成为区域代理</span>
							<?php }?>
						</div>
					</div>
					<div class="right-action item-right">
						<img src="./images/vic/right_arrow.png" width="8" height="13">
					</div>
				</li>
				<?php
					}else{
				?>
				<li class="itemWrapper button buttonclick">
					<div class = "item-left1">
						<img  class = "item-left1-img2"  src = "./images/goods_image/20160050303.png" width= "27">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-top1">
							<span>区域代理</span>	
						</div>
						<div class = "item-left2-top2">
							<span>申请成为区域代理</span>
						</div>
					</div>
					<div class="right-action item-right">
					</div>
				</li>
				<?php
					}
				}
				if(1 == $is_OpenShareholder){
					if($is_shareholder>0){
				?>
				<li class="itemWrapper button buttonclick" id = "itemWrapper5" > 
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050408.png" width= "30">
					</div>
					<div class = "item-left2" >
						<div class = "item-left2-top1">
							<span>股东</span>	
						</div>
						<div class = "item-left2-top2">
							<?php 
								if($is_consume>0){
							?>
							<span style="color:#333;white-space: nowrap;"><?php echo '您已成为'.$shareholder_name;?></span>
							<?php
								}else{
							?>
							<span>申请成为股东</span>
							<?php }?>
						</div>
					</div>
					<div class="right-action item-right">
						<img src="./images/vic/right_arrow.png" width="8" height="13">
					</div>
				</li>
					<?php }else{?>
				<li class="itemWrapper button buttonclick">
					<div class = "item-left1">
						<img src = "./images/goods_image/20160050404.png" width= "30">
					</div>
					<div class = "item-left2">
						<div class = "item-left2-top1">
							<span >股东</span>	
						</div>
						<div class = "item-left2-top2">
							<span>申请成为股东</span>	
						</div>
					</div>
					<div class="right-action item-right">
					</div>
				</li>
					<?php
						}
					}
					?>
			</ul>
    	</div>
		<?php }?>
	</div>
	<!-- content-->

	<script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
</body>		
<!-- 页联系js -->
<script src="./js/goods/global.js"></script>
</body>
<script>
var customer_id_en	  = '<?php echo $customer_id_en;?>';
var is_ncomission  	  = '<?php echo $is_ncomission;?>';
var is_showuplevel_t  = '<?php echo $is_showuplevel_t;?>';
var is_showuplevel_s  = '<?php echo $is_showuplevel_s;?>';
var isOpenAgent  	  = '<?php echo $isOpenAgent;?>';
var isOpenSupply 	  = '<?php echo $isOpenSupply;?>';
var status 	   		  = '<?php echo $status;?>';

 $(".itemWrapper ").click(
 	function(){
 		var targetId = $(this).attr("id");
 		if(targetId == "itemWrapper1"){
			window.location.href = 'promoter_upgrade.php?customer_id='+customer_id_en;
			
 		}else if(targetId == "itemWrapper2"){
			if(1==status){
				if(isOpenAgent){
					window.location.href = 'agent_login.php?customer_id='+customer_id_en;
				}else{
					showAlertMsg ("提示：","商家没有开启代理商申请","知道了");
					return;
				}
			}
 		}else if(targetId == "itemWrapper3"){
			if(1==status){
				if(isOpenSupply){
					window.location.href = 'supply_login.php?customer_id='+customer_id_en;
				}else{
					showAlertMsg ("提示：","商家没有开启供应商申请","知道了");
					return;
				}
			}
 		}else if(targetId == "itemWrapper4"){
			if(1==status){
				if(!is_showuplevel_t){
					showAlertMsg ("提示：","商家没有开启区域代理申请","知道了");
				}
				window.location.href = 'area_agent.php?customer_id='+customer_id_en;
			}
 		}else if(targetId == "itemWrapper5"){
			if(1==status){
				if(!is_showuplevel_s){
					showAlertMsg ("提示：","商家没有开启股东升级","知道了");
				}
				window.location.href = 'shareholder.php?customer_id='+customer_id_en;
			}
 		}
 	}
 );
 
   //返回按键点击事件
  $(".header-btn").click(
	 	 function(){
	 	 	window.location.href = "personal_center.php?customer_id="+customer_id_en;
	 	 }
 );	
</script>
<!--引入微信分享文件----start-->
<script>
<!--微信分享页面参数----start-->
debug=false;
share_url=''; //分享链接
title=""; //标题
desc=""; //分享内容
imgUrl="";//分享LOGO
share_type=3;//自定义类型
<!--微信分享页面参数----end-->
</script>
<?php require('../common/share.php');?>
<!--引入微信分享文件----end-->
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</html>