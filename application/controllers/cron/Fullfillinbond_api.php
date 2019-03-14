<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Queries
 * 1. We want to know some shipments has many trasport charges e.g differt carriers and different price how we ditermine which charge we want to insert , or you want that we need to create a new table to store all the charges for shipments
 * 2. Please send of status list of shipments you want to see data accoriding following Status  e.g SHIPPED ,WORKING,IN_TRANSIT,DELIVERED,CHECKED_IN,RECEIVING,CLOSED,CANCELLED
 */
class Fullfillinbond_api extends CI_Controller
{
  private $seller_id    =  '';
  private $auth_token   =  '';
  private $access_key   =  '';
  private $secret_key   =  '';
  private $market_id    =  '';
  //private $service_url  = 'mws.amazonservices.com';
  private $service_url  = '';
  public function  __construct()
	{
	     parent::__construct();
       $this->load->model('new_cron/Fullfillinbond_api_model','fullfill');
  }
  public function index()
  {

    $query=$this->db->query("SELECT * FROM amazon_profile");
    $users=$query->result_array();
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {
        $data = $this->fetch_product_details($usr);
      }

    }
  }

  public function getLastUpdatedDate($user_id)
  {
    $query=$this->db->query("SELECT update_at as date FROM fba_shipment_details WHERE user_id = $user_id order by  date DESC LIMIT 1");
    $last_date=$query->row();
    if (isset($last_date->date)) {
      $last_date = $last_date->date;
      // $startDate                            = date("c", time());
      $startDate                            = date("c", strtotime("-1 day", strtotime($last_date)));
      // $startDate                            = date("c", strtotime('2018-01-01 00:00:00'));
      $last_date =$startDate;
    }else {
      $startDate                            = date("c", time());
      $startDate                            = date("c", strtotime("-1 day", strtotime($startDate)));
      // $startDate                            = date("c", strtotime('2018-01-01 00:00:00'));
      $last_date =$startDate;
    }

    return $last_date;
  }
  public function set_credentials($usr)
  {
        $this->seller_id  = $usr['seller_id'];
        $this->auth_token = $usr['auth_token'];
        $this->access_key = $usr['access_key'];
        $this->secret_key = $usr['secret_key'];
        $this->market_id  = $usr['market_placeID'];
        $this->service_url  = $usr['mws_endpoint'];
        // $this->mws_site   = $usr['mws_url'];
        return $usr;
  }

  public function fetch_product_details($usr)
  {
    try
    {
      $user_id                              = $usr['profile_id'];
      $last_date                            = $this->getLastUpdatedDate($user_id);
      $startDate                            = date("c", strtotime($last_date));
      $endDate                              = date("c", time());
      $endDate                              = date("c", strtotime('-5 minutes'));

      // Setting up credentials
      $this->seller_id                      = $usr['seller_id'];
      $this->auth_token                     = $usr['auth_token'];
      $this->access_key                     = $usr['access_key'];
      $this->secret_key                     = $usr['secret_key'];
      $this->market_id                      = $usr['market_placeID'];
      $this->service_url                    = $usr['mws_endpoint'];


      // Api URL
      $param['Action']                      = urlencode("ListInboundShipments");
      // Api Required Params
      $param['ShipmentStatusList.member.1'] = 'SHIPPED';
      $param['ShipmentStatusList.member.2'] = 'WORKING';
      $param['ShipmentStatusList.member.3'] = 'IN_TRANSIT';
      $param['ShipmentStatusList.member.4'] = 'DELIVERED';
      $param['ShipmentStatusList.member.5'] = 'ERROR';
      // $param['ShipmentStatusList.member.5'] = 'CHECKED_IN';
      $param['ShipmentStatusList.member.6'] = 'RECEIVING';
      $param['ShipmentStatusList.member.7'] = 'CLOSED';
      $param['ShipmentStatusList.member.8'] = 'CANCELLED';
      $param['ShipmentStatusList.member.9'] = 'DELETED';
      // $param['ShipmentStatusList.member.10'] = 'ERROR';

      // echo "<pre>";      print_r($param);      die;
      // $startDate                            = date("c", time());

      // Get records within specific times
      $param['LastUpdatedAfter']            = $startDate;
      $param['LastUpdatedBefore']           = $endDate;

      //echo "<prE>"; print_r($usr); die;

      $response                             = $this->curl_request($param);

      // create object array from xml response using simplexml_load_string function
  	  $data                                 = simplexml_load_string($response);
      // echo "string";


      // declare global function
      $shipmentst_array                         = array();
      $update_shipmentst_array                  = array();
      $transport_content_array                  = array();
      $transport_content_update_array           = array();
      $List_inbound_shipment_items_array        = array();
      $List_inbound_shipment_items_update_array = array();
      if (isset($data->ListInboundShipmentsResult->ShipmentData->member) )
      {
        $insert = 0;
        $update = 0;
        foreach ($data->ListInboundShipmentsResult->ShipmentData->member as $key => $value)
        {

          $transport_content = $this->GetTransportContent((string) $value->ShipmentId);
          $List_inbound_shipment_items = $this->ListInboundShipmentItems((string) $value->ShipmentId);
          $is_exits = $this->fullfill->checkexist('fba_shipment_details',array('shipment_id'=>(string) $value->ShipmentId));


          if (isset($value->ShipmentStatus) && $value->ShipmentStatus == 'CLOSED' ) {
            $text = $value->ShipmentName;
            preg_match("/(([^)]*))/", $text, $match);
            if ($user_id == 1)
            {
              $date_      =  $match[1];
              $date       = explode(' (',$match[1]);
              $date       = $date[1];
              $date       = explode(' ',$date);
              $dateTime   = ltrim($date[1],'(');
              $dateTime   = rtrim($date[1],')');
              if (strpos($date_, '.')) {
                $date = explode('.', $date[0]);
              }else {
                $date     = explode('/', $date[0]);
              }
              $date       = $date[2].'-'.$date[1].'-'.$date[0].' '.$dateTime;
              $created_at       =  date('Y-m-d H:i:s',strtotime($date));
              $updated_at       =  date('Y-m-d H:i:s',strtotime($date));
            }else {
              $date_      =  $match[1];
              $date       = explode(' (',$match[1]);
              $date       = $date[1];
              $date       = explode(' ',$date);
              $dateTime   = ltrim($date[1],'(');
              $dateTime   = rtrim($date[1],')');
              if (strpos($date_, '.')) {
                $date = explode('.', $date[0]);
              }else {
                $date     = explode('/', $date[0]);
              }
              $date       = $date[2].'-'.$date[1].'-'.$date[0].' '.$dateTime;
              $created_at       =  date('Y-m-d H:i:s',strtotime($date));
              $updated_at       =  date('Y-m-d H:i:s',strtotime($date));
            }
          }else{
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');
          }

          if ($is_exits)
          {
            $update_shipmentst_array[$update] = array(
                                'destination_fulfillment_center_id' => (string) (isset($value->DestinationFulfillmentCenterId)) ? (string) $value->DestinationFulfillmentCenterId : '' ,
                                'label_prep_type'                   => (string) (isset($value->LabelPrepType)) ? (string) $value->LabelPrepType : '' ,
                                'city'                              => (string) (isset($value->ShipFromAddress->City)) ? (string) $value->ShipFromAddress->City : '' ,
                                'country_code'                      => (string) (isset($value->ShipFromAddress->CountryCode)) ? (string) $value->ShipFromAddress->CountryCode : '' ,
                                'postal_code'                       => (string) (isset($value->ShipFromAddress->PostalCode)) ? (string) $value->ShipFromAddress->PostalCode : '' ,
                                'from_name'                         => (string) (isset($value->ShipFromAddress->Name)) ? (string) $value->ShipFromAddress->Name : '' ,
                                'addressLine1'                      => (string) (isset($value->ShipFromAddress->AddressLine1)) ? (string) $value->ShipFromAddress->AddressLine1 : '' ,
                                'state_or_province_code'            => (string) (isset($value->ShipFromAddress->StateOrProvinceCode)) ? (string) $value->ShipFromAddress->StateOrProvinceCode : '' ,
                                'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                'are_cases_required'                => (string) (isset($value->AreCasesRequired)) ? (string) $value->AreCasesRequired : '' ,
                                'shipment_name'                     => (string) (isset($value->ShipmentName)) ? (string) $value->ShipmentName : '' ,
                                'box_contents_source'               => (string) (isset($value->BoxContentsSource)) ? (string) $value->BoxContentsSource : '' ,
                                'shipment_status'                   => (string) (isset($value->ShipmentStatus)) ? (string) $value->ShipmentStatus : '' ,
                                'update_at'                         => $updated_at,
                                'user_id'                           => $user_id,
                                );
                                #  Transport charges
                                if (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member))
                                {
                                  foreach ($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member as $key => $transport) {

                                    $is_exits = $this->fullfill->checkexist('fba_shipment_products',array('shipment_id'=>(string) $value->ShipmentId));
                                    if ($is_exits) {
                                      $transport_content_update_array[] = array
                                      (
                                        'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                        'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                        'updated_at'                        => $updated_at,
                                        'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                        'weight'                            => (string) (isset($transport->Weight->Value)) ?  (string) $transport->Weight->Value : '' ,
                                        'unit'                              => (string) (isset($transport->Weight->Unit))  ?  (string) $transport->Weight->Unit  : '' ,
                                        'carrier_name'                      => (string) (isset($transport->CarrierName))   ?  (string) $transport->CarrierName   : '' ,
                                        'package_status'                    => (string) (isset($transport->PackageStatus)) ?  (string) $transport->PackageStatus : '' ,
                                        'tracking_number'                   => (string) (isset($transport->TrackingId))    ?  (string) $transport->TrackingId    : '' ,
                                        'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                        'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                        'user_id'                           => $user_id,
                                      );
                                    } else {
                                      $transport_content_array[] = array
                                      (
                                        'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                        'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                        'created_at'                        => $created_at,
                                        'updated_at'                        => $updated_at,
                                        'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                        'weight'                            => (string) (isset($transport->Weight->Value)) ?  (string) $transport->Weight->Value : '' ,
                                        'unit'                              => (string) (isset($transport->Weight->Unit))  ?  (string) $transport->Weight->Unit  : '' ,
                                        'carrier_name'                      => (string) (isset($transport->CarrierName))   ?  (string) $transport->CarrierName   : '' ,
                                        'package_status'                    => (string) (isset($transport->PackageStatus)) ?  (string) $transport->PackageStatus : '' ,
                                        'tracking_number'                   => (string) (isset($transport->TrackingId))    ?  (string) $transport->TrackingId    : '' ,
                                        'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                        'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                        'user_id'                           => $user_id,
                                      );
                                    }


                                  }
                                }

                                #Shipment items
                                if (isset($List_inbound_shipment_items->ListInboundShipmentItemsResult->ItemData->member))
                                {
                                  foreach ($List_inbound_shipment_items->ListInboundShipmentItemsResult->ItemData->member as $key => $shipmentItems) {

                                    $is_exits = $this->fullfill->checkexist('fba_shipment_Items',array('shipment_id'=>(string) $value->ShipmentId , 'seller_sku' => (string) $shipmentItems->SellerSKU));
                                    if ($is_exits) {
                                      $List_inbound_shipment_items_update_array[] = array
                                      (
                                        'updated_at'                        => $updated_at,
                                        'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                        'seller_sku'                        => (string) (isset($shipmentItems->SellerSKU)) ? (string) $shipmentItems->SellerSKU : '' ,
                                        'ffn_sku'                           => (string) (isset($shipmentItems->FulfillmentNetworkSKU)) ? (string) $shipmentItems->FulfillmentNetworkSKU : '' ,
                                        'quantity_shipped'                  => (string) (isset($shipmentItems->QuantityShipped)) ? (string) $shipmentItems->QuantityShipped : '' ,
                                        'quantity_received'                 => (string) (isset($shipmentItems->QuantityReceived)) ? (string) $shipmentItems->QuantityReceived : '' ,
                                        'quantity_in_case'                  => (string) (isset($shipmentItems->QuantityInCase)) ? (string) $shipmentItems->QuantityInCase : '' ,
                                        'user_id'                           => $user_id,

                                      );
                                    } else {
                                      $List_inbound_shipment_items_array[] = array
                                      (
                                        'updated_at'                        => $updated_at,
                                        'created_at'                        => $created_at,
                                        'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                        'seller_sku'                       => (string) (isset($shipmentItems->SellerSKU)) ? (string) $shipmentItems->SellerSKU : '' ,
                                        'ffn_sku'                           => (string) (isset($shipmentItems->FulfillmentNetworkSKU)) ? (string) $shipmentItems->FulfillmentNetworkSKU : '' ,
                                        'quantity_shipped'                  => (string) (isset($shipmentItems->QuantityShipped)) ? (string) $shipmentItems->QuantityShipped : '' ,
                                        'quantity_received'                 => (string) (isset($shipmentItems->QuantityReceived)) ? (string) $shipmentItems->QuantityReceived : '' ,
                                        'quantity_in_case'                  => (string) (isset($shipmentItems->QuantityInCase)) ? (string) $shipmentItems->QuantityInCase : '' ,
                                        'user_id'                           => $user_id,

                                      );
                                    }
                                  }

                                  if (isset($List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken) && !empty($List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken))
                                  {
                                      $token = (string) $List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken;
                                      $result = $this->amazonShipmentsItems($token,$user_id,$updated_at,$created_at);
                                  }
                                }

                $update++;
          }
          else
          {
            $shipmentst_array[$insert] = array(
                                  'destination_fulfillment_center_id' => (string) (isset($value->DestinationFulfillmentCenterId)) ? (string) $value->DestinationFulfillmentCenterId : '' ,
                                  'label_prep_type'                   => (string) (isset($value->LabelPrepType)) ? (string) $value->LabelPrepType : '' ,
                                  'city'                              => (string) (isset($value->ShipFromAddress->City)) ? (string) $value->ShipFromAddress->City : '' ,
                                  'country_code'                      => (string) (isset($value->ShipFromAddress->CountryCode)) ? (string) $value->ShipFromAddress->CountryCode : '' ,
                                  'postal_code'                       => (string) (isset($value->ShipFromAddress->PostalCode)) ? (string) $value->ShipFromAddress->PostalCode : '' ,
                                  'from_name'                         => (string) (isset($value->ShipFromAddress->Name)) ? (string) $value->ShipFromAddress->Name : '' ,
                                  'addressLine1'                      => (string) (isset($value->ShipFromAddress->AddressLine1)) ? (string) $value->ShipFromAddress->AddressLine1 : '' ,
                                  'state_or_province_code'            => (string) (isset($value->ShipFromAddress->StateOrProvinceCode)) ? (string) $value->ShipFromAddress->StateOrProvinceCode : '' ,
                                  'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                  'are_cases_required'                => (string) (isset($value->AreCasesRequired)) ? (string) $value->AreCasesRequired : '' ,
                                  'shipment_name'                     => (string) (isset($value->ShipmentName)) ? (string) $value->ShipmentName : '' ,
                                  'box_contents_source'               => (string) (isset($value->BoxContentsSource)) ? (string) $value->BoxContentsSource : '' ,
                                  'shipment_status'                   => (string) (isset($value->ShipmentStatus)) ? (string) $value->ShipmentStatus : '' ,
                                  'date'                              => $created_at,
                                  'update_at'                         => $updated_at,
                                  'user_id'                           => $user_id,
                                );

              if (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member))
              {
                foreach ($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member as $key => $transport)
                {
                    $transport_content_array[] = array
                    (
                      'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                      'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                      'created_at'                        => date('Y-m-d H:i:s'),
                      'updated_at'                        => date('Y-m-d H:i:s'),
                      'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                      'weight'                            => (string) (isset($transport->Weight->Value)) ?  (string) $transport->Weight->Value : '' ,
                      'unit'                              => (string) (isset($transport->Weight->Unit))  ?  (string) $transport->Weight->Unit  : '' ,
                      'carrier_name'                      => (string) (isset($transport->CarrierName))   ?  (string) $transport->CarrierName   : '' ,
                      'package_status'                    => (string) (isset($transport->PackageStatus)) ?  (string) $transport->PackageStatus : '' ,
                      'tracking_number'                   => (string) (isset($transport->TrackingId))    ?  (string) $transport->TrackingId    : '' ,
                      'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                      'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? (string) $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                      'user_id'                           => $user_id,
                    );
                }
              }

              #Shipment items
              if (isset($List_inbound_shipment_items->ListInboundShipmentItemsResult->ItemData->member))
              {
                foreach ($List_inbound_shipment_items->ListInboundShipmentItemsResult->ItemData->member as $key => $shipmentItems) {
                    $List_inbound_shipment_items_array[] = array
                    (
                      'created_at'                        => date('Y-m-d H:i:s'),
                      'updated_at'                        => date('Y-m-d H:i:s'),
                      'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                      'seller_sku'                        =>  (string) (isset($shipmentItems->SellerSKU)) ? (string) $shipmentItems->SellerSKU : '' ,
                      'ffn_sku'                           => (string) (isset($shipmentItems->FulfillmentNetworkSKU)) ? (string) $shipmentItems->FulfillmentNetworkSKU : '' ,
                      'quantity_shipped'                  => (string) (isset($shipmentItems->QuantityShipped)) ? (string) $shipmentItems->QuantityShipped : '' ,
                      'quantity_received'                 => (string) (isset($shipmentItems->QuantityReceived)) ? (string) $shipmentItems->QuantityReceived : '' ,
                      'quantity_in_case'                  => (string) (isset($shipmentItems->QuantityInCase)) ? (string) $shipmentItems->QuantityInCase : '' ,
                      'user_id'                           => $user_id,

                    );
                }
                if (isset($List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken) && !empty($List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken))
                {
                    $token = (string) $List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken;
                    $result = $this->amazonShipmentsItems($token,$user_id,$updated_at,$created_at);
                }
              }

              $insert++;
          }

        }

          if (count($shipmentst_array) > 0) { $__insert_shipemts                =   $this->__insert_shipemts($shipmentst_array); }
          if (count($update_shipmentst_array) > 0) { $__insert_shipemts         =   $this->__update_shipemts($update_shipmentst_array); }

          if (count($transport_content_array) > 0) { $__transport_content_array =   $this->__insert_shipemts_products($transport_content_array);}
          if (count($transport_content_update_array) > 0) { $__transport_content_update_array =   $this->__update_shipemts_products($transport_content_update_array, array('tracking_number','shipment_id') ); }

          #Shipment items insert into databse
          if (count($List_inbound_shipment_items_array) > 0) { $__transport_content_array     =   $this->__insert_shipemts_items($List_inbound_shipment_items_array); }
          if (count($List_inbound_shipment_items_update_array) > 0) { $__transport_content_update_array =   $this->__update_shipemts_items($List_inbound_shipment_items_update_array, array('seller_sku','shipment_id')); }

          if (isset($data->ListInboundShipmentsResult->NextToken))
          {
                $token = (string) $data->ListInboundShipmentsResult->NextToken;
                $result = $this->amazonShipments($token,$user_id);
          }
        } else {
          echo json_encode(array('success' => true,'success_message' => 'Data updated succesfully...','user_id' => $user_id )).'<br>';
        }



    }
    catch(Exception $e)
    {
      $data['status_code']=0;
      $data['status_text']=$e->getMessage();
      return $data;
    }
  }
  // function to insert records
  public function __insert_shipemts($parms)
  {
    $this->db->insert_batch('fba_shipment_details', $parms);
  }
  // function to update records
  public function __update_shipemts($parms)
  {
    $this->db->update_batch('fba_shipment_details', $parms,'shipment_id');
  }

  // function to insert records
  public function __insert_shipemts_products($parms)
  {
    $parms = array_values($parms);
    $this->db->insert_batch('fba_shipment_products', $parms);
    return true;
  }
  // function to update records
  public function __update_shipemts_products($parms,$where )
  {
    // echo "<pre>";
    // print_r($parms[0]['tracking_number']);
    // print_r($where);
    // die;
    $parms = array_values($parms);
    foreach ($parms as $key => $value) {
      foreach ($where as $keywal) {
        $this->db->where($keywal , $value[$keywal]);
      }
      $this->db->update('fba_shipment_products',$value);
    }
    return true;
  }

  // function to insert Shipment items
  public function __insert_shipemts_items($parms)
  {
    $parms = array_values($parms);
    $this->db->insert_batch('fba_shipment_Items', $parms);
  }
  // function to update Shipment items
  public function __update_shipemts_items($parms,$where)
  {

    $parms = array_values($parms);
    foreach ($parms as $key => $value) {
      foreach ($where as $keywal) {
        $this->db->where($keywal , $value[$keywal]);
      }
      $this->db->update('fba_shipment_Items',$value);
    }
    return true;

  }

  /**
   * Recursive funtion for api response
   */
  public function amazonShipments($token,$user_id) {
        $param['Action']                          =   urlencode("ListInboundShipmentsByNextToken");
        $param['NextToken']                       =   $token;
        $response                                 =   $this->curl_request($param);
        $data                                     =   simplexml_load_string($response);
        // echo "Recursive";
        // echo "<pre>";
        // print_r($data);
        $List_inbound_shipment_items_array        = array();
        $List_inbound_shipment_items_update_array = array();
        $transport_content_array                  = array();
        $transport_content_update_array           = array();
        $shipmentst_array                         = array();
        $update_shipmentst_array                  = array();

        if (isset($data->ListInboundShipmentsByNextTokenResult->ShipmentData->member) )
        {

          $insert = 0;
          $update = 0;
          foreach ($data->ListInboundShipmentsByNextTokenResult->ShipmentData->member as $key => $value)
          {


            if (isset($value->ShipmentStatus) && $value->ShipmentStatus == 'CLOSED' ) {
              $text = $value->shipment_name;
              preg_match('#\((.*?)\)#', $text, $match);
              if ($user_id == 1)
              {
                $date_      =  $match[1];
                $date       = explode(' ',$match[1]);
                $dateTime   = (string) $date[1];
                if (strpos($date_, '.')) {
                  $date = explode('.', $date[0]);
                }else {
                  $date     = explode('/', $date[0]);
                }
                $date       = $date[2].'-'.$date[1].'-'.$date[0].' '.$dateTime;
                $date       =  date('Y-m-d H:i:s',strtotime($date));
              }else {
                $date = $match[1];
                $created_at =  date('Y-m-d H:i:s',strtotime($date));
                $updated_at =  date('Y-m-d H:i:s',strtotime($date));
              }
            }else{
              $created_at = date('Y-m-d H:i:s');
              $updated_at = date('Y-m-d H:i:s');
            }

            $transport_content           = $this->GetTransportContent((string) $value->ShipmentId);
            $List_inbound_shipment_items = $this->ListInboundShipmentItems((string) $value->ShipmentId);
            $is_exits                    = $this->fullfill->checkexist('fba_shipment_details',array('shipment_id'=>(string) $value->ShipmentId));

            if ($is_exits)
            {
              $update_shipmentst_array[$update] = array(
                                  'destination_fulfillment_center_id' => (string) (isset($value->DestinationFulfillmentCenterId)) ? (string) $value->DestinationFulfillmentCenterId : '' ,
                                  'label_prep_type'                   => (string) (isset($value->LabelPrepType)) ? (string) $value->LabelPrepType : '' ,
                                  'city'                              => (string) (isset($value->ShipFromAddress->City)) ? (string) $value->ShipFromAddress->City : '' ,
                                  'country_code'                      => (string) (isset($value->ShipFromAddress->CountryCode)) ? (string) $value->ShipFromAddress->CountryCode : '' ,
                                  'postal_code'                       => (string) (isset($value->ShipFromAddress->PostalCode)) ? (string) $value->ShipFromAddress->PostalCode : '' ,
                                  'from_name'                         => (string) (isset($value->ShipFromAddress->Name)) ? (string) $value->ShipFromAddress->Name : '' ,
                                  'addressLine1'                      => (string) (isset($value->ShipFromAddress->AddressLine1)) ? (string) $value->ShipFromAddress->AddressLine1 : '' ,
                                  'state_or_province_code'            => (string) (isset($value->ShipFromAddress->StateOrProvinceCode)) ? (string) $value->ShipFromAddress->StateOrProvinceCode : '' ,
                                  'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                  'are_cases_required'                => (string) (isset($value->AreCasesRequired)) ? (string) $value->AreCasesRequired : '' ,
                                  'shipment_name'                     => (string) (isset($value->ShipmentName)) ? (string) $value->ShipmentName : '' ,
                                  'box_contents_source'               => (string) (isset($value->BoxContentsSource)) ? (string) $value->BoxContentsSource : '' ,
                                  'shipment_status'                   => (string) (isset($value->ShipmentStatus)) ? (string) $value->ShipmentStatus : '' ,
                                  'update_at'                         => $updated_at,
                                  'user_id'                           => $user_id,
                                  );

                                  if (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member))
                                  {
                                    foreach ($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member as $key => $transport) {

                                      $is_exits = $this->fullfill->checkexist('fba_shipment_products',array('shipment_id'=>(string) $value->ShipmentId));
                                      if ($is_exits) {
                                        $transport_content_update_array[] = array
                                        (
                                          'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                          'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                          'updated_at'                        => $updated_at,
                                          'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                          'weight'                            => (string) (isset($transport->Weight->Value)) ?  (string) $transport->Weight->Value : '' ,
                                          'unit'                              => (string) (isset($transport->Weight->Unit))  ?  (string) $transport->Weight->Unit  : '' ,
                                          'carrier_name'                      => (string) (isset($transport->CarrierName))   ?  (string) $transport->CarrierName   : '' ,
                                          'package_status'                    => (string) (isset($transport->PackageStatus)) ?  (string) $transport->PackageStatus : '' ,
                                          'tracking_number'                   => (string) (isset($transport->TrackingId))    ?  (string) $transport->TrackingId    : '' ,
                                          'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                          'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                          'user_id'                           => $user_id,

                                        );
                                      } else {
                                        $transport_content_array[] = array
                                        (
                                          'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                          'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                          'created_at'                        => $created_at,
                                          'updated_at'                        => $updated_at,
                                          'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                          'weight'                            => (string) (isset($transport->Weight->Value)) ?  (string) $transport->Weight->Value : '' ,
                                          'unit'                              => (string) (isset($transport->Weight->Unit))  ?  (string) $transport->Weight->Unit  : '' ,
                                          'carrier_name'                      => (string) (isset($transport->CarrierName))   ?  (string) $transport->CarrierName   : '' ,
                                          'package_status'                    => (string) (isset($transport->PackageStatus)) ?  (string) $transport->PackageStatus : '' ,
                                          'tracking_number'                   => (string) (isset($transport->TrackingId))    ?  (string) $transport->TrackingId    : '' ,
                                          'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                          'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                          'user_id'                           => $user_id,

                                        );
                                      }


                                    }
                                  }

                                  #Shipment items
                                  if (isset($List_inbound_shipment_items->ListInboundShipmentItemsResult->ItemData->member))
                                  {
                                    foreach ($List_inbound_shipment_items->ListInboundShipmentItemsResult->ItemData->member as $key => $shipmentItems) {

                                      $is_exits = $this->fullfill->checkexist('fba_shipment_Items',array('shipment_id'=>(string) $value->ShipmentId , 'seller_sku' => (string) $shipmentItems->SellerSKU));
                                      if ($is_exits) {
                                        $List_inbound_shipment_items_update_array[] = array
                                        (
                                          'created_at'                        => $created_at,
                                          'updated_at'                        => $updated_at,
                                          'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                          'seller_sku'                        => (string) (isset($shipmentItems->SellerSKU)) ? (string) $shipmentItems->SellerSKU : '' ,
                                          'ffn_sku'                           => (string) (isset($shipmentItems->FulfillmentNetworkSKU)) ? (string) $shipmentItems->FulfillmentNetworkSKU : '' ,
                                          'quantity_shipped'                  => (string) (isset($shipmentItems->QuantityShipped)) ? (string) $shipmentItems->QuantityShipped : '' ,
                                          'quantity_received'                 => (string) (isset($shipmentItems->QuantityReceived)) ? (string) $shipmentItems->QuantityReceived : '' ,
                                          'quantity_in_case'                  => (string) (isset($shipmentItems->QuantityInCase)) ? (string) $shipmentItems->QuantityInCase : '' ,
                                          'user_id'                           => $user_id,

                                        );
                                      }
                                      else
                                      {
                                        $List_inbound_shipment_items_array[] = array
                                        (
                                          'updated_at'                        => $updated_at,
                                          'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                          'seller_sku'                       => (string) (isset($shipmentItems->SellerSKU)) ? (string) $shipmentItems->SellerSKU : '' ,
                                          'ffn_sku'                           => (string) (isset($shipmentItems->FulfillmentNetworkSKU)) ? (string) $shipmentItems->FulfillmentNetworkSKU : '' ,
                                          'quantity_shipped'                  => (string) (isset($shipmentItems->QuantityShipped)) ? (string) $shipmentItems->QuantityShipped : '' ,
                                          'quantity_received'                 => (string) (isset($shipmentItems->QuantityReceived)) ? (string) $shipmentItems->QuantityReceived : '' ,
                                          'quantity_in_case'                  => (string) (isset($shipmentItems->QuantityInCase)) ? (string) $shipmentItems->QuantityInCase : '' ,
                                          'user_id'                           => $user_id,

                                        );
                                      }
                                    }

                                    if (isset($List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken) && !empty($List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken))
                                    {
                                        // Recursive function to get all shipment items
                                        $token = (string) $List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken;
                                        $result = $this->amazonShipmentsItems($token,$user_id,$updated_at,$created_at);
                                    }
                                  }

              $update++;
            }
            else
            {
              $shipmentst_array[$insert] = array(
                                    'destination_fulfillment_center_id' => (string) (isset($value->DestinationFulfillmentCenterId)) ? (string) $value->DestinationFulfillmentCenterId : '' ,
                                    'label_prep_type'                   => (string) (isset($value->LabelPrepType)) ? (string) $value->LabelPrepType : '' ,
                                    'city'                              => (string) (isset($value->ShipFromAddress->City)) ? (string) $value->ShipFromAddress->City : '' ,
                                    'country_code'                      => (string) (isset($value->ShipFromAddress->CountryCode)) ? (string) $value->ShipFromAddress->CountryCode : '' ,
                                    'postal_code'                       => (string) (isset($value->ShipFromAddress->PostalCode)) ? (string) $value->ShipFromAddress->PostalCode : '' ,
                                    'from_name'                         => (string) (isset($value->ShipFromAddress->Name)) ? (string) $value->ShipFromAddress->Name : '' ,
                                    'addressLine1'                      => (string) (isset($value->ShipFromAddress->AddressLine1)) ? (string) $value->ShipFromAddress->AddressLine1 : '' ,
                                    'state_or_province_code'            => (string) (isset($value->ShipFromAddress->StateOrProvinceCode)) ? (string) $value->ShipFromAddress->StateOrProvinceCode : '' ,
                                    'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                    'are_cases_required'                => (string) (isset($value->AreCasesRequired)) ? (string) $value->AreCasesRequired : '' ,
                                    'shipment_name'                     => (string) (isset($value->ShipmentName)) ? (string) $value->ShipmentName : '' ,
                                    'box_contents_source'               => (string) (isset($value->BoxContentsSource)) ? (string) $value->BoxContentsSource : '' ,
                                    'shipment_status'                   => (string) (isset($value->ShipmentStatus)) ? (string) $value->ShipmentStatus : '' ,
                                    'date'                              => $created_at,
                                    'update_at'                         => $updated_at,
                                    'user_id'                           => $user_id,
                                  );

                                  if (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member))
                                  {
                                    foreach ($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member as $key => $transport)
                                    {
                                        $transport_content_array[] = array
                                        (
                                          'shipment_id'                 => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                          'shipment_type'               => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                          'created_at'                  => $created_at,
                                          'updated_at'                  => $updated_at,
                                          'transport_status'            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                          'weight'                      => (string) (isset($transport->Weight->Value)) ?  (string) $value->Weight->Value : '' ,
                                          'unit'                        => (string) (isset($transport->Weight->Unit))  ?  (string) $value->Weight->Unit  : '' ,
                                          'carrier_name'                => (string) (isset($transport->CarrierName))   ?  (string) $transport->CarrierName   : '' ,
                                          'package_status'              => (string) (isset($transport->PackageStatus)) ?  (string) $transport->PackageStatus : '' ,
                                          'tracking_number'             => (string) (isset($transport->TrackingId))    ?  (string) $transport->TrackingId    : '' ,
                                          'currency_code'               => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                          'amount'                      => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                          'user_id'                     => $user_id,
                                        );
                                    }
                                  }

                                  #Shipment items
                                  if (isset($List_inbound_shipment_items->ListInboundShipmentItemsResult->ItemData->member))
                                  {
                                    foreach ($List_inbound_shipment_items->ListInboundShipmentItemsResult->ItemData->member as $key => $shipmentItems) {
                                      $List_inbound_shipment_items_array[] = array
                                        (
                                          'created_at'                        => $created_at,
                                          'updated_at'                        => $updated_at,
                                          'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                                          'seller_sku'                       =>  (string) (isset($shipmentItems->SellerSKU)) ? (string) $shipmentItems->SellerSKU : '' ,
                                          'ffn_sku'                           => (string) (isset($shipmentItems->FulfillmentNetworkSKU)) ? (string) $shipmentItems->FulfillmentNetworkSKU : '' ,
                                          'quantity_shipped'                  => (string) (isset($shipmentItems->QuantityShipped)) ? (string) $shipmentItems->QuantityShipped : '' ,
                                          'quantity_received'                 => (string) (isset($shipmentItems->QuantityReceived)) ? (string) $shipmentItems->QuantityReceived : '' ,
                                          'quantity_in_case'                  => (string) (isset($shipmentItems->QuantityInCase)) ? (string) $shipmentItems->QuantityInCase : '' ,
                                          'user_id'                           => $user_id,

                                        );
                                    }
                                    if (isset($List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken) && !empty($List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken))
                                    {
                                        // Recursive function to get all shipment items
                                        $token = (string) $List_inbound_shipment_items->ListInboundShipmentItemsResult->NextToken;
                                        $result = $this->amazonShipmentsItems($token,$user_id,$updated_at,$created_at);
                                    }
                                  }
              $insert++;
            }

          }

          /** * Inserting and updating records * **/

          #Shipment items insert into databse
          if (count($List_inbound_shipment_items_array) > 0) { $__transport_content_array                =   $this->__insert_shipemts_items($List_inbound_shipment_items_array);  }
          if (count($List_inbound_shipment_items_update_array) > 0) {  $__transport_content_update_array =   $this->__update_shipemts_items($List_inbound_shipment_items_update_array, array('seller_sku','shipment_id')); }

          if (count($transport_content_array) > 0) { $__transport_content_array                          =   $this->__insert_shipemts_products($transport_content_array); }
          if (count($transport_content_update_array) > 0) { $__transport_content_update_array            =   $this->__update_shipemts_products($transport_content_update_array, array('tracking_number','shipment_id') ); }

          if (count($shipmentst_array) > 0) { $__insert_shipemts                                         =   $this->__insert_shipemts($shipmentst_array); }
          if (count($update_shipmentst_array) > 0) {  $__insert_shipemts                                 =   $this->__update_shipemts($update_shipmentst_array); }


          if (isset($data->ListInboundShipmentsByNextTokenResult->NextToken))
          {
                $token = (string) $data->ListInboundShipmentsByNextTokenResult->NextToken;
                $result = $this->amazonShipments($token,$user_id);
          }

        }else {
          echo json_encode(array('success' => true,'success_message' => 'Data updated succesfully...','user_id'=> $user_id,)).'<br>';
        }


    }

  /**
   * Recursive funtion for api response
   */
  public function amazonShipmentsItems($token,$user_id,$updated_at,$created_at)
  {
        $param['Action']             = urlencode("ListInboundShipmentItemsByNextToken");
        $param['NextToken']          = $token;
        $response                    = $this->curl_request($param);
        $List_inbound_shipment_items = simplexml_load_string($response);
        $shipmentst_array            = array();
        #Shipment items
        if (isset($List_inbound_shipment_items->ListInboundShipmentItemsByNextTokenResult->ItemData->member))
        {
          foreach ($List_inbound_shipment_items->ListInboundShipmentItemsByNextTokenResult->ItemData->member as $key => $shipmentItems) {

            $is_exits = $this->fullfill->checkexist('fba_shipment_Items',array('shipment_id'=> (string) $value->ShipmentId , 'seller_sku' => (string) $shipmentItems->SellerSKU));
            if ($is_exits)
            {
                $List_inbound_shipment_items_update_array[] = array
                (
                  'updated_at'                        => $updated_at,
                  'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                  'seller_sku'                        => (string) (isset($shipmentItems->SellerSKU)) ? (string) $shipmentItems->SellerSKU : '' ,
                  'ffn_sku'                           => (string) (isset($shipmentItems->FulfillmentNetworkSKU)) ? (string) $shipmentItems->FulfillmentNetworkSKU : '' ,
                  'quantity_shipped'                  => (string) (isset($shipmentItems->QuantityShipped)) ? (string) $shipmentItems->QuantityShipped : '' ,
                  'quantity_received'                 => (string) (isset($shipmentItems->QuantityReceived)) ? (string) $shipmentItems->QuantityReceived : '' ,
                  'quantity_in_case'                  => (string) (isset($shipmentItems->QuantityInCase)) ? (string) $shipmentItems->QuantityInCase : '' ,
                  'user_id'                           => $user_id,
                );
            } else {
              $List_inbound_shipment_items_array[] = array
              (
                'updated_at'                        => $updated_at,
                'created_at'                        => $created_at,
                'shipment_id'                       => (string) (isset($value->ShipmentId)) ? (string) $value->ShipmentId : '' ,
                'seller_sku'                        => (string) (isset($shipmentItems->SellerSKU)) ? (string) $shipmentItems->SellerSKU : '' ,
                'ffn_sku'                           => (string) (isset($shipmentItems->FulfillmentNetworkSKU)) ? (string) $shipmentItems->FulfillmentNetworkSKU : '' ,
                'quantity_shipped'                  => (string) (isset($shipmentItems->QuantityShipped)) ? (string) $shipmentItems->QuantityShipped : '' ,
                'quantity_received'                 => (string) (isset($shipmentItems->QuantityReceived)) ? (string) $shipmentItems->QuantityReceived : '' ,
                'quantity_in_case'                  => (string) (isset($shipmentItems->QuantityInCase)) ? (string) $shipmentItems->QuantityInCase : '' ,
                'user_id'                           => $user_id,
              );
            }
          }

          #Shipment items insert into databse
          if (count($List_inbound_shipment_items_array) > 0) {
            $__transport_content_array =   $this->__insert_shipemts_items($List_inbound_shipment_items_array);
          }
          if (count($List_inbound_shipment_items_update_array) > 0) {
            $__transport_content_update_array =   $this->__update_shipemts_items($List_inbound_shipment_items_update_array, array('seller_sku','shipment_id'));
          }

          if (isset($List_inbound_shipment_items->ListInboundShipmentItemsByNextTokenResult->NextToken) && !empty($List_inbound_shipment_items->ListInboundShipmentItemsByNextTokenResult->NextToken))
          {
              $token = (string) $List_inbound_shipment_items->ListInboundShipmentItemsByNextTokenResult->NextToken;
              $result = $this->amazonShipmentsItems($token,$user_id,$updated_at,$created_at);
          }
        }
        // else {}
  }


  private function curl_request($param)
  {
    /**
     * https://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01?
     AWSAccessKeyId=AKIAIP4IUQSSOQXEBSAA
     &Action=ListInboundShipments
     &LastUpdatedAfter=2019-01-15T17%3A11%3A35%2B05%3A30
     &LastUpdatedBefore=2019-02-14T17%3A06%3A35%2B05%3A30
     &MWSAuthToken=amzn.mws.48b3d760-b504-e217-3b5d-82fe23080eb5&MarketplaceId=
     &SellerId=AO1Q865UF7VZH
     &ShipmentStatusList.member.10=ERROR
     &ShipmentStatusList.member.1=Shipped
     &ShipmentStatusList.member.2=WORKING
     &ShipmentStatusList.member.3=IN_TRANSIT
     &ShipmentStatusList.member.4=DELIVERED
     &ShipmentStatusList.member.5=CHECKED_IN
     &ShipmentStatusList.member.6=RECEIVING
     &ShipmentStatusList.member.7=CLOSED
     &ShipmentStatusList.member.8=CANCELLED
     &ShipmentStatusList.member.9=DELETED
     &SignatureMethod=HmacSHA256&SignatureVersion=2
     &Timestamp=2019-02-14T11%3A41%3A35.000Z&Version=2010-10-01
     &Signature=F9OMOHH8K7VAQZPM6iwovc7bAVPCH%2Bc5l7iczwYjzi8%3D
     */

    $httpHeader=array();
    $httpHeader[]='Transfer-Encoding: chunked';
    $httpHeader[]='Content-Type: text/json';
    $httpHeader[]='Expect:';
    $httpHeader[]='Accept:';
    // Init curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->built_query_string($param));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
    curl_setopt($ch, CURLOPT_POST, true);
    $response = curl_exec($ch);
    // return curl request response back to function
    return $response;
  }

  // Api Call For Get Transport Cost
  public function GetTransportContent($shipmentId)
  {
    $param['Action']    = urlencode("GetTransportContent");
    $param['ShipmentId']= $shipmentId;
    $response = $this->curl_request($param);
    $data = simplexml_load_string($response);
    return $data;

  }
  // Api Call For Get ListInbound Shipment Items Cost
  public function ListInboundShipmentItems($shipmentId)
  {
    $param['Action']    = urlencode("ListInboundShipmentItems");
    $param['ShipmentId']= $shipmentId;
    $response = $this->curl_request($param);
    $data = simplexml_load_string($response);
    return $data;

  }

  /**
   * Function to build query string for api
   */
  private function built_query_string($add_param)
  {
         $params = array(
                  'AWSAccessKeyId'  =>  urlencode($this->access_key),
                  'SellerId'        =>  urlencode($this->seller_id),
				          'MWSAuthToken'    =>  urlencode($this->auth_token),
                  'SignatureMethod' =>  urlencode("HmacSHA256"),
                  'SignatureVersion'=>  urlencode("2"),
                  'Timestamp'       =>  gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time()),
                  'Version'         =>  urlencode("2010-10-01"),
                  'MarketplaceId'   =>  $this->market_id
                 );
    				if(!empty($this->auth_token))
            {
              $params['MWSAuthToken']=urlencode($this->auth_token);
            }

            $params           = array_merge($params,$add_param);
            $url_parts        = array();
            foreach(array_keys($params) as $key){
                $url_parts[]  = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
            }
            sort($url_parts);
            $url_string       = implode("&", $url_parts);
            //$string_to_sign = "POST\nmws.amazonaws.com\n/FulfillmentInboundShipment/2010-10-01\n" . $url_string;
            $string_to_sign   = "POST\n".$this->service_url."\n/FulfillmentInboundShipment/2010-10-01\n".$url_string;
            $signature        = hash_hmac("sha256",$string_to_sign,$this->secret_key,TRUE);
            $signature        = urlencode(base64_encode($signature));
            //$url = "https://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01?". $url_string . "&Signature=" . $signature;
            $url              = "https://".$this->service_url."/FulfillmentInboundShipment/2010-10-01?".$url_string."&Signature=".$signature;
            return $url;
  }


   public function xml2array($xmlObject, $out = array ())
   {
      foreach ( (array) $xmlObject as $index => $node )
           $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

       return $out;
    }

    public function updateShipmentsDate()
    {
      $getRecords = $this->fullfill->get_all_records();
      foreach ($getRecords as $key => $value) {
        $text = $value->shipment_name;
        preg_match('#\((.*?)\)#', $text, $match);
        if ($value->user_id == 1) {
          $date_ =  $match[1];
          $date = explode(' ',$match[1]);
          $dateTime = (string) $date[1];
          if (strpos($date_, '.')) {
            $date = explode('.', $date[0]);
          }else {
            $date = explode('/', $date[0]);
          }
          $date  = $date[2].'-'.$date[1].'-'.$date[0].' '.$dateTime;
          $date =  date('Y-m-d H:i:s',strtotime($date));
        }else {
          $date = $match[1];
          $date =  date('Y-m-d H:i:s',strtotime($date));
        }

        $this->db->set('date',$date);
        $this->db->set('update_at',$date);
        $this->db->where('shipment_id',$value->shipment_id);
        $this->db->update('fba_shipment_details');
        echo 'fba_shipment_details=>'. $this->db->affected_rows().'<br>';

        $this->db->set('created_at',$date);
        $this->db->set('updated_at',$date);
        $this->db->where('shipment_id',$value->shipment_id);
        $this->db->update('fba_shipment_Items');
        echo 'fba_shipment_Items=>'. $this->db->affected_rows().'<br>';
        $this->db->set('created_at',$date);
        $this->db->set('updated_at',$date);
        $this->db->where('shipment_id',$value->shipment_id);
        $this->db->update('fba_shipment_products');
        echo 'fba_shipment_products=>'. $this->db->affected_rows().'<br>';

      }
      // echo "<pre>";print_r($getRecords);die;
    }
}
