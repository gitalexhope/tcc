<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Amazon_mws_product_api extends CI_Controller 
{
  public function  __construct()
  {
	     parent::__construct();
       	 $this->load->model('amazon_feed/amazon_product_api','amazon_api');
  }

  public function product_match()
  {
    $query=$this->db->query("SELECT * FROM amazon_profile");
    $user=$query->result_array();
	//print_r($user);
	//die();
    if(count($user) > 0)
    {
      foreach($user as $usr)
      {
         $this->amazon_api->set_credentials($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID']);
         $prod_list=$this->amazon_api->get_product_to_match(600000,$usr['profile_id']);
         if(!empty($prod_list))
         {
         	echo date('y-m-d h:i:s')."\n";
         	foreach($prod_list as $prd)
         	{
 			  if($prd['pro_asin'])
 			  {
 			   time_nanosleep(0, 250000000);
 			  	echo "Processing\t".$prd['pro_asin']."\n";
 			  	$res=$this->amazon_api->fetch_product_details($usr['profile_id'],$prd['pro_asin']);
 			  	if($res['status_code']==1)
 			  	{
 			  		$qi="UPDATE product_info SET pro_asin_counts=".$this->db->escape($res['payload']['asin_counts']).",pro_rank=".$this->db->escape($res['payload']['sales_rank']).",added_on=now(),pro_title=".$this->db->escape($res['payload']['title']).",pro_image=".$this->db->escape($res['payload']['image']).",pro_p_height=".$this->db->escape($res['payload']['p_height']).",pro_p_length=".$this->db->escape($res['payload']['p_length']).",pro_p_width=".$this->db->escape($res['payload']['p_width']).",pro_p_weight=".$this->db->escape($res['payload']['p_weight']).",pro_brand=".$this->db->escape($res['payload']['brand']).",pro_category=".$this->db->escape($res['payload']['category']).",pro_model=".$this->db->escape($res['payload']['model']).",pro_model=".$this->db->escape($res['payload']['model']).",pro_manufacturer=".$this->db->escape($res['payload']['manufacturer']).",pro_part_num=".$this->db->escape($res['payload']['part_number']).",pro_pack_qty=".$this->db->escape($res['payload']['pack_qty']).",pro_curr_code=".$this->db->escape($res['payload']['currencycode']).",pro_itm_weight=".$this->db->escape($res['payload']['weight'])." WHERE pro_asin='".$prd['pro_asin']."' ";
					print_r($qi);
					
                    $this->db->query($qi);
					echo "\n INSERT MADED**********************\n";
 			  	}
 			  	elseif($res['status_code']==3)
 			  	{
 			  		//$product[]=$res['payload'];
 			  		$qi="UPDATE product_info SET pro_asin_counts=".$this->db->escape($res['payload']['asin_counts'])." WHERE pro_asin='".$prd['pro_asin']."' ";
					//print_r($qi);
					
                    $this->db->query($qi);
					echo "\n INSERT MADED**********************\n";
 			  	}
 			 
         }
	     
	  }
	}
  }
 }
}


  public function product_lowest_match()
  {
    $query=$this->db->query("SELECT * FROM amazon_profile");
    $user=$query->result_array();
	//print_r($user);
	//die();
    if(count($user) > 0)
    {
      foreach($user as $usr)
      {
         $this->amazon_api->set_credentials($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID']);
         $prod_list=$this->amazon_api->get_product_to_match(600000);
         if(!empty($prod_list))
         {
         	echo date('y-m-d h:i:s')."\n";
         	foreach($prod_list as $prd)
         	{
 			  if($prd['pro_asin'])
 			  {
 			   time_nanosleep(0, 250000000);
 			  	echo "Processing\t".$prd['pro_asin']."\n";
 			  	$res=$this->amazon_api->fetch_lowest_product_details($usr['profile_id'],$prd['pro_asin']);
 			  	if($res['status_code']==1)
 			  	{
 			  		$qi="UPDATE product_info SET  pro_rank=".$this->db->escape($res['payload']['sales_rank']).",added_on=now(),pro_title=".$this->db->escape($res['payload']['title']).",pro_image=".$this->db->escape($res['payload']['image']).",pro_p_height=".$this->db->escape($res['payload']['p_height']).",pro_p_length=".$this->db->escape($res['payload']['p_length']).",pro_p_width=".$this->db->escape($res['payload']['p_width']).",pro_p_weight=".$this->db->escape($res['payload']['p_weight']).",pro_brand=".$this->db->escape($res['payload']['brand']).",pro_category=".$this->db->escape($res['payload']['category']).",pro_model=".$this->db->escape($res['payload']['model']).",pro_model=".$this->db->escape($res['payload']['model']).",pro_manufacturer=".$this->db->escape($res['payload']['manufacturer']).",pro_part_num=".$this->db->escape($res['payload']['part_number']).",pro_pack_qty=".$this->db->escape($res['payload']['pack_qty']).",pro_curr_code=".$this->db->escape($res['payload']['currencycode']).",pro_itm_weight=".$this->db->escape($res['payload']['weight'])." WHERE pro_asin='".$prd['pro_asin']."' ";
					print_r($qi);
					
                    $this->db->query($qi);
					echo "\n INSERT MADED**********************\n";
 			  	}
 			  	elseif($res['status_code']==3)
 			  	{
 			  		//$product[]=$res['payload'];
 			  		$qi="UPDATE product_info SET added_on=now() WHERE pro_asin='".$prd['pro_asin']."' ";
					//print_r($qi);
					
                    $this->db->query($qi);
					echo "\n INSERT MADED**********************\n";
 			  	}
 			 
         }
	     
	  }
	}
  }
 }
}
}