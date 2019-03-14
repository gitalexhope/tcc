<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Orders_report_orderby_date_list_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='prod_id',$direction='DESC',$offet,$limit,$searchterm='')
    {
         $srterm='';
         $status='';
		 $itm_status='';
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
		 if(isset($str[3]->ord_status))
         {
           if($str[3]->ord_status == 'SHI')
           $status='Shipped';
           elseif($str[3]->ord_status == 'UNS')
           $status='Unshipped';
           elseif($str[3]->ord_status == 'CAN')
           $status='Canceled'; 
           elseif($str[3]->ord_status == 'PEN')
           $status='Pending'; 
         }
		 if(isset($str[4]->itm_status))
         {
           if($str[4]->itm_status == 'SHI')
           $itm_status='Shipped';
           elseif($str[4]->itm_status == 'UNS')
           $itm_status='Unshipped';
           elseif($str[4]->itm_status == 'CAN')
           $itm_status='Cancelled'; 
           elseif($str[4]->itm_status == 'PEN')
           $itm_status='Pending'; 
         }
		 $from_date=date('Y-m-d', strtotime('-30 days'));
         // $from_date="";
         $to_date=date('Y-m-d ');
         if(isset($str[5]->from_date) && !empty($str[5]->from_date))
         {
            $test_arr  = explode('-', $str[5]->from_date);
            
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
              
                $from_date=$str[5]->from_date;
            }
         }
         if(isset($str[6]->to_date) && !empty($str[6]->to_date))
         {
            $test_arr  = explode('-', $str[6]->to_date);
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
                $to_date=$str[6]->to_date;
            }
         }
         if(isset($str[7]->date_rng) && !empty($str[7]->date_rng))
         {
            if($str[7]->date_rng=='today')
            {
              $from_date=date('Y-m-d');
              $to_date=date('Y-m-d');
            }
            if($str[7]->date_rng=='7 days')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-d',strtotime("-7 days"));
            }
            if($str[7]->date_rng=='30 days')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-d',strtotime("-30 days"));
            }
            if($str[7]->date_rng=='this month')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-01');
            }
            if($str[7]->date_rng=='last month')
            {
              $to_date= date('Y-m-d', strtotime('last day of last month'));
              $from_date =  date('Y-m-d', strtotime('first day of last month'));
            }
         }
		// print_r($cnt);
         $sqlcount="SELECT count(*) as total from rep_orders_data_order_date_list WHERE user_id={$this->user_id}";
         $sqlquery= "SELECT order_id,DATE_FORMAT(po_date,'%Y-%m-%d %H-%i-%s') AS po_date,DATE_FORMAT(last_update_date,'%Y-%m-%d %H-%i-%s') AS last_update_date,ord_status,fulfillment,REPLACE(itm_status,'Cancelled','Canceled') AS itm_status,ord_sku,asin,title,qty,sales_channel,itm_price,itm_tax,ship_price,ship_tax,gift_price,gift_tax,itm_promo_discount,itm_ship_discount,ship_city,ship_state,ship_country,ship_post,promo_id  FROM rep_orders_data_order_date_list  WHERE user_id={$this->user_id}";

        
        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR ord_sku LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (order_id LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR ord_sku LIKE '%".$srterm."%') "; 
        }
		  if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND sales_channel = '".$cnt."'"; 
          $sqlcount.= " AND sales_channel = '".$cnt."'"; 		  
        }
		  if(!empty($status))
        {
          $sqlquery.= " AND ord_status = '".$status."'"; 
          $sqlcount.= " AND ord_status = '".$status."'"; 
        }
        if(!empty($itm_status))
        {
          $sqlquery.= " AND itm_status = '".$itm_status."'"; 
          $sqlcount.= " AND itm_status = '".$itm_status."'"; 
        }
		if(!empty($from_date))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  po_date >= ".$this->db->escape($from_date.' 00:00:00')." AND po_date <=".$this->db->escape($to_date.' 23:59:00');
          $sqlcount.=" AND  po_date >= ".$this->db->escape($from_date.' 00:00:00')." AND po_date <=".$this->db->escape($to_date.' 23:59:00');
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
         $status='';
		 $itm_status='';
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
		 if(isset($str[3]->ord_status))
         {
           if($str[3]->ord_status == 'SHI')
           $status='Shipped';
           elseif($str[3]->ord_status == 'UNS')
           $status='Unshipped';
           elseif($str[3]->ord_status == 'CAN')
           $status='Canceled'; 
           elseif($str[3]->ord_status == 'PEN')
           $status='Pending'; 
         }
		 if(isset($str[4]->itm_status))
         {
           if($str[4]->itm_status == 'SHI')
           $itm_status='Shipped';
           elseif($str[4]->itm_status == 'UNS')
           $itm_status='Unshipped';
           elseif($str[4]->itm_status == 'CAN')
           $itm_status='Cancelled'; 
           elseif($str[4]->itm_status == 'PEN')
           $itm_status='Pending'; 
         }
		 $from_date=date('Y-m-d', strtotime('-30 days'));
         // $from_date="";
         $to_date=date('Y-m-d ');
         if(isset($str[5]->from_date) && !empty($str[5]->from_date))
         {
            $test_arr  = explode('-', $str[5]->from_date);
            
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
              
                $from_date=$str[5]->from_date;
            }
         }
         if(isset($str[6]->to_date) && !empty($str[6]->to_date))
         {
            $test_arr  = explode('-', $str[6]->to_date);
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
                $to_date=$str[6]->to_date;
            }
         }
         if(isset($str[7]->date_rng) && !empty($str[7]->date_rng))
         {
            if($str[7]->date_rng=='today')
            {
              $from_date=date('Y-m-d');
              $to_date=date('Y-m-d');
            }
            if($str[7]->date_rng=='7 days')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-d',strtotime("-7 days"));
            }
            if($str[7]->date_rng=='30 days')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-d',strtotime("-30 days"));
            }
            if($str[7]->date_rng=='this month')
            {
              $to_date=date('Y-m-d');
              $from_date = date('Y-m-01');
            }
            if($str[7]->date_rng=='last month')
            {
              $to_date= date('Y-m-d', strtotime('last day of last month'));
              $from_date =  date('Y-m-d', strtotime('first day of last month'));
            }
         }
         $sqlcount="SELECT count(*) as total from rep_orders_data_order_date_list WHERE user_id={$this->user_id}";
         $sqlquery= "SELECT order_id,DATE_FORMAT(po_date,'%Y-%m-%d %H-%i-%s') AS po_date,DATE_FORMAT(last_update_date,'%Y-%m-%d %H-%i-%s') AS last_update_date,ord_status,fulfillment,itm_status,ord_sku,asin,title,qty,sales_channel,itm_price,itm_tax,ship_price,ship_tax,gift_price,gift_tax,itm_promo_discount,itm_ship_discount,ship_city,ship_state,ship_country,ship_post,promo_id  FROM rep_orders_data_order_date_list  WHERE user_id={$this->user_id}";

        
         if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR ord_sku LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (order_id LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR ord_sku LIKE '%".$srterm."%') "; 
        }
		  if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND sales_channel = '".$cnt."'"; 
          $sqlcount.= " AND sales_channel = '".$cnt."'"; 		  
        }
		  if(!empty($status))
        {
          $sqlquery.= " AND ord_status = '".$status."'"; 
          $sqlcount.= " AND ord_status = '".$status."'"; 
        }
        if(!empty($itm_status))
        {
          $sqlquery.= " AND itm_status = '".$itm_status."'"; 
          $sqlcount.= " AND itm_status = '".$itm_status."'"; 
        }
		if(!empty($from_date))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  po_date >= ".$this->db->escape($from_date.' 00:00:00')." AND po_date <=".$this->db->escape($to_date.' 23:59:00');
        }
        // die($sqlquery);
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
