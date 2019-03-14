<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Rep_settlement_data_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='order_id',$direction='DESC',$offet,$limit,$searchterm='')
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
		 
		// print_r($cnt);
         $sqlcount="SELECT COUNT(DISTINCT(order_id)) as total FROM amz_settlement_data WHERE settlement_for={$this->user_id}";
         
		 $sqlquery= " SELECT stl.order_id,stl.fulfilment_id,SUM(stl.price_amt) AS sale_amt,ABS(SUM(stl.itm_related_fee_amount)+SUM(stl.other_amt)) AS old_fee_amt,SUM(stl.price_amt)+(SUM(stl.itm_related_fee_amount)+SUM(stl.other_amt)) AS old_net_stl_amt,
         ABS(MAX(CASE WHEN stl.itm_related_fee_type='Commission' THEN stl.itm_related_fee_amount END)) AS stl_com_fee,
        
         ABS(MAX(CASE WHEN stl.itm_related_fee_type='Cost of Advertising ' THEN stl.itm_related_fee_amount END)) AS stl_cost_of_adv,
         
         ABS(MAX(CASE WHEN stl.itm_related_fee_type='FBAPerUnitFulfillmentFee' THEN stl.itm_related_fee_amount END)) AS stl_fbau_fee,
         
          ABS(MAX(CASE WHEN stl.itm_related_fee_type='FBAWeightBasedFee' THEN stl.itm_related_fee_amount END)) AS stl_fbaw_fee,
          
          ABS(MAX(CASE WHEN stl.itm_related_fee_type='RefundCommission' THEN stl.itm_related_fee_amount END)) AS stl_ref_fee,
         
          ABS(MAX(CASE WHEN stl.itm_related_fee_type='ShippingHB' THEN stl.itm_related_fee_amount END)) AS stl_shiphb_fee,
          
          ABS(MAX(CASE WHEN stl.itm_related_fee_type='ShippingChargeback' THEN stl.itm_related_fee_amount END)) AS stl_shipchar_fee,
          
          
         ABS(MAX(CASE WHEN stl.itm_related_fee_type='Commission' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Commission Tax' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Fixed closing fee' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Fixed closing fee Tax' THEN stl.itm_related_fee_amount END)) AS fee_amt,
         SUM(stl.price_amt)+MAX(CASE WHEN stl.itm_related_fee_type='Commission' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Commission Tax' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Fixed closing fee' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Fixed closing fee Tax' THEN stl.itm_related_fee_amount END) AS net_stl_amt,
         ABS(SUM(stl.other_amt)) AS easy_ship,
         MAX(CASE WHEN stl.price_type='Principal' THEN stl.price_amt END) AS stl_pricipal,
         
         MAX(CASE WHEN stl.price_type='Shipping' THEN stl.price_amt END) AS stl_ship_price,
         
         MAX(CASE WHEN stl.price_type='Tax' THEN stl.price_amt END) AS stl_tax,
        
         MAX(CASE WHEN stl.price_type='ShippingTax' THEN stl.price_amt END) AS stl_shiptax,
         
         MAX(CASE WHEN stl.price_type='MarketplaceFacilitatorTax-Principal' THEN stl.price_amt END) AS stl_ftax,
         
         MAX(CASE WHEN stl.price_type='MarketplaceFacilitatorTax-Shipping' THEN stl.price_amt END) AS stl_fstax,
         SUM(qty_purchased) AS qty_purchased,posted_date,REPLACE(market_place,'Amazon.','') AS market_place
          FROM amz_settlement_data  AS stl WHERE order_id NOT LIKE '' AND settlement_for={$this->user_id}";

        
        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' ) "; 
          $sqlcount.=" AND (order_id LIKE '%".$srterm."%' ) "; 
        }
		  
		 if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND REPLACE(market_place,'Amazon.','') = '".$cnt."'"; 
          $sqlcount.= " AND REPLACE(market_place,'Amazon.','') = '".$cnt."'"; 		  
        } 
        $sqlquery.=" GROUP BY order_id ORDER BY ".$orderby." ".$direction." LIMIT ".$offet.",".$limit;
        //die($sqlcount);
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
         $sqlcount="SELECT COUNT(DISTINCT(order_id)) as total FROM amz_settlement_data WHERE settlement_for={$this->user_id}";
         $sqlquery= " SELECT stl.order_id,stl.fulfilment_id,SUM(stl.price_amt) AS sale_amt,ABS(SUM(stl.itm_related_fee_amount)+SUM(stl.other_amt)) AS old_fee_amt,SUM(stl.price_amt)+(SUM(stl.itm_related_fee_amount)+SUM(stl.other_amt)) AS old_net_stl_amt,
         ABS(MAX(CASE WHEN stl.itm_related_fee_type='Commission' THEN stl.itm_related_fee_amount END)) AS stl_com_fee,
        
         ABS(MAX(CASE WHEN stl.itm_related_fee_type='Cost of Advertising ' THEN stl.itm_related_fee_amount END)) AS stl_cost_of_adv,
         
         ABS(MAX(CASE WHEN stl.itm_related_fee_type='FBAPerUnitFulfillmentFee' THEN stl.itm_related_fee_amount END)) AS stl_fbau_fee,
         
          ABS(MAX(CASE WHEN stl.itm_related_fee_type='FBAWeightBasedFee' THEN stl.itm_related_fee_amount END)) AS stl_fbaw_fee,
          
          ABS(MAX(CASE WHEN stl.itm_related_fee_type='RefundCommission' THEN stl.itm_related_fee_amount END)) AS stl_ref_fee,
         
          ABS(MAX(CASE WHEN stl.itm_related_fee_type='ShippingHB' THEN stl.itm_related_fee_amount END)) AS stl_shiphb_fee,
          
          ABS(MAX(CASE WHEN stl.itm_related_fee_type='ShippingChargeback' THEN stl.itm_related_fee_amount END)) AS stl_shipchar_fee,
          
          
         ABS(MAX(CASE WHEN stl.itm_related_fee_type='Commission' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Commission Tax' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Fixed closing fee' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Fixed closing fee Tax' THEN stl.itm_related_fee_amount END)) AS fee_amt,
         SUM(stl.price_amt)+MAX(CASE WHEN stl.itm_related_fee_type='Commission' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Commission Tax' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Fixed closing fee' THEN stl.itm_related_fee_amount END) + MAX(CASE WHEN stl.itm_related_fee_type='Fixed closing fee Tax' THEN stl.itm_related_fee_amount END) AS net_stl_amt,
         ABS(SUM(stl.other_amt)) AS easy_ship,
         MAX(CASE WHEN stl.price_type='Principal' THEN stl.price_amt END) AS stl_pricipal,
         
         MAX(CASE WHEN stl.price_type='Shipping' THEN stl.price_amt END) AS stl_ship_price,
         
         MAX(CASE WHEN stl.price_type='Tax' THEN stl.price_amt END) AS stl_tax,
        
         MAX(CASE WHEN stl.price_type='ShippingTax' THEN stl.price_amt END) AS stl_shiptax,
         
         MAX(CASE WHEN stl.price_type='MarketplaceFacilitatorTax-Principal' THEN stl.price_amt END) AS stl_ftax,
         
         MAX(CASE WHEN stl.price_type='MarketplaceFacilitatorTax-Shipping' THEN stl.price_amt END) AS stl_fstax,
         SUM(qty_purchased) AS qty_purchased,posted_date,REPLACE(market_place,'Amazon.','') AS market_place
          FROM amz_settlement_data  AS stl WHERE order_id NOT LIKE '' AND settlement_for={$this->user_id}";

        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%') "; 
        }
		if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND REPLACE(market_place,'Amazon.','') = '".$cnt."'"; 
        }  

       //die($sqlquery);		
		$sqlquery.=" GROUP BY order_id ";
 
       
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
