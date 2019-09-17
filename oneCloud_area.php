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
	$start = ($pagenum-1) * 10;
	$end = 10;
	$query = 'SELECT count(1) as wcount FROM weixin_commonshop_team_area where isvalid=true and parent_id=-1 and customer_id='.$customer_id;
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
<script type="text/javascript" src="../common/js/jquery.js"></script>
<script type="text/javascript" src="../common/js/inside.js"></script>
<script type="text/javascript" src="../common/js_V6.0/content.js"></script>
<meta http-equiv="content-type" content="text/html;charset=UTF-8">
<title>区域管理列表</title>
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
.WSY_text_mid{
text-align:center;
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
					<a class="white1" href="oneCloud_area.php?customer_id=<?php echo passport_encrypt($customer_id);?>">区域设置</a>
					<a href="oneCloud_UserList.php?customer_id=<?php echo passport_encrypt($customer_id);?>">人员管理</a>
                </div>
            </div>
            <!--列表头部切换结束-->
  			<!--自定义代码开始-->
            <div class="WSY_data" id="div_menucon">
                <!--列表按钮开始-->
                <div class="WSY_list">
                    <li class="WSY_left"><a>区域管理列表<span>(区域唯一，不可重复)</span></a></li>
                    <ul class="WSY_righticon">
                        <li><a href="oneCloud_area_edit.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>">添加区域</a></li>
                        <!--<li class="WSY_inputicon"><input type="button" value="批量删除"></li>-->
                    </ul>
                </div>
                <!--列表按钮开始-->
                <table width="97%" class="WSY_table" id="custom">
                    <thead class="WSY_table_header">
                        <th width="4%"><input id="s" onclick="$(this).attr('checked')?checkAll():uncheckAll()" type="checkbox" name="sex"></th>
                        <th width="10%">ID</th>
                        <th width="27%">区域名</th>
                        <th width="12%">区域等级 </th>
                        <th width="10%">区域负责人</th> 
                        <th width="15%">操作</th>
                    </thead>
				</table>

                <table width="97%" class="WSY_table" cellspacing="1" cellpadding="1" border="0"  class="tb_class table table-bordered"  style="margin-bottom:5px;margin-top:0;" bgcolor="#fff">
                    <?php
                
                   $query = 'SELECT id,areaname,area_user FROM weixin_commonshop_team_area where isvalid=true and parent_id=-1 and grade = 2 and customer_id='.$customer_id." limit ".$start.",".$end;
                   $result = mysql_query($query) or die('L89 Query failed: ' . mysql_error());  
         
                  while ($row = mysql_fetch_object($result)) {
                       $keyid =  $row->id ;
                       $areaname = $row->areaname;
                       $grade = "省级";  
                       $area_user = $row->area_user;		

						if($area_user>0){
							$name =  "";
							$weixin_name =  "";								
							$query_name = 'SELECT name,weixin_name  FROM weixin_users where isvalid=true and id='.$area_user." and customer_id=".$customer_id." limit 0,1";
							$result_name = mysql_query($query_name) or die('L156 Query failed: ' . mysql_error());  
							while ($row_name = mysql_fetch_object($result_name)) {						  
								$name =  $row_name->name;
								$weixin_name =  $row_name->weixin_name;
							}
							if(!empty($weixin_name)){
								$area_user_name = $name."(".$weixin_name.")";
							}else{
								$area_user_name = $name ;
							}
					  
							$area_user_name = $area_user.":".$area_user_name;
					  
						 }else{
							$area_user_name = "暂无";
						 } 					   
                	?>  
					<tr><td>
					<table width="97%" class="WSY_table WSY_table_add" id="custom">
                    <tr>
							<td width="3%"><input type="checkbox" name="code_Value" value="1"></td>
							<td width="10%" class="xuhao"><?php echo $keyid ?></td>
							<td width="27%"><?php echo $areaname; ?></td>
							<td width="12%" class="WSY_text_mid"><?php echo $grade; ?></td>
							<td width="10%" class="WSY_text_mid"><?php echo $area_user_name; ?></td>
							<td class="WSY_t4" width="15%">   
									<a href="oneCloud_user_edit.php?keyid=<?php echo $keyid ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&pagenum=<?php echo $pagenum ?>" title="编辑区域负责人"><img src="../common/images_V6.0/operating_icon/icon05.png"></a>
									<?php if($area_user!=-1){ ?>
									<a href="javascript: G.ui.tips.confirm('您确定撤销本区域负责人吗？','oneCloud_area.class.php?keyid=<?php echo $keyid ?>&op=cancle&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum=<?php echo $pagenum ?>');" title="撤销负责人"><img src="../common/images_V6.0/operating_icon/icon26.png"></a>
									<?php } ?>
									<a href="javascript: G.ui.tips.confirm('您确定删除吗？','oneCloud_area.class.php?keyid=<?php echo $keyid ?>&op=del&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&pagenum=<?php echo $pagenum ?>');" title="删除"><img src="../common/images_V6.0/operating_icon/icon04.png"></a>
																							  
							</td>
                    </tr>
                   
                  <!-- 二级菜单开始 -->					
					  <?php   
					  $query2 = 'SELECT id,areaname,area_user  FROM weixin_commonshop_team_area where isvalid=true and parent_id='.$keyid;
                      //echo $query2;
                      $result2 = mysql_query($query2) or die('L124 Query failed: ' . mysql_error());  
                      $rcount = mysql_num_rows($result2);
                      
                      while ($row2 = mysql_fetch_object($result2)) {
                      
						$keyid_sub =  $row2->id ;
						$areaname_sub = $row2->areaname;
						$grade_sub = "市级";  
						$area_user_sub = $row2->area_user;					
						if($area_user_sub>0){
							$name =  "";
							$weixin_name =  "";								
							$query_name = 'SELECT name,weixin_name  FROM weixin_users where isvalid=true and id='.$area_user_sub." and customer_id=".$customer_id." limit 0,1";	
							$result_name = mysql_query($query_name) or die('L156 Query failed: ' . mysql_error());  
							while ($row_name = mysql_fetch_object($result_name)) {						  
								$name =  $row_name->name;
								$weixin_name =  $row_name->weixin_name;
							}
							if(!empty($weixin_name)){
								$area_user_sub_name = $name."(".$weixin_name.")";
							}else{
								$area_user_sub_name = $name ;
							}
					  
							$area_user_sub_name = $area_user_sub.":".$area_user_sub_name;
					  
						 }else{
							$area_user_sub_name = "暂无";
						 } 
                      ?>
                        <tr>
                            <td><input type="checkbox" name="code_Value" value="1"></td>
                            <td class="xuhao"><?php echo $keyid_sub ?></td>
                            <td><li class="WSY_lefticon1"><?php echo $areaname."->".$areaname_sub; ?></li></td>
                            <td class="WSY_text_mid"><?php echo $grade_sub; ?></td>
                            <td class="WSY_text_mid"><?php echo $area_user_sub_name; ?></td>
                            <td class="WSY_t4">
									<a href="oneCloud_user_edit.php?keyid=<?php echo $keyid_sub ?>&parent_id=<?php echo $id ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&pagenum=<?php echo $pagenum ?>" title="编辑区域负责人"><img src="../common/images_V6.0/operating_icon/icon05.png"></a>
									<?php if($area_user_sub!=-1){ ?>
									<a href="javascript: G.ui.tips.confirm('您确定撤销本区域负责人吗？','oneCloud_area.class.php?keyid=<?php echo $keyid_sub ?>									
									&op=cancle&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum=<?php echo $pagenum ?>');" title="撤销负责人"><img src="../common/images_V6.0/operating_icon/icon26.png"></a>
									<?php } ?>
									<a href="javascript: G.ui.tips.confirm('您确定删除吗？','oneCloud_area.class.php?keyid=<?php echo $keyid_sub ?>&op=del&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum=<?php echo $pagenum ?>');" title="删除"><img src="../common/images_V6.0/operating_icon/icon04.png"></a>
                             
                            </td>
                        </tr>
						
							<!-- 三级菜单开始 -->
						  <?php   
						  $query3 = 'SELECT id,areaname,area_user  FROM weixin_commonshop_team_area where isvalid=true and parent_id='.$keyid_sub;
						  //echo $query2;
						  $result3 = mysql_query($query3) or die('L184 Query failed: ' . mysql_error());  
						  
						  while ($row3 = mysql_fetch_object($result3)) {
						  
							$keyid_sub2 =  $row3->id ;
							$areaname_sub2 = $row3->areaname;
							$grade_sub2 = "区级";  
							$area_user_sub2 = $row3->area_user;		
							 
							if($area_user_sub2>0){
								$name =  "";
								$weixin_name =  "";								
								$query_name = 'SELECT name,weixin_name  FROM weixin_users where isvalid=true and id='.$area_user_sub2." and customer_id=".$customer_id." limit 0,1";
								$result_name = mysql_query($query_name) or die('L156 Query failed: ' . mysql_error());  
								while ($row_name = mysql_fetch_object($result_name)) {						  
									$name =  $row_name->name;
									$weixin_name =  $row_name->weixin_name;
								}
								if(!empty($weixin_name)){
									$area_user_sub2_name = $name."(".$weixin_name.")";
								}else{
									$area_user_sub2_name = $name ;
								}
						  
								$area_user_sub2_name = $area_user_sub2.":".$area_user_sub2_name;
						  
							 }else{
								$area_user_sub2_name = "暂无";
							 }
							 
						  ?>							
							<tr class="WSY_three">
								<td><input type="checkbox" name="code_Value" value="1"></td>
								<td><?php echo $keyid_sub2; ?></td>
								<td>
									<li class="WSY_lefticon" style="width:150px;padding-left: 25px;"><?php echo $areaname_sub."->".$areaname_sub2; ?></li >
								</td>

                            <td class="WSY_text_mid"><?php echo $grade_sub2; ?></td>
                            <td class="WSY_text_mid"><?php echo $area_user_sub2_name; ?></td>
                            <td class="WSY_t4">
									<a href="oneCloud_user_edit.php?keyid=<?php echo $keyid_sub2 ?>&parent_id=<?php echo $id ?>&customer_id=<?php echo passport_encrypt((string)$customer_id); ?>&pagenum=<?php echo $pagenum ?>" title="编辑区域负责人"><img src="../common/images_V6.0/operating_icon/icon05.png"></a>
									<?php if($area_user_sub2!=-1){ ?>		
									<a href="javascript: G.ui.tips.confirm('您确定撤销本区域负责人吗？','oneCloud_area.class.php?keyid=<?php echo $keyid_sub2 ?>&op=cancle&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum=<?php echo $pagenum ?>');" title="撤销负责人"><img src="../common/images_V6.0/operating_icon/icon26.png"></a>
									<?php } ?>
									<a href="javascript: G.ui.tips.confirm('您确定删除吗？','oneCloud_area.class.php?keyid=<?php echo $keyid_sub2 ?>&op=del&customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum=<?php echo $pagenum ?>');" title="删除"><img src="../common/images_V6.0/operating_icon/icon04.png"></a>                              
                            </td>
							</tr>
						<?php } 
						} ?>	
					
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
<script src="../js/fenye/jquery.page1.js"></script>


<script type="text/javascript" > 
<!-- 分页 --start-->
	var customer_id = "<?php echo passport_encrypt((string)$customer_id);?>";
	var pagenum = <?php echo $pagenum ?>;
	var count =<?php echo $page ?>;//总页数
	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
		pageCount:count,
		current:pagenum,
		backFn:function(p){
		document.location= "oneCloud_area.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+p;
	   }
	});

  var page = <?php echo $page ?>;
  
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	document.location= "oneCloud_area.php?customer_id=<?php echo passport_encrypt((string)$customer_id) ?>&pagenum="+a;
	}
  }
<!-- 分页 --end-->
</script> 

</body>
</html>