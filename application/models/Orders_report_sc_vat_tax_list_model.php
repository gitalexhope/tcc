<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Orders_report_sc_vat_tax_list_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='rep_id',$direction='DESC',$offet,$limit,$searchterm='')
    {
         $srterm='';
         $status='';
		 $itm_status='';
		 $cnt='';
         $date_filter='ord_date';
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
         $from_date=date('Y-m-d', strtotime('-150 days'));
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
          if(isset($str[6]->date_filter))
         {
           if($str[6]->date_filter == 'ORD')
           $date_filter='ord_date';
           elseif($str[6]->date_filter == 'SHIP')
           $date_filter='ship_date';
           elseif($str[6]->date_filter == 'TAX')
           $date_filter='tax_cal_date'; 
		 }
		 
		
         $sqlcount="SELECT count(*) as total from rep_sc_vat_tax WHERE user_id={$this->user_id}";
         $sqlquery= "SELECT market_id,merchant_id,ord_date,trans_type,order_id,ship_date,ship_id,trans_id,asin,sku,qty,tax_cal_date,tax_rate,tax_code 
,currency,tax_type,tax_cal_rsn_code,tax_addr_role,juri_level,juri_country,our_price_tax_inclusive,our_price_tax,our_price_tax_exclusive,
our_promo_amount_inclusive,our_promo_amount,our_promo_amount_exclusive,ship_tax_inclusive,ship_tax ,ship_tax_exclusive,ship_tax_promo_inclusive
,ship_tax_promo,ship_tax_promo_exclusive,gift_tax_inclusive,gift_tax ,gift_tax_exclusive,gift_tax_promo_inclusive,gift_tax_promo,gift_tax_promo_exclusive,
sell_tax_reg,sell_tax_reg_jud,buy_tax_reg,buy_tax_reg_jud ,buy_tax_reg_type,inv_curr_code,inv_ex_rate,inv_ex_date,con_tax_amt,vat_inv_no,inv_url,exp_out_eu,ship_from_city,ship_to_city,ship_from_state,ship_to_state
,ship_from_country,ship_to_country ,ship_from_postal,ship_to_postal,ship_from_tax_loca ,ship_to_tax_loca,user_id                      
 FROM rep_sc_vat_tax WHERE user_id={$this->user_id}";

        
        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (order_id LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
        }
		  if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND market_id = '".$cnt."'"; 
          $sqlcount.= " AND market_id = '".$cnt."'"; 		  
        }
		if(!empty($from_date) || !empty($date_filter))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  ".$date_filter." >= ".$this->db->escape($from_date.' 00:00:00')." AND ".$date_filter." <=".$this->db->escape($to_date.' 23:59:00');
          $sqlcount.=" AND  ".$date_filter." >= ".$this->db->escape($from_date.' 00:00:00')." AND ".$date_filter." <=".$this->db->escape($to_date.' 23:59:00');
        } 
        $sqlquery.=" ORDER BY ".$orderby." ".$direction." LIMIT ".$offet.",".$limit;
        //print_r($date_filter);
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
          $date_filter='ord_date';
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
          if(isset($str[6]->date_filter))
         {
           if($str[6]->date_filter == 'ORD')
           $date_filter='ord_date';
           elseif($str[6]->date_filter == 'SHIP')
           $date_filter='ship_date';
           elseif($str[6]->date_filter == 'TAX')
           $date_filter='tax_cal_date'; 
		 }
		 
         $sqlcount="SELECT count(*) as total from rep_sc_vat_tax WHERE user_id={$this->user_id}";
         $sqlquery= "SELECT market_id,merchant_id,ord_date,trans_type,order_id,ship_date,ship_id,trans_id,asin,sku,qty,tax_cal_date,tax_rate,tax_code 
,currency,tax_type,tax_cal_rsn_code,tax_addr_role,juri_level,juri_country,our_price_tax_inclusive,our_price_tax,our_price_tax_exclusive,
our_promo_amount_inclusive,our_promo_amount,our_promo_amount_exclusive,ship_tax_inclusive,ship_tax ,ship_tax_exclusive,ship_tax_promo_inclusive
,ship_tax_promo,ship_tax_promo_exclusive,gift_tax_inclusive,gift_tax ,gift_tax_exclusive,gift_tax_promo_inclusive,gift_tax_promo,gift_tax_promo_exclusive,
sell_tax_reg,sell_tax_reg_jud,buy_tax_reg,buy_tax_reg_jud ,buy_tax_reg_type,inv_curr_code,inv_ex_rate,inv_ex_date,con_tax_amt,vat_inv_no,inv_url,exp_out_eu,ship_from_city,ship_to_city,ship_from_state,ship_to_state
,ship_from_country,ship_to_country ,ship_from_postal,ship_to_postal,ship_from_tax_loca ,ship_to_tax_loca,user_id                      
 FROM rep_sc_vat_tax WHERE user_id={$this->user_id}";
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
         
        }
		 if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND market_id = '".$cnt."'"; 
        }
		if(!empty($from_date) || !empty($date_filter))
        {
          $from_date=$from_date." 00:00:00";
          $to_date=$to_date." 23:59:59";

          $sqlquery.=" AND  ".$date_filter." >= ".$this->db->escape($from_date.' 00:00:00')." AND ".$date_filter." <=".$this->db->escape($to_date.' 23:59:00');
        }   
       
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
