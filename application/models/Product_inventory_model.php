<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Product_inventory_model extends CI_Model
{
	  public function  __construct()
	  {
	   	 parent::__construct();
       $user=$this->session->userdata('user_logged_in');  
       $this->user_id=$user['id'];
	  }
    public function get_inventory_list($orderby='added_on',$direction='DESC',$offet,$limit,$searchterm='')
    {
         $srterm='';
         $status='';
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));
            
            $srterm=urldecode($str[0]->searchtext);
         }

         $sort_order='added_on';
         if($orderby=='added_on')
         {
          $sort_order='added_on';
         } 
         $sqlcount="SELECT count(*) as total from product_info WHERE process_flag=1 and pro_title <>''";
                    
         $sqlquery= "SELECT pro_id,pro_asin,pro_title,pro_image,pro_brand,pro_category,pro_model,pro_manufacturer,pro_part_num,pro_rank,pro_pack_qty,pro_curr_code,pro_itm_weight,pro_p_height,pro_p_length,pro_p_width,pro_p_weight,pro_no_of_fba_offers,(pro_min_fba_price+pro_min_fba_ship_price) AS pro_min_fba_price,(pro_max_fba_price+pro_max_fba_ship_price) AS pro_max_fba_price,
(pro_avg_fba_price+pro_avg_fba_ship_price) AS pro_avg_fba_price,(pro_bb_price+pro_bb_ship_price) AS pro_bb_price,IF(pro_bb_offer_type='true','FBA',IF(pro_bb_offer_type='false','FBM','')) AS pro_bb_offer_type FROM product_info WHERE process_flag=1  and pro_title <>''";

	

        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (pro_asin LIKE '%".$srterm."%' OR pro_brand LIKE '%".$srterm."%' OR pro_category LIKE '%".$srterm."%'  ) "; 
          $sqlcount.=" AND (pro_asin LIKE '%".$srterm."%' OR pro_brand LIKE '%".$srterm."%' OR pro_category LIKE '%".$srterm."%'  ) "; 
        }

        $sqlquery.=" ORDER BY ".$sort_order." ".$direction." LIMIT ".$offet.",".$limit;

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
        
		
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));
            $srterm=urldecode($str[0]->searchtext);
         }
       $sqlcount="SELECT count(*) as total from product_info WHERE process_flag=1 and pro_title <>''";
       $sqlquery= "SELECT pro_id,pro_asin,pro_title,pro_image,pro_brand,pro_category,pro_model,pro_manufacturer,pro_part_num,pro_rank,pro_pack_qty,pro_curr_code,pro_itm_weight,pro_p_height,pro_p_length,pro_p_width,pro_p_weight,pro_no_of_fba_offers,(pro_min_fba_price+pro_min_fba_ship_price) AS pro_min_fba_price,(pro_max_fba_price+pro_max_fba_ship_price) AS pro_max_fba_price,
                  (pro_avg_fba_price+pro_avg_fba_ship_price) AS pro_avg_fba_price,(pro_bb_price+pro_bb_ship_price) AS pro_bb_price,IF(pro_bb_offer_type='true','FBA',IF(pro_bb_offer_type='false','FBM','')) AS pro_bb_offer_type FROM product_info WHERE process_flag=1 and pro_title <>''";

	
        if(!empty($srterm) || $srterm !='')
        {
          $sqlquery.=" AND (pro_asin LIKE '%".$srterm."%' OR pro_brand LIKE '%".$srterm."%' OR pro_category LIKE '%".$srterm."%'  ) "; 
        }
		 
		
        $query=$this->db->query($sqlquery) ;
        $data= $query->result_array();
        
        return $data;
    }

	public function import_data($file_name)
   {
       $pqr=$this->db->query("SELECT count(pro_id) as ttl FROM product_info");
       $prd=$pqr->result_array();
	   ini_set('auto_detect_line_endings',TRUE);
          $dir="./import_data/";
          $fp=fopen($dir.$file_name, "r");
          $i=0;
          $bulk_data=[];
          while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) 
          {
			  if($i >0)
			{
           $asin=isset($data[0])?$data[0]:"";
		      if(!empty($asin))
              $bulk_data[]="(".$this->db->escape($asin).",".$this->db->escape($this->user_id).")";
              if(count($bulk_data) == 500 )
              {
                $quer=implode(",",$bulk_data);
                $qi="INSERT IGNORE INTO product_info(`pro_asin`,`pro_user`) VALUES {$quer}";
                $this->db->query($qi);
                unset($bulk_data);
                  unset($quer);
              }
			} 
            $i++;
          }
          if(isset($bulk_data) && count($bulk_data) > 0 && count($bulk_data) < 500)
          {
                $quer=implode(",",$bulk_data);
                $qi="INSERT IGNORE INTO product_info(`pro_asin`,`pro_user`) VALUES {$quer}";
                $this->db->query($qi);
                unset($bulk_data);
                unset($quer);
          }
        $aqr=$this->db->query("SELECT count(pro_id) as ttl FROM product_info");
            $ard=$aqr->result_array();
            $import_count=$ard[0]['ttl']-$prd[0]['ttl']; 
             $res['status_code']=1;
            $res['status_text']='A total of '.$import_count."  ASINs have been imported.";
            return $res;
    }
public function total_imported()
    {
      $sql= "SELECT COUNT(*) AS ttl FROM product_info where is_processed=0";
      $query=$this->db->query($sql);
      return $query->result_array();                
    }
    public function total_processed()
    {
      $sql= "SELECT count(*) as ttl from product_info where process_flag=1 and fees_flag=1 and feedback_flag=1";
      $query=$this->db->query($sql);
      return $query->result_array();                
    }


    
 
}