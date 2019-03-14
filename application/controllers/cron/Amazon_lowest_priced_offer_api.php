<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Amazon_lowest_priced_offer_api extends CI_Controller
{
  public function  __construct()
  {
	     parent::__construct();
       	 $this->load->model('amazon_feed/amazon_lowest_priced_api','amazon_api');
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
 			   time_nanosleep(0, 200000000);
 			  	echo "Processing\t".$prd['pro_asin']."\n";
 			  	$res=$this->amazon_api->fetch_product_details($usr['profile_id'],$prd['pro_asin']);
 			  	if($res['status_code']==1)
 			  	{
 			  		$qi="UPDATE product_info SET pro_no_of_fba_offers=".$this->db->escape($res['payload']['no_of_fba_offers']).",pro_min_fba_price=".$this->db->escape($res['payload']['min_fba_price']).",pro_min_fba_ship_price=".$this->db->escape($res['payload']['min_fba_ship_price']).",pro_max_fba_price=".$this->db->escape($res['payload']['max_fba_price']).",pro_max_fba_ship_price=".$this->db->escape($res['payload']['max_fba_ship_price']).",pro_avg_fba_price=".$this->db->escape($res['payload']['avg_fba_price']).",pro_avg_fba_ship_price=".$this->db->escape($res['payload']['avg_fba_ship_price']).",pro_bb_price=".$this->db->escape($res['payload']['bb_price']).",pro_bb_ship_price=".$this->db->escape($res['payload']['bb_ship_price']).",pro_bb_offer_type=".$this->db->escape($res['payload']['bb_offer_type']).",process_flag='1'  WHERE pro_asin='".$prd['pro_asin']."' ";
					print_r($qi);

                    $this->db->query($qi);
					echo "\n INSERT MADED**********************\n";
 			  	}
 			  	elseif($res['status_code']==3)
 			  	{
 			  		//$product[]=$res['payload'];
 			  		$qi="UPDATE product_info SET process_flag='1'  WHERE pro_asin='".$prd['pro_asin']."' ";
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
