<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Amazon_order_api extends CI_Model
{
  private $seller_id='';
  private $auth_token='';
  private $access_key='';
  private $secret_key='';
  private $market_id='';  
  private $service_url='mws.amazonservices.it';
  private $market_code='AMZ';
  private $number_of_days_for_new='-30 minutes';
  // private $number_of_days_for_new='-15 days';
  private $number_of_days_for_update='-15 days';
  public function  __construct()
  {
      parent::__construct();
  }

  public function set_credentials($user_id,$seller_id,$auth_token,$access_key='AKIAJ2TKJHD3ASZVXHSA',$secret_key='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',$market_placeID='APJ6JRA9NG5V4',$get_from='30 minutes',$update_from='15 days')
  {
        $this->seller_id=$seller_id;
        $this->auth_token=$auth_token;
        $this->access_key=$access_key;
        $this->secret_key=$secret_key;
        $this->market_id=$market_placeID;  
        $this->number_of_days_for_new="-".$get_from;
        $this->number_of_days_for_update="-".$update_from;
        return TRUE;
  }




  public function fetch_order_details($user_id,$fetch_type='NEW')
  { 
    // echo "FETCH STARTED:\n";
    echo $this->number_of_days_for_new."\n";
    echo $this->number_of_days_for_update."\n";
    try
    {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param['Action']=urlencode("ListOrders");
      if($fetch_type=='NEW')
      {
        $time_frame=$this->number_of_days_for_new;
        $qry=$this->db->query("SELECT count(*) as ttl FROM order_transaction_list WHERE customer_id=".$user_id);
        $res=$qry->result_array();
        if(empty($res) || $res[0]['ttl']==0 || count($res)==0)
        {
            $time_frame="-7 days";
        }
        $param['CreatedAfter']=  gmdate('Y-m-d\TH:i:s\Z',strtotime($time_frame));
      }
      else
      {
        $param['LastUpdatedAfter']=  gmdate('Y-m-d\TH:i:s\Z',strtotime($this->number_of_days_for_update));
      }
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      curl_setopt($ch, CURLOPT_POST, true);
      $response = curl_exec($ch);
      if(curl_errno($ch))
      {
          throw new Exception(curl_error($ch));
      }
      $res = simplexml_load_string($response);
      // print_r($res);
     
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if($httpcode != 200)
      {
        if(preg_match('/throttled/',(string)$res->Error->Message ))
        {
          sleep(60);
          echo "throttling occured;\n";
          $this->fetch_order_details($user_id,$fetch_type);
        }
        else
        {
          throw new Exception($res->Error->Message);  
        }
      }
      $data['status_code']=1;
      $total_order=isset($res->ListOrdersResult->Orders->Order)?count($res->ListOrdersResult->Orders->Order):0;
      if($total_order > 0 && $fetch_type=='NEW')
      {
        $data['status_text']=$this->process_order($total_order,$res->ListOrdersResult->Orders->Order,$user_id);
      }
      elseif($total_order > 0 && $fetch_type=="UPDATE")
      {
       $data['status_text']=$this->update_order($total_order,$res->ListOrdersResult->Orders->Order,$user_id); 
      }
      else
      {
         $data['status_code']=1;
         $data['status_text']=$response;
      }
      
      
      if(isset($res->ListOrdersResult->NextToken))
      {
        $data=$this->fetch_order_details_by_token($user_id,(string)$res->ListOrdersResult->NextToken,$fetch_type);
      }
      return $data;
    }
    catch(Exception $e) 
    {
      
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }

 public function fetch_order_details_by_token($user_id,$token,$fetch_type='NEW')
  { 
    echo "ByToken\n";
    sleep(15);
    try
    {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param['Action']=urlencode("ListOrdersByNextToken");
      $param['NextToken']=  $token;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      curl_setopt($ch, CURLOPT_POST, true);
      $response = curl_exec($ch);
      
      if(curl_errno($ch))
      {
          throw new Exception(curl_error($ch));
      }
      $res = simplexml_load_string($response);
        
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if($httpcode != 200)
      {
        if(preg_match('/throttled/',(string)$res->Error->Message ))
        {
          sleep(60);
          echo "throttling occured;\n";
          $this->fetch_order_details_by_token($user_id,$token,$fetch_type);
        }
        else
        {
          throw new Exception($res->Error->Message);  
        }
      }
      $data['status_code']=1;
      $data['status_text']="Success";
      
      $total_order=isset($res->ListOrdersByNextTokenResult->Orders->Order)?count($res->ListOrdersByNextTokenResult->Orders->Order):0;
      // if($total_order > 0)
      // {
      //   $data['status_text']=$this->process_order($total_order,$res->ListOrdersByNextTokenResult->Orders->Order,$user_id);
      // }
      if($total_order > 0 && $fetch_type=='NEW')
      {
        $data['status_text']=$this->process_order($total_order,$res->ListOrdersByNextTokenResult->Orders->Order,$user_id);
      }
      elseif($total_order > 0 && $fetch_type=="UPDATE")
      {
       $data['status_text']=$this->update_order($total_order,$res->ListOrdersByNextTokenResult->Orders->Order,$user_id); 
      }
      else
      {
         $data['status_code']=1;
         $data['status_text']=$response;
      }
      
      if(isset($res->ListOrdersByNextTokenResult->NextToken))
      {
        $data=$this->fetch_order_details_by_token($user_id,(string)$res->ListOrdersByNextTokenResult->NextToken,$fetch_type);
      }
      $data['payload']=$res;
      return $data;
    }
    catch(Exception $e) 
    {
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }
 private function process_order($total_order,$order_details,$user_id)
 {
      $rows=[];
      for($i=0;$i<$total_order;$i++)
      {
         $amz_order_id=(string)$order_details[$i]->AmazonOrderId;
         $buyer_name=isset($order_details[$i]->BuyerName)?(string)$order_details[$i]->BuyerName:"";
         $buyer_email=isset($order_details[$i]->BuyerEmail)?(string)$order_details[$i]->BuyerEmail:"";
         $order_status=isset($order_details[$i]->OrderStatus)?(string)$order_details[$i]->OrderStatus:"";
         $order_type=isset($order_details[$i]->OrderType)?(string)$order_details[$i]->OrderType:"";
         $order_total_amt=isset($order_details[$i]->OrderTotal->Amount)?(string)$order_details[$i]->OrderTotal->Amount:0;
         $currency_code=isset($order_details[$i]->OrderTotal->CurrencyCode)?(string)$order_details[$i]->OrderTotal->CurrencyCode:"";
         $purchase_date=isset($order_details[$i]->PurchaseDate)?(string)$order_details[$i]->PurchaseDate:"";
         $no_of_item_shipped=isset($order_details[$i]->NumberOfItemsShipped)?(string)$order_details[$i]->NumberOfItemsShipped:0;
         $no_of_item_unshipped=isset($order_details[$i]->NumberOfItemsUnshipped)?(string)$order_details[$i]->NumberOfItemsUnshipped:0;
         $order_last_update=isset($order_details[$i]->LastUpdateDate)?(string)$order_details[$i]->LastUpdateDate:"";
         $early_ship_date=isset($order_details[$i]->EarliestShipDate)?(string)$order_details[$i]->EarliestShipDate:"";
         $early_deliver_date=isset($order_details[$i]->EarliestDeliveryDate)?(string)$order_details[$i]->EarliestDeliveryDate:"";
         $shipping_country=isset($order_details[$i]->ShippingAddress->CountryCode)?(string)$order_details[$i]->ShippingAddress->CountryCode:"";
         $shipping_state=isset($order_details[$i]->ShippingAddress->StateOrRegion)?(string)$order_details[$i]->ShippingAddress->StateOrRegion:"";
         $shipping_city=isset($order_details[$i]->ShippingAddress->City)?(string)$order_details[$i]->ShippingAddress->City:"";
         $shipping_zipcode=isset($order_details[$i]->ShippingAddress->PostalCode)?(string)$order_details[$i]->ShippingAddress->PostalCode:"";
         $shipping_address1=isset($order_details[$i]->ShippingAddress->AddressLine1)?(string)$order_details[$i]->ShippingAddress->AddressLine1:"";
         $shipping_address2=isset($order_details[$i]->ShippingAddress->AddressLine2)?(string)$order_details[$i]->ShippingAddress->AddressLine2:"";
         $shipping_phone=isset($order_details[$i]->ShippingAddress->Phone)?(string)$order_details[$i]->ShippingAddress->Phone:"";
         $shipping_name=isset($order_details[$i]->ShippingAddress->Name)?(string)$order_details[$i]->ShippingAddress->Name:"";
         $tfm_status=isset($order_details[$i]->TFMShipmentStatus)?(string)$order_details[$i]->TFMShipmentStatus:"";
         echo $user_id." ".$amz_order_id." ".$tfm_status." ".$order_total_amt."\n";
         $rows[]="('".$this->market_code."',".$this->db->escape($amz_order_id).",".$this->db->escape($purchase_date).",".$this->db->escape($buyer_name).",".$this->db->escape($buyer_email).",".$this->db->escape($shipping_country).",".$this->db->escape($shipping_state).",".$this->db->escape($shipping_city).",".$this->db->escape($shipping_zipcode).",".$this->db->escape($shipping_name).",".$this->db->escape($shipping_phone).",".$this->db->escape($shipping_address1).",".$this->db->escape($shipping_address2).",".$this->db->escape($early_ship_date).",".$this->db->escape($early_deliver_date).",".$this->db->escape($order_total_amt).",".$this->db->escape($currency_code).",".$this->db->escape($order_status).",".$this->db->escape($no_of_item_shipped).",".$this->db->escape($no_of_item_unshipped).",".$this->db->escape($tfm_status).",".$this->db->escape($order_last_update).",".$user_id.")";

      }
      if(!empty($rows) && count($rows) > 0)
      {
           $quer=implode(',' ,$rows);
           $query="INSERT INTO `order_transaction_list` (
          `market_code` ,
          `order_no` ,
          `purchase_date` ,
          `buyer_name` ,
          `buyer_email` ,
          `shipping_country` ,
          `shipping_state` ,
          `shipping_city`,
          `shipping_zip` ,
          `shipping_name` ,
          `shipping_phone` ,
          `shipping_addr1` ,
          `shipping_addr2` ,
          `exp_ship_date` ,
          `deliver_by_date` ,
          `order_total` ,
          `currency_code` ,
          `order_status`,
          `no_of_itm_shipped` ,
          `no_of_itm_unshipped` ,
          `order_tfmstatus`,
          `last_updated`,
          `customer_id`
            )
            VALUES 
            $quer 
            ON DUPLICATE KEY 
            UPDATE 
            market_code= VALUES(market_code),
            order_no= VALUES(order_no),
            purchase_date= VALUES(purchase_date),
            buyer_name= VALUES(buyer_name),
            buyer_email= VALUES(buyer_email),
            shipping_country= VALUES(shipping_country),
            shipping_state= VALUES(shipping_state),
            shipping_city = VALUES(shipping_city),
            shipping_zip = VALUES(shipping_zip),
            shipping_name = VALUES(shipping_name),
            shipping_phone= VALUES(shipping_phone),
            shipping_addr1= VALUES(shipping_addr1),
            shipping_addr2= VALUES(shipping_addr2),
            exp_ship_date = VALUES(exp_ship_date),
            deliver_by_date= VALUES(deliver_by_date),
            order_total= VALUES(order_total),
            currency_code= VALUES(currency_code),
            order_status= VALUES(order_status),
            no_of_itm_shipped= VALUES(no_of_itm_shipped),
            no_of_itm_unshipped= VALUES(no_of_itm_unshipped),
            order_tfmstatus=VALUES(order_tfmstatus),  
            last_updated= VALUES(last_updated),
            customer_id= VALUES(customer_id);";
            $this->db->query($query);
      }
      return $rows;
          
 }
 private function update_order($total_order,$order_details,$user_id)
 {
      $rows=[];
      for($i=0;$i<$total_order;$i++)
      {
         $amz_order_id=(string)$order_details[$i]->AmazonOrderId;
         $buyer_name=isset($order_details[$i]->BuyerName)?(string)$order_details[$i]->BuyerName:"";
         $buyer_email=isset($order_details[$i]->BuyerEmail)?(string)$order_details[$i]->BuyerEmail:"";
         $order_status=isset($order_details[$i]->OrderStatus)?(string)$order_details[$i]->OrderStatus:"";
         $order_type=isset($order_details[$i]->OrderType)?(string)$order_details[$i]->OrderType:"";
         $order_total_amt=isset($order_details[$i]->OrderTotal->Amount)?(string)$order_details[$i]->OrderTotal->Amount:0;
         $currency_code=isset($order_details[$i]->OrderTotal->CurrencyCode)?(string)$order_details[$i]->OrderTotal->CurrencyCode:"";
         $purchase_date=isset($order_details[$i]->PurchaseDate)?(string)$order_details[$i]->PurchaseDate:"";
         $no_of_item_shipped=isset($order_details[$i]->NumberOfItemsShipped)?(string)$order_details[$i]->NumberOfItemsShipped:0;
         $no_of_item_unshipped=isset($order_details[$i]->NumberOfItemsUnshipped)?(string)$order_details[$i]->NumberOfItemsUnshipped:0;
         $order_last_update=isset($order_details[$i]->LastUpdateDate)?(string)$order_details[$i]->LastUpdateDate:"";
         $early_ship_date=isset($order_details[$i]->EarliestShipDate)?(string)$order_details[$i]->EarliestShipDate:"";
         $early_deliver_date=isset($order_details[$i]->EarliestDeliveryDate)?(string)$order_details[$i]->EarliestDeliveryDate:"";
         
         $latest_ship_date=isset($order_details[$i]->LatestShipDate)?(string)$order_details[$i]->LatestShipDate:"";
         $latest_deliver_date=isset($order_details[$i]->LatestDeliveryDate)?(string)$order_details[$i]->LatestDeliveryDate:"";
         $pay_method=isset($order_details[$i]->PaymentMethod)?(string)$order_details[$i]->PaymentMethod:"";
         $is_prime=isset($order_details[$i]->IsPrime)?(string)$order_details[$i]->IsPrime:"";
         $is_premium=isset($order_details[$i]->IsPremiumOrder)?(string)$order_details[$i]->IsPremiumOrder:"";
         $ful_channel=isset($order_details[$i]->FulfillmentChannel)?(string)$order_details[$i]->FulfillmentChannel:"";
         $ship_cat=isset($order_details[$i]->ShipmentServiceLevelCategory)?(string)$order_details[$i]->ShipmentServiceLevelCategory:"";
         
         

         
         $shipping_country=isset($order_details[$i]->ShippingAddress->CountryCode)?(string)$order_details[$i]->ShippingAddress->CountryCode:"";
         $shipping_state=isset($order_details[$i]->ShippingAddress->StateOrRegion)?(string)$order_details[$i]->ShippingAddress->StateOrRegion:"";
         $shipping_city=isset($order_details[$i]->ShippingAddress->City)?(string)$order_details[$i]->ShippingAddress->City:"";
         $shipping_zipcode=isset($order_details[$i]->ShippingAddress->PostalCode)?(string)$order_details[$i]->ShippingAddress->PostalCode:"";
         $shipping_address1=isset($order_details[$i]->ShippingAddress->AddressLine1)?(string)$order_details[$i]->ShippingAddress->AddressLine1:"";
         $shipping_address2=isset($order_details[$i]->ShippingAddress->AddressLine2)?(string)$order_details[$i]->ShippingAddress->AddressLine2:"";
         $shipping_phone=isset($order_details[$i]->ShippingAddress->Phone)?(string)$order_details[$i]->ShippingAddress->Phone:"";
         $shipping_name=isset($order_details[$i]->ShippingAddress->Name)?(string)$order_details[$i]->ShippingAddress->Name:"";
         $tfm_status=isset($order_details[$i]->TFMShipmentStatus)?(string)$order_details[$i]->TFMShipmentStatus:"";
         
         echo $amz_order_id." ".$purchase_date." ".$latest_ship_date." ".$latest_deliver_date."\n";
         $rows[]="(".$this->db->escape($amz_order_id).",".$this->db->escape($order_status).",".$this->db->escape($latest_deliver_date).",".$this->db->escape($latest_ship_date).",".$this->db->escape($tfm_status).",".$this->db->escape($ship_cat).",".$user_id.")";
         $sql="UPDATE order_transaction_list 
               SET order_status=".$this->db->escape($order_status).",
               purchase_date=".$this->db->escape($purchase_date).",
               buyer_name=".$this->db->escape($buyer_name).",
               buyer_email=".$this->db->escape($buyer_email).",
               exp_ship_date=".$this->db->escape($early_ship_date).",
               deliver_by_date=".$this->db->escape($early_deliver_date).",
               lst_ship_date=".$this->db->escape($latest_ship_date).",
               lst_delive_date=".$this->db->escape($latest_deliver_date).",
               fulfillment_channel=".$this->db->escape($ful_channel).",
               shipservice_lvl_category=".$this->db->escape($ship_cat).",
               payment_method=".$this->db->escape($pay_method).",
               is_premium_order=".$this->db->escape($is_premium).",
               is_prime=".$this->db->escape($is_prime).",
               order_total=".$this->db->escape($order_total_amt).",
               currency_code=".$this->db->escape($currency_code).",
               shipping_country=".$this->db->escape($shipping_country).",
               shipping_state=".$this->db->escape($shipping_state).",
               shipping_city=".$this->db->escape($shipping_city).",
               shipping_zip=".$this->db->escape($shipping_zipcode).",
               shipping_name=".$this->db->escape($shipping_name).",
               shipping_phone=".$this->db->escape($shipping_phone).",
               shipping_addr1=".$this->db->escape($shipping_address1).",
               shipping_addr2=".$this->db->escape($shipping_address2).",
               no_of_itm_shipped=".$this->db->escape($no_of_item_shipped).",
               no_of_itm_unshipped=".$this->db->escape($no_of_item_unshipped).",
               last_updated=".$this->db->escape($order_last_update).",
               order_tfmstatus=".$this->db->escape($tfm_status)."
               WHERE customer_id=".$user_id." AND order_no=".$this->db->escape($amz_order_id);
         $this->db->query($sql);
         
    }
    return  $rows;
 }
 public function fetch_order_item_details($user_id,$ssg_tn,$amz_order_no)
  { 
    echo "ITEM FETCH STARTED FOR:{$ssg_tn}\n";
    sleep(2);
    try
    {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param['Action']=urlencode("ListOrderItems");
      $param['AmazonOrderId']=urlencode($amz_order_no);
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      curl_setopt($ch, CURLOPT_POST, true);
      $response = curl_exec($ch);
      if(curl_errno($ch))
      {
          throw new Exception(curl_error($ch));
      }
      $res = simplexml_load_string($response);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      if($httpcode != 200)
      {
        if(preg_match('/throttled/',(string)$res->Error->Message ))
        {
          sleep(4);
          echo "throttling occured;\n";
          $this->fetch_order_item_details($user_id,$ssg_tn,$amz_order_no);
        }
        else
        {
          throw new Exception($res->Error->Message);  
        }
      }
      $total_order_item=isset($res->ListOrderItemsResult->OrderItems->OrderItem)?count($res->ListOrderItemsResult->OrderItems->OrderItem):0;
      if($total_order_item > 0)
      {
        $data['status_text']=$this->process_order_items($total_order_item,$res->ListOrderItemsResult->OrderItems->OrderItem,$user_id,$ssg_tn,$amz_order_no);
      }
      else
      {
         $data['status_code']=1;
         $data['status_text']=$response;
      }
       return $data;
    }
    catch(Exception $e) 
    {
      
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }
 private function process_order_items($total_order_items,$item_details,$user_id,$ssgtn,$amz_order_no)
 {
      $rows=[];
      for($i=0;$i<$total_order_items;$i++)
      {
         $seller_sku=isset($item_details[$i]->SellerSKU)?(string)$item_details[$i]->SellerSKU:"";
         $asin=isset($item_details[$i]->ASIN)?(string)$item_details[$i]->ASIN:"";
         $title=isset($item_details[$i]->Title)?(string)$item_details[$i]->Title:"";
         $qty_ordered=isset($item_details[$i]->QuantityOrdered)?(string)$item_details[$i]->QuantityOrdered:0;
         $item_price=isset($item_details[$i]->ItemPrice->Amount)?(string)$item_details[$i]->ItemPrice->Amount:0;
         $ship_price=isset($item_details[$i]->ShippingPrice->Amount)?(string)$item_details[$i]->ShippingPrice->Amount:0;
         $item_tax=isset($item_details[$i]->ItemTax->Amount)?(string)$item_details[$i]->ItemTax->Amount:0;
         $is_shipped=isset($item_details[$i]->QuantityShipped)?(string)$item_details[$i]->QuantityShipped:0;
         $rows[]=array('ssg_tn'=>$ssgtn,'seller_sku'=>$seller_sku,'asin'=>$asin,'itm_title'=>$title,'itm_quantity'=>$qty_ordered,'itm_price'=>$item_price,
                     'itm_ship_price'=>$ship_price,'itm_tax'=>$item_tax);

     }
      if(count($rows) > 0)
      {
        $this->db->trans_start();
        $this->db->insert_ignore_batch('order_items_transaction_list', $rows); 
        $this->db->query("UPDATE order_transaction_list SET is_item_croned=1 WHERE ssg_tn={$ssgtn}");
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE)
        {
           return $rows;
        }
        else
         {
           return $rows;
         }  

      }
          
 }
 public function check_access($user_id,$fetch_type='NEW')
  { 
    try
    {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param['Action']=urlencode("ListOrders");
      if($fetch_type=='NEW')
      {
        $time_frame=$this->number_of_days_for_new;
        $param['CreatedAfter']=  gmdate('Y-m-d\TH:i:s\Z',strtotime($time_frame));
      }
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      curl_setopt($ch, CURLOPT_POST, true);
      $response = curl_exec($ch);
      if(curl_errno($ch))
      {
          throw new Exception(curl_error($ch));
      }
      $res = simplexml_load_string($response);
     
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if($httpcode != 200)
      {
          throw new Exception($res->Error->Message);  
      }
      $data['status_code']=1;
      $data['status_text']="Validation success";
      return $data;
    }
    catch(Exception $e) 
    {
      
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }
 private function built_query_string($add_param)
 {
         $params = array(
                  'AWSAccessKeyId'=> urlencode($this->access_key),
                  //'MWSAuthToken'=>urlencode($this->auth_token),
                  'SellerId'=> urlencode($this->seller_id),
                  'SignatureMethod' => urlencode("HmacSHA256"),
                  'SignatureVersion'=> urlencode("2"),
                  'Timestamp'=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
                  'Version' => urlencode("2013-09-01"),
                  'MarketplaceId.Id.1'=>$this->market_id

                 );
				 
				 if(!empty($this->auth_token))
    {
      $params['MWSAuthToken']=urlencode($this->auth_token);
    }
  
            $params=array_merge($params,$add_param);
          $url_parts = array();
        foreach(array_keys($params) as $key)
        {
            $url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
        }
        sort($url_parts);
            $url_string = implode("&", $url_parts);
            $string_to_sign = "POST\nmws-eu.amazonservices.com\n/Orders/2013-09-01\n" . $url_string;
            
            $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
            $signature = urlencode(base64_encode($signature));
            $url = "https://mws-eu.amazonservices.com/Orders/2013-09-01?". $url_string . "&Signature=" . $signature;
            return $url; 

 }
}
