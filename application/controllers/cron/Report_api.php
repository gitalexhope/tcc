<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_api extends CI_Controller 
{
  public function  __construct()
	{
	     parent::__construct();
       $this->load->model('new_cron/Process_report_model','report_api');
  }

  public function manage_scheduled_report($report_type='_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_',$sch_status='_30_MINUTES_',$user_id='')
  {
    $users=$this->report_api->get_seller_for_process($user_id);
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {
        $this->report_api->set_credentials($usr);
        $this->report_api->manage_scheduled_report($user_id,$report_type,$sch_status);
      }
    }
  }

  public function request_report($report_type='_GET_MERCHANT_LISTINGS_DATA_',$time_from='30',$user_id='')
  {
    
   $users=$this->report_api->get_seller_for_process($user_id);
   //print_r($users);
   //die();
 
   if(count($users) > 0)
    {
      foreach($users as $usr)
      {
		 //sleep(1); 
        $this->report_api->set_credentials($usr);
        $res=$this->report_api->request_report($usr['profile_id'],$report_type,$time_from);
      }
    }
  }
  
   public function request_report_new($report_type='_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_ORDER_DATE_',$time_from='30',$user_id='1')
  {
    
   $users=$this->report_api->get_seller_for_process_new($user_id);
   //print_r($users);
   //die();
 
   if(count($users) > 0)
    {
      foreach($users as $usr)
      {
		 //sleep(1); 
        $this->report_api->set_credentials($usr);
        $res=$this->report_api->request_report($usr['profile_id'],$report_type,$time_from);
      }
    }
  }
  public function update_report_status($request_id='NULL',$user_id='')
  {
    echo "Cron triggered on :".date('Y-m-d H:i:s')."\n";
    $users=$this->report_api->get_seller_who_have_pending_report($request_id,$user_id);

    if(count($users) > 0)
    {
echo "User count :".count($users)."\n";

      foreach($users as $usr)
      {
echo "Current user :".$usr['profile_id']."\n";

        $this->report_api->set_credentials($usr);
        $rep=$this->report_api->get_seller_pending_report($usr['profile_id']);
//print_r($rep);
        if(count($rep)>0)
        {
          $this->report_api->set_credentials($usr);
          $res=$this->report_api->update_report_request($usr['profile_id'],$rep);     
//print_r($res);
         }
      }
    }
echo "Cron completed on :".date('Y-m-d H:i:s')."\n";

  }
  
  public function get_report($request_id='NULL',$user_id='')
  {
     echo "Cron triggered on :".date('Y-m-d H:i:s')."\n";
    $users=$this->report_api->get_seller_who_have_generated_report($request_id,$user_id);
	//die();
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {
		 print_r($usr['country_code']);
		// die();
        $this->report_api->set_credentials($usr);
        $res=$this->report_api->get_report($usr);
		
     if(is_file($res['report_file']))
     {
       $this->load->model('new_cron/report_file_process','report_process');
	   
       if($usr['request_type']=='_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_')
       {
           $this->report_process->process_fba_shipments_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
       }
	   elseif($usr['request_type']=='_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_LAST_UPDATE_')
       {
           $this->report_process->process_order_update_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
       }
       elseif($usr['request_type']=='_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_ORDER_DATE_')
       {
           $this->report_process->process_order_data_by_date($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
       }
	   elseif($usr['request_type']=='_GET_FBA_FULFILLMENT_CUSTOMER_SHIPMENT_SALES_DATA_')
        {
            $this->report_process->process_fba_fulfill_ship_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
        }
	    elseif($usr['request_type']=='_GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_')
        {
            $this->report_process->process_actionable_order_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
        }
         elseif($usr['request_type']=='_GET_FLAT_FILE_ORDERS_DATA_')
        {
            $this->report_process->process_flat_order_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
        }
       
        elseif($usr['request_type']=='_GET_CONVERGED_FLAT_FILE_ORDER_REPORT_DATA_')
        {
            $this->report_process->process_converged_order_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
        }
		elseif($usr['request_type']=='_GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA_')
          {
             $this->report_process->process_fba_returns_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
          }
		elseif($usr['request_type']=='_GET_RESTOCK_INVENTORY_RECOMMENDATIONS_REPORT_')
         {
            $this->report_process->process_restock_inv_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
         }
		elseif($usr['request_type']=='_GET_FBA_FULFILLMENT_INVENTORY_HEALTH_DATA_')
         {
            $this->report_process->process_fba_inv_health_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
         }
		 elseif($usr['request_type']=='_GET_STRANDED_INVENTORY_UI_DATA_')
         {
            $this->report_process->process_stranded_inv_ui_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
         }
		 elseif($usr['request_type']=='_GET_FBA_STORAGE_FEE_CHARGES_DATA_')
         {
            $this->report_process->process_fba_storage_fee_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
         }
		 elseif($usr['request_type']=='_GET_FBA_FULFILLMENT_CUSTOMER_SHIPMENT_REPLACEMENT_DATA_')
         {
            $this->report_process->process_fba_shipment_replacement_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
         }
		 elseif($usr['request_type']=='_GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA_')
         {
            $this->report_process->process_fba_estimated_fees_txt_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
         }
		 elseif($usr['request_type']=='_GET_MERCHANT_LISTINGS_DATA_')
         {
            $this->report_process->process_inventory_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
         }
	    elseif($usr['request_type']=='_GET_MERCHANT_LISTINGS_INACTIVE_DATA_')
           {
              $this->report_process->process_inactive_inventory_data($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);
           }
	    else
        {
          $this->report_process->process_report_data_for_testing($usr['profile_id'],$res['report_file'],$usr['country_code'],$usr['request_type']);    
        }
        $this->report_process->update_report_feed_log($usr['profile_id'],$usr['req_id']);
      }
      }
    }
echo "Cron completed on :".date('Y-m-d H:i:s')."\n";
  }
  public function test_process()
  {
    $this->load->model('new_cron/report_file_process','report_process');
    $usr['profile_id']=1;
    $usr['report_file']='/var/www/html/feedback_mailing/asset/amazon_report/1_11487807186017722';
    $usr['country_code']='IT';
    $usr['request_type']='_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_';
    $this->report_process->process_fba_shipments_data($usr['profile_id'],$usr['report_file'],$usr['country_code'],$usr['request_type']);

  }
  public function update_ack_status($request_id='NULL',$user_id='')
  {
    $users=$this->report_api->get_seller_report_to_ack($request_id,$user_id);
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {

        $this->report_api->set_credentials($usr);
        $rep=$this->report_api->get_seller_completed_report($usr['profile_id']);
        
        if(count($rep)>0)
        {
          $this->report_api->set_credentials($usr);
          $res=$this->report_api->update_ack_status($usr['profile_id'],$rep);     
        }
      }
    }
  }

  public function test()
  {
    echo date('Y-m-d H:i:s');
    echo "\n";
    echo $dt='11.07.2018 11:33:55 PDT';
    echo "\n";
    echo date('Y-m-d H:i:s',strtotime($dt));
    echo "\n";
    echo $dt='22.06.2018 16:27:31 Europe/Paris';
    echo "\n";
    echo date('Y-m-d H:i:s',strtotime($dt));
    echo "\n";
    echo $dt='22.06.2018 16:27:31';
    echo "\n";
    echo date('Y-m-d H:i:s',strtotime($dt));
    echo "\n";
    echo $dt='';
    echo "\n";
    echo date('Y-m-d H:i:s',strtotime($dt));
    echo "\n";
  }
}
