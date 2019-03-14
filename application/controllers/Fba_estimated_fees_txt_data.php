<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fba_estimated_fees_txt_data extends CI_Controller {
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
       $this->load->model('fba_estimated_fees_txt_data_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/fba_estimated_fees_txt_data');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='asin',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->fba_estimated_fees_txt_data_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->fba_estimated_fees_txt_data_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array("SKU","Fnsku","ASIN","Product Name","Product Group","Brand","Fulfilled By","Your Price","Sales Price","Longest Side","Median Side","Shortest Side","Length And Girth","Unit Of Dimension","Item Package Weight","Unit Of Weight","Product Size Tier","Currency","Estimated Fee Total","Estimated Referral Fee Per Unit","Estimated Variable Closing Fee","Estimated Order Handling Fee Per Order","Estimated Pick Pack Fee Per Unit","Estimated Weight Handling Fee Per Unit","Expected Fulfillment Fee Per Unit","Estimated Future Fee","Estimated Future Order Handling Fee Per Order","Estimated Future Pick Pack Fee Per Unit","Estimated Future Weight Handling Fee Per Unit","Expected Future Fulfillment Fee Per Unit","Has Local Inventory","Expected Efn Fulfilment Fee Per Unit UK","Expected Efn Fulfilment Fee Per Unit DE","Expected Efn Fulfilment Fee Per Unit FR","Expected Efn Fulfilment Fee Per Unit IT","Expected Efn Fulfilment Fee Per Unit ES"));
        foreach($product as $prd)
        {
                                                                                                                     
		 $data=array($prd['sku'],$prd['fnsku'],$prd['asin'],$prd['product_name'],$prd['product_group'],$prd['brand'],$prd['fulfilled_by'],$prd['your_price'],$prd['sales_price'],$prd['longest_side'],$prd['median_side'],$prd['shortest_side'],$prd['length_and_girth'],$prd['unit_of_dimension'],$prd['item_package_weight'],$prd['unit_of_weight'],$prd['product_size_tier'],$prd['currency'],$prd['estimated_fee_total'],$prd['estimated_referral_fee_per_unit'],$prd['estimated_variable_closing_fee'],$prd['estimated_order_handling_fee_per_order'],$prd['estimated_pick_pack_fee_per_unit'],$prd['estimated_weight_handling_fee_per_unit'],$prd['expected_fulfillment_fee_per_unit'],$prd['estimated_future_fee'],$prd['estimated_future_order_handling_fee_per_order'],$prd['estimated_future_pick_pack_fee_per_unit'],$prd['estimated_future_weight_handling_fee_per_unit'],$prd['expected_future_fulfillment_fee_per_unit'],$prd['has_local_inventory'],$prd['expected_efn_fulfilment_fee_per_unit_uk'],$prd['expected_efn_fulfilment_fee_per_unit_de'],$prd['expected_efn_fulfilment_fee_per_unit_fr'],$prd['expected_efn_fulfilment_fee_per_unit_it'],$prd['expected_efn_fulfilment_fee_per_unit_es']);
         fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file, array("SKU","Fnsku","ASIN","Product Name","Product Group","Brand","Fulfilled By","Your Price","Sales Price","Longest Side","Median Side","Shortest Side","Length And Girth","Unit Of Dimension","Item Package Weight","Unit Of Weight","Product Size Tier","Currency","Estimated Fee Total","Estimated Referral Fee Per Unit","Estimated Variable Closing Fee","Estimated Order Handling Fee Per Order","Estimated Pick Pack Fee Per Unit","Estimated Weight Handling Fee Per Unit","Expected Fulfillment Fee Per Unit","Estimated Future Fee","Estimated Future Order Handling Fee Per Order","Estimated Future Pick Pack Fee Per Unit","Estimated Future Weight Handling Fee Per Unit","Expected Future Fulfillment Fee Per Unit","Has Local Inventory","Expected Efn Fulfilment Fee Per Unit UK","Expected Efn Fulfilment Fee Per Unit DE","Expected Efn Fulfilment Fee Per Unit FR","Expected Efn Fulfilment Fee Per Unit IT","Expected Efn Fulfilment Fee Per Unit ES"),"\t");
        foreach($product as $prd)
        {
		 $data=array($prd['sku'],$prd['fnsku'],$prd['asin'],$prd['product_name'],$prd['product_group'],$prd['brand'],$prd['fulfilled_by'],$prd['your_price'],$prd['sales_price'],$prd['longest_side'],$prd['median_side'],$prd['shortest_side'],$prd['length_and_girth'],$prd['unit_of_dimension'],$prd['item_package_weight'],$prd['unit_of_weight'],$prd['product_size_tier'],$prd['currency'],$prd['estimated_fee_total'],$prd['estimated_referral_fee_per_unit'],$prd['estimated_variable_closing_fee'],$prd['estimated_order_handling_fee_per_order'],$prd['estimated_pick_pack_fee_per_unit'],$prd['estimated_weight_handling_fee_per_unit'],$prd['expected_fulfillment_fee_per_unit'],$prd['estimated_future_fee'],$prd['estimated_future_order_handling_fee_per_order'],$prd['estimated_future_pick_pack_fee_per_unit'],$prd['estimated_future_weight_handling_fee_per_unit'],$prd['expected_future_fulfillment_fee_per_unit'],$prd['has_local_inventory'],$prd['expected_efn_fulfilment_fee_per_unit_uk'],$prd['expected_efn_fulfilment_fee_per_unit_de'],$prd['expected_efn_fulfilment_fee_per_unit_fr'],$prd['expected_efn_fulfilment_fee_per_unit_it'],$prd['expected_efn_fulfilment_fee_per_unit_es']);
         fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt')
		{
        fputcsv($feed_file, array("SKU","Fnsku","ASIN","Product Name","Product Group","Brand","Fulfilled By","Your Price","Sales Price","Longest Side","Median Side","Shortest Side","Length And Girth","Unit Of Dimension","Item Package Weight","Unit Of Weight","Product Size Tier","Currency","Estimated Fee Total","Estimated Referral Fee Per Unit","Estimated Variable Closing Fee","Estimated Order Handling Fee Per Order","Estimated Pick Pack Fee Per Unit","Estimated Weight Handling Fee Per Unit","Expected Fulfillment Fee Per Unit","Estimated Future Fee","Estimated Future Order Handling Fee Per Order","Estimated Future Pick Pack Fee Per Unit","Estimated Future Weight Handling Fee Per Unit","Expected Future Fulfillment Fee Per Unit","Has Local Inventory","Expected Efn Fulfilment Fee Per Unit UK","Expected Efn Fulfilment Fee Per Unit DE","Expected Efn Fulfilment Fee Per Unit FR","Expected Efn Fulfilment Fee Per Unit IT","Expected Efn Fulfilment Fee Per Unit ES"),"\t");
        foreach($product as $prd)
        {
		 $data=array($prd['sku'],$prd['fnsku'],$prd['asin'],$prd['product_name'],$prd['product_group'],$prd['brand'],$prd['fulfilled_by'],$prd['your_price'],$prd['sales_price'],$prd['longest_side'],$prd['median_side'],$prd['shortest_side'],$prd['length_and_girth'],$prd['unit_of_dimension'],$prd['item_package_weight'],$prd['unit_of_weight'],$prd['product_size_tier'],$prd['currency'],$prd['estimated_fee_total'],$prd['estimated_referral_fee_per_unit'],$prd['estimated_variable_closing_fee'],$prd['estimated_order_handling_fee_per_order'],$prd['estimated_pick_pack_fee_per_unit'],$prd['estimated_weight_handling_fee_per_unit'],$prd['expected_fulfillment_fee_per_unit'],$prd['estimated_future_fee'],$prd['estimated_future_order_handling_fee_per_order'],$prd['estimated_future_pick_pack_fee_per_unit'],$prd['estimated_future_weight_handling_fee_per_unit'],$prd['expected_future_fulfillment_fee_per_unit'],$prd['has_local_inventory'],$prd['expected_efn_fulfilment_fee_per_unit_uk'],$prd['expected_efn_fulfilment_fee_per_unit_de'],$prd['expected_efn_fulfilment_fee_per_unit_fr'],$prd['expected_efn_fulfilment_fee_per_unit_it'],$prd['expected_efn_fulfilment_fee_per_unit_es']);
         fputcsv($feed_file,$data,"\t");
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
		  $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."fba_estimated_fees_txt_data/download/".$hash_name;
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
