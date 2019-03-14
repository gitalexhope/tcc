<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Orders_fullfill_shipment_report_list_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='ssg_tn',$direction='DESC',$offet,$limit,$searchterm='')
    {
         $srterm='';
         $status='';
		  $cnt='';
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));
            
            $srterm=urldecode($str[0]->searchtext);
         } 
        
		 if(isset($str[2]->country_status))
         {
           if($str[2]->country_status == 'IT')
           $cnt='IT';
           elseif($str[2]->country_status == 'FR')
           $cnt='FR';
           elseif($str[2]->country_status == 'DE')
           $cnt='DE'; 
		   elseif($str[2]->country_status == 'ES')
           $cnt='ES'; 
		   elseif($str[2]->country_status == 'UK')
           $cnt='UK'; 
         }
		 $from_date=date('Y-m-d', strtotime('-30 days'));
         // $from_date="";
         $to_date=date('Y-m-d ');
         if(isset($str[3]->from_date) && !empty($str[3]->from_date))
         {
            $test_arr  = explode('-', $str[3]->from_date);
            
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
              
                $from_date=$str[3]->from_date;
            }
         }
         if(isset($str[4]->to_date) && !empty($str[4]->to_date))
         {
            $test_arr  = explode('-', $str[4]->to_date);
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
                $to_date=$str[4]->to_date;
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
		//print_r($cnt);
         $sqlcount="SELECT count(*) as total from rep_amz_fullfill_list WHERE customer_id={$this->user_id}";
         $sqlquery= "SELECT order_no,order_item_no,ord_sku,ship_id,ship_itm_id,DATE_FORMAT(purchase_date,'%Y-%m-%d %H-%i-%s') as purchase_date,DATE_FORMAT(payment_date,'%Y-%m-%d %H-%i-%s') as payment_date,DATE_FORMAT(calc_shipdate,'%Y-%m-%d %H-%i-%s') as calc_shipdate,DATE_FORMAT(calc_deliverydate,'%Y-%m-%d %H-%i-%s') as calc_deliverydate,DATE_FORMAT(report_date,'%Y-%m-%d %H-%i-%s') as report_date,ord_title,ord_itm_price,ord_ship_price,ord_qty,buyer_name,buyer_email,shipping_addr1,shipping_addr2,shipping_addr3,shipping_city,shipping_state,shipping_country,shipping_zip,sales_country,tracking_number,ord_itm_price,ord_itm_tax,ord_ship_tax,ord_gift_price,ord_gift_tax  FROM rep_amz_fullfill_list  WHERE customer_id={$this->user_id}";

        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_no LIKE '%".$srterm."%' OR order_item_no LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (order_no LIKE '%".$srterm."%' OR order_item_no LIKE '%".$srterm."%') "; 
        }
		  if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.=" AND (sales_country LIKE '%".$cnt."%') "; 
          $sqlcount.=" AND (sales_country LIKE '%".$cnt."%') "; 
        }
		if(!empty($from_date))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  purchase_date >= ".$this->db->escape($from_date.' 00:00:00')." AND purchase_date <=".$this->db->escape($to_date.' 23:59:00');
          $sqlcount.=" AND  purchase_date >= ".$this->db->escape($from_date.' 00:00:00')." AND purchase_date <=".$this->db->escape($to_date.' 23:59:00');
        }

        $sqlquery.=" ORDER BY ".$orderby." ".$direction." LIMIT ".$offet.",".$limit;
        //die($sqlquery);
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
	
	public function export_data($searchterm='')
    {
         $srterm='';
         $cnt='';
		
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));
            $srterm=urldecode($str[0]->searchtext);
         }
		  if(isset($str[2]->country_status))
         {
           if($str[2]->country_status == 'IT')
           $cnt='IT';
           elseif($str[2]->country_status == 'FR')
           $cnt='FR';
           elseif($str[2]->country_status == 'DE')
           $cnt='DE'; 
		   elseif($str[2]->country_status == 'ES')
           $cnt='ES'; 
		   elseif($str[2]->country_status == 'UK')
           $cnt='UK'; 
         }
		 $from_date=date('Y-m-d', strtotime('-30 days'));
         // $from_date="";
         $to_date=date('Y-m-d ');
		 if(isset($str[3]->from_date) && !empty($str[3]->from_date))
         {
            $test_arr  = explode('-', $str[3]->from_date);
            
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
              
                $from_date=$str[3]->from_date;
            }
         }
         if(isset($str[4]->to_date) && !empty($str[4]->to_date))
         {
            $test_arr  = explode('-', $str[4]->to_date);
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
                $to_date=$str[4]->to_date;
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
		 
         $sqlcount="SELECT count(*) as total from rep_amz_fullfill_list WHERE customer_id={$this->user_id}";
         $sqlquery= "SELECT order_no,order_item_no,ord_sku,ship_id,ship_itm_id,DATE_FORMAT(purchase_date,'%Y-%m-%d %H-%i-%s') as purchase_date,DATE_FORMAT(payment_date,'%Y-%m-%d %H-%i-%s') as payment_date,DATE_FORMAT(calc_shipdate,'%Y-%m-%d %H-%i-%s') as calc_shipdate,DATE_FORMAT(calc_deliverydate,'%Y-%m-%d %H-%i-%s') as calc_deliverydate,DATE_FORMAT(report_date,'%Y-%m-%d %H-%i-%s') as report_date,ord_title,ord_itm_price,ord_ship_price,ord_qty,buyer_name,buyer_email,shipping_addr1,shipping_addr2,shipping_addr3,shipping_city,shipping_state,shipping_country,shipping_zip,sales_country,tracking_number,ord_itm_price,ord_itm_tax,ord_ship_tax,ord_gift_price,ord_gift_tax,ord_fullfill  FROM rep_amz_fullfill_list  WHERE customer_id={$this->user_id}";

        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_no LIKE '%".$srterm."%' OR order_item_no LIKE '%".$srterm."%') "; 
          
        }
		if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.=" AND (sales_country LIKE '%".$cnt."%') "; 
        }
		if(!empty($from_date))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  purchase_date >= ".$this->db->escape($from_date)." AND purchase_date <=".$this->db->escape($to_date);
        }
       //die($sqlquery);
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
