<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orders_fullfill_shipment_report_list extends CI_Controller {
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
       $this->load->model('Orders_fullfill_shipment_report_list_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/orders_fullfill_shipment_report_list');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='ssg_tn',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->Orders_fullfill_shipment_report_list_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->Orders_fullfill_shipment_report_list_model->export_data($searchterm);
	 
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status." ";
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		if($status=='xls') 
		{
        fputcsv($feed_file, array('Order No','Order Item No','SKU','Title','Shipping ID','Shipping Item ID','Purchase Date','Payment Date','Shipping Date','Reporting Date','Delivery Date','Buyer Name','Buyer Email','Shipping Address1','Shipping Address2','Shipping Address3','Shipping City','Shipping State','Shipping Zip','Shipping Country','Tracking Number','Order Quantity','Item Price','Item Tax','Shipping Price','Shipping Tax','Gift Price','Gift Tax','Fullfillment'),"\t");
        foreach($product as $prd)
        {
          
		  $data=array($prd['order_no'],$prd['order_item_no'],$prd['ord_sku'],$prd['ord_title'],$prd['ship_id'],$prd['ship_itm_id'],$prd['purchase_date'],$prd['payment_date'],$prd['calc_shipdate'],$prd['report_date'],$prd['calc_deliverydate'],$prd['buyer_name'],$prd['buyer_email'],$prd['shipping_addr1'],$prd['shipping_addr2'],$prd['shipping_addr3'],$prd['shipping_city'],$prd['shipping_state'],$prd['shipping_zip'],$prd['shipping_country'],$prd['tracking_number'],$prd['ord_qty'],$prd['ord_itm_price'],$prd['ord_itm_tax'],$prd['ord_ship_price'],$prd['ord_ship_tax'],$prd['ord_gift_price'],$prd['ord_gift_tax'],$prd['ord_fullfill']);
          $array = array_map("utf8_decode", $data);          
		  fputcsv($feed_file,$array,"\t");
        }
		}
		if($status=='txt') 
		{
        fputcsv($feed_file, array('Order No','Order Item No','SKU','Title','Shipping ID','Shipping Item ID','Purchase Date','Payment Date','Shipping Date','Reporting Date','Delivery Date','Buyer Name','Buyer Email','Shipping Address1','Shipping Address2','Shipping Address3','Shipping City','Shipping State','Shipping Zip','Shipping Country','Tracking Number','Order Quantity','Item Price','Item Tax','Shipping Price','Shipping Tax','Gift Price','Gift Tax','Fullfillment'),"\t");
        foreach($product as $prd)
        {
         
          $data=array($prd['order_no'],$prd['order_item_no'],$prd['ord_sku'],$prd['ord_title'],$prd['ship_id'],$prd['ship_itm_id'],$prd['purchase_date'],$prd['payment_date'],$prd['calc_shipdate'],$prd['report_date'],$prd['calc_deliverydate'],$prd['buyer_name'],$prd['buyer_email'],$prd['shipping_addr1'],$prd['shipping_addr2'],$prd['shipping_addr3'],$prd['shipping_city'],$prd['shipping_state'],$prd['shipping_zip'],$prd['shipping_country'],$prd['tracking_number'],$prd['ord_qty'],$prd['ord_itm_price'],$prd['ord_itm_tax'],$prd['ord_ship_price'],$prd['ord_ship_tax'],$prd['ord_gift_price'],$prd['ord_gift_tax'],$prd['ord_fullfill']);
          $array = array_map("utf8_decode", $data);          
		  fputcsv($feed_file,$array,"\t");
        }
		}
		if($status=='csv') 
		{
        fputcsv($feed_file, array('Order No','Order Item No','SKU','Title','Shipping ID','Shipping Item ID','Purchase Date','Payment Date','Shipping Date','Reporting Date','Delivery Date','Buyer Name','Buyer Email','Shipping Address1','Shipping Address2','Shipping Address3','Shipping City','Shipping State','Shipping Zip','Shipping Country','Tracking Number','Order Quantity','Item Price','Item Tax','Shipping Price','Shipping Tax','Gift Price','Gift Tax','Fullfillment'));
        foreach($product as $prd)
        {
		  
          $data=array($prd['order_no'],$prd['order_item_no'],$prd['ord_sku'],$prd['ord_title'],$prd['ship_id'],$prd['ship_itm_id'],$prd['purchase_date'],$prd['payment_date'],$prd['calc_shipdate'],$prd['report_date'],$prd['calc_deliverydate'],$prd['buyer_name'],$prd['buyer_email'],$prd['shipping_addr1'],$prd['shipping_addr2'],$prd['shipping_addr3'],$prd['shipping_city'],$prd['shipping_state'],$prd['shipping_zip'],$prd['shipping_country'],$prd['tracking_number'],$prd['ord_qty'],$prd['ord_itm_price'],$prd['ord_itm_tax'],$prd['ord_ship_price'],$prd['ord_ship_tax'],$prd['ord_gift_price'],$prd['ord_gift_tax'],$prd['ord_fullfill']);
          $array = array_map("utf8_decode", $data);
		  fputcsv($feed_file,$array);
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
          $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."Orders_Fullfill_shipment_report_list/download/".$hash_name;
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
