<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Orders_report_actionable_data_list_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='po_date',$direction='DESC',$offet,$limit,$searchterm='')
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
		// print_r($cnt);
         $sqlcount="SELECT count(*) as total from rep_actionable_order_data_list WHERE usr_id={$this->user_id}";
         $sqlquery= "SELECT order_id,order_item_id,DATE_FORMAT(po_date,'%Y-%m-%d') AS po_date,DATE_FORMAT(pay_date,'%Y-%m-%d %H-%i-%s') AS pay_date,DATE_FORMAT(rep_date,'%Y-%m-%d %H-%i-%s') AS rep_date,DATE_FORMAT(prom_date,'%Y-%m-%d %H-%i-%s') AS prom_date,day_past,buy_email,buy_name,buy_phone,sku,prod_name,qty_pur,qty_ship,qty_unship,ship_addr1,ship_addr2,ship_addr3,ship_city,ship_state,ship_post,ship_country,country  FROM rep_actionable_order_data_list WHERE usr_id={$this->user_id}";

        
        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' OR order_item_id LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (order_id LIKE '%".$srterm."%' OR order_item_id LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
        }
		  if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND country = '".$cnt."'"; 
          $sqlcount.= " AND country = '".$cnt."'"; 		  
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
         $sqlcount="SELECT count(*) as total from rep_actionable_order_data_list WHERE usr_id={$this->user_id}";
         $sqlquery= "SELECT order_id,order_item_id,DATE_FORMAT(po_date,'%Y-%m-%d %H-%i-%s') AS po_date,DATE_FORMAT(pay_date,'%Y-%m-%d %H-%i-%s') AS pay_date,DATE_FORMAT(rep_date,'%Y-%m-%d %H-%i-%s') AS rep_date,DATE_FORMAT(prom_date,'%Y-%m-%d %H-%i-%s') AS prom_date,day_past,buy_email,buy_name,buy_phone,sku,prod_name,qty_pur,qty_ship,qty_unship,ship_addr1,ship_addr2,ship_addr3,ship_city,ship_state,ship_post,ship_country,country  FROM rep_actionable_order_data_list WHERE usr_id={$this->user_id}";

        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' OR order_item_id LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
        }
		  if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND country = '".$cnt."'"; 
        }
		if(!empty($from_date))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  po_date >= ".$this->db->escape($from_date.' 00:00:00')." AND po_date <=".$this->db->escape($to_date.' 23:59:00');
        } 
       
       
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
