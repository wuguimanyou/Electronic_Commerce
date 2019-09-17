<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php'); //配置
if(!empty($_POST['customer_id'])){
	$customer_id = $configutil->splash_new($_POST['customer_id']);
}
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$user_id        = -1;//用户ID
$statu          = "";//动作
$choose_val     = 0;//身份搜索ID
$commisions_val = 0;//等级搜索ID
$search_text    = "";//关键字搜索
$end            = 10;
if(!empty($_POST["user_id"])){
   $user_id = $configutil->splash_new($_POST["user_id"]);
}
if(!empty($_GET["statu"])){//操作动作
   $statu = $configutil->splash_new($_GET["statu"]);
}
if(!empty($_GET["choose_val"])){//身份搜索
   $choose_val = $configutil->splash_new($_GET["choose_val"]);
}
if(!empty($_GET["commisions_val"])){//下级等级搜索
   $commisions_val = $configutil->splash_new($_GET["commisions_val"]);
}
switch ($statu){
	
case 'member'://查询团队成员信息

	$page  = -1;	
	$start = -1;
	
	$page = $configutil->splash_new($_POST["page"]);
	$start = ($page - 1) * 10;
	
	/*查询本人代数 start*/
	$generation = 0;//代数
	$query = "select generation from weixin_users where id=".$user_id;
	$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
		$generation = $row->generation;
		}
		/*查询本人代数 end*/
	
	$query = "";//查询语句
	$query_base = "SELECT 
				u.id,
				u.fromw,
				u.weixin_headimgurl,
				u.weixin_name,
				u.parent_id,
				u.createtime 
				FROM weixin_users u 
				";//weixin_users表数据
	$query_all = " where u.isvalid=true and match(u.gflag) against (',".$user_id.",') and u.customer_id=".$customer_id;//weixin_users表条件
	
	$query_p = "inner join promoters p 
				on u.id=p.user_id";//promoters表条件
	
		if(!empty($_GET["commisions_val"])){//下级等级
			$query_all .= " and u.generation=".($generation+$commisions_val);
		}
	
		if(!empty($_GET["search_text"])){//搜索关键词
			$search_text = $_GET["search_text"];
			$query_all .= " and u.weixin_name like '%".$search_text."%'";
		}
		
		$query = $query_base.$query_all;//若无推广员条件则为总查询语句
		
		if(!empty($_GET["choose_val"])){
			$query = $query_base.$query_p.$query_all." and p.isvalid=true and p.status=1 ";//推广员通用sql
			switch($choose_val){
				case '1':case '10'://所有推广员
					
				break;
				case '2':case '20'://所有区域代理
					$query .= " and p.isAgent in(5,6,7,8)";
				break;
				case '3':case '30'://所有股东
					 $query .= " and p.is_consume>0";
				break;
				case '4'://代理商
					 $query .= " and p.isAgent=1";
				break;
				case '5'://供应商
					$query .= " and p.isAgent=3";
				break;
				// case '6'://技师
					// $query .= " and p.isAgent=4";
				break;
				
				//推广员分佣级别
				case '11':case '12':case '13':case '14':case '15':case '16':case '17':case '18':
					$commision_level = (int)substr($choose_val,1);
					$query .= " and p.commision_level=".$commision_level;
				break;
				
				//区域代理
				case '21'://区代
					$query .= " and p.isAgent=5";
				break;
				case '22'://市代
					$query .= " and p.isAgent=6";
				break;
				case '23'://省代
					$query .= " and p.isAgent=7";
				break;
				case '24'://自定义区域
					$query .= " and p.isAgent=8";
				break;
				
				//股东
				case '31'://股东
					$query .= " and p.is_consume=4";
				break;
				case '32'://总代理
					$query .= " and p.is_consume=3";
				break;
				case '33'://渠道
					$query .= " and p.is_consume=2";
				break;
				case '34'://代理
					$query .= " and p.is_consume=1";
				break;
			}
		}
	
	/*** 统计人数 start ***/
	$rcount = 0;	//总人数
	$query_count = "select count(a.id) as rcount from(".$query." group by u.id)a";	//统计查询后的人数
	$result_count = mysql_query($query_count) or die('Query_count failed:'.mysql_error());
	while($row_count = mysql_fetch_object($result_count)){
		$rcount = $row_count->rcount;
	}
	/*** 统计人数 end ***/
	
	$query .= " group by u.id order by u.createtime desc limit ".$start.",".$end;
	//echo $query;

	$result = mysql_query($query) or die('Query failed1: ' . mysql_error());

	$p_id              = 0;//成员ID
	$fromw             = 0;//来源
	$weixin_headimgurl = "";//头像
	$user_name         = "匿名";//名字
	$parent_id         = 0;//上级ID
	$sq_time           = "";//申请时间
	$parent_name       = "";//上级名字
	$pro_id        	   = 0;	//推广员id
	$array = array();
	while ($row = mysql_fetch_object($result)) {
		$p_id              = $row->id;
		$fromw             = $row->fromw;
		$weixin_headimgurl = $row->weixin_headimgurl;
		$user_name         = $row->weixin_name;
		$parent_id         = $row->parent_id;
		$sq_time           = $row->createtime;
		$parent_name       = "";	
		$sql1 = "SELECT weixin_name from weixin_users where isvalid=true and id=".$parent_id;
		$result1 = mysql_query($sql1) or die('Query failed2: ' . mysql_error());
		while ($row1 = mysql_fetch_object($result1)) {
			$parent_name = $row1->weixin_name;
		}				
		$pro_id        	   = 0;	//推广员id
		$isAgent           = 0;//推广员登记
		$is_consume        = 0;	//是否为股东
		$sql2 = "SELECT id,isAgent,is_consume from promoters where isvalid=true and status=1 and user_id=".$p_id." and customer_id=".$customer_id;
		$result2 = mysql_query($sql2) or die('Query failed2: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
			$pro_id     = $row2->id;
			$isAgent    = $row2->isAgent;
			$is_consume = $row2->is_consume;
		}

		if($p_id != 0){	
			$tmp=array(
					"id"=>$p_id,
					"p_id"=>$p_id,
					"fromw"=>$fromw,
					"weixin_headimgurl"=>$weixin_headimgurl,
					"user_name"=>$user_name,
					"sq_time"=>$sq_time,
					"isAgent"=>$isAgent,
					"is_consume"=>$is_consume,
					"parent_name"=>$parent_name,
					"rcount"=>$rcount,
					"pro_id"=>$pro_id
				);
		}
			array_push($array ,$tmp);		
	}
	$out = json_encode($array);	
	echo $out;
	break;
	
	case 'commisions'://查询推广员等级
		
		//推广员分佣级别和推广员自定义名称-----start
		$query = "select exp_mem_name,reward_level from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$exp_mem_name = "会员";//推广员名称
		$reward_level = 3;//分佣级别
		while ($row = mysql_fetch_object($result)) {
			$exp_mem_name = $row->exp_mem_name;
			$reward_level = $row->reward_level;
		}
		$exp_mem_name = explode("_", $exp_mem_name);
		for($i=1;$i<=$reward_level;$i++){
			if(empty($exp_mem_name[$i])){ $exp_mem_name[$i] = "".$i."级会员";};
			$arr[$i] = $exp_mem_name[$i];
		}
		$out = json_encode($arr);
		echo $out;
	break;
	
	case 'shareholder'://查询股东身份
		
		//股东身份自定义名称-----start
		$query = "select a_name,b_name,c_name,d_name from weixin_commonshop_shareholder where isvalid=true and customer_id=".$customer_id;
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$a_name = "股东";//一级自定义名称
		$b_name = "总代理";//二级自定义名称
		$c_name = "渠道";//三级自定义名称
		$d_name = "代理";//四级自定义名称
		while ($row = mysql_fetch_object($result)) {
			$a_name = $row->a_name;
			$b_name = $row->b_name;
			$c_name = $row->c_name;
			$d_name = $row->d_name;
		}

		$arr = array(
			'a_name'=>$a_name,
			'b_name'=>$b_name,
			'c_name'=>$c_name,
			'd_name'=>$d_name
		);
		$out = json_encode($arr);
		echo $out;
	break;
	
	case 'promoters'://查询推广员等级
		
		//推广员分佣级别和推广员自定义名称-----start
				
		$query="select id,level,exp_name from weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." order by level asc";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$exp_name = "";//推广员自定义名称
		$c_id     = -1;//等级名字id
		$level_arry = array("一级","二级","三级","四级","五级","六级","七级","八级");//若某等级自定义名字为空，则填充相应的等级
		$i = 0;
		$arr = array();
		while ($row = mysql_fetch_object($result)) {
			$exp_name = $row->exp_name;
			$c_id     = $row->id;
			if($exp_name==""){//若某等级自定义名字为空，则填充相应的等级
				$exp_name = $level_arry[$i];
			}
			$i++;
			$tmp = array(
				"id"=>$c_id,
				"exp_name"=>$exp_name
				);
			array_push($arr ,$tmp);
		}
		//推广员分佣级别和推广员自定义名称-----end
		$out = json_encode($arr);
		echo $out;
	break;
}		
?>
