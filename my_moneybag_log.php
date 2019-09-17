<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('select_skin.php');
//头文件----start
require('../common/common_from.php');
//头文件----end


?>
<!DOCTYPE html>
<html>
<head>
    <title>零钱记录</title>
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
    <link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />    
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" /> 
    
    <link rel="stylesheet" id="wp-pagenavi-css" href="./css/list_css/pagenavi-css.css" type="text/css" media="all">
	<link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">
	
	<link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
    
<style>  
   .list {  	margin: 10px 5px 0 3px;	overflow: hidden;}
   .pinterest_title{ overflow: hidden;height: 36px;line-height: 19px;font-size:12px;color: #1c1f20;font-weight:bold;}
   .plus-tag-add{width:100%;min-width:320px;height:44px;line-height:44px;padding-left:10px;border-bottom:1px solid #dddddd;}
   .list{padding:3px;margin-top:10px;height:107px;background-color:white;}
   .submenu{width:33%;height:45px;line-height:45px;float:left;text-align:center;}
   .area-line{height:25px;width:1px;float:left;margin-top: 10px;padding-top: 20px;border-left:1px solid #cdcdcd;}
   .topDivSel{width:100%;height:45px;padding-top:0px;background-color:white;}
   .info_left{width:70%;float:left;}
   .info_left .up{width:100%;float:left;text-align:left;line-height: 40px;color:black;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
   .info_left .down{width:100%;float:left;text-align:left;line-height: 5px;color:#ddd;}
   .info_right{width:30%;float:right;color:black;text-align:right;padding-right:10px;}
   .my_info{width:100%;height:60px;line-height:60px;background-color:white;padding-left:10px;border-bottom:1px solid #ececec;}
   .red{color:red;}
   .tis{text-align: center;font-family: "微软雅黑";font-size: 18px;color:#ccc;margin-top: 10%;display: none}
   .loading{text-align: center;font-family: "微软雅黑";font-size: 18px;color:#ccc;margin-top: 20px;}
   .jiange{width:100%;content:" ";height: 10px;background-color:#f8f8f8;}
</style>
</style>


</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#fff;">
	<!-- <header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">零钱记录</h1>
	</header>
  <div class="topDiv"></div> --> <!-- 暂时屏蔽头部 -->
	    <div class="topDivSel">
		    <div class="plus-tag-add" style="color:rgb(174, 174, 174);padding-left:0px;">
				<div id="all" class="submenu selected" style="" onclick="viewRecord('moneybag_all');">全部</div>
				<div class="area-line" ></div>
				<div id="in" class="submenu"  onclick="viewRecord('moneybag_in');">收入</div>
				<div class="area-line" ></div>
				<div id="out" class="submenu"  onclick="viewRecord('moneybag_out');">支出</div>
			</div>
	    </div>
    <div style="height:45px"></div>  <!-- 占据选项框的高度 -->
    <div class="jiange"></div>
    <!-- 所有零钱记录 start -->
    <div id="allRecordDiv" style="width:100%;">
        <div class="recordDiv" id="recordContainer">
    		<!-- 记录列表 start -->
		    	<div class="entry-content">

				</div><!-- .entry-content -->
	    	</div>
	    	<!-- 记录列表 end -->
        <div class="tis" style="padding-bottom:10px;">---已无更多记录---</div>
        <div class="loading" style="padding-bottom:10px;">---正在加载中---</div>
    	</div>
    </div>
    <input type="hidden" id="IsEnd" value="0">
    <input type="hidden" id="Search" value="moneybag_all">


    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
    <script src="./js/r_global_brain.js" type="text/javascript"></script>
    <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
    </body>		

<script type="text/javascript">
var Search_type = $('#Search').val();
var winWidth    = $(window).width();
var winheight   = $(window).height();
var from        = 'moneybag';

var user_id     = "<?php echo $user_id;?>";
var customer_id = "<?php echo $customer_id_en;?>";

var i = 1;
$(window).scroll(function() {
    var pageH = $(document.body).height();
    var scrollT = $(window).scrollTop(); //滚动条top
    var aa = (pageH-winheight-scrollT)/winheight;
    if(aa<0.3){ 
	$(".loading").show();
        loadmore(); 	
    }
    
});
$(function(){
	$("#all").removeClass("selected");
    $("#in").removeClass("selected");
    $("#out").removeClass("selected");
	if(Search_type=='moneybag_all'){
		$("#all").addClass("selected");
	}else if(Search_type=='moneybag_in'){
		$("#in").addClass("selected");
	}else if(Search_type=='moneybag_out'){
		$("#out").addClass("selected");
	}
})

    function loadmore(){
        page=page+1;
        var viewType    = $("#Search").val();
        searchRecord(viewType); 
        //alert(page);  
    }
    var page = 0;
    function searchRecord(type){

        var search = $("#Search").val();
        var type   = type;
        
        // if(search!=type){ 
        //     $("#recordContainer").html('');
        //     page = 1;
        // }
        if($("#IsEnd").val() == 1){
			 $(".loading").hide();
            return false;
        }

        var type =  Search_type;
        console.log(type);

        $.ajax({
        url:'get_money_log.php',
        dataType: 'json',
        type: "post",
        data:{
          'from':from,
          'type':type,
          'user_id':user_id,
          'customer_id':customer_id,
          'page':page
        },
        success:function(data){
			var k = 0;
            $("#Search").val(type);
            var data = eval(data);
            var content='';
            //console.log(data);
            if(data==''){
                $("#IsEnd").val(1);
                $(".tis").show();
                $(".loading").hide();
            }else{
                for ( var i in data ) {
                    content += '<div class="my_info" onclick="gotoViewRerecordDetail('+data[i]["type"]+','+data[i]["id"]+');">';
                    content += '    <div class="info_left" >';
                    content += '    <div class="up" >'+data[i]["remark"]+'...</div>';
                    content += '    <div class="down" ><span>'+data[i]["createtime"]+'</span></div>';
                    content += '</div>';
                    if( data[i]["type"] == 2 || data[i]["type"] == 0 )
                      content += '<div class="info_right" style=""><span class="red">+'+data[i]["money"]+'</span></div>';
                    
                    else if( data[i]["type"] == 1)
                      content += '<div class="info_right" style=""><span class="black">-'+data[i]["money"]+'</span></div>';
                      content += '</div>';
                   k++; 
                }
				if(k<10){
					$(".loading").hide();  
					$(".tis").show();
				}else{
					 $(".tis").hide(); 
					 $(".loading").hide();
				}
                $("#recordContainer").append(content);
                   
            }
            
            
          }
      });

    }



</script>
<script type="text/javascript">
var customer_id_en = "<?php echo $customer_id_en;?>";
function viewRecord(type){
var cilck_type = type;
    $("#all").removeClass("selected");
    $("#in").removeClass("selected");
    $("#out").removeClass("selected");
    if(type=='moneybag_in'){
        $("#in").addClass("selected");
        type="moneybag_in";
        
        $("#IsEnd").val(0);
    }else if(type=='moneybag_out'){
        $("#out").addClass("selected");
        type="moneybag_out";
         
         $("#IsEnd").val(0);
    }else{
        $("#all").addClass("selected");
        type="moneybag_all";
        
        $("#IsEnd").val(0);
    }

    $("#recordContainer").html('');
    page = 1;
    $(".loading").show();
    Search_type = type;   //重置搜索类型
    searchRecord(type);
}

function gotoViewRerecordDetail(type,id){

if(type==1){
window.location.href='my_money_out.php?customer_id='+customer_id_en+'&from=moneybag&id='+id;
}else{
window.location.href='my_money_in.php?customer_id='+customer_id_en+'&from=moneybag&id='+id;
}

}
</script>



</body>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>