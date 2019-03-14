<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Amazon_settlement_api extends CI_Controller 
{
  public function  __construct()
	{
	     parent::__construct();
       $this->load->model('amazon_feed/amazon_settlement_model','settlement_api');
  }

  public function  fetch_amazon_settlement_report_id()
  {
    $query=$this->db->query("SELECT * FROM amazon_profile");
    $user=$query->result_array();
    if(count($user) > 0)
    {
      foreach($user as $usr)
      {
          // $this->request_report($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID']);
          $this->settlement_api->set_credentials($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID']);
          $res=$this->settlement_api->get_settlement_report_id($usr['profile_id']);     
          
      }
    }
  }
  
  // public function get_listing_report()
  // {
    // $query=$this->db->query("SELECT * FROM amazon_profile");
    // $user=$query->result_array();
    // if(count($user) > 0)
    // {
    //   foreach($user as $usr)
    //   {
    //       $this->request_report($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID']);
          
    //   }
    // }
  //   $this->update_report_status();
  // }
  // public function update_report_status()
  // {
  //   $qry=$this->db->query("SELECT profile_id,user_id,seller_id,auth_token,access_key,secret_key,market_placeID FROM report_feed INNER JOIN amazon_profile on profile_id=user_id where request_id<>'' group by user_id");
  //   $usr=$qry->result_array();
  //   foreach($usr as $usr)
  //   {
  //       $query=$this->db->query("SELECT req_id,request_id FROM report_feed where user_id=".$usr['user_id']." AND request_id<>''");  
  //       $rep=$query->result_array();
  //       if(count($rep)>0)
  //       $this->update_report($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID'],$rep);
  //   }
  //   $this->get_report();
  // }
  public function get_report()
  {
    $qry=$this->db->query("SELECT profile_id,req_id,report_id,request_type,user_id,seller_id,auth_token,access_key,secret_key,market_placeID FROM settlement_report_feed INNER JOIN amazon_profile on profile_id=user_id where report_id<>'' and is_processed=0 ");
    $usr=$qry->result_array();
    foreach($usr as $usr)
    {
      echo $usr['req_id']."\n";
        $this->settlement_api->set_credentials($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID']);
        $res=$this->settlement_api->get_report_request($usr['profile_id'],$usr['req_id'],$usr['report_id']);     
        //sleep(60);
    }
  }
  // public function get_report()
  // {
  //   $qry=$this->db->query("SELECT profile_id,user_id,seller_id,auth_token,access_key,secret_key,market_placeID FROM report_feed INNER JOIN amazon_profile on profile_id=user_id where report_id<>'' and is_processed=0 group by user_id");
  //   $usr=$qry->result_array();
  //   foreach($usr as $usr)
  //   {
  //       $query=$this->db->query("SELECT req_id,report_id FROM report_feed where user_id=".$usr['user_id']." AND report_id<>'' and is_processed=0 order by process_on desc");  
  //       $rep=$query->result_array();
  //       if(count($rep)>0)
  //       foreach($rep as $rp)
  //       {
  //       	echo "Processing for ".$rp['report_id']."\n";
  //       	$this->get_report_file($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID'],$rp['report_id']);	
  //       }
        
  //   }
  // }
  // public function get_report_file($user_id,$seller_id,$auth_token,$access_key,$secret_key,$market_placeID,$report_id)
  // {
  //    if(!$this->settlement_api->set_credentials($user_id,$seller_id,$auth_token,$access_key,$secret_key,$market_placeID))
  //    {
  //        echo "No Credentials available";
  //    }
  //    $res=$this->settlement_api->get_report_request($user_id,$report_id);     
  // }

  

// public function update_report($user_id,$seller_id,$auth_token,$access_key,$secret_key,$market_placeID,$report_arr)
//   {
//      if(!$this->settlement_api->set_credentials($user_id,$seller_id,$auth_token,$access_key,$secret_key,$market_placeID))
//      {
//          echo "No Credentials available";
//      }
//      $res=$this->settlement_api->update_report_request($user_id,$report_arr);     
//   }
// public function request_report($user_id,$seller_id,$auth_token,$access_key,$secret_key,$market_placeID)
//   {
     // if(!$this->settlement_api->set_credentials($user_id,$seller_id,$auth_token,$access_key,$secret_key,$market_placeID))
     // {
     //     echo "No Credentials available";
     // }
     // $res=$this->settlement_api->make_report_request($user_id);     
//   }

      

}