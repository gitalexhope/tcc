<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rep_vat_transaction_data_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='vat_id',$direction='DESC',$offet,$limit,$searchterm='')
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
           $cnt='CO.UK'; 
         }

         $from_date='';
         // $from_date="";
         $to_date='';
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
         $sqlcount="SELECT count(*) as total from rep_vat_transaction_data WHERE user_id={$this->user_id}";
         $sqlquery= "SELECT unique_acc_identifier,activity_period,sales_channel,REPLACE(country,'amazon.','') AS country,trans_type,trans_event_id,activity_trans_id,IF(tax_cal_date='1970-01-01','',tax_cal_date) as tax_cal_date,IF(trans_depart_date='1970-01-01','',trans_depart_date) as trans_depart_date,IF(trans_arraival_date='1970-01-01','',trans_arraival_date) AS trans_arraival_date,IF(trans_compile_date='1970-01-01','',trans_compile_date)  as trans_compile_date,seller_sku,prod_asin,description,qty,itm_weight,total_weight_activity,cost_price_of_items,price_of_items_amt_vat_excl,promo_price_of_items_amt_vat_excl,total_price_of_items_amt_vat_excl,ship_charge_amt_vat_excl,promo_ship_charge_amt_vat_excl,total_ship_charge_amt_vat_excl,gift_wrap_amt_vat_excl,promo_gift_wrap_amt_vat_excl,total_gift_wrap_amt_vat_excl,total_activity_value_amt_vat_excl,price_of_items_vat_rate_percent,price_of_items_vat_amt,promo_price_of_items_vat_amt,total_price_of_items_vat_amt,ship_charge_vat_rate_percent,ship_charge_vat_amt,promo_ship_charge_vat_amt,total_ship_charge_vat_amt,gift_wrap_vat_rate_percent,gift_wrap_vat_amt,promo_gift_wrap_vat_amt,total_gift_wrap_vat_amt,total_activity_value_vat_amt,price_of_items_amt_vat_incl,promo_price_of_items_amt_vat_incl,total_price_of_items_amt_vat_incl,ship_charge_amt_vat_incl,promo_ship_charge_amt_vat_incl,total_ship_charge_amt_vat_incl,gift_wrap_amt_vat_incl,promo_gift_wrap_amt_vat_incl,total_gift_wrap_amt_vat_incl,total_activity_value_amt_vat_incl,transaction_currency_code,commodity_code,statistical_code_depart,statistical_code_arrival,commodity_code_supplementary_unit,item_qty_supplementary_unit,total_activity_supplementary_unit,product_tax_code,depature_city,departure_country,departure_post_code,arrival_city,arrival_country,arrival_post_code,sale_depart_country,sale_arrival_country,transportation_mode,delivery_conditions,seller_depart_vat_number_country,seller_depart_country_vat_number,seller_arrival_vat_number_country,seller_arrival_country_vat_number,transaction_seller_vat_number_country,transaction_seller_vat_number,buyer_vat_number_country,buyer_vat_number,vat_calculation_imputation_country,taxable_jurisdiction,taxable_jurisdiction_level,vat_inv_number,vat_inv_converted_amt,vat_inv_currency_code,vat_inv_exchange_rate,vat_inv_exchange_rate_date,export_outside_eu,invoice_url,buyer_name,arrival_address,user_id FROM rep_vat_transaction_data  WHERE user_id={$this->user_id}";

        
        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (trans_event_id LIKE '%".$srterm."%' OR prod_asin LIKE '%".$srterm."%' OR seller_sku LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (trans_event_id LIKE '%".$srterm."%' OR prod_asin LIKE '%".$srterm."%' OR seller_sku LIKE '%".$srterm."%') "; 
        }
		  if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND REPLACE(country,'amazon.','') = '".$cnt."'"; 
          $sqlcount.= " AND REPLACE(country,'amazon.','') = '".$cnt."'"; 		  
        }
       if(!empty($from_date))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  trans_compile_date >= ".$this->db->escape($from_date.' 00:00:00')." AND trans_compile_date <=".$this->db->escape($to_date.' 23:59:00');
          $sqlcount.=" AND  trans_compile_date >= ".$this->db->escape($from_date.' 00:00:00')." AND trans_compile_date <=".$this->db->escape($to_date.' 23:59:00');
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
           $cnt='CO.UK'; 
         }
          $from_date=date('Y-m-d', strtotime('-60 days'));
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
        
         $sqlcount="SELECT count(*) as total from rep_vat_transaction_data WHERE user_id={$this->user_id}";
         $sqlquery= "SELECT unique_acc_identifier,activity_period,sales_channel,REPLACE(country,'amazon.','') AS country,trans_type,trans_event_id,activity_trans_id,IF(tax_cal_date='1970-01-01','',tax_cal_date) as tax_cal_date,IF(trans_depart_date='1970-01-01','',trans_depart_date) as trans_depart_date,IF(trans_arraival_date='1970-01-01','',trans_arraival_date) AS trans_arraival_date,IF(trans_compile_date='1970-01-01','',trans_compile_date)  as trans_compile_date,seller_sku,prod_asin,description,qty,itm_weight,total_weight_activity,cost_price_of_items,price_of_items_amt_vat_excl,promo_price_of_items_amt_vat_excl,total_price_of_items_amt_vat_excl,ship_charge_amt_vat_excl,promo_ship_charge_amt_vat_excl,total_ship_charge_amt_vat_excl,gift_wrap_amt_vat_excl,promo_gift_wrap_amt_vat_excl,total_gift_wrap_amt_vat_excl,total_activity_value_amt_vat_excl,price_of_items_vat_rate_percent,price_of_items_vat_amt,promo_price_of_items_vat_amt,total_price_of_items_vat_amt,ship_charge_vat_rate_percent,ship_charge_vat_amt,promo_ship_charge_vat_amt,total_ship_charge_vat_amt,gift_wrap_vat_rate_percent,gift_wrap_vat_amt,promo_gift_wrap_vat_amt,total_gift_wrap_vat_amt,total_activity_value_vat_amt,price_of_items_amt_vat_incl,promo_price_of_items_amt_vat_incl,total_price_of_items_amt_vat_incl,ship_charge_amt_vat_incl,promo_ship_charge_amt_vat_incl,total_ship_charge_amt_vat_incl,gift_wrap_amt_vat_incl,promo_gift_wrap_amt_vat_incl,total_gift_wrap_amt_vat_incl,total_activity_value_amt_vat_incl,transaction_currency_code,commodity_code,statistical_code_depart,statistical_code_arrival,commodity_code_supplementary_unit,item_qty_supplementary_unit,total_activity_supplementary_unit,product_tax_code,depature_city,departure_country,departure_post_code,arrival_city,arrival_country,arrival_post_code,sale_depart_country,sale_arrival_country,transportation_mode,delivery_conditions,seller_depart_vat_number_country,seller_depart_country_vat_number,seller_arrival_vat_number_country,seller_arrival_country_vat_number,transaction_seller_vat_number_country,transaction_seller_vat_number,buyer_vat_number_country,buyer_vat_number,vat_calculation_imputation_country,taxable_jurisdiction,taxable_jurisdiction_level,vat_inv_number,vat_inv_converted_amt,vat_inv_currency_code,vat_inv_exchange_rate,vat_inv_exchange_rate_date,export_outside_eu,invoice_url,buyer_name,arrival_address,user_id FROM rep_vat_transaction_data  WHERE user_id={$this->user_id}";

        
        
        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (trans_event_id LIKE '%".$srterm."%' OR prod_asin LIKE '%".$srterm."%' OR seller_sku LIKE '%".$srterm."%') "; 
        }
		  if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND REPLACE(country,'amazon.','') = '".$cnt."'"; 
        }
		if(!empty($from_date))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  trans_compile_date >= ".$this->db->escape($from_date.' 00:00:00')." AND trans_compile_date <=".$this->db->escape($to_date.' 23:59:00');
        } 
		  

       //die($sqlquery);		
		  
       
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
