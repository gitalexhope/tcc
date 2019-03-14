<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rep_vat_transaction_data extends CI_Controller {
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
       $this->load->model('Rep_vat_transaction_data_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/rep_vat_transaction_data');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='vat_id',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->Rep_vat_transaction_data_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->Rep_vat_transaction_data_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array('Unique Account Identifier','Activity Period','Sales Channel','Marketplace','Transaction Type','Transaction Event Id','Activity Transaction Id','Tax Calculation Date','Transaction Depart Date','Transaction Arrival Date','Transaction Complete Date','Seller Sku','Asin','Item Description','Qty','Item Weight','Total Activity Weight','Cost Price Of Items','Price Of Items Amt Vat Excl','Promo Price Of Items Amt Vat Excl','Total Price Of Items Amt Vat Excl','Ship Charge Amt Vat Excl','Promo Ship Charge Amt Vat Excl','Total Ship Charge Amt Vat Excl','Gift Wrap Amt Vat Excl','Promo Gift Wrap Amt Vat Excl','Total Gift Wrap Amt Vat Excl','Total Activity Value Amt Vat Excl','Price Of Items Vat Rate Percent','Price Of Items Vat Amt','Promo Price Of Items Vat Amt','Total Price Of Items Vat Amt','Ship Charge Vat Rate Percent','Ship Charge Vat Amt','Promo Ship Charge Vat Amt','Total Ship Charge Vat Amt','Gift Wrap Vat Rate Percent','Gift Wrap Vat Amt','Promo Gift Wrap Vat Amt','Total Gift Wrap Vat Amt','Total Activity Value Vat Amt','Price Of Items Amt Vat Incl','Promo Price Of Items Amt Vat Incl','Total Price Of Items Amt Vat Incl','Ship Charge Amt Vat Incl','Promo Ship Charge Amt Vat Incl','Total Ship Charge Amt Vat Incl','Gift Wrap Amt Vat Incl','Promo Gift Wrap Amt Vat Incl','Total Gift Wrap Amt Vat Incl','Total Activity Value Amt Vat Incl','Transaction Currency Code','Commodity Code','Statistical Code Depart','Statistical Code Arrival','Commodity Code Supplementary Unit','Item Qty Supplementary Unit','Total Activity Supplementary Unit','Product Tax Code','Depature City','Departure Country','Departure Post Code','Arrival City','Arrival Country','Arrival Post Code','Sale Depart Country','Sale Arrival Country','Transportation Mode','Delivery Conditions','Seller Depart Vat Number Country','Seller Depart Country Vat Number','Seller Arrival Vat Number Country','Seller Arrival Country Vat Number','Transaction Seller Vat Number Country','Transaction Seller Vat Number','Buyer Vat Number Country','Buyer Vat Number','Vat Calculation Imputation Country','Taxable Jurisdiction','Taxable Jurisdiction Level','Vat Inv Number','Vat Inv Converted Amt','Vat Inv Currency Code','Vat Inv Exchange Rate','Vat Inv Exchange Rate Date','Export Outside Eu','Invoice Url','Buyer Name','Arrival Address'));
        foreach($product as $prd)
        {
          $data=array($prd['unique_acc_identifier'],$prd['activity_period'],$prd['sales_channel'],$prd['country'],$prd['trans_type'],$prd['trans_event_id'],$prd['activity_trans_id'],$prd['tax_cal_date'],$prd['trans_depart_date'],$prd['trans_arraival_date'],$prd['trans_compile_date'],$prd['seller_sku'],$prd['prod_asin'],$prd['description'],$prd['qty'],$prd['itm_weight'],$prd['total_weight_activity'],$prd['cost_price_of_items'],$prd['price_of_items_amt_vat_excl'],$prd['promo_price_of_items_amt_vat_excl'],$prd['total_price_of_items_amt_vat_excl'],$prd['ship_charge_amt_vat_excl'],$prd['promo_ship_charge_amt_vat_excl'],$prd['total_ship_charge_amt_vat_excl'],$prd['gift_wrap_amt_vat_excl'],$prd['promo_gift_wrap_amt_vat_excl'],$prd['total_gift_wrap_amt_vat_excl'],$prd['total_activity_value_amt_vat_excl'],$prd['price_of_items_vat_rate_percent'],$prd['price_of_items_vat_amt'],$prd['promo_price_of_items_vat_amt'],$prd['total_price_of_items_vat_amt'],$prd['ship_charge_vat_rate_percent'],$prd['ship_charge_vat_amt'],$prd['promo_ship_charge_vat_amt'],$prd['total_ship_charge_vat_amt'],$prd['gift_wrap_vat_rate_percent'],$prd['gift_wrap_vat_amt'],$prd['promo_gift_wrap_vat_amt'],$prd['total_gift_wrap_vat_amt'],$prd['total_activity_value_vat_amt'],$prd['price_of_items_amt_vat_incl'],$prd['promo_price_of_items_amt_vat_incl'],$prd['total_price_of_items_amt_vat_incl'],$prd['ship_charge_amt_vat_incl'],$prd['promo_ship_charge_amt_vat_incl'],$prd['total_ship_charge_amt_vat_incl'],$prd['gift_wrap_amt_vat_incl'],$prd['promo_gift_wrap_amt_vat_incl'],$prd['total_gift_wrap_amt_vat_incl'],$prd['total_activity_value_amt_vat_incl'],$prd['transaction_currency_code'],$prd['commodity_code'],$prd['statistical_code_depart'],$prd['statistical_code_arrival'],$prd['commodity_code_supplementary_unit'],$prd['item_qty_supplementary_unit'],$prd['total_activity_supplementary_unit'],$prd['product_tax_code'],$prd['depature_city'],$prd['departure_country'],$prd['departure_post_code'],$prd['arrival_city'],$prd['arrival_country'],$prd['arrival_post_code'],$prd['sale_depart_country'],$prd['sale_arrival_country'],$prd['transportation_mode'],$prd['delivery_conditions'],$prd['seller_depart_vat_number_country'],$prd['seller_depart_country_vat_number'],$prd['seller_arrival_vat_number_country'],$prd['seller_arrival_country_vat_number'],$prd['transaction_seller_vat_number_country'],$prd['transaction_seller_vat_number'],$prd['buyer_vat_number_country'],$prd['buyer_vat_number'],$prd['vat_calculation_imputation_country'],$prd['taxable_jurisdiction'],$prd['taxable_jurisdiction_level'],$prd['vat_inv_number'],$prd['vat_inv_converted_amt'],$prd['vat_inv_currency_code'],$prd['vat_inv_exchange_rate'],$prd['vat_inv_exchange_rate_date'],$prd['export_outside_eu'],$prd['invoice_url'],$prd['buyer_name'],$prd['arrival_address']);
          fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file,array('Unique Account Identifier','Activity Period','Sales Channel','Marketplace','Transaction Type','Transaction Event Id','Activity Transaction Id','Tax Calculation Date','Transaction Depart Date','Transaction Arrival Date','Transaction Complete Date','Seller Sku','Asin','Item Description','Qty','Item Weight','Total Activity Weight','Cost Price Of Items','Price Of Items Amt Vat Excl','Promo Price Of Items Amt Vat Excl','Total Price Of Items Amt Vat Excl','Ship Charge Amt Vat Excl','Promo Ship Charge Amt Vat Excl','Total Ship Charge Amt Vat Excl','Gift Wrap Amt Vat Excl','Promo Gift Wrap Amt Vat Excl','Total Gift Wrap Amt Vat Excl','Total Activity Value Amt Vat Excl','Price Of Items Vat Rate Percent','Price Of Items Vat Amt','Promo Price Of Items Vat Amt','Total Price Of Items Vat Amt','Ship Charge Vat Rate Percent','Ship Charge Vat Amt','Promo Ship Charge Vat Amt','Total Ship Charge Vat Amt','Gift Wrap Vat Rate Percent','Gift Wrap Vat Amt','Promo Gift Wrap Vat Amt','Total Gift Wrap Vat Amt','Total Activity Value Vat Amt','Price Of Items Amt Vat Incl','Promo Price Of Items Amt Vat Incl','Total Price Of Items Amt Vat Incl','Ship Charge Amt Vat Incl','Promo Ship Charge Amt Vat Incl','Total Ship Charge Amt Vat Incl','Gift Wrap Amt Vat Incl','Promo Gift Wrap Amt Vat Incl','Total Gift Wrap Amt Vat Incl','Total Activity Value Amt Vat Incl','Transaction Currency Code','Commodity Code','Statistical Code Depart','Statistical Code Arrival','Commodity Code Supplementary Unit','Item Qty Supplementary Unit','Total Activity Supplementary Unit','Product Tax Code','Depature City','Departure Country','Departure Post Code','Arrival City','Arrival Country','Arrival Post Code','Sale Depart Country','Sale Arrival Country','Transportation Mode','Delivery Conditions','Seller Depart Vat Number Country','Seller Depart Country Vat Number','Seller Arrival Vat Number Country','Seller Arrival Country Vat Number','Transaction Seller Vat Number Country','Transaction Seller Vat Number','Buyer Vat Number Country','Buyer Vat Number','Vat Calculation Imputation Country','Taxable Jurisdiction','Taxable Jurisdiction Level','Vat Inv Number','Vat Inv Converted Amt','Vat Inv Currency Code','Vat Inv Exchange Rate','Vat Inv Exchange Rate Date','Export Outside Eu','Invoice Url','Buyer Name','Arrival Address'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['unique_acc_identifier'],$prd['activity_period'],$prd['sales_channel'],$prd['country'],$prd['trans_type'],$prd['trans_event_id'],$prd['activity_trans_id'],$prd['tax_cal_date'],$prd['trans_depart_date'],$prd['trans_arraival_date'],$prd['trans_compile_date'],$prd['seller_sku'],$prd['prod_asin'],$prd['description'],$prd['qty'],$prd['itm_weight'],$prd['total_weight_activity'],$prd['cost_price_of_items'],$prd['price_of_items_amt_vat_excl'],$prd['promo_price_of_items_amt_vat_excl'],$prd['total_price_of_items_amt_vat_excl'],$prd['ship_charge_amt_vat_excl'],$prd['promo_ship_charge_amt_vat_excl'],$prd['total_ship_charge_amt_vat_excl'],$prd['gift_wrap_amt_vat_excl'],$prd['promo_gift_wrap_amt_vat_excl'],$prd['total_gift_wrap_amt_vat_excl'],$prd['total_activity_value_amt_vat_excl'],$prd['price_of_items_vat_rate_percent'],$prd['price_of_items_vat_amt'],$prd['promo_price_of_items_vat_amt'],$prd['total_price_of_items_vat_amt'],$prd['ship_charge_vat_rate_percent'],$prd['ship_charge_vat_amt'],$prd['promo_ship_charge_vat_amt'],$prd['total_ship_charge_vat_amt'],$prd['gift_wrap_vat_rate_percent'],$prd['gift_wrap_vat_amt'],$prd['promo_gift_wrap_vat_amt'],$prd['total_gift_wrap_vat_amt'],$prd['total_activity_value_vat_amt'],$prd['price_of_items_amt_vat_incl'],$prd['promo_price_of_items_amt_vat_incl'],$prd['total_price_of_items_amt_vat_incl'],$prd['ship_charge_amt_vat_incl'],$prd['promo_ship_charge_amt_vat_incl'],$prd['total_ship_charge_amt_vat_incl'],$prd['gift_wrap_amt_vat_incl'],$prd['promo_gift_wrap_amt_vat_incl'],$prd['total_gift_wrap_amt_vat_incl'],$prd['total_activity_value_amt_vat_incl'],$prd['transaction_currency_code'],$prd['commodity_code'],$prd['statistical_code_depart'],$prd['statistical_code_arrival'],$prd['commodity_code_supplementary_unit'],$prd['item_qty_supplementary_unit'],$prd['total_activity_supplementary_unit'],$prd['product_tax_code'],$prd['depature_city'],$prd['departure_country'],$prd['departure_post_code'],$prd['arrival_city'],$prd['arrival_country'],$prd['arrival_post_code'],$prd['sale_depart_country'],$prd['sale_arrival_country'],$prd['transportation_mode'],$prd['delivery_conditions'],$prd['seller_depart_vat_number_country'],$prd['seller_depart_country_vat_number'],$prd['seller_arrival_vat_number_country'],$prd['seller_arrival_country_vat_number'],$prd['transaction_seller_vat_number_country'],$prd['transaction_seller_vat_number'],$prd['buyer_vat_number_country'],$prd['buyer_vat_number'],$prd['vat_calculation_imputation_country'],$prd['taxable_jurisdiction'],$prd['taxable_jurisdiction_level'],$prd['vat_inv_number'],$prd['vat_inv_converted_amt'],$prd['vat_inv_currency_code'],$prd['vat_inv_exchange_rate'],$prd['vat_inv_exchange_rate_date'],$prd['export_outside_eu'],$prd['invoice_url'],$prd['buyer_name'],$prd['arrival_address']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt')
		{
        fputcsv($feed_file,array('Unique Account Identifier','Activity Period','Sales Channel','Marketplace','Transaction Type','Transaction Event Id','Activity Transaction Id','Tax Calculation Date','Transaction Depart Date','Transaction Arrival Date','Transaction Complete Date','Seller Sku','Asin','Item Description','Qty','Item Weight','Total Activity Weight','Cost Price Of Items','Price Of Items Amt Vat Excl','Promo Price Of Items Amt Vat Excl','Total Price Of Items Amt Vat Excl','Ship Charge Amt Vat Excl','Promo Ship Charge Amt Vat Excl','Total Ship Charge Amt Vat Excl','Gift Wrap Amt Vat Excl','Promo Gift Wrap Amt Vat Excl','Total Gift Wrap Amt Vat Excl','Total Activity Value Amt Vat Excl','Price Of Items Vat Rate Percent','Price Of Items Vat Amt','Promo Price Of Items Vat Amt','Total Price Of Items Vat Amt','Ship Charge Vat Rate Percent','Ship Charge Vat Amt','Promo Ship Charge Vat Amt','Total Ship Charge Vat Amt','Gift Wrap Vat Rate Percent','Gift Wrap Vat Amt','Promo Gift Wrap Vat Amt','Total Gift Wrap Vat Amt','Total Activity Value Vat Amt','Price Of Items Amt Vat Incl','Promo Price Of Items Amt Vat Incl','Total Price Of Items Amt Vat Incl','Ship Charge Amt Vat Incl','Promo Ship Charge Amt Vat Incl','Total Ship Charge Amt Vat Incl','Gift Wrap Amt Vat Incl','Promo Gift Wrap Amt Vat Incl','Total Gift Wrap Amt Vat Incl','Total Activity Value Amt Vat Incl','Transaction Currency Code','Commodity Code','Statistical Code Depart','Statistical Code Arrival','Commodity Code Supplementary Unit','Item Qty Supplementary Unit','Total Activity Supplementary Unit','Product Tax Code','Depature City','Departure Country','Departure Post Code','Arrival City','Arrival Country','Arrival Post Code','Sale Depart Country','Sale Arrival Country','Transportation Mode','Delivery Conditions','Seller Depart Vat Number Country','Seller Depart Country Vat Number','Seller Arrival Vat Number Country','Seller Arrival Country Vat Number','Transaction Seller Vat Number Country','Transaction Seller Vat Number','Buyer Vat Number Country','Buyer Vat Number','Vat Calculation Imputation Country','Taxable Jurisdiction','Taxable Jurisdiction Level','Vat Inv Number','Vat Inv Converted Amt','Vat Inv Currency Code','Vat Inv Exchange Rate','Vat Inv Exchange Rate Date','Export Outside Eu','Invoice Url','Buyer Name','Arrival Address'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['unique_acc_identifier'],$prd['activity_period'],$prd['sales_channel'],$prd['country'],$prd['trans_type'],$prd['trans_event_id'],$prd['activity_trans_id'],$prd['tax_cal_date'],$prd['trans_depart_date'],$prd['trans_arraival_date'],$prd['trans_compile_date'],$prd['seller_sku'],$prd['prod_asin'],$prd['description'],$prd['qty'],$prd['itm_weight'],$prd['total_weight_activity'],$prd['cost_price_of_items'],$prd['price_of_items_amt_vat_excl'],$prd['promo_price_of_items_amt_vat_excl'],$prd['total_price_of_items_amt_vat_excl'],$prd['ship_charge_amt_vat_excl'],$prd['promo_ship_charge_amt_vat_excl'],$prd['total_ship_charge_amt_vat_excl'],$prd['gift_wrap_amt_vat_excl'],$prd['promo_gift_wrap_amt_vat_excl'],$prd['total_gift_wrap_amt_vat_excl'],$prd['total_activity_value_amt_vat_excl'],$prd['price_of_items_vat_rate_percent'],$prd['price_of_items_vat_amt'],$prd['promo_price_of_items_vat_amt'],$prd['total_price_of_items_vat_amt'],$prd['ship_charge_vat_rate_percent'],$prd['ship_charge_vat_amt'],$prd['promo_ship_charge_vat_amt'],$prd['total_ship_charge_vat_amt'],$prd['gift_wrap_vat_rate_percent'],$prd['gift_wrap_vat_amt'],$prd['promo_gift_wrap_vat_amt'],$prd['total_gift_wrap_vat_amt'],$prd['total_activity_value_vat_amt'],$prd['price_of_items_amt_vat_incl'],$prd['promo_price_of_items_amt_vat_incl'],$prd['total_price_of_items_amt_vat_incl'],$prd['ship_charge_amt_vat_incl'],$prd['promo_ship_charge_amt_vat_incl'],$prd['total_ship_charge_amt_vat_incl'],$prd['gift_wrap_amt_vat_incl'],$prd['promo_gift_wrap_amt_vat_incl'],$prd['total_gift_wrap_amt_vat_incl'],$prd['total_activity_value_amt_vat_incl'],$prd['transaction_currency_code'],$prd['commodity_code'],$prd['statistical_code_depart'],$prd['statistical_code_arrival'],$prd['commodity_code_supplementary_unit'],$prd['item_qty_supplementary_unit'],$prd['total_activity_supplementary_unit'],$prd['product_tax_code'],$prd['depature_city'],$prd['departure_country'],$prd['departure_post_code'],$prd['arrival_city'],$prd['arrival_country'],$prd['arrival_post_code'],$prd['sale_depart_country'],$prd['sale_arrival_country'],$prd['transportation_mode'],$prd['delivery_conditions'],$prd['seller_depart_vat_number_country'],$prd['seller_depart_country_vat_number'],$prd['seller_arrival_vat_number_country'],$prd['seller_arrival_country_vat_number'],$prd['transaction_seller_vat_number_country'],$prd['transaction_seller_vat_number'],$prd['buyer_vat_number_country'],$prd['buyer_vat_number'],$prd['vat_calculation_imputation_country'],$prd['taxable_jurisdiction'],$prd['taxable_jurisdiction_level'],$prd['vat_inv_number'],$prd['vat_inv_converted_amt'],$prd['vat_inv_currency_code'],$prd['vat_inv_exchange_rate'],$prd['vat_inv_exchange_rate_date'],$prd['export_outside_eu'],$prd['invoice_url'],$prd['buyer_name'],$prd['arrival_address']);
          fputcsv($feed_file,$data,"\t");
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
		  $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."Rep_vat_transaction_data/download/".$hash_name;
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
