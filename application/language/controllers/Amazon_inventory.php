<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Amazon_inventory extends CI_Controller {
  private $user_id;
  public function  __construct()
  {
     parent::__construct();
     if(!$this->login_model->userLoginCheck() && !$this->input->is_ajax_request())
     {
      redirect('user_auth');
     }
     
      else
      {
        $this->load->model("amazon_inventory_model");    
        $user=$this->session->userdata('user_logged_in');  
        $this->user_id=$user['id'];
       
      }
       
  }

  public function index()
  {
    $this->load->view('UI/header');
    $this->load->view('UI/amazon_inventory');
    $this->load->view('UI/footer');
  }
  public function get_inventory_list($orderby='',$direction='',$offet,$limit,$searchterm='')
  {
      $orderby=$orderby=='GEN'?'':$orderby;
      $result_set=$this->amazon_inventory_model->get_inventory_list($orderby,$direction,$offet,$limit,$searchterm);
      echo json_encode($result_set);
  }
  public function get_order_list($cnt_asin,$cnt_sku,$orderby='purchase_date',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->amazon_inventory_model->get_order_list($cnt_asin,$cnt_sku,$orderby,$direction,$offet,$limit,$searchterm);
      // $result_set['outstanding']=$this->amazon_transaction_model->get_outstanding_bill();
      echo json_encode($result_set);
  }
  
  public function get_graph_data()
  {
    if(!empty($_POST['product_sku']))
    {
        $data['status_code']=1;
        $data['status_text']='Success';
        $sql="SELECT DATE(purchase_date) AS order_date,sum(no_of_itm_shipped) as order_count FROM order_transaction_list as tnx
        INNER JOIN order_items_transaction_list as itm on itm.ssg_tn=tnx.ssg_tn and itm.seller_sku=".$this->db->escape($_POST['product_sku']);
        $sql.=" GROUP BY order_date";
        $query=$this->db->query($sql);

        $data['payload']=$query->result_array();
        
        echo json_encode($data);
    }
  }

  
  public function update_product_info()
  {
    if(!empty($_POST['product_title']) && !empty($_POST['product_asin']) && is_numeric($_POST['actual_price']) && is_numeric($_POST['selling_price']))
    {
        $amazon_fees=$this->input->post('selling_price')*(15/100);
        $amazon_fee_deducted=$this->input->post('selling_price')-$amazon_fees;
        $earnings=$amazon_fee_deducted-$this->input->post('actual_price');
        if(!empty($_POST['prod_id']))
        {
          if($this->db->query("Update customer_product SET act_price=".$this->db->escape($_POST['actual_price']).",profit=".$this->db->escape($earnings)." WHERE prod_id=".$this->db->escape($_POST['prod_id'])))
          {
            $data=array('status_code'=>'1','status_text'=>'Successfully updated');
            echo json_encode($data); 
          }
          else
          {
            $data=array('status_code'=>'0','status_text'=>'Something went wrong');
            echo json_encode($data); 
          }
        }  
        else
        {
             $insert_product=array('prod_title'=>$this->input->post('product_title'),
                               'prod_asin'=>$this->input->post('product_asin'),
                               'prod_sku'=>$this->input->post('product_sku'),
                               'act_price'=>$this->input->post('actual_price'), 
                               'itm_price'=>$this->input->post('selling_price'),
                               'profit'=>$earnings,
                               'added_by'=>$this->user_id
                              );
            if($this->db->insert('customer_product',$insert_product))
            {
              $data=array('status_code'=>'1','status_text'=>'Successfully added');
              echo json_encode($data); 
            }
            else
            {
              $data=array('status_code'=>'0','status_text'=>'Something went wrong');
              echo json_encode($data); 
            }  
        }

    }
    else
    {
      $data=array('status_code'=>'0','status_text'=>'Data input error please check it');
      echo json_encode($data); 
    }
  }


  public function sync_inventory()
  {
    $this->load->model('new_cron/process_report_model','report_api');
    $users=$this->report_api->get_seller_for_process($this->user_id);
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {
        $this->report_api->set_credentials($usr);
        $res=$this->report_api->request_report($usr['profile_id'],'_GET_MERCHANT_LISTINGS_DATA_',30);
        $res=$this->report_api->request_report($usr['profile_id'],'_GET_MERCHANT_LISTINGS_INACTIVE_DATA_',30);
        $res=$this->report_api->request_report($usr['profile_id'],'_GET_AFN_INVENTORY_DATA_',30);
      }
    }
    echo '{"status_code":"1","status_text":"Inventory will be synced by 15 minutes"}';   
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

         if(isset($str[3]->order_status))
         {
           if($str[3]->order_status == 'CSV')
           $status='csv';
           elseif($str[3]->order_status == 'TXT')
           $status='txt';
           elseif($str[3]->order_status == 'XLS')
           $status='xls'; 
         }
		
      $product=$this->amazon_inventory_model->export_data($searchterm);
	 // print_r($product);
	  //die();
      if(!empty($product))
      {
        $hash_name=md5(microtime().rand(1,1000)).".".$status;
		$file_path=realpath('asset').DIRECTORY_SEPARATOR."exportdata".DIRECTORY_SEPARATOR.$hash_name;
        $feed_file = fopen($file_path, 'w');
		if($status=='xls') 
		{
       fputcsv($feed_file, array('Title','Brand','ASIN','SKU','Image','Item Price','Quantity','Fullfillment','FC code','Condition','Country'),"\t");
		

		foreach($product as $prd)
        {
         
          $data=array($prd['prod_title'],$prd['prod_brand'],$prd['prod_asin'],$prd['prod_sku'],$prd['prod_image'],$prd['itm_price'],$prd['itm_qty'],$prd['fullfillment_type'],$prd['fc_code'],$prd['itm_condition'],$prd['prod_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='txt') 
		{
       fputcsv($feed_file, array('Title','Brand','ASIN','SKU','Image','Item Price','Quantity','Fullfillment','FC code','Condition','Country'),"\t");
		

		foreach($product as $prd)
        {
         
          $data=array($prd['prod_title'],$prd['prod_brand'],$prd['prod_asin'],$prd['prod_sku'],$prd['prod_image'],$prd['itm_price'],$prd['itm_qty'],$prd['fullfillment_type'],$prd['fc_code'],$prd['itm_condition'],$prd['prod_country']);
          fputcsv($feed_file,$data,"\t");
        }
		}
		if($status=='csv') 
		{
       fputcsv($feed_file, array('Title','Brand','ASIN','SKU','Image','Item Price','Quantity','Fullfillment','FC code','Condition','Country'));
		

		foreach($product as $prd)
        {
         
          $data=array($prd['prod_title'],$prd['prod_brand'],$prd['prod_asin'],$prd['prod_sku'],$prd['prod_image'],$prd['itm_price'],$prd['itm_qty'],$prd['fullfillment_type'],$prd['fc_code'],$prd['itm_condition'],$prd['prod_country']);
          fputcsv($feed_file,$data);
        }
		}
        fclose($feed_file);
        if(is_file($file_path))
        {
          $data['status_code']=1;
          $data['status_text']='Success';
          $data['exported_file']=$hash_name;
          $data['download_url']=base_url()."amazon_inventory/download/".$hash_name;
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
     header('Content-Type: text/csv');
     header('Content-Transfer-Encoding: binary');
     header('Expires: 0');
     header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
     header('Pragma: public'); 
	 readfile($abs_path);
   }

 
 
}

