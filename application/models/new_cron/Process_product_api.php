<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Process_product_api extends CI_Model
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
    INNER JOIN supported_country AS spt ON spt.country_code=cnt.country_code AND spt.is_active=1";
    $query=$this->db->query($sql);
    return $query->result_array();
  }

  public function set_credentials($usr)
  {
        $this->seller_id=$usr['seller_id'];
        $this->auth_token=$usr['auth_token'];
        $this->access_key=$usr['access_key'];
        $this->secret_key=$usr['secret_key'];
        $this->market_id=$usr['amz_code'];  
        $this->mws_site=$usr['mws_url'];
        $this->ch = curl_init();
        return TRUE;
  }
  public function get_product_to_match($user_id,$country_code)
  {
    $sql="SELECT prod_asin,prod_country FROM customer_product where added_by=".$user_id." AND prod_brand='' and prod_country=".$this->db->escape($country_code);
    $query=$this->db->query($sql);
    return $query->result_array();
  }
  public function get_product_to_hijack_check($user_id,$country_code)
  {
    $sql="SELECT prod_asin,is_alert_sent,prod_country FROM customer_product where added_by={$user_id} AND prod_country=".$this->db->escape($country_code)." AND check_hijack =1 ";
    $query=$this->db->query($sql);
    return $query->result_array();
  }
  public function fetch_product_details($user_id,$asin,$amz_country_code,$country_code)
  { 
    try
    {
      $param['Action']=urlencode("GetMatchingProductForId");
      $param['IdType']='ASIN';
      $param['IdList.Id.1']=$asin;
      $param['MarketplaceId']=$amz_country_code;

      $curl_res=$this->create_curl_request($param);
      if($curl_res['status_code']==0)
      {
        throw new Exception($curl_res['status_text']);   
      }
      

      $res = simplexml_load_string($curl_res['payload']);
    
      $payload=[];

      $payload['lm_ean']=$payload['lm_asin']=$payload['title']=$payload['image']=$payload['brand']=$payload['manufacture']=$payload['release_date']=$payload['model']=$payload['product_group']=$payload['part_number']=$payload['is_adult_product']=$payload['list_price']='';
      $payload['asin_counts']=-3;
      
      $namespaces = $res->getNamespaces(true);
      $httpcode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
      $payload=[];
      $payload['lm_ean']=$payload['lm_asin']=$payload['title']=$payload['image']=$payload['brand']=$payload['manufacture']=$payload['release_date']=$payload['model']=$payload['product_group']=$payload['part_number']=$payload['is_adult_product']=$payload['list_price']='';
      $payload['asin_counts']=-3;
      if($httpcode != 200)
      {
        if(preg_match('/throttled/',(string)$res->GetMatchingProductForIdResult->Error->Message ))
        {
          sleep(10);
          echo "throttling occured;\n";
          $this->fetch_product_details($user_id,$asin,$amz_country_code,$country_code);
        }
      }
      if(preg_match('/Invalid /',(string)$res->GetMatchingProductForIdResult->Error->Message))
      {
        
          echo "ERROR ".(string)$res->GetMatchingProductForIdResult->Error->Message;
          $data['status_code']=3;
          $data['status_text']="No Data";
          $payload['lm_ean']=$asin;
          $payload['asin_counts']=-3;
          $payload['lm_asin']='';
          $data['payload']=$payload;  
          return $data;
        //throw new Exception($res->GetMatchingProductForIdResult->Error->Message);   
      }
      
      if(isset($res->GetMatchingProductForIdResult[0]->Products->Product))
      {
        // print_r($res->GetMatchingProductForIdResult[0]->Products->Product);
        
            $payload['lm_ean']=$asin;
            $payload['asin_counts']= count($res->GetMatchingProductForIdResult[0]->Products->Product);
            $payload['lm_asin']= (string)$res->GetMatchingProductForIdResult[0]->Products->Product->Identifiers->MarketplaceASIN->ASIN;
            
            if(isset($res->GetMatchingProductForIdResult->Products->Product->AttributeSets))
            {
              $ns = $res->GetMatchingProductForIdResult->Products->Product->AttributeSets->children($namespaces["ns2"]);  
              // print_r($ns);
              // die();
              $payload['title']=isset($ns->ItemAttributes->Title)?(string)$ns->ItemAttributes->Title:'';
              $payload['manufacture']=isset($ns->ItemAttributes->Manufacturer)?(string)$ns->ItemAttributes->Manufacturer:'';
              $payload['brand']=isset($ns->ItemAttributes->Brand)?(string)$ns->ItemAttributes->Brand:'';
              $payload['image']=isset($ns->ItemAttributes->SmallImage->URL)?str_replace('_SL75_','_SL500_',(string)$ns->ItemAttributes->SmallImage->URL):'';
              $payload['release_date']=isset($ns->ItemAttributes->ReleaseDate)?(string)$ns->ItemAttributes->ReleaseDate:'';
              $payload['model']=isset($ns->ItemAttributes->Model)?(string)$ns->ItemAttributes->Model:'';
              $payload['product_group']=isset($ns->ItemAttributes->ProductGroup)?(string)$ns->ItemAttributes->ProductGroup:'';
              $payload['part_number']=isset($ns->ItemAttributes->PartNumber)?(string)$ns->ItemAttributes->PartNumber:'';
              $payload['is_adult_product']=isset($ns->ItemAttributes->IsAdultProduct)?(string)$ns->ItemAttributes->IsAdultProduct:'';
              $payload['list_price']=isset($ns->ItemAttributes->ListPrice->Amount)?str_replace('.',',',(string)$ns->ItemAttributes->ListPrice->Amount):'';
              
            }
            
             
      

      }
      if(count($payload) > 0 && !empty($payload['lm_asin']))
      {
        $data['status_code']=1;
        $data['status_text']="Success";
        $data['payload']=$payload;  
      }
      else
      {
          $data['status_code']=3;
          $data['status_text']="No Data";
          $payload['lm_ean']=$asin;
          $payload['asin_counts']=-3;
          $payload['lm_asin']='';
          $data['payload']=$payload;  
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
 public function check_hijack_details($user_id,$asin,$amz_country_code,$country_code)
  { 
    try
    {
      $param['Action']=urlencode("GetLowestOfferListingsForASIN");
      $param['ExcludeMe']='true';
      $param['ASINList.ASIN.1']=$asin;
      $param['MarketplaceId']=$amz_country_code;

      $curl_res=$this->create_curl_request($param);
      if($curl_res['status_code']==0)
      {
        throw new Exception($curl_res['status_text']);   
      }
      

      $res = simplexml_load_string($curl_res['payload']);
    
      $data['status_code']=1;
      $data['status_text']='Success';
      $data['hijack_count']=0;
      if(isset($res->GetLowestOfferListingsForASINResult->Product->LowestOfferListings->LowestOfferListing))
      {
       $data['hijack_count']=count($res->GetLowestOfferListingsForASINResult->Product->LowestOfferListings->LowestOfferListing);
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
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 15);
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
                  'SellerId'=> urlencode($this->seller_id),
                  'SignatureMethod' => urlencode("HmacSHA256"),
                  'SignatureVersion'=> urlencode("2"),
                  'Timestamp'=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
                  'Version' => urlencode("2011-10-01")
                  

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
            $string_to_sign = "POST\n".$this->mws_site."\n/Products/2011-10-01\n" . $url_string;
            
            $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
            $signature = urlencode(base64_encode($signature));
            $url = "https://".$this->mws_site."/Products/2011-10-01?". $url_string . "&Signature=" . $signature;
            return $url; 
 }
 
}
?>
  
