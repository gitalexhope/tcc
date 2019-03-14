<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Queries
 * 1. We want to know some shipments has many trasport charges e.g differt carriers and different price how we ditermine which charge we want to insert , or you want that we need to create a new table to store all the charges for shipments
 * 2. Please send of status list of shipments you want to see data accoriding following Status  e.g SHIPPED ,WORKING,IN_TRANSIT,DELIVERED,CHECKED_IN,RECEIVING,CLOSED,CANCELLED
 */
class Fullfillinbond_api extends CI_Controller
{
  private $seller_id    = '';
  private $auth_token   = '';
  private $access_key   = '';
  private $secret_key   = '';
  private $market_id    = '';
  private $service_url  = 'mws.amazonservices.com';
  public function  __construct()
	{
	     parent::__construct();
       $this->load->model('new_cron/Fullfillinbond_api_model','fullfill');
  }
  public function index()
  {
    $query=$this->db->query("SELECT * FROM amazon_profile WHERE profile_id != 1  ");
    $users=$query->result_array();
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {
        $data = $this->fetch_product_details($usr);
      }

    }
  }

  public function set_credentials($usr)
  {
        $this->seller_id  = $usr['seller_id'];
        $this->auth_token = $usr['auth_token'];
        $this->access_key = $usr['access_key'];
        $this->secret_key = $usr['secret_key'];
        // $this->market_id  = $usr['amz_code'];
        // $this->mws_site   = $usr['mws_url'];
        return $usr;
  }

  public function fetch_product_details($usr)
  {
    try
    {

      
      // Setting up credentials
      $this->seller_id                      = $usr['seller_id'];
      $this->auth_token                     = $usr['auth_token'];
      $this->access_key                     = $usr['access_key'];
      $this->secret_key                     = $usr['secret_key'];
      $user_id                              = $usr['profile_id'];
      // Api URL
      $param['Action']                      = urlencode("ListInboundShipments");
      // Api Required Params
      $param['ShipmentStatusList.member.1'] = 'Shipped';
      $param['ShipmentStatusList.member.2'] = 'WORKING';
      $param['ShipmentStatusList.member.3'] = 'IN_TRANSIT';
      $param['ShipmentStatusList.member.4'] = 'DELIVERED';
      $param['ShipmentStatusList.member.5'] = 'CHECKED_IN';
      $param['ShipmentStatusList.member.6'] = 'RECEIVING';
      $param['ShipmentStatusList.member.7'] = 'CLOSED';
      $param['ShipmentStatusList.member.8'] = 'CANCELLED';
      // $param['ShipmentStatusList.member.9'] = 'DELETED';
      // $param['ShipmentStatusList.member.10'] = 'ERROR';
      $startDate                            = date("c", time());
      $startDate                            = date("c", strtotime("-20 days", strtotime($startDate)));
      echo "$startDate<br>";
      $endDate                              = date("c", time());
      $endDate                              = date("c", strtotime("-5 minutes", strtotime($endDate)));
      echo "$endDate<br>";

      // Get records within specific times
      $param['LastUpdatedAfter']            = $startDate;
      $param['LastUpdatedBefore']           = $endDate;
      $response                             = $this->curl_request($param);
      // create object array from xml response using simplexml_load_string function
  	  $data                                = simplexml_load_string($response);

      // declare global function
      $shipmentst_array         = array();
      $update_shipmentst_array = array();
      if (isset($data->ListInboundShipmentsResult->ShipmentData->member) )
      {
        $insert = 0;
        $update = 0;
        foreach ($data->ListInboundShipmentsResult->ShipmentData->member as $key => $value)
        {

          $transport_content = $this->GetTransportContent((string) $value->ShipmentId);
          $is_exits = $this->fullfill->checkexist('fba_shipment_details',array('shipment_id'=>(string) $value->ShipmentId));

          if ($is_exits)
          {
            $update_shipmentst_array[$update] = array(
                                'destination_fulfillment_center_id' => (string) (isset($value->DestinationFulfillmentCenterId)) ? $value->DestinationFulfillmentCenterId : '' ,
                                'label_prep_type'                   => (string) (isset($value->LabelPrepType)) ? $value->LabelPrepType : '' ,
                                'city'                              => (string) (isset($value->ShipFromAddress->City)) ? $value->ShipFromAddress->City : '' ,
                                'country_code'                      => (string) (isset($value->ShipFromAddress->CountryCode)) ? $value->ShipFromAddress->CountryCode : '' ,
                                'postal_code'                       => (string) (isset($value->ShipFromAddress->PostalCode)) ? $value->ShipFromAddress->PostalCode : '' ,
                                'from_name'                         => (string) (isset($value->ShipFromAddress->Name)) ? $value->ShipFromAddress->Name : '' ,
                                'addressLine1'                      => (string) (isset($value->ShipFromAddress->AddressLine1)) ? $value->ShipFromAddress->AddressLine1 : '' ,
                                'state_or_province_code'            => (string) (isset($value->ShipFromAddress->StateOrProvinceCode)) ? $value->ShipFromAddress->StateOrProvinceCode : '' ,
                                'shipment_id'                       => (string) (isset($value->ShipmentId)) ? $value->ShipmentId : '' ,
                                'are_cases_required'                => (string) (isset($value->AreCasesRequired)) ? $value->AreCasesRequired : '' ,
                                'shipment_name'                     => (string) (isset($value->ShipmentName)) ? $value->ShipmentName : '' ,
                                'box_contents_source'               => (string) (isset($value->BoxContentsSource)) ? $value->BoxContentsSource : '' ,
                                'shipment_status'                   => (string) (isset($value->ShipmentStatus)) ? $value->ShipmentStatus : '' ,
                                'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                'weight'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Value : '' ,
                                'unit'                              => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Unit)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Unit : '' ,
                                'carrier_name'                      => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->CarrierName)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->CarrierName : '' ,
                                'package_status'                    => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->PackageStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->PackageStatus : '' ,
                                'tracking_number'                   => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->TrackingId)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->TrackingId : '' ,
                                'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                'update_at'                         => date('Y-m-d H:i:s'),
                                'user_id'                           => $user_id,
                                );
                $update++;
          }
          else
          {
            $shipmentst_array[$insert] = array(
                                  'destination_fulfillment_center_id' => (string) (isset($value->DestinationFulfillmentCenterId)) ? $value->DestinationFulfillmentCenterId : '' ,
                                  'label_prep_type'                   => (string) (isset($value->LabelPrepType)) ? $value->LabelPrepType : '' ,
                                  'city'                              => (string) (isset($value->ShipFromAddress->City)) ? $value->ShipFromAddress->City : '' ,
                                  'country_code'                      => (string) (isset($value->ShipFromAddress->CountryCode)) ? $value->ShipFromAddress->CountryCode : '' ,
                                  'postal_code'                       => (string) (isset($value->ShipFromAddress->PostalCode)) ? $value->ShipFromAddress->PostalCode : '' ,
                                  'from_name'                         => (string) (isset($value->ShipFromAddress->Name)) ? $value->ShipFromAddress->Name : '' ,
                                  'addressLine1'                      => (string) (isset($value->ShipFromAddress->AddressLine1)) ? $value->ShipFromAddress->AddressLine1 : '' ,
                                  'state_or_province_code'            => (string) (isset($value->ShipFromAddress->StateOrProvinceCode)) ? $value->ShipFromAddress->StateOrProvinceCode : '' ,
                                  'shipment_id'                       => (string) (isset($value->ShipmentId)) ? $value->ShipmentId : '' ,
                                  'are_cases_required'                => (string) (isset($value->AreCasesRequired)) ? $value->AreCasesRequired : '' ,
                                  'shipment_name'                     => (string) (isset($value->ShipmentName)) ? $value->ShipmentName : '' ,
                                  'box_contents_source'               => (string) (isset($value->BoxContentsSource)) ? $value->BoxContentsSource : '' ,
                                  'shipment_status'                   => (string) (isset($value->ShipmentStatus)) ? $value->ShipmentStatus : '' ,
                                  'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                  'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                  'weight'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Value : '' ,
                                  'unit'                              => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Unit)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Unit : '' ,
                                  'carrier_name'                      => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->CarrierName)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->CarrierName : '' ,
                                  'package_status'                    => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->PackageStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->PackageStatus : '' ,
                                  'tracking_number'                   => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->TrackingId)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->TrackingId : '' ,
                                  'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                  'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                  'date'                              => date('Y-m-d H:i:s'),
                                  'update_at'                         => date('Y-m-d H:i:s'),
                                  'user_id'                           => $user_id,
                                );
              $insert++;
          }

        }

        if (count($shipmentst_array) > 0) {
          $__insert_shipemts =   $this->__insert_shipemts($shipmentst_array);
        }
        if (count($update_shipmentst_array) > 0) {
          $__insert_shipemts =   $this->__update_shipemts($update_shipmentst_array);
        }

        if (isset($data->ListInboundShipmentsResult->NextToken)) {
                  $token = (string) $data->ListInboundShipmentsResult->NextToken;
                  $result = $this->amazonShipments($token,$user_id);
        }
      } else {
        echo json_encode(array(
                    'success' => false,
                    'error_message' => 'something went wrong please try again..',
                    'user_id' => $user_id
                ));
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

  /**
   * Recursive funtion for api response
   */
  public function amazonShipments($token,$user_id) {
        $param['Action']=urlencode("ListInboundShipmentsByNextToken");
        $param['NextToken']=$token;
        $response = $this->curl_request($param);
        $data = simplexml_load_string($response);
        $shipmentst_array = array();
        if (isset($data->ListInboundShipmentsResult->ShipmentData->member) ) {
          $insert = 0;
          $update = 0;
          foreach ($data->ListInboundShipmentsResult->ShipmentData->member as $key => $value)
          {

            $transport_content = $this->GetTransportContent((string) $value->ShipmentId);
            $is_exits = $this->fullfill->checkexist('fba_shipment_details',array('shipment_id'=>(string) $value->ShipmentId));

            if ($is_exits)
            {
              $update_shipmentst_array[$update] = array(
                                  'destination_fulfillment_center_id' => (string) (isset($value->DestinationFulfillmentCenterId)) ? $value->DestinationFulfillmentCenterId : '' ,
                                  'label_prep_type'                   => (string) (isset($value->LabelPrepType)) ? $value->LabelPrepType : '' ,
                                  'city'                              => (string) (isset($value->ShipFromAddress->City)) ? $value->ShipFromAddress->City : '' ,
                                  'country_code'                      => (string) (isset($value->ShipFromAddress->CountryCode)) ? $value->ShipFromAddress->CountryCode : '' ,
                                  'postal_code'                       => (string) (isset($value->ShipFromAddress->PostalCode)) ? $value->ShipFromAddress->PostalCode : '' ,
                                  'from_name'                         => (string) (isset($value->ShipFromAddress->Name)) ? $value->ShipFromAddress->Name : '' ,
                                  'addressLine1'                      => (string) (isset($value->ShipFromAddress->AddressLine1)) ? $value->ShipFromAddress->AddressLine1 : '' ,
                                  'state_or_province_code'            => (string) (isset($value->ShipFromAddress->StateOrProvinceCode)) ? $value->ShipFromAddress->StateOrProvinceCode : '' ,
                                  'shipment_id'                       => (string) (isset($value->ShipmentId)) ? $value->ShipmentId : '' ,
                                  'are_cases_required'                => (string) (isset($value->AreCasesRequired)) ? $value->AreCasesRequired : '' ,
                                  'shipment_name'                     => (string) (isset($value->ShipmentName)) ? $value->ShipmentName : '' ,
                                  'box_contents_source'               => (string) (isset($value->BoxContentsSource)) ? $value->BoxContentsSource : '' ,
                                  'shipment_status'                   => (string) (isset($value->ShipmentStatus)) ? $value->ShipmentStatus : '' ,
                                  'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                  'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                  'weight'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Value : '' ,
                                  'unit'                              => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Unit)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Unit : '' ,
                                  'carrier_name'                      => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->CarrierName)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->CarrierName : '' ,
                                  'package_status'                    => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->PackageStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->PackageStatus : '' ,
                                  'tracking_number'                   => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->TrackingId)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->TrackingId : '' ,
                                  'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                  'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                  'update_at'                         => date('Y-m-d H:i:s'),
                                  'user_id'                           => $user_id,
                                  );
              $update++;
            }
            else
            {
              $shipmentst_array[$insert] = array(
                                    'destination_fulfillment_center_id' => (string) (isset($value->DestinationFulfillmentCenterId)) ? $value->DestinationFulfillmentCenterId : '' ,
                                    'label_prep_type'                   => (string) (isset($value->LabelPrepType)) ? $value->LabelPrepType : '' ,
                                    'city'                              => (string) (isset($value->ShipFromAddress->City)) ? $value->ShipFromAddress->City : '' ,
                                    'country_code'                      => (string) (isset($value->ShipFromAddress->CountryCode)) ? $value->ShipFromAddress->CountryCode : '' ,
                                    'postal_code'                       => (string) (isset($value->ShipFromAddress->PostalCode)) ? $value->ShipFromAddress->PostalCode : '' ,
                                    'from_name'                         => (string) (isset($value->ShipFromAddress->Name)) ? $value->ShipFromAddress->Name : '' ,
                                    'addressLine1'                      => (string) (isset($value->ShipFromAddress->AddressLine1)) ? $value->ShipFromAddress->AddressLine1 : '' ,
                                    'state_or_province_code'            => (string) (isset($value->ShipFromAddress->StateOrProvinceCode)) ? $value->ShipFromAddress->StateOrProvinceCode : '' ,
                                    'shipment_id'                       => (string) (isset($value->ShipmentId)) ? $value->ShipmentId : '' ,
                                    'are_cases_required'                => (string) (isset($value->AreCasesRequired)) ? $value->AreCasesRequired : '' ,
                                    'shipment_name'                     => (string) (isset($value->ShipmentName)) ? $value->ShipmentName : '' ,
                                    'box_contents_source'               => (string) (isset($value->BoxContentsSource)) ? $value->BoxContentsSource : '' ,
                                    'shipment_status'                   => (string) (isset($value->ShipmentStatus)) ? $value->ShipmentStatus : '' ,
                                    'shipment_type'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType)) ? $transport_content->GetTransportContentResult->TransportContent->TransportHeader->ShipmentType : '' ,
                                    'transport_status'                  => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportResult->TransportStatus : '' ,
                                    'weight'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Value : '' ,
                                    'unit'                              => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Unit)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->Weight->Unit : '' ,
                                    'carrier_name'                      => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->CarrierName)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->CarrierName : '' ,
                                    'package_status'                    => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->PackageStatus)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->PackageStatus : '' ,
                                    'tracking_number'                   => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->TrackingId)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PackageList->member[0]->TrackingId : '' ,
                                    'currency_code'                     => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->CurrencyCode : '' ,
                                    'amount'                            => (string) (isset($transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value)) ? $transport_content->GetTransportContentResult->TransportContent->TransportDetails->PartneredSmallParcelData->PartneredEstimate->Amount->Value : '' ,
                                    'date'                              => date('Y-m-d H:i:s'),
                                    'update_at'                         => date('Y-m-d H:i:s'),
                                    'user_id'                           => $user_id,
                                  );
              $insert++;
            }

          }

          if (count($shipmentst_array) > 0) {
            $__insert_shipemts =   $this->__insert_shipemts($shipmentst_array);
          }
          if (count($update_shipmentst_array) > 0) {
            $__insert_shipemts =   $this->__update_shipemts($update_shipmentst_array);
          }
          if (isset($data->ListInboundShipmentsResult->NextToken) && !empty($data->ListInboundShipmentsResult->NextToken))
          {
              $token = (string) $data->ListInboundShipmentsResult->NextToken;
              $result = $this->amazonShipments($token,$user_id);
          }
        }else {
          echo json_encode(array(
                    'success'       => false,
                    'error_message' => 'something went wrong please try again..',
                    'user_id'       => $user_id,
                    'recursive'       => true,
                ));
        }


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

  public function GetTransportContent($shipmentId)
  {
    $param['Action']    = urlencode("GetTransportContent");
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
				           'MWSAuthToken'   =>  urlencode($this->auth_token),
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

            $params=array_merge($params,$add_param);
            $url_parts = array();
            foreach(array_keys($params) as $key)
            {
                $url_parts[] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
            }
            sort($url_parts);
            $url_string = implode("&", $url_parts);
            $string_to_sign = "POST\nmws.amazonaws.com\n/FulfillmentInboundShipment/2010-10-01\n" . $url_string;
            $signature = hash_hmac("sha256", $string_to_sign, $this->secret_key, TRUE);
            $signature = urlencode(base64_encode($signature));
            $url = "https://mws.amazonaws.com/FulfillmentInboundShipment/2010-10-01?". $url_string . "&Signature=" . $signature;
            return $url;
 }


 public function xml2array($xmlObject, $out = array ())
 {
   foreach ( (array) $xmlObject as $index => $node )
           $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

       return $out;
 }


}
