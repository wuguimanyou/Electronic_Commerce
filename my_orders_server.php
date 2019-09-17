<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php'); //配置
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../proxy_info.php');
require('../common/jssdk.php');
$user_id = 196282;
$batchcode = -1;
$batchcode = $_GET['batchcode'];
$customer_id = 3243;

$query = "select sendstatus,address_id from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." limit 1";
$result = mysql_query($query) or die('query failed'.mysql_error());
while($row = mysql_fetch_object($result)){
	$sendstatus = $row->sendstatus;		//发货状态
	$address_id = $row->address_id;		//地址ID
}
if($sendstatus == 2){
	header("Location:dingdanxiangqing_wancheng.php?batchcode=".$batchcode);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>订单详情</title>
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
    <link type="text/css" rel="stylesheet" href="./css/css_orange.css" /> 

    
    
</head>

<link rel="stylesheet" href="./css/order_css/style.css" type="text/css" media="all">
<link type="text/css" rel="stylesheet" href="./css/order_css/dingdan.css" />
<link type="text/css" rel="stylesheet" href="./css/order_css/dingdan_detail.css"/>

<body class="mainBody" data-ctrl=true>
	<!-- <header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="history.go(-1)">
			<img class="am-header-icon-custom icon_back" src="./images/center/nav_bar_back.png"/><span>返回</span>
		</div>
	    <h1 class="am-header-title topTitle">订单详情</h1>
	</header>
    <div class="topDiv"></div> --><!-- 暂时隐藏头部导航栏 -->
	
	<!-- 基本地区-开始 -->
	<div class="mainArea">
		<div class="entry-content">
			
			<!-- 订单状态 -->
			<div class="divOrderState">
				<div class="orderState">订单状态</div>
				<div class="line_gray"></div>
				<div id="middle-tab">
					<div class="area-one comment-mark sel">
						<div class="lineBlack"></div>
						<img class="btn_round_status" src="./images/order_image/icon_check_orange.png">
						<div>已付款</div>
					</div>
					<div class="area-one comment-mark sel">
						<div class="lineGray"></div>
						<img class="btn_round_status" src="./images/order_image/icon_check_orange.png"> 
						<div>已发货</div>
					</div>
					<div class="area-one comment-mark">
						<img class="btn_round_status" src="./images/order_image/icon_time_gray.png"> 
						<div>待收货</div>
					</div>
				</div>
			</div>
			<?php
				$name = '佚名';
				$query2 = "select address,name,phone,location_p,location_c,location_a from weixin_commonshop_addresses where isvalid=true and user_id=".$user_id." and id=".$address_id;
				// echo $query2;
				$result2 = mysql_query($query2) or die('query failed2'.mysql_error());
				while($row2 = mysql_fetch_object($result2)){
					$address = $row2->address;			//详细地址
					$name = $row2->name;				//收货人姓名
					$phone = $row2->phone;				//收货人联系电话
					$location_p = $row2->location_p;	//省份
					$location_c = $row2->location_c;	//市区
					$location_a = $row2->location_a;	//街道/镇区
				}
			?>
			<!-- 收货人信息 -->
			<div class="div_receiver">
				<div class="div_pos">
					<img src="./images/order_image/icon_position.png">    
				</div>
				<div class="div_right">
					<div class="frame_top">
						<span class="name">收货人&nbsp;:&nbsp;</span>
						<span class="name right"><?php echo $name;?></span>
						<span class="phone_right"><?php echo $phone;?></span>
					</div>
					<div class="frame_bottom">
						<span>地址&nbsp;:&nbsp;</span><span><?php echo $location_p.$location_c.$location_a.$address;?></span>
					</div>
				</div>
			</div>
			<?php
						/* 店铺 start */
						$sql = "select pid,createtime,paytime,confirm_sendtime from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." limit 1";
						$res = mysql_query($sql) or die('sql failed'.mysql_error());
						while($row_s = mysql_fetch_object($res)){
							$pid = $row_s->pid;								//商品ID
							$createtime = $row_s->createtime;				//订单创建时间
							$paytime = $row_s->paytime;						//支付时间
							$confirm_sendtime = $row_s->confirm_sendtime;	//发货时间
						}
						$sql1 = "select name from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
						$res1 = mysql_query($sql1) or die('query failed1'.mysql_error());
						while($row_s1 = mysql_fetch_object($res1)) {
							$shop_name = $row_s1->name;		//商家名
						}
						$sql2 = "select is_supply_id from weixin_commonshop_products where customer_id=".$customer_id." and id=".$pid;
						$res2 = mysql_query($sql2) or die('sql failed2'.mysql_error());
						while($row_s2 = mysql_fetch_object($res2)){
							$is_supply_id = $row_s2->is_supply_id;		//供应商ID
						}
						$supply_id = -1;
						$sql3 = "select id,shopName from weixin_commonshop_applysupplys where user_id=".$is_supply_id;
						$res3 = mysql_query($sql3) or die('sql failed3'.mysql_error());
						while($row_s3 = mysql_fetch_object($res3)){
							$supply_id = $row_s3->id;			//店铺ID
							$shop_name = $row_s3->shopName;		//店铺名
						}
						/* 店铺 end */
			?>
			<!-- 订单的商品目录信息 -->
			<ul class="ui_order_goods">
				<div class="shopHead">
					<ul class="am-navbar-nav am-cf am-avg-sm-1">
						<li class="tab_right_top" style="margin:0px;">
							<img class="itemPhotoCheck shopall shopCheck" src="./images/order_image/icon_shop.png">
							<span <?php if($supply_id>0){echo "onclick='gotoShop(".$supply_id.")'";}?> class="am-navbar-label"><span class="shopName"><?php echo $shop_name;?></span></span>
							<img class="img_shop_right" <?php if($supply_id>0){echo "onclick='gotoShop(".$supply_id.")'";}?> src="./images/order_image/btn_right.png">
						</li>
					 </ul>
				</div>
				<?php
						$totalcount = 0;	//商品总数量
						$totalprice = 0;	//商品总价格
						$query3 = "select id,pid,rcount,totalprice,prvalues from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." and paystatus=1 and status!=-1 and sendstatus=1";
						$result3 = mysql_query($query3) or die('query failed3'.mysql_error());
						while($row3 = mysql_fetch_object($result3)){
							$pid = $row3->pid;						//商品ID
							$rcount = $row3->rcount;				//商品数量
							$price = $row3->totalprice;				//商品价格
							$prvalues = $row3->prvalues;			//商品属性
							
							$totalcount = $rcount + $totalcount;
							$totalprice = $price + $totalprice;
							
							$prvstr="";
							if(!empty($prvalues)){
								$prvarr= explode("_",$prvalues);						
								for($i=0;$i<count($prvarr);$i++){
									$prvid = $prvarr[$i];
									if($prvid>0){
										$parent_id = -1;
										$prname = '';
										$query4 = "select name,parent_id from weixin_commonshop_pros where isvalid=true and id=".$prvid;
										$result4 = mysql_query($query4) or die('query failed4'.mysql_error());
										while($row4 = mysql_fetch_object($result4)){
											$parent_id = $row4->parent_id;	//是否子属性
										    $prname = $row4->name;			//属性名
										}
										$p_prname = '';
										$query5 = "select name from weixin_commonshop_pros where isvalid=true and id=".$parent_id;
										$result5 = mysql_query($query5) or die('query failed5'.mysql_error());
										while($row5 = mysql_fetch_object($result5)){
											$p_prname = $row5->name;		//属性名
											$prvstr = $prvstr.$p_prname.":".$prname."  ";
										}
									}
								}
							}
						
							$query6 = "select id,name,orgin_price,now_price,is_virtual,default_imgurl from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and id=".$pid;
							$result6 = mysql_query($query6) or die('query failed6'.mysql_error());
							while($row6 = mysql_fetch_object($result6)){
								$product_id = $row6->id;						//商品ID
								$product_name = $row6->name;					//商品名
								$product_orgin_price = $row6->orgin_price;		//商品原价
								$product_now_price = $row6->now_price;			//商品现价
								$product_is_virtual = $row6->is_virtual;		//是否虚拟产品
								$product_default_imgurl = $row6->default_imgurl;//商品封面图
							}
				?>
				<!-- 第一个商品 -->
				<li class="itemWrapper item_goods">
					<div class="itemMainDiv">
							<img onclick="gotoProductDetail(<?php echo $pid;?>)" class="itemPhoto" src="<?php echo $product_default_imgurl;?>">      
							<div class="contentLiDiv">            
								<div class="itemProName">
									<span class="goodsName"><?php echo $product_name;?></span>
									<span class="goodsPrice">￥<?php echo $product_now_price;?></span>
								</div>            
								<span class="itemProContent goodsContent">内容</span>
								<div class="itemProContent goodsSize"><?php echo $prvstr;?><span>x <?php echo $rcount;?></span></div>
								<div class="goodsRedRect" style="float:left;">七天退换</div>
								<div class="itemProContent right_white_rect" onclick="shenqingshouhou(<?php echo $pid;?>,<?php echo $batchcode;?>);">申请售后</div>
							</div>
					</div>
				</li>
				<div class="line_white"></div>
				<?php 	}
						$query7 = "select price,ExpressPrice,rcount from weixin_commonshop_order_prices where isvalid=true and batchcode=".$batchcode;
						$result7 = mysql_query($query7) or die('query failed7'.mysql_error());
						while($row7 = mysql_fetch_object($result7)){
							// $totalprice = $row7->price;
							$ExpressPrice = $row7->ExpressPrice;		//运费
							// $rcount = $row7->rcount;
						}
				?>
				
				<!-- 费用信息 -->
				<div class="itemWrapper itemOrderInfo">
					<span class="text_left_13">运费</span>
					<span class="text_right_13">￥<?php echo $ExpressPrice;?></span>
				</div>							
				<div class="itemOrderMoney">
					<span class="itemLeft">实付款</span>
					<span class="itemRight">￥<?php echo $totalprice;?></span>
				</div>							
				<div class="horizLineGray"></div>
			</ul>
			
			<!-- 订单编号，各种时间信息 -->
			<div class="infoWrapper">
				<span class="text_gray_13">订单编号:<span id="batchcode"><?php echo $batchcode;?></span></span>
					<div id="copy_btn" onclick="copy()">复制</div>
				<span class="content-line">支付宝交易号:12345123456789</span>
				<span class="content-line">创建时间:<?php echo $createtime;?></span>
				<span class="content-line">付款时间:<?php echo $paytime;?></span>
				<span class="content-line" style="margin-bottom:10px;">发货时间:<?php echo $confirm_sendtime;?></span>
			</div>

			<!-- 留言，回复信息 -->
			<div class="comment-frame">
				<span class="content-line" style="color:red;">买家留言:</span>
				<span class="content-line">太感谢你了，下次欢迎购买。</span>
			</div>
			
			<div class="comment-frame">
				<span class="content-line" style="color:red;">商家回复:</span>
				<span class="content-line">太感谢你了，下次欢迎购买。</span>
			</div>
			
		</div>
	</div>
	<!-- 基本地区-终结 -->
		
	<!-- 下面的按钮地区 - 开始 -->
	<div class="white-list">
		<div style="width:100%;">
			<ul class="am-navbar-nav am-cf am-avg-sm-1">
				<li class="tab_right_top" style="margin:0px;">
					<span onclick="queRenShouHuo(<?php echo $batchcode;?>);" class="am-navbar-label btnWhite2">确认收货</span>
					<span onclick="clickedChaKanWuLiu(100);" class="am-navbar-label btnWhite4">查看物流</span>
				</li>
			 </ul>
		</div>
    </div>
	<!-- 下面的按钮地区 - 终结 -->

	<script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
	<script type="text/javascript" src="./js/dingdanguanli.js"></script>
</body>		

<script type="text/javascript">
var customer_id = '<?php echo $customer_id;?>';
var user_id = '<?php echo $user_id;?>';	
	
	//点击【申请售后】
	function shenqingshouhou(productID,orderID){
		window.location.href = "returngoods.php?pid="+productID+"&batchcode="+orderID;
	}
	
	//点击【复制】
	function clickedCopy(dingdanNum){
		alert("复制---"+dingdanNum);
	}
	
	//点击【查看物流】
	function clickedChaKanWuLiu(dingdanID){
		window.location.href = "wuliuxiangqing.html";
	}

		
</script>

</body>
</html>