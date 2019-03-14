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
  
 
 public function get_report()
  {
    $qry=$this->db->query("SELECT profile_id,req_id,report_id,request_type,user_id,seller_id,auth_token,access_key,secret_key,market_placeID FROM settlement_report_feed INNER JOIN amazon_profile on profile_id=user_id where report_id<>'' and is_processed=0 ");
    $usr=$qry->result_array();
    foreach($usr as $usr)
    {
      echo $usr['req_id']."\n";
        $this->settlement_api->set_credentials($usr['profile_id'],$usr['seller_id'],$usr['auth_token'],$usr['access_key'],$usr['secret_key'],$usr['market_placeID']);
        $res=$this->settlement_api->get_report_request($usr['profile_id'],$usr['req_id'],$usr['report_id']);     
        sleep(30);
    }
  }
      

}