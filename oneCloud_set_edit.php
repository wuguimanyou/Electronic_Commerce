<?php
	header("Content-type: text/html; charset=utf-8"); 
	require('../config.php');
	$customer_id = passport_decrypt($customer_id);
	require('../back_init.php');  
 
	$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	mysql_query("SET NAMES UTF8");
    
	require('../proxy_info.php');
	
	$aplay_user_id = -1;  //负责人编号
	(int)$aplay_user_id = $configutil->splash_new($_GET["aplay_user_id"]); 
	if($aplay_user_id<1){
		echo "<script>alert('参数不正确');window.history.go(-1)</script>";
		return;
	}
	$pagenum = -1;  //区域编号
	(int)$pagenum = $configutil->splash_new($_GET["pagenum"]); 
	if($pagenum<1){
		echo "<script>alert('页数不正确');window.history.go(-1)</script>";
		return;
	}
	
	//查询区域信息
	$area_user_name = "";
	$query = 'SELECT a.name,a.weixin_name,b.aplay_grate FROM weixin_users as a left join weixin_commonshop_team_aplay as b on a.id=b.aplay_user_id where a.isvalid=true and b.isvalid=true and a.customer_id='.$customer_id.' and a.id='.$aplay_user_id.' limit 0,1';
	$result = mysql_query($query) or die('L31 Query failed: ' . mysql_error());  										 
	while ($row = mysql_fetch_object($result)) {
	   $weixin_name =  $row->weixin_name ;
	   $name = $row->name;
	   $aplay_grate = $row->aplay_grate;
	   
		if(!empty($weixin_name)){
			$area_user_name = $name."(".$weixin_name.")";
		}else{
			$area_user_name = $name ;
		}	
		$grateName = "区级";
		$area_leave= $aplay_grate;
		switch($aplay_grate){
			case 0: $grateName = "区级"; break;
			case 1: $grateName = "市级"; break;
			case 2: $grateName = "省级"; break; 
			default:
		}
	}	
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../common/js/layer/layer.js"></script>
<script charset="utf-8" src="../common/js/jquery.jsonp-2.2.0.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>负责人区域设置</title>
</head>
<style>
.button_icon01:hover{
	background-color: #20b3a4;
	border: solid 1px #20b3a4;
	color: #fff;
}
.button_icon01{
	display: block;
	cursor: pointer;
	background-color: #f3f3f3;
	border: solid 1px #dadada;
	margin-right: 10px;
	padding-left: 10px;
	padding-right: 10px;
	line-height: 24px;
	margin-right:10px;
}
#div_text p i{	
	display: block;
	height: 24px;
	float: left;
	line-height: 24px;
}
#div_text p input{	
	width: 200px;
	height: 24px;
	border: solid 1px #ccc;
	border-radius: 2px;
	padding-left: 5px;
}
#div_text p{
	display: block;
	float: none;
	overflow: hidden;
	margin-top: 5px;
	margin-bottom: 10px;
}

</style>
<body>
<!--内容框架-->
	<div class="WSY_content">

		<!--列表内容大框-->
		<div class="WSY_columnbox">
			<!--列表头部切换开始-->
			<div class="WSY_column_header">
				<div class="WSY_columnnav">
					<a class="white1">负责人区域设置</a>
					<a href="oneCloud.php?customer_id=<?php echo passport_encrypt($customer_id);?>">基本设置</a>
					<a href="oneCloud_area.php?customer_id=<?php echo passport_encrypt($customer_id);?>">区域设置</a>
					<a href="oneCloud_UserList.php?customer_id=<?php echo passport_encrypt($customer_id);?>">人员管理</a>					
				</div>
			</div>
			<!--列表头部切换结束-->
            <!--关注用户开始-->
            <form action="oneCloud_set_edit.class.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>"  method="post" id="upform" name="upform">
              <div id="area">
					<div class="WSY_data" id="oneCloud_area">
                    <dl class="WSY_bulk">
                        <dt>当前负责人：</dt>
                        <dd> <label><?php echo $area_user_name; ?></label></dd>
                    </dl>
					</div>

					<div class="WSY_data" id="oneCloud_area">
                    <dl class="WSY_bulk">
                        <dt>申请区域等级：</dt>
                        <dd> <label><?php echo $grateName; ?></label></dd>
                    </dl>
					</div>					
					
                 <div class="WSY_data" id="WSY_dataddbox">
                      <dl class="WSY_bulk">
                          <dt>区域等级：</dt>
                          <dd> <label><input type="radio" name="area_leave" value="2" <?php if($area_leave==2){?>checked<?php } ?> onClick="chksubmenu(2);" />省</label></dd>
                          <dd> <label><input type="radio" name="area_leave" value="1" <?php if($area_leave==1){?>checked<?php } ?> onClick="chksubmenu(1);" />市</label></dd>
                          <dd> <label><input type="radio" name="area_leave" value="0" <?php if($area_leave==0){?>checked<?php } ?> onClick="chksubmenu(0);" />区</label></dd>
                      </dl>
					</div>						
					
                    <div id="div_menucontent">

                          <div class="WSY_nonebox">   
                              <dl class="WSY_bulk WSY_bulknone WSY_bulk" style="height:auto;">
                                  <dt>区域列表：</dt>
                  
                                     <div id="div_province" <?php if($area_leave!=2){?> style="display:none" <?php } ?> >
												<dd>
												<select name="location_p" id="location_p"  >
												<?php 
													$num = 0;
													//查询有效的 区域负责人名单
												    $query = 'SELECT id,all_areaname FROM weixin_commonshop_team_area where area_user=-1 and isvalid=true and grade=2 and customer_id='.$customer_id;echo $query;													
												    $result = mysql_query($query) or die('L130 Query failed: ' . mysql_error());  	 									 
												    while ($row = mysql_fetch_object($result)) {
													   $areaID =  $row->id;
													   $all_areaname = $row->all_areaname;

														echo "<option value='".$areaID."'>".$all_areaname."</option>";
												    }
													$num = mysql_num_rows($result);
													if($num == 0){
														echo "<option value=''>无可用省级地区</option>"; 
													}
												?>															
												</select>		
												</dd>
                                  </div>	

                                     <div id="div_city" <?php if($area_leave!=1){?> style="display:none" <?php } ?> >
												<dd>
												<select name="location_c" id="location_c"  >
												<?php 
													$num = 0;
													//查询有效的 区域负责人名单
												    $query = 'SELECT id,all_areaname FROM weixin_commonshop_team_area where area_user=-1 and isvalid=true and grade=1 and customer_id='.$customer_id;echo $query;													
												    $result = mysql_query($query) or die('L130 Query failed: ' . mysql_error());  	 									 
												    while ($row = mysql_fetch_object($result)) {
													   $areaID =  $row->id;
													   $all_areaname = $row->all_areaname;

														echo "<option value='".$areaID."'>".$all_areaname."</option>";
												    }
													$num = mysql_num_rows($result);
													if($num == 0){
														echo "<option value=''>无可用市级地区</option>"; 
													}
												?>															
												</select>		
												</dd>
                                  </div>	
								  
                                     <div id="div_area" <?php if($area_leave!=0){?> style="display:none" <?php } ?> >
												<dd>
												<select name="location_a" id="location_a"  >
												<?php 
													$num = 0;
													//查询有效的 区域负责人名单
												    $query = 'SELECT id,all_areaname FROM weixin_commonshop_team_area where area_user=-1 and isvalid=true and grade=0 and customer_id='.$customer_id;echo $query;													
												    $result = mysql_query($query) or die('L130 Query failed: ' . mysql_error());  	 									 
												    while ($row = mysql_fetch_object($result)) {
													   $areaID =  $row->id;
													   $all_areaname = $row->all_areaname;

														echo "<option value='".$areaID."'>".$all_areaname."</option>";
												    }
													$num = mysql_num_rows($result);
													if($num == 0){
														echo "<option value=''>无可用区级地区</option>"; 
													}
												?>															
												</select>		
												</dd>
                                  </div>									  
								  
									</dl>
                          </div>
                    </div>
                      
                      <div class="WSY_text_input01">					  
                          <div class="WSY_text_input">
								<input type="hidden" name="aplayID" value="<?php echo $aplay_user_id; ?>" />				
								<input type="hidden" name="pagenum" value="<?php echo $pagenum; ?>" />
								<input type="button" class="WSY_button" onClick="submitV();" value="提交"></div>
                      </div>
                  </div>
                </div>
              </form>
              <!--关注用户结束-->
		
        
        </div>
        <!--列表内容大框结束-->
	</div>
	<!--内容框架结束-->
	


<script type="text/javascript">
var level = <?php echo $aplay_grate; ?>;
function chksubmenu(type){
	level = type;
	switch(type){
		case 2:		
		$("#div_area").hide();
		$("#div_city").hide();
		$("#div_province").show();
		break;		
		case 1:
		$("#div_area").hide();
		$("#div_city").show();
		$("#div_province").hide();
		break;		
		case 0: 
		$("#div_area").show();
		$("#div_city").hide();
		$("#div_province").hide(); 
		break;
	}		
}

function submitV(){	
	switch(level){
		case 2:
		var div_province = $("#location_p").val();
		if(div_province == ""){
			alert("请选择正确的省级区域");
			return;
		}
		break;		
		case 1:
		var div_city = $("#location_c").val();
		if(div_city == ""){
			alert("请选择正确的市级区域");
			return;
		}	
		break;		
		case 0:
		var div_area = $("#location_a").val();
		if(div_area == ""){
			alert("请选择正确的区级区域");
			return;
		}	
		break;
	}

	$("#upform").submit();
}
</script>

</body>
<?php mysql_close($link);?>
</html>