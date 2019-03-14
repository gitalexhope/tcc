<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Amazon_lowest_priced_api extends CI_Model
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
      $param = array();
      $param['Action']           = 'GetLowestPricedOffersForASIN';
      $param['ASIN']             = $ean;
      $param['ItemCondition']    = 'New';
      $param['SellerId']         =  urlencode($this->seller_id);
      $param['AWSAccessKeyId']   =  urlencode($this->access_key);
      $param['MarketplaceId']    =  urlencode($this->market_id);
      $param['SignatureMethod']  = 'HmacSHA256';
      $param['SignatureVersion'] = '2';
      $param['Timestamp']        = gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
      $param['Version']          = '2011-10-01';
      $secret =urlencode($this->secret_key);
      $url = array();
      foreach ($param as $key => $val) {
          $key = str_replace("%7E", "~", rawurlencode($key));
          $val = str_replace("%7E", "~", rawurlencode($val));
          $url[] = "{$key}={$val}";
      }
      sort($url);
      $arr   = implode('&', $url);
      $sign  = 'POST' . "\n";
      $sign .= 'mws.amazonservices.com' . "\n";
      $sign .= '/Products/2011-10-01' . "\n";
      $sign .= $arr;
      $signature = hash_hmac("sha256", $sign, $secret, true);
      $s64 = base64_encode($signature);
      $signature = urlencode($s64);
      $link  = "https://mws.amazonservices.com/Products/2011-10-01";
      $arr .= "&Signature=" . $signature;
      $ch = curl_init($link);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Accept:'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
      $response = curl_exec($ch);
      //echo "response1:$response";
	  //die();
      $res = simplexml_load_string($response);
      //echo "response:$res";
	  //die();
      $namespaces = $res->getNamespaces(true);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $payload=[];
      $payload['no_of_fba_offers']=$payload['min_fba_price']=$payload['min_fba_ship_price']=$payload['max_fba_price']=$payload['max_fba_price']=$payload['avg_fba_price']=$payload['avg_fba_ship_price']=$payload['bb_price']=$payload['bb_ship_price']=$payload['bb_offer_type']='';
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
      if(preg_match('/Invalid ASIN/',(string)$res->GetLowestPricedOffersForASINResult->Error->Message))
      {

          echo "ERROR ".(string)$res->GetLowestPricedOffersForASINResult->Error->Message;
          $data['status_code']=3;
          $data['status_text']="No Data";
          $payload['lm_ean']=$ean;
          $payload['asin_counts']=-3;
          $payload['lm_asin']='';
          $data['payload']=$payload;
          return $data;
       }


	   if(preg_match_all('/<Summary>\s*<TotalOfferCount>[^>]*?<\/TotalOfferCount>\s*<NumberOfOffers>\s*<OfferCount condition="new" fulfillmentChannel="Amazon">([^>]*?)<\/OfferCount>/',$response,$matches)){
	    $res=$matches[1];
	    $payload['no_of_fba_offers']=$res[0];
       }
	   //print_r( $payload['no_of_fba_offers']);
	   //die();
	   if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>([^>]*?)<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>true<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>[^>]*?<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	    $res=$matches[1];
	    $payload['min_fba_price']=$res[0];
       }
	   if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>([^>]*?)<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>true<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>[^>]*?<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	    $res=$matches[1];
	    $payload['min_fba_ship_price']=$res[0];
       }
	    if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>([^>]*?)<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>true<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>[^>]*?<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	    $res=$matches[1];
		//print_r($res);
		//die();
	    $payload['max_fba_price']=$res[$payload['no_of_fba_offers']-1];
        }
		if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>([^>]*?)<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>true<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>[^>]*?<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	    $res=$matches[1];
		//print_r($res);
		//die();
	    $payload['max_fba_ship_price']=$res[$payload['no_of_fba_offers']-1];
        }
		if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>([^>]*?)<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>true<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>[^>]*?<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	    $res=$matches[1];
		$count=count($res);
		$payload['avg_fba_price']=array_sum($res)/$count;
        }
		if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>([^>]*?)<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>true<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>[^>]*?<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	    $res=$matches[1];
		$count=count($res);
	    $payload['avg_fba_ship_price']=array_sum($res)/$count;
        }
		if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>([^>]*?)<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>[^>]*?<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>true<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	    $res=$matches[1];
	    $payload['bb_price']=$res[0];
        }
		if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>([^>]*?)<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>[^>]*?<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>true<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	    $res=$matches[1];
	    $payload['bb_ship_price']=$res[0];
        }
	   if(preg_match_all('/<Offer>\s*<SubCondition>[^>]*?<\/SubCondition>\s*<SellerFeedbackRating>\s*<SellerPositiveFeedbackRating>[^>]*?<\/SellerPositiveFeedbackRating>\s*<FeedbackCount>[^>]*?<\/FeedbackCount>\s*<\/SellerFeedbackRating>\s*<ShippingTime minimumHours="[^>]*?" maximumHours="[^>]*?" availabilityType="[^>]*?"\/>\s*<ListingPrice>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/ListingPrice>\s*<Shipping>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<Amount>[^>]*?<\/Amount>\s*<\/Shipping>\s*<IsFulfilledByAmazon>([^>]*?)<\/IsFulfilledByAmazon>\s*<IsBuyBoxWinner>true<\/IsBuyBoxWinner>\s*<IsFeaturedMerchant>[^>]*?<\/IsFeaturedMerchant>\s*<\/Offer>/',$response,$matches)){
	   $res=$matches[1];
	   $payload['bb_offer_type']=$res[0];
       }
	//print_r($payload['avg_fba_price']);
	// echo"\n";
	// print_r($payload['no_of_fba_offers']);
	// echo"\n";
	// print_r($payload['min_fba_price']);
	// echo"\n";
	//  print_r($payload['max_fba_price']);
	// echo"\n";
	// print_r($payload['bb_price']);
	// echo"\n";
	   //die();
      if(count($payload) > 0 && !empty($payload['no_of_fba_offers']))
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
				  //'MWSAuthToken'=>urlencode($this->auth_token),
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
            $string_to_sign = "POST\mws.amazonservices.com\n/Products/2011-10-01\n" . $url_string;

            $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
            $signature = urlencode(base64_encode($signature));
            $url = "https://mws.amazonservices.com/Products/2011-10-01?". $url_string . "&Signature=" . $signature;
            // print_r($url);
		   return $url;
 }
}
