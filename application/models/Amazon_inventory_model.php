<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Amazon_inventory_model extends CI_Model
{
	  public function  __construct()
	  {
	   	 parent::__construct();
       $user=$this->session->userdata('user_logged_in');  
       $this->user_id=$user['id'];
    }
    
    public function get_order_list($asin,$sku,$orderby='purchase_date',$direction='DESC',$offet,$limit,$searchterm='')
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
         // $from_date=date('Y-m-d', strtotime('-7 days'));
         $from_date="";
         $to_date=date('Y-m-d ');
         if(isset($str[2]->from_date))
         {
            $test_arr  = explode('-', $str[2]->from_date);
            
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) 
            {
              
                $from_date=$str[2]->from_date;
            }
         }
         if(isset($str[3]->to_date))
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
                    where customer_id={$this->user_id} AND asin=".$this->db->escape($asin);
                    
         $sqlquery= "SELECT seller_sku,asin,itm_title,order_no,DATE_FORMAT(purchase_date,'%Y-%m-%d') as purchase_date,buyer_name,
                      (no_of_itm_shipped+no_of_itm_unshipped) AS no_of_item,order_status,order_tfmstatus as tfm_status,itm_qty,
                      buyer_email,shipping_country,shipping_state,shipping_zip,shipping_city,shipping_addr1,itm_price,itm_ship_price
                      FROM amz_order_info  as tx
                      WHERE  customer_id={$this->user_id} AND asin=".$this->db->escape($asin);
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
          $sqlquery.=" AND ( order_no LIKE '%".$srterm."%' OR buyer_name LIKE '%".$srterm."%'  ) "; 
          $sqlcount.=" AND ( order_no LIKE '%".$srterm."%' OR buyer_name LIKE '%".$srterm."%'  ) "; 
        }
        if(!empty($from_date))
        {
          $sqlquery.=" AND  purchase_date >= ".$this->db->escape($from_date.' 00:00:00')." AND purchase_date <=".$this->db->escape($to_date.' 23:59:00');
          $sqlcount.=" AND  purchase_date >= ".$this->db->escape($from_date.' 00:00:00')." AND purchase_date <=".$this->db->escape($to_date.' 23:59:00');
        }

        $sqlquery.=" ORDER BY purchase_date ".$direction." LIMIT ".$offet.",".$limit;
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

	  
    public function get_inventory_list($orderby,$direction,$offet,$limit,$searchterm='')
    {
         $srterm='';
         $status='';
		 $cnt="";
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));
            $srterm=urldecode($str[0]->searchtext);
         }
         $sort_order='';
         if($orderby=='qty')
         {
          $sort_order='itm_qty';
         }
         elseif($orderby=='price')
         {
          $sort_order='itm_price';
         }
         elseif($orderby=='sold')
         {
          $sort_order='sold_qty';
         }
         elseif($orderby=='profit')
         {
          $sort_order='total_profit';
         }
         if(empty($direction))
         $direction='DESC';
         $sqlcount="SELECT count(*) as total from customer_product as prd
                                            
                      where is_active IS NOT NULL AND added_by={$this->user_id}";
         $sqlquery= "SELECT prod_image,prod_brand,prod_title,prod_asin,prod_country,fc_code,is_active,fullfillment_type,is_deleted,prod_sku,itm_qty,itm_condition,prd.itm_price,DATE_FORMAT(open_date,'%Y-%m-%d') AS open_date,act_price,profit
                      FROM customer_product AS prd
                                          WHERE  is_active IS NOT NULL AND added_by={$this->user_id}";

        if(isset($str[1]->list_status))
         {
           if($str[1]->list_status == 'ACT')
           {
              $sqlquery.=" AND is_active=1 ";
              $sqlcount.=" AND is_active=1 ";
           }
           elseif($str[1]->list_status == 'INAC')
           {
              $sqlquery.=" AND is_active=0 ";
              $sqlcount.=" AND is_active=0 ";
           }
           elseif($str[1]->list_status == 'DEL')
           {
              $sqlquery.=" AND is_active=-1 ";
              $sqlcount.=" AND is_active=-1 ";
           }
           
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
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (prod_title LIKE '%".$srterm."%' OR prod_asin LIKE '%".$srterm."%' OR prod_sku LIKE '%".$srterm."%'  ) "; 
          $sqlcount.=" AND (prod_title LIKE '%".$srterm."%' OR prod_asin LIKE '%".$srterm."%' OR prod_sku LIKE '%".$srterm."%'  ) "; 
        }
		if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND prod_country = '".$cnt."'"; 
        }
        //$sqlcount.=" GROUP BY prod_sku";
        $sqlquery.="  ORDER BY is_active DESC ";
		 
        if(!empty($sort_order))
          $sqlquery.=",".$sort_order." ".$direction;
        $sqlquery.=" LIMIT ".$offet.",".$limit;
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
		 $cnt="";
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));
            $srterm=urldecode($str[0]->searchtext);
         }
         
        
         $sqlcount="SELECT count(*) as total from customer_product as trx
                    WHERE is_active IS NOT NULL AND added_by={$this->user_id}";
          $sqlquery= "SELECT prod_image,prod_brand,prod_title,prod_asin,prod_country,fc_code,is_active,fullfillment_type,is_deleted,prod_sku,itm_qty,itm_condition,prd.itm_price,DATE_FORMAT(open_date,'%Y-%m-%d') AS open_date,act_price,profit
                      FROM customer_product AS prd
                                           
                      where is_active IS NOT NULL AND added_by={$this->user_id}";
        if(isset($str[1]->list_status))
         {
           if($str[1]->list_status == 'ACT')
           {
              $sqlquery.=" AND is_active=1 ";
              $sqlcount.=" AND is_active=1 ";
           }
           elseif($str[1]->list_status == 'INAC')
           {
              $sqlquery.=" AND is_active=0 ";
              $sqlcount.=" AND is_active=0 ";
           }
           elseif($str[1]->list_status == 'DEL')
           {
              $sqlquery.=" AND is_active=-1 ";
              $sqlcount.=" AND is_active=-1 ";
           }
           
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
        if(!empty($srterm) || $srterm !='')
        {
        $sqlquery.=" AND (prod_title LIKE '%".$srterm."%' OR prod_asin LIKE '%".$srterm."%' OR prod_sku LIKE '%".$srterm."%'  ) "; 
        }
		if(!empty($cnt) || $cnt !='')
        {
        $sqlquery.= " AND prod_country = '".$cnt."'"; 
        }

        $sqlquery.=" ORDER BY is_active DESC ";
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        return $data;
        
    }
	
    
 
}