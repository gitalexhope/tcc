<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fba_inventory_health_data extends CI_Controller {
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
       $this->load->model('fba_inventory_health_data_model');
      
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/fba_inventory_health_data');
      $this->load->view('UI/footer');
  }
  
   public function get_product_list($orderby='snap_date',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->fba_inventory_health_data_model->get_product_list($orderby,$direction,$offet,$limit,$searchterm);
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
		
      $product=$this->fba_inventory_health_data_model->export_data($searchterm);
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
        $file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		
		if($status=='csv')
		{
        fputcsv($feed_file, array('SNAPSHOT-DATE','SKU','FNSKU','ASIN','PRODUCT-NAME','CONDITION','SALES-RANK','PRODUCT-GROUP','TOTAL-QUANTITY','SELLABLE-QUANTITY','UNSELLABLE-QUANTITY','INV-AGE-0-TO-90-DAYS','INV-AGE-91-TO-180-DAYS','INV-AGE-181-TO-270-DAYS','INV-AGE-271-TO-365-DAYS','INV-AGE-365-PLUS-DAYS','UNITS-SHIPPED-LAST-24-HRS','UNITS-SHIPPED-LAST-7-DAYS','UNITS-SHIPPED-LAST-30-DAYS','UNITS-SHIPPED-LAST-90-DAYS','UNITS-SHIPPED-LAST-180-DAYS','UNITS-SHIPPED-LAST-365-DAYS','WEEKS-OF-COVER-T7','WEEKS-OF-COVER-T30','WEEKS-OF-COVER-T90','WEEKS-OF-COVER-T180','WEEKS-OF-COVER-T365','NUM-AFN-NEW-SELLERS','NUM-AFN-USED-SELLERS','CURRENCY','YOUR-PRICE','SALES-PRICE','LOWEST-AFN-NEW-PRICE','LOWEST-AFN-USED-PRICE','LOWEST-MFN-NEW-PRICE','LOWEST-MFN-USED-PRICE','QTY-TO-BE-CHARGED-LTSF-12-MO','QTY-IN-LONG-TERM-STORAGE-PROGRAM','QTY-WITH-REMOVALS-IN-PROGRESS','PROJECTED-LTSF-12-MO','PER-UNIT-VOLUME','IS-HAZMAT','IN-BOUND-QUANTITY','ASIN-LIMIT','INBOUND-RECOMMEND-QUANTITY','QTY-TO-BE-CHARGED-LTSF-6-MO','PROJECTED-LTSF-6-MO'));
        foreach($product as $prd)
        {
                                                                                                                     
		 $data=array($prd['snap_date'],$prd['sku'],$prd['fn_sku'],$prd['asin'],$prd['prod_name'],$prd['prod_cond'],$prd['sales_rank'],$prd['prod_group'],$prd['total_qty'],$prd['sell_qty'],$prd['unsell_qty'],$prd['inv_age_0_to_90'],$prd['inv_age_91_to_180'],$prd['inv_age_181_to_270'],$prd['inv_age_271_to_365'],$prd['inv_age_365_plus'],$prd['unit_ship_24_hrs'],$prd['unit_ship_7_days'],$prd['unit_ship_30_days'],$prd['unit_ship_90_days'],$prd['unit_ship_180_days'],$prd['unit_ship_365_days'],$prd['weeks_of_cover_t7'],$prd['weeks_of_cover_t30'],$prd['weeks_of_cover_t90'],$prd['weeks_of_cover_t180'],$prd['weeks_of_cover_t365'],$prd['num_afn_new_sellers'],$prd['num_afn_user_sellers'],$prd['curr'],$prd['your_price'],$prd['sale_price'],$prd['low_afn_new_price'],$prd['low_afn_used_price'],$prd['low_mfn_new_price'],$prd['low_mfn_used_price'],$prd['qty_charged_12'],$prd['qty_charger_long_term'],$prd['qty_removal_in_progress'],$prd['projected_12'],$prd['per_unit_vol'],$prd['is_hazmat'],$prd['in_bound_qty'],$prd['asin_limit'],$prd['inbound_recomm_qty'],$prd['qty_charged_6'],$prd['projected_6']);
          fputcsv($feed_file,$data);
        }
		}
		if($status=='xls')
		{
        fputcsv($feed_file, array('SNAPSHOT-DATE','SKU','FNSKU','ASIN','PRODUCT-NAME','CONDITION','SALES-RANK','PRODUCT-GROUP','TOTAL-QUANTITY','SELLABLE-QUANTITY','UNSELLABLE-QUANTITY','INV-AGE-0-TO-90-DAYS','INV-AGE-91-TO-180-DAYS','INV-AGE-181-TO-270-DAYS','INV-AGE-271-TO-365-DAYS','INV-AGE-365-PLUS-DAYS','UNITS-SHIPPED-LAST-24-HRS','UNITS-SHIPPED-LAST-7-DAYS','UNITS-SHIPPED-LAST-30-DAYS','UNITS-SHIPPED-LAST-90-DAYS','UNITS-SHIPPED-LAST-180-DAYS','UNITS-SHIPPED-LAST-365-DAYS','WEEKS-OF-COVER-T7','WEEKS-OF-COVER-T30','WEEKS-OF-COVER-T90','WEEKS-OF-COVER-T180','WEEKS-OF-COVER-T365','NUM-AFN-NEW-SELLERS','NUM-AFN-USED-SELLERS','CURRENCY','YOUR-PRICE','SALES-PRICE','LOWEST-AFN-NEW-PRICE','LOWEST-AFN-USED-PRICE','LOWEST-MFN-NEW-PRICE','LOWEST-MFN-USED-PRICE','QTY-TO-BE-CHARGED-LTSF-12-MO','QTY-IN-LONG-TERM-STORAGE-PROGRAM','QTY-WITH-REMOVALS-IN-PROGRESS','PROJECTED-LTSF-12-MO','PER-UNIT-VOLUME','IS-HAZMAT','IN-BOUND-QUANTITY','ASIN-LIMIT','INBOUND-RECOMMEND-QUANTITY','QTY-TO-BE-CHARGED-LTSF-6-MO','PROJECTED-LTSF-6-MO'),"\t");
        foreach($product as $prd)
        {
		 $data=array($prd['snap_date'],$prd['sku'],$prd['fn_sku'],$prd['asin'],$prd['prod_name'],$prd['prod_cond'],$prd['sales_rank'],$prd['prod_group'],$prd['total_qty'],$prd['sell_qty'],$prd['unsell_qty'],$prd['inv_age_0_to_90'],$prd['inv_age_91_to_180'],$prd['inv_age_181_to_270'],$prd['inv_age_271_to_365'],$prd['inv_age_365_plus'],$prd['unit_ship_24_hrs'],$prd['unit_ship_7_days'],$prd['unit_ship_30_days'],$prd['unit_ship_90_days'],$prd['unit_ship_180_days'],$prd['unit_ship_365_days'],$prd['weeks_of_cover_t7'],$prd['weeks_of_cover_t30'],$prd['weeks_of_cover_t90'],$prd['weeks_of_cover_t180'],$prd['weeks_of_cover_t365'],$prd['num_afn_new_sellers'],$prd['num_afn_user_sellers'],$prd['curr'],$prd['your_price'],$prd['sale_price'],$prd['low_afn_new_price'],$prd['low_afn_used_price'],$prd['low_mfn_new_price'],$prd['low_mfn_used_price'],$prd['qty_charged_12'],$prd['qty_charger_long_term'],$prd['qty_removal_in_progress'],$prd['projected_12'],$prd['per_unit_vol'],$prd['is_hazmat'],$prd['in_bound_qty'],$prd['asin_limit'],$prd['inbound_recomm_qty'],$prd['qty_charged_6'],$prd['projected_6']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt')
		{
        fputcsv($feed_file, array('SNAPSHOT-DATE','SKU','FNSKU','ASIN','PRODUCT-NAME','CONDITION','SALES-RANK','PRODUCT-GROUP','TOTAL-QUANTITY','SELLABLE-QUANTITY','UNSELLABLE-QUANTITY','INV-AGE-0-TO-90-DAYS','INV-AGE-91-TO-180-DAYS','INV-AGE-181-TO-270-DAYS','INV-AGE-271-TO-365-DAYS','INV-AGE-365-PLUS-DAYS','UNITS-SHIPPED-LAST-24-HRS','UNITS-SHIPPED-LAST-7-DAYS','UNITS-SHIPPED-LAST-30-DAYS','UNITS-SHIPPED-LAST-90-DAYS','UNITS-SHIPPED-LAST-180-DAYS','UNITS-SHIPPED-LAST-365-DAYS','WEEKS-OF-COVER-T7','WEEKS-OF-COVER-T30','WEEKS-OF-COVER-T90','WEEKS-OF-COVER-T180','WEEKS-OF-COVER-T365','NUM-AFN-NEW-SELLERS','NUM-AFN-USED-SELLERS','CURRENCY','YOUR-PRICE','SALES-PRICE','LOWEST-AFN-NEW-PRICE','LOWEST-AFN-USED-PRICE','LOWEST-MFN-NEW-PRICE','LOWEST-MFN-USED-PRICE','QTY-TO-BE-CHARGED-LTSF-12-MO','QTY-IN-LONG-TERM-STORAGE-PROGRAM','QTY-WITH-REMOVALS-IN-PROGRESS','PROJECTED-LTSF-12-MO','PER-UNIT-VOLUME','IS-HAZMAT','IN-BOUND-QUANTITY','ASIN-LIMIT','INBOUND-RECOMMEND-QUANTITY','QTY-TO-BE-CHARGED-LTSF-6-MO','PROJECTED-LTSF-6-MO'),"\t");
        foreach($product as $prd)
        {
		 $data=array($prd['snap_date'],$prd['sku'],$prd['fn_sku'],$prd['asin'],$prd['prod_name'],$prd['prod_cond'],$prd['sales_rank'],$prd['prod_group'],$prd['total_qty'],$prd['sell_qty'],$prd['unsell_qty'],$prd['inv_age_0_to_90'],$prd['inv_age_91_to_180'],$prd['inv_age_181_to_270'],$prd['inv_age_271_to_365'],$prd['inv_age_365_plus'],$prd['unit_ship_24_hrs'],$prd['unit_ship_7_days'],$prd['unit_ship_30_days'],$prd['unit_ship_90_days'],$prd['unit_ship_180_days'],$prd['unit_ship_365_days'],$prd['weeks_of_cover_t7'],$prd['weeks_of_cover_t30'],$prd['weeks_of_cover_t90'],$prd['weeks_of_cover_t180'],$prd['weeks_of_cover_t365'],$prd['num_afn_new_sellers'],$prd['num_afn_user_sellers'],$prd['curr'],$prd['your_price'],$prd['sale_price'],$prd['low_afn_new_price'],$prd['low_afn_used_price'],$prd['low_mfn_new_price'],$prd['low_mfn_used_price'],$prd['qty_charged_12'],$prd['qty_charger_long_term'],$prd['qty_removal_in_progress'],$prd['projected_12'],$prd['per_unit_vol'],$prd['is_hazmat'],$prd['in_bound_qty'],$prd['asin_limit'],$prd['inbound_recomm_qty'],$prd['qty_charged_6'],$prd['projected_6']);
          fputcsv($feed_file,$data,"\t");
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
		  $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."fba_inventory_health_data/download/".$hash_name;
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
