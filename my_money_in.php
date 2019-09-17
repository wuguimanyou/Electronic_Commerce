<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');

//头文件----start
require('../common/common_from.php');
//头文件----end

$from = $_GET['from'];

if($from == 'moneybag'){
	$title = "零钱明细";
}elseif($from == 'score'){
	$title = "积分明细";
}
//$from = 'moneybag';
$id   = $_GET['id'];

$query = "SELECT custom FROM weixin_commonshop_currency WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$result= mysql_query($query);
while( $row = mysql_fetch_object( $result ) ){
    $custom = $row->custom;
}




?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title;?></title>
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
    <link rel="stylesheet" id="twentytwelve-style-css" href="./css/list_css/style.css" type="text/css" media="all">
	
    
<style>
	.my_info{width:100%;height:60px;line-height:60px;background-color:white;padding-left:10px;border-bottom:1px solid #eee;}
	.detail{width:90%;line-height:50px;float:left;margin-left:5%;border-bottom:1px solid #ececec;}
	.detail_left{width:35%;float:left;color:#707070;}
	.detail_left span{letter-spacing: 25px;}
	.detail_right{width:65%;float:right;color:black;text-align:right;}
	.detail_right span{color:black;font-weight:200;}
	.container{width:100%;margin-top:10px;border-top:1px solid #eee;border-bottom:1px solid #eee;float:left;background-color:white;}
	.left{width:22%;float:left;color:black;}
    .left span{color: #707070;}
    .danshel{width: 45.3%;overflow: hidden;text-align: left;white-space: nowrap;margin-left: 3%;}
	.right{width:60%;float:right;color:black;text-align:right;padding-right:30px;}
	.red{color:#e92527;font-size:28px;}
	.black{color:red;font-size:28px;}
</style>

</head>
<!-- Loading Screen -->
<div id='loading' class='loadingPop'style="display: none;"><img src='./images/loading.gif' style="width:40px;"/><p class=""></p></div>

<body data-ctrl=true style="background:#f8f8f8;">
	<!-- <header data-am-widget="header" class="am-header am-header-default">
		<div class="am-header-left am-header-nav" onclick="goBack();">
			<img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		</div>
	    <h1 class="am-header-title" style="font-size:18px;">入账明细</h1>
	</header>
	<div class="topDiv"></div> -->  <!-- 暂时屏蔽头部 -->
    <div class="content" id="content">
        
    </div>

    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script src="./js/jquery.ellipsis.js"></script>
    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>
</body>		

<script type="text/javascript">
get_money_in();

function get_money_in(){
var from = '<?php echo $from;?>';
    var id   = <?php echo $id;?>;
    var type = 'out_in';
     if(from == 'score'){
        var money_name = '积分';
    }else if(from == 'currency'){
        var money_name = "<?php echo $custom;?>";
    }else if(from == 'moneybag'){
        var money_name = '金额';
    }
    $.ajax({
        url:'get_money_log.php',
        dataType: 'json',
        type: "post",
        data:{'from':type,'where':from,'id':id},
        success:function(data){
            var data = eval(data);
            var content='';
                content += '<div class="my_info">';
                content += '    <div class="left danshel"><span>入账'+money_name+'</span></div>';
               if(from=='score'||from=='currency'){
                    content += '    <div class="right" style="width:50%;"><span class="black">'+data["money"]+'</span></div>';
                }else{
                    content += '    <div class="right" style="width:50%;"><span class="black">￥'+data["money"]+'</span></div>';
                }
                content += '</div>';
                content += '<div class="container">';
                content += '    <div class="detail">';
                content += '        <div class="detail_left"><span>类型</span></div>';
                content += '        <div class="detail_right"><span>'+data["consume_type"]+'</span></div>';
                content += '    </div>';
                content += '    <div class="detail">';
                content += '        <div class="detail_left"><span>时间</span></div>';
                content += '        <div class="detail_right"><span>'+data["createtime"]+'</span></div>';
                content += '    </div>'; 
                if(from!=='score'){                
                content += '    <div class="detail">';
                    content += '        <div class="detail_left"><span style="letter-spacing: 6px;">订单号</span></div>';
                    content += '        <div class="detail_right"><span>'+data["batchcode"]+'</span></div>';
                    content += '    </div>';
                }
                content += '    <div class="detail">';
                content += '    <div class="detail beizhu" style="margin:0;width:100%;">';
                content += '        <div class="detail_left"><span>备注</span></div>';
                content += '        <div class="detail_right"><span style="display:inline-block;line-height:25px;padding-top:10px;">'+data["remark"]+'</span></div>';
                content += '    </div>';
                content += '</div>';
                content += '    </div>';
                $("#content").html(content);
        }
    });

}
</script>




</body>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
</html>