<?php
  header("Content-type: text/html; charset=utf-8"); 
  require('../config.php');
  $customer_id = passport_decrypt($customer_id);
  require('../back_init.php');  
  $keyid = 0;
  $parent_id = -1;
  $del = "";
   
 if(!empty($_GET["keyid"])){
   $keyid = $configutil->splash_new($_GET["keyid"]);
 }
 

   if(!empty($_GET["del"])){
	  $del = $configutil->splash_new($_GET["del"]);
   }

  if(!empty($_GET["parent_id"])){
      $parent_id = $configutil->splash_new($_GET["parent_id"]); 
  }
   $link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
     mysql_select_db(DB_NAME) or die('Could not select database');
	 mysql_query("SET NAMES UTF8");
     if($del=="isok"){
    
     
     $query = 'update weixin_menu_trues set isvalid=false where id='.$keyid;

	 mysql_query($query);
	 mysql_close($link);
	 
	 echo "<script>location.href='menu.php?customer_id=".passport_encrypt((string)$customer_id)."';</script>";
	 
	 return;
  }

  
  
  $area_leave= 2;
  
  
  $name= "";
  $content = "";
  $type =1;
  $hassubmenu = 0;
  $subscribe_id = -1;
  $single_id  =-1;
  $music_id =-1;
  $media_id="";
  $e_link="";
  $function_id=-1;
  $hassubcheck=0;
  $id_sub = "";
  $scan_key="";
  $pic_key="";
  $location_key="";
  $shop_link=-1;
  $pid=-1;
  $card_id=-1;
  $card_type =-1;
  if($keyid>0){
     //取一级菜单
    
    $query = 'SELECT id,name,pic_key,location_key,scan_key,content,type,shop_link,subscribe_id ,hassubmenu,single_id,e_link,music_id,media_id,function_id,pid,card_id,card_type FROM weixin_menu_trues where isvalid=true and  parent_id='.$parent_id.' and  id='.$keyid;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
	while ($row = mysql_fetch_object($result)) {
		$id = $row->id ;
		$name =  $row->name ;
		$content =  $row->content ;
		$type = $row->type;
		$subscribe_id = $row->subscribe_id;
		$hassubmenu = $row->hassubmenu;
		$single_id = $row->single_id;
		$e_link = $row->e_link;
		$music_id = $row->music_id;
		$media_id = $row->media_id;
		$function_id = $row->function_id;
		$hassubcheck = $row->hassubmenu;
		$scan_key = $row->scan_key;
		$pic_key = $row->pic_key;
		$location_key=$row->location_key;
		$shop_link = $row->shop_link;
		$pid = $row->pid;
		$card_id = $row->card_id;
		$card_type = $row->card_type;
	}
	
	$query2 = 'SELECT id FROM weixin_menu_trues where isvalid=true and parent_id='.$id;
	//echo $query2;
        $result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());  
		$rcount = mysql_num_rows($result2);			
	while ($row2 = mysql_fetch_object($result2)) {			
		$id_sub =  $row2->id ;
	}
  }
  //获取主题颜色
	$query = 'SELECT theme FROM customers where isvalid=true and id='.$customer_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$theme="blue";
	while ($row = mysql_fetch_object($result)) {
		$theme = $row->theme;
		break;
	}
	
  $hasmobile = false ;
  $query = "select count(id) rowcount from weixin_mobiletraffic_store where isvalid = true and sellstore > 0 and customer_id = ".$customer_id;
  $result = mysql_query($query) or die('L92 Query failed: ' . mysql_error());  
  if ($row = mysql_fetch_object($result)) {
	  if($row->rowcount > 0){
		 $hasmobile = true; 
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
.hidden{
	display:none;
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


                    <div id="div_menucontent">

                          <div class="WSY_nonebox">   
                              <dl class="WSY_bulk WSY_bulknone WSY_bulk" style="height:auto;">
                                  <dt>区域：</dt>
                  
                                     <div id="div_province" style="display:none" >
                                          <dd>
													<select name="location_p" id="location_p" >
													</select>													
													<select name="location_c" id="location_c" class="hidden">
													</select>				
													<select name="location_a" id="location_a" class="hidden">
													</select>																											
                                          </dd>
                                     </div>	
                                     <div id="div_city" style="display:none" >
                                          <dd>
													<select name="location_p2" id="location_p2" >
													</select>													
													<select name="location_c2" id="location_c2" >
													</select>				
													<select name="location_a2" id="location_a2" class="hidden">
													</select>																											
                                          </dd>
                                     </div>	
                                     <div id="div_area" style="display:none" >
                                          <dd>
													<select name="location_p3" id="location_p3" >
													</select>													
													<select name="location_c3" id="location_c3" >
													</select>				
													<select name="location_a3" id="location_a3" >
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
                <input type=hidden name="keyid" value="<?php echo $keyid ?>" />
                <input type=hidden name="parent_id" value="<?php echo $parent_id ?>" />
                <input type=hidden name="pid" id="pid" value="<?php echo $pid ?>" />  
              </form>
              <!--关注用户结束-->
		
        
        </div>
        <!--列表内容大框结束-->
	</div>
	<!--内容框架结束-->
	
<?php mysql_close($link);?>

<script charset="utf-8" src="../common_shop/jiushop/js/region_select_name.js"></script>
<script type="text/javascript">
	new PCAS('location_p','location_c','location_a' ,'北京市','',''); 
	var p2 = new PCAS('location_p2','location_c2','location_a2' ,'广东省','',''); 
	p2.SelP.innerHTML = "";
	p2.SelP.options.add(new Option("广东省","广东省")); 
	p2.SelP.options.add(new Option("浙江省","浙江省")); 
	p2.SelP.change();
</script>
<script type="text/javascript">
var level=2;
$(function(){
	switch(level){
		case 2:
		$("#div_province").show();
		break;		
		case 1:
		$("#div_city").show();
		break;		
		case 0: 
		$("#div_area").show();
		break;
	}	
});
function chksubmenu(type){
	level = type;
	switch(type){
		case 2:		
		$("dl div").hide();
		$("#div_province").show();
		break;		
		case 1:
		$("dl div").hide();
		$("#div_city").show();
		break;		
		case 0: 
		$("dl div").hide();
		$("#div_area").show();		 
		break;
	}		
}

function submitV(){
	switch(level){
		case 2:
		var location_p = $("#location_p").val();
		if(location_p == ""){
			alert("请选择正确的区域");
			return;
		}
		break;		
		case 1:
		
		break;		
		case 0:
		
		break;
	}
}
</script>

</body>
</html>