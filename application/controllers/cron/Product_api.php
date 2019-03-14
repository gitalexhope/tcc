<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_api extends CI_Controller 
{
  public function  __construct()
	{
	     parent::__construct();
       $this->load->model('new_cron/process_product_api','product_api');
  }

  public function product_match($user_id='')
  {
    $users=$this->product_api->get_seller_for_process($user_id);
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {
        $this->product_api->set_credentials($usr);
        $prod_list=$this->product_api->get_product_to_match($usr['profile_id'],$usr['country_code']);
        if(!empty($prod_list))
         {
           foreach($prod_list as $prd)
           {
              if(!empty($prd['prod_asin']))
              {
                 $res=$this->product_api->fetch_product_details($usr['profile_id'],$prd['prod_asin'],$usr['amz_code'],$usr['country_code']);
                 if($res['status_code']==1)
                  {
                      echo $res['payload']['lm_asin']."\t".$res['payload']['brand']."\t".$usr['country_code']."\n";
                      $this->db->query("UPDATE customer_product SET prod_brand=".$this->db->escape($res['payload']['brand']).", prod_image=".$this->db->escape($res['payload']['image'])." WHERE prod_country=".$this->db->escape($usr['country_code'])." AND prod_asin=".$this->db->escape($prd['prod_asin']));
                  }
              }
           }
         }        
      }
    }
  }
  public function check_hijack($user_id='')
  {
    $users=$this->product_api->get_seller_for_process($user_id);
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {
        $this->product_api->set_credentials($usr);
        $prod_list=$this->product_api->get_product_to_hijack_check($usr['profile_id'],$usr['country_code']);
        if(!empty($prod_list))
        {
           foreach($prod_list as $prd)
           {
              if(!empty($prd['prod_asin']))
              {
                 $res=$this->product_api->check_hijack_details($usr['profile_id'],$prd['prod_asin'],$usr['amz_code'],$usr['country_code']);
                 sleep(1);
                 if($res['status_code']==1)
                 {
                     echo $usr['profile_id']."\t".$prd['prod_asin']."\t".$res['hijack_count']."\n";
                     $is_alert_sent=$res['hijack_count']==0?0:$prd['is_alert_sent'];
                     $this->db->query("UPDATE customer_product SET hijacked_count=".$this->db->escape($res['hijack_count']).",last_hijack_check=now(),is_alert_sent=".$this->db->escape($is_alert_sent)." WHERE check_hijack=1 AND prod_asin=".$this->db->escape($prd['prod_asin'])." AND prod_country=".$this->db->escape($usr['country_code']));
                 }

              }
           }
           $this->send_alert_mail($usr['profile_id']);  
        }
      }
    }
  }

  public function send_alert_mail($user_id)
  {
    $qry=$this->db->query("SELECT * FROM customer_product WHERE added_by={$user_id} AND check_hijack = 1 and hijacked_count>0 and is_alert_sent<>1");
    $ng_feed=$qry->result_array();
    $qr=$this->db->query("SELECT * from scr_user where scr_u_id=".$user_id);
    $res=$qr->result_array();
  
     if(count($ng_feed) >0)
       {
      
        $msg="Hi, ".$res[0]['scr_firstname']."<br>";
        $msg.=" You have got some ASIN been hijacked by some other seller, please have a look at it<br>";

        foreach($ng_feed as $fd)
        {
           $msg.="<p><b>Title :".$fd['prod_title']." </b> ";
           $msg.="<p><b>SKU :".$fd['prod_sku']." </b> ";
           $msg.="<p><b>ASIN :".$fd['prod_asin']." </b> Last check On ".$fd['last_hijack_check'];
        }
        $alert_type="Hijack Alert";
        $alert_subject="Hijack Alert Notification Mail";
        $alert_msg=$msg;
        $sent_mail=1;
        $timestamp=date('Y-m-d H:s:i');
        $this->load->model('alert_model');
        $this->alert_model->set_alert($alert_type,$alert_subject,$alert_msg,$sent_mail,$user_id,$timestamp);
        $up_sql="UPDATE customer_product SET is_alert_sent=1 where added_by={$user_id} AND prod_asin IN (";
        foreach($ng_feed as $fd)
        {
              $up_sql.=$this->db->escape($fd['prod_asin']).",";
        } 
        $up_sql=rtrim($up_sql,',').")";
        $this->db->query($up_sql);
      }
}


  
  
}