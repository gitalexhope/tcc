<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Order_finance_data_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='fin_id',$direction='DESC',$offet,$limit,$searchterm='')
    {
         $srterm='';
         $status='';
		  $cnt='';
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));
            
            $srterm=urldecode($str[0]->searchtext);
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
         $sqlcount="SELECT count(*) as total from finance_data WHERE added_by={$this->user_id} and sku<>'' ";
         $sqlquery= "SELECT * FROM finance_data  WHERE added_by={$this->user_id} and sku<>''";

        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' OR itemid LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (order_id LIKE '%".$srterm."%' OR itemid LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
        }
		  if(isset($str[2]->country_status) && $str[2]->country_status!='ALL')
        {
        	$sqlquery.=" AND fin_country = ".$this->db->escape($str[2]->country_status);
            $sqlcount.=" AND fin_country = ".$this->db->escape($str[2]->country_status);
        }
		
        $sqlquery.=" ORDER BY ".$orderby." ".$direction." LIMIT ".$offet.",".$limit;
          // die($sqlquery);
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
         $sqlcount="SELECT count(*) as total from finance_data WHERE added_by={$this->user_id}";
         $sqlquery= "SELECT * FROM finance_data  WHERE added_by={$this->user_id} and sku<>''";

         if(isset($str[2]->country_status) && $str[2]->country_status!='ALL')
        {
        	$sqlquery.=" AND fin_country = ".$this->db->escape($str[2]->country_status);
            $sqlcount.=" AND fin_country = ".$this->db->escape($str[2]->country_status);
        }
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (order_id LIKE '%".$srterm."%' OR itemid LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
        }
		 if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.=" AND (marketplace LIKE '%".$cnt."%') "; 
        }
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

      public function get_source_list()
     {
     	$qry=$this->db->query("SELECT fin_country FROM  finance_data group by fin_country");
     	return $qry->result_array();
     	
     }

  }
?>
