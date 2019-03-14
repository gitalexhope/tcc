<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Amazon_settlement_model extends CI_Model
{
  private $seller_id='';
  private $auth_token='';
  private $access_key='';
  private $secret_key='';
  private $market_id='';  
  public function  __construct()
  {
      parent::__construct();
      
  }

  public function set_credentials($user_id,$seller_id,$auth_token,$access_key,$secret_key,$market_placeID)
  {
        $this->seller_id=$seller_id;
        $this->auth_token=$auth_token;
        $this->access_key=$access_key;
        $this->secret_key=$secret_key;
        $this->market_id=$market_placeID;  
        return TRUE;
  }
  public function get_settlement_report_id($user_id,$fetch_type='NEW')
  { 
    echo "FETCH STARTED:\n";
    try
    {
	  $time_frame="-60 days";
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param['Action']=urlencode("GetReportRequestList");
      $param['ReportTypeList.Type.1']=urlencode("_GET_V2_SETTLEMENT_REPORT_DATA_FLAT_FILE_");
	  $param['ReportProcessingStatusList.Status.1']=urlencode('_DONE_');
      $param['MaxCount']=100;
	  $param['RequestedFromDate']=gmdate('Y-m-d\TH:i:s\Z',strtotime($time_frame));
      $param['RequestedToDate']=gmdate('Y-m-d\TH:i:s\Z');
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
      // die();
     
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
      $total_report=isset($res->GetReportRequestListResult->ReportRequestInfo)?count($res->GetReportRequestListResult->ReportRequestInfo):0;
      if($total_report > 0)
      {
        $data['status_text']=$this->process_report_id($total_report,$res->GetReportRequestListResult->ReportRequestInfo,$user_id);
      }
      else
      {
         $data['status_code']=1;
         $data['status_text']=$response;
      }
      
      
      if(isset($res->GetReportRequestListResult->NextToken))
      {
        $data=$this->get_settlement_report_id_by_token($user_id,(string)$res->GetReportRequestListResult->NextToken);
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
  public function get_settlement_report_id_by_token($user_id,$token)
  { 
    echo "ByToken\n";
    sleep(55);
    try
    {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param['Action']=urlencode("GetReportRequestListByNextToken");
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
          $this->get_settlement_report_id_by_token($user_id,$token);
        }
        else
        {
          throw new Exception($res->Error->Message);  
        }
      }
      $data['status_code']=1;
      $data['status_text']="Success";
      
      $total_report=isset($res->GetReportRequestListByNextTokenResult->ReportRequestInfo)?count($res->GetReportRequestListByNextTokenResult->ReportRequestInfo):0;
      if($total_report > 0)
      {
        $data['status_text']=$this->process_report_id($total_report,$res->GetReportRequestListByNextTokenResult->ReportRequestInfo,$user_id);
      }
      else
      {
         $data['status_code']=1;
         $data['status_text']=$response;
      }
      
      if(isset($res->GetReportRequestListResult->NextToken))
      {
        $data=$this->get_settlement_report_id_by_token($user_id,(string)$res->GetReportRequestListResult->NextToken);
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
 private function process_report_id($total_report,$report_details,$user_id)
 {
      $rows=[];
      for($i=0;$i<$total_report;$i++)
      {
         $report_id=(string)$report_details[$i]->GeneratedReportId;
         $request_id=(string)$report_details[$i]->ReportRequestId;
         $start_date=date("Y-m-d H:i:s",strtotime((string)$report_details[$i]->StartDate));
         $end_date=date("Y-m-d H:i:s",strtotime((string)$report_details[$i]->EndDate));
         $process_start=date("Y-m-d H:i:s",strtotime((string)$report_details[$i]->StartedProcessingDate));
         $process_submit=date("Y-m-d H:i:s",strtotime((string)$report_details[$i]->SubmittedDate));
         $process_complete=date("Y-m-d H:i:s",strtotime((string)$report_details[$i]->CompletedDate));
         $process_status=(string)$report_details[$i]->ReportProcessingStatus;
         echo $user_id." ".$report_id." ".$request_id." ".$process_status."\n";
         $rows[]="(".$this->db->escape($request_id).",".$this->db->escape($report_id).",".$this->db->escape($start_date).",".$this->db->escape($end_date).",".$this->db->escape($process_start).",".$this->db->escape($process_submit).",".$this->db->escape($process_complete).",".$this->db->escape($process_status).",".$user_id.")";

      }
       // print_r($rows);
      // die();
      if(!empty($rows) && count($rows) > 0)
      {
           $quer=implode(',' ,$rows);
           $query="INSERT IGNORE INTO `settlement_report_feed` (
          `request_id` ,
          `report_id` ,
          `start_date` ,
          `end_date` ,
          `process_start` ,
          `submitted_date` ,
          `process_complete` ,
          `req_status`,
          `user_id`
            )
            VALUES 
            $quer 
            ";
            $this->db->query($query);
      }
      return $rows;
          
 }


  
  public function make_report_request($user_id)
  { 
    try
    {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param=array('Action'=>urlencode("RequestReport"),'ReportType' => urlencode("_GET_MERCHANT_LISTINGS_ALL_DATA_"),'MarketplaceIdList.Id.1'=>urlencode($this->market_id)) ;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      curl_setopt($ch, CURLOPT_POST, true);
      $response = curl_exec($ch);
      $res = simplexml_load_string($response);
      print_r($res);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if($httpcode != 200)
      {
          throw new Exception($res->Error->Message);  
      }
      $request_id=(string)$res->RequestReportResult->ReportRequestInfo->ReportRequestId;
      $status=(string)$res->RequestReportResult->ReportRequestInfo->ReportProcessingStatus;
      $insert_feed_log=array('request_id'=>$request_id,'req_status'=>$status,'user_id'=>$user_id); 
      $this->db->insert('report_feed',$insert_feed_log);
    }
    catch(Exception $e) 
    {
      
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }

public function get_report_request($user_id,$req_id,$report_id)
  { 
    try
    {
      $rep_file=realpath('asset').DIRECTORY_SEPARATOR."amazon_report".DIRECTORY_SEPARATOR.$user_id."_".$report_id;
      global $file_handle; 
      $file_handle = fopen($rep_file, 'w+'); 
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param=array('Action'=>urlencode("GetReport"),'ReportId'=>$report_id);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
      curl_setopt($ch, CURLOPT_FILE, $file_handle);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 505);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($cp, $data) {
  global $file_handle;
  $len = fwrite($file_handle, $data);
  return $len;
});

      curl_setopt($ch, CURLOPT_POST, true);
      curl_exec($ch);
      curl_close($ch);
      fclose($file_handle);
      $fp=fopen($rep_file,'r');
      if ($fp)
      {
        $i=0;
        while (!feof($fp)) 
        {
            
            $buffer = fgetcsv($fp,0,"\t");
            // echo "Works Settlement";
            // print_r($buffer);

            if($i >= 1 && !empty($buffer[0]))
            {
          	  $settlement_id= isset($buffer[0])?$this->db->escape($buffer[0]):'';
              $start_date= isset($buffer[1])?$this->db->escape($buffer[1]):'';
              
              $end_date= isset($buffer[2])?$this->db->escape($buffer[2]):'';
              $deposit_date= isset($buffer[3])?$this->db->escape($buffer[3]):'';
              $total_amt= isset($buffer[4])?$this->db->escape($buffer[4]):'';
              $currency= isset($buffer[5])?$this->db->escape($buffer[5]):'';
              $transaction_type= isset($buffer[6])?$this->db->escape($buffer[6]):'';
              $order_id= isset($buffer[7])?$this->db->escape($buffer[7]):'';
              $mrch_order_id= isset($buffer[8])?$this->db->escape($buffer[8]):'';
              $adjus_id= isset($buffer[9])?$this->db->escape($buffer[9]):'';
              $shipment_id= isset($buffer[10])?$this->db->escape($buffer[10]):'';
              $market_place= isset($buffer[11])?$this->db->escape($buffer[11]):'';
              $ship_fee_type= isset($buffer[12])?$this->db->escape($buffer[12]):'';
              $ship_fee_amt= isset($buffer[13])?$this->db->escape($buffer[13]):'';
              $order_fee_type= isset($buffer[14])?$this->db->escape($buffer[14]):'';
              $order_fee_amt= isset($buffer[15])?$this->db->escape($buffer[15]):'';
              $fulfilment_id= isset($buffer[16])?$this->db->escape($buffer[16]):'';
              $posted_date= isset($buffer[17])?$this->db->escape($buffer[17]):'';
              $order_itm_code= isset($buffer[18])?$this->db->escape($buffer[18]):'';
              $mrcht_order_itm_code= isset($buffer[19])?$this->db->escape($buffer[19]):'';
              $mrcht_adjust_itm_id= isset($buffer[20])?$this->db->escape($buffer[20]):'';
              $sku= isset($buffer[21])?$this->db->escape($buffer[21]):'';
              $qty_purchased= isset($buffer[22])?$this->db->escape($buffer[22]):'';
              $price_type= isset($buffer[23])?$this->db->escape($buffer[23]):'';
              $price_amt= isset($buffer[24])?$this->db->escape($buffer[24]):'';
              $itm_related_fee_type= isset($buffer[25])?$this->db->escape($buffer[25]):'';
              $itm_related_fee_amt= isset($buffer[26])?$this->db->escape($buffer[26]):'';
              $misc_fee_amt= isset($buffer[27])?$this->db->escape($buffer[27]):'';
              $other_fee_amt= isset($buffer[28])?$this->db->escape($buffer[28]):'';
              $other_fee_reason= isset($buffer[29])?$this->db->escape($buffer[29]):'';
              $promo_id= isset($buffer[30])?$this->db->escape($buffer[30]):'';
              $promo_type= isset($buffer[31])?$this->db->escape($buffer[31]):'';
              $promo_amt= isset($buffer[32])?($buffer[32]):'';
              $direct_pay_type= isset($buffer[33])?($buffer[33]):'';
              $direct_pay_amt= isset($buffer[34])?($buffer[34]):'';
              $other_amt= isset($buffer[35])?($buffer[35]):'';

              $sql="INSERT IGNORE INTO amz_settlement_data (settlement_id,start_date,end_date,deposit_date,total_amount,currency,transaction_type,order_id,merchant_order_id,adjustment_id,shipment_id,market_place,shipment_fee_type,shipment_fee_amt,order_fee_type,order_fee_amount,fulfilment_id,posted_date,order_item_code,merchant_order_item_id,merchant_adjustment_item_id,sku,qty_purchased,price_type,price_amt,itm_related_fee_type,itm_related_fee_amount,misc_fee_amt,other_fee_amt,other_fee_reason_desc,promo_id,prom_type,promo_amt,direct_pay_type,direct_pay_amt,other_amt,settlement_for)
              VALUES({$settlement_id},{$start_date},{$end_date},{$deposit_date},{$total_amt},{$currency},{$transaction_type},{$order_id},{$mrch_order_id},{$adjus_id},{$shipment_id},{$market_place},{$ship_fee_type},{$ship_fee_amt},{$order_fee_type},{$order_fee_amt},{$fulfilment_id},{$posted_date},{$order_itm_code},{$mrcht_order_itm_code},{$mrcht_adjust_itm_id},{$sku},{$qty_purchased},{$price_type},{$price_amt},{$itm_related_fee_type},{$itm_related_fee_amt},{$misc_fee_amt},{$other_fee_amt},{$other_fee_reason},{$promo_id},{$promo_type},'{$promo_amt}','{$direct_pay_type}','{$direct_pay_amt}','{$other_amt}','{$user_id}')" ;
              $this->db->query($sql);           
            }

            // if($i==25)
            // {
            //   break;       
            // }
            $i++;
        }
        fclose($fp);
       }
        $this->db->query("UPDATE settlement_report_feed SET is_processed=1 WHERE req_id=".$req_id);    
    
      
      // $res = simplexml_load_string($response);
      // print_r($res);
      // $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      // if($httpcode != 200)
      // {
      //     throw new Exception($res->Error->Message);  
      // }
      // $request_id=(string)$res->RequestReportResult->ReportRequestInfo->ReportRequestId;
      // $status=(string)$res->RequestReportResult->ReportRequestInfo->ReportProcessingStatus;
      // $insert_feed_log=array('request_id'=>$request_id,'req_status'=>$status,'user_id'=>$user_id); 
      // $this->db->insert('report_feed',$insert_feed_log);
    }
    catch(Exception $e) 
    {
      
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }

 // public function update_report_request($user_id,$report_arr)
 //  { 
 //    try
 //    {
 //      $httpHeader=array();
 //      $httpHeader[]='Transfer-Encoding: chunked';
 //      $httpHeader[]='Content-Type: text/xml';
 //      $httpHeader[]='Expect:';
 //      $httpHeader[]='Accept:';
 //      $param=array('Action'=>urlencode("GetReportRequestList"));
 //      for($i=1;$i<=count($report_arr);$i++)
 //      {
 //        $param['ReportRequestIdList.Id.'.$i]=$report_arr[$i-1]['request_id'];
 //      }
 //      print_r($param);
 //      $ch = curl_init();
 //      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
 //      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 //      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
 //      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 //      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
 //      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
 //      curl_setopt($ch, CURLOPT_POST, true);
 //      $response = curl_exec($ch);
 //      $res = simplexml_load_string($response);
 //      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 //      if($httpcode != 200)
 //      {
 //          throw new Exception($res->Error->Message);  
 //      }
 //      foreach($res->GetReportRequestListResult->ReportRequestInfo as $report)
 //      {
 //        $status=$report->ReportProcessingStatus;
 //        $request_id=$report->ReportRequestId;
 //        $report_id=$report->GeneratedReportId;
 //        if($status=='_DONE_')
 //        {
 //          $this->db->query("UPDATE report_feed SET req_status=".$this->db->escape($status)." ,report_id=".$this->db->escape($report_id)." WHERE user_id=".$user_id." AND request_id=".$this->db->escape($request_id))  ;
 //        }
        
 //      }
      
 //    }
 //    catch(Exception $e) 
 //    {
      
 //      $data['status_code']=0;
 //      $data['status_text']=$e->getMessage();
 //      return $data;
 //    }
 // }


 private function built_query_string($add_param)
 {
         $params = array(
                  'AWSAccessKeyId'=> urlencode($this->access_key),
                  'MWSAuthToken'=>urlencode($this->auth_token),
                  'Merchant'=> urlencode($this->seller_id),
                  'SignatureMethod' => urlencode("HmacSHA256"),
                  'SignatureVersion'=> urlencode("2"),
                  'Timestamp'=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
                  'Version' => urlencode("2009-01-01")
                 );
  
            $params=array_merge($params,$add_param);
          $url_parts = array();
        foreach(array_keys($params) as $key)
        {
            $url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
        }
        sort($url_parts);
            $url_string = implode("&", $url_parts);
            $string_to_sign = "POST\nmws-eu.amazonservices.com\n/\n" . $url_string;
            
            $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
            $signature = urlencode(base64_encode($signature));
            $url = "https://mws-eu.amazonservices.com/" . '?' . $url_string . "&Signature=" . $signature;
            return $url; 

 }
    
   

}
?>
