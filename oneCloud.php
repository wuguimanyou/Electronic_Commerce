<?php
	header("Content-type: text/html; charset=utf-8"); 
	require('../config.php');
	$customer_id = passport_decrypt($customer_id);

	require('../back_init.php');
	$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
	mysql_select_db(DB_NAME) or die('Could not select database');
	mysql_query("SET NAMES UTF8");
	require('../proxy_info.php');


	$QUERY_BASE = "SELECT id,team_all,consume_percent,consume_money,p_percent,p_people,p_order,c_percent,c_people,c_order,a_percent,a_people,a_order,agreement from weixin_commonshop_team WHERE isvalid = true AND customer_id = ".$customer_id." limit 0,1";
	$RESULT_BASE = mysql_query($QUERY_BASE) or die (" Wrong_1 : QUERY ERROR : ".mysql_error());
		$team_all = 0;				
		$consume_percent = 0;	
		$consume_money = 0;		
		$p_percent = 0;				
		$p_people = 0;				
		$p_order = 0;				
		$c_percent = 0;				
		$c_people = 0;				
		$c_order = 0;				
		$a_percent = 0;				
		$a_people = 0;					
		$a_order = 0;				
		$agreement = "";	
		if($row = mysql_fetch_object($RESULT_BASE)){
			$team_all = $row->team_all;					//团队总奖励
			$consume_percent = $row->consume_percent;	//消费奖励比例
			$consume_money = $row->consume_money;		//消费奖励最低金额
			$p_percent = $row->p_percent;				//省代奖励比例
			$p_people = $row->p_people;					//省代直推人数
			$p_order = $row->p_order;					//省代团队订单数
			$c_percent = $row->c_percent;				//市代奖励比例
			$c_people = $row->c_people;					//市代直推人数
			$c_order = $row->c_order;					//市代团队订单数
			$a_percent = $row->a_percent;				//区代奖励比例
			$a_people = $row->a_people;					//区代直推人数
			$a_order = $row->a_order;					//区代团队订单数
			$agreement = $row->agreement;				//团队申请协议
		}
		
		$query="select is_shareholder from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result=mysql_query($query);
		while ($row=mysql_fetch_object($result)){
			$is_shareholder=$row->is_shareholder;
		}
		$query="select shareholder_all from weixin_commonshop_shareholder where isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result=mysql_query($query);
		while ($row=mysql_fetch_object($result)){
			$shareholder_all=$row->shareholder_all;
		}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>区域团队奖励设置</title>
<link rel="stylesheet" type="text/css" href="../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../common/css_V6.0/content<?php echo $theme; ?>.css">
<script type="text/javascript" src="../common/js/jquery-2.1.0.min.js"></script>
<style>
.WSY_t6 input{width:200px;display:inline-block;margin:0;border:solid 1px #ccc;height:24px;border-radius:2px;padding-left:5px;margin-right:5px;}
.WSY_t6 span{display:inline-block;margin-top:6px;}
.WSY_table_little {text-align:left;text-indent:1em;}
</style>
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">
    
       <!--列表内容大框开始-->
	<div class="WSY_columnbox">
    	<!--列表头部切换开始-->
    	<div class="WSY_column_header">
        	<div class="WSY_columnnav">
				<a class="white1" href="oneCloud.php?customer_id=<?php echo passport_encrypt($customer_id);?>">基本设置</a>
				<a href="oneCloud_area.php?customer_id=<?php echo passport_encrypt($customer_id);?>">区域设置</a>
				<a href="oneCloud_UserList.php?customer_id=<?php echo passport_encrypt($customer_id);?>">人员管理</a>
			</div>
        </div>
        <!--列表头部切换结束-->
        
    <!--代理商设置代码开始-->
	<div class="WSY_data">
    
		<!--列表按钮开始-->
        <div class="WSY_list">
        	<li class="WSY_left"><a>区域团队奖励设置</a></li>
        </div>
        <!--列表按钮结束-->
		<form action="oneCloud.class.php?customer_id=<?php echo passport_encrypt($customer_id); ?>" method="post" id="upform" name="upform" onsubmit="return checkForm()">
        <!--表格1开始-->
        <table width="33%" class="WSY_table" id="WSY_t1" style="min-width: 640px;">
          <thead class="WSY_table_header">
            <th width="33%" class="WSY_table_little">团队总奖励比例</th>
          </thead>
          <tr>
            <td class="WSY_t6"><input name="team_all" id="team_all" value="<?php echo $team_all; ?>" placeHolder="团队总奖励(0-1)" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" /><span>(0~1)</span><span style="color:red">“团队奖励” 与 “股东奖励” 之和为1，则推广员无分佣佣金</span></td>
          </tr>	  
        </table>
        <!--表格1结束-->
 
        <!--表格2开始-->
		<?php if($is_shareholder!=1){?>
        <table width="65%" class="WSY_table" id="WSY_t1">
          <thead class="WSY_table_header">
            <th width="34%" class="WSY_table_little">无限级奖励比例 (总奖励比例不得大于1)</th>
            <th width="33%" class="WSY_table_little">无限级奖励最低金额</th> 
          </thead>
          <tr>
            <td class="WSY_t6"><span>无限级奖励比例：</span><input name="consume_percent" id="consume_percent" value="<?php echo $consume_percent; ?>" placeHolder="消费奖励比例(0-1)" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" /><span>(0~1)</span></td>
            <td class="WSY_t6"><input name="consume_money" id="consume_money" value="<?php echo $consume_money; ?>" placeHolder="消费奖励最低金额(元)" autocomplete="off" onkeyup="checkNum(this)" onafterpaste="checkNum(this)" /></td>
          </tr>	  
        </table>
		<?php }?>
        <!--表格2结束-->
         
        <!--表格3开始-->
        <table width="97%" class="WSY_table" id="WSY_t1">
          <thead class="WSY_table_header">
            <th width="34%" class="WSY_table_little">各级奖励比例设置 <?php if($is_shareholder==1){echo "(总奖励比例不得大于1)";}?></th>
            <th width="33%" class="WSY_table_little">各级直推人数设置</th>
            <th width="33%" class="WSY_table_little">各级团队订单数设置</th>
          </thead>
          <tr>
            <td class="WSY_t6"><span>省代奖励比例：</span><input name="p_percent" id="p_percent" value="<?php echo $p_percent; ?>" placeHolder="省代奖励比例(0-1)" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" /><span>(0~1)</span></td>
            <td class="WSY_t6"><span>升级省代需要直推人数：</span><input name="p_people" id="p_people" value="<?php echo $p_people; ?>" placeHolder="省代直推人数(人)" autocomplete="off" onkeyup="checkNum(this)" onafterpaste="checkNum(this)" /></td>
            <td class="WSY_t6"><span>升级省代需要订单数：</span><input name="p_order" id="p_order" value="<?php echo $p_order; ?>" placeHolder="省代团队订单数(单)" autocomplete="off" onkeyup="checkNum(this)" onafterpaste="checkNum(this)" /></td>
          </tr>

          <tr>
            <td class="WSY_t6"><span>市代奖励比例：</span><input name="c_percent" id="c_percent" value="<?php echo $c_percent; ?>" placeHolder="市代奖励比例(0-1)" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" /><span>(0~1)</span></td>
            <td class="WSY_t6"><span>升级市代需要直推人数：</span><input name="c_people" id="c_people" value="<?php echo $c_people; ?>" placeHolder="市代直推人数(人)" autocomplete="off" onkeyup="checkNum(this)" onafterpaste="checkNum(this)" /></td>
            <td class="WSY_t6"><span>升级市代需要订单数：</span><input name="c_order" id="c_order" value="<?php echo $c_order; ?>" placeHolder="市代团队订单数(单)" autocomplete="off" onkeyup="checkNum(this)" onafterpaste="checkNum(this)" /></td>
          </tr>
		  
          <tr>
            <td class="WSY_t6"><span>区代奖励比例：</span><input name="a_percent" id="a_percent" value="<?php echo $a_percent; ?>" placeHolder="区代奖励比例(0-1)" autocomplete="off" onkeyup="clearNoNum(this)" onafterpaste="clearNoNum(this)" /><span>(0~1)</span></td>
            <td class="WSY_t6"><span>成为区代需要直推人数：</span><input name="a_people" id="a_people" value="<?php echo $a_people; ?>" placeHolder="区代直推人数(人)" autocomplete="off" onkeyup="checkNum(this)" onafterpaste="checkNum(this)" /></td>
            <td class="WSY_t6"><span>成为区代需要订单数：</span><input name="a_order" id="a_order" value="<?php echo $a_order; ?>" placeHolder="区代团队订单数(单)" autocomplete="off" onkeyup="checkNum(this)" onafterpaste="checkNum(this)" /></td>
          </tr>		  
        </table>
        <!--表格3结束-->

		<div class="WSY_textboxF">
        	<h1>区域团队协议:</h1>
            <div class="text_box">                             						
                    <textarea cols="100" id="editor1" name="agreement" rows="10"><?php echo $agreement; ?></textarea>                 
            </div> 
		</div>
        <div class="WSY_text_input"><button class="WSY_button">提交保存</button><br class="WSY_clearfloat"></div>
		</form>
	</div>
    <!--代理商设置代码结束-->
    
</div>
</div>
<!--内容框架结束-->
<script type="text/javascript" src="../common/js_V6.0/content.js"></script>
<!--配置ckeditor和ckfinder-->
<script type="text/javascript" src="../../weixin/plat/Public/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/ckfinder/ckfinder.js"></script>
<!--编辑器多图片上传引入开始--->
<script type="text/javascript" src="../../weixin/plat/Public/js/jquery.dragsort-0.5.2.min.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/swfupload/swfupload/swfupload.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/swfupload/js/swfupload.queue.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/swfupload/js/fileprogress.js"></script>
<script type="text/javascript" src="../../weixin/plat/Public/swfupload/js/handlers.js"></script>
<!--编辑器多图片上传引入结束--->
<script>
CKEDITOR.replace( 'editor1',
{
extraAllowedContent: 'img iframe[*]',
filebrowserBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html',
filebrowserImageBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Images',
filebrowserFlashBrowseUrl : '../../weixin/plat/Public/ckfinder/ckfinder.html?Type=Flash',
filebrowserUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
filebrowserImageUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
filebrowserFlashUploadUrl : '../../weixin/plat/Public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
});
function clearNoNum(obj)
{
//先把非数字的都替换掉，除了数字和.
obj.value = obj.value.replace(/[^\d.]/g,"");
//必须保证第一个为数字而不是.
obj.value = obj.value.replace(/^\./g,"");
//保证只有出现一个.而没有多个.
obj.value = obj.value.replace(/\.{2,}/g,".");
//保证.只出现一次，而不能出现两次以上
obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
}
function checkNum(obj){
obj.value = obj.value.replace(/\D/g,'');	
}
function checkForm(){
	var team_all = $('#team_all').val(); 
	if(team_all>1 || team_all<0 || team_all == ""){
		alert("团队总奖励比例不得大于1或者少于0");		
		$('#team_all').val("");
		return false;
	}
	var shareholder_all=<?php echo $shareholder_all;?>;
	var all=parseFloat(shareholder_all)*100 + parseFloat(team_all)*100;
	if(all>100){
		alert("“区域团队总奖励” 与 “股东分红总奖励” 之和不得大于1");
		$(team_all).val("");
		return false;
	}	
	var consume_percent = $('#consume_percent').val(); 
	var p_percent = $('#p_percent').val(); 
	var c_percent = $('#c_percent').val(); 
	var a_percent = $('#a_percent').val();
	var all_percent = parseFloat(consume_percent)*100 + parseFloat(p_percent)*100 + parseFloat(c_percent)*100 + parseFloat(a_percent)*100;
	if(all_percent>100 || all_percent == ""){
		alert("总比例不得大于1或者少于0");		
		return false;
	}
}
</script>
</body>
</html>
