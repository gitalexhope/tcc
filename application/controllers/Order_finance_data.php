<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_finance_data extends CI_Controller {
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
       $this->load->model('Order_finance_data_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
	  $data['src']=$this->Order_finance_data_model->get_source_list();
      $this->load->view('UI/order_finance_data',$data);
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='fin_id',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->Order_finance_data_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->Order_finance_data_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array('Order ID','SKU','OrderItemID','Principal','Tax','GiftWrap','GiftWrapTax','ShippingCharge','ShippingTax','FBAPerUnitFulfillmentFee','Commission','FixedClosingFee','GiftwrapChargeback','ShippingChargeback','VariableClosingFee','Quantity Shipped','Marketplace','Promo Amount1','Promo Amount1','Promo Amount3','Promo Amount4','Promo Amount5','Promo Amount6','MarketplaceFacilitatorTax-Principal','MarketplaceFacilitatorTax-Shipping','MarketplaceFacilitatorTax-Giftwrap','MarketplaceFacilitatorTax-Other','TaxDiscount','CODItemCharge','CODItemTaxCharge','CODOrderCharge','CODOrderTaxCharge','CODShippingCharge','CODShippingTaxCharge','Goodwill','RestockingFee','ReturnShipping','PointsFee','GenericDeduction','FreeReplacementReturnShipping','PaymentMethodFee','ExportCharge','SAFE-TReimbursement','TCS-CGST','TCS-SGST','TCS-IGST','TCS-UTGST','BubblewrapFee','FBACustomerReturnPerOrderFee ','FBACustomerReturnPerUnitFee','FBACustomerReturnWeightBasedFee','FBADisposalFee','FBAFulfillmentCODFee','FBAInboundConvenienceFee','FBAInboundDefectFee','FBAInboundTransportationFee','FBAInboundTransportationProgramFee','FBALongTermStorageFee','FBAOverageFee','FBAPerOrderFulfillmentFee','FBARemovalFee','FBAStorageFee','FBATransportationFee','FBAWeightBasedFee','FulfillmentFee','FulfillmentNetworkFee','LabelingFee','OpaqueBaggingFee','PolybaggingFee','SSOFFulfillmentFee','TapingFee','TransportationFee','UnitFulfillmentFee'));
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['sku'],$prd['itemid'],$prd['principal'],$prd['tax'],$prd['giftwrap'],$prd['giftwraptax'],$prd['shippingcharge'],$prd['shippingtax'],$prd['fbafee'],$prd['commission'],$prd['fixedclosingfee'],$prd['giftwrapchargeback'],$prd['shippingchargeback'],$prd['variableclosingfee'],$prd['qty'],$prd['fin_country'],$prd['promo_price1'],$prd['promo_price2'],$prd['promo_price3'],$prd['promo_price4'],$prd['promo_price5'],$prd['promo_price6'],$prd['market_facilatortax_principal'],$prd['market_facilatortax_shipping'],$prd['market_facilatortax_giftwrap'],$prd['market_facilatortax_other'],$prd['taxdiscount'],$prd['cod_item_charge'],$prd['cod_item_tax_charge'],$prd['cod_order_charge'],$prd['cod_order_tax_charge'],$prd['cod_shipping_charge'],$prd['cod_shipping_tax_charge'],$prd['good_will'],$prd['restocking_fee'],$prd['return_shipping'],$prd['points_fee'],$prd['generic_deduction'],$prd['free_replace_ret_shipping'],$prd['payment_method_fee'],$prd['export_charge'],$prd['safe_t_claim'],$prd['tcs_cgst'],$prd['tcs_sgst'],$prd['tcs_igst'],$prd['tcs_utgst'],$prd['bubble_wrap_fee'],$prd['fba_cus_ret_per_order_fee'],$prd['fba_cus_ret_per_unit_fee'],$prd['fba_cus_ret_weightbased_fee'],$prd['fba_disposal_fee'],$prd['fba_fulfil_cod_fee'],$prd['fba_inb_con_fee'],$prd['fba_inb_def_fee'],$prd['fba_inb_transport_fee'],$prd['fba_inb_transport_program_fee'],$prd['fba_longterm_storage_fee'],$prd['fba_overage_fee'],$prd['fba_perorder_fulfill_fee'],$prd['fba_removal_fee'],$prd['fba_storage_fee'],$prd['fba_transport_fee'],$prd['fba_weightbased_fee'],$prd['fullfill_fee'],$prd['fullfill_network_fee'],$prd['lable_fee'],$prd['opa_bagging_fee'],$prd['poly_bagging_fee'],$prd['ssof_fullfill_fee'],$prd['taping_fee'],$prd['transport_fee'],$prd['unfullfill_fee']);
          fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file,array('Order ID','SKU','OrderItemID','Principal','Tax','GiftWrap','GiftWrapTax','ShippingCharge','ShippingTax','FBAPerUnitFulfillmentFee','Commission','FixedClosingFee','GiftwrapChargeback','ShippingChargeback','VariableClosingFee','Quantity Shipped','Marketplace','Promo Amount1','Promo Amount1','Promo Amount3','Promo Amount4','Promo Amount5','Promo Amount6','MarketplaceFacilitatorTax-Principal','MarketplaceFacilitatorTax-Shipping','MarketplaceFacilitatorTax-Giftwrap','MarketplaceFacilitatorTax-Other','TaxDiscount','CODItemCharge','CODItemTaxCharge','CODOrderCharge','CODOrderTaxCharge','CODShippingCharge','CODShippingTaxCharge','Goodwill','RestockingFee','ReturnShipping','PointsFee','GenericDeduction','FreeReplacementReturnShipping','PaymentMethodFee','ExportCharge','SAFE-TReimbursement','TCS-CGST','TCS-SGST','TCS-IGST','TCS-UTGST','BubblewrapFee','FBACustomerReturnPerOrderFee ','FBACustomerReturnPerUnitFee','FBACustomerReturnWeightBasedFee','FBADisposalFee','FBAFulfillmentCODFee','FBAInboundConvenienceFee','FBAInboundDefectFee','FBAInboundTransportationFee','FBAInboundTransportationProgramFee','FBALongTermStorageFee','FBAOverageFee','FBAPerOrderFulfillmentFee','FBARemovalFee','FBAStorageFee','FBATransportationFee','FBAWeightBasedFee','FulfillmentFee','FulfillmentNetworkFee','LabelingFee','OpaqueBaggingFee','PolybaggingFee','SSOFFulfillmentFee','TapingFee','TransportationFee','UnitFulfillmentFee'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['sku'],$prd['itemid'],$prd['principal'],$prd['tax'],$prd['giftwrap'],$prd['giftwraptax'],$prd['shippingcharge'],$prd['shippingtax'],$prd['fbafee'],$prd['commission'],$prd['fixedclosingfee'],$prd['giftwrapchargeback'],$prd['shippingchargeback'],$prd['variableclosingfee'],$prd['qty'],$prd['fin_country'],$prd['promo_price1'],$prd['promo_price2'],$prd['promo_price3'],$prd['promo_price4'],$prd['promo_price5'],$prd['promo_price6'],$prd['market_facilatortax_principal'],$prd['market_facilatortax_shipping'],$prd['market_facilatortax_giftwrap'],$prd['market_facilatortax_other'],$prd['taxdiscount'],$prd['cod_item_charge'],$prd['cod_item_tax_charge'],$prd['cod_order_charge'],$prd['cod_order_tax_charge'],$prd['cod_shipping_charge'],$prd['cod_shipping_tax_charge'],$prd['good_will'],$prd['restocking_fee'],$prd['return_shipping'],$prd['points_fee'],$prd['generic_deduction'],$prd['free_replace_ret_shipping'],$prd['payment_method_fee'],$prd['export_charge'],$prd['safe_t_claim'],$prd['tcs_cgst'],$prd['tcs_sgst'],$prd['tcs_igst'],$prd['tcs_utgst'],$prd['bubble_wrap_fee'],$prd['fba_cus_ret_per_order_fee'],$prd['fba_cus_ret_per_unit_fee'],$prd['fba_cus_ret_weightbased_fee'],$prd['fba_disposal_fee'],$prd['fba_fulfil_cod_fee'],$prd['fba_inb_con_fee'],$prd['fba_inb_def_fee'],$prd['fba_inb_transport_fee'],$prd['fba_inb_transport_program_fee'],$prd['fba_longterm_storage_fee'],$prd['fba_overage_fee'],$prd['fba_perorder_fulfill_fee'],$prd['fba_removal_fee'],$prd['fba_storage_fee'],$prd['fba_transport_fee'],$prd['fba_weightbased_fee'],$prd['fullfill_fee'],$prd['fullfill_network_fee'],$prd['lable_fee'],$prd['opa_bagging_fee'],$prd['poly_bagging_fee'],$prd['ssof_fullfill_fee'],$prd['taping_fee'],$prd['transport_fee'],$prd['unfullfill_fee']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt')
		{
        fputcsv($feed_file,array('Order ID','SKU','OrderItemID','Principal','Tax','GiftWrap','GiftWrapTax','ShippingCharge','ShippingTax','FBAPerUnitFulfillmentFee','Commission','FixedClosingFee','GiftwrapChargeback','ShippingChargeback','VariableClosingFee','Quantity Shipped','Marketplace','Promo Amount1','Promo Amount1','Promo Amount3','Promo Amount4','Promo Amount5','Promo Amount6','MarketplaceFacilitatorTax-Principal','MarketplaceFacilitatorTax-Shipping','MarketplaceFacilitatorTax-Giftwrap','MarketplaceFacilitatorTax-Other','TaxDiscount','CODItemCharge','CODItemTaxCharge','CODOrderCharge','CODOrderTaxCharge','CODShippingCharge','CODShippingTaxCharge','Goodwill','RestockingFee','ReturnShipping','PointsFee','GenericDeduction','FreeReplacementReturnShipping','PaymentMethodFee','ExportCharge','SAFE-TReimbursement','TCS-CGST','TCS-SGST','TCS-IGST','TCS-UTGST','BubblewrapFee','FBACustomerReturnPerOrderFee ','FBACustomerReturnPerUnitFee','FBACustomerReturnWeightBasedFee','FBADisposalFee','FBAFulfillmentCODFee','FBAInboundConvenienceFee','FBAInboundDefectFee','FBAInboundTransportationFee','FBAInboundTransportationProgramFee','FBALongTermStorageFee','FBAOverageFee','FBAPerOrderFulfillmentFee','FBARemovalFee','FBAStorageFee','FBATransportationFee','FBAWeightBasedFee','FulfillmentFee','FulfillmentNetworkFee','LabelingFee','OpaqueBaggingFee','PolybaggingFee','SSOFFulfillmentFee','TapingFee','TransportationFee','UnitFulfillmentFee'),"\t");
        foreach($product as $prd)
        {
          $data=array($prd['order_id'],$prd['sku'],$prd['itemid'],$prd['principal'],$prd['tax'],$prd['giftwrap'],$prd['giftwraptax'],$prd['shippingcharge'],$prd['shippingtax'],$prd['fbafee'],$prd['commission'],$prd['fixedclosingfee'],$prd['giftwrapchargeback'],$prd['shippingchargeback'],$prd['variableclosingfee'],$prd['qty'],$prd['fin_country'],$prd['promo_price1'],$prd['promo_price2'],$prd['promo_price3'],$prd['promo_price4'],$prd['promo_price5'],$prd['promo_price6'],$prd['market_facilatortax_principal'],$prd['market_facilatortax_shipping'],$prd['market_facilatortax_giftwrap'],$prd['market_facilatortax_other'],$prd['taxdiscount'],$prd['cod_item_charge'],$prd['cod_item_tax_charge'],$prd['cod_order_charge'],$prd['cod_order_tax_charge'],$prd['cod_shipping_charge'],$prd['cod_shipping_tax_charge'],$prd['good_will'],$prd['restocking_fee'],$prd['return_shipping'],$prd['points_fee'],$prd['generic_deduction'],$prd['free_replace_ret_shipping'],$prd['payment_method_fee'],$prd['export_charge'],$prd['safe_t_claim'],$prd['tcs_cgst'],$prd['tcs_sgst'],$prd['tcs_igst'],$prd['tcs_utgst'],$prd['bubble_wrap_fee'],$prd['fba_cus_ret_per_order_fee'],$prd['fba_cus_ret_per_unit_fee'],$prd['fba_cus_ret_weightbased_fee'],$prd['fba_disposal_fee'],$prd['fba_fulfil_cod_fee'],$prd['fba_inb_con_fee'],$prd['fba_inb_def_fee'],$prd['fba_inb_transport_fee'],$prd['fba_inb_transport_program_fee'],$prd['fba_longterm_storage_fee'],$prd['fba_overage_fee'],$prd['fba_perorder_fulfill_fee'],$prd['fba_removal_fee'],$prd['fba_storage_fee'],$prd['fba_transport_fee'],$prd['fba_weightbased_fee'],$prd['fullfill_fee'],$prd['fullfill_network_fee'],$prd['lable_fee'],$prd['opa_bagging_fee'],$prd['poly_bagging_fee'],$prd['ssof_fullfill_fee'],$prd['taping_fee'],$prd['transport_fee'],$prd['unfullfill_fee']);
          fputcsv($feed_file,$data,"\t");
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
		  $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."Order_finance_data/download/".$hash_name;
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
