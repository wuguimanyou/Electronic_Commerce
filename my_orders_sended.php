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

$order_num = 5;//每次加载数据的数量

$query = "select batchcode from weixin_commonshop_orders where isvalid=true and paystatus=1 and status!=-1 and sendstatus=1 and customer_id=".$customer_id." and user_id=".$user_id." group by batchcode desc limit ".$order_num;	//获取订单号

$pid = -1;					//商品ID
$rcount = 0;				//商品数量
$totalprice = 0;			//订单总价格
$batchcode_arr = array();
$i = 0;

$result = mysql_query($query) or die('query failed'.mysql_error());
while($row = mysql_fetch_object($result)){
	$batchcode_arr[$i] = $row->batchcode;
	$i++;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>订单管理</title>
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

    
    <!-- 基本dialog-->
    <link type="text/css" rel="stylesheet" href="./css/goods/dialog.css" />
    <link type="text/css" rel="stylesheet" href="./css/self_dialog.css" />
 
</head>

<link type="text/css" rel="stylesheet" href="./css/order_css/style.css" media="all">
<link type="text/css" rel="stylesheet" href="./css/order_css/dingdan.css" />

<body id="mainBody" data-ctrl=true>
    <div id="mainDiv">
	  <!--   <header data-am-widget="header" class="am-header am-header-default">
		    <div class="am-header-left am-header-nav" onclick="history.go(-1)">
			    <img class="am-header-icon-custom icon_back" src="./images/center/nav_bar_back.png"/><span>返回</span>
		    </div>
	        <h1 class="am-header-title topTitle">订单管理</h1>
	    </header>
        <div class="topDiv"></div> --><!-- 暂时隐藏头部导航栏 -->
        
		<!-- 上面的Tabbar开始 -->
		<div id="middle-tab" class="tabbar">
            <div id="kindAll" class="area-one">
                <img src="./images/order_image/icon_dingdan_quanbu.png" width="20" height="20">
                <div>全部</div>
            </div>
            
			<div id="kindDaiFuKuan" class="area-one">
                <img src="./images/order_image/icon_daifukuan.png" width="20" height="20">
                <div>待付款</div>
            </div>
            
			<div id="kindDaiFaHuo" class="area-one">
                <img src="./images/order_image/icon_daifahuo.png" width="20" height="20">
                <div>待发货</div>
            </div>
            
			<div id="kindDaiShouHuo" class="area-one select">
                <img src="./images/order_image/icon_daishouhuo_sel-orange.png" width="20" height="20">
                <div>待收货</div>
            </div>
            
			<div id="kindYiWanCheng" class="area-one">
                <img src="./images/order_image/icon_daipingjia.png" width="20" height="20">
                <div>已完成</div>
            </div>
            
			<div id="kindShouHouZhong" class="area-one">
                <img src="./images/order_image/icon_shouhouzhong.png" width="20" height="20">
                <div>售后中</div>
            </div>
			
        </div>
		<!-- 上面的Tabbar终结 -->
            
		<!-- 基本数据地区 开始 -->
        <div id="productContainerDiv">
            <div class="entry-content">
                <ul id="pinterestList">
				<?php
					$b_length = count($batchcode_arr);
					for($j=0;$j<$b_length;$j++){
						$batchcode = $batchcode_arr[$j];
						$order_status = '待收货';		//订单状态
						$order_str = 'shouhuo';			//订单详情页
						/* 店铺 start*/
						$sql = "select pid from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." limit 1";
						$res = mysql_query($sql) or die('sql failed'.mysql_error());
						while($row_s = mysql_fetch_object($res)){
							$pid = $row_s->pid;							//商品ID
						}
						$sql1 = "select name from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
						$res1 = mysql_query($sql1) or die('query failed1'.mysql_error());
						while($row_s1 = mysql_fetch_object($res1)) {
							$shop_name = $row_s1->name;					//商家名
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
							$supply_id = $row_s3->id;					//店铺ID
							$shop_name = $row_s3->shopName;				//店铺名
						}
						/* 店铺 end*/
				?>
					<div class="shopHead">	
						<ul class="am-navbar-nav am-cf am-avg-sm-1">		
							<li class="tab_right_top" style="margin:0px;">			
								<img class="itemPhotoCheck shopall shopCheck" src="./images/order_image/icon_shop.png">			
								<span <?php if($supply_id>0){echo "onclick='gotoShop(".$supply_id.")'";}?> class="am-navbar-label"><span class="shopName"><?php echo $shop_name;?></span></span>
								<img class="img_shop_right" <?php if($supply_id>0){echo "onclick='gotoShop(".$supply_id.")'";}?> src="./images/order_image/btn_right.png">
								<span class="am-navbar-label orderState"><?php echo $order_status;?></span>		
							</li>	 
						</ul>
					</div>
					<?php
						$totalcount = 0;		//商品总数量
						$totalprice = 0;		//商品总价格
						$query3 = "select pid,rcount,totalprice,prvalues from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." and paystatus=1 and status!=-1 and sendstatus=1";
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
					<li class="itemWrapper" style="margin:0px;">	
						<div class="itemMainDiv" onclick="gotoProductOrder(<?php echo $batchcode.",'".$order_str."'";?>)">			
							<img class="itemPhoto" style="height: 90px;" src="<?php echo $product_default_imgurl;?>">			
							<div class="contentLiDiv">				
								<div class="itemProName">					
									<span class="goodsName"><?php echo $product_name;?></span>					
									<span class="goodsPrice-red">￥<?php echo $product_now_price;?></span>				
								</div>				
								<span class="itemProContent goodsContent"></span>				
								<div class="itemProContent goodsSize">
									<?php echo $prvstr;?><span>x <?php echo $rcount;?></span>
								</div>				
								<div class="goodsRedRect">七天退换</div>			
							</div>	
						</div>
					</li>
					<div class="horizLine1"></div>
					<?php 	}
							$query7 = "select price,ExpressPrice,rcount from weixin_commonshop_order_prices where isvalid=true and batchcode=".$batchcode;
							$result7 = mysql_query($query7) or die('query failed7'.mysql_error());
							while($row7 = mysql_fetch_object($result7)){
								// $price = $row7->price;
								$ExpressPrice = $row7->ExpressPrice;		//运费
								// $rcount = $row7->rcount;
							}
							$totalprice = $totalprice + $ExpressPrice;		//商品总价格加上运费
					?>
					<div class="order_info">	
						<span class="order_goods_count">共<?php echo $totalcount;?>件商品&nbsp;&nbsp;合计:￥<span style="font-size:17px;"><?php echo $totalprice;?></span><span style="color:#aaa;">&nbsp;&nbsp;<?php if($ExpressPrice>0){echo '(含运费'.$ExpressPrice.'元)';}else{ echo '(不含运费)';}?></span></span>
					</div>
					<div style="width:100%;">	
						<ul class="am-navbar-nav am-cf am-avg-sm-1 button_area">		
							<li class="tab_right_top" style="margin:0px;">	
								<span onclick="chaKanWuLiu(<?php echo $batchcode;?>)" class="am-navbar-label btnWhite4"><span style="color:#777;">查看物流</span></span>	
								<span onclick="queRenShouHuo(<?php echo $batchcode;?>)" class="am-navbar-label btnBlack4"><span style="color:#fff;">确认收货</span></span>
							</li>	 
						</ul>
					</div>
					<div class="horizLineGray"></div>
					<?php }?>
				</ul>
            </div>
        </div>
		<!-- 基本数据地区 终结 -->
        
    </div>
	<!-- 加载数据 -->
    <div id="jiazai">
		<div id="foo" onclick="get_orders('daishouhuo')"></div>
	</div>
	<!-- 加载数据 -->

	<script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
	<script type="text/javascript" src="./js/dingdanguanli.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
 </body>		

<script type="text/javascript">
	
	$(".area-one").click(function(){
		$(".area-one").removeClass("select");
		$(this).addClass("select");
	});
    
	//全部
	$("#kindAll").click(function(){
        window.location.href = "my_orders.php?customer_id="+customer_id;
    });
    
	//待付款
    $("#kindDaiFuKuan").click(function(){
		window.location.href = "my_orders_paying.php?customer_id="+customer_id;
	});
	
	//待发货
    $("#kindDaiFaHuo").click(function(){
		window.location.href = "my_orders_sending.php?customer_id="+customer_id;
	});
	
	//待收货
    $("#kindDaiShouHuo").click(function(){
		window.location.href = "my_orders_sended.php?customer_id="+customer_id;
	});
 
	//已完成
    $("#kindYiWanCheng").click(function(){
		window.location.href = "my_orders_finish.php?customer_id="+customer_id;
	});
	
	//售后中
    $("#kindShouHouZhong").click(function(){
		window.location.href = "my_orders_server.php?customer_id="+customer_id;
	});
	
	//点击【查看物流】
	function chaKanWuLiu(dingdanID){
		window.location.href = "wuliuxiangqing.html";
	}
	
</script>
<script>
var customer_id = '<?php echo $customer_id;?>';
var user_id = '<?php echo $user_id;?>';
var order_num = '<?php echo $order_num;?>';
var order_num_end = 5;
//滑动加载
$(window).scroll(function() {
	if ($(document).scrollTop() + $(window).height() >= $(document).height()) {
		document.getElementById('foo').click();
	} 
});
//获取订单
function get_orders(order_type){
	var order_num_l = $('.shopHead').length;
	// console.log(order_num);
	if(order_num_l < order_num){
		$('#jiazai').hide();
		return;
	}
	$.ajax({
		url: 'get_orders.php',
		data:{
			customer_id:customer_id,
			user_id:user_id,
			order_type:order_type,
			order_num:order_num,
			order_num_end:order_num_end
		},
		type:"POST",
		dataType:"json",
		async:true,
		success:function(res){
			var content = '';
			if(res.length<5){	//假如无数据则隐藏
				$('#jiazai').hide();
			}
			for (id in res) {
				gotoshop = '';
			if(res[id][0][1]>0){
				gotoshop = "onclick=gotoShop("+res[id][0][1]+")";
			}
            content += ('<div class="shopHead">');
			content += ('	<ul class="am-navbar-nav am-cf am-avg-sm-1">');
			content += ('		<li class="tab_right_top" style="margin:0px;">');
			content += ('			<img class="itemPhotoCheck shopall shopCheck" src="./images/order_image/icon_shop.png">');
			content += ('			<span '+gotoshop+' class="am-navbar-label"><span class="shopName">'+res[id][0][2]+'</span></span>');
			content += ('			<img class="img_shop_right" '+gotoshop+' src="./images/order_image/btn_right.png">');
			content += ('			<span class="am-navbar-label orderState">'+res[id][0][3]+'</span>');
			content += ('		</li>');
			content += ('	 </ul>');
			content += ('</div>');
			
			for(var i=1;i<res[id].length;i++){
				
				content += ('<li class="itemWrapper" style="margin:0px;">');
				content += ('	<div class="itemMainDiv" style="" onclick="gotoProductOrder('+res[id][0][4]+',\''+res[id][0][5]+'\')" >');
				content += ('			<img class="itemPhoto" src="'+res[id][i]["product_default_imgurl"]+'">');      
				content += ('			<div class="contentLiDiv">');            
				content += ('				<div class="itemProName">');
				content += ('					<span class="goodsName">'+res[id][i]["product_name"]+'</span>');
				content += ('					<span class="goodsPrice-red">￥'+res[id][i]["product_now_price"]+'</span>');
				content += ('				</div>');
				content += ('				<span class="itemProContent goodsContent">内容</span>');
				content += ('				<div class="itemProContent goodsSize">'+res[id][i]["prvstr"]+'<span>x '+res[id][i]["rcount"]+'</span></div>');
				content += ('				<div class="goodsRedRect">七天退换</div>');            
				content += ('			</div>');
				content += ('	</div>');
				content += ('</li>');
				content += ('<div class="horizLine1"></div>');
				
				totalcount = res[id][i]["totalcount"];		//商品总数量
				totalprice = res[id][i]["totalprice"];		//商品总价格
				ExpressPrice = res[id][i]["ExpressPrice"];	//运费
			}
			if(ExpressPrice > 0){
				ExpressPrice_str = '(含运费'+ExpressPrice+'元)';
			}else{
				ExpressPrice_str = '(不含运费)';
			}
			content += ('<div class="order_info">');
			content += ('	<span class="order_goods_count">共'+totalcount+'件商品&nbsp;&nbsp;合计:￥'+totalprice+'<span style="color:#aaa;">&nbsp;&nbsp;'+ExpressPrice_str+'</span></span>');
			content += ('</div>');							
			content += ('<div style="width:100%;">');
			content += ('	<ul class="am-navbar-nav am-cf am-avg-sm-1 button_area">');
			content += ('		<li class="tab_right_top" style="margin:0px;">');
			
			content += ('			<span onclick = "chaKanWuLiu(' + res[id][0][4] + ')" class="am-navbar-label btnWhite4"><span style="color:#777;">查看物流</span></span>');
			content += ('			<span onclick = "queRenShouHuo('+totalprice+')" class="am-navbar-label btnBlack4"><span style="color:#fff;">确认收货</span></span>');

			content += ('		</li>');
			content += ('	 </ul>');
			content += ('</div>');
			content += ('<div class="horizLineGray"></div>');
			order_num++;
        }
			$("#pinterestList").append(content);  
		},
		error:function(er){
			
		}
	});
}
$(function(){
	var order_num_l2 = $('.shopHead').length;
	// console.log(order_num_l2);
	if(order_num_l2<5){		//小于加载数量则数据不足，无需加载数据
		$('#jiazai').hide();
		return;
	}
})
</script>
</body>
</html>