<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$customer_id = passport_decrypt($customer_id);
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
require('../proxy_info.php');
// 分页---start
$pagenum = 1;
if(!empty($_GET["pagenum"])){
   $pagenum = $configutil->splash_new($_GET["pagenum"]);
}
$start = ($pagenum-1) * 20;
$end = 20;
$query = 'SELECT count(1) as wcount FROM weixin_commonshop_team_aplay where isvalid=true and customer_id='.$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$wcount =0;
$page=0;
while ($row = mysql_fetch_object($result)) {
	$wcount =  $row->wcount ;
}			
$page=ceil($wcount/$end);
// 分页---end
?>

	<!doctype html>
	<html>
	<head>
	<link rel="stylesheet" type="text/css" href="../common/css_V6.0/content.css">
	<link rel="stylesheet" type="text/css" href="../common/css_V6.0/content<?php echo $theme; ?>.css">
	<link type="text/css" rel="stylesheet" rev="stylesheet" href="../css/inside.css" media="all">
    <script type="text/javascript" src="../common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../common/js/inside.js"></script>
	<script type="text/javascript" src="../common/js_V6.0/content.js"></script>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<title>人员管理</title>
	<style type="text/css">
	.table-bordered {
	border: 1px solid #ddd;
	border-collapse: separate;
	-moz-border-radius: 4px;
	border-radius: 4px;
	}
	menu1.phpmedia="all"
	.table {
	width: 100%;
	margin-bottom: 20px;
	}
	menu1.phpmedia="all"
	.tb_class {
	font-size: 12px;
	text-align: center;
	margin: 0 auto;
	}
	.WSY_table_add{
	margin-left: 0;
	margin-top: 0;
	width: 100%;
	}
	</style>
	</head>
	<body>
	<!--内容框架开始-->
		<div class="WSY_content">
			<!--列表内容大框开始-->
			<div class="WSY_columnbox">
				<!--列表头部切换开始-->
				<div class="WSY_column_header">
					<div class="WSY_columnnav">
						<a href="oneCloud.php?customer_id=<?php echo passport_encrypt($customer_id);?>">基本设置</a>
						<a href="oneCloud_area.php?customer_id=<?php echo passport_encrypt($customer_id);?>">区域设置</a>
						<a class="white1" href="oneCloud_UserList.php?customer_id=<?php echo passport_encrypt($customer_id);?>">人员管理</a>
					</div>
				</div>
				<!--列表头部切换结束-->
				<!--自定义代码开始-->
				<div class="WSY_data" id="div_menucon">
					<!--列表按钮开始-->
					<div class="WSY_list">
						<li class="WSY_left"><a>人员审核<span></span></a></li>
						<ul class="WSY_righticon">
							<li><a href="oneCloud_user_add.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>">手动添加区域负责人</a></li>
							<!--<li class="WSY_inputicon"><input type="button" value="批量删除"></li>--> 
						</ul>
					</div>
					<!--列表按钮开始-->
					<table width="97%" class="WSY_table" id="custom">
						<thead class="WSY_table_header">
							<th width="3%"><input id="s" onclick="$(this).attr('checked')?checkAll():uncheckAll()" type="checkbox" name="sex"></th>
							<th width="7%">ID</th>
							<th width="15%">微信昵称</th>
							<th width="15%">申请信息</th>
							<th width="6%">申请区域</th>
							<th width="15%">分配区域</th>
							<th width="6%">审核状态</th>
							<th width="13%">申请时间</th>
							<th width="20%">操作</th>
						</thead>
					</table>

					<table width="97%" class="WSY_table" cellspacing="1" cellpadding="1" border="0"  class="tb_class table table-bordered"  style="margin-bottom:5px;margin-top:0;" bgcolor="#fff">
						<?php
						//区域代理申请表
					   $query_teamaplay = 'SELECT id,aplay_user_id,aplay_grate,aplay_name,aplay_phone,createtime,status,now_area FROM weixin_commonshop_team_aplay where isvalid=true and customer_id='.$customer_id." order by createtime desc limit ".$start.",".$end;
					   $keyid = -1;
					   $aplay_user_id = -1;
					   $aplay_grate = 0;
					   $aplay_name = "";
					   $aplay_phone = "";
					   $createtime = "";
					   $status = -1;
					   $grate_name = "";
					   $status_str = "";//审核状态
					   $now_area = 0;//当前分配区域
					   $result_teamaplay = mysql_query($query_teamaplay) or die('Query_teamaplay failed: ' . mysql_error());
					   $rcount_q = mysql_num_rows($result_teamaplay);
					   while ($row = mysql_fetch_object($result_teamaplay)) {
						   $keyid =  $row->id ;
						   $aplay_user_id = $row->aplay_user_id;
						   $aplay_grate = $row->aplay_grate;  //0：区代  1：市代   2：省代
						   $aplay_name = $row->aplay_name;
						   $aplay_phone = $row->aplay_phone;		   
						   $createtime = $row->createtime;	//申请时间
						   $status = $row->status;			//申请状态 0：审核  1：确认
						   $now_area = $row->now_area;			//当前分配区域
						   
						   switch($aplay_grate){
							   case 0: $grate_name="区代";break;
							   case 1: $grate_name="市代";break;
							   case 2: $grate_name="省代";break;
						   }
						   
						   switch($status){
							   case -1:	$status_str	= "驳回申请"; break;
							   case 0:	$status_str	= "审核中"; break;
							   case 1:	$status_str	= "审核通过"; break;
									
						   }
						   //查询个人的微信昵称
							$query_user="select name,weixin_name from weixin_users where isvalid=true and id=".$aplay_user_id." and customer_id=".$customer_id." limit 0,1";
							$username="";
							$weixin_name="";
							$userphone="";
							$result_user = mysql_query($query_user) or die('Query_user failed: ' . mysql_error());
							while ($row2 = mysql_fetch_object($result_user)) {	
								$username=$row2->name;
								$weixin_name = $row2->weixin_name;
								$username = $username."(".$weixin_name.")";
							}
							//分配区域名称
							$query_teamarea="select all_areaname from weixin_commonshop_team_area where isvalid=true and area_user=".$aplay_user_id." and id=".$now_area." and customer_id=".$customer_id;
							$areaname = "未分配";//当前分配区域名称
							$result_teamarea = mysql_query($query_teamarea) or die('Query_teamarea failed: ' . mysql_error());
							while ($row2 = mysql_fetch_object($result_teamarea)) {	
								$areaname=$row2->all_areaname;
							}
							
						?>  
						<tr><td>
						<table width="97%" class="WSY_table WSY_table_add" id="custom">
							<tr id="record_<?php echo $keyid;?>">
								<td width="3%"><input type="checkbox" name="code_Value" value="1"></td>
								<td width="7%" class="xuhao"><?php echo $aplay_user_id ?></td>
								<td width="15%"><a style="color: #5493F1;" href="qrsell.php?search_user_id=<?php echo $aplay_user_id; ?>&customer_id=<?php echo $customer_id; ?>"><?php echo $username; ?></a></td>
								<td width="15%">申请姓名：<?php echo $aplay_name; ?><br>联系电话：<?php echo $aplay_phone; ?></td>
								<td width="6%"><?php echo $grate_name; ?></td>
								<td width="15%"><?php echo $areaname; ?></td>
								<td width="6%" id="status_<?php echo $keyid;?>"><?php echo $status_str; ?></td>
								<td width="13%"><?php echo $createtime; ?></td>
								<td class="WSY_t4" width="20%">
									<?php if($status==0){?>
										<a id="through_<?php echo $keyid; ?>" onclick="UserConfirm(<?php echo $keyid;?>,<?php echo $aplay_user_id;?>,1)" title="通过"><img src="../common/images_V6.0/operating_icon/icon23.png"></a>
										<a id="rejected_<?php echo $keyid; ?>" onclick="UserConfirm(<?php echo $keyid;?>,<?php echo $aplay_user_id;?>,2)" title="驳回"><img src="../common/images_V6.0/operating_icon/icon25.png"></a>
									<?php }?>
									<?php if($status==1 && $now_area==0){?>
										<a href="oneCloud_set_edit.php?aplay_user_id=<?php echo $aplay_user_id ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&pagenum=<?php echo $pagenum ?>" title="编辑区域负责人"><img src="../common/images_V6.0/operating_icon/icon05.png"></a>
									<?php }?>									
										<a id="delete" onclick="UserConfirm(<?php echo $keyid;?>,<?php echo $aplay_user_id;?>,3)" title="删除"><img src="../common/images_V6.0/operating_icon/icon04.png"></a>
								</td>
							</tr>
						</table>
						</td></tr>
					  <?php } ?>
					</table>  
					
					<!--表格结束-->
				  
				</div>
				<!--自定义菜单代码结束-->            
				
				<!-- 分页 --start-->
				<div class="WSY_page">	
				</div>
				<!-- 分页 --end-->
			</div>
			<!--列表内容大框开始-->
			
			
		</div>    
		<!--内容框架开始-->
		

	<?php mysql_close($link); ?>
	

<script type="text/javascript" src="../js/tis.js"></script>
<!-- 分页 --start-->
<script src="../js/fenye/jquery.page1.js"></script>
<script type="text/javascript" > 
	var customer_id = "<?php echo passport_encrypt((string)$customer_id);?>";
	var pagenum = <?php echo $pagenum ?>;
	var count =<?php echo $page ?>;//总页数
	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
		pageCount:count,
		current:pagenum,
		backFn:function(p){
		document.location= "oneCloud_UserList.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+p;
	   }
	});

  var page = <?php echo $page ?>;
  
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	document.location= "oneCloud_UserList.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a;
	}
  }
<!-- 分页 --end-->
		
		
		function UserConfirm(id,user_id,type){
			 switch(type){
			  case 1:
				 var i = window.confirm("确认该成员通过审核吗?");
			  break; 
			  case 2:
				 var i = window.confirm("驳回申请");
			  break;
			  case 3:
				 var i = window.confirm("确认删除此记录?");
			  break;
			 }
			 console.log(i+"="+type);
			if(i===true){
				$.ajax({
					type: 'POST',
					url: "oneCloud_UserList_Confirm.php?customer_id=<?php echo passport_encrypt((string)$customer_id);?>",
					data: {
						keyid:id, 
						type:type, 
						user_id:user_id
					},
					dataType: "json",
					success:function(data){
						alert(data.errorMsg);
							switch(data.status){
								case 1001:
									$("#through_"+id).hide();
									$("#rejected_"+id).hide();
									$("#status_"+data.keyid).html(data.status_str);
									url="oneCloud_set_edit.php?aplay_user_id="+user_id+"&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+pagenum;
									setTimeout(location.replace(url) , 1000);
								break;
								case 1002:
									$("#through_"+id).hide();
									$("#rejected_"+id).hide();
									$("#status_"+data.keyid).html(data.status_str);							
								break;
								case 1005:
								break;
								default:
									url="oneCloud_UserList.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+pagenum;
									setTimeout(location.replace(url) , 1000);
							}
					} 

				}); 
			}else{ 
				return false; 
			}
	}
	</script> 

	</body>
	</html>