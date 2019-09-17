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
<link rel="stylesheet" type="text/css" href="../css/chosen.css">
<script type="text/javascript" src="../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../common/js/layer/layer.js"></script>
<script charset="utf-8" src="../common/js/jquery.jsonp-2.2.0.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>区域负责人手动添加</title>
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
.WSY_bulk dd #promoter_name{
	width: 100px;
	height: 30px;
	display: inline;
	border: 1px solid #ddd;
	vertical-align: middle;
	border-radius: 5px;
	margin-left:10px;
}
.WSY_bulk dd .search_button{
	margin-top:0px;
	width: 75px;
	height: 30px;
	margin-top:1px;
}
#promoter{
	float: left;
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
					<a class="white1">区域负责人手动添加</a>
					<a href="oneCloud.php?customer_id=<?php echo passport_encrypt($customer_id);?>">基本设置</a>
					<a href="oneCloud_area.php?customer_id=<?php echo passport_encrypt($customer_id);?>">区域设置</a>
					<a href="oneCloud_UserList.php?customer_id=<?php echo passport_encrypt($customer_id);?>">人员管理</a>				
				</div>
			</div>
			<!--列表头部切换结束-->
            <!--关注用户开始-->
            <form action="oneCloud_user_add.class.php?customer_id=<?php echo passport_encrypt((string)$customer_id); ?>"  method="post" id="upform" name="upform">
              <div id="area"> 

                    <div id="div_menucontent">

                          <div class="WSY_nonebox">   
                              <dl class="WSY_bulk WSY_bulknone WSY_bulk" style="height:auto;">
                                  <dt>区域：</dt>
                  
                                     <div id="div_province" >
												<dd style="overflow:auto">
												<select name="promoter" id="promoter">
												<option value="">请选择推广员</option>
												<?php 
												$query_promoter = 'SELECT pro.id,pro.user_id,users.name,users.weixin_name from promoters as pro LEFT JOIN weixin_users as users on users.id=pro.user_id  where  pro.isAgent = 0 and pro.isvalid=1 and pro.customer_id = '.$customer_id ." order by pro.user_id"; 
												$result_promoter = mysql_query($query_promoter) or die('Query_promoter failed: ' . mysql_error());
												while ($row_promoter = mysql_fetch_object($result_promoter)) {		
													$user_id =  $row_promoter->user_id;			
													$name =  $row_promoter->name;													
													$weixin_name =  $row_promoter->weixin_name;	
													$names = "[编号". $user_id . "] ".$name;
													if(!empty($weixin_name)){
														$names .= "(" . $weixin_name  . ")";
													}
												?>
												<option value="<?php echo $user_id; ?>"><?php echo $names; ?></option>
												<?php } ?>
												</select>
												
												<input value="" placeholder="输入推广员编号" name="promoter_name" id="promoter_name" type="text">
												<input type="button" class="WSY_button search_button" onClick="search_promoter();" value="搜索">
												</dd>
                                     </div>										 
									</dl>

									<dl class="WSY_member">
										<dt>申请人</dt>
										<dd><input value="" name="aplay_name" id="aplay_name" type="text"></dd>
									</dl>	

									<dl class="WSY_member">
										<dt>电话</dt>
										<dd><input value="" name="aplay_phone" id="aplay_phone" type="text"></dd>
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
function search_promoter(){	
	var promoter_val=$('#promoter_name').val();
	if(promoter_val != ""){
		$.ajax({
			type: "post",
			url: "search_oneCloud_user_add.php",
			
			data: { customer_id: <?php echo $customer_id?>,id:promoter_val},
			success: function (result) {
				var Json = eval(result);
				var html = '<option value="">请选择推广员</option>';
				for (var i = 0; i < Json.length; i++) {
					html +='<option value='+ Json[i].user_id +'>'+ Json[i].names +'</option>';
				}
				$("#promoter").html(html); 			
			}
		})
	}
}

function submitV(){	
	var promoter = $("#promoter").val();
	if(promoter == ""){
		alert("请选择推广员");
		return;
	}	
	var aplay_name = $("#aplay_name").val();
	if(aplay_name == ""){
		alert("请填写申请人名称"); 
		return;
	}	
	var aplay_phone = $("#aplay_phone").val();
	if(aplay_phone == ""){
		alert("请填写申请人手机号"); 
		return;
	}
	$("#upform").submit();
}
</script>

</body>
<?php mysql_close($link);?>
</html>