<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_list extends CI_Controller {
  private $user_id;
  public function  __construct()
  {
       parent::__construct();
      if(!$this->login_model->userLoginCheck())
      {
        redirect('user_auth');
      }
      else
      {
        $this->load->model('product_inventory_model','product_model');   
        $user=$this->session->userdata('user_logged_in');  
        $this->user_id=$user['id'];
       
      }
       
  }

  public function index()
  {
    $this->load->view('UI/header');
    $this->load->view('UI/product_list');
    $this->load->view('UI/footer');
  }
  public function get_product_list($orderby='added_on',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->product_model->get_inventory_list($orderby,$direction,$offet,$limit,$searchterm);
      echo json_encode($result_set);
  }
  
 public function import_data()
     {
      $this->load->library('upload');

      if(!empty($_FILES['import_file']['tmp_name']))
      {
          $main=$this->upload_import_file('import_file','./import_data/');
          if($main )
          {
            $res=$this->product_model->import_data($main);
            echo json_encode($res);
          }
          else
          {
            echo '{"status_code":"0","status_text":"Not able to upload import file"}';    
          }
      }
      else
      {
        echo '{"status_code":"0","status_text":"Not able to upload import file"}';
      }
  }
  function upload_import_file($image,$folder)
     {
           $config['upload_path'] = $folder;
           $config['allowed_types'] = 'csv';
           $config['file_name']=strtoupper(md5(microtime()).mt_rand()); 
           $config['max_size']  = '0';
           $config['max_width']  = '0';
           $config['max_height']  = '0';
           $this->upload->initialize($config);
           if( ! $this->upload->do_upload($image))
           {  
              echo '{"status_code":"0","status_text":"'.$this->upload->display_errors().'"}';
              die();
           }
           else
           {
               $img=$this->upload->data();
               return $img['file_name'];; 
           }
     }


     public function remove_data()
     {
        $this->db->query("DELETE FROM  product_info WHERE pro_user={$this->user_id}");
        //$this->db->query("TRUNCATE imported_asin");        
        echo '{"status_code":"1","status_text":"All data has been removed."}';    
     }
public function get_pre_data()
  {
    $data['status_text']='Success';
    $data['status_code']='1';
    $data['total_imported']=$this->product_model->total_imported();
    $data['total_processed']=$this->product_model->total_processed();
    echo json_encode($data);
  }

  public function cron_run()
  {
	 $query=$this->db->query("SELECT pro_asin  FROM product_info where upc_flag=0 OR upc_flag is null  limit 0,600000");
	 $prod_list=$query->result_array();;
         if(!empty($prod_list))
         {
	$trigger="start /B C:\\xampp\php\php C:\\xampp\htdocs\integrity\index.php cron Amazon_mws_product_api product_match 2>nul >nul";
    pclose(popen($trigger, "r"));
    $trigger1="start /B C:\\xampp\php\php C:\\xampp\htdocs\integrity\index.php cron Amazon_mws_lowestprice_api product_match 2>nul >nul";
    pclose(popen($trigger1, "r"));	
    $trigger2="start /B C:\\xampp\php\php C:\\xampp\htdocs\integrity\index.php cron Amazon_mws_competitive_asin_api product_match 2>nul >nul";
    pclose(popen($trigger2, "r"));
	$trigger3="start /B C:\\xampp\php\php C:\\xampp\htdocs\integrity\index.php cron Amazon_mws_myfeesestimate_api product_match 2>nul >nul";
    pclose(popen($trigger3, "r"));
	$trigger4="start /B C:\\xampp\php\php  c:\\xampp\htdocs\integrity\amz_upc_process.php 2>nul >nul";
    pclose(popen($trigger4, "r"));
	$data['status_text']="Sucess";
    echo '{"status_code":"1","status_text":"Your data is now being processed."}'; 
      
  }
  else{
	  echo '{"status_code":"0","status_text":"No input provided."}'; 
  }
}
public function add_extra_keyword_to_asin($keyword='')
  {
      
	  
      if(empty($_POST['keyword']) || strlen($_POST['keyword']) <= 0)
      {
      	 $data['status_code']="0";
      	 $data['status_text']="ASINs Are Empty";
      	 echo json_encode($data);
         die();	
      }
      $pqr=$this->db->query("SELECT count(pro_id) as ttl FROM product_info");
      $prd=$pqr->result_array();
      $res=[];
      
      $delimit= "\n";  
      $key_list=array_unique(explode($delimit,$this->input->post('keyword')));
      foreach($key_list as $key)
      {
        if(!empty($key))
        {
          
          $res[]=array('pro_asin'=>$key,'pro_user'=>$this->user_id);    
        }
        
      }
      
      $this->db->insert_ignore_batch('product_info',$res);
	   $aqr=$this->db->query("SELECT count(pro_id) as ttl FROM product_info");
            $ard=$aqr->result_array();
            $import_count=$ard[0]['ttl']-$prd[0]['ttl']; 
             $data['status_code']=1;
            $data['status_text']='A total of '.$import_count."  ASINs have been imported.";
      
      echo json_encode($data);
  }
  
  
  public function export_data($searchterm='')
  {
	  
	      $srterm='';
         $status='';
         if($searchterm !='')
         {
            $str=json_decode(urldecode($searchterm));
            $srterm=urldecode($str[0]->searchtext);
         }

         if(isset($str[1]->order_status))
         {
           if($str[1]->order_status == 'CSV')
           $status='csv';
           elseif($str[1]->order_status == 'TXT')
           $status='txt';
           elseif($str[1]->order_status == 'XLS')
           $status='xls'; 
         }
		
      $product=$this->product_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array('ASIN','Title','Image URL','Brand','Category','Model','Manufacturer','Part Number','Current BSR','Product Weight','Package Quantity','Package Height','Package Length','Package Width','Package Weigth','Currency Code','No Of FBA Offers','Minimum FBA Offer','Maximum FBA Offer','Average FBA Offer','Current Buy Box Price','Current Buy Box Offer Type'));
        foreach($product as $prd)
        {
                                                                                                                     
		 $data=array($prd['pro_asin'],$prd['pro_title'],$prd['pro_image'],$prd['pro_brand'],$prd['pro_category'],$prd['pro_model'],$prd['pro_manufacturer'],$prd['pro_part_num'],$prd['pro_rank'],$prd['pro_itm_weight'],$prd['pro_pack_qty'],$prd['pro_p_height'],$prd['pro_p_length'],$prd['pro_p_width'],$prd['pro_p_weight'],$prd['pro_curr_code'],$prd['pro_no_of_fba_offers'],$prd['pro_min_fba_price'],$prd['pro_max_fba_price'],$prd['pro_avg_fba_price'],$prd['pro_bb_price'],$prd['pro_bb_offer_type']);
          fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file,array('ASIN','Title','Image URL','Brand','Category','Model','Manufacturer','Part Number','Current BSR','Product Weight','Package Quantity','Product Height','Package Length','Package Width','Package Weigth','Currency Code','No Of FBA Offers','Minimum FBA Offer','Maximum FBA Offer','Average FBA Offer','Current Buy Box Price','Current Buy Box Offer Type'),"\t");
        foreach($product as $prd)
        {
		 $data=array($prd['pro_asin'],$prd['pro_title'],$prd['pro_image'],$prd['pro_brand'],$prd['pro_category'],$prd['pro_model'],$prd['pro_manufacturer'],$prd['pro_part_num'],$prd['pro_rank'],$prd['pro_itm_weight'],$prd['pro_pack_qty'],$prd['pro_p_height'],$prd['pro_p_length'],$prd['pro_p_width'],$prd['pro_p_weight'],$prd['pro_curr_code'],$prd['pro_no_of_fba_offers'],$prd['pro_min_fba_price'],$prd['pro_max_fba_price'],$prd['pro_avg_fba_price'],$prd['pro_bb_price'],$prd['pro_bb_offer_type']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt')
		{
        fputcsv($feed_file, array('ASIN','Title','Image URL','Brand','Category','Model','Manufacturer','Part Number','Current BSR','Product Weight','Product Weight','Package Quantity','Package Height','Package Length','Package Width','Package Weigth','Currency Code','No Of FBA Offers','Minimum FBA Offer','Maximum FBA Offer','Average FBA Offer','Current Buy Box Price','Current Buy Box Offer Type'),"\t");
        foreach($product as $prd)
        {
		 $data=array($prd['pro_asin'],$prd['pro_title'],$prd['pro_image'],$prd['pro_brand'],$prd['pro_category'],$prd['pro_model'],$prd['pro_manufacturer'],$prd['pro_part_num'],$prd['pro_rank'],$prd['pro_itm_weight'],$prd['pro_pack_qty'],$prd['pro_p_height'],$prd['pro_p_length'],$prd['pro_p_width'],$prd['pro_p_weight'],$prd['pro_curr_code'],$prd['pro_no_of_fba_offers'],$prd['pro_min_fba_price'],$prd['pro_max_fba_price'],$prd['pro_avg_fba_price'],$prd['pro_bb_price'],$prd['pro_bb_offer_type']);
          fputcsv($feed_file,$data,"\t");
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
		  $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."product_list/download/".$hash_name;
          echo json_encode($data);
        }  
        else
        {
          echo  '{"status_code":"0","status_text":"Not able to create export file please try again"}';             
        }
      }
      else
      {
        echo  '{"status_code":"0","status_text":"There is no product found for this filter criteria"}';             
      }
      

  }
  public function download($file_name)
  {
     $abs_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$file_name;
     header('Content-Disposition: attachment; filename='.$file_name);
     header('Content-Description: File Transfer');
     header('Content-Type: text/csv');
     header('Content-Transfer-Encoding: binary');
     header('Expires: 0');
     header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
     header('Pragma: public'); 
     readfile($abs_path);
   }

 

}