<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Subscription_fee_data_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='posted_date',$direction='DESC',$offet,$limit,$searchterm='')
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
         $sqlcount="SELECT COUNT(*) as total FROM amz_settlement_data WHERE transaction_type='Subscription Fee' and settlement_for={$this->user_id} GROUP BY settlement_id";
         
		 $sqlquery= "SELECT settlement_id,transaction_type,posted_date,other_amt FROM amz_settlement_data  WHERE  transaction_type='Subscription Fee' and settlement_for={$this->user_id}";

        
        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (settlement_id LIKE '%".$srterm."%' ) "; 
          $sqlcount.=" AND (settlement_id LIKE '%".$srterm."%' ) "; 
        }
		  
		
        $sqlquery.=" GROUP BY settlement_id ORDER BY ".$orderby." ".$direction." LIMIT ".$offet.",".$limit;
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
          $sqlcount="SELECT COUNT(*) as total FROM amz_settlement_data WHERE transaction_type='Subscription Fee' and settlement_for={$this->user_id} GROUP BY settlement_id";
         
		 $sqlquery= "SELECT settlement_id,transaction_type,posted_date,other_amt FROM amz_settlement_data  WHERE  transaction_type='Subscription Fee' and settlement_for={$this->user_id}";

        
        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (settlement_id LIKE '%".$srterm."%' ) "; 
        }
		  

       //die($sqlquery);		
		$sqlquery.=" GROUP BY settlement_id  order by posted_date desc";
 
       
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
