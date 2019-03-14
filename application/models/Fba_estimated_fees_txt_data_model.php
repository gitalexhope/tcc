<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Fba_estimated_fees_txt_data_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();
				$user=$this->session->userdata('user_logged_in');  
                $this->user_id=$user['id'];
		       	
	   	  }
		
	 public function get_product_list($orderby='asin',$direction='DESC',$offet,$limit,$searchterm='')
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
		  

		// print_r($cnt);
         $sqlcount="SELECT count(*) as total from fba_estimated_fees_txt_data WHERE user_id={$this->user_id} ";
         $sqlquery= "SELECT * FROM fba_estimated_fees_txt_data  WHERE user_id={$this->user_id} ";

        
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (product_group LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
          $sqlcount.=" AND (product_group LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
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
         $sqlcount="SELECT count(*) as total from fba_estimated_fees_txt_data WHERE user_id={$this->user_id} ";
         $sqlquery= "SELECT * FROM fba_estimated_fees_txt_data  WHERE user_id={$this->user_id} ";
        
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
      	 $sqlquery.=" AND (product_group LIKE '%".$srterm."%' OR asin LIKE '%".$srterm."%' OR sku LIKE '%".$srterm."%') "; 
        }
		
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

 

  }
?>
