<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$customer_id = $_SESSION["customer_id"];
require('../customer_id_decrypt.php');
require('../common/utility.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
//require('../common/common_from.php');
require('../common/own_data.php');
$op = '';
if(!empty($_GET["op"])){
	$op = $configutil->splash_new($_GET["op"]);
}
$page = -1;
$start = -1;
$end = 10;
if(!empty($_POST["page"])){
	$page = $configutil->splash_new($_POST["page"]);
}
$start =($page - 1) * 10;
switch ($op){
	case 'get':
	
		//-------查找商城返现
		$query = "select is_cashback from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		$is_cashback = 0;//是否开启消费返现
		//$cashback_perday_old = 0; //每天限制领取返现金额
		$shop_id=-1;
		while ($row = mysql_fetch_object($result)) {
			$is_cashback = $row->is_cashback;//是否开启消费返现
			
		}
		/*
		$query = "select id,cb_condition,cashback,cashback_r,get_cb_condition,perday_condition,cashback_perday,cashback_perday_r,cashback_perday_r_l from weixin_commonshop_cashback where isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result = mysql_query($query) or die('L39: '.mysql_error());
		$key_id = -1;
		$cb_condition = 0;	//返现金额模式    0：固定金额  1：产品价格按比例
		$cashback = 0;
		$cashback_r = 0;
		$get_cb_condition = 0;
		$perday_condition = 0;
		$cashback_perday = 0;
		$cashback_perday_r = 0;
		$cashback_perday_r_l = 0;
		*/

		while($row = mysql_fetch_object($result)){
			$key_id = $row->id;
			$cb_condition = $row->cb_condition;
			$cashback = $row->cashback;
			$cashback_r = $row->cashback_r;
			$get_cb_condition = $row->get_cb_condition;
			$perday_condition = $row->perday_condition;
			$cashback_perday = $row->cashback_perday;
			$cashback_perday_r = $row->cashback_perday_r;
			$cashback_perday_r_l = $row->cashback_perday_r_l;
		}
		//-------查找商城返现
		
		$rtn_data = array();
		$user_id = $configutil->splash_new($_POST['user_id']);
		$type = $configutil->splash_new($_POST['type']);
		
		$query = "select cb_condition from weixin_commonshop_cashback where isvalid=true and customer_id=".$customer_id." limit 0,1";
		$result = mysql_query($query) or die('L324: ' . mysql_error());  	
		while ($row = mysql_fetch_object($result)) {
		   $cb_condition = $row->cb_condition;   //返现金额模式    0：固定金额  1：产品价格按比例
		}
		$temp_arr 	 =  array();		
		//我的商品收藏和商家收藏
        //2016年7月7日14:41:56 子祥（增加线下商城商品足迹）
		//type 足迹：1：商城商品足迹 2：店铺足迹 3：微店足迹 4:线下商城商品足迹
        switch($type){
            case 1:
                $query = "select id,visit_id,visit_type,createtime from weixin_commonshop_visit where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and (visit_type=1 or visit_type=4) order by createtime desc";
            break;
            case 2:
                $query = "select id,visit_id,visit_type,createtime from weixin_commonshop_visit where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and (visit_type=2 or visit_type=3) order by createtime desc";
            break;
            default:
                $query = "select id,visit_id,visit_type,createtime from weixin_commonshop_visit where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and visit_type=".$type." order by createtime desc";
            break;  
        }
		//$query = "select id,visit_id,visit_type,createtime from weixin_commonshop_visit where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and visit_type=".$type." order by createtime ";
		//echo $query;
		
		/*** 统计数目 start ***/
		$rcount = 0;	//总数
		$query_count = "select count(a.id) as rcount from(".$query.")a";	//统计查询后的数目
		$result_count = mysql_query($query_count) or die('Query_count failed:'.mysql_error());
		while($row_count = mysql_fetch_object($result_count)){
			$rcount = $row_count->rcount;
		}
		/*** 统计数目 end ***/
		
		$query .= " limit ".$start." , ".$end."";
		$id = -1;
		$visit_id = -1;
		$visit_type = -1;
		$createtime = '';
		$result=mysql_query($query)or die('L32 Query failed'.mysql_error());
		while($row=mysql_fetch_object($result)){
			$id 			= $row->id;
			$visit_id 	    = $row->visit_id;
			$visit_type     = $row->visit_type;
			$createtime     = $row->createtime;
			if($visit_id<0){
				continue;
			}
			if($visit_type ==1 ){		//查找商品详情
				$product_data = array();
				$query2 = "select id,name,default_imgurl,isvalid,isout,is_supply_id,orgin_price,now_price,isvp,vp_score,back_currency,cashback,cashback_r,show_sell_count,is_free_shipping from weixin_commonshop_products where customer_id=".$customer_id." and id=".$visit_id."";
				//echo $query2;
				$pro_id 			= '';//产品ID
				$pro_name 			= '';//产品名称
				$default_imgurl 	= '';//默认图片
				$pro_supply_id 		= '';//供应商ID
				$orgin_price 		= '';//原价
				$now_price 			= '';//现价
				$isvp 				= '';//是否VP产品
				$vp_score 			= '';//VP值
				$back_currency		= '';//返购物币
				$show_sell_count	= '';//虚拟销售量
				$is_free_shipping	= '';//是否包邮，1是，0否
				$isvalid	        = '';//有效性
				$isout	            = '';//是否下架
				
				$result2=mysql_query($query2)or die('L41 Query failed'.mysql_error());
				while($row2=mysql_fetch_object($result2)){
					$pro_id		  	  = $row2->id;
					$pro_name		  = $row2->name;
					$default_imgurl   = $row2->default_imgurl;
					$pro_supply_id 	  = $row2->is_supply_id;
					$orgin_price 	  = $row2->orgin_price;
					$now_price 		  = $row2->now_price;
					$isvp 			  = $row2->isvp;
					$vp_score 		  = $row2->vp_score;
					$back_currency 	  = $row2->back_currency;
					$p_cashback 	  = $row2->cashback;
					$p_cashback_r	  = $row2->cashback_r;
					$show_sell_count  = $row2->show_sell_count;
					$is_free_shipping = $row2->is_free_shipping;
					$isvalid          = $row2->isvalid;
					$isout            = $row2->isout;
					
					//计算返现金额
					/*
					if($is_cashback == 1){	//返现开关
						$product_data['is_cashback']= 1;
						if($cb_condition == 0){					//返现金额
								$pro_cash_money = $cashback;
						}else{									//返现比例
							
							if($p_cashback_r>0){				//产品返现比例大于0则使用本身比例，否则使用默认比例
								$pro_cash_money = $now_price * $p_cashback_r;
							}else{
								$pro_cash_money = $now_price * $cashback_r;
							}
						
						}
						$product_data['pro_cash_money'] = round($pro_cash_money,2);
					}else{
						$product_data['is_cashback'] = 0;
					}	*/		
					//计算返现金额	
					
					/*返现金额开始*/
					if($is_cashback == 1){ //开启了消费返现
						$p_now_price = $now_price;
						$info = new my_data();//own_data.php my_data类
						$showAndCashback = $info->showCashback($customer_id,$user_id,$p_cashback,$p_cashback_r,$p_now_price);
						$product_data['pro_cash_money']  = $showAndCashback['cashback_m'];
						$product_data['display']		 = $showAndCashback['display'];						
					}
					/*返现金额结束*/
					
					$product_data['pro_id'] 		 = $pro_id;
					$product_data['pro_name'] 		 = $pro_name;
					$product_data['default_imgurl']  = $default_imgurl;
					$product_data['pro_supply_id'] 	 = $pro_supply_id;
					$product_data['orgin_price']     = $orgin_price;
					$product_data['now_price'] 		 = $now_price;
					$product_data['isvp'] 			 = $isvp;
					$product_data['vp_score'] 		 = $vp_score;
					$product_data['cashback'] 		 = $p_cashback;
					$product_data['cashback_r'] 	 = $p_cashback_r;
					$product_data['show_sell_count'] = $show_sell_count;
					$product_data['cb_condition'] 	 = $cb_condition;
					$product_data['is_free_shipping']= $is_free_shipping;
					$product_data['isvalid']         = $isvalid;
					$product_data['isout']           = $isout;
					$product_data['back_currency']   = $back_currency;
					$product_data['is_cashback']  	 = $is_cashback;
					
					
				
				}
				//var_dump($product_data);
				$temp_arr = array(
				'id'=>$id,
				'visit_id'=>$visit_id,
				'visit_type'=>$visit_type,				
				'rtn_data'=>$product_data,
				'rcount'=>$rcount
				);
				
			}elseif($visit_type ==2){	//查找商家详情
					$brand_data = array();
					$brand="select brand_logo,brand_name,isvalid from weixin_commonshop_brand_supplys where customer_id=".$customer_id." and brand_status=1 and user_id=".$visit_id."";
					//echo $brand;
					
					$brand .= " limit ".$start." , ".$end."";
					$brand_logo = '';
					$brand_name = '';
					$isvalid    = '';
					$result2=mysql_query($brand) or die ('brand faild' .mysql_error());
						while($row2=mysql_fetch_object($result2)){
							$brand_logo=$row2->brand_logo;
							$brand_name=$row2->brand_name;	
							$isvalid   =$row2->isvalid;	
							
							$brand_data['brand_logo'] = $brand_logo;		
							$brand_data['brand_name'] = $brand_name;		
							$brand_data['isvalid']    = $isvalid;		
						}
						
					$temp_arr = array(
						'id'=>$id,
						'visit_id'=>$visit_id,
						'visit_type'=>$visit_type,				
						'rtn_data'=>$brand_data,
						'rcount'=>$rcount
						);		
			}elseif($visit_type ==3){	//查找微店详情
					$brand_data = array();
					$brand="select shop_name,shop_bgimgurl,isvalid from weixin_commonshop_microshop where customer_id=".$customer_id." and isvalid=1 and user_id=".$visit_id."";
					//echo $brand;
					
					$brand .= " limit ".$start." , ".$end."";
					$brand_logo = '';
					$brand_name = '';
					$isvalid    = '';
					$result2=mysql_query($brand) or die ('micro faild' .mysql_error());
						while($row2=mysql_fetch_object($result2)){
							$brand_logo=$row2->shop_bgimgurl;
							$brand_name=$row2->shop_name;	
							$isvalid   =$row2->isvalid;	
							
							$brand_data['brand_logo'] = $brand_logo;		
							$brand_data['brand_name'] = $brand_name;		
							$brand_data['isvalid']    = $isvalid;		
						}
						
					$temp_arr = array(
						'id'=>$id,
						'visit_id'=>$visit_id,
						'visit_type'=>$visit_type,				
						'rtn_data'=>$brand_data,
						'rcount'=>$rcount
						);		
			}else if($visit_type ==4 ){       //线下商城查找商品详情
                $product_data = array();
                $query2 = "select id,name,default_imgurl,isvalid,isout,supply_id,orgin_price,now_price,sell_count from weixin_cityarea_shop_products where customer_id=".$customer_id." and id=".$visit_id."";
                //echo $query2;
                $pro_id             = '';//产品ID
                $pro_name           = '';//产品名称
                $default_imgurl     = '';//默认图片
                $pro_supply_id      = '';//供应商ID
                $orgin_price        = '';//原价
                $now_price          = '';//现价
                $show_sell_count    = '';//销售量
                $isvalid            = '';//有效性
                $isout              = '';//是否下架
                
                $result2=mysql_query($query2)or die('L41 Query failed'.mysql_error());
                while($row2=mysql_fetch_object($result2)){
                    $pro_id           = $row2->id;
                    $pro_name         = $row2->name;
                    $default_imgurl   = $row2->default_imgurl;
                    $pro_supply_id    = $row2->supply_id;
                    $orgin_price      = $row2->orgin_price;
                    $now_price        = $row2->now_price;
                    $show_sell_count  = $row2->sell_count;
                    $isvalid          = $row2->isvalid;
                    $isout            = $row2->isout;
 
                    $product_data['pro_id']          = $pro_id;
                    $product_data['pro_name']        = $pro_name;
                    $product_data['default_imgurl']  = $default_imgurl;
                    $product_data['pro_supply_id']   = $pro_supply_id;
                    $product_data['orgin_price']     = $orgin_price;
                    $product_data['now_price']       = $now_price;
                    $product_data['show_sell_count'] = $show_sell_count;
                    $product_data['isvalid']         = $isvalid;
                    $product_data['isout']           = $isout;
                    
                    
                
                }
                //var_dump($product_data);
                $temp_arr = array(
                'id'=>$id,
                'visit_id'=>$visit_id,
                'visit_type'=>$visit_type,              
                'rtn_data'=>$product_data,
                'rcount'=>$rcount
                );
                
            }
			
			
			//var_dump($temp_arr);	
			array_push($rtn_data,$temp_arr);
			
		} 
			$out = json_encode($rtn_data);
			echo $out;
		
	break;
	
	case 'del_visit':
		$ids = $configutil->splash_new($_POST['ids']);
		$ids_arr = explode(',',$ids);
		$type = 1; //1、收藏商品；2、收藏店铺
		$ids_str = '';
		$query = "update weixin_commonshop_visit Set isvalid = Case id ";
						
		foreach($ids_arr as $values){
			if($values == '' or $values ==-1 or $values ==0){
				continue;
			}
			$ids_str .= $values.',';
			$query .= "When ".$values." Then false ";	
		}
		$ids_str = substr($ids_str,0,-1);	//删除最后一个逗号
		$query .= "	END where id in(".$ids_str.")";
		//echo $query;
		$result=mysql_query($query)or die('L149 Query failed'.mysql_error());
		$error = mysql_error();
		if($error ==0 ){
			echo 1;
		}else{
			echo 0;	
		}
	break;
	
	
}



?>