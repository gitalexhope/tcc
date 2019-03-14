<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orders_report_list extends CI_Controller {
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
       $this->load->model('orders_report_list_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/orders_report_list');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='ord_id',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->orders_report_list_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->orders_report_list_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		if($status=='xls') 
		{
       fputcsv($feed_file, array('Order ID','Order Item ID','SKU','Purchase Date','Payment Date','Title','Price','Ship Price','Qty','Order Total','Buyer Name','Buyer Email','Shipping Address 1','Shipping Address 2','Shipping City','Shipping State','Shipping Zip','Shipping Country','Country'),"\t");
		

		foreach($product as $prd)
        {
         
          $data=array($prd['ord_id'],$prd['ord_itm_id'],$prd['sku'],$prd['po_date'],$prd['pay_date'],$prd['itm_name'],$prd['price'],$prd['ship_price'],$prd['qty'],$prd['order_total'],$prd['buyer_name'],$prd['buyer_email'],$prd['ship_addr1'],$prd['ship_addr2'],$prd['ship_city'],$prd['ship_state'],$prd['ship_zip'],$prd['ship_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt') 
		{
       fputcsv($feed_file, array('Order ID','Order Item ID','SKU','Purchase Date','Payment Date','Title','Price','Ship Price','Qty','Order Total','Buyer Name','Buyer Email','Shipping Address 1','Shipping Address 2','Shipping City','Shipping State','Shipping Zip','Shipping Country'),"\t");
		

		foreach($product as $prd)
        {
         
          $data=array($prd['ord_id'],$prd['ord_itm_id'],$prd['sku'],$prd['po_date'],$prd['pay_date'],$prd['itm_name'],$prd['price'],$prd['ship_price'],$prd['qty'],$prd['order_total'],$prd['buyer_name'],$prd['buyer_email'],$prd['ship_addr1'],$prd['ship_addr2'],$prd['ship_city'],$prd['ship_state'],$prd['ship_zip'],$prd['ship_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='csv') 
		{
       fputcsv($feed_file, array('Order ID','Order Item ID','SKU','Purchase Date','Payment Date','Title','Price','Ship Price','Qty','Order Total','Buyer Name','Buyer Email','Shipping Address 1','Shipping Address 2','Shipping City','Shipping State','Shipping Zip','Shipping Country'));
		

		foreach($product as $prd)
        {
         
          $data=array($prd['ord_id'],$prd['ord_itm_id'],$prd['sku'],$prd['po_date'],$prd['pay_date'],$prd['itm_name'],$prd['price'],$prd['ship_price'],$prd['qty'],$prd['order_total'],$prd['buyer_name'],$prd['buyer_email'],$prd['ship_addr1'],$prd['ship_addr2'],$prd['ship_city'],$prd['ship_state'],$prd['ship_zip'],$prd['ship_country']);
          fputcsv($feed_file,$data);
        }
		}
		      
 		  
		//elseif($status=='xls')
		//{
		//	fputcsv($feed_file, array("Order ID\tOrder Item ID\tSKU\tPurchase Date\tPayment Date\tTitle\tPrice\tShip Price\tQty\tOrder Total\tBuyer Name\tBuyer Email\tShipping Address 1\tShipping Address 2\tShipping City\tShipping State\tShipping Zip\tShipping Country\n "));
        //    
        //foreach($product as $prd)
        //{
        // 
        //  $data=array($prd['ord_id']."\t".$prd['ord_itm_id']."\t".$prd['sku']."\t".$prd['po_date']."\t".$prd['pay_date']."\t".$prd['itm_name']."\t".$prd['price']."\t".$prd['ship_price']."\t".$prd['qty']."\t".$prd['order_total']."\t".$prd['buyer_name']."\t".$prd['buyer_email']."\t".$prd['ship_addr1']."\t".$prd['ship_addr2']."\t".$prd['ship_city']."\t".$prd['ship_state']."\t".$prd['ship_zip']."\t".$prd['ship_country']."\n");
        // // print_r($data);
		//	//die();
		// fputcsv($feed_file,$data); 
        //}
		// 
		//}
		//elseif($status=='txt')
		//{
		//	$data="Order ID\tOrder Item ID\tSKU\tPurchase Date\tPayment Date\tTitle\tPrice\tShip Price\tQty\tOrder Total\tBuyer Name\tBuyer Email\tShipping Address 1\tShipping Address 2\tShipping City\tShipping State\tShipping Zip\tShipping Country";
        //
       //foreach($product as $prd)
        //{
        // 
        //  $data=$prd['ord_id']."\t".$prd['ord_itm_id']."\t".$prd['sku']."\t".$prd['po_date']."\t".$prd['pay_date']."\t".$prd['itm_name']."\t".$prd['price']."\t".$prd['ship_price']."\t".$prd['qty']."\t".$prd['order_total']."\t".$prd['buyer_name']."\t".$prd['buyer_email']."\t".$prd['ship_addr1']."\t".$prd['ship_addr2']."\t".$prd['ship_city']."\t".$prd['ship_state']."\t".$prd['ship_zip']."\t".$prd['ship_country']."\n";
        //  
        //}
		//fputcsv($file_path,$data);	
		//}
        fclose($feed_file);
        if(is_file($file_path))
        {
          $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."Orders_report_list/download/".$hash_name;
          echo json_encode($data);
		  die();
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
     header('Content-Type: text/vnd.ms-excel');
     header('Content-Transfer-Encoding: binary');
     header('Expires: 0');
     header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
     header('Pragma: public'); 
     readfile($abs_path);
   }

 
  
}
