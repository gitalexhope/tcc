<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Inactive_inventory_report_model extends CI_Model
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
		   elseif($str[2]->country_status == 'US')
           $cnt='US';
         }
		  
		// print_r($cnt);
         $sqlcount="SELECT count(*) as total from inactive_inventory_data WHERE added_by={$this->user_id} ";
         $sqlquery= "SELECT * FROM inactive_inventory_data  WHERE added_by={$this->user_id} ";

        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (product_id LIKE '%".$srterm."%' OR seller_sku LIKE '%".$srterm."%' OR asin1 LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (product_id LIKE '%".$srterm."%' OR seller_sku LIKE '%".$srterm."%' OR asin1 LIKE '%".$srterm."%') "; 
        }
		if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND country = '".$cnt."'"; 
          $sqlcount.= " AND country = '".$cnt."'"; 		  
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
         $sqlcount="SELECT count(*) as total from inactive_inventory_data WHERE added_by={$this->user_id} ";
         $sqlquery= "SELECT * FROM inactive_inventory_data  WHERE added_by={$this->user_id} ";

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
		   elseif($str[2]->country_status == 'US')
           $cnt='US';
         }
        if(!empty($srterm) || $srterm !='')
        {
         $sqlquery.=" AND (fulfillment_center LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR fnsku LIKE '%".$srterm."%') "; 

        }
		if(!empty($cnt) || $cnt !='')
        {
          $sqlquery.= " AND country = '".$cnt."'"; 
        }
		
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
