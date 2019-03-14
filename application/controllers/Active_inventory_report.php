<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Active_inventory_report extends CI_Controller {
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
       $this->load->model('active_inventory_report_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/active_inventory_report');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='prod_id',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->active_inventory_report_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->active_inventory_report_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array("Item_Name","Item Description","Listing Id","Seller Sku","Price","Quantity","Open Date","Image Url","Item Is Marketplace","Product Id Type","Zshop Shipping Fee","Item Note","Item Condition","Zshop Category1","Zshop Browse Path","Zshop Storefront Feature","Asin1","Asin2","Asin3","Will Ship Internationally","Expedited Shipping","Zshop Boldface","Product Id","Bid For Featured Placement","Add Delete","Pending Quantity","Fulfillment Channel","Business Price","Quantity Price Type","Quantity Lower Bound 1","Quantity Price 1","Quantity Lower Bound 2","Quantity Price 2","Quantity Lower Bound 3","Quantity Price 3","Quantity Lower Bound 4","Quantity Price 4","Quantity Lower Bound 5","Quantity Price 5","Merchant Shipping Group","Country"));
        foreach($product as $prd)
        {
                                                                                                                     
		 $data=array($prd['item_name'],$prd['item_description'],$prd['listing_id'],$prd['seller_sku'],$prd['price'],$prd['quantity'],$prd['open_date'],$prd['image_url'],$prd['item_is_marketplace'],$prd['product_id_type'],$prd['zshop_shipping_fee'],$prd['item_note'],$prd['item_condition'],$prd['zshop_category1'],$prd['zshop_browse_path'],$prd['zshop_storefront_feature'],$prd['asin1'],$prd['asin2'],$prd['asin3'],$prd['will_ship_internationally'],$prd['expedited_shipping'],$prd['zshop_boldface'],$prd['product_id'],$prd['bid_for_featured_placement'],$prd['add_delete'],$prd['pending_quantity'],$prd['fulfillment_channel'],$prd['business_price'],$prd['quantity_price_type'],$prd['quantity_lower_bound_1'],$prd['quantity_price_1'],$prd['quantity_lower_bound_2'],$prd['quantity_price_2'],$prd['quantity_lower_bound_3'],$prd['quantity_price_3'],$prd['quantity_lower_bound_4'],$prd['quantity_price_4'],$prd['quantity_lower_bound_5'],$prd['quantity_price_5'],$prd['merchant_shipping_group'],$prd['country']);
          fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file, array("Item_Name","Item Description","Listing Id","Seller Sku","Price","Quantity","Open Date","Image Url","Item Is Marketplace","Product Id Type","Zshop Shipping Fee","Item Note","Item Condition","Zshop Category1","Zshop Browse Path","Zshop Storefront Feature","Asin1","Asin2","Asin3","Will Ship Internationally","Expedited Shipping","Zshop Boldface","Product Id","Bid For Featured Placement","Add Delete","Pending Quantity","Fulfillment Channel","Business Price","Quantity Price Type","Quantity Lower Bound 1","Quantity Price 1","Quantity Lower Bound 2","Quantity Price 2","Quantity Lower Bound 3","Quantity Price 3","Quantity Lower Bound 4","Quantity Price 4","Quantity Lower Bound 5","Quantity Price 5","Merchant Shipping Group","Country"),"\t");
        foreach($product as $prd)
        {
		 $data=array($prd['item_name'],$prd['item_description'],$prd['listing_id'],$prd['seller_sku'],$prd['price'],$prd['quantity'],$prd['open_date'],$prd['image_url'],$prd['item_is_marketplace'],$prd['product_id_type'],$prd['zshop_shipping_fee'],$prd['item_note'],$prd['item_condition'],$prd['zshop_category1'],$prd['zshop_browse_path'],$prd['zshop_storefront_feature'],$prd['asin1'],$prd['asin2'],$prd['asin3'],$prd['will_ship_internationally'],$prd['expedited_shipping'],$prd['zshop_boldface'],$prd['product_id'],$prd['bid_for_featured_placement'],$prd['add_delete'],$prd['pending_quantity'],$prd['fulfillment_channel'],$prd['business_price'],$prd['quantity_price_type'],$prd['quantity_lower_bound_1'],$prd['quantity_price_1'],$prd['quantity_lower_bound_2'],$prd['quantity_price_2'],$prd['quantity_lower_bound_3'],$prd['quantity_price_3'],$prd['quantity_lower_bound_4'],$prd['quantity_price_4'],$prd['quantity_lower_bound_5'],$prd['quantity_price_5'],$prd['merchant_shipping_group'],$prd['country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt')
		{
        fputcsv($feed_file, array("Item_Name","Item Description","Listing Id","Seller Sku","Price","Quantity","Open Date","Image Url","Item Is Marketplace","Product Id Type","Zshop Shipping Fee","Item Note","Item Condition","Zshop Category1","Zshop Browse Path","Zshop Storefront Feature","Asin1","Asin2","Asin3","Will Ship Internationally","Expedited Shipping","Zshop Boldface","Product Id","Bid For Featured Placement","Add Delete","Pending Quantity","Fulfillment Channel","Business Price","Quantity Price Type","Quantity Lower Bound 1","Quantity Price 1","Quantity Lower Bound 2","Quantity Price 2","Quantity Lower Bound 3","Quantity Price 3","Quantity Lower Bound 4","Quantity Price 4","Quantity Lower Bound 5","Quantity Price 5","Merchant Shipping Group","Country"),"\t");
        foreach($product as $prd)
        {
		 $data=array($prd['item_name'],$prd['item_description'],$prd['listing_id'],$prd['seller_sku'],$prd['price'],$prd['quantity'],$prd['open_date'],$prd['image_url'],$prd['item_is_marketplace'],$prd['product_id_type'],$prd['zshop_shipping_fee'],$prd['item_note'],$prd['item_condition'],$prd['zshop_category1'],$prd['zshop_browse_path'],$prd['zshop_storefront_feature'],$prd['asin1'],$prd['asin2'],$prd['asin3'],$prd['will_ship_internationally'],$prd['expedited_shipping'],$prd['zshop_boldface'],$prd['product_id'],$prd['bid_for_featured_placement'],$prd['add_delete'],$prd['pending_quantity'],$prd['fulfillment_channel'],$prd['business_price'],$prd['quantity_price_type'],$prd['quantity_lower_bound_1'],$prd['quantity_price_1'],$prd['quantity_lower_bound_2'],$prd['quantity_price_2'],$prd['quantity_lower_bound_3'],$prd['quantity_price_3'],$prd['quantity_lower_bound_4'],$prd['quantity_price_4'],$prd['quantity_lower_bound_5'],$prd['quantity_price_5'],$prd['merchant_shipping_group'],$prd['country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
		  $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."active_inventory_report/download/".$hash_name;
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
