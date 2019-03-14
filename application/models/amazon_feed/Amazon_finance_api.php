<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Amazon_finance_api extends CI_Model
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
  public function get_orders_to_match($limit=200,$u_id)
  {
    $query=$this->db->query("SELECT prod_id,order_id FROM rep_orders_data_order_date_list WHERE ord_status='shipped' AND fee_flag='0' and user_id={$u_id} ORDER BY po_date DESC limit 0,".$limit);
    return $query->result_array();
  }

  public function fetch_product_details($user_id,$order_id)
  {
    try
    {
      $httpHeader=array();
      $httpHeader[]='Transfer-Encoding: chunked';
      $httpHeader[]='Content-Type: text/json';
      $httpHeader[]='Expect:';
      $httpHeader[]='Accept:';
      $param['Action']=urlencode("ListFinancialEvents");
      //$param['IdType']='ISBN';
      $param['AmazonOrderId']=$order_id;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
      curl_setopt($ch, CURLOPT_POST, true);
      $response = curl_exec($ch);
	  //$result=php_strip_whitespace($response);
      //echo "response1:$response";
	  $res = simplexml_load_string($response);
      //echo "response:$res";
	  //die();
      $namespaces = $res->getNamespaces(true);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $payload=[];
      $payload['order_id']=$payload['principal']=$payload['tax']=$payload['giftwrap']=$payload['giftwraptax']=$payload['shippingcharge']=$payload['shippingtax']=$payload['fbafee']=$payload['commission']=$payload['fixedclosingfee']=$payload['giftwrapchargeback']=
	  $payload['shippingchargeback']=$payload['variableclosingfee']=$payload['sku']=$payload['itemid']=$payload['marketplace']=$payload['qty']='';
      $payload['asin_counts']=-3;
      if($httpcode != 200)
      {
        if(preg_match('/throttled/',(string)$res->ListFinancialEventsResult->Error->Message ))
        {
          sleep(1);
          echo "throttling occured;\n";
          $this->fetch_product_details($user_id,$order_id);
        }

      }

        if(preg_match_all('/<ChargeComponent>\s*<ChargeType>[^>]*?<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['principal']=$res[0];
		$payload['tax']=$res[1];
		$payload['giftwrap']=$res[2];
		$payload['giftwraptax']=$res[3];
		$payload['shippingcharge']=$res[4];
		$payload['shippingtax']=$res[5];
		//print_r($payload['principal']);
        }

		if(preg_match_all('/<FeeComponent>\s*<FeeType>[^>]*?<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fbafee']=$res[0];
		$payload['commission']=$res[1];
		$payload['fixedclosingfee']=$res[2];
		$payload['giftwrapchargeback']=$res[3];
		$payload['shippingchargeback']=$res[4];
		$payload['variableclosingfee']=$res[5];
		//print_r($payload['principal']);
        }
		if(preg_match_all('/<AmazonOrderId>([^>]*?)<\/AmazonOrderId>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['order_id']=$res[0];
		}
		if(preg_match_all('/<SellerSKU>([^>]*?)<\/SellerSKU>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['sku']=$res[0];
		}
		if(preg_match_all('/<OrderItemId>([^>]*?)<\/OrderItemId>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['itemid']=$res[0];
		}
		if(preg_match_all('/<MarketplaceName>([^>]*?)<\/MarketplaceName>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['marketplace']=$res[0];
		}
		if(preg_match_all('/<QuantityShipped>([^>]*?)<\/QuantityShipped>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['qty']=$res[0];
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
          $payload['order_id']=$order_id;
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
				  //'MWSAuthToken'=>urlencode($this->auth_token),
                  'SignatureMethod' => urlencode("HmacSHA256"),
                  'SignatureVersion'=> urlencode("2"),
                  'Timestamp'=>gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
                  'Version' => urlencode("2015-05-01"),
                  //'MarketplaceId'=>$this->market_id

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
            $string_to_sign = "POST\nmws-eu.amazonservices.com\n/Finances/2015-05-01\n" . $url_string;

            $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
            $signature = urlencode(base64_encode($signature));
            $url = "https://mws-eu.amazonservices.com/Finances/2015-05-01?". $url_string . "&Signature=" . $signature;
            return $url;
 }

}
