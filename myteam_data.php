<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php'); //����
if(!empty($_POST['customer_id'])){
	$customer_id = $configutil->splash_new($_POST['customer_id']);
}
require('../customer_id_decrypt.php'); //�����ļ�,��ȡcustomer_id_en[���ܵ�customer_id]�Լ�customer_id[�ѽ���]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$user_id        = -1;//�û�ID
$statu          = "";//����
$choose_val     = 0;//�������ID
$commisions_val = 0;//�ȼ�����ID
$search_text    = "";//�ؼ�������
$end            = 10;
if(!empty($_POST["user_id"])){
   $user_id = $configutil->splash_new($_POST["user_id"]);
}
if(!empty($_GET["statu"])){//��������
   $statu = $configutil->splash_new($_GET["statu"]);
}
if(!empty($_GET["choose_val"])){//�������
   $choose_val = $configutil->splash_new($_GET["choose_val"]);
}
if(!empty($_GET["commisions_val"])){//�¼��ȼ�����
   $commisions_val = $configutil->splash_new($_GET["commisions_val"]);
}
switch ($statu){
	
case 'member'://��ѯ�Ŷӳ�Ա��Ϣ

	$page  = -1;	
	$start = -1;
	
	$page = $configutil->splash_new($_POST["page"]);
	$start = ($page - 1) * 10;
	
	/*��ѯ���˴��� start*/
	$generation = 0;//����
	$query = "select generation from weixin_users where id=".$user_id;
	$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
		$generation = $row->generation;
		}
		/*��ѯ���˴��� end*/
	
	$query = "";//��ѯ���
	$query_base = "SELECT 
				u.id,
				u.fromw,
				u.weixin_headimgurl,
				u.weixin_name,
				u.parent_id,
				u.createtime 
				FROM weixin_users u 
				";//weixin_users������
	$query_all = " where u.isvalid=true and match(u.gflag) against (',".$user_id.",') and u.customer_id=".$customer_id;//weixin_users������
	
	$query_p = "inner join promoters p 
				on u.id=p.user_id";//promoters������
	
		if(!empty($_GET["commisions_val"])){//�¼��ȼ�
			$query_all .= " and u.generation=".($generation+$commisions_val);
		}
	
		if(!empty($_GET["search_text"])){//�����ؼ���
			$search_text = $_GET["search_text"];
			$query_all .= " and u.weixin_name like '%".$search_text."%'";
		}
		
		$query = $query_base.$query_all;//�����ƹ�Ա������Ϊ�ܲ�ѯ���
		
		if(!empty($_GET["choose_val"])){
			$query = $query_base.$query_p.$query_all." and p.isvalid=true and p.status=1 ";//�ƹ�Աͨ��sql
			switch($choose_val){
				case '1':case '10'://�����ƹ�Ա
					
				break;
				case '2':case '20'://�����������
					$query .= " and p.isAgent in(5,6,7,8)";
				break;
				case '3':case '30'://���йɶ�
					 $query .= " and p.is_consume>0";
				break;
				case '4'://������
					 $query .= " and p.isAgent=1";
				break;
				case '5'://��Ӧ��
					$query .= " and p.isAgent=3";
				break;
				// case '6'://��ʦ
					// $query .= " and p.isAgent=4";
				break;
				
				//�ƹ�Ա��Ӷ����
				case '11':case '12':case '13':case '14':case '15':case '16':case '17':case '18':
					$commision_level = (int)substr($choose_val,1);
					$query .= " and p.commision_level=".$commision_level;
				break;
				
				//�������
				case '21'://����
					$query .= " and p.isAgent=5";
				break;
				case '22'://�д�
					$query .= " and p.isAgent=6";
				break;
				case '23'://ʡ��
					$query .= " and p.isAgent=7";
				break;
				case '24'://�Զ�������
					$query .= " and p.isAgent=8";
				break;
				
				//�ɶ�
				case '31'://�ɶ�
					$query .= " and p.is_consume=4";
				break;
				case '32'://�ܴ���
					$query .= " and p.is_consume=3";
				break;
				case '33'://����
					$query .= " and p.is_consume=2";
				break;
				case '34'://����
					$query .= " and p.is_consume=1";
				break;
			}
		}
	
	/*** ͳ������ start ***/
	$rcount = 0;	//������
	$query_count = "select count(a.id) as rcount from(".$query." group by u.id)a";	//ͳ�Ʋ�ѯ�������
	$result_count = mysql_query($query_count) or die('Query_count failed:'.mysql_error());
	while($row_count = mysql_fetch_object($result_count)){
		$rcount = $row_count->rcount;
	}
	/*** ͳ������ end ***/
	
	$query .= " group by u.id order by u.createtime desc limit ".$start.",".$end;
	//echo $query;

	$result = mysql_query($query) or die('Query failed1: ' . mysql_error());

	$p_id              = 0;//��ԱID
	$fromw             = 0;//��Դ
	$weixin_headimgurl = "";//ͷ��
	$user_name         = "����";//����
	$parent_id         = 0;//�ϼ�ID
	$sq_time           = "";//����ʱ��
	$parent_name       = "";//�ϼ�����
	$pro_id        	   = 0;	//�ƹ�Աid
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
		$pro_id        	   = 0;	//�ƹ�Աid
		$isAgent           = 0;//�ƹ�Ա�Ǽ�
		$is_consume        = 0;	//�Ƿ�Ϊ�ɶ�
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
	
	case 'commisions'://��ѯ�ƹ�Ա�ȼ�
		
		//�ƹ�Ա��Ӷ������ƹ�Ա�Զ�������-----start
		$query = "select exp_mem_name,reward_level from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$exp_mem_name = "��Ա";//�ƹ�Ա����
		$reward_level = 3;//��Ӷ����
		while ($row = mysql_fetch_object($result)) {
			$exp_mem_name = $row->exp_mem_name;
			$reward_level = $row->reward_level;
		}
		$exp_mem_name = explode("_", $exp_mem_name);
		for($i=1;$i<=$reward_level;$i++){
			if(empty($exp_mem_name[$i])){ $exp_mem_name[$i] = "".$i."����Ա";};
			$arr[$i] = $exp_mem_name[$i];
		}
		$out = json_encode($arr);
		echo $out;
	break;
	
	case 'shareholder'://��ѯ�ɶ����
		
		//�ɶ�����Զ�������-----start
		$query = "select a_name,b_name,c_name,d_name from weixin_commonshop_shareholder where isvalid=true and customer_id=".$customer_id;
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$a_name = "�ɶ�";//һ���Զ�������
		$b_name = "�ܴ���";//�����Զ�������
		$c_name = "����";//�����Զ�������
		$d_name = "����";//�ļ��Զ�������
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
	
	case 'promoters'://��ѯ�ƹ�Ա�ȼ�
		
		//�ƹ�Ա��Ӷ������ƹ�Ա�Զ�������-----start
				
		$query="select id,level,exp_name from weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." order by level asc";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$exp_name = "";//�ƹ�Ա�Զ�������
		$c_id     = -1;//�ȼ�����id
		$level_arry = array("һ��","����","����","�ļ�","�弶","����","�߼�","�˼�");//��ĳ�ȼ��Զ�������Ϊ�գ��������Ӧ�ĵȼ�
		$i = 0;
		$arr = array();
		while ($row = mysql_fetch_object($result)) {
			$exp_name = $row->exp_name;
			$c_id     = $row->id;
			if($exp_name==""){//��ĳ�ȼ��Զ�������Ϊ�գ��������Ӧ�ĵȼ�
				$exp_name = $level_arry[$i];
			}
			$i++;
			$tmp = array(
				"id"=>$c_id,
				"exp_name"=>$exp_name
				);
			array_push($arr ,$tmp);
		}
		//�ƹ�Ա��Ӷ������ƹ�Ա�Զ�������-----end
		$out = json_encode($arr);
		echo $out;
	break;
}		
?>
