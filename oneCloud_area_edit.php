<?php
	header("Content-type: text/html; charset=utf-8"); 
	require('../config.php');
	$customer_id = passport_decrypt($customer_id);
	require('../back_init.php');  
 
	$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	mysql_query("SET NAMES UTF8");
  
	$area_leave= 2;
  
	require('../proxy_info.php');
	
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
<title>区域设置</title>
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
					<a class="white1">区域设置</a>
					<a href="oneCloud.php?customer_id=<?php echo passport_encrypt($customer_id);?>">基本设置</a>
					<a href="oneCloud_area.php?customer_id=<?php echo passport_encrypt($customer_id);?>">区域设置</a>
					<a href="oneCloud_UserList.php?customer_id=<?php echo passport_encrypt($customer_id);?>">人员管理</a>				
				</div>
			</div>
			<!--列表头部切换结束-->
            <!--关注用户开始-->
            <form action="oneCloud_area_edit.class.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>"  method="post" id="upform" name="upform">
              <div id="area">
                  <div class="WSY_data" id="WSY_dataddbox">
                      <dl class="WSY_bulk">
                          <dt>区域等级：</dt>
                          <dd> <label><input type="radio" name="area_leave" value=2 <?php if($area_leave==2){?>checked<?php } ?> onClick="chksubmenu(2);" />省</label></dd>
                          <dd> <label><input type="radio" name="area_leave" value=1 <?php if($area_leave==1){?>checked<?php } ?> onClick="chksubmenu(1);" />市</label></dd>
                          <dd> <label><input type="radio" name="area_leave" value=0 <?php if($area_leave==0){?>checked<?php } ?> onClick="chksubmenu(0);" />区</label></dd>
                      </dl>
					</div>	 

                    <div id="div_menucontent">

                          <div class="WSY_nonebox">   
                              <dl class="WSY_bulk WSY_bulknone WSY_bulk" style="height:auto;">
                                  <dt>区域：</dt>
                  
                                     <div id="div_province" >
												<dd>
												<select name="location_p" id="location_p"  >
												</select>													
												<select name="location_c" id="location_c" style="display:none" >
												</select>				
												<select name="location_a" id="location_a" style="display:none" >
												</select>
												</dd>
                                     </div>										 
									</dl>
                          </div>
                      </div>
                      
                      <div class="WSY_text_input01">
                          <div class="WSY_text_input"><input type="button" class="WSY_button" onClick="submitV();" value="提交"></div>
                      </div>
                  </div>
                </div>
              </form>
              <!--关注用户结束-->
		
        
        </div>
        <!--列表内容大框结束-->
	</div>
	<!--内容框架结束-->
	

<script charset="utf-8" src="../common_shop/jiushop/js/region_select_name.js"></script>
<script type="text/javascript">
	/*省*/
	new PCAS('location_p','location_c','location_a' ,'北京市','',''); 
	/*省End*/
</script>
<script type="text/javascript">
var level = 2;
function chksubmenu(type){
	level = type;
	switch(type){
		case 2:		
		$("#location_a").hide();
		$("#location_c").hide();
		break;		
		case 1:
		$("#location_a").hide();
		$("#location_c").show();
		break;		
		case 0: 
		$("#location_a").show();
		$("#location_c").show();	 
		break;
	}		
}

function submitV(){	
	switch(level){
		case 2:
		var location_p = $("#location_p").val();
		if(location_p == ""){
			alert("请选择正确的省级区域");
			return;
		}
		break;		
		case 1:
		var location_p = $("#location_p").val();
		if(location_p == ""){
			alert("请选择正确的省级区域");
			return;
		}
		var location_c = $("#location_c").val();
		if(location_c == ""){
			alert("请选择正确的市级区域");
			return;
		}		
		break;		
		case 0:
		var location_p = $("#location_p").val();
		if(location_p == ""){
			alert("请选择正确的省级区域");
			return;
		}
		var location_c = $("#location_c").val();
		if(location_c == ""){
			alert("请选择正确的市级区域");
			return;
		}
		var location_a = $("#location_a").val();
		if(location_a == ""){
			alert("请选择正确的市级区域");
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