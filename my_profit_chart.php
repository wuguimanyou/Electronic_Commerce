<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../common/utility_fun.php');

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
    <title>累计收益</title>
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
    
    <link href="./css/goods/mobiscroll/bootstrap.min.css" rel="stylesheet" type="text/css"> 
    <!-- Mobiscroll JS and CSS Includes -->
    <link href="./css/goods/mobiscroll/mobiscroll.custom-2.17.1.min.css" rel="stylesheet" type="text/css">
    
    <link type="text/css" rel="stylesheet" href="./css/list_css/r_style.css" />
</head>
<style>
	#middle-tab .area-one {float:left;width:25%!important;padding-bottom: 0px !important; padding-top: 0px !important;border-bottom:none!important;}
	#middle-tab .area-one .item{ height:50px;line-height:50px;margin-top:0px !important;font-size:15px!important;margin-top: 0px!important;}
	#middle-tab .area-one div{margin-top: 0px!important;}
	.menu_selected{position:absolute;font-size:15px;top:47px;left:45%;}
	#rendez-vous .rendezvous-popup { position: fixed;left: 0; right: 0; top: 0; bottom: 0; background-color: rgba(30, 30, 30, 0.4);}
	#rendez-vous .rendezvous-datepicker {position: absolute;left: 50%;top: 50%; width: 18em; font-size: 1.3em; margin-left: -9em; margin-top: -8em;}
    .m_calendar{background: white;border: none;vertical-align: middle;width: 145px;}
	.left_content{width:70%;height:50px;float:left;padding: 12px 15px 15px;}    
    .right_content{width:30%;height:50px;float:right;padding:15px;text-align:right;}
    .period{vertical-align: middle;}
    .right_content img{margin-right:5px;margin-left:5px;height:14px;}
    .left_content img{width:20px;height:18px;vertical-align:middle;}
    .everyValue{font-size:30px;color:#3e7baf;}
    .everyText{font-size:16px;color:#999;margin-top: 5px;}
    #detail-count{height:114px;padding-top:15px;}
    .graph_desc{width:100%;height:50px;padding-left:10px;}
    #title_round{display: inline-block; width: 8px;height: 8px;border-radius: 50%;border:1px solid #3e7baf;}
    #chartTitle{color:#999;margin-left: 2px;}
    #container{width: 100%; height: 300px;margin-top:20px;}
    .seletor{width:100%;height:30px;line-height:23px;text-align: center;}
    #list_img{float:left;margin-left:15px;margin-top:10px;width:17px;height:15px;vertical-align:middle;}

    .content-base-size{float: left;width: 60px;height: 30px;line-height: 27px;margin-left: 3px;margin-top: 5px;border: 1px solid #c4c4c4;background-color: white;color: #707070;font-size: 12px;}
</style>

<link type="text/css" rel="stylesheet" href="./css/basic.css" />

<body id="mainBody" data-ctrl=true style="background:white;">
    <div id="mainDiv" style="width: 100%;height:100%;">
	    <!-- <header data-am-widget="header" class="am-header am-header-default">
		    <div class="am-header-left am-header-nav" onclick="goBack();">
			    <img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		    </div>
	        <h1 class="am-header-title" style="font-size:18px;line-height: 50px;">累计收益</h1>
	    </header>
        <div class="topDiv"></div> --><!-- 暂时隐藏头部导航栏 -->
        <div class="seletor" style="">
        	<div style="width:100%;float:left;" onclick="showSearch();">
        		<img src="./<?php echo $images_skin?>/goods_image/2016042901-orange.png" id="list_img" />
        		<span style="float:left;margin-left:5px;margin-top: 7px;">全部</span>
        	</div>
        </div>
        <div style="width:100%;height:50px;">
        	<div class="left_content m_calendar" style="" >
        		<img src="./images/info_image/calendar.png" style=""/>
        		<span>
                    <input type="text" value="2016-05-10" class=" m_calendar" id="startDate" style="background: white;border: none;vertical-align: middle;width: 83px;color:#a6a6a6;">
                </span>~
                <span>
                    <input type="text" value="2016-05-10" class=" m_calendar1" id="endDate" style="background: white;border: none;vertical-align: middle;width: 83px;color:#a6a6a6;">
                </span>
        	</div>
        	<div class="right_content" style="">
        		<span class="period">15天</span>
                <img src="./images/vic/right_arrow.png" style=""/>
        	</div>
        </div>
        <div id="detail-count" style="">
            <div class="area-one" style="width:100%;">
                <div class="everyValue"></div>
                <div class="everyText">累计返佣</div>
            </div>
        </div>
         <div class="graph_desc" style="">
            <span style="" id="title_round" ></span>
            <span id="chartTitle" style="">周期内返佣情况统计折线图（单位：元）</span>
        </div>
        <div id="container" style=""></div>

        <div id="leftmask" style="display:none;z-index:999;" data-role="none"></div>
        <div class="search_new" id="seardiv"  style="display:none;z-index:1000;" data-role="none"> 
            <!-- 价格区间 -->
            <ul  class="div_mo"  id="modiv" style="width:100%;">    
                <div id="buts" style="text-align:center;padding:12px;float:left;">
                    
                </div>
                <div style="padding:16px;margin-top:30px;width:100%;">
                    <button class="small-type-button6"  type="button" onclick="popClose();" style="width:100%;">确定</button>
                </div>
            </ul>
      
     </div>

         <!-- Marsk End-->
    <input type="hidden" id="IsEnd" value="0">
    <input type="hidden" class="Search" value="1">
    <input type="hidden" id="date" value="">
    <input type="hidden" id="m_calendar" value="">  <!--开始日期-->
    <input type="hidden" id="m_calendar1" value=""> <!--结束日期-->

    </div>

    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
    <script type="text/javascript" src="./js/global.js"></script>
    <script type="text/javascript" src="./js/loading.js"></script>
    <script language="javascript" type="text/javascript" src="./js/highchart/highchart.js"></script>
    <script language="javascript" type="text/javascript" src="./js/highchart/exporting.js"></script>
    <script src="./js/goods/mobiscroll/mobiscroll.custom-2.17.1.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="./js/r_jquery.mobile-1.2.0.min.js"></script>
    <script type="text/javascript" src="./js/my_profit_chart.js"></script>
</body>		

<script type="text/javascript">
/* format calendar*/    

    var mStartDate = new Date().DateAdd('d',-7).Format("yyyy-MM-dd");
    var mEndDate = new Date().Format("yyyy-MM-dd");
    
    var startDateString = new Date().DateAdd('d',-7).Format("MM月dd日");
    var endDateString = new Date().Format("MM月dd日");

	var between = daysBetween(mStartDate,mEndDate);
	var customer_id='<?php echo $customer_id_en;?>';
	var user_id='<?php echo $user_id;?>';

	$('.m_calendar').val(mStartDate);
	$('.m_calendar1').val(mEndDate);
	$(".period").html(between+"天");

    var winWidth = $(window).width();
    var winheight = $(window).height();
    var colors = new Array('#f3b86c','#f77070','#3eaf9a','#3e7baf');
	$(function() {
        $("#mainDiv").show();
        $("#cropDiv").hide();
        $(document.body).css("background:","#f8f8f8");
        $("#arrow_1").show();
        showGraph();
	});

///////////////////////////////////////////////////////////////////////manage graph//////////////////////////////////////////////////////////////

// function showCalendar(){
//     var user_id = "<?php echo $user_id;?>";
//     var customer_id = "<?php echo $customer_id_en;?>";
//     $.ajax({
//         type:"post",
//         url:"get_money_log.php",
//         dataType:"json",
//         data:{customer_id:customer_id,user_id:user_id,from:"profit_all"},
//         success:function(data){
//             if(data!==''){
//                 showGraph(data.first_time,data.now_time);
//             }
//         }
//     });
// }

/*
from=>开始日期,
to=>停止日期
*/
function showGraph(){   //报表

    	var url         = "";
    	var d1          = [];
        var d2          = [];
    	mStartDate      = $(".m_calendar").val();
    	mEndDate        = $(".m_calendar1").val();
        type            = $(".Search").val();
        var user_id     = "<?php echo $user_id;?>";
        var customer_id = "<?php echo $customer_id_en;?>";

        var total_money = 0;
        $(".everyValue").html(total_money);

            //通过异步加载传值的数据
            var min = Date.UTC(mStartDate.substring(0,4),parseInt(mStartDate.substring(5,7))-1,mStartDate.substring(8,10));    
            var max = Date.UTC(mEndDate.substring(0,4),parseInt(mEndDate.substring(5,7))-1,mEndDate.substring(8,10));
			$.ajax({
				type:"post",
				url:"get_money_log.php",
				dataType:"json",
				data:{startTime:mStartDate,endTime:mEndDate,user_id:user_id,customer_id:customer_id,from:"profit_chart",type:type},
				success:function(data){
				
					for(var i=0;i<data.length;i++){
						var item = data[i].createtime;
						var total = parseInt(data[i].total_money);
						var gd_value = Date.UTC(item.substring(0,4),parseInt(item.substring(5,7))-1,item.substring(8,10));
						d1.push([gd_value,total]);
                        total_money += data[i].total_money;
					}
                    //alert(total_money);
                    var total = total_money.toFixed(2)+"<span style='color:#999;font-size:14px;margin-left:5px;'>元</span>";

                    $(".everyValue").html(total);
					
					$('#container').highcharts({
						chart: { type: 'area', spacingBottom: 30},
						title: {text: null},
						legend: {enabled: false},
						xAxis: { type: 'datetime',gridLineWidth:1,min:min,max:max, labels: {format: '{value:%d(%m月)}', align: 'left' } },
						yAxis: { title: {//纵轴标题  
								text: null  
							},gridLineWidth:0,  
							labels: {
								formatter: function () {
									return this.value;
								}
							}
						},
						tooltip: {
							backgroundColor: '#3e7baf', 
							borderColor: '#3e7baf', 
							formatter: function () {
								return '<b style="width:20px;">' + this.y + '</b>'
									
							}
						}, credits: { enabled: false },exporting: {enabled:false},
						plotOptions: {
							area: {
								fillOpacity: 0.5,
								lineColor: '#3e7baf',
								lineWidth: 1,
								marker: {
									lineWidth: 2,
									lineColor: '#3e7baf',
									fillColor: 'white',
								}
							}
						},
						series: [{
							data:d1,
							color: '#3e7baf',
							fillOpacity: 0.3
							
						}]
					});
						
					}
					
				});		
	    	
	    }

	///////////////////////////////////////////////////////////////////////////////manage calendar//////////////////////////////////////////////////////////
 $(function () {
	    function fillDateBox(box, date) {
	        var d = date.getDate();
	        $('.range-day', box).html((d < 10 ? '0' : '') + d);
	    }
    	
        startDate = new Date(), // Initial start date
        // Initial end date
        endDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 6),
        $startBox = $('#startDate'),
        $endBox = $('#endDate'),
        // Init start calendar
        $startCal = $('#startDate').mobiscroll().calendar({
            defaultValue: startDate,
            theme: 'mobiscroll',      
            lang: 'zh',    
            display: 'modal',  
            mode: 'clickpick',
            dateFormat: 'mm月dd日',
            onSelect: function (dateText, inst) {
                var newMinDate, newMaxDate;
 
                // Update start date and start label
                startDate = inst.getDate();
                fillDateBox($startBox, startDate);
 
                // Validate selection
                if (startDate > endDate) {
                    // If start is after end, modify end date
                    endDate = new Date(startDate);
                    // Set the new end date to the calendar, also update the label
                    $endCal.mobiscroll('setDate', endDate);
                    fillDateBox($endBox, endDate);
                }
 
                // Update minDate and maxDate for end date
                newMinDate = startDate;
                newMaxDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate() + 100);
                $endCal.mobiscroll('option', { minDate: newMinDate, maxDate: newMaxDate });
                between = daysBetween(startDate.toISOString().substring(0,10),endDate.toISOString().substring(0,10));
                $(".period").html((between)+"天");
				//showGraph(startDate.toISOString().substring(0,10),endDate.toISOString().substring(0,10));   //显示graph
                $(".m_calendar").val(startDate.toLocaleDateString().substring(0,10));
                $(".m_calendar1").val(endDate.toLocaleDateString().substring(0,10));
                showGraph();
            }
        }),
        // Init end calendar
        $endCal = $('#endDate').mobiscroll().calendar({
            minDate: startDate,
            defaultValue: endDate,
            theme: 'mobiscroll',      
            lang: 'zh',    
            display: 'modal',  
            mode: 'clickpick',
            dateFormat: 'mm月dd日',
            onSelect: function (dateText, inst) {
                // Update end date and end label
                endDate = inst.getDate();
                fillDateBox($endBox, endDate);
                between = daysBetween(startDate.toISOString().substring(0,10),endDate.toISOString().substring(0,10));
                $(".period").html((between)+"天");
                //showGraph(startDate.toISOString().substring(0,10),endDate.toISOString().substring(0,10));   //显示graph
                $(".m_calendar").val(startDate.toLocaleDateString().substring(0,10));
                $(".m_calendar1").val(endDate.toLocaleDateString().substring(0,10));
                showGraph();
            }
        });
    // Fill initial values
    fillDateBox($startBox, startDate);
    fillDateBox($endBox, endDate);
    mEndDate = endDate.toISOString().substring(0,10);
    mStartDate = startDate.toISOString().substring(0,10);
});

// 日期点击事件
$(".m_calendar").click(function(){
     $('.m_calendar').mobiscroll('show'); 
     return false;
});
$(".m_calendar1").click(function(){
     $('.m_calendar1').mobiscroll('show'); 
     return false;
});
</script> 
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>

