<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
//require('../back_init.php'); 
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../common/jssdk.php');
require('select_skin.php');
//头文件----start
require('../common/common_from.php');
//头文件----end

require('./myteam_switch.php');

require('../common/own_data.php');
$info = new my_data();//own_data.php my_data类
// $pcount = $info->my_team_count($customer_id,$user_id);//团队总人数
$Agent = $info->Agent_name($customer_id);//区域身份自定义名称方法




//查询推广员的等级，自定义名等

$exp_name 	      = '推广员自定义名称';

$sql = "SELECT name,exp_name,is_cashback,modify_up,modify_type,is_my_commission,openbillboard,isOpenreward,is_qr_code FROM weixin_commonshops WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
$res = mysql_query($sql) or die('Query failed34: ' . mysql_error());
while( $row = mysql_fetch_object($res) ){
	
	$exp_name 		    = $row->exp_name;		//推广员自定义名称
	
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>我的团队</title>
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
    <!-- 模板 -->
    
    
    <!-- 页联系style-->
    <link type="text/css" rel="stylesheet" href="./css/vic.css"/>
    <link type="text/css" rel="stylesheet" href="./css/goods/dialog.css" />
    <link type="text/css" rel="stylesheet" href="./css/goods/myteam.css?v=<?php echo time() ;?>" />
    
    <!-- 页联系style-->
	<link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" /> 
    <style >
	.am-form-field{background:url(./images/vic/icon_search.png) 5% 9px no-repeat;background-size: 16px 16px;background-color: #fff;}
	.searching{ float: right;display: inline-flex; width: 19%;}
	.searching button{background: #fff;border: 1px solid #ccc;padding: 6px 11px;display:inline-block;position: absolute;}
    </style>
    
</head>

<!-- Loading Screen -->
<!--<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/></div> -->
<!-- Loading Screen -->

<body data-ctrl=true>
	<!-- header部门-->
	<!-- <header data-am-widget="header" class="am-header am-header-default header">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom header-btn"  src="./images/center/nav_bar_back.png" /><span  class = "header-btn">返回</span>
		</div>
	    <h1 class="header-title">我的团队</h1>
	    <div class="am-header-right am-header-nav">
		</div>
	</header>
	<div class="topDiv" style="height:49px;"></div> -->   <!-- 暂时屏蔽头部 -->
	<!-- header部门-->
	
	<!-- 搜索部门-->
	<div class = "condition">
		<div class = "condition-row1" >
			<input  class="am-form-field search" id="tvKeyword" type="text" style="position:absolute;display:inline-block;width:70%;text-indent:25px;border-radius:3px 0 0 3px;font-size:15px;" placeholder="搜索" >
			<span class="searching">
				<button class="searchFor">搜索</button>
			</span>
		</div>
		<div class = "condition-row2">
			<img class = "condition-row2-btn" id="all_btn" src = "./images/goods_image/2016042901.png" width = "20" height = "20">
			<span class = "condition-row2-text">全部</span>
			<span class = "" style="float:right">团队总人数：<span class="people">0</span></span>
		</div>
	</div>
	<div style="height:101px"></div>  <!-- 占据上面框的高度 -->

	<!-- 搜索部门-->
	
	<!-- content --->
    <div class = "content" id="containerDiv">
    	<ul class = "content-list" id="resultData">
		</ul>		
	</div>
	<!-- content --->	
	
	<!-- dialog--->
	<div class="am-share dlg">
		<div class = "dlg-div">
			<div class = "dlg-div-title">
				<div class = "dlg-div-title-left"><span>角色<span></div>
				<div class = "dlg-div-title-right">
					<span class = "dlg-div-title-right-cell1">
						<font>当前选择</font>
						<font class = "dlg-div-title-right-cell1-text" id = "dlg-div-title-juese">全部</font>
					</span>
				</div>
			</div>
			<div class = "dlg-div-content">
			</div>
			<div class = "dlg-div-title">
				<div class = "dlg-div-title-left"><span>等级<span></div>
				<div class = "dlg-div-title-right">
					<span class = "dlg-div-title-right-cell1">
						<font>当前选择</font>
						<font class = "dlg-div-title-right-cell1-text" id = "dlg-div-title-dengji">全部</font>
					</span>
				</div>
			</div>
			<div class = "dlg-div-content">
			</div>
			<div class = "dlg_close_btn">
				<span>确定</span>
			</div>
		</div>
		
		
	</div>
	<!-- dialog--->
    
   
 <!--引入侧边栏 start-->
   <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->   
</body>		
<!-- 页联系js -->
<script src="./js/goods/global.js"></script>

<div id='loading' class='loadingPop'style="display: none;text-align: center"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>
<script>
var customer_id = '<?php echo $customer_id_en; ?>';
var user_id     = '<?php echo $user_id; ?>';
var i           = 0;//数据序号
var page        = 1;//页数
var url         = "";//异步加载链接
var choose_val  = 0 ;//身份数值
var pass        = 0;//判断是拉动加载还是
var data_null   = 0;//判断返回数据是否为空
var search_text = "";//关键字
var downFlow    = false;//是否数据加载完

var a_customer      = "<?php echo $Agent['a_customer'];?>";//区代自定义名字
var c_customer      = "<?php echo $Agent['c_customer'];?>";//市代自定义名字
var p_customer      = "<?php echo $Agent['p_customer'];?>";//省代自定义名字
var diy_customer    = "<?php echo $Agent['diy_customer'];?>";//自定义区域自定义名字
var is_showcustomer = "<?php echo $Agent['is_showcustomer'];?>";//开启区代自定义名字
var is_diy_area     = "<?php echo $Agent['is_diy_area'];?>";//开启自定义区域自定义名字

/*身份开关----start*/
var issell			   = '<?php echo $issell; ?>';//是否开启分销
var is_team 		   = '<?php echo $is_team; ?>';//是否开启团队奖励
var is_shareholder	   = '<?php echo $is_shareholder; ?>';//是否开启股东分红奖励
var isOpenInstall 	   = '<?php echo $isOpenInstall; ?>';//是否开启技师
var is_agent 		   = '<?php echo $is_agent; ?>';//渠道是否开启代理商功能
var is_supply		   = '<?php echo $is_supply; ?>';//渠道是否开启供应商功能
var is_areaAgent 	   = '<?php echo $is_areaAgent; ?>';//渠道是否开启区域团队功能
var is_OpenShareholder = '<?php echo $is_OpenShareholder; ?>';//渠道是否开启股东分红功能
/*身份开关----end*/


function show_data(url,page,customer_id,user_id){
	$.ajax({//加载团队人员
	   url: url,
	   data:{
		   page:page,
		   customer_id:customer_id,
		   user_id:user_id
		   },
	   type: "POST",
	   dataType:'json',
	   async: true,     
	   success:function(res){
		   if(res!=""){
			 $('.people').text(res[0]['rcount']); 
			 data_null = 0;
		   }else if(pass == 0){
			 $('.people').text(0);
		   }
		    var html = '';
			if(res==''){				//假如无数据则隐藏
			data_null = 1;
			$('.loadingPop').hide();
			}
			if(res.length<10){
				downFlow = true;
			}
			var userName = "";
			for(id in res){
				userName = "";
				i++;
				html+='<li class="itemWrapper">';
				html+='<div class = "itemWrapper-main clearfix">';
				html+='<div class = "itemWrapper-main-left1">';
				html+='<img class = "itemWrapper-main-left1-img" id = "itemWrapper-main-left1-img'+i+'" indexid = "'+i+'" src="'+res[id]['weixin_headimgurl']+'" width="60" height="60">';
				html+='</div>';
				html+='<div  class = "itemWrapper-main-left2">';
				html+='<div class = "itemWrapper-main-left2-row1">';
				userName = res[id]['user_name'];
				if(userName!=null){
					if(userName.length>6){
					userName = userName.substring(0,6);
					html+='<span>'+userName+'...</span>';
					}else{
						html+='<span>'+userName+'</span>';
					}
				}else{
					html+='<span>'+userName+'</span>';
				}
				
				if(res[id]['pro_id']>0){
					if(res[id]['is_consume']>0){//判断是否为股东身份
						html+='<span class = "itemWrapper-juese itemWrapper-juese-1">';
						if(1==res[id]['is_consume']){
							html+=shareholderArr[4];
						}else if(2==res[id]['is_consume']){
							html+=shareholderArr[3];
						}else if(3==res[id]['is_consume']){
							html+=shareholderArr[2];
						}else if(4==res[id]['is_consume']){
							html+=shareholderArr[1];
						}
						html+='</span>';
					}else if(5==res[id]['isAgent']||6==res[id]['isAgent']||7==res[id]['isAgent']||8==res[id]['isAgent']){//判断是否为区域代理
						
						html+='<span class = "itemWrapper-juese itemWrapper-juese-0">';
						if(0==is_showcustomer){
							html+='区代';
						}else if(5==res[id]['isAgent']){//区代自定义名字
							html+=a_customer;
						}
						else if(6==res[id]['isAgent']){//市代自定义名字
							html+=c_customer;
						}
						else if(7==res[id]['isAgent']){//省代自定义名字
							html+=p_customer;
						}
						else if(8==res[id]['isAgent']&&0==is_diy_area){//自定义区域名字
							html+='区代';
						}
						else if(8==res[id]['isAgent']&&1==is_diy_area){
							html+=diy_customer;
						}
						html+='</span>';
					}else if(1==res[id]['isAgent']){//代理商
						html+='<span class = "itemWrapper-juese itemWrapper-juese-5">代理商</span>';
					}else if(3==res[id]['isAgent']){//供应商
						html+='<span class = "itemWrapper-juese itemWrapper-juese-4">供应商</span>';
					}else if(4==res[id]['isAgent']){//技师
						html+='<span class = "itemWrapper-juese itemWrapper-juese-3">技师</span>';
					}else if(0==res[id]['isAgent']){//推广员
						html+='<span class = "itemWrapper-juese itemWrapper-juese-2"><?php echo $exp_name ;?></span>';
					}
				}
				html+='</div>';
				html+='<div class = "itemWrapper-main-left2-row2">';
				if(res[id]['parent_name']!=""){
					html+='<span>推荐人: <font>';
					html+=res[id]['parent_name'];
					html+='</font></span>';
				}
				html+='</div>';
				html+='<div class = "itemWrapper-main-left2-row3">';
				html+='<span>'+res[id]['sq_time']+'</span>';
				html+='</div>';
				html+='</div>';
				html+='	<div class="itemWrapper-main-right">';
				html+='<div  class="itemWrapper-main-right-row1">';
				html+="<img class = 'itemWrapper-main-right-row1-img' src='./images/vic/right_arrow.png' width='8' height='13' style='' onclick="+'Post_data('+res[id]['p_id']+')'+">";
				html+='</div>';
				html+='<div class = "itemWrapper-main-right-row2">';
				html+='<span>来源:</span>';
				if(1==res[id]['fromw']){
					html+='<img src = "images/goods_image/20160050501.png">';
				}else if(2==res[id]['fromw']){
					html+='<img src="images/goods_image/20160050502.png">';
				}
				else if(3==res[id]['fromw']){
					html+='<img src="images/goods_image/20160050503.png">';
				}
				else if(4==res[id]['fromw']){
					html+='<img src="images/goods_image/appFromw.png">';
				}
				else if(5==res[id]['fromw']){
					html+='<img src="images/goods_image/webFromw.png">';
				}else{
					html+='<span>未知</span>';
				}
				html+='</div>';
				html+='</div>';
				html+='</div>';
				html+='</li>';
			}	
				$('ul').append(html);	//加载数据
				$('.loadingPop').hide();
				$('.loadingPop').removeClass('wait');
	  }, 
	   error:function(er){
		return false;
	   }
	});
}
$(window).scroll(function () {
	if (($(window).scrollTop()) >= ($(document).height() - $(window).height())) {
		if($('.loadingPop').hasClass('wait') || downFlow){
			return;
		}else{
			if(data_null==1){
				return
			}
			url = "myteam_data.php?user_id="+user_id+'&statu=member';
			if(choose_val>0 || commisions_val>0){
				url = "myteam_data.php?statu=member&choose_val="+choose_val+"&commisions_val="+commisions_val;
			}
			if(search_text!=""){
				url = "myteam_data.php?statu=member&search_text="+search_text;
			}
			$('.loadingPop').show();
			page = page + 1;
			pass = 1;
			$('.loadingPop').addClass('wait');
			show_data(url,page,customer_id,user_id);
		}
	}
});

$(".searchFor").click(function(){//搜索关键字
	downFlow = false;
	search_text  = $('.search').val();
	if(search_text==""){
		return false;
	}
	url = "myteam_data.php?statu=member&search_text="+search_text;
	$('.itemWrapper').remove();
	$('.loadingPop').show();
	pass = 0;
	page = 1;
	$('.people').text(0);
	show_data(url,1,customer_id,user_id);
	
	});
</script>
<script>
// condition监听事件
$("#all_btn").click(
	function(){
		//alert("condition按键点击了");
		showConditionDlg();
	}
);

</script>
<script>
function Post_data(persion_id){
	/* 将GET方法改为POST ----start---*/	
    var objform = document.createElement('form');
	document.body.appendChild(objform);
	
	var obj_p = document.createElement("input");
	obj_p.type = "hidden";
	objform.appendChild(obj_p);
	obj_p.value = persion_id;
	obj_p.name = "persion_id";
	
	objform.action = "team_person.php?customer_id="+customer_id;
	objform.method = "POST"
	objform.submit();
	/* 将GET方法改为POST ----end---*/
	
}
</script>
<?php require('../common/share.php'); ?>
<script src="./js/goods/myteam.js"></script>
</body>
</html>
