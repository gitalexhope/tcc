<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Fba_inventory_health_data_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='snap_date',$direction='DESC',$offet,$limit,$searchterm='')
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
           $cnt='GB'; 
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
         $sqlcount="SELECT count(*) as total from rep_fba_inv_health_data WHERE added_by={$this->user_id} ";
         $sqlquery= "SELECT snap_date,sku,fn_sku,asin,prod_name,prod_cond,sales_rank,prod_group,total_qty,sell_qty,unsell_qty,inv_age_0_to_90,inv_age_91_to_180,inv_age_181_to_270,inv_age_271_to_365,inv_age_365_plus,unit_ship_24_hrs,unit_ship_7_days,unit_ship_30_days,unit_ship_90_days,unit_ship_180_days,unit_ship_365_days,weeks_of_cover_t7,weeks_of_cover_t30,weeks_of_cover_t90,weeks_of_cover_t180,weeks_of_cover_t365,num_afn_new_sellers,num_afn_user_sellers,curr,your_price,sale_price,low_afn_new_price,low_afn_used_price,low_mfn_new_price,low_mfn_used_price,qty_charged_12,qty_charger_long_term,qty_removal_in_progress,projected_12,per_unit_vol,is_hazmat,in_bound_qty,asin_limit,inbound_recomm_qty,qty_charged_6,projected_6 
 FROM rep_fba_inv_health_data  WHERE added_by={$this->user_id} ";

        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (sku LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (sku LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%') "; 
        }
		 
		
        $sqlquery.=" ORDER BY ".$orderby." ".$direction." LIMIT ".$offet.",".$limit;
          //  die($sqlquery);
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
         $sqlcount="SELECT count(*) as total from rep_fba_inv_health_data WHERE added_by={$this->user_id} ";
         $sqlquery= "SELECT snap_date,sku,fn_sku,asin,prod_name,prod_cond,sales_rank,prod_group,total_qty,sell_qty,unsell_qty,inv_age_0_to_90,inv_age_91_to_180,inv_age_181_to_270,inv_age_271_to_365,inv_age_365_plus,unit_ship_24_hrs,unit_ship_7_days,unit_ship_30_days,unit_ship_90_days,unit_ship_180_days,unit_ship_365_days,weeks_of_cover_t7,weeks_of_cover_t30,weeks_of_cover_t90,weeks_of_cover_t180,weeks_of_cover_t365,num_afn_new_sellers,num_afn_user_sellers,curr,your_price,sale_price,low_afn_new_price,low_afn_used_price,low_mfn_new_price,low_mfn_used_price,qty_charged_12,qty_charger_long_term,qty_removal_in_progress,projected_12,per_unit_vol,is_hazmat,in_bound_qty,asin_limit,inbound_recomm_qty,qty_charged_6,projected_6 
 FROM rep_fba_inv_health_data  WHERE added_by={$this->user_id} ";

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
           $cnt='GB'; 
         }
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (sku LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (sku LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%') "; 
        }
		 
		
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
