<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Amazon_product_api extends CI_Model
{
  private $seller_id='';
  private $auth_token='';
  private $access_key='';
  private $secret_key='';
  private $market_id='';
  private $service_url='mws.amazonservices.com';
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
  public function get_product_to_match($limit=600000,$user_id)
  {
    $query=$this->db->query("SELECT pro_asin  FROM product_info where pro_user={$user_id} AND process_flag=0 OR process_flag is null  limit 0,".$limit);
    return $query->result_array();
  }
  public function fetch_product_details($user_id,$ean)
  { 
    try
    {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/xml';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param['Action']=urlencode("GetMatchingProductForId");
      $param['IdType']='ASIN';
      $param['IdList.Id.1']=$ean;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      curl_setopt($ch, CURLOPT_POST, true);
      $response = curl_exec($ch);
     // echo "response1:$response";
	  //die();
      $res = simplexml_load_string($response);
      //echo "response:$res";
      $namespaces = $res->getNamespaces(true);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $payload=[];
      $payload['lm_ean']=$payload['height']=$payload['length']=$payload['width']=$payload['weight']=$payload['sales_rank']=$payload['category']=$payload['title']=$payload['image']=$payload['p_height']=$payload['p_length']=$payload['p_width']=$payload['p_weight']='';
     // $payload['asin_counts']=-3;
      if($httpcode != 200)
      {
        if(preg_match('/throttled/',(string)$res->Error->Message ))
        {
          sleep(1);
          echo "throttling occured;\n";
          $this->fetch_product_details($user_id,$ean);
        }
        
        
      }
      if(preg_match('/Invalid ASIN/',(string)$res->GetMatchingProductForIdResult->Error->Message))
      {
        
          echo "ERROR ".(string)$res->GetMatchingProductForIdResult->Error->Message;
          $data['status_code']=3;
          $data['status_text']="No Data";
          $payload['lm_ean']=$ean;
          $payload['asin_counts']=-3;
          $payload['lm_asin']='';
          $data['payload']=$payload;  
          return $data;
        //throw new Exception($res->GetMatchingProductForIdResult->Error->Message);   
      }
      
      if(isset($res->GetMatchingProductForIdResult[0]->Products->Product))
      {
            $payload['lm_ean']=$ean;
            $payload['asin_counts']= count($res->GetMatchingProductForIdResult[0]->Products->Product);
            $payload['lm_asin']= (string)$res->GetMatchingProductForIdResult[0]->Products->Product->Identifiers->MarketplaceASIN->ASIN;
			
            if(isset($res->GetMatchingProductForIdResult->Products->Product->AttributeSets))
            {
              $ns = $res->GetMatchingProductForIdResult->Products->Product->AttributeSets->children($namespaces["ns2"]);  
              $payload['sales_rank']=isset($res->GetMatchingProductForIdResult->Products->Product->SalesRankings->SalesRank[0]->Rank)?(string)$res->GetMatchingProductForIdResult->Products->Product->SalesRankings->SalesRank[0]->Rank:'';
			  $payload['category']=isset($ns->ItemAttributes->ProductGroup)?(string)$ns->ItemAttributes->ProductGroup:'';
              //$payload['height']=isset($ns->ItemAttributes->ItemDimensions->Height)?str_replace('.','.',(string)$ns->ItemAttributes->ItemDimensions->Height):'';
              //$payload['length']=isset($ns->ItemAttributes->ItemDimensions->Length)?str_replace('.','.',(string)$ns->ItemAttributes->ItemDimensions->Length):'';
			  //$payload['width']=isset($ns->ItemAttributes->ItemDimensions->Width)?str_replace('.','.',(string)$ns->ItemAttributes->ItemDimensions->Width):'';
              $payload['weight']=isset($ns->ItemAttributes->ItemDimensions->Weight)?str_replace('.','.',(string)$ns->ItemAttributes->ItemDimensions->Weight):'';
			  $payload['p_height']=isset($ns->ItemAttributes->PackageDimensions->Height)?str_replace('.','.',(string)$ns->ItemAttributes->PackageDimensions->Height):'';
              $payload['p_length']=isset($ns->ItemAttributes->PackageDimensions->Length)?str_replace('.','.',(string)$ns->ItemAttributes->PackageDimensions->Length):'';
			  $payload['p_width']=isset($ns->ItemAttributes->PackageDimensions->Width)?str_replace('.','.',(string)$ns->ItemAttributes->PackageDimensions->Width):'';
              $payload['p_weight']=isset($ns->ItemAttributes->PackageDimensions->Weight)?str_replace('.','.',(string)$ns->ItemAttributes->PackageDimensions->Weight):'';
              $payload['title']=isset($ns->ItemAttributes->Title)?(string)$ns->ItemAttributes->Title:'';
              $payload['image']=isset($ns->ItemAttributes->SmallImage->URL)?str_replace('_SL75_','_SL500_',(string)$ns->ItemAttributes->SmallImage->URL):'';
			  $payload['pack_qty']=isset($ns->ItemAttributes->PackageQuantity)?(string)$ns->ItemAttributes->PackageQuantity:'';
			  $payload['brand']=isset($ns->ItemAttributes->Brand)?(string)$ns->ItemAttributes->Brand:'';
			  $payload['part_number']=isset($ns->ItemAttributes->PartNumber)?(string)$ns->ItemAttributes->PartNumber:'';
			  $payload['model']=isset($ns->ItemAttributes->Model)?(string)$ns->ItemAttributes->Model:'';
			  $payload['manufacturer']=isset($ns->ItemAttributes->Manufacturer)?(string)$ns->ItemAttributes->Manufacturer:'';
			  $payload['currencycode']=isset($ns->ItemAttributes->ListPrice->CurrencyCode)?(string)$ns->ItemAttributes->ListPrice->CurrencyCode:'';
	  
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
          $payload['lm_ean']=$ean;
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

 private function built_query_string($add_param)
 {
         $params = array(
                  'AWSAccessKeyId'=> urlencode($this->access_key),
                  'SellerId'=> urlencode($this->seller_id),
				  'MWSAuthToken'=>urlencode($this->auth_token),
                  'SignatureMethod' => urlencode("HmacSHA256"),
                  'SignatureVersion'=> urlencode("2"),
                  'Timestamp'=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
                  'Version' => urlencode("2011-10-01"),
                  'MarketplaceId'=>$this->market_id

                 );
  
            $params=array_merge($params,$add_param);
          $url_parts = array();
        foreach(array_keys($params) as $key)
        {
            $url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
        }
        sort($url_parts);
            $url_string = implode("&", $url_parts);
            $string_to_sign = "POST\nmws.amazonservices.com\n/Products/2011-10-01\n" . $url_string;
            
            $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
            $signature = urlencode(base64_encode($signature));
            $url = "https://mws.amazonservices.com/Products/2011-10-01?". $url_string . "&Signature=" . $signature;
            return $url; 
 }
}