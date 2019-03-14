<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Amazon_transaction_model extends CI_Model
{
	  public function  __construct()
	  {
	   	 parent::__construct();
       $user=$this->session->userdata('user_logged_in');  
       $this->user_id=$user['id'];
       $this->set_config();
	  }
    public function set_config()
    {
      // $this->config->load('site_config'); 
      $this->amazon_fee_percent=15;
      $this->ssg_profit_percent=30;
      $this->customer_profit_percent=70;
      // $this->number_of_days=$this->config->item('fetch_amazon_order_detail_of');
    }

	  
    public function get_order_list($orderby='purchase_date',$direction='DESC',$offet,$limit,$searchterm='')
    {
         $srterm='';
         $status='';
         $tf_status='';
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));


            $srterm=urldecode($str[0]->searchtext);
         } 
         if(isset($str[1]->order_status))
         {
           if($str[1]->order_status == 'SHI')
           $status='Shipped';
           elseif($str[1]->order_status == 'UNS')
           $status='Unshipped';
           elseif($str[1]->order_status == 'CAN')
           $status='Canceled'; 
           elseif($str[1]->order_status == 'PEN')
           $status='Pending'; 
         }
         if(isset($str[4]->tfm_status))
         {
           if($str[4]->tfm_status == 'PIC')
           $tf_status='PickedUp';
         if($str[4]->tfm_status == 'PNP')
           $tf_status='PendingPickUp';
           
       	   elseif($str[4]->tfm_status == 'DLI')
           $tf_status='Delivered';
       	   elseif($str[4]->tfm_status == 'OUT')
           $tf_status='OutForDelivery';
       	   elseif($str[4]->tfm_status == 'LBL')
           $tf_status='LabelCanceled';
       	   elseif($str[4]->tfm_status == 'RTS')
           $tf_status='ReturnedToSeller';
       	   elseif($str[4]->tfm_status == 'UDL')
           $tf_status='Undeliverable';
	       elseif($str[4]->tfm_status == 'RBB')
           $tf_status='RejectedByBuyer';

       
       
       
       

         }
         $from_date=date('Y-m-d', strtotime('-30 days'));
         // $from_date="";
         $to_date=date('Y-m-d ');
         if(isset($str[2]->from_date) && !empty($str[2]->from_date))
         {
            $test_arr  = explode('-', $str[2]->from_date);
            
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
              
                $from_date=$str[2]->from_date;
            }
         }
         if(isset($str[3]->to_date) && !empty($str[2]->to_date))
         {
            $test_arr  = explode('-', $str[3]->to_date);
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
                $to_date=$str[3]->to_date;
            }
         }
         if(isset($str[5]->date_rng) && !empty($str[5]->date_rng))
         {
            if($str[5]->date_rng=='today')
            {
              $from_date=date('Y-m-d');
              $to_date=date('Y-m-d');
            }
            if($str[5]->date_rng=='7 days')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-d',strtotime("-7 days"));
            }
            if($str[5]->date_rng=='30 days')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-d',strtotime("-30 days"));
            }
            if($str[5]->date_rng=='this month')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-01');
            }
            if($str[5]->date_rng=='last month')
            {
              $to_date= date('Y-m-d', strtotime('last day of last month'));
              $from_date =  date('Y-m-d', strtotime('first day of last month'));
            }
         }


         $sqlcount="SELECT count(*) as total from amz_order_info as trx
                    WHERE customer_id={$this->user_id}";
         $sqlquery= "SELECT seller_sku,asin,itm_title,order_no,DATE_FORMAT(purchase_date,'%Y-%m-%d') as purchase_date,buyer_name,DATE_FORMAT(calc_shipdate,'%Y-%m-%d') as calc_shipdate,DATE_FORMAT(calc_deliverydate,'%Y-%m-%d') as calc_deliverydate,
                      itm_qty AS no_of,order_status,order_tfmstatus as tfm_status,itm_qty as no_of_item ,
                      buyer_email,shipping_country,shipping_state,shipping_zip,shipping_city,shipping_addr1,itm_price,itm_ship_price
                      FROM amz_order_info  as tx
                      WHERE customer_id={$this->user_id}";

        if(!empty($status))
        {
          $sqlquery.= " AND order_status = '".$status."'"; 
          $sqlcount.= " AND order_status = '".$status."'"; 
        }
        if(!empty($tf_status))
        {
          $sqlquery.= " AND order_tfmstatus LIKE '%".$tf_status."%'"; 
          $sqlcount.= " AND order_tfmstatus LIKE '%".$tf_status."%'"; 
        }
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (tx.seller_sku LIKE '%".$srterm."%' OR order_no LIKE '%".$srterm."%' OR buyer_name LIKE '%".$srterm."%' OR tx.itm_title LIKE '%".$srterm."%'   ) "; 
          $sqlcount.=" AND (trx.seller_sku LIKE '%".$srterm."%' OR order_no LIKE '%".$srterm."%' OR buyer_name LIKE '%".$srterm."%' OR trx.itm_title LIKE '%".$srterm."%'   ) "; 
        }
        if(!empty($from_date))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  purchase_date >= ".$this->db->escape($from_date.' 00:00:00')." AND purchase_date <=".$this->db->escape($to_date.' 23:59:00');
          $sqlcount.=" AND  purchase_date >= ".$this->db->escape($from_date.' 00:00:00')." AND purchase_date <=".$this->db->escape($to_date.' 23:59:00');
        }

        $sqlquery.=" ORDER BY ".$orderby." ".$direction." LIMIT ".$offet.",".$limit;
        

        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        $countquery=$this->db->query($sqlcount);
        
        $numrows= $countquery->result_array();
        if(count($data) > 0)
        {
        $result_set=array('status_code'=>'1','status_text'=>'successfully reterived','total' =>$numrows[0]['total'], 'datalist' => $data ,'searchterm' => $searchterm );
        }
        else
        {
         $result_set=array('status_code'=>'0','status_text'=>'No data found'); 
        }
        return $result_set;
    }
    public function get_order_info_by_id($order_no)
    {
      $query=$this->db->query("SELECT market_code,order_no,order_status,exp_ship_date as exp_ship,deliver_by_date as exp_deliver,
                              purchase_date as order_date,buyer_name,buyer_email,(no_of_itm_shipped+no_of_itm_unshipped) AS no_of_item,order_status ,
                              shipping_addr1,shipping_addr2,shipping_country,shipping_city,shipping_state,shipping_zip
                               FROM order_transaction_list 
                               where ssg_tn=".$this->db->escape($ssg_tn)." AND customer_id={$this->user_id}");
      return $query->result_array();
    }
    public function get_order_items($ssg_tn)
    {
      $query=$this->db->query("SELECT itm.ssg_tn AS ssg_tn,asin,seller_sku,itm_price,itm_ship_price,itm_tax,itm_title,itm_quantity
                               FROM order_items_transaction_list as itm
                               INNER JOIN order_transaction_list as tnx ON tnx.ssg_tn=itm.ssg_tn
                               where tnx.ssg_tn=".$this->db->escape($ssg_tn)." AND tnx.customer_id={$this->user_id}");
      
      return $query->result_array();
    }

 
}