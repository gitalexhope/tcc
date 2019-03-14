<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Process_report_model extends CI_Model
{
  private $seller_id='';
  private $auth_token='';
  private $access_key='';
  private $secret_key='';
  private $market_id='';  
  private $ch = '';
  public function  __construct()
  {
      parent::__construct();
      
  }

  public function get_seller_for_process($user_id='')
  {
    $sql="SELECT profile_id,prf.seller_id,auth_token,access_key,secret_key,amz_code,cnt.country_code,country_name,mws_url,amz_code FROM amazon_profile AS prf
          INNER JOIN seller_country_mapping AS cnt ON ";
            if(!empty($user_id))
            {
              $sql.=" profile_id=".$this->db->escape($user_id)." AND ";  
            }
    $sql.=" prf.is_active=1 AND cnt.seller_id=profile_id
    INNER JOIN supported_country AS spt ON spt.country_code=cnt.country_code AND spt.is_active=1 and prf.profile_id <> 1";
	//die($sql);
    $query=$this->db->query($sql);
    return $query->result_array();
  }
  public function get_seller_for_process_new($user_id='')
  {
    $sql="SELECT profile_id,prf.seller_id,auth_token,access_key,secret_key,amz_code,cnt.country_code,country_name,mws_url,amz_code FROM amazon_profile AS prf
          INNER JOIN seller_country_mapping AS cnt ON ";
            if(!empty($user_id))
            {
              $sql.=" profile_id=".$this->db->escape($user_id)." AND ";  
            }
    $sql.=" prf.is_active=1 AND cnt.seller_id=profile_id
    INNER JOIN supported_country AS spt ON spt.country_code=cnt.country_code AND spt.is_active=1 AND prf.profile_id =1";
//die($sql);
    $query=$this->db->query($sql);
    return $query->result_array();
  }

  public function get_seller_who_have_pending_report($request_id,$user_id,$limit=10)
  {
    $sql="SELECT req_id,request_id,market_id,profile_id,user_id,seller_id,auth_token,access_key,secret_key,country_name,country_code,mws_url,amz_code FROM report_feed AS fed
          INNER JOIN amazon_profile AS prf ON report_id='' AND req_status in ('_SUBMITTED_','_IN_PROGRESS_') AND user_id=profile_id ";
    if($request_id <> 'NULL')
    {
      $sql.=" AND request_id=".$this->db->escape($request_id);
    }
    if(!empty($user_id))
    {
      $sql.=" AND user_id=".$this->db->escape($user_id);
    }      
    $sql.="INNER JOIN supported_country AS spt ON spt.amz_code=fed.market_id";;
    $query=$this->db->query($sql);
    return $query->result_array();       
  }
  public function get_seller_who_have_generated_report($request_id,$user_id,$limit=10)
  {
    $sql="SELECT req_id,request_id,request_type,market_id,profile_id,user_id,seller_id,auth_token,access_key,secret_key,country_name,country_code,mws_url,amz_code,report_id FROM report_feed AS fed
          INNER JOIN amazon_profile AS prf ON  is_processed=0 AND report_id <> '' AND req_status='_DONE_' AND user_id=profile_id ";
    if($request_id <> 'NULL')
    {
      $sql.=" AND request_id=".$this->db->escape($request_id);
    }
    if(!empty($user_id))
    {
      $sql.=" AND user_id=".$this->db->escape($user_id);
    }      
    $sql.="INNER JOIN supported_country AS spt ON spt.amz_code=fed.market_id ";
    $query=$this->db->query($sql);
    return $query->result_array();       
  }
  public function get_seller_report_to_ack($request_id,$user_id,$limit=10)
  {
    $sql="SELECT req_id,request_id,request_type,market_id,profile_id,user_id,seller_id,auth_token,access_key,secret_key,country_name,country_code,mws_url,amz_code,report_id FROM report_feed AS fed
          INNER JOIN amazon_profile AS prf ON  is_processed=1 AND is_ack=0 AND user_id=profile_id ";
    if($request_id <> 'NULL')
    {
      $sql.=" AND request_id=".$this->db->escape($request_id);
    }
    if(!empty($user_id))
    {
      $sql.=" AND user_id=".$this->db->escape($user_id);
    }      
    $sql.="INNER JOIN supported_country AS spt ON spt.amz_code=fed.market_id ";
    // die($sql);
    $query=$this->db->query($sql);
    return $query->result_array();       
  }


  public function get_seller_pending_report($user_id)
  {
    $query=$this->db->query("SELECT req_id,request_id,report_id,req_status,market_id,request_type FROM report_feed where user_id=".$user_id." AND request_id<>'' AND report_id='' AND is_processed=0 limit 0,5");  
    return $query->result_array();
  }

  public function get_seller_completed_report($user_id)
  {
    $query=$this->db->query("SELECT req_id,request_id,report_id,req_status,market_id,request_type FROM report_feed where user_id=".$user_id." AND is_processed=1 AND is_ack=0 limit 0,5");  
    return $query->result_array();
  }

  public function set_credentials($usr)
  {
        $this->seller_id=trim($usr['seller_id']);
        $this->auth_token=trim($usr['auth_token']);
        $this->access_key=trim($usr['access_key']);
        $this->secret_key=trim($usr['secret_key']);
        $this->market_id=trim($usr['amz_code']);  
        $this->mws_site=trim($usr['mws_url']);
        $this->ch = curl_init();
        return TRUE;
  }
  public function manage_scheduled_report($user_id,$report_type,$sch_status)
  { 
    try
    {
      $param['Action']=urlencode("ManageReportSchedule");
      $param['ReportType']=urlencode($report_type);
      $param['Marketplace']=urlencode($this->market_id);
      $param['Schedule']=urlencode($sch_status);
      $curl_res=$this->create_curl_request($param);
      if($curl_res['status_code']==0)
      {
        throw new Exception($curl_res['status_text']);   
      }

      $req_res = simplexml_load_string($curl_res['payload']);

      $httpcode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
      if($httpcode != 200)
      {
          throw new Exception($req_res->Error->Message);  
      }
      print_r($req_res);
      
    }
    catch(Exception $e) 
    {
      
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }
 public function update_ack_status($user_id,$report_arr)
 {
    $param['Action']="UpdateReportAcknowledgements";
    $param['Acknowledged']=true;
    for($i=1;$i<=count($report_arr);$i++)
    {
      $param['ReportIdList.Id.'.$i]=$report_arr[$i-1]['report_id'];
    }
    $curl_res=$this->create_curl_request($param);
    if($curl_res['status_code']==0)
    {
      throw new Exception($curl_res['status_text']);   
    }

    $req_res = simplexml_load_string($curl_res['payload']);
    $httpcode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    if($httpcode != 200)
    {
        throw new Exception($req_res->Error->Message);  
    }
    foreach($req_res->UpdateReportAcknowledgementsResult->ReportInfo as $report)
    {
      $status=$report->Acknowledged;
      $report_id=$report->ReportId;
      $request_id=$report->ReportRequestId;
      if($status=='true')
      {
        $this->db->query("UPDATE report_feed SET is_ack=1 WHERE user_id=".$user_id." AND request_id=".$this->db->escape($request_id)." AND report_id=".$this->db->escape($report_id));
      }
    }
 }
  public function request_report($user_id,$report_type,$time_frame='30')
  { 
    try
    {
      $time_frame='-'.$time_frame." days";  
	  $end_time_frame="-30 days";  	  
      $param['Action']=urlencode("RequestReport");
      $param['ReportType']=urlencode($report_type);
      $param['MarketplaceIdList.Id.1']=urlencode($this->market_id);
      // if($report_type=='_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_' || $report_type == '_GET_FLAT_FILE_ORDERS_DATA_' || $report_type=='_GET_FLAT_FILE_ALL_ORDERS_DATA_BY_ORDER_DATE_' || $report_type=='_GET_SELLER_FEEDBACK_DATA_')
      // {
        $param['StartDate']=gmdate('Y-m-d\TH:i:s\Z',strtotime($time_frame));
      // }
	  if($report_type=='_GET_VAT_TRANSACTION_DATA_')
       {
       $param['EndDate']=gmdate('Y-m-d\TH:i:s\Z',strtotime($end_time_frame));  
       }
       print_r($param);  
      $curl_res=$this->create_curl_request($param);
	  print_r($curl_res);
      if($curl_res['status_code']==0)
      {
        throw new Exception($curl_res['status_text']);   
      }
      $req_res = simplexml_load_string($curl_res['payload']);
      $httpcode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
	  print_r($httpcode);
      if($httpcode != 200)
      {
          throw new Exception($req_res->Error->Message);  
      }
      $request_id=(string)$req_res->RequestReportResult->ReportRequestInfo->ReportRequestId;
      $status=(string)$req_res->RequestReportResult->ReportRequestInfo->ReportProcessingStatus;
      $insert_feed_log=array('request_id'=>$request_id,'req_status'=>$status,'user_id'=>$user_id,'request_type'=>$report_type,'market_id'=>$this->market_id); 
      $this->db->insert('report_feed',$insert_feed_log);
    }
    catch(Exception $e) 
    {
      
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }
 public function update_report_request($user_id,$report_arr)
  { 
    try
    {
      $param=array('Action'=>urlencode("GetReportRequestList"));
      for($i=1;$i<=count($report_arr);$i++)
      {
        $param['ReportRequestIdList.Id.'.$i]=$report_arr[$i-1]['request_id'];
      }
      $curl_res=$this->create_curl_request($param);
      if($curl_res['status_code']==0)
      {
        throw new Exception($curl_res['status_text']);   
      }

      $req_res = simplexml_load_string($curl_res['payload']);
      $httpcode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
      if($httpcode != 200)
      {
          throw new Exception($req_res->Error->Message);  
      }
      
      foreach($req_res->GetReportRequestListResult->ReportRequestInfo as $report)
      {
        $status=$report->ReportProcessingStatus;
        $request_id=$report->ReportRequestId;
        $report_id=$report->GeneratedReportId;
        if($status=='_CANCELLED_' || $status == '_DONE_NO_DATA_')
        {
          $this->db->query("UPDATE report_feed SET req_status=".$this->db->escape($status)." ,report_id=".$this->db->escape($report_id).",is_processed=1 WHERE user_id=".$user_id." AND request_id=".$this->db->escape($request_id));
        }
        elseif($status=='_DONE_' || $status=='_IN_PROGRESS_')
        {
          $this->db->query("UPDATE report_feed SET req_status=".$this->db->escape($status)." ,report_id=".$this->db->escape($report_id)." WHERE user_id=".$user_id." AND request_id=".$this->db->escape($request_id));
        }
      }
      
    }
    catch(Exception $e) 
    {
      
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }
 public function get_report($usr)
  { 
    try
    {
      $param=array('Action'=>urlencode("GetReport"),'ReportId'=>$usr['report_id']);
      $curl_res=$this->create_curl_request($param,$usr['profile_id'],1,$usr['report_id']);
	  print_r($curl_res);
      if($curl_res['status_code']==0)
      {
        throw new Exception($curl_res['status_text']);   
      }
      $data['status_code']=1;
      $data['status_text']='Success';
      $data['report_file']=$curl_res['report_file'];
      return $data;
    }
    catch(Exception $e) 
    {
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
 }
  
  private function create_curl_request($param,$user_id=null,$store_to_file=0,$report_id='')
  {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      try
      {
        curl_setopt($this->ch, CURLOPT_URL, $this->built_query_string($param));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($this->ch, CURLOPT_POST, true);
        
        if($store_to_file==1 && $user_id != null && $report_id!='')
        {
          $rep_file=realpath('asset').DIRECTORY_SEPARATOR."amazon_report".DIRECTORY_SEPARATOR.$user_id."_".$report_id;
          global $file_handle; 
          $file_handle = fopen($rep_file, 'w+'); 
          curl_setopt($this->ch, CURLOPT_FILE, $file_handle);
          curl_setopt($this->ch, CURLOPT_WRITEFUNCTION, function ($cp, $data) {
          global $file_handle;
          $len = fwrite($file_handle, $data);
          return $len;
          });
          curl_exec($this->ch);
          fclose($file_handle);
          
        }  
        else
        {
          $response = curl_exec($this->ch);  
        }
        
        if(curl_errno($this->ch))
        {
            throw new Exception(curl_error($this->ch));
        }
        $data['status_code']=1;
        $data['status_text']='Success';
        if($store_to_file==1 && $user_id != null && $report_id!='')
        {
          $data['report_file']=$rep_file;
        }
        else
        {
          $data['payload']=$response;  
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
 private function built_query_string($add_param)
 {
    $params = array(
              'AWSAccessKeyId'=> urlencode($this->access_key),
              'Merchant'=> urlencode($this->seller_id),
              'SignatureMethod' => urlencode("HmacSHA256"),
              'SignatureVersion'=> urlencode("2"),
              'Timestamp'=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
              'Version' => urlencode("2009-01-01")
             );
    if(!empty($this->auth_token))
    {
      $params['MWSAuthToken']=urlencode($this->auth_token);
    }

    $params=array_merge($params,$add_param);
     print_r($params);   
    $url_parts = array();
    foreach(array_keys($params) as $key)
    {
        $url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
    }
    sort($url_parts);
    $url_string = implode("&", $url_parts);
    $string_to_sign = "POST\n".$this->mws_site."\n/\n" . $url_string;
    
    $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
    $signature = urlencode(base64_encode($signature));
    $url = "https://".$this->mws_site."/" . '?' . $url_string . "&Signature=" . $signature;
    return $url; 
 }
  
}
?>
  