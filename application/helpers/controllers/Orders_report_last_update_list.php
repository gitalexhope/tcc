<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orders_report_last_update_list extends CI_Controller {
  public function  __construct()
  {
     parent::__construct();
     $this->load->model('login_model');
     if(!$this->login_model->userLoginCheck() && !$this->input->is_ajax_request())
     {
      redirect('user_auth');
     }
     else
     {
       $user=$this->session->userdata('user_logged_in');  
       $this->user_id=$user['id'];
       $this->load->model('Orders_report_last_update_list_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/orders_report_last_update_list');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='prod_id',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->Orders_report_last_update_list_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
      echo json_encode($result_set);
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
		
      $product=$this->Orders_report_last_update_list_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status." ";
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array('Order ID','Merchant Order ID','SKU','ASIN','Title','Purchase Date','Last Update Date','Order Status','Fullfillment','Item Status','Quantity','Item Price','Item Tax','Shipping Price','Shipping Tax','Gift Price','Gift Tax','Item Promotional Discount','Shipping Promotional Discount','Promotional ID','Shipping City','Shipping State','Shipping Post','Shipping country'));
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['mer_order_id'],$prd['ord_sku'],$prd['asin'],$prd['title'],$prd['po_date'],$prd['last_update_date'],$prd['ord_status'],$prd['fillfillment'],$prd['itm_status'],$prd['qty'],$prd['itm_price'],$prd['itm_tax'],$prd['ship_price'],$prd['ship_tax'],$prd['gift_price'],$prd['gift_tax'],$prd['itm_promo_discount'],$prd['itm_ship_discount'],$prd['promo_id'],$prd['ship_city'],$prd['ship_state'],$prd['ship_post'],$prd['ship_country']);
          fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file, array('Order ID','Merchant Order ID','SKU','ASIN','Title','Purchase Date','Last Update Date','Order Status','Fullfillment','Item Status','Quantity','Item Price','Item Tax','Shipping Price','Shipping Tax','Gift Price','Gift Tax','Item Promotional Discount','Shipping Promotional Discount','Promotional ID','Shipping City','Shipping State','Shipping Post','Shipping country'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['mer_order_id'],$prd['ord_sku'],$prd['asin'],$prd['title'],$prd['po_date'],$prd['last_update_date'],$prd['ord_status'],$prd['fillfillment'],$prd['itm_status'],$prd['qty'],$prd['itm_price'],$prd['itm_tax'],$prd['ship_price'],$prd['ship_tax'],$prd['gift_price'],$prd['gift_tax'],$prd['itm_promo_discount'],$prd['itm_ship_discount'],$prd['promo_id'],$prd['ship_city'],$prd['ship_state'],$prd['ship_post'],$prd['ship_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file, array('Order ID','Merchant Order ID','SKU','ASIN','Title','Purchase Date','Last Update Date','Order Status','Fullfillment','Item Status','Quantity','Item Price','Item Tax','Shipping Price','Shipping Tax','Gift Price','Gift Tax','Item Promotional Discount','Shipping Promotional Discount','Promotional ID','Shipping City','Shipping State','Shipping Post','Shipping country'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['mer_order_id'],$prd['ord_sku'],$prd['asin'],$prd['title'],$prd['po_date'],$prd['last_update_date'],$prd['ord_status'],$prd['fillfillment'],$prd['itm_status'],$prd['qty'],$prd['itm_price'],$prd['itm_tax'],$prd['ship_price'],$prd['ship_tax'],$prd['gift_price'],$prd['gift_tax'],$prd['itm_promo_discount'],$prd['itm_ship_discount'],$prd['promo_id'],$prd['ship_city'],$prd['ship_state'],$prd['ship_post'],$prd['ship_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
          $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."Orders_report_last_update_list/download/".$hash_name;
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
