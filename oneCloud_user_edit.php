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
	
	$keyid = -1;  //区域编号
	(int)$keyid = $configutil->splash_new($_GET["keyid"]); 
	if($keyid<1){
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
	$query = 'SELECT all_areaname,grade FROM weixin_commonshop_team_area  where isvalid=true and customer_id='.$customer_id.' and id='.$keyid.' limit 0,1';
	$result = mysql_query($query) or die('L23 Query failed: ' . mysql_error());  										 
	while ($row = mysql_fetch_object($result)) {
	   $all_areaname =  $row->all_areaname ;
	   $grade = $row->grade;
	   switch($grade){
		   case 0: $gradeName = "区级"; break;
		   case 1: $gradeName = "市级"; break;
		   case 2: $gradeName = "省级"; break;		   
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
<title>区域负责人设置</title>
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
					<a class="white1">区域负责人设置</a>
				</div>
			</div>
			<!--列表头部切换结束-->
            <!--关注用户开始-->
            <form action="oneCloud_user_edit.class.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>"  method="post" id="upform" name="upform">
              <div id="area">
					<div class="WSY_data" id="oneCloud_area">
                    <dl class="WSY_bulk">
                        <dt>当前区域：</dt>
                        <dd> <label><?php echo $all_areaname; ?></label></dd>
                    </dl>
					</div>
					<div class="WSY_data" id="oneCloud_leave">
                    <dl class="WSY_bulk">
                        <dt>等级：</dt>
                        <dd> <label><?php echo $gradeName; ?></label></dd>
                    </dl>
					</div>
                    <div id="div_menucontent">

                          <div class="WSY_nonebox">   
                              <dl class="WSY_bulk WSY_bulknone WSY_bulk" style="height:auto;">
                                  <dt>负责人列表：</dt>
                  
                                     <div>
												<dd>
												<select name="location_user" id="location_user"  >
												<?php 
													$num = 0;
													//查询有效的 区域负责人名单
												    $query = 'SELECT a.aplay_user_id,b.name,b.weixin_name FROM weixin_commonshop_team_aplay as a LEFT JOIN weixin_users as b on a.aplay_user_id = b.id  where a.isvalid=true and a.now_area=0 and a.status=1 and a.customer_id='.$customer_id;
													echo  $query;
												    $result = mysql_query($query) or die('L113 Query failed: ' . mysql_error());  	 									 
												    while ($row = mysql_fetch_object($result)) {
													   $aplay_user_id =  $row->aplay_user_id;
													   $name = $row->name;
													   $weixin_name = $row->weixin_name;	
													   if(!empty($weixin_name)){
														   $name .= "(". $weixin_name.")";
													   }
														echo "<option value='".$aplay_user_id."'>".$name."</option>";
												    }
													$num = mysql_num_rows($result);
													if($num == 0){
														echo "<option value=''>无可用负责人</option>"; 
													}
												?>															
												</select>
												<input type="hidden" name="areaID" value="<?php echo $keyid; ?>" />		
												<input type="hidden" name="areaLeave" value="<?php echo $grade; ?>" />		
												<input type="hidden" name="pagenum" value="<?php echo $pagenum; ?>" />		
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
	


<script type="text/javascript">
function submitV(){	
	var location_user = $("#location_user").val();
	if(location_user == ""){
		alert("请选择有效的区域负责人");
		return;
	}

	$("#upform").submit();
}
</script>

</body>
<?php mysql_close($link);?>
</html>