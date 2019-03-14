<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Amazon_mws_finance_api extends CI_Controller 
{
  public function  __construct()
  {
	     parent::__construct();
       	 $this->load->model('amazon_feed/amazon_finance_api','amazon_api');
  }

  public function finance_orders_match()
  {
    $query=$this->db->query("SELECT * FROM amazon_profile");
    $user=$query->result_array();
	if(count($user) > 0)
    {
      foreach($user as $usr)
      {
		 
         $this->amazon_api->set_credentials($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID']);
         $prod_list=$this->amazon_api->get_orders_to_match(2000,$usr['profile_id']);
	     if(!empty($prod_list))
         {
         	echo date('y-m-d h:i:s')."\n";
         	foreach($prod_list as $prd)
         	{
 			  if($prd['order_id'])
 			  {
 			time_nanosleep(0, 500000000);
 			  	echo "Processing\t".$prd['order_id']."\n";
 			  	$res=$this->amazon_api->fetch_product_details($usr['profile_id'],$prd['order_id']);
 			  	if($res['status_code']==1)
 			  	{
 			  		//$product[]=$res['payload'];
 			  		$product[]=$prd['prod_id'];
 			  		$bulk_data[]="(".$this->db->escape($prd['prod_id']).",".$this->db->escape($res['payload']['order_id']).",".$this->db->escape($res['payload']['principal']).",".$this->db->escape($res['payload']['tax']).",".$this->db->escape($res['payload']['giftwrap']).",".$this->db->escape($res['payload']['giftwraptax']).",".$this->db->escape($res['payload']['shippingcharge']).",".$this->db->escape($res['payload']['shippingtax']).",".$this->db->escape($res['payload']['fbafee']).",".$this->db->escape($res['payload']['commission']).",".$this->db->escape($res['payload']['fixedclosingfee']).",".$this->db->escape($res['payload']['giftwrapchargeback']).",".$this->db->escape($res['payload']['shippingchargeback']).",".$this->db->escape($res['payload']['variableclosingfee']).",".$this->db->escape($res['payload']['sku']).",".$this->db->escape($res['payload']['itemid']).",".$this->db->escape($res['payload']['marketplace']).",".$this->db->escape($res['payload']['qty']).",".$this->db->escape($usr['profile_id']).")";
 			  	}
 			  	elseif($res['status_code']==3)
 			  	{
 			  		//$product[]=$res['payload'];
 			  		$product[]=$prd['prod_id'];
 			  		$bulk_data[]="(".$this->db->escape($prd['prod_id']).",".$this->db->escape($res['payload']['order_id']).",".$this->db->escape($res['payload']['principal']).",".$this->db->escape($res['payload']['tax']).",".$this->db->escape($res['payload']['giftwrap']).",".$this->db->escape($res['payload']['giftwraptax']).",".$this->db->escape($res['payload']['shippingcharge']).",".$this->db->escape($res['payload']['shippingtax']).",".$this->db->escape($res['payload']['fbafee']).",".$this->db->escape($res['payload']['commission']).",".$this->db->escape($res['payload']['fixedclosingfee']).",".$this->db->escape($res['payload']['giftwrapchargeback']).",".$this->db->escape($res['payload']['shippingchargeback']).",".$this->db->escape($res['payload']['variableclosingfee']).",".$this->db->escape($res['payload']['sku']).",".$this->db->escape($res['payload']['itemid']).",".$this->db->escape($res['payload']['marketplace']).",".$this->db->escape($res['payload']['qty']).",".$this->db->escape($usr['profile_id']).")";
 			  	if(isset($bulk_data) && count($bulk_data) == 12 )
				{
				  //print_r($bulk_data);
				   $quer=implode(',',$bulk_data);
                   $qi="INSERT INTO `finance_data` (`fin_id`,`order_id`,`principal`,`tax`,`giftwrap`,`giftwraptax`,`shippingcharge`,`shippingtax`,`fbafee`,`commission`,`fixedclosingfee`,`giftwrapchargeback`,`shippingchargeback`,`variableclosingfee`,`sku`,`itemid`,`marketplace`,`qty`,`added_by`) 
            	   VALUES $quer
                   ON DUPLICATE KEY
                   UPDATE
                   fin_id=VALUES(fin_id),order_id=VALUES(order_id),principal=VALUES(principal),tax=VALUES(tax),giftwrap=VALUES(giftwrap),giftwraptax=VALUES(giftwraptax),shippingcharge=VALUES(shippingcharge),shippingtax=VALUES(shippingtax),fbafee=VALUES(fbafee),commission=VALUES(commission),fixedclosingfee=VALUES(fixedclosingfee),giftwrapchargeback=VALUES(giftwrapchargeback),shippingchargeback=VALUES(shippingchargeback),variableclosingfee=VALUES(variableclosingfee),sku=VALUES(sku),itemid=VALUES(itemid),marketplace=VALUES(marketplace),qty=VALUES(qty),added_by=VALUES(added_by);";
                   $this->db->query($qi);
                   echo "\n INSERT MADED**********************\n";
				   $sql="UPDATE rep_orders_data_order_date_list SET fee_flag=1 WHERE prod_id in (";
                   $id_csv="'";  
                   foreach($product as $pro_asin)
                   {
                      $id_csv.=$pro_asin."','";
					  
                   } 
                   $id_csv=rtrim($id_csv,"','");
				   //echo"$id_csv";
                   $sql=$sql.$id_csv."')";
		           $this->db->query($sql);
		           unset($quer);
   				   unset($product);
   				   unset($bulk_data);
				}

 			  }
 			  	
         	}
         	if(isset($bulk_data) && count($bulk_data) > 0 )
			  {

			  	//print_r($bulk_data);
				   $quer=implode(',',$bulk_data);
                   $qi="INSERT INTO `finance_data` (`fin_id`,`order_id`,`principal`,`tax`,`giftwrap`,`giftwraptax`,`shippingcharge`,`shippingtax`,`fbafee`,`commission`,`fixedclosingfee`,`giftwrapchargeback`,`shippingchargeback`,`variableclosingfee`,`sku`,`itemid`,`marketplace`,`qty`,`added_by`) 
            	   VALUES $quer
                   ON DUPLICATE KEY
                   UPDATE
                   fin_id=VALUES(fin_id),order_id=VALUES(order_id),principal=VALUES(principal),tax=VALUES(tax),giftwrap=VALUES(giftwrap),giftwraptax=VALUES(giftwraptax),shippingcharge=VALUES(shippingcharge),shippingtax=VALUES(shippingtax),fbafee=VALUES(fbafee),commission=VALUES(commission),fixedclosingfee=VALUES(fixedclosingfee),giftwrapchargeback=VALUES(giftwrapchargeback),shippingchargeback=VALUES(shippingchargeback),variableclosingfee=VALUES(variableclosingfee),sku=VALUES(sku),itemid=VALUES(itemid),marketplace=VALUES(marketplace),qty=VALUES(qty),added_by=VALUES(added_by);";
                   $this->db->query($qi);
                   echo "\n INSERT MADED**********************\n";
				   $sql="UPDATE rep_orders_data_order_date_list SET fee_flag=1 WHERE prod_id in (";
                   $id_csv="'";  
                   foreach($product as $pro_asin)
                   {
                      $id_csv.=$pro_asin."','";
					  
                   } 
                   $id_csv=rtrim($id_csv,"','");
				   //echo"$id_csv";
                   $sql=$sql.$id_csv."')";
		           $this->db->query($sql);
		           unset($quer);
   				   unset($product);
   				   unset($bulk_data);
			  }
		    echo date('y-m-d h:i:s')."\n";
         }
	     
	  }
	}
  }
  
}
}