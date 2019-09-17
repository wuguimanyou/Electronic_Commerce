<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');

//头文件----start
require('../common/common_from.php');
//头文件----end
$status 		 = -1;
$weixin_fromuser = '';
$has_change 	 =  0;
$pro_id 		 = -1;
$query = "SELECT p.id,u.weixin_fromuser,p.status,u.has_change from weixin_users u LEFT JOIN promoters p ON p.user_id = u.id where u.id=".$user_id." AND u.isvalid=true AND p.isvalid=TRUE AND p.status=1  limit 1";
$result= mysql_query($query);
while($row=mysql_fetch_object($result)){
	$pro_id 		 = $row->id;
	$weixin_fromuser = $row->weixin_fromuser;
	$status 		 = $row->status;
	$has_change 	 = $row->has_change;
}

//查询自定义功能
if($status>0){
	$query_custom	 ="select id,subscribe_id,need_score,imgurl from weixin_commonshop_subscribes where isvalid=true  and customer_id=".$customer_id." ORDER BY id desc";
}else{
	$query_custom	 ="select id,subscribe_id,need_score,imgurl from weixin_commonshop_subscribes where isvalid=true and is_needmember=0 and customer_id=".$customer_id." ORDER BY id desc ";
}
$result_custom = mysql_query($query_custom) or die('Query failed28: ' . mysql_error());
$count   = mysql_num_rows($result_custom);



$is_cashback 	= 0;//是否开启消费返现
$modify_up 		= 0;
$modify_type =  0;//修改类型：0、顶级粉丝能修改一次关系；1、顶级用户能修改一次关系；2、所有用户能修改一次关系
$is_cashback =  0;//是否开启消费返现
$isOpenreward	= 0;	//是否开启累计佣金
$is_my_commission= 0;//是否开启我的佣金
$sql = "SELECT is_cashback,modify_up,modify_type,is_my_commission,openbillboard,isOpenreward FROM weixin_commonshops WHERE isvalid=true AND customer_id=".$customer_id." LIMIT 1";
$res = mysql_query($sql) or die('Query failed34: ' . mysql_error());
while( $row = mysql_fetch_object($res) ){
	$is_cashback 		= $row->is_cashback;	//是否开启消费返现
	$modify_up 	 		= $row->modify_up;		//是否开启修改上下级
	$modify_type 		= $row->modify_type;
	$isOpenreward		= $row->isOpenreward;	//是否开启累计佣金
	$is_my_commission	= $row->is_my_commission;//是否开启我的佣金
	$openbillboard 		= $row->openbillboard;	//龙虎榜
}

//修改上下级
$is_modify_up = 0;
if( $modify_up ){
	switch($modify_type){
		case 0:
			if( $status < 1 and $parent_id < 0 ){
				$is_modify_up = 1;
			}
			break;
		case 1:
			if( $parent_id < 0 ){
				$is_modify_up = 1;
			}
			break;
		case 2:
			$is_modify_up = 1;
			break;
	}
} 


//echo $is_modify_up."===".$has_change;die;
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>所有功能</title>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<meta content="telephone=no" name="format-detection" />
	<style type="text/css">

	html,body{
	margin:0;
	padding:0;
	_background: none;
	font-family:微软雅黑;
}
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
		
	}
	.warp ul li img{
		width:35px;
		margin-top: 25px;
		margin-left: 15%;
		float: left;
		position: center;
	}
	.warp ul li span{
		margin-left: 10px;
		margin-top: 30px;
		line-height: 6;
		font-size: 14px;
		color:#888;
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
        	box-shadow:  0 0 5px 0 rgba(0,0,0,0.24);
        }

	</style>
</head>
<body>

	<!-- <div class="header">功能列表</div> -->
	<div class="warp">
			<ul>
				<?php 
					if($is_cashback==1){

				?>
				<li class="button buttonclick" onclick='goPage("../common_shop/jiushop/cashback.php?user_id=<?php echo passport_encrypt((string)$user_id) ?>&customer_id=<?php echo $customer_id_en; ?>")'>
					<img src="./images/info_image/wode_fanxian.png" alt="">
					<span>我的返现</span>
				</li>
				<?php }?>

				<?php if( $is_my_commission == 1 && $pro_id>0){ ?>
				<li class="button buttonclick" onclick='goPage("../common_shop/jiushop/my_reward.php?user_id=<?php echo passport_encrypt((string)$user_id) ?>&customer_id=<?php echo $customer_id_en; ?>; ?>")'>		
					<img src="./images/info_image/commission.png" alt="">
					<span>我的佣金</span>
				</li>	
				<?php }?>



				<?php if( $openbillboard == 1 && $pro_id>0){ ?>
				<li class="button buttonclick" onclick='goPage("../common_shop/jiushop/longhuban.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo passport_encrypt((string)$user_id) ?>")'>		
					<img src="./images/info_image/dragon.png" alt="">
					<span>店铺龙虎榜</span>
				</li>	
				<?php }?>

				<?php 	
					$is_charitable        = 0;//慈善开关
					$query ="select is_charitable from charitable_set_t where isvalid=true and customer_id=".$customer_id;
					$result = mysql_query($query) or die('Query failed: ' . mysql_error());
					while ($row = mysql_fetch_object($result)) {
						$is_charitable        = $row->is_charitable;
					}
					
					if($is_charitable==1){
				?>
				<li class="button buttonclick" onclick='goPage("../common_shop/jiushop/charitable.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo passport_encrypt((string)$user_id) ?>")'>		
					<img src="./images/info_image/charity.png" alt="">
					<span>我的慈善</span>
				</li>
				<?php }?>
				<!--修改上下级-->
				<?php if( $is_modify_up ==1 and $has_change == 0 and $pro_id>0){ ?>
				<li class="button buttonclick" onclick='goPage("../common_shop/jiushop/change_relation_user.php?customer_id=<?php echo $customer_id_en; ?>&user_id=<?php echo passport_encrypt((string)$user_id) ?>")'>		
					<img src="./images/info_image/superior.png" alt="">
					<span>修改上级</span>
				</li>	
				<?php }?>
				<?php 
						$imgurl ='';
					while ($row_c = mysql_fetch_object($result_custom)) {
						$cs_id = $row_c->id;
						$subscribe_id = $row_c->subscribe_id;
						$need_score = $row_c->need_score;
						$imgurl =$row_c->imgurl;
						
						if($imgurl == '' ){
							$imgurl='./images/info_image/function.png';
						}
						
						$imgurl = $new_baseurl.$imgurl;
						$query = "SELECT id,title,website_url FROM weixin_subscribes where  id=".$subscribe_id;
						$result = mysql_query($query) or die('Query failed: ' . mysql_error());
						$website_url="";
						$title="";
						while ($row = mysql_fetch_object($result)) {
							$website_url = $row->website_url;
							$title = $row->title;
						}
						$pos = strpos($website_url,"?"); 
						if($pos>0){
							$website_url = $website_url."&C_id=".$customer_id."&fromuser=".$weixin_fromuser;
						}else{
							$website_url = $website_url."?C_id=".$customer_id."&fromuser=".$weixin_fromuser;
						}
						$mppos= strstr($title,"{weixin_title}");
						if(!empty($mppos)){
							$title = str_replace("{weixin_title}",$weixin_name,$title);
						}
							$mppos= strstr($title,"{weixin_parent_title}");
						if(!empty($mppos) and $parent_id>0){
							$query="select weixin_name from weixin_users where  isvalid=true and id=".$parent_id." limit 0,1";
							$result = mysql_query($query) or die('Query failed: ' . mysql_error());
							$parent_weixin_name="";
							while ($row = mysql_fetch_object($result)) {
								$parent_weixin_name = $row->weixin_name;
							}
								$title = str_replace("{weixin_parent_title}",$parent_weixin_name,$title);
						}


				?>
				<li class="button buttonclick" onclick='goPage("<?php echo $website_url?>")'>
					<img src="<?php echo $imgurl?>" alt="">
					<span><?php echo $title;?></span>
				</li>
				<?php }?>
			</ul>
		</div>
</body>
<script type="text/javascript">
	function goPage(url){
		if(url!==''){
			window.location.href=url;
		}
	}
</script>
<?php require('./NoShare.php');?>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</html>