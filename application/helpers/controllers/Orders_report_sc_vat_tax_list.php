<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orders_report_sc_vat_tax_list extends CI_Controller {
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
       $this->load->model('Orders_report_sc_vat_tax_list_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/orders_report_sc_vat_tax_list');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='rep_id',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->Orders_report_sc_vat_tax_list_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->Orders_report_sc_vat_tax_list_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status." ";
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array('Order ID','SKU','ASIN','Merchant ID','Order Date','Transaction Type','Ship Date','Shipment ID','Transaction ID','Tax Calculation Date','Tax Rate','Tax Code','Tax Type','Tax Calculation Reason Code','Our Price Tax Inclusive','Our Price Tax','Our Price Tax Exclusive','Ship Price Tax Inclusive','Ship Price Tax','Ship Price Tax Exclusive','Gift Price Tax Inclusive','Gift Price Tax','Gift Price Tax Exclusive','Our Promotional Tax Inclusive','Our Promotional Tax','Our Promotional Tax Exclusive','Ship Promotional Tax Inclusive','Ship Promotional Tax','Ship Promotional Tax Exclusive','Gift Promotional Tax Inclusive','Gift Promotional Tax','Gift Promotional Tax Exclusive','Seller Tax Registration','Buyer Tax Registration','Buyer Tax Registration Type','Invoice Exchange Rate','Invoice Exchange Date','Converted Tax Amount','VAT Invoice No',' Invoice Url','Ship From City','Ship to City','Ship From State','Ship to State','Ship From Postal','Ship to Postal','Ship From Country','Ship to Country'));
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['sku'],$prd['asin'],$prd['merchant_id'],$prd['ord_date'],$prd['trans_type'],$prd['ship_date'],$prd['ship_id'],$prd['trans_id'],$prd['tax_cal_date'],$prd['tax_rate'],$prd['tax_code'],$prd['tax_type'],$prd['tax_cal_rsn_code'],$prd['our_price_tax_inclusive'],$prd['our_price_tax'],$prd['our_price_tax_inclusive'],$prd['ship_tax_inclusive'],$prd['ship_tax'],$prd['ship_tax_exclusive'],$prd['gift_tax_inclusive'],$prd['gift_tax'],$prd['gift_tax_exclusive'],$prd['our_promo_amount_inclusive'],$prd['our_promo_amount'],$prd['our_promo_amount_exclusive'],$prd['ship_tax_promo_inclusive'],$prd['ship_tax_promo'],$prd['ship_tax_promo_exclusive'],$prd['gift_tax_promo_inclusive'],$prd['gift_tax_promo'],$prd['gift_tax_promo_exclusive'],$prd['sell_tax_reg'],$prd['buy_tax_reg'],$prd['buy_tax_reg_type'],$prd['inv_ex_rate'],$prd['inv_ex_date'],$prd['con_tax_amt'],$prd['vat_inv_no'],$prd['inv_url'],$prd['ship_from_city'],$prd['ship_to_city'],$prd['ship_from_state'],$prd['ship_to_state'],$prd['ship_from_postal'],$prd['ship_to_postal'],$prd['ship_from_country'],$prd['ship_to_country']);
          fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file,array('Order ID','SKU','ASIN','Merchant ID','Order Date','Transaction Type','Ship Date','Shipment ID','Transaction ID','Tax Calculation Date','Tax Rate','Tax Code','Tax Type','Tax Calculation Reason Code','Our Price Tax Inclusive','Our Price Tax','Our Price Tax Exclusive','Ship Price Tax Inclusive','Ship Price Tax','Ship Price Tax Exclusive','Gift Price Tax Inclusive','Gift Price Tax','Gift Price Tax Exclusive','Our Promotional Tax Inclusive','Our Promotional Tax','Our Promotional Tax Exclusive','Ship Promotional Tax Inclusive','Ship Promotional Tax','Ship Promotional Tax Exclusive','Gift Promotional Tax Inclusive','Gift Promotional Tax','Gift Promotional Tax Exclusive','Seller Tax Registration','Buyer Tax Registration','Buyer Tax Registration Type','Invoice Exchange Rate','Invoice Exchange Date','Converted Tax Amount','VAT Invoice No',' Invoice Url','Ship From City','Ship to City','Ship From State','Ship to State','Ship From Postal','Ship to Postal','Ship From Country','Ship to Country'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['sku'],$prd['asin'],$prd['merchant_id'],$prd['ord_date'],$prd['trans_type'],$prd['ship_date'],$prd['ship_id'],$prd['trans_id'],$prd['tax_cal_date'],$prd['tax_rate'],$prd['tax_code'],$prd['tax_type'],$prd['tax_cal_rsn_code'],$prd['our_price_tax_inclusive'],$prd['our_price_tax'],$prd['our_price_tax_inclusive'],$prd['ship_tax_inclusive'],$prd['ship_tax'],$prd['ship_tax_exclusive'],$prd['gift_tax_inclusive'],$prd['gift_tax'],$prd['gift_tax_exclusive'],$prd['our_promo_amount_inclusive'],$prd['our_promo_amount'],$prd['our_promo_amount_exclusive'],$prd['ship_tax_promo_inclusive'],$prd['ship_tax_promo'],$prd['ship_tax_promo_exclusive'],$prd['gift_tax_promo_inclusive'],$prd['gift_tax_promo'],$prd['gift_tax_promo_exclusive'],$prd['sell_tax_reg'],$prd['buy_tax_reg'],$prd['buy_tax_reg_type'],$prd['inv_ex_rate'],$prd['inv_ex_date'],$prd['con_tax_amt'],$prd['vat_inv_no'],$prd['inv_url'],$prd['ship_from_city'],$prd['ship_to_city'],$prd['ship_from_state'],$prd['ship_to_state'],$prd['ship_from_postal'],$prd['ship_to_postal'],$prd['ship_from_country'],$prd['ship_to_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt')
		{
        fputcsv($feed_file,array('Order ID','SKU','ASIN','Merchant ID','Order Date','Transaction Type','Ship Date','Shipment ID','Transaction ID','Tax Calculation Date','Tax Rate','Tax Code','Tax Type','Tax Calculation Reason Code','Our Price Tax Inclusive','Our Price Tax','Our Price Tax Exclusive','Ship Price Tax Inclusive','Ship Price Tax','Ship Price Tax Exclusive','Gift Price Tax Inclusive','Gift Price Tax','Gift Price Tax Exclusive','Our Promotional Tax Inclusive','Our Promotional Tax','Our Promotional Tax Exclusive','Ship Promotional Tax Inclusive','Ship Promotional Tax','Ship Promotional Tax Exclusive','Gift Promotional Tax Inclusive','Gift Promotional Tax','Gift Promotional Tax Exclusive','Seller Tax Registration','Buyer Tax Registration','Buyer Tax Registration Type','Invoice Exchange Rate','Invoice Exchange Date','Converted Tax Amount','VAT Invoice No',' Invoice Url','Ship From City','Ship to City','Ship From State','Ship to State','Ship From Postal','Ship to Postal','Ship From Country','Ship to Country'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['sku'],$prd['asin'],$prd['merchant_id'],$prd['ord_date'],$prd['trans_type'],$prd['ship_date'],$prd['ship_id'],$prd['trans_id'],$prd['tax_cal_date'],$prd['tax_rate'],$prd['tax_code'],$prd['tax_type'],$prd['tax_cal_rsn_code'],$prd['our_price_tax_inclusive'],$prd['our_price_tax'],$prd['our_price_tax_inclusive'],$prd['ship_tax_inclusive'],$prd['ship_tax'],$prd['ship_tax_exclusive'],$prd['gift_tax_inclusive'],$prd['gift_tax'],$prd['gift_tax_exclusive'],$prd['our_promo_amount_inclusive'],$prd['our_promo_amount'],$prd['our_promo_amount_exclusive'],$prd['ship_tax_promo_inclusive'],$prd['ship_tax_promo'],$prd['ship_tax_promo_exclusive'],$prd['gift_tax_promo_inclusive'],$prd['gift_tax_promo'],$prd['gift_tax_promo_exclusive'],$prd['sell_tax_reg'],$prd['buy_tax_reg'],$prd['buy_tax_reg_type'],$prd['inv_ex_rate'],$prd['inv_ex_date'],$prd['con_tax_amt'],$prd['vat_inv_no'],$prd['inv_url'],$prd['ship_from_city'],$prd['ship_to_city'],$prd['ship_from_state'],$prd['ship_to_state'],$prd['ship_from_postal'],$prd['ship_to_postal'],$prd['ship_from_country'],$prd['ship_to_country']);
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
