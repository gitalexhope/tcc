<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orders_report_actionable_data_list extends CI_Controller {
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
       $this->load->model('Orders_report_actionable_data_list_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/orders_report_actionable_data_list');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='po_date',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->Orders_report_actionable_data_list_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->Orders_report_actionable_data_list_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array('Order ID','Order Item ID','SKU','Title','Purchase Date','Payment Date','Reporting Date','Promise Date','Day Past Promise','Buyer Name','Buyer Email','Buyer Phone','Qty Purchased','Qty Shipped','Qty UnShipped','Shipping Address1','Shipping Address2','Shipping Address3','Shipping City','Shipping State','Shipping Post','Shipping country'));
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['order_item_id'],$prd['sku'],$prd['prod_name'],$prd['po_date'],$prd['pay_date'],$prd['rep_date'],$prd['prom_date'],$prd['day_past'],$prd['buy_name'],$prd['buy_email'],$prd['buy_phone'],$prd['qty_pur'],$prd['qty_ship'],$prd['qty_unship'],$prd['ship_addr1'],$prd['ship_addr2'],$prd['ship_addr3'],$prd['ship_city'],$prd['ship_state'],$prd['ship_post'],$prd['ship_country']);
          fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file,array('Order ID','Order Item ID','SKU','Title','Purchase Date','Payment Date','Reporting Date','Promise Date','Day Past Promise','Buyer Name','Buyer Email','Buyer Phone','Qty Purchased','Qty Shipped','Qty UnShipped','Shipping Address1','Shipping Address2','Shipping Address3','Shipping City','Shipping State','Shipping Post','Shipping country'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['order_item_id'],$prd['sku'],$prd['prod_name'],$prd['po_date'],$prd['pay_date'],$prd['rep_date'],$prd['prom_date'],$prd['day_past'],$prd['buy_name'],$prd['buy_email'],$prd['buy_phone'],$prd['qty_pur'],$prd['qty_ship'],$prd['qty_unship'],$prd['ship_addr1'],$prd['ship_addr2'],$prd['ship_addr3'],$prd['ship_city'],$prd['ship_state'],$prd['ship_post'],$prd['ship_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt')
		{
        fputcsv($feed_file,array('Order ID','Order Item ID','SKU','Title','Purchase Date','Payment Date','Reporting Date','Promise Date','Day Past Promise','Buyer Name','Buyer Email','Buyer Phone','Qty Purchased','Qty Shipped','Qty UnShipped','Shipping Address1','Shipping Address2','Shipping Address3','Shipping City','Shipping State','Shipping Post','Shipping country'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['order_item_id'],$prd['sku'],$prd['prod_name'],$prd['po_date'],$prd['pay_date'],$prd['rep_date'],$prd['prom_date'],$prd['day_past'],$prd['buy_name'],$prd['buy_email'],$prd['buy_phone'],$prd['qty_pur'],$prd['qty_ship'],$prd['qty_unship'],$prd['ship_addr1'],$prd['ship_addr2'],$prd['ship_addr3'],$prd['ship_city'],$prd['ship_state'],$prd['ship_post'],$prd['ship_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
          $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."Orders_report_orderby_date_list/download/".$hash_name;
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
