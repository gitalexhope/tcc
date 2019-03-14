<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Process_finance_api extends CI_Model
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

    $this->db->select('amazon_profile.*,country_code');
    $this->db->from('amazon_profile');
    if ($user_id !='') {
      $this->db->where('profile_id',$this->db->escape($user_id));
    }
    $this->db->join('seller_country_mapping','seller_country_mapping.seller_id = amazon_profile.profile_id','inner');
    $this->db->where('seller_country_mapping.status',1);
    $this->db->order_by('profile_id','ASC');
    $query = $this->db->get();
    return $query->result_array();
    //
    // $sql="SELECT profile_id,prf.seller_id,auth_token,access_key,secret_key,amz_code,cnt.country_code,country_name,mws_url,amz_code FROM amazon_profile AS prf
    //       INNER JOIN seller_country_mapping AS cnt ON ";
    //         if(!empty($user_id))
    //         {
    //           $sql.=" profile_id=".$this->db->escape($user_id)." AND ";
    //         }
    // $sql.=" prf.is_active=1 AND cnt.seller_id=profile_id
    // INNER JOIN supported_country AS spt ON spt.country_code=cnt.country_code AND spt.is_active=1  ORDER BY profile_id ASC";
    // $query=$this->db->query($sql);
    // return $query->result_array();
  }

  public function set_credentials($usr)
  {
        $this->seller_id=$usr['seller_id'];
        $this->auth_token=$usr['auth_token'];
        $this->access_key=$usr['access_key'];
        $this->secret_key=$usr['secret_key'];
        $this->market_id=$usr['market_placeID'];
        $this->mws_site=$usr['mws_endpoint'];
        $this->ch = curl_init();
        return TRUE;
  }
  public function get_product_to_match($user_id,$country_code)
  {
    $sql="SELECT prod_id,order_id,sales_channel FROM rep_orders_data_order_date_list where user_id=".$user_id." AND ord_status='Shipped' and sales_channel=".$this->db->escape($country_code)." LIMIT 0,2000";
    $query=$this->db->query($sql);
    return $query->result_array();
  }

  public function fetch_product_details($user_id,$order_id,$amz_country_code,$country_code)
  {
    try
    {
      $param['Action']=urlencode("ListFinancialEvents");
      //$param['IdType']='ISBN';
      $param['AmazonOrderId']=$order_id;
      // $param['AmazonOrderId']='112-6678342-5017826';
      // $param['AmazonOrderId']='113-8782293-8724236';
      //$param['MarketplaceId']=$amz_country_code;

      $curl_res=$this->create_curl_request($param);
      if($curl_res['status_code']==0)
      {
        throw new Exception($curl_res['status_text']);
      }

      $response=$curl_res['payload'];
      $res = simplexml_load_string($curl_res['payload']);
      $payload=[];
      // $payload['order_id']=$payload['principal']=$payload['tax']=$payload['giftwrap']=$payload['giftwraptax']=$payload['shippingcharge']=$payload['shippingtax']=$payload['fbafee']=$payload['commission']=$payload['fixedclosingfee']=$payload['giftwrapchargeback']=
	    // $payload['shippingchargeback']=$payload['variableclosingfee']=$payload['sku']=$payload['itemid']=$payload['marketplace']=$payload['qty']=$payload['posted_date']='';
	    // $payload['asin_counts']=-3;
      echo "<pre>";
      // print_r($res);die;

      // checking if multiple shipment for one order {{foreach}}
      if (isset($res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent[0]))
      {
          $i = 0;
          // start for each
          foreach ($res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent as $key => $ShipmentEvent)
          {
              $payload[$i]['order_id']    = (string) $ShipmentEvent->AmazonOrderId;
              $payload[$i]['marketplace'] = (string) $ShipmentEvent->MarketplaceName;
              $payload[$i]['posted_date'] = (string) $ShipmentEvent->PostedDate;
              $payload[$i]['sku']         = (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->SellerSKU;
              $payload[$i]['itemid']      = (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->OrderItemId;
              $payload[$i]['qty']         = (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->QuantityShipped;

              $shipmentItems = $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemChargeList;
              $promotions    = isset($ShipmentEvent->ShipmentItemList->ShipmentItem->PromotionList) ? $ShipmentEvent->ShipmentItemList->ShipmentItem->PromotionList : [];

              $payload[$i]['promo_price1'] = '0.00';
              $payload[$i]['promo_price2'] = '0.00';
              $payload[$i]['promo_price3'] = '0.00';
              $payload[$i]['promo_price4'] = '0.00';
              $payload[$i]['promo_price5'] = '0.00';
              $payload[$i]['promo_price6'] = '0.00';
              if (!empty($promotions))
              {
                  if (isset($promotions->Promotion[0]))
                  {
                    $payload[$i]['promo_price1']=isset($promotions->Promotion[0]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[0]->PromotionAmount->CurrencyAmount : '0.00';
                    $payload[$i]['promo_price2']=isset($promotions->Promotion[1]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[1]->PromotionAmount->CurrencyAmount : '0.00';
                    $payload[$i]['promo_price3']=isset($promotions->Promotion[2]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[2]->PromotionAmount->CurrencyAmount : '0.00';
                    $payload[$i]['promo_price4']=isset($promotions->Promotion[3]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[3]->PromotionAmount->CurrencyAmount : '0.00';
                    $payload[$i]['promo_price5']=isset($promotions->Promotion[5]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[5]->PromotionAmount->CurrencyAmount : '0.00';
                    $payload[$i]['promo_price6']=isset($promotions->Promotion[6]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[6]->PromotionAmount->CurrencyAmount : '0.00';
                  }
                  else
                  {
                    $payload[$i]['promo_price1']=isset($promotions->Promotion->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion->PromotionAmount->CurrencyAmount : '0.00';
                  }
              }

              // echo "<pre>";print_r($shipmentItems);die;
              if (isset($ShipmentEvent->ShipmentItem->ItemFeeList->FeeComponent[0]))
              {
                foreach ($ShipmentEvent->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent as $ItemFeeList)
                {
                  if ( (string) $ItemFeeList->FeeType == 'FBAPerUnitFulfillmentFee')
                  {
                    $payload[$i]['fbafee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fbafee'] = isset($payload[$i]['fbafee']) ? $payload[$i]['fbafee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'Commission')
                  {
                    $payload[$i]['commission'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['commission'] = isset($payload[$i]['commission']) ? $payload[$i]['commission'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FixedClosingFee')
                  {
                    $payload[$i]['fixedclosingfee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fixedclosingfee'] = isset($payload[$i]['fixedclosingfee']) ? $payload[$i]['fixedclosingfee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'GiftwrapChargeback')
                  {
                    $payload[$i]['giftwrapchargeback'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['giftwrapchargeback'] = isset($payload[$i]['giftwrapchargeback']) ? $payload[$i]['giftwrapchargeback'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'ShippingChargeback')
                  {
                    $payload[$i]['shippingchargeback'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['shippingchargeback'] = isset($payload[$i]['shippingchargeback']) ? $payload[$i]['shippingchargeback'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'VariableClosingFee')
                  {
                    $payload[$i]['variableclosingfee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['variableclosingfee'] = isset($payload[$i]['variableclosingfee']) ? $payload[$i]['variableclosingfee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'BubblewrapFee')
                  {
                    $payload[$i]['bubble_wrap_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['bubble_wrap_fee'] = isset($payload[$i]['bubble_wrap_fee']) ? $payload[$i]['bubble_wrap_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBACustomerReturnPerOrderFee')
                  {
                    $payload[$i]['fba_cus_ret_per_order_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_cus_ret_per_order_fee'] = isset($payload[$i]['fba_cus_ret_per_order_fee']) ? $payload[$i]['fba_cus_ret_per_order_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBACustomerReturnPerUnitFee')
                  {
                    $payload[$i]['fba_cus_ret_per_unit_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_cus_ret_per_unit_fee'] = isset($payload[$i]['fba_cus_ret_per_unit_fee']) ? $payload[$i]['fba_cus_ret_per_unit_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBACustomerReturnWeightBasedFee')
                  {
                    $payload[$i]['fba_cus_ret_weightbased_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_cus_ret_weightbased_fee'] = isset($payload[$i]['fba_cus_ret_weightbased_fee']) ? $payload[$i]['fba_cus_ret_weightbased_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBADisposalFee')
                  {
                    $payload[$i]['fba_disposal_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_disposal_fee'] = isset($payload[$i]['fba_disposal_fee']) ? $payload[$i]['fba_disposal_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAFulfillmentCODFee')
                  {
                    $payload[$i]['fba_fulfil_cod_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_fulfil_cod_fee'] = isset($payload[$i]['fba_fulfil_cod_fee']) ? $payload[$i]['fba_fulfil_cod_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAInboundConvenienceFee')
                  {
                    $payload[$i]['fba_inb_con_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_inb_con_fee'] = isset($payload[$i]['fba_inb_con_fee']) ? $payload[$i]['fba_inb_con_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAInboundDefectFee')
                  {
                    $payload[$i]['fba_inb_def_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_inb_def_fee'] = isset($payload[$i]['fba_inb_def_fee']) ? $payload[$i]['fba_inb_def_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAInboundTransportationFee')
                  {
                    $payload[$i]['fba_inb_transport_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_inb_transport_fee'] = isset($payload[$i]['fba_inb_transport_fee']) ? $payload[$i]['fba_inb_transport_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAInboundTransportationProgramFee')
                  {
                    $payload[$i]['fba_inb_transport_program_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_inb_transport_program_fee'] = isset($payload[$i]['fba_inb_transport_program_fee']) ? $payload[$i]['fba_inb_transport_program_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBALongTermStorageFee')
                  {
                    $payload[$i]['fba_longterm_storage_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_longterm_storage_fee'] = isset($payload[$i]['fba_longterm_storage_fee']) ? $payload[$i]['fba_longterm_storage_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAOverageFee')
                  {
                    $payload[$i]['fba_overage_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_overage_fee'] = isset($payload[$i]['fba_overage_fee']) ? $payload[$i]['fba_overage_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAPerOrderFulfillmentFee')
                  {
                    $payload[$i]['fba_perorder_fulfill_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_perorder_fulfill_fee'] = isset($payload[$i]['fba_perorder_fulfill_fee']) ? $payload[$i]['fba_perorder_fulfill_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBARemovalFee')
                  {
                    $payload[$i]['fba_removal_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_removal_fee'] = isset($payload[$i]['fba_removal_fee']) ? $payload[$i]['fba_removal_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAStorageFee')
                  {
                    $payload[$i]['fba_storage_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_storage_fee'] = isset($payload[$i]['fba_storage_fee']) ? $payload[$i]['fba_storage_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBATransportationFee')
                  {
                    $payload[$i]['fba_transport_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_transport_fee'] = isset($payload[$i]['fba_transport_fee']) ? $payload[$i]['fba_transport_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FBAWeightBasedFee')
                  {
                    $payload[$i]['fba_weightbased_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fba_weightbased_fee'] = isset($payload[$i]['fba_weightbased_fee']) ? $payload[$i]['fba_weightbased_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FulfillmentFee')
                  {
                    $payload[$i]['fullfill_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fullfill_fee'] = isset($payload[$i]['fullfill_fee']) ? $payload[$i]['fullfill_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'FulfillmentNetworkFee')
                  {
                    $payload[$i]['fullfill_network_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['fullfill_network_fee'] = isset($payload[$i]['fullfill_network_fee']) ? $payload[$i]['fullfill_network_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'LabelingFee')
                  {
                    $payload[$i]['lable_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['lable_fee'] = isset($payload[$i]['lable_fee']) ? $payload[$i]['lable_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'OpaqueBaggingFee')
                  {
                    $payload[$i]['opa_bagging_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['opa_bagging_fee'] = isset($payload[$i]['opa_bagging_fee']) ? $payload[$i]['opa_bagging_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'PolybaggingFee')
                  {
                    $payload[$i]['poly_bagging_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['poly_bagging_fee'] = isset($payload[$i]['poly_bagging_fee']) ? $payload[$i]['poly_bagging_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'SSOFFulfillmentFee')
                  {
                    $payload[$i]['ssof_fullfill_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['ssof_fullfill_fee'] = isset($payload[$i]['ssof_fullfill_fee']) ? $payload[$i]['ssof_fullfill_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'TapingFee')
                  {
                    $payload[$i]['taping_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['taping_fee'] = isset($payload[$i]['taping_fee']) ? $payload[$i]['taping_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'TransportationFee')
                  {
                    $payload[$i]['transport_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['transport_fee'] = isset($payload[$i]['transport_fee']) ? $payload[$i]['transport_fee'] : '0.00';
                  }
                  if ( (string) $ItemFeeList->FeeType == 'UnitFulfillmentFee')
                  {
                    $payload[$i]['UnitFulfillmentFee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                  }else{
                    $payload[$i]['UnitFulfillmentFee'] = isset($payload[$i]['UnitFulfillmentFee']) ? $payload[$i]['UnitFulfillmentFee'] : '0.00';
                  }

                }
              }
              else
              {
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAPerUnitFulfillmentFee')
                {
                  $payload[$i]['fbafee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fbafee'] = isset($payload[$i]['fbafee']) ? $payload[$i]['fbafee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'Commission')
                {
                  $payload[$i]['commission'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['commission'] = isset($payload[$i]['commission']) ? $payload[$i]['commission'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FixedClosingFee')
                {
                  $payload[$i]['fixedclosingfee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fixedclosingfee'] = isset($payload[$i]['fixedclosingfee']) ? $payload[$i]['fixedclosingfee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'GiftwrapChargeback')
                {
                  $payload[$i]['giftwrapchargeback'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['giftwrapchargeback'] = isset($payload[$i]['giftwrapchargeback']) ? $payload[$i]['giftwrapchargeback'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'ShippingChargeback')
                {
                  $payload[$i]['shippingchargeback'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['shippingchargeback'] = isset($payload[$i]['shippingchargeback']) ? $payload[$i]['shippingchargeback'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'VariableClosingFee')
                {
                  $payload[$i]['variableclosingfee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['variableclosingfee'] = isset($payload[$i]['variableclosingfee']) ? $payload[$i]['variableclosingfee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'BubblewrapFee')
                {
                  $payload[$i]['bubble_wrap_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['bubble_wrap_fee'] = isset($payload[$i]['bubble_wrap_fee']) ? $payload[$i]['bubble_wrap_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBACustomerReturnPerOrderFee')
                {
                  $payload[$i]['fba_cus_ret_per_order_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_cus_ret_per_order_fee'] = isset($payload[$i]['fba_cus_ret_per_order_fee']) ? $payload[$i]['fba_cus_ret_per_order_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBACustomerReturnPerUnitFee')
                {
                  $payload[$i]['fba_cus_ret_per_unit_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_cus_ret_per_unit_fee'] = isset($payload[$i]['fba_cus_ret_per_unit_fee']) ? $payload[$i]['fba_cus_ret_per_unit_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBACustomerReturnWeightBasedFee')
                {
                  $payload[$i]['fba_cus_ret_weightbased_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_cus_ret_weightbased_fee'] = isset($payload[$i]['fba_cus_ret_weightbased_fee']) ? $payload[$i]['fba_cus_ret_weightbased_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBADisposalFee')
                {
                  $payload[$i]['fba_disposal_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_disposal_fee'] = isset($payload[$i]['fba_disposal_fee']) ? $payload[$i]['fba_disposal_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAFulfillmentCODFee')
                {
                  $payload[$i]['fba_fulfil_cod_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_fulfil_cod_fee'] = isset($payload[$i]['fba_fulfil_cod_fee']) ? $payload[$i]['fba_fulfil_cod_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAInboundConvenienceFee')
                {
                  $payload[$i]['fba_inb_con_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_inb_con_fee'] = isset($payload[$i]['fba_inb_con_fee']) ? $payload[$i]['fba_inb_con_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAInboundDefectFee')
                {
                  $payload[$i]['fba_inb_def_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_inb_def_fee'] = isset($payload[$i]['fba_inb_def_fee']) ? $payload[$i]['fba_inb_def_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAInboundTransportationFee')
                {
                  $payload[$i]['fba_inb_transport_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_inb_transport_fee'] = isset($payload[$i]['fba_inb_transport_fee']) ? $payload[$i]['fba_inb_transport_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAInboundTransportationProgramFee')
                {
                  $payload[$i]['fba_inb_transport_program_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_inb_transport_program_fee'] = isset($payload[$i]['fba_inb_transport_program_fee']) ? $payload[$i]['fba_inb_transport_program_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBALongTermStorageFee')
                {
                  $payload[$i]['fba_longterm_storage_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_longterm_storage_fee'] = isset($payload[$i]['fba_longterm_storage_fee']) ? $payload[$i]['fba_longterm_storage_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAOverageFee')
                {
                  $payload[$i]['fba_overage_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_overage_fee'] = isset($payload[$i]['fba_overage_fee']) ? $payload[$i]['fba_overage_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAPerOrderFulfillmentFee')
                {
                  $payload[$i]['fba_perorder_fulfill_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_perorder_fulfill_fee'] = isset($payload[$i]['fba_perorder_fulfill_fee']) ? $payload[$i]['fba_perorder_fulfill_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBARemovalFee')
                {
                  $payload[$i]['fba_removal_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_removal_fee'] = isset($payload[$i]['fba_removal_fee']) ? $payload[$i]['fba_removal_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAStorageFee')
                {
                  $payload[$i]['fba_storage_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_storage_fee'] = isset($payload[$i]['fba_storage_fee']) ? $payload[$i]['fba_storage_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBATransportationFee')
                {
                  $payload[$i]['fba_transport_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_transport_fee'] = isset($payload[$i]['fba_transport_fee']) ? $payload[$i]['fba_transport_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FBAWeightBasedFee')
                {
                  $payload[$i]['fba_weightbased_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fba_weightbased_fee'] = isset($payload[$i]['fba_weightbased_fee']) ? $payload[$i]['fba_weightbased_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FulfillmentFee')
                {
                  $payload[$i]['fullfill_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fullfill_fee'] = isset($payload[$i]['fullfill_fee']) ? $payload[$i]['fullfill_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'FulfillmentNetworkFee')
                {
                  $payload[$i]['fullfill_network_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['fullfill_network_fee'] = isset($payload[$i]['fullfill_network_fee']) ? $payload[$i]['fullfill_network_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'LabelingFee')
                {
                  $payload[$i]['lable_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['lable_fee'] = isset($payload[$i]['lable_fee']) ? $payload[$i]['lable_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'OpaqueBaggingFee')
                {
                  $payload[$i]['opa_bagging_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['opa_bagging_fee'] = isset($payload[$i]['opa_bagging_fee']) ? $payload[$i]['opa_bagging_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'PolybaggingFee')
                {
                  $payload[$i]['poly_bagging_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['poly_bagging_fee'] = isset($payload[$i]['poly_bagging_fee']) ? $payload[$i]['poly_bagging_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'SSOFFulfillmentFee')
                {
                  $payload[$i]['ssof_fullfill_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['ssof_fullfill_fee'] = isset($payload[$i]['ssof_fullfill_fee']) ? $payload[$i]['ssof_fullfill_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'TapingFee')
                {
                  $payload[$i]['taping_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['taping_fee'] = isset($payload[$i]['taping_fee']) ? $payload[$i]['taping_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'TransportationFee')
                {
                  $payload[$i]['transport_fee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['transport_fee'] = isset($payload[$i]['transport_fee']) ? $payload[$i]['transport_fee'] : '0.00';
                }
                if ( (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeType == 'UnitFulfillmentFee')
                {
                  $payload[$i]['UnitFulfillmentFee'] 									= (string) $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[$i]['UnitFulfillmentFee'] = isset($payload[$i]['UnitFulfillmentFee']) ? $payload[$i]['UnitFulfillmentFee'] : '0.00';
                }
              }

              if (isset($shipmentItems->ChargeComponent[0]))
              {
                foreach ($shipmentItems->ChargeComponent as $key => $ItemChargeList)
                {

                    if ( (string) $ItemChargeList->ChargeType == 'Principal')
                    {
                      $payload[$i]['principal'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                      // echo "<pre>";print_r($payload); die('dfgsdfgsfdg');
                    }else{
                      $payload[$i]['principal'] = isset($payload[$i]['principal']) ? $payload[$i]['principal'] : isset($payload[$i]['principal'] ) ? $payload[$i]['principal'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'Tax')
                    {
                      $payload[$i]['tax'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tax'] = isset($payload[$i]['tax']) ? $payload[$i]['tax'] : isset($payload[$i]['tax'] ) ? $payload[$i]['tax'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'GiftWrap')
                    {
                      $payload[$i]['giftwrap'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['giftwrap'] = isset($payload[$i]['giftwrap']) ? $payload[$i]['giftwrap'] : isset($payload[$i]['giftwrap'] ) ? $payload[$i]['giftwrap'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'GiftWrapTax')
                    {
                      $payload[$i]['giftwraptax'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['giftwraptax'] = isset($payload[$i]['giftwraptax']) ? $payload[$i]['giftwraptax'] : isset($payload[$i]['giftwraptax'] ) ? $payload[$i]['giftwraptax'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'ShippingCharge')
                    {
                      $payload[$i]['shippingcharge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['shippingcharge'] = isset($payload[$i]['shippingcharge']) ? $payload[$i]['shippingcharge'] : isset($payload[$i]['shippingcharge'] ) ? $payload[$i]['shippingcharge'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'ShippingTax')
                    {
                      $payload[$i]['shippingtax'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['shippingtax'] = isset($payload[$i]['shippingtax']) ? $payload[$i]['shippingtax'] : isset($payload[$i]['shippingtax'] ) ? $payload[$i]['shippingtax'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'MarketplaceFacilitatorTax-Principal')
                    {
                      $payload[$i]['market_facilatortax_principal'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['market_facilatortax_principal'] = isset($payload[$i]['market_facilatortax_principal']) ? $payload[$i]['market_facilatortax_principal'] : isset($payload[$i]['market_facilatortax_principal'] ) ? $payload[$i]['market_facilatortax_principal'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'MarketplaceFacilitatorTax-Shipping')
                    {
                      $payload[$i]['market_facilatortax_shipping'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['market_facilatortax_shipping'] = isset($payload[$i]['market_facilatortax_shipping']) ? $payload[$i]['market_facilatortax_shipping'] : isset($payload[$i]['market_facilatortax_shipping'] ) ? $payload[$i]['market_facilatortax_shipping'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'MarketplaceFacilitatorTax-Giftwrap')
                    {
                      $payload[$i]['market_facilatortax_giftwrap'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['market_facilatortax_giftwrap'] = isset($payload[$i]['market_facilatortax_giftwrap']) ? $payload[$i]['market_facilatortax_giftwrap'] : isset($payload[$i]['market_facilatortax_giftwrap'] ) ? $payload[$i]['market_facilatortax_giftwrap'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'MarketplaceFacilitatorTax-Other')
                    {
                      $payload[$i]['market_facilatortax_other'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['market_facilatortax_other'] = isset($payload[$i]['market_facilatortax_other']) ? $payload[$i]['market_facilatortax_other'] : isset($payload[$i]['market_facilatortax_other'] ) ? $payload[$i]['market_facilatortax_other'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'TaxDiscount')
                    {
                      $payload[$i]['taxdiscount'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['taxdiscount'] = isset($payload[$i]['taxdiscount']) ? $payload[$i]['taxdiscount'] : isset($payload[$i]['taxdiscount'] ) ? $payload[$i]['taxdiscount'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'CODItemCharge')
                    {
                      $payload[$i]['cod_item_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_item_charge'] = isset($payload[$i]['cod_item_charge']) ? $payload[$i]['cod_item_charge'] : isset($payload[$i]['cod_item_charge'] ) ? $payload[$i]['cod_item_charge'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'CODItemTaxCharge')
                    {
                      $payload[$i]['cod_item_tax_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_item_tax_charge'] = isset($payload[$i]['cod_item_tax_charge']) ? $payload[$i]['cod_item_tax_charge'] : isset($payload[$i]['cod_item_tax_charge'] ) ? $payload[$i]['cod_item_tax_charge'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'CODOrderCharge')
                    {
                      $payload[$i]['cod_order_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_order_charge'] = isset($payload[$i]['cod_order_charge']) ? $payload[$i]['cod_order_charge'] : isset($payload[$i]['cod_order_charge'] ) ? $payload[$i]['cod_order_charge'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'CODOrderTaxCharge')
                    {
                      $payload[$i]['cod_order_tax_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_order_tax_charge'] = isset($payload[$i]['cod_order_tax_charge']) ? $payload[$i]['cod_order_tax_charge'] : isset($payload[$i]['cod_order_tax_charge'] ) ? $payload[$i]['cod_order_tax_charge'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'CODShippingCharge')
                    {
                      $payload[$i]['cod_shipping_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_shipping_charge'] = isset($payload[$i]['cod_shipping_charge']) ? $payload[$i]['cod_shipping_charge'] : isset($payload[$i]['cod_shipping_charge'] ) ? $payload[$i]['cod_shipping_charge'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'CODShippingTaxCharge')
                    {
                      $payload[$i]['cod_shipping_tax_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_shipping_tax_charge'] = isset($payload[$i]['cod_shipping_tax_charge']) ? $payload[$i]['cod_shipping_tax_charge'] : isset($payload[$i]['cod_shipping_tax_charge'] ) ? $payload[$i]['cod_shipping_tax_charge'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'Goodwill')
                    {
                      $payload[$i]['good_will'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['good_will'] = isset($payload[$i]['good_will']) ? $payload[$i]['good_will'] : isset($payload[$i]['good_will'] ) ? $payload[$i]['good_will'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'RestockingFee')
                    {
                      $payload[$i]['restocking_fee'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['restocking_fee'] = isset($payload[$i]['restocking_fee']) ? $payload[$i]['restocking_fee'] : isset($payload[$i]['restocking_fee'] ) ? $payload[$i]['restocking_fee'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'ReturnShipping')
                    {
                      $payload[$i]['return_shipping'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['return_shipping'] = isset($payload[$i]['return_shipping']) ? $payload[$i]['return_shipping'] : isset($payload[$i]['return_shipping'] ) ? $payload[$i]['return_shipping'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'PointsFee')
                    {
                      $payload[$i]['points_fee'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['points_fee'] = isset($payload[$i]['points_fee']) ? $payload[$i]['points_fee'] : isset($payload[$i]['points_fee'] ) ? $payload[$i]['points_fee'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'GenericDeduction')
                    {
                      $payload[$i]['generic_deduction'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['generic_deduction'] = isset($payload[$i]['generic_deduction']) ? $payload[$i]['generic_deduction'] : isset($payload[$i]['generic_deduction'] ) ? $payload[$i]['generic_deduction'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'FreeReplacementReturnShipping')
                    {
                      $payload[$i]['free_replace_ret_shipping'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['free_replace_ret_shipping'] = isset($payload[$i]['free_replace_ret_shipping']) ? $payload[$i]['free_replace_ret_shipping'] : isset($payload[$i]['free_replace_ret_shipping'] ) ? $payload[$i]['free_replace_ret_shipping'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'PaymentMethodFee')
                    {
                      $payload[$i]['payment_method_fee'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['payment_method_fee'] = isset($payload[$i]['payment_method_fee']) ? $payload[$i]['payment_method_fee'] : isset($payload[$i]['payment_method_fee'] ) ? $payload[$i]['payment_method_fee'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'ExportCharge')
                    {
                      $payload[$i]['export_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['export_charge'] = isset($payload[$i]['export_charge']) ? $payload[$i]['export_charge'] : isset($payload[$i]['export_charge'] ) ? $payload[$i]['export_charge'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'SAFE-TReimbursement')
                    {
                      $payload[$i]['safe_t_claim'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['safe_t_claim'] = isset($payload[$i]['safe_t_claim']) ? $payload[$i]['safe_t_claim'] : isset($payload[$i]['safe_t_claim'] ) ? $payload[$i]['safe_t_claim'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'TCS-CGST')
                    {
                      $payload[$i]['tcs_cgst'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tcs_cgst'] = isset($payload[$i]['tcs_cgst']) ? $payload[$i]['tcs_cgst'] : isset($payload[$i]['tcs_cgst'] ) ? $payload[$i]['tcs_cgst'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'TCS-SGST')
                    {
                      $payload[$i]['tcs_sgst'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tcs_sgst'] = isset($payload[$i]['tcs_sgst']) ? $payload[$i]['tcs_sgst'] : isset($payload[$i]['tcs_sgst'] ) ? $payload[$i]['tcs_sgst'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'TCS-IGST')
                    {
                      $payload[$i]['tcs_igst'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tcs_igst'] = isset($payload[$i]['tcs_igst']) ? $payload[$i]['tcs_igst'] : isset($payload[$i]['tcs_igst'] ) ? $payload[$i]['tcs_igst'] :  '0.00';
                    }
                    if ( (string) $ItemChargeList->ChargeType == 'TCS-UTGST')
                    {
                      $payload[$i]['tcs_utgst'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tcs_utgst'] = isset($payload[$i]['tcs_utgst']) ? $payload[$i]['tcs_utgst'] : isset($payload[$i]['tcs_utgst'] ) ? $payload[$i]['tcs_utgst'] :  '0.00';
                    }
                }
              }
              else
              {
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'Principal')
                    {
                      $payload[$i]['principal'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['principal'] = isset($payload[$i]['principal']) ? $payload[$i]['principal'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'Tax')
                    {
                      $payload[$i]['tax'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tax'] = isset($payload[$i]['tax']) ? $payload[$i]['tax'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'GiftWrap')
                    {
                      $payload[$i]['giftwrap'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['giftwrap'] = isset($payload[$i]['giftwrap']) ? $payload[$i]['giftwrap'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'GiftWrapTax')
                    {
                      $payload[$i]['giftwraptax'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['giftwraptax'] = isset($payload[$i]['giftwraptax']) ? $payload[$i]['giftwraptax'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'ShippingCharge')
                    {
                      $payload[$i]['shippingcharge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['shippingcharge'] = isset($payload[$i]['shippingcharge']) ? $payload[$i]['shippingcharge'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'ShippingTax')
                    {
                      $payload[$i]['shippingtax'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['shippingtax'] = isset($payload[$i]['shippingtax']) ? $payload[$i]['shippingtax'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'MarketplaceFacilitatorTax-Principal')
                    {
                      $payload[$i]['market_facilatortax_principal'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['market_facilatortax_principal'] = isset($payload[$i]['market_facilatortax_principal']) ? $payload[$i]['market_facilatortax_principal'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'MarketplaceFacilitatorTax-Shipping')
                    {
                      $payload[$i]['market_facilatortax_shipping'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['market_facilatortax_shipping'] = isset($payload[$i]['market_facilatortax_shipping']) ? $payload[$i]['market_facilatortax_shipping'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'MarketplaceFacilitatorTax-Giftwrap')
                    {
                      $payload[$i]['market_facilatortax_giftwrap'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['market_facilatortax_giftwrap'] = isset($payload[$i]['market_facilatortax_giftwrap']) ? $payload[$i]['market_facilatortax_giftwrap'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'MarketplaceFacilitatorTax-Other')
                    {
                      $payload[$i]['market_facilatortax_other'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['market_facilatortax_other'] = isset($payload[$i]['market_facilatortax_other']) ? $payload[$i]['market_facilatortax_other'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TaxDiscount')
                    {
                      $payload[$i]['taxdiscount'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['taxdiscount'] = isset($payload[$i]['taxdiscount']) ? $payload[$i]['taxdiscount'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODItemCharge')
                    {
                      $payload[$i]['cod_item_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_item_charge'] = isset($payload[$i]['cod_item_charge']) ? $payload[$i]['cod_item_charge'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODItemTaxCharge')
                    {
                      $payload[$i]['cod_item_tax_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_item_tax_charge'] = isset($payload[$i]['cod_item_tax_charge']) ? $payload[$i]['cod_item_tax_charge'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODOrderCharge')
                    {
                      $payload[$i]['cod_order_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_order_charge'] = isset($payload[$i]['cod_order_charge']) ? $payload[$i]['cod_order_charge'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODOrderTaxCharge')
                    {
                      $payload[$i]['cod_order_tax_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_order_tax_charge'] = isset($payload[$i]['cod_order_tax_charge']) ? $payload[$i]['cod_order_tax_charge'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODShippingCharge')
                    {
                      $payload[$i]['cod_shipping_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_shipping_charge'] = isset($payload[$i]['cod_shipping_charge']) ? $payload[$i]['cod_shipping_charge'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODShippingTaxCharge')
                    {
                      $payload[$i]['cod_shipping_tax_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['cod_shipping_tax_charge'] = isset($payload[$i]['cod_shipping_tax_charge']) ? $payload[$i]['cod_shipping_tax_charge'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'Goodwill')
                    {
                      $payload[$i]['good_will'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['good_will'] = isset($payload[$i]['good_will']) ? $payload[$i]['good_will'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'RestockingFee')
                    {
                      $payload[$i]['restocking_fee'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['restocking_fee'] = isset($payload[$i]['restocking_fee']) ? $payload[$i]['restocking_fee'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'ReturnShipping')
                    {
                      $payload[$i]['return_shipping'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['return_shipping'] = isset($payload[$i]['return_shipping']) ? $payload[$i]['return_shipping'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'PointsFee')
                    {
                      $payload[$i]['points_fee'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['points_fee'] = isset($payload[$i]['points_fee']) ? $payload[$i]['points_fee'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'GenericDeduction')
                    {
                      $payload[$i]['generic_deduction'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['generic_deduction'] = isset($payload[$i]['generic_deduction']) ? $payload[$i]['generic_deduction'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'FreeReplacementReturnShipping')
                    {
                      $payload[$i]['free_replace_ret_shipping'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['free_replace_ret_shipping'] = isset($payload[$i]['free_replace_ret_shipping']) ? $payload[$i]['free_replace_ret_shipping'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'PaymentMethodFee')
                    {
                      $payload[$i]['payment_method_fee'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['payment_method_fee'] = isset($payload[$i]['payment_method_fee']) ? $payload[$i]['payment_method_fee'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'ExportCharge')
                    {
                      $payload[$i]['export_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['export_charge'] = isset($payload[$i]['export_charge']) ? $payload[$i]['export_charge'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'SAFE-TReimbursement')
                    {
                      $payload[$i]['safe_t_claim'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['safe_t_claim'] = isset($payload[$i]['safe_t_claim']) ? $payload[$i]['safe_t_claim'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TCS-CGST')
                    {
                      $payload[$i]['tcs_cgst'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tcs_cgst'] = isset($payload[$i]['tcs_cgst']) ? $payload[$i]['tcs_cgst'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TCS-SGST')
                    {
                      $payload[$i]['tcs_sgst'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tcs_sgst'] = isset($payload[$i]['tcs_sgst']) ? $payload[$i]['tcs_sgst'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TCS-IGST')
                    {
                      $payload[$i]['tcs_igst'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tcs_igst'] = isset($payload[$i]['tcs_igst']) ? $payload[$i]['tcs_igst'] : '0.00';
                    }
                    if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TCS-UTGST')
                    {
                      $payload[$i]['tcs_utgst'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
                    }else{
                      $payload[$i]['tcs_utgst'] = isset($payload[$i]['tcs_utgst']) ? $payload[$i]['tcs_utgst'] : '0.00';
                    }
              }

              $i++;
          }
      }
      else
      {
        if (isset($res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent))
        {
            $payload[0]['order_id']    = (string) $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->AmazonOrderId;
            $payload[0]['marketplace'] = (string) $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->MarketplaceName;
            $payload[0]['posted_date'] = (string) $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->PostedDate;
            $payload[0]['sku']         = (string) $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->ShipmentItemList->ShipmentItem->SellerSKU;
            $payload[0]['itemid']      = (string) $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->ShipmentItemList->ShipmentItem->OrderItemId;
            $payload[0]['qty']         = (string) $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->ShipmentItemList->ShipmentItem->QuantityShipped;

            $shipmentItems = $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->ShipmentItemList->ShipmentItem->ItemChargeList;
            $promotions    = isset($res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->ShipmentItemList->ShipmentItem->PromotionList) ? $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->ShipmentItemList->ShipmentItem->PromotionList : [];


            $payload[$i]['promo_price1'] = '0.00';
            $payload[$i]['promo_price2'] = '0.00';
            $payload[$i]['promo_price3'] = '0.00';
            $payload[$i]['promo_price4'] = '0.00';
            $payload[$i]['promo_price5'] = '0.00';
            $payload[$i]['promo_price6'] = '0.00';
            if (!empty($promotions))
            {
                if (isset($promotions->Promotion[0]))
                {
                  $payload[$i]['promo_price1']=isset($promotions->Promotion[0]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[0]->PromotionAmount->CurrencyAmount : '0.00';
                  $payload[$i]['promo_price2']=isset($promotions->Promotion[1]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[1]->PromotionAmount->CurrencyAmount : '0.00';
                  $payload[$i]['promo_price3']=isset($promotions->Promotion[2]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[2]->PromotionAmount->CurrencyAmount : '0.00';
                  $payload[$i]['promo_price4']=isset($promotions->Promotion[3]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[3]->PromotionAmount->CurrencyAmount : '0.00';
                  $payload[$i]['promo_price5']=isset($promotions->Promotion[5]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[5]->PromotionAmount->CurrencyAmount : '0.00';
                  $payload[$i]['promo_price6']=isset($promotions->Promotion[6]->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion[6]->PromotionAmount->CurrencyAmount : '0.00';
                }
                else
                {
                  $payload[$i]['promo_price1']=isset($promotions->Promotion->PromotionAmount->CurrencyAmount) ? (string) $promotions->Promotion->PromotionAmount->CurrencyAmount : '0.00';
                }
            }

            if (isset($shipmentItems->ChargeComponent[0]))
            {
                foreach ($shipmentItems->ChargeComponent as $key => $ItemChargeList)
                {

                  if ( (string) $ItemChargeList->ChargeType == 'Principal')
                  {
                    $payload[0]['principal'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['principal'] = isset($payload[0]['principal']) ? $payload[0]['principal'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'Tax')
                  {
                    $payload[0]['tax'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['tax'] = isset($payload[0]['tax']) ? $payload[0]['tax'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'GiftWrap')
                  {
                    $payload[0]['giftwrap'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['giftwrap'] = isset($payload[0]['giftwrap']) ? $payload[0]['giftwrap'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'GiftWrapTax')
                  {
                    $payload[0]['giftwraptax'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['giftwraptax'] = isset($payload[0]['giftwraptax']) ? $payload[0]['giftwraptax'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'ShippingCharge')
                  {
                    $payload[0]['shippingcharge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['shippingcharge'] = isset($payload[0]['shippingcharge']) ? $payload[0]['shippingcharge'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'ShippingTax')
                  {
                    $payload[0]['shippingtax'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['shippingtax'] = isset($payload[0]['shippingtax']) ? $payload[0]['shippingtax'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'MarketplaceFacilitatorTax-Principal')
                  {
                    $payload[0]['market_facilatortax_principal'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['market_facilatortax_principal'] = isset($payload[0]['market_facilatortax_principal']) ? $payload[0]['market_facilatortax_principal'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'MarketplaceFacilitatorTax-Shipping')
                  {
                    $payload[0]['market_facilatortax_shipping'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['market_facilatortax_shipping'] = isset($payload[0]['market_facilatortax_shipping']) ? $payload[0]['market_facilatortax_shipping'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'MarketplaceFacilitatorTax-Giftwrap')
                  {
                    $payload[0]['market_facilatortax_giftwrap'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['market_facilatortax_giftwrap'] = isset($payload[0]['market_facilatortax_giftwrap']) ? $payload[0]['market_facilatortax_giftwrap'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'MarketplaceFacilitatorTax-Other')
                  {
                    $payload[0]['market_facilatortax_other'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['market_facilatortax_other'] = isset($payload[0]['market_facilatortax_other']) ? $payload[0]['market_facilatortax_other'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'TaxDiscount')
                  {
                    $payload[0]['taxdiscount'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['taxdiscount'] = isset($payload[0]['taxdiscount']) ? $payload[0]['taxdiscount'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'CODItemCharge')
                  {
                    $payload[0]['cod_item_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['cod_item_charge'] = isset($payload[0]['cod_item_charge']) ? $payload[0]['cod_item_charge'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'CODItemTaxCharge')
                  {
                    $payload[0]['cod_item_tax_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['cod_item_tax_charge'] = isset($payload[0]['cod_item_tax_charge']) ? $payload[0]['cod_item_tax_charge'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'CODOrderCharge')
                  {
                    $payload[0]['cod_order_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['cod_order_charge'] = isset($payload[0]['cod_order_charge']) ? $payload[0]['cod_order_charge'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'CODOrderTaxCharge')
                  {
                    $payload[0]['cod_order_tax_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['cod_order_tax_charge'] = isset($payload[0]['cod_order_tax_charge']) ? $payload[0]['cod_order_tax_charge'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'CODShippingCharge')
                  {
                    $payload[0]['cod_shipping_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['cod_shipping_charge'] = isset($payload[0]['cod_shipping_charge']) ? $payload[0]['cod_shipping_charge'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'CODShippingTaxCharge')
                  {
                    $payload[0]['cod_shipping_tax_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['cod_shipping_tax_charge'] = isset($payload[0]['cod_shipping_tax_charge']) ? $payload[0]['cod_shipping_tax_charge'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'Goodwill')
                  {
                    $payload[0]['good_will'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['good_will'] = isset($payload[0]['good_will']) ? $payload[0]['good_will'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'RestockingFee')
                  {
                    $payload[0]['restocking_fee'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['restocking_fee'] = isset($payload[0]['restocking_fee']) ? $payload[0]['restocking_fee'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'ReturnShipping')
                  {
                    $payload[0]['return_shipping'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['return_shipping'] = isset($payload[0]['return_shipping']) ? $payload[0]['return_shipping'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'PointsFee')
                  {
                    $payload[0]['points_fee'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['points_fee'] = isset($payload[0]['points_fee']) ? $payload[0]['points_fee'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'GenericDeduction')
                  {
                    $payload[0]['generic_deduction'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['generic_deduction'] = isset($payload[0]['generic_deduction']) ? $payload[0]['generic_deduction'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'FreeReplacementReturnShipping')
                  {
                    $payload[0]['free_replace_ret_shipping'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['free_replace_ret_shipping'] = isset($payload[0]['free_replace_ret_shipping']) ? $payload[0]['free_replace_ret_shipping'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'PaymentMethodFee')
                  {
                    $payload[0]['payment_method_fee'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['payment_method_fee'] = isset($payload[0]['payment_method_fee']) ? $payload[0]['payment_method_fee'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'ExportCharge')
                  {
                    $payload[0]['export_charge'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['export_charge'] = isset($payload[0]['export_charge']) ? $payload[0]['export_charge'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'SAFE-TReimbursement')
                  {
                    $payload[0]['safe_t_claim'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['safe_t_claim'] = isset($payload[0]['safe_t_claim']) ? $payload[0]['safe_t_claim'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'TCS-CGST')
                  {
                    $payload[0]['tcs_cgst'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['tcs_cgst'] = isset($payload[0]['tcs_cgst']) ? $payload[0]['tcs_cgst'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'TCS-SGST')
                  {
                    $payload[0]['tcs_sgst'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['tcs_sgst'] = isset($payload[0]['tcs_sgst']) ? $payload[0]['tcs_sgst'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'TCS-IGST')
                  {
                    $payload[0]['tcs_igst'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['tcs_igst'] = isset($payload[0]['tcs_igst']) ? $payload[0]['tcs_igst'] : '0.00';
                  }
                  if ( (string) $ItemChargeList->ChargeType == 'TCS-UTGST')
                  {
                    $payload[0]['tcs_utgst'] 										=  (string) $ItemChargeList->ChargeAmount->CurrencyAmount;
                  }else{
                    $payload[0]['tcs_utgst'] = isset($payload[0]['tcs_utgst']) ? $payload[0]['tcs_utgst'] : '0.00';
                  }
                }
            }
            else
            {
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'Principal')
              {
                $payload[0]['principal'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['principal'] = isset($payload[0]['principal']) ? $payload[0]['principal'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'Tax')
              {
                $payload[0]['tax'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['tax'] = isset($payload[0]['tax']) ? $payload[0]['tax'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'GiftWrap')
              {
                $payload[0]['giftwrap'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['giftwrap'] = isset($payload[0]['giftwrap']) ? $payload[0]['giftwrap'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'GiftWrapTax')
              {
                $payload[0]['giftwraptax'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['giftwraptax'] = isset($payload[0]['giftwraptax']) ? $payload[0]['giftwraptax'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'ShippingCharge')
              {
                $payload[0]['shippingcharge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['shippingcharge'] = isset($payload[0]['shippingcharge']) ? $payload[0]['shippingcharge'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'ShippingTax')
              {
                $payload[0]['shippingtax'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['shippingtax'] = isset($payload[0]['shippingtax']) ? $payload[0]['shippingtax'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'MarketplaceFacilitatorTax-Principal')
              {
                $payload[0]['market_facilatortax_principal'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['market_facilatortax_principal'] = isset($payload[0]['market_facilatortax_principal']) ? $payload[0]['market_facilatortax_principal'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'MarketplaceFacilitatorTax-Shipping')
              {
                $payload[0]['market_facilatortax_shipping'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['market_facilatortax_shipping'] = isset($payload[0]['market_facilatortax_shipping']) ? $payload[0]['market_facilatortax_shipping'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'MarketplaceFacilitatorTax-Giftwrap')
              {
                $payload[0]['market_facilatortax_giftwrap'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['market_facilatortax_giftwrap'] = isset($payload[0]['market_facilatortax_giftwrap']) ? $payload[0]['market_facilatortax_giftwrap'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'MarketplaceFacilitatorTax-Other')
              {
                $payload[0]['market_facilatortax_other'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['market_facilatortax_other'] = isset($payload[0]['market_facilatortax_other']) ? $payload[0]['market_facilatortax_other'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TaxDiscount')
              {
                $payload[0]['taxdiscount'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['taxdiscount'] = isset($payload[0]['taxdiscount']) ? $payload[0]['taxdiscount'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODItemCharge')
              {
                $payload[0]['cod_item_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['cod_item_charge'] = isset($payload[0]['cod_item_charge']) ? $payload[0]['cod_item_charge'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODItemTaxCharge')
              {
                $payload[0]['cod_item_tax_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['cod_item_tax_charge'] = isset($payload[0]['cod_item_tax_charge']) ? $payload[0]['cod_item_tax_charge'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODOrderCharge')
              {
                $payload[0]['cod_order_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['cod_order_charge'] = isset($payload[0]['cod_order_charge']) ? $payload[0]['cod_order_charge'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODOrderTaxCharge')
              {
                $payload[0]['cod_order_tax_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['cod_order_tax_charge'] = isset($payload[0]['cod_order_tax_charge']) ? $payload[0]['cod_order_tax_charge'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODShippingCharge')
              {
                $payload[0]['cod_shipping_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['cod_shipping_charge'] = isset($payload[0]['cod_shipping_charge']) ? $payload[0]['cod_shipping_charge'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'CODShippingTaxCharge')
              {
                $payload[0]['cod_shipping_tax_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['cod_shipping_tax_charge'] = isset($payload[0]['cod_shipping_tax_charge']) ? $payload[0]['cod_shipping_tax_charge'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'Goodwill')
              {
                $payload[0]['good_will'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['good_will'] = isset($payload[0]['good_will']) ? $payload[0]['good_will'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'RestockingFee')
              {
                $payload[0]['restocking_fee'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['restocking_fee'] = isset($payload[0]['restocking_fee']) ? $payload[0]['restocking_fee'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'ReturnShipping')
              {
                $payload[0]['return_shipping'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['return_shipping'] = isset($payload[0]['return_shipping']) ? $payload[0]['return_shipping'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'PointsFee')
              {
                $payload[0]['points_fee'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['points_fee'] = isset($payload[0]['points_fee']) ? $payload[0]['points_fee'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'GenericDeduction')
              {
                $payload[0]['generic_deduction'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['generic_deduction'] = isset($payload[0]['generic_deduction']) ? $payload[0]['generic_deduction'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'FreeReplacementReturnShipping')
              {
                $payload[0]['free_replace_ret_shipping'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['free_replace_ret_shipping'] = isset($payload[0]['free_replace_ret_shipping']) ? $payload[0]['free_replace_ret_shipping'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'PaymentMethodFee')
              {
                $payload[0]['payment_method_fee'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['payment_method_fee'] = isset($payload[0]['payment_method_fee']) ? $payload[0]['payment_method_fee'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'ExportCharge')
              {
                $payload[0]['export_charge'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['export_charge'] = isset($payload[0]['export_charge']) ? $payload[0]['export_charge'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'SAFE-TReimbursement')
              {
                $payload[0]['safe_t_claim'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['safe_t_claim'] = isset($payload[0]['safe_t_claim']) ? $payload[0]['safe_t_claim'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TCS-CGST')
              {
                $payload[0]['tcs_cgst'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['tcs_cgst'] = isset($payload[0]['tcs_cgst']) ? $payload[0]['tcs_cgst'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TCS-SGST')
              {
                $payload[0]['tcs_sgst'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['tcs_sgst'] = isset($payload[0]['tcs_sgst']) ? $payload[0]['tcs_sgst'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TCS-IGST')
              {
                $payload[0]['tcs_igst'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['tcs_igst'] = isset($payload[0]['tcs_igst']) ? $payload[0]['tcs_igst'] : '0.00';
              }
              if ( (string) $shipmentItems->ChargeComponent->ChargeType == 'TCS-UTGST')
              {
                $payload[0]['tcs_utgst'] 										=  (string) $shipmentItems->ChargeComponent->ChargeAmount->CurrencyAmount;
              }else{
                $payload[0]['tcs_utgst'] = isset($payload[0]['tcs_utgst']) ? $payload[0]['tcs_utgst'] : '0.00';
              }
            }

            $feeComp = $res->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList;

            if (isset($feeComp->FeeComponent[0]) )
            {
              foreach ($feeComp->FeeComponent as $ItemFeeList)
              {
                    if ( (string) $ItemFeeList->FeeType == 'FBAPerUnitFulfillmentFee')
                    {
                      $payload[0]['fbafee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fbafee'] = isset($payload[0]['fbafee']) ? $payload[0]['fbafee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'Commission')
                    {
                      $payload[0]['commission'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['commission'] = isset($payload[0]['commission']) ? $payload[0]['commission'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FixedClosingFee')
                    {
                      $payload[0]['fixedclosingfee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fixedclosingfee'] = isset($payload[0]['fixedclosingfee']) ? $payload[0]['fixedclosingfee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'GiftwrapChargeback')
                    {
                      $payload[0]['giftwrapchargeback'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['giftwrapchargeback'] = isset($payload[0]['giftwrapchargeback']) ? $payload[0]['giftwrapchargeback'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'ShippingChargeback')
                    {
                      $payload[0]['shippingchargeback'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['shippingchargeback'] = isset($payload[0]['shippingchargeback']) ? $payload[0]['shippingchargeback'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'VariableClosingFee')
                    {
                      $payload[0]['variableclosingfee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['variableclosingfee'] = isset($payload[0]['variableclosingfee']) ? $payload[0]['variableclosingfee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'BubblewrapFee')
                    {
                      $payload[0]['bubble_wrap_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['bubble_wrap_fee'] = isset($payload[0]['bubble_wrap_fee']) ? $payload[0]['bubble_wrap_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBACustomerReturnPerOrderFee')
                    {
                      $payload[0]['fba_cus_ret_per_order_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_cus_ret_per_order_fee'] = isset($payload[0]['fba_cus_ret_per_order_fee']) ? $payload[0]['fba_cus_ret_per_order_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBACustomerReturnPerUnitFee')
                    {
                      $payload[0]['fba_cus_ret_per_unit_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_cus_ret_per_unit_fee'] = isset($payload[0]['fba_cus_ret_per_unit_fee']) ? $payload[0]['fba_cus_ret_per_unit_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBACustomerReturnWeightBasedFee')
                    {
                      $payload[0]['fba_cus_ret_weightbased_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_cus_ret_weightbased_fee'] = isset($payload[0]['fba_cus_ret_weightbased_fee']) ? $payload[0]['fba_cus_ret_weightbased_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBADisposalFee')
                    {
                      $payload[0]['fba_disposal_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_disposal_fee'] = isset($payload[0]['fba_disposal_fee']) ? $payload[0]['fba_disposal_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAFulfillmentCODFee')
                    {
                      $payload[0]['fba_fulfil_cod_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_fulfil_cod_fee'] = isset($payload[0]['fba_fulfil_cod_fee']) ? $payload[0]['fba_fulfil_cod_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAInboundConvenienceFee')
                    {
                      $payload[0]['fba_inb_con_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_inb_con_fee'] = isset($payload[0]['fba_inb_con_fee']) ? $payload[0]['fba_inb_con_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAInboundDefectFee')
                    {
                      $payload[0]['fba_inb_def_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_inb_def_fee'] = isset($payload[0]['fba_inb_def_fee']) ? $payload[0]['fba_inb_def_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAInboundTransportationFee')
                    {
                      $payload[0]['fba_inb_transport_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_inb_transport_fee'] = isset($payload[0]['fba_inb_transport_fee']) ? $payload[0]['fba_inb_transport_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAInboundTransportationProgramFee')
                    {
                      $payload[0]['fba_inb_transport_program_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_inb_transport_program_fee'] = isset($payload[0]['fba_inb_transport_program_fee']) ? $payload[0]['fba_inb_transport_program_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBALongTermStorageFee')
                    {
                      $payload[0]['fba_longterm_storage_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_longterm_storage_fee'] = isset($payload[0]['fba_longterm_storage_fee']) ? $payload[0]['fba_longterm_storage_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAOverageFee')
                    {
                      $payload[0]['fba_overage_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_overage_fee'] = isset($payload[0]['fba_overage_fee']) ? $payload[0]['fba_overage_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAPerOrderFulfillmentFee')
                    {
                      $payload[0]['fba_perorder_fulfill_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_perorder_fulfill_fee'] = isset($payload[0]['fba_perorder_fulfill_fee']) ? $payload[0]['fba_perorder_fulfill_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBARemovalFee')
                    {
                      $payload[0]['fba_removal_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_removal_fee'] = isset($payload[0]['fba_removal_fee']) ? $payload[0]['fba_removal_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAStorageFee')
                    {
                      $payload[0]['fba_storage_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_storage_fee'] = isset($payload[0]['fba_storage_fee']) ? $payload[0]['fba_storage_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBATransportationFee')
                    {
                      $payload[0]['fba_transport_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_transport_fee'] = isset($payload[0]['fba_transport_fee']) ? $payload[0]['fba_transport_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FBAWeightBasedFee')
                    {
                      $payload[0]['fba_weightbased_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fba_weightbased_fee'] = isset($payload[0]['fba_weightbased_fee']) ? $payload[0]['fba_weightbased_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FulfillmentFee')
                    {
                      $payload[0]['fullfill_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fullfill_fee'] = isset($payload[0]['fullfill_fee']) ? $payload[0]['fullfill_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'FulfillmentNetworkFee')
                    {
                      $payload[0]['fullfill_network_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['fullfill_network_fee'] = isset($payload[0]['fullfill_network_fee']) ? $payload[0]['fullfill_network_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'LabelingFee')
                    {
                      $payload[0]['lable_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['lable_fee'] = isset($payload[0]['lable_fee']) ? $payload[0]['lable_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'OpaqueBaggingFee')
                    {
                      $payload[0]['opa_bagging_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['opa_bagging_fee'] = isset($payload[0]['opa_bagging_fee']) ? $payload[0]['opa_bagging_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'PolybaggingFee')
                    {
                      $payload[0]['poly_bagging_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['poly_bagging_fee'] = isset($payload[0]['poly_bagging_fee']) ? $payload[0]['poly_bagging_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'SSOFFulfillmentFee')
                    {
                      $payload[0]['ssof_fullfill_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['ssof_fullfill_fee'] = isset($payload[0]['ssof_fullfill_fee']) ? $payload[0]['ssof_fullfill_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'TapingFee')
                    {
                      $payload[0]['taping_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['taping_fee'] = isset($payload[0]['taping_fee']) ? $payload[0]['taping_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'TransportationFee')
                    {
                      $payload[0]['transport_fee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['transport_fee'] = isset($payload[0]['transport_fee']) ? $payload[0]['transport_fee'] : '0.00';
                    }
                    if ( (string) $ItemFeeList->FeeType == 'UnitFulfillmentFee')
                    {
                      $payload[0]['UnitFulfillmentFee'] 									= (string) $ItemFeeList->FeeAmount->CurrencyAmount;
                    }else{
                      $payload[0]['UnitFulfillmentFee'] = isset($payload[0]['UnitFulfillmentFee']) ? $payload[0]['UnitFulfillmentFee'] : '0.00';
                    }

              }
            }
            else
            {
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAPerUnitFulfillmentFee')
                {
                  $payload[0]['fbafee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fbafee'] = isset($payload[0]['fbafee']) ? $payload[0]['fbafee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'Commission')
                {
                  $payload[0]['commission'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['commission'] = isset($payload[0]['commission']) ? $payload[0]['commission'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FixedClosingFee')
                {
                  $payload[0]['fixedclosingfee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fixedclosingfee'] = isset($payload[0]['fixedclosingfee']) ? $payload[0]['fixedclosingfee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'GiftwrapChargeback')
                {
                  $payload[0]['giftwrapchargeback'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['giftwrapchargeback'] = isset($payload[0]['giftwrapchargeback']) ? $payload[0]['giftwrapchargeback'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'ShippingChargeback')
                {
                  $payload[0]['shippingchargeback'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['shippingchargeback'] = isset($payload[0]['shippingchargeback']) ? $payload[0]['shippingchargeback'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'VariableClosingFee')
                {
                  $payload[0]['variableclosingfee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['variableclosingfee'] = isset($payload[0]['variableclosingfee']) ? $payload[0]['variableclosingfee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'BubblewrapFee')
                {
                  $payload[0]['bubble_wrap_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['bubble_wrap_fee'] = isset($payload[0]['bubble_wrap_fee']) ? $payload[0]['bubble_wrap_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBACustomerReturnPerOrderFee')
                {
                  $payload[0]['fba_cus_ret_per_order_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_cus_ret_per_order_fee'] = isset($payload[0]['fba_cus_ret_per_order_fee']) ? $payload[0]['fba_cus_ret_per_order_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBACustomerReturnPerUnitFee')
                {
                  $payload[0]['fba_cus_ret_per_unit_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_cus_ret_per_unit_fee'] = isset($payload[0]['fba_cus_ret_per_unit_fee']) ? $payload[0]['fba_cus_ret_per_unit_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBACustomerReturnWeightBasedFee')
                {
                  $payload[0]['fba_cus_ret_weightbased_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_cus_ret_weightbased_fee'] = isset($payload[0]['fba_cus_ret_weightbased_fee']) ? $payload[0]['fba_cus_ret_weightbased_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBADisposalFee')
                {
                  $payload[0]['fba_disposal_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_disposal_fee'] = isset($payload[0]['fba_disposal_fee']) ? $payload[0]['fba_disposal_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAFulfillmentCODFee')
                {
                  $payload[0]['fba_fulfil_cod_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_fulfil_cod_fee'] = isset($payload[0]['fba_fulfil_cod_fee']) ? $payload[0]['fba_fulfil_cod_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAInboundConvenienceFee')
                {
                  $payload[0]['fba_inb_con_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_inb_con_fee'] = isset($payload[0]['fba_inb_con_fee']) ? $payload[0]['fba_inb_con_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAInboundDefectFee')
                {
                  $payload[0]['fba_inb_def_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_inb_def_fee'] = isset($payload[0]['fba_inb_def_fee']) ? $payload[0]['fba_inb_def_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAInboundTransportationFee')
                {
                  $payload[0]['fba_inb_transport_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_inb_transport_fee'] = isset($payload[0]['fba_inb_transport_fee']) ? $payload[0]['fba_inb_transport_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAInboundTransportationProgramFee')
                {
                  $payload[0]['fba_inb_transport_program_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_inb_transport_program_fee'] = isset($payload[0]['fba_inb_transport_program_fee']) ? $payload[0]['fba_inb_transport_program_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBALongTermStorageFee')
                {
                  $payload[0]['fba_longterm_storage_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_longterm_storage_fee'] = isset($payload[0]['fba_longterm_storage_fee']) ? $payload[0]['fba_longterm_storage_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAOverageFee')
                {
                  $payload[0]['fba_overage_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_overage_fee'] = isset($payload[0]['fba_overage_fee']) ? $payload[0]['fba_overage_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAPerOrderFulfillmentFee')
                {
                  $payload[0]['fba_perorder_fulfill_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_perorder_fulfill_fee'] = isset($payload[0]['fba_perorder_fulfill_fee']) ? $payload[0]['fba_perorder_fulfill_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBARemovalFee')
                {
                  $payload[0]['fba_removal_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_removal_fee'] = isset($payload[0]['fba_removal_fee']) ? $payload[0]['fba_removal_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAStorageFee')
                {
                  $payload[0]['fba_storage_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_storage_fee'] = isset($payload[0]['fba_storage_fee']) ? $payload[0]['fba_storage_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBATransportationFee')
                {
                  $payload[0]['fba_transport_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_transport_fee'] = isset($payload[0]['fba_transport_fee']) ? $payload[0]['fba_transport_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FBAWeightBasedFee')
                {
                  $payload[0]['fba_weightbased_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fba_weightbased_fee'] = isset($payload[0]['fba_weightbased_fee']) ? $payload[0]['fba_weightbased_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FulfillmentFee')
                {
                  $payload[0]['fullfill_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fullfill_fee'] = isset($payload[0]['fullfill_fee']) ? $payload[0]['fullfill_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'FulfillmentNetworkFee')
                {
                  $payload[0]['fullfill_network_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['fullfill_network_fee'] = isset($payload[0]['fullfill_network_fee']) ? $payload[0]['fullfill_network_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'LabelingFee')
                {
                  $payload[0]['lable_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['lable_fee'] = isset($payload[0]['lable_fee']) ? $payload[0]['lable_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'OpaqueBaggingFee')
                {
                  $payload[0]['opa_bagging_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['opa_bagging_fee'] = isset($payload[0]['opa_bagging_fee']) ? $payload[0]['opa_bagging_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'PolybaggingFee')
                {
                  $payload[0]['poly_bagging_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['poly_bagging_fee'] = isset($payload[0]['poly_bagging_fee']) ? $payload[0]['poly_bagging_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'SSOFFulfillmentFee')
                {
                  $payload[0]['ssof_fullfill_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['ssof_fullfill_fee'] = isset($payload[0]['ssof_fullfill_fee']) ? $payload[0]['ssof_fullfill_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'TapingFee')
                {
                  $payload[0]['taping_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['taping_fee'] = isset($payload[0]['taping_fee']) ? $payload[0]['taping_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'TransportationFee')
                {
                  $payload[0]['transport_fee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['transport_fee'] = isset($payload[0]['transport_fee']) ? $payload[0]['transport_fee'] : '0.00';
                }
                if ( (string) $feeComp->FeeComponent->FeeType == 'UnitFulfillmentFee')
                {
                  $payload[0]['UnitFulfillmentFee'] 									= (string) $feeComp->FeeComponent->FeeAmount->CurrencyAmount;
                }else{
                  $payload[0]['UnitFulfillmentFee'] = isset($payload[0]['UnitFulfillmentFee']) ? $payload[0]['UnitFulfillmentFee'] : '0.00';
                }
            }
        }

      }

      print_r($payload);die;


      $namespaces = $res->getNamespaces(true);
      $httpcode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
      if($httpcode != 200)
      {
        if(preg_match('/throttled/',(string)$res->Error->Message))
        {
          sleep(10);
          echo "throttling occured;\n";
          $this->fetch_product_details($user_id,$order_id,$amz_country_code,$country_code);
        }
      }
      if(preg_match('/Invalid/',(string)$res->Error->Message))
      {
          echo "ERROR ".(string)$res->ListFinancialEventsResult->Error->Message;
          $data['status_code']=3;
          $data['status_text']="No Data";
          $payload['lm_ean']=$order_id;
          $payload['asin_counts']=-3;
          $payload['lm_asin']='';
          $data['payload']=$payload;
          return $data;
        //throw new Exception($res->GetMatchingProductForIdResult->Error->Message);
      }

      /**************
	   if(preg_match_all('/<ChargeComponent>\s*<ChargeType>Principal<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['principal']=$res[0];
		}
		else
		{
		$payload['principal']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>Tax<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['tax']=$res[0];
		}
		else
		{
		$payload['tax']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>GiftWrap<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['giftwrap']=$res[0];
		}
		else
		{
		$payload['giftwrap']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>GiftWrapTax<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['giftwraptax']=$res[0];
		}
		else
		{
		$payload['giftwraptax']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>ShippingCharge<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['shippingcharge']=$res[0];
		}
		else
		{
		$payload['shippingcharge']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>ShippingTax<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['shippingtax']=$res[0];
		}
		else
		{
		$payload['shippingtax']='0.00';
		}

		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>MarketplaceFacilitatorTax-Principal<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['market_facilatortax_principal']=$res[0];
		}
		else
		{
		$payload['market_facilatortax_principal']='0.00';
		}

		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>MarketplaceFacilitatorTax-Shipping<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['market_facilatortax_shipping']=$res[0];
		}
		else
		{
		$payload['market_facilatortax_shipping']='0.00';
		}

		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>MarketplaceFacilitatorTax-Giftwrap<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['market_facilatortax_giftwrap']=$res[0];
		}
		else
		{
		$payload['market_facilatortax_giftwrap']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>MarketplaceFacilitatorTax-Other<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['market_facilatortax_other']=$res[0];
		}
		else
		{
		$payload['market_facilatortax_other']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>TaxDiscount<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['taxdiscount']=$res[0];
		}
		else
		{
		$payload['taxdiscount']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>CODItemCharge<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['cod_item_charge']=$res[0];
		}
		else
		{
		$payload['cod_item_charge']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>CODItemTaxCharge<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['cod_item_tax_charge']=$res[0];
		}
		else
		{
		$payload['cod_item_tax_charge']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>CODOrderCharge<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['cod_order_charge']=$res[0];
		}
		else
		{
		$payload['cod_order_charge']='0.00';
		}

		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>CODOrderTaxCharge<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['cod_order_tax_charge']=$res[0];
		}
		else
		{
		$payload['cod_order_tax_charge']='0.00';
		}

	  if(preg_match_all('/<ChargeComponent>\s*<ChargeType>CODShippingCharge<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['cod_shipping_charge']=$res[0];
		}
		else
		{
		$payload['cod_shipping_charge']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>CODShippingTaxCharge<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['cod_shipping_tax_charge']=$res[0];
		}
		else
		{
		$payload['cod_shipping_tax_charge']='0.00';
		}

		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>Goodwill<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['good_will']=$res[0];
		}
		else
		{
		$payload['good_will']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>RestockingFee<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['restocking_fee']=$res[0];
		}
		else
		{
		$payload['restocking_fee']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>ReturnShipping<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['return_shipping']=$res[0];
		}
		else
		{
		$payload['return_shipping']='0.00';
		}

		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>PointsFee<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['points_fee']=$res[0];
		}
		else
		{
		$payload['points_fee']='0.00';
		}

		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>GenericDeduction<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['generic_deduction']=$res[0];
		}
		else
		{
		$payload['generic_deduction']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>FreeReplacementReturnShipping<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['free_replace_ret_shipping']=$res[0];
		}
		else
		{
		$payload['free_replace_ret_shipping']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>PaymentMethodFee<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['payment_method_fee']=$res[0];
		}
		else
		{
		$payload['payment_method_fee']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>ExportCharge<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['export_charge']=$res[0];
		}
		else
		{
		$payload['export_charge']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>SAFE-TReimbursement<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['safe_t_claim']=$res[0];
		}
		else
		{
		$payload['safe_t_claim']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>TCS-CGST<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['tcs_cgst']=$res[0];
		}
		else
		{
		$payload['tcs_cgst']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>TCS-SGST<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['tcs_sgst']=$res[0];
		}
		else
		{
		$payload['tcs_sgst']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>TCS-IGST<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['tcs_igst']=$res[0];
		}
		else
		{
		$payload['tcs_igst']='0.00';
		}
		if(preg_match_all('/<ChargeComponent>\s*<ChargeType>TCS-UTGST<\/ChargeType>\s*<ChargeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/ChargeAmount>\s*<\/ChargeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['tcs_utgst']=$res[0];
		}
		else
		{
		$payload['tcs_utgst']='0.00';
		}


        if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAPerUnitFulfillmentFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['fbafee']=$res[0];

		}
		else
		{
		$payload['fbafee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>Commission<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['commission']=$res[0];

		}
		else
		{
		$payload['commission']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FixedClosingFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['fixedclosingfee']=$res[0];

		}
		else
		{
		$payload['fixedclosingfee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>GiftwrapChargeback<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
		//print_r($res);
	    $payload['giftwrapchargeback']=$res[0];

		}
		else
		{
		$payload['giftwrapchargeback']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>ShippingChargeback<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['shippingchargeback']=$res[0];
		}
		else
		{
		$payload['shippingchargeback']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>VariableClosingFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['variableclosingfee']=$res[0];
		}
		else
		{
		$payload['variableclosingfee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>BubblewrapFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['bubble_wrap_fee']=$res[0];
	    }
		else
		{
		$payload['bubble_wrap_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBACustomerReturnPerOrderFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_cus_ret_per_order_fee']=$res[0];
	    }
		else
		{
		$payload['fba_cus_ret_per_order_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBACustomerReturnPerUnitFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_cus_ret_per_unit_fee']=$res[0];
	    }
		else
		{
		$payload['fba_cus_ret_per_unit_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBACustomerReturnWeightBasedFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_cus_ret_weightbased_fee']=$res[0];
	    }
		else
		{
		$payload['fba_cus_ret_weightbased_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBADisposalFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_disposal_fee']=$res[0];
	    }
		else
		{
		$payload['fba_disposal_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAFulfillmentCODFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_fulfil_cod_fee']=$res[0];
	    }
		else
		{
		$payload['fba_fulfil_cod_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAInboundConvenienceFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_inb_con_fee']=$res[0];
	    }
		else
		{
		$payload['fba_inb_con_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAInboundDefectFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_inb_def_fee']=$res[0];
	    }
		else
		{
		$payload['fba_inb_def_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAInboundTransportationFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_inb_transport_fee']=$res[0];
	    }
		else
		{
		$payload['fba_inb_transport_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAInboundTransportationProgramFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_inb_transport_program_fee']=$res[0];
	    }
		else
		{
		$payload['fba_inb_transport_program_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBALongTermStorageFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_longterm_storage_fee']=$res[0];
	    }
		else
		{
		$payload['fba_longterm_storage_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAOverageFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_overage_fee']=$res[0];
	    }
		else
		{
		$payload['fba_overage_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAPerOrderFulfillmentFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_perorder_fulfill_fee']=$res[0];
	    }
		else
		{
		$payload['fba_perorder_fulfill_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBARemovalFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_removal_fee']=$res[0];
	    }
		else
		{
		$payload['fba_removal_fee']='0.00';
		}

		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAStorageFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_storage_fee']=$res[0];
	    }
		else
		{
		$payload['fba_storage_fee']='0.00';
		}

		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBATransportationFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_transport_fee']=$res[0];
	    }
		else
		{
		$payload['fba_transport_fee']='0.00';
		}

		if(preg_match_all('/<FeeComponent>\s*<FeeType>FBAWeightBasedFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fba_weightbased_fee']=$res[0];
	    }
		else
		{
		$payload['fba_weightbased_fee']='0.00';
		}

		if(preg_match_all('/<FeeComponent>\s*<FeeType>FulfillmentFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fullfill_fee']=$res[0];
	    }
		else
		{
		$payload['fullfill_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>FulfillmentNetworkFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['fullfill_network_fee']=$res[0];
	    }
		else
		{
		$payload['fullfill_network_fee']='0.00';
		}

		if(preg_match_all('/<FeeComponent>\s*<FeeType>LabelingFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['lable_fee']=$res[0];
	    }
		else
		{
		$payload['lable_fee']='0.00';
		}

		if(preg_match_all('/<FeeComponent>\s*<FeeType>OpaqueBaggingFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['opa_bagging_fee']=$res[0];
	    }
		else
		{
		$payload['opa_bagging_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>PolybaggingFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['poly_bagging_fee']=$res[0];
	    }
		else
		{
		$payload['poly_bagging_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>SSOFFulfillmentFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['ssof_fullfill_fee']=$res[0];
	    }
		else
		{
		$payload['ssof_fullfill_fee']='0.00';
		}
		if(preg_match_all('/<FeeComponent>\s*<FeeType>TapingFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['taping_fee']=$res[0];
	    }
		else
		{
		$payload['taping_fee']='0.00';
		}

		if(preg_match_all('/<FeeComponent>\s*<FeeType>TransportationFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['transport_fee']=$res[0];
	    }
		else
		{
		$payload['transport_fee']='0.00';
		}

		if(preg_match_all('/<FeeComponent>\s*<FeeType>UnitFulfillmentFee<\/FeeType>\s*<FeeAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/FeeAmount>\s*<\/FeeComponent>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['UnitFulfillmentFee']=$res[0];
	    }
		else
		{
		$payload['UnitFulfillmentFee']='0.00';
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
		if(preg_match_all('/<PostedDate>([^>]*?)<\/PostedDate>/i',$response,$matches))
		{
	    $res=$matches[1];
	    $payload['posted_date']=$res[0];
		}
		if(preg_match_all('/<Promotion>\s*<PromotionType>[^>]*?<\/PromotionType>\s*<PromotionAmount>\s*<CurrencyAmount>([^>]*?)<\/CurrencyAmount>\s*<CurrencyCode>[^>]*?<\/CurrencyCode>\s*<\/PromotionAmount>\s*<PromotionId>[^>]*?<\/PromotionId>\s*<\/Promotion>/i',$response,$matches))
		{
	    $res=$matches[1];
		 //print_r($res);
	    $payload['promo_price1']=$res[0];
		$payload['promo_price2']=$res[1];
		$payload['promo_price3']=$res[2];
		$payload['promo_price4']=$res[3];
		$payload['promo_price5']=$res[4];
		$payload['promo_price6']=$res[5];
		}
		else
		{
		$payload['promo_price1']='0.0';
		$payload['promo_price2']='0.0';
		$payload['promo_price3']='0.0';
		$payload['promo_price4']='0.0';
		$payload['promo_price5']='0.0';
		$payload['promo_price6']='0.0';
		}

    ********/
    // echo "<prE>";print_r($payload);die;
     //die();

      if(count($payload) > 0 && !empty($payload['order_id']))
      {
        $data['status_code']  =   1;
        $data['status_text']  =   "Success";
        $data['payload']      =   $payload;
      }
      else
      {
          $data['status_code']    = 3;
          $data['status_text']    = "No Data";
          $payload['lm_ean']      = $order_id;
          $payload['asin_counts'] = -3;
          $payload['lm_asin']     = '';
          $data['payload']        = $payload;
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
                  'Version' => urlencode("2015-05-01"),


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
            $string_to_sign = "POST\n".$this->mws_site."\n/Finances/2015-05-01\n" . $url_string;

            $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
            $signature = urlencode(base64_encode($signature));
            $url = "https://".$this->mws_site."/Finances/2015-05-01?". $url_string . "&Signature=" . $signature;
            return $url;
 }

}
?>
