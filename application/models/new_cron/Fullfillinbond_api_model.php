<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Fullfillinbond_api_model extends CI_Model
{
  private $seller_id='';
  private $auth_token='';
  private $access_key='';
  private $secret_key='';
  private $market_id='';
  private $service_url='mws.amazonservices.com';
  public function  __construct()
  {
      parent::__construct();
  }


  public function checkexist($table,$where)
	{
        $this->db->from($table);
        $this->db->where($where);
        $query  = $this->db->get();
    		$output = 0;
    		if($query->num_rows() > 0)
    			$output = 1;
    		return $output;
	}


  public function get_all_records($user_id='')
  {
    $this->db->select('*');
    $this->db->from('fba_shipment_details');
    if ($user_id !='') {
      $this->db->where('user_id',$user_id);
    }

    $this->db->where('shipment_status','CLOSED');
    $query  = $this->db->get();
    $output = [];
    if($query->num_rows() > 0)
      $output = $query->result();

    return $output;
  }


}
?>
