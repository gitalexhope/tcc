<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Report_file_process extends CI_Model
{
  public function  __construct()
  {
      parent::__construct();
  }

  public function update_report_feed_log($user_id,$req_id)
  {
    $this->db->query("UPDATE report_feed SET is_processed=1 WHERE req_id=".$req_id);
  }


   public function process_afn_inventory_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i>=1 && !empty($buffer[0]) )
        {
           $sku= isset($buffer[0])?$this->db->escape($buffer[0]):'';
           $asin= isset($buffer[2])?$this->db->escape($buffer[2]):'';
           $itm_qty=isset($buffer[5])?$this->db->escape($buffer[5]):'';
           echo $sku."\t".$asin."\t".$itm_qty."\t".$buffer[4]."\n";
           $bulk_data[]="(".$sku.",".$asin.",".$itm_qty.",".$user_id.",'".$country."','FBA')";
        }

        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT INTO `customer_product` (prod_sku,prod_asin,itm_qty,added_by,prod_country,fc_code)VALUES
          $quer
          ON DUPLICATE KEY
          UPDATE
          prod_sku=VALUES(prod_sku),prod_asin=VALUES(prod_asin),itm_qty=VALUES(itm_qty),prod_country=VALUES(prod_country),fc_code=VALUES(fc_code);";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {
            $quer=implode(',',$bulk_data);
            $qi="INSERT INTO `customer_product` (prod_sku,prod_asin,itm_qty,added_by,prod_country,fc_code)VALUES
            $quer
            ON DUPLICATE KEY
            UPDATE
            prod_sku=VALUES(prod_sku),prod_asin=VALUES(prod_asin),itm_qty=VALUES(itm_qty),prod_country=VALUES(prod_country),fc_code=VALUES(fc_code);";
            $this->db->query($qi);
            unset($bulk_data);
            unset($quer);
     }
     fclose($fp);
    }
  }
  public function process_inventory_data($user_id,$report_file,$country,$request_type)
  {

    $fp=fopen($report_file,'r');
    if ($fp)
    {

     $i=0;
     while (!feof($fp))
     {
            $buffer = fgetcsv($fp,0,"\t");
            if($i>=1 && !empty($buffer[3]) && $country!='FR')
            {
                $item_name=isset($buffer[0])?$this->db->escape($buffer[0]):'';
                $item_description=isset($buffer[1])?$this->db->escape($buffer[1]):'';
                $listing_id=isset($buffer[2])?$this->db->escape($buffer[2]):'';
                $seller_sku=isset($buffer[3])?$this->db->escape($buffer[3]):'';
                $price=isset($buffer[4])?$this->db->escape($buffer[4]):'';
                $quantity=isset($buffer[5])?$this->db->escape($buffer[5]):'';
                $open_dat=isset($buffer[6])?$this->db->escape($buffer[6]):'';
			    $date = substr($open_dat, 1, 20);
			    $date_format=date( 'd-m-Y H:i:s', strtotime(str_replace('/', '-', $date ) ));
			    $open_date=$this->db->escape(date('Y-m-d H:i:s',strtotime($date_format)));
                $image_url=isset($buffer[7])?$this->db->escape($buffer[7]):'';
                $item_is_marketplace=isset($buffer[8])?$this->db->escape($buffer[8]):'';
                $product_id_type=isset($buffer[9])?$this->db->escape($buffer[9]):'';
                $zshop_shipping_fee=isset($buffer[10])?$this->db->escape($buffer[10]):'';
                $item_note=isset($buffer[11])?$this->db->escape($buffer[11]):'';
                $item_condition=isset($buffer[12])?$this->db->escape($buffer[12]):'';
                $zshop_category1=isset($buffer[13])?$this->db->escape($buffer[13]):'';
                $zshop_browse_path=isset($buffer[14])?$this->db->escape($buffer[14]):'';
                $zshop_storefront_feature=isset($buffer[15])?$this->db->escape($buffer[15]):'';
                $asin1=isset($buffer[16])?$this->db->escape($buffer[16]):'';
                $asin2=isset($buffer[17])?$this->db->escape($buffer[17]):'';
                $asin3=isset($buffer[18])?$this->db->escape($buffer[18]):'';
                $will_ship_internationally=isset($buffer[19])?$this->db->escape($buffer[19]):'';
                $expedited_shipping=isset($buffer[20])?$this->db->escape($buffer[20]):'';
                $zshop_boldface=isset($buffer[21])?$this->db->escape($buffer[21]):'';
                $product_id=isset($buffer[22])?$this->db->escape($buffer[22]):'';
                $bid_for_featured_placement=isset($buffer[23])?$this->db->escape($buffer[23]):'';
                $add_delete=isset($buffer[24])?$this->db->escape($buffer[24]):'';
                $pending_quantity=isset($buffer[25])?$this->db->escape($buffer[25]):'';
                $fulfillment_channel=isset($buffer[26])?$this->db->escape($buffer[26]):'';
                $business_price=isset($buffer[27])?$this->db->escape($buffer[27]):'';
                $quantity_price_type=isset($buffer[28])?$this->db->escape($buffer[28]):'';
                $quantity_lower_bound_1=isset($buffer[29])?$this->db->escape($buffer[29]):'';
                $quantity_price_1=isset($buffer[30])?$this->db->escape($buffer[30]):'';
                $quantity_lower_bound_2=isset($buffer[31])?$this->db->escape($buffer[31]):'';
                $quantity_price_2=isset($buffer[32])?$this->db->escape($buffer[32]):'';
                $quantity_lower_bound_3=isset($buffer[33])?$this->db->escape($buffer[33]):'';
                $quantity_price_3=isset($buffer[34])?$this->db->escape($buffer[34]):'';
                $quantity_lower_bound_4=isset($buffer[35])?$this->db->escape($buffer[35]):'';
                $quantity_price_4=isset($buffer[36])?$this->db->escape($buffer[36]):'';
                $quantity_lower_bound_5=isset($buffer[37])?$this->db->escape($buffer[37]):'';
                $quantity_price_5=isset($buffer[38])?$this->db->escape($buffer[38]):'';
                $merchant_shipping_group=isset($buffer[39])?$this->db->escape($buffer[39]):'';

			  $bulk_data[]="(".$item_name.",".$item_description.",".$listing_id.",".$seller_sku.",".$price.",".$quantity.",".$open_date.",".$image_url.",".$item_is_marketplace.",".$product_id_type.",".$zshop_shipping_fee.",".$item_note.",".$item_condition.",".$zshop_category1.",".$zshop_browse_path.",".$zshop_storefront_feature.",".$asin1.",".$asin2.",".$asin3.",".$will_ship_internationally.",".$expedited_shipping.",".$zshop_boldface.",".$product_id.",".$bid_for_featured_placement.",".$add_delete.",".$pending_quantity.",".$fulfillment_channel.",".$business_price.",".$quantity_price_type.",".$quantity_lower_bound_1.",".$quantity_price_1.",".$quantity_lower_bound_2.",".$quantity_price_2.",".$quantity_lower_bound_3.",".$quantity_price_3.",".$quantity_lower_bound_4.",".$quantity_price_4.",".$quantity_lower_bound_5.",".$quantity_price_5.",".$merchant_shipping_group.",'".$country."','".$user_id."')";
           }
               elseif($i>=1 && !empty($buffer[3]) && $country='FR')
               {
              $item_name=isset($buffer[0])?$this->db->escape($buffer[0]):'';
              $listing_id=isset($buffer[1])?$this->db->escape($buffer[1]):'';
              $seller_sku=isset($buffer[2])?$this->db->escape($buffer[2]):'';
              $price=isset($buffer[3])?$this->db->escape($buffer[3]):'';
              $quantity=isset($buffer[4])?$this->db->escape($buffer[4]):'';
              $open_dat=isset($buffer[5])?$this->db->escape($buffer[5]):'';
			  $date = substr($open_dat, 1, 20);
			  $date_format=date('d-m-Y H:i:s', strtotime(str_replace('/', '-', $date ) ));
			  $open_date=$this->db->escape(date('Y-m-d H:i:s',strtotime($date_format)));
              $product_id_type=isset($buffer[6])?$this->db->escape($buffer[6]):'';
              $item_note=isset($buffer[7])?$this->db->escape($buffer[7]):'';
              $item_condition=isset($buffer[8])?$this->db->escape($buffer[8]):'';
              $will_ship_internationally=isset($buffer[9])?$this->db->escape($buffer[9]):'';
              $expedited_shipping=isset($buffer[10])?$this->db->escape($buffer[10]):'';
              $product_id=isset($buffer[11])?$this->db->escape($buffer[11]):'';
              $pending_quantity=isset($buffer[12])?$this->db->escape($buffer[12]):'';
              $fulfillment_channel=isset($buffer[13])?$this->db->escape($buffer[13]):'';
              $business_price=isset($buffer[14])?$this->db->escape($buffer[14]):'';
              $quantity_price_type=isset($buffer[15])?$this->db->escape($buffer[15]):'';
              $quantity_lower_bound_1=isset($buffer[16])?$this->db->escape($buffer[16]):'';
              $quantity_price_1=isset($buffer[17])?$this->db->escape($buffer[17]):'';
              $quantity_lower_bound_2=isset($buffer[18])?$this->db->escape($buffer[18]):'';
              $quantity_price_2=isset($buffer[19])?$this->db->escape($buffer[19]):'';
              $quantity_lower_bound_3=isset($buffer[20])?$this->db->escape($buffer[20]):'';
              $quantity_price_3=isset($buffer[21])?$this->db->escape($buffer[21]):'';
              $quantity_lower_bound_4=isset($buffer[22])?$this->db->escape($buffer[22]):'';
              $quantity_price_4=isset($buffer[23])?$this->db->escape($buffer[23]):'';
              $quantity_lower_bound_5=isset($buffer[24])?$this->db->escape($buffer[24]):'';
              $quantity_price_5=isset($buffer[25])?$this->db->escape($buffer[25]):'';
              $merchant_shipping_group=isset($buffer[26])?$this->db->escape($buffer[26]):'';
			  $item_description="''";
			  $image_url="''";
			  $item_is_marketplace="''";
			  $zshop_shipping_fee="''";
			  $zshop_category1="''";
              $zshop_browse_path="''";
              $zshop_storefront_feature="''";
              $asin1="''";
              $asin2="''";
              $asin3="''";
			  $zshop_boldface="''";
			  $bid_for_featured_placement="''";
			  $add_delete="''";



			  $bulk_data[]="(".$item_name.",".$item_description.",".$listing_id.",".$seller_sku.",".$price.",".$quantity.",".$open_date.",".$image_url.",".$item_is_marketplace.",".$product_id_type.",".$zshop_shipping_fee.",".$item_note.",".$item_condition.",".$zshop_category1.",".$zshop_browse_path.",".$zshop_storefront_feature.",".$asin1.",".$asin2.",".$asin3.",".$will_ship_internationally.",".$expedited_shipping.",".$zshop_boldface.",".$product_id.",".$bid_for_featured_placement.",".$add_delete.",".$pending_quantity.",".$fulfillment_channel.",".$business_price.",".$quantity_price_type.",".$quantity_lower_bound_1.",".$quantity_price_1.",".$quantity_lower_bound_2.",".$quantity_price_2.",".$quantity_lower_bound_3.",".$quantity_price_3.",".$quantity_lower_bound_4.",".$quantity_price_4.",".$quantity_lower_bound_5.",".$quantity_price_5.",".$merchant_shipping_group.",'".$country."','".$user_id."')";
           }
		   //print_r($bulk_data);
           if(isset($bulk_data) && count($bulk_data)>=500)
           {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `active_inventory_data` (item_name,item_description,listing_id,seller_sku,price,quantity,open_date,image_url,item_is_marketplace,product_id_type,zshop_shipping_fee,item_note,item_condition,zshop_category1,zshop_browse_path,zshop_storefront_feature,asin1,asin2,asin3,will_ship_internationally,expedited_shipping,zshop_boldface,product_id,bid_for_featured_placement,add_delete,pending_quantity,fulfillment_channel,business_price,quantity_price_type,quantity_lower_bound_1,quantity_price_1,quantity_lower_bound_2,quantity_price_2,quantity_lower_bound_3,quantity_price_3,quantity_lower_bound_4,quantity_price_4,quantity_lower_bound_5,quantity_price_5,merchant_shipping_group,country,added_by)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              item_name=VALUES(item_name),item_description=VALUES(item_description),listing_id=VALUES(listing_id),seller_sku=VALUES(seller_sku),price=VALUES(price),quantity=VALUES(quantity),open_date=VALUES(open_date),image_url=VALUES(image_url),item_is_marketplace=VALUES(item_is_marketplace),product_id_type=VALUES(product_id_type),zshop_shipping_fee=VALUES(zshop_shipping_fee),item_note=VALUES(item_note),item_condition=VALUES(item_condition),zshop_category1=VALUES(zshop_category1),zshop_browse_path=VALUES(zshop_browse_path),zshop_storefront_feature=VALUES(zshop_storefront_feature),asin1=VALUES(asin1),asin2=VALUES(asin2),asin3=VALUES(asin3),will_ship_internationally=VALUES(will_ship_internationally),expedited_shipping=VALUES(expedited_shipping),zshop_boldface=VALUES(zshop_boldface),product_id=VALUES(product_id),bid_for_featured_placement=VALUES(bid_for_featured_placement),add_delete=VALUES(add_delete),pending_quantity=VALUES(pending_quantity),fulfillment_channel=VALUES(fulfillment_channel),business_price=VALUES(business_price),quantity_price_type=VALUES(quantity_price_type),quantity_lower_bound_1=VALUES(quantity_lower_bound_1),quantity_price_1=VALUES(quantity_price_1),quantity_lower_bound_2=VALUES(quantity_lower_bound_2),quantity_price_2=VALUES(quantity_price_2),quantity_lower_bound_3=VALUES(quantity_lower_bound_3),quantity_price_3=VALUES(quantity_price_3),quantity_lower_bound_4=VALUES(quantity_lower_bound_4),quantity_price_4=VALUES(quantity_price_4),quantity_lower_bound_5=VALUES(quantity_lower_bound_5),quantity_price_5=VALUES(quantity_price_5),merchant_shipping_group=VALUES(merchant_shipping_group),country=VALUES(country),added_by=VALUES(added_by);";
              $this->db->query($qi);
              unset($bulk_data);
              unset($quer);
           }
           $i++;
    }//while ends here
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT INTO `active_inventory_data` (item_name,item_description,listing_id,seller_sku,price,quantity,open_date,image_url,item_is_marketplace,product_id_type,zshop_shipping_fee,item_note,item_condition,zshop_category1,zshop_browse_path,zshop_storefront_feature,asin1,asin2,asin3,will_ship_internationally,expedited_shipping,zshop_boldface,product_id,bid_for_featured_placement,add_delete,pending_quantity,fulfillment_channel,business_price,quantity_price_type,quantity_lower_bound_1,quantity_price_1,quantity_lower_bound_2,quantity_price_2,quantity_lower_bound_3,quantity_price_3,quantity_lower_bound_4,quantity_price_4,quantity_lower_bound_5,quantity_price_5,merchant_shipping_group,country,added_by)VALUES
          $quer
          ON DUPLICATE KEY
          UPDATE
          item_name=VALUES(item_name),item_description=VALUES(item_description),listing_id=VALUES(listing_id),seller_sku=VALUES(seller_sku),price=VALUES(price),quantity=VALUES(quantity),open_date=VALUES(open_date),image_url=VALUES(image_url),item_is_marketplace=VALUES(item_is_marketplace),product_id_type=VALUES(product_id_type),zshop_shipping_fee=VALUES(zshop_shipping_fee),item_note=VALUES(item_note),item_condition=VALUES(item_condition),zshop_category1=VALUES(zshop_category1),zshop_browse_path=VALUES(zshop_browse_path),zshop_storefront_feature=VALUES(zshop_storefront_feature),asin1=VALUES(asin1),asin2=VALUES(asin2),asin3=VALUES(asin3),will_ship_internationally=VALUES(will_ship_internationally),expedited_shipping=VALUES(expedited_shipping),zshop_boldface=VALUES(zshop_boldface),product_id=VALUES(product_id),bid_for_featured_placement=VALUES(bid_for_featured_placement),add_delete=VALUES(add_delete),pending_quantity=VALUES(pending_quantity),fulfillment_channel=VALUES(fulfillment_channel),business_price=VALUES(business_price),quantity_price_type=VALUES(quantity_price_type),quantity_lower_bound_1=VALUES(quantity_lower_bound_1),quantity_price_1=VALUES(quantity_price_1),quantity_lower_bound_2=VALUES(quantity_lower_bound_2),quantity_price_2=VALUES(quantity_price_2),quantity_lower_bound_3=VALUES(quantity_lower_bound_3),quantity_price_3=VALUES(quantity_price_3),quantity_lower_bound_4=VALUES(quantity_lower_bound_4),quantity_price_4=VALUES(quantity_price_4),quantity_lower_bound_5=VALUES(quantity_lower_bound_5),quantity_price_5=VALUES(quantity_price_5),merchant_shipping_group=VALUES(merchant_shipping_group),country=VALUES(country),added_by=VALUES(added_by);";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
      fclose($fp);

      }
  }

  public function process_inactive_inventory_data($user_id,$report_file,$country,$request_type)
  {

    $fp=fopen($report_file,'r');
    if ($fp)
    {

     $i=0;
     while (!feof($fp))
     {
            $buffer = fgetcsv($fp,0,"\t");
            if($i>=1 && !empty($buffer[3]) && $country!='FR')
            {
             $item_name=isset($buffer[0])?$this->db->escape($buffer[0]):'';
             $item_description=isset($buffer[1])?$this->db->escape($buffer[1]):'';
             $listing_id=isset($buffer[2])?$this->db->escape($buffer[2]):'';
             $seller_sku=isset($buffer[3])?$this->db->escape($buffer[3]):'';
             $price=isset($buffer[4])?$this->db->escape($buffer[4]):'';
             $quantity=isset($buffer[5])?$this->db->escape($buffer[5]):'';
             $open_date=isset($buffer[6])?$this->db->escape($buffer[6]):'';
             $image_url=isset($buffer[7])?$this->db->escape($buffer[7]):'';
             $item_is_marketplace=isset($buffer[8])?$this->db->escape($buffer[8]):'';
             $product_id_type=isset($buffer[9])?$this->db->escape($buffer[9]):'';
             $zshop_shipping_fee=isset($buffer[10])?$this->db->escape($buffer[10]):'';
             $item_note=isset($buffer[11])?$this->db->escape($buffer[11]):'';
             $item_condition=isset($buffer[12])?$this->db->escape($buffer[12]):'';
             $zshop_category1=isset($buffer[13])?$this->db->escape($buffer[13]):'';
             $zshop_browse_path=isset($buffer[14])?$this->db->escape($buffer[14]):'';
             $zshop_storefront_feature=isset($buffer[15])?$this->db->escape($buffer[15]):'';
             $asin1=isset($buffer[16])?$this->db->escape($buffer[16]):'';
             $asin2=isset($buffer[17])?$this->db->escape($buffer[17]):'';
             $asin3=isset($buffer[18])?$this->db->escape($buffer[18]):'';
             $will_ship_internationally=isset($buffer[19])?$this->db->escape($buffer[19]):'';
             $expedited_shipping=isset($buffer[20])?$this->db->escape($buffer[20]):'';
             $zshop_boldface=isset($buffer[21])?$this->db->escape($buffer[21]):'';
             $product_id=isset($buffer[22])?$this->db->escape($buffer[22]):'';
             $bid_for_featured_placement=isset($buffer[23])?$this->db->escape($buffer[23]):'';
             $add_delete=isset($buffer[24])?$this->db->escape($buffer[24]):'';
             $pending_quantity=isset($buffer[25])?$this->db->escape($buffer[25]):'';
             $fulfillment_channel=isset($buffer[26])?$this->db->escape($buffer[26]):'';
             $merchant_shipping_group=isset($buffer[27])?$this->db->escape($buffer[27]):'';

			  $bulk_data[]="(".$item_name.",".$item_description.",".$listing_id.",".$seller_sku.",".$price.",".$quantity.",".$open_date.",".$image_url.",".$item_is_marketplace.",".$product_id_type.",".$zshop_shipping_fee.",".$item_note.",".$item_condition.",".$zshop_category1.",".$zshop_browse_path.",".$zshop_storefront_feature.",".$asin1.",".$asin2.",".$asin3.",".$will_ship_internationally.",".$expedited_shipping.",".$zshop_boldface.",".$product_id.",".$bid_for_featured_placement.",".$add_delete.",".$pending_quantity.",".$fulfillment_channel.",".$merchant_shipping_group.",'".$country."','".$user_id."')";
           }
               elseif($i>=1 && !empty($buffer[3]) && $country='FR')
               {
              $item_name=isset($buffer[0])?$this->db->escape($buffer[0]):'';
              $listing_id=isset($buffer[1])?$this->db->escape($buffer[1]):'';
              $seller_sku=isset($buffer[2])?$this->db->escape($buffer[2]):'';
              $price=isset($buffer[3])?$this->db->escape($buffer[3]):'';
              $quantity=isset($buffer[4])?$this->db->escape($buffer[4]):'';
              $open_date=isset($buffer[5])?$this->db->escape($buffer[5]):'';
              $product_id_type=isset($buffer[6])?$this->db->escape($buffer[6]):'';
              $item_note=isset($buffer[7])?$this->db->escape($buffer[7]):'';
              $item_condition=isset($buffer[8])?$this->db->escape($buffer[8]):'';
              $will_ship_internationally=isset($buffer[9])?$this->db->escape($buffer[9]):'';
              $expedited_shipping=isset($buffer[10])?$this->db->escape($buffer[10]):'';
              $product_id=isset($buffer[11])?$this->db->escape($buffer[11]):'';
              $pending_quantity=isset($buffer[12])?$this->db->escape($buffer[12]):'';
              $fulfillment_channel=isset($buffer[13])?$this->db->escape($buffer[13]):'';
              $merchant_shipping_group=isset($buffer[14])?$this->db->escape($buffer[14]):'';
			  $item_description="''";
			  $image_url="''";
			  $item_is_marketplace="''";
			  $zshop_shipping_fee="''";
			  $zshop_category1="''";
              $zshop_browse_path="''";
              $zshop_storefront_feature="''";
              $asin1="''";
              $asin2="''";
              $asin3="''";
			  $zshop_boldface="''";
			  $bid_for_featured_placement="''";
			  $add_delete="''";
			  $bulk_data[]="(".$item_name.",".$item_description.",".$listing_id.",".$seller_sku.",".$price.",".$quantity.",".$open_date.",".$image_url.",".$item_is_marketplace.",".$product_id_type.",".$zshop_shipping_fee.",".$item_note.",".$item_condition.",".$zshop_category1.",".$zshop_browse_path.",".$zshop_storefront_feature.",".$asin1.",".$asin2.",".$asin3.",".$will_ship_internationally.",".$expedited_shipping.",".$zshop_boldface.",".$product_id.",".$bid_for_featured_placement.",".$add_delete.",".$pending_quantity.",".$fulfillment_channel.",".$merchant_shipping_group.",'".$country."','".$user_id."')";



           }
		   //print_r($bulk_data);
           if(isset($bulk_data) && count($bulk_data)>=500)
           {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `inactive_inventory_data` (item_name,item_description,listing_id,seller_sku,price,quantity,open_date,image_url,item_is_marketplace,product_id_type,zshop_shipping_fee,item_note,item_condition,zshop_category1,zshop_browse_path,zshop_storefront_feature,asin1,asin2,asin3,will_ship_internationally,expedited_shipping,zshop_boldface,product_id,bid_for_featured_placement,add_delete,pending_quantity,fulfillment_channel,merchant_shipping_group,country,added_by)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              item_name=VALUES(item_name),item_description=VALUES(item_description),listing_id=VALUES(listing_id),seller_sku=VALUES(seller_sku),price=VALUES(price),quantity=VALUES(quantity),open_date=VALUES(open_date),image_url=VALUES(image_url),item_is_marketplace=VALUES(item_is_marketplace),product_id_type=VALUES(product_id_type),zshop_shipping_fee=VALUES(zshop_shipping_fee),item_note=VALUES(item_note),item_condition=VALUES(item_condition),zshop_category1=VALUES(zshop_category1),zshop_browse_path=VALUES(zshop_browse_path),zshop_storefront_feature=VALUES(zshop_storefront_feature),asin1=VALUES(asin1),asin2=VALUES(asin2),asin3=VALUES(asin3),will_ship_internationally=VALUES(will_ship_internationally),expedited_shipping=VALUES(expedited_shipping),zshop_boldface=VALUES(zshop_boldface),product_id=VALUES(product_id),bid_for_featured_placement=VALUES(bid_for_featured_placement),add_delete=VALUES(add_delete),pending_quantity=VALUES(pending_quantity),fulfillment_channel=VALUES(fulfillment_channel),merchant_shipping_group=VALUES(merchant_shipping_group),country=VALUES(country),added_by=VALUES(added_by);";
              $this->db->query($qi);
              unset($bulk_data);
              unset($quer);
           }
           $i++;
    }//while ends here
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT INTO `inactive_inventory_data` (item_name,item_description,listing_id,seller_sku,price,quantity,open_date,image_url,item_is_marketplace,product_id_type,zshop_shipping_fee,item_note,item_condition,zshop_category1,zshop_browse_path,zshop_storefront_feature,asin1,asin2,asin3,will_ship_internationally,expedited_shipping,zshop_boldface,product_id,bid_for_featured_placement,add_delete,pending_quantity,fulfillment_channel,merchant_shipping_group,country,added_by)VALUES
          $quer
          ON DUPLICATE KEY
          UPDATE
          item_name=VALUES(item_name),item_description=VALUES(item_description),listing_id=VALUES(listing_id),seller_sku=VALUES(seller_sku),price=VALUES(price),quantity=VALUES(quantity),open_date=VALUES(open_date),image_url=VALUES(image_url),item_is_marketplace=VALUES(item_is_marketplace),product_id_type=VALUES(product_id_type),zshop_shipping_fee=VALUES(zshop_shipping_fee),item_note=VALUES(item_note),item_condition=VALUES(item_condition),zshop_category1=VALUES(zshop_category1),zshop_browse_path=VALUES(zshop_browse_path),zshop_storefront_feature=VALUES(zshop_storefront_feature),asin1=VALUES(asin1),asin2=VALUES(asin2),asin3=VALUES(asin3),will_ship_internationally=VALUES(will_ship_internationally),expedited_shipping=VALUES(expedited_shipping),zshop_boldface=VALUES(zshop_boldface),product_id=VALUES(product_id),bid_for_featured_placement=VALUES(bid_for_featured_placement),add_delete=VALUES(add_delete),pending_quantity=VALUES(pending_quantity),fulfillment_channel=VALUES(fulfillment_channel),merchant_shipping_group=VALUES(merchant_shipping_group),country=VALUES(country),added_by=VALUES(added_by);";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
      fclose($fp);

      }
  }




  public function process_order_update_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i>=1 && !empty($buffer[0]) )
        {
               $order_id= isset($buffer[0])?$this->db->escape($buffer[0]):'';
			   $mer_order_id= isset($buffer[1])?$this->db->escape($buffer[1]):'';
			   $po_date= isset($buffer[2])?$this->db->escape($buffer[2]):'';
               $last_update= isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $order_status= isset($buffer[4])?$buffer[4]:'';
               if($order_status=='Cancelled')
               {
                $order_status='Canceled';
               }
               $order_status=!empty($order_status)?$this->db->escape($order_status):'';
               $fullfill=isset($buffer[5])?$this->db->escape($buffer[5]):'';
			   $sale_channel= $buffer[6];
               $cnt=explode('.',$sale_channel);
               if(count($cnt) > 1)
               {
               $contry=$cnt[count($cnt)-1];
               $country2=strtoupper($contry);
                 //print_r($country2);
               }
               $sale_channel= isset($buffer[6])?$this->db->escape($buffer[6]):'';
//print_r($sale_channel);
//die();
			   $ship_service=isset($buffer[9])?$this->db->escape($buffer[9]):'';
			   $title=isset($buffer[10])?$this->db->escape($buffer[10]):'';
			   $sku=isset($buffer[11])?$this->db->escape($buffer[11]):'';
               $asin=isset($buffer[12])?$this->db->escape($buffer[12]):'';
			   $itm_status=isset($buffer[13])?$this->db->escape($buffer[13]):'';
			   $qty=isset($buffer[14])?$this->db->escape($buffer[14]):'';
			   $curr=isset($buffer[15])?$this->db->escape($buffer[15]):'';
			   $itm_price=isset($buffer[16])?$this->db->escape($buffer[16]):'';
			   $itm_tax=isset($buffer[17])?$this->db->escape($buffer[17]):'';
			   $ship_price=isset($buffer[18])?$this->db->escape($buffer[18]):'';
			   $ship_tax=isset($buffer[19])?$this->db->escape($buffer[19]):'';
               $gift_price=isset($buffer[20])?$this->db->escape($buffer[20]):'';
               $gift_tax=isset($buffer[21])?$this->db->escape($buffer[21]):'';
			   $promo_disc=isset($buffer[22])?$this->db->escape($buffer[22]):'';
			   $ship_disc=isset($buffer[23])?$this->db->escape($buffer[23]):'';
			   $ship_city=isset($buffer[24])?$this->db->escape($buffer[24]):'';
			   $ship_state=isset($buffer[25])?$this->db->escape($buffer[25]):'';
			   $ship_post=isset($buffer[26])?$this->db->escape($buffer[26]):'';
			   $ship_country=isset($buffer[27])?$this->db->escape($buffer[27]):'';
			   $promo_id=isset($buffer[28])?$this->db->escape($buffer[28]):'';




   $bulk_data[]="(".$order_id.",".$mer_order_id.",".$po_date.",".$last_update.",".$order_status.",".$fullfill.",'".$country2."',".$sku.",".$asin.",".$itm_status.",".$gift_price.",".$title.",".$ship_service.",".$qty.",".$curr.",".$itm_price.",".$itm_tax.",".$ship_price.",".$ship_tax.",".$gift_tax.",".$promo_disc.",".$ship_disc.",".$ship_city.",".$ship_state.",".$ship_post.",".$ship_country.",".$promo_id.",".$user_id.")";

        }

       if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              // $qi="INSERT INTO `customer_product` (prod_title,prod_asin,prod_sku,itm_price,itm_qty,open_date,itm_condition,added_by,is_active,is_deleted)VALUES
              $qi="INSERT INTO `rep_orders_update_list`(order_id,mer_order_id,po_date,last_update_date,ord_status,fulfillment,sales_channel,ord_sku,asin,itm_status,gift_price,title,ship_service,qty,currency,itm_price,itm_tax,ship_price,ship_tax,gift_tax,itm_promo_discount,itm_ship_discount,ship_city,ship_state,ship_post,ship_country,promo_id,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              order_id=VALUES(order_id),mer_order_id=VALUES(mer_order_id),po_date=VALUES(po_date),last_update_date=VALUES(last_update_date),ord_status=VALUES(ord_status),fulfillment=VALUES(fulfillment),sales_channel=VALUES(sales_channel),ord_sku=VALUES(ord_sku),asin=VALUES(asin),itm_status=VALUES(itm_status),gift_price=VALUES(gift_price),title=VALUES(title),ship_service=VALUES(ship_service),currency=VALUES(currency),itm_price=VALUES(itm_price),itm_tax=VALUES(itm_tax),ship_price=VALUES(ship_price),ship_tax=VALUES(ship_tax),gift_tax=VALUES(gift_tax),itm_promo_discount=VALUES(itm_promo_discount),itm_ship_discount=VALUES(itm_ship_discount),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_country=VALUES(ship_country),promo_id=VALUES(promo_id),user_id=VALUES(user_id);";
              $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
              $quer=implode(',',$bulk_data);
              // $qi="INSERT INTO `customer_product` (prod_title,prod_asin,prod_sku,itm_price,itm_qty,open_date,itm_condition,added_by,is_active,is_deleted)VALUES
              $qi="INSERT INTO `rep_orders_update_list`(order_id,mer_order_id,po_date,last_update_date,ord_status,fulfillment,sales_channel,ord_sku,asin,itm_status,gift_price,title,ship_service,qty,currency,itm_price,itm_tax,ship_price,ship_tax,gift_tax,itm_promo_discount,itm_ship_discount,ship_city,ship_state,ship_post,ship_country,promo_id,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              order_id=VALUES(order_id),mer_order_id=VALUES(mer_order_id),po_date=VALUES(po_date),last_update_date=VALUES(last_update_date),ord_status=VALUES(ord_status),fulfillment=VALUES(fulfillment),sales_channel=VALUES(sales_channel),ord_sku=VALUES(ord_sku),asin=VALUES(asin),itm_status=VALUES(itm_status),gift_price=VALUES(gift_price),title=VALUES(title),ship_service=VALUES(ship_service),currency=VALUES(currency),itm_price=VALUES(itm_price),itm_tax=VALUES(itm_tax),ship_price=VALUES(ship_price),ship_tax=VALUES(ship_tax),gift_tax=VALUES(gift_tax),itm_promo_discount=VALUES(itm_promo_discount),itm_ship_discount=VALUES(itm_ship_discount),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_country=VALUES(ship_country),promo_id=VALUES(promo_id),user_id=VALUES(user_id);";
              $this->db->query($qi);

	          unset($bulk_data);
              unset($quer);
        }
     fclose($fp);
    }
  }
  public function process_vat_tax_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {

     $i=0;
     while (!feof($fp))
     {
            $buffer = fgetcsv($fp);
			//print_r($buffer);

            if($i>=1 && !empty($buffer[4]))
            {
               $market_id= isset($buffer[0])?$this->db->escape($buffer[0]):'';
			   //print_r($market_id);
               $merchant_id= isset($buffer[1])?$this->db->escape($buffer[1]):'';
               $order_date=isset($buffer[2])?$this->db->escape($buffer[2]):'';
               $or_date = substr($order_date, 1, 12);
               $ord_date=date("Y-m-d", strtotime($or_date));
               //print_r($ord_date);
               //die();
               $trans_type=isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $order_id=isset($buffer[4])?$this->db->escape($buffer[4]):'';
               // $open_date=$this->db->escape(date('Y-m-d H:i:s',strtotime($open_date)));
               $shipping_date=isset($buffer[5])?$this->db->escape($buffer[5]):'';
               $sh_date = substr($shipping_date, 1, 12);
               $ship_date=date("Y-m-d", strtotime($sh_date));
               $ship_id=isset($buffer[6])?$this->db->escape($buffer[6]):'';
			   $trans_id=isset($buffer[7])?$this->db->escape($buffer[7]):'';
			   $asin=isset($buffer[8])?$this->db->escape($buffer[8]):'';
			   $sku=isset($buffer[9])?$this->db->escape($buffer[9]):'';
			   $qty=isset($buffer[10])?$this->db->escape($buffer[10]):'';
			   $tax_calcul_date=isset($buffer[11])?$this->db->escape($buffer[11]):'';
               $tx_date = substr($tax_calcul_date, 1, 12);
               $tax_cal_date=date("Y-m-d", strtotime($tx_date));
			   $tax_rate=isset($buffer[12])?$this->db->escape($buffer[12]):'';
			   $tax_code=isset($buffer[13])?$this->db->escape($buffer[13]):'';
			   $currency=isset($buffer[14])?$this->db->escape($buffer[14]):'';
			   $tax_type=isset($buffer[15])?$this->db->escape($buffer[15]):'';
			   $tax_cal_reason=isset($buffer[16])?$this->db->escape($buffer[16]):'';
			   $tax_addr_role=isset($buffer[17])?$this->db->escape($buffer[17]):'';
			   $jurisdiction_level=isset($buffer[18])?$this->db->escape($buffer[18]):'';
		       $jurisdiction_name=isset($buffer[19])?$this->db->escape($buffer[19]):'';
			   $our_price_tax_inclusive=isset($buffer[20])?$this->db->escape($buffer[20]):'';
			   $our_price_tax_amount=isset($buffer[21])?$this->db->escape($buffer[21]):'';
			   $our_price_tax_exclusive=isset($buffer[22])?$this->db->escape($buffer[22]):'';
			   $our_promo_amount_inclusive=isset($buffer[23])?$this->db->escape($buffer[23]):'';
			   $our_tax_promo_amount=isset($buffer[24])?$this->db->escape($buffer[24]):'';
			   $our_promo_amount_exclusive=isset($buffer[25])?$this->db->escape($buffer[25]):'';
			   $ship_tax_inclusive=isset($buffer[26])?$this->db->escape($buffer[26]):'';
			   $ship_tax=isset($buffer[27])?$this->db->escape($buffer[27]):'';
			   $ship_tax_exclusive=isset($buffer[28])?$this->db->escape($buffer[28]):'';
			   $ship_tax_promo_inclusive=isset($buffer[29])?$this->db->escape($buffer[29]):'';
			   $ship_tax_promo=isset($buffer[30])?$this->db->escape($buffer[30]):'';
			   $ship_tax_promo_exclusive=isset($buffer[31])?$this->db->escape($buffer[31]):'';
			   $gift_tax_inclusive=isset($buffer[32])?$this->db->escape($buffer[32]):'';
			   $gift_tax=isset($buffer[33])?$this->db->escape($buffer[33]):'';
			   $gift_tax_exclusive=isset($buffer[34])?$this->db->escape($buffer[34]):'';
			   $gift_tax_promo_inclusive=isset($buffer[35])?$this->db->escape($buffer[35]):'';
			   $gift_tax_promo=isset($buffer[36])?$this->db->escape($buffer[36]):'';
			   $gift_tax_promo_exclusive=isset($buffer[37])?$this->db->escape($buffer[37]):'';
			   $sell_tax_reg=isset($buffer[38])?$this->db->escape($buffer[38]):'';
			   $sell_tax_reg_jud=isset($buffer[39])?$this->db->escape($buffer[39]):'';
			   $buy_tax_reg=isset($buffer[40])?$this->db->escape($buffer[40]):'';
			   $buy_tax_reg_jud=isset($buffer[41])?$this->db->escape($buffer[41]):'';
			   $buy_tax_reg_type=isset($buffer[42])?$this->db->escape($buffer[42]):'';
			   $inv_curr_code=isset($buffer[43])?$this->db->escape($buffer[43]):'';
			   $inv_ex_rate=isset($buffer[44])?$this->db->escape($buffer[44]):'';
			   $inv_ex_date=isset($buffer[45])?$this->db->escape($buffer[45]):'';
			   $con_tax_amt=isset($buffer[46])?$this->db->escape($buffer[46]):'';
			   $vat_inv_no=isset($buffer[47])?$this->db->escape($buffer[47]):'';
			   $inv_url=isset($buffer[48])?$this->db->escape($buffer[48]):'';
			   $exp_out_eu=isset($buffer[49])?$this->db->escape($buffer[49]):'';
			   $ship_from_city=isset($buffer[50])?$this->db->escape($buffer[50]):'';
			   $ship_from_state=isset($buffer[51])?$this->db->escape($buffer[51]):'';
			   $ship_from_country=isset($buffer[52])?$this->db->escape($buffer[52]):'';
			   $ship_from_postal=isset($buffer[53])?$this->db->escape($buffer[53]):'';
			   $ship_from_tax_loca=isset($buffer[54])?$this->db->escape($buffer[54]):'';
			   $ship_to_city=isset($buffer[55])?$this->db->escape($buffer[55]):'';
			   $ship_to_state=isset($buffer[56])?$this->db->escape($buffer[56]):'';
			   $ship_to_country=isset($buffer[57])?$this->db->escape($buffer[57]):'';
			   $ship_to_postal=isset($buffer[58])?$this->db->escape($buffer[58]):'';
			   $ship_to_tax_loca=isset($buffer[59])?$this->db->escape($buffer[59]):'';


      		 $bulk_data[]="(".$market_id.",".$merchant_id.",'".$ord_date."',".$trans_type.",".$order_id.",'".$ship_date."',".$ship_id.",".$trans_id.",".$asin.",".$sku.",".$qty.",'".$tax_cal_date."',".$tax_rate.",".$tax_code.",".$currency.",".$tax_type.",".$tax_cal_reason.",".$tax_addr_role.",".$jurisdiction_level.",".$jurisdiction_name.",".$our_price_tax_inclusive.",".$our_price_tax_amount.",".$our_price_tax_exclusive.",".$our_promo_amount_inclusive.",".$our_tax_promo_amount.",".$our_promo_amount_exclusive.",".$ship_tax_inclusive.",".$ship_tax.",".$ship_tax_exclusive.",".$ship_tax_promo_inclusive.",".$ship_tax_promo.",".$ship_tax_promo_exclusive.",".$gift_tax_inclusive.",".$gift_tax.",".$gift_tax_exclusive.",".$gift_tax_promo_inclusive.",".$gift_tax_promo.",".$gift_tax_promo_exclusive.",".$sell_tax_reg.",".$sell_tax_reg_jud.",".$buy_tax_reg.",".$buy_tax_reg_jud.",".$buy_tax_reg_type.",".$inv_curr_code.",".$inv_ex_rate.",".$inv_ex_date.",".$con_tax_amt.",".$vat_inv_no.",".$inv_url.",".$exp_out_eu.",".$ship_from_city.",".$ship_from_state.",".$ship_from_country.",".$ship_from_postal.",".$ship_from_tax_loca.",".$ship_to_city.",".$ship_to_state.",".$ship_to_country.",".$ship_to_postal.",".$ship_to_tax_loca.",".$user_id.")";

		  }

           if(isset($bulk_data) && count($bulk_data)>=500)
           {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_sc_vat_tax` (market_id,merchant_id,ord_date,trans_type,order_id,ship_date,ship_id,trans_id,asin,sku,qty,tax_cal_date,tax_rate,tax_code,currency,tax_type,tax_cal_rsn_code,tax_addr_role,juri_level,juri_country,our_price_tax_inclusive,our_price_tax,our_price_tax_exclusive,our_promo_amount_inclusive,our_promo_amount,our_promo_amount_exclusive,ship_tax_inclusive,ship_tax,ship_tax_exclusive,ship_tax_promo_inclusive,ship_tax_promo,ship_tax_promo_exclusive,gift_tax_inclusive,gift_tax,gift_tax_exclusive,gift_tax_promo_inclusive,gift_tax_promo,gift_tax_promo_exclusive,sell_tax_reg,sell_tax_reg_jud,buy_tax_reg,buy_tax_reg_jud,buy_tax_reg_type,inv_curr_code,inv_ex_rate,inv_ex_date,con_tax_amt,vat_inv_no,inv_url,exp_out_eu,ship_from_city,ship_from_state,ship_from_country,ship_from_postal,ship_from_tax_loca,ship_to_city,ship_to_state,ship_to_country,ship_to_postal,ship_to_tax_loca,user_id)VALUES
              $quer
              ON DUPLICATE KEY
               UPDATE
              market_id=VALUES(market_id),merchant_id=VALUES(merchant_id),ord_date=VALUES(ord_date),trans_type=VALUES(trans_type),order_id=VALUES(order_id),ship_date=VALUES(ship_date),ship_id=VALUES(ship_id),trans_id=VALUES(trans_id),asin=VALUES(asin),sku=VALUES(sku),qty=VALUES(qty),tax_cal_date=VALUES(tax_cal_date),tax_rate=VALUES(tax_rate),
			  tax_code=VALUES(tax_code),currency=VALUES(currency),tax_type=VALUES(tax_type),tax_cal_rsn_code=VALUES(tax_cal_rsn_code),tax_addr_role=VALUES(tax_addr_role),juri_level=VALUES(juri_level),juri_country=VALUES(juri_country),our_price_tax_inclusive=VALUES(our_price_tax_inclusive),our_price_tax=VALUES(our_price_tax),our_price_tax_exclusive=VALUES(our_price_tax_exclusive),our_promo_amount_inclusive=VALUES(our_promo_amount_inclusive),
			  our_promo_amount=VALUES(our_promo_amount),our_promo_amount_exclusive=VALUES(our_promo_amount_exclusive),ship_tax_inclusive=VALUES(ship_tax_inclusive),ship_tax=VALUES(ship_tax),ship_tax_exclusive=VALUES(ship_tax_exclusive),ship_tax_promo_inclusive=VALUES(ship_tax_promo_inclusive), ship_tax_promo=VALUES(ship_tax_promo),ship_tax_promo_exclusive=VALUES(ship_tax_promo_exclusive),gift_tax_inclusive=VALUES(gift_tax_inclusive),
			  gift_tax=VALUES(gift_tax),gift_tax_exclusive=VALUES(gift_tax_exclusive),gift_tax_promo_inclusive=VALUES(gift_tax_promo_inclusive),gift_tax_promo=VALUES(gift_tax_promo),gift_tax_promo_exclusive=VALUES(gift_tax_promo_exclusive),sell_tax_reg=VALUES(sell_tax_reg),sell_tax_reg_jud=VALUES(sell_tax_reg_jud),buy_tax_reg=VALUES(buy_tax_reg),buy_tax_reg_jud=VALUES(buy_tax_reg_jud),buy_tax_reg_type=VALUES(buy_tax_reg_type),inv_curr_code=VALUES(inv_curr_code),inv_ex_rate=VALUES(inv_ex_rate),
			  inv_ex_date=VALUES(inv_ex_date),con_tax_amt=VALUES(con_tax_amt),vat_inv_no=VALUES(vat_inv_no),inv_url=VALUES(inv_url),exp_out_eu=VALUES(exp_out_eu),ship_from_city=VALUES(ship_from_city),ship_from_state=VALUES(ship_from_state),ship_from_country=VALUES(ship_from_country),ship_from_postal=VALUES(ship_from_postal),ship_from_tax_loca=VALUES(ship_from_tax_loca),ship_to_city=VALUES(ship_to_city),ship_to_state=VALUES(ship_to_state),ship_to_country=VALUES(ship_to_country),ship_to_postal=VALUES(ship_to_postal),ship_to_tax_loca=VALUES(ship_to_tax_loca),user_id=VALUES(user_id);";
              $this->db->query($qi);
			 // print_r($qi);
              unset($bulk_data);
              unset($quer);
           }
           $i++;
    }//while ends here
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT INTO `rep_sc_vat_tax` (market_id,merchant_id,ord_date,trans_type,order_id,ship_date,ship_id,trans_id,asin,sku,qty,tax_cal_date,tax_rate,tax_code,currency,tax_type,tax_cal_rsn_code,tax_addr_role,juri_level,juri_country,our_price_tax_inclusive,our_price_tax,our_price_tax_exclusive,our_promo_amount_inclusive,our_promo_amount,our_promo_amount_exclusive,ship_tax_inclusive,ship_tax,ship_tax_exclusive,ship_tax_promo_inclusive,ship_tax_promo,ship_tax_promo_exclusive,gift_tax_inclusive,gift_tax,gift_tax_exclusive,gift_tax_promo_inclusive,gift_tax_promo,gift_tax_promo_exclusive,sell_tax_reg,sell_tax_reg_jud,buy_tax_reg,buy_tax_reg_jud,buy_tax_reg_type,inv_curr_code,inv_ex_rate,inv_ex_date,con_tax_amt,vat_inv_no,inv_url,exp_out_eu,ship_from_city,ship_from_state,ship_from_country,ship_from_postal,ship_from_tax_loca,ship_to_city,ship_to_state,ship_to_country,ship_to_postal,ship_to_tax_loca,user_id)VALUES
              $quer
              ON DUPLICATE KEY
             UPDATE
              market_id=VALUES(market_id),merchant_id=VALUES(merchant_id),ord_date=VALUES(ord_date),trans_type=VALUES(trans_type),order_id=VALUES(order_id),ship_date=VALUES(ship_date),ship_id=VALUES(ship_id),trans_id=VALUES(trans_id),asin=VALUES(asin),sku=VALUES(sku),qty=VALUES(qty),tax_cal_date=VALUES(tax_cal_date),tax_rate=VALUES(tax_rate),
			  tax_code=VALUES(tax_code),currency=VALUES(currency),tax_type=VALUES(tax_type),tax_cal_rsn_code=VALUES(tax_cal_rsn_code),tax_addr_role=VALUES(tax_addr_role),juri_level=VALUES(juri_level),juri_country=VALUES(juri_country),our_price_tax_inclusive=VALUES(our_price_tax_inclusive),our_price_tax=VALUES(our_price_tax),our_price_tax_exclusive=VALUES(our_price_tax_exclusive),our_promo_amount_inclusive=VALUES(our_promo_amount_inclusive),
			  our_promo_amount=VALUES(our_promo_amount),our_promo_amount_exclusive=VALUES(our_promo_amount_exclusive),ship_tax_inclusive=VALUES(ship_tax_inclusive),ship_tax=VALUES(ship_tax),ship_tax_exclusive=VALUES(ship_tax_exclusive),ship_tax_promo_inclusive=VALUES(ship_tax_promo_inclusive), ship_tax_promo=VALUES(ship_tax_promo),ship_tax_promo_exclusive=VALUES(ship_tax_promo_exclusive),gift_tax_inclusive=VALUES(gift_tax_inclusive),
			  gift_tax=VALUES(gift_tax),gift_tax_exclusive=VALUES(gift_tax_exclusive),gift_tax_promo_inclusive=VALUES(gift_tax_promo_inclusive),gift_tax_promo=VALUES(gift_tax_promo),gift_tax_promo_exclusive=VALUES(gift_tax_promo_exclusive),sell_tax_reg=VALUES(sell_tax_reg),sell_tax_reg_jud=VALUES(sell_tax_reg_jud),buy_tax_reg=VALUES(buy_tax_reg),buy_tax_reg_jud=VALUES(buy_tax_reg_jud),buy_tax_reg_type=VALUES(buy_tax_reg_type),inv_curr_code=VALUES(inv_curr_code),inv_ex_rate=VALUES(inv_ex_rate),
			  inv_ex_date=VALUES(inv_ex_date),con_tax_amt=VALUES(con_tax_amt),vat_inv_no=VALUES(vat_inv_no),inv_url=VALUES(inv_url),exp_out_eu=VALUES(exp_out_eu),ship_from_city=VALUES(ship_from_city),ship_from_state=VALUES(ship_from_state),ship_from_country=VALUES(ship_from_country),ship_from_postal=VALUES(ship_from_postal),ship_from_tax_loca=VALUES(ship_from_tax_loca),ship_to_city=VALUES(ship_to_city),ship_to_state=VALUES(ship_to_state),ship_to_country=VALUES(ship_to_country),ship_to_postal=VALUES(ship_to_postal),ship_to_tax_loca=VALUES(ship_to_tax_loca),user_id=VALUES(user_id);";
              $this->db->query($qi);
			 // print_r($qi);
          unset($bulk_data);
          unset($quer);
        }
      fclose($fp);

      }
  }
  public function process_order_data_by_date($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        //print_r($buffer);
        if($i>=1 && !empty($buffer[0]) )
        {

              $order_id= isset($buffer[0])?$this->db->escape($buffer[0]):'';
			   $po_date= isset($buffer[2])?$this->db->escape($buffer[2]):'';
               $last_update= isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $order_status= isset($buffer[4])?$buffer[4]:'';
            if($order_status=='Cancelled')
            {
              $order_status='Canceled';
            }
              $order_status=!empty($order_status)?$this->db->escape($order_status):'';
              $fullfill=isset($buffer[5])?$this->db->escape($buffer[5]):'';
			 $sale_channel= $buffer[6];
            $cnt=explode('.',$sale_channel);
            if(count($cnt) > 1)
            {
             $contry=$cnt[count($cnt)-1];
             $country2=strtoupper($contry);
			 $cont2=str_replace('COM','US',(string)$country2);
            }

             $sale_channel= isset($buffer[47])?$this->db->escape($buffer[6]):'';

               $ship_service=isset($buffer[9])?$this->db->escape($buffer[9]):'';
			   $title=isset($buffer[10])?$this->db->escape($buffer[10]):'';
			   $sku=isset($buffer[11])?$this->db->escape($buffer[11]):'';
               $asin=isset($buffer[12])?$this->db->escape($buffer[12]):'';
			   $itm_status=isset($buffer[13])?$this->db->escape($buffer[13]):'';
			   $qty=isset($buffer[14])?$this->db->escape($buffer[14]):'';
			   $curr=isset($buffer[15])?$this->db->escape($buffer[15]):'';
			   $itm_price=isset($buffer[16])?$this->db->escape($buffer[16]):'';
			   $itm_tax=isset($buffer[17])?$this->db->escape($buffer[17]):'';
			   $ship_price=isset($buffer[18])?$this->db->escape($buffer[18]):'';
			   $ship_tax=isset($buffer[19])?$this->db->escape($buffer[19]):'';
               $gift_price=isset($buffer[20])?$this->db->escape($buffer[20]):'';
               $gift_tax=isset($buffer[21])?$this->db->escape($buffer[21]):'';
			   $promo_disc=isset($buffer[22])?$this->db->escape($buffer[22]):'';
			   $ship_disc=isset($buffer[23])?$this->db->escape($buffer[23]):'';
			   $ship_city=isset($buffer[24])?$this->db->escape($buffer[24]):'';
			   $ship_state=isset($buffer[25])?$this->db->escape($buffer[25]):'';
			   $ship_post=isset($buffer[26])?$this->db->escape($buffer[26]):'';
			   $ship_country=isset($buffer[27])?$this->db->escape($buffer[27]):'';
			   $promo_id=isset($buffer[28])?$this->db->escape($buffer[28]):'';



     $bulk_data[]="(".$order_id.",".$po_date.",".$last_update.",".$order_status.",".$fullfill.",'".$country2."',".$sku.",".$asin.",".$itm_status.",".$gift_price.",".$title.",".$ship_service.",".$qty.",".$curr.",".$itm_price.",".$itm_tax.",".$ship_price.",".$ship_tax.",".$gift_tax.",".$promo_disc.",".$ship_disc.",".$ship_city.",".$ship_state.",".$ship_post.",".$ship_country.",".$promo_id.",".$user_id.")";

        }

        if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_orders_data_order_date_list`(order_id,po_date,last_update_date,ord_status,fulfillment,sales_channel,ord_sku,asin,itm_status,gift_price,title,ship_service,qty,currency,itm_price,itm_tax,ship_price,ship_tax,gift_tax,itm_promo_discount,itm_ship_discount,ship_city,ship_state,ship_post,ship_country,promo_id,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              order_id=VALUES(order_id),po_date=VALUES(po_date),last_update_date=VALUES(last_update_date),ord_status=VALUES(ord_status),fulfillment=VALUES(fulfillment),sales_channel=VALUES(sales_channel),ord_sku=VALUES(ord_sku),asin=VALUES(asin),itm_status=VALUES(itm_status),gift_price=VALUES(gift_price),title=VALUES(title),ship_service=VALUES(ship_service),currency=VALUES(currency),itm_price=VALUES(itm_price),itm_tax=VALUES(itm_tax),ship_price=VALUES(ship_price),ship_tax=VALUES(ship_tax),gift_tax=VALUES(gift_tax),itm_promo_discount=VALUES(itm_promo_discount),itm_ship_discount=VALUES(itm_ship_discount),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_country=VALUES(ship_country),promo_id=VALUES(promo_id),user_id=VALUES(user_id);";
              $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_orders_data_order_date_list`(order_id,po_date,last_update_date,ord_status,fulfillment,sales_channel,ord_sku,asin,itm_status,gift_price,title,ship_service,qty,currency,itm_price,itm_tax,ship_price,ship_tax,gift_tax,itm_promo_discount,itm_ship_discount,ship_city,ship_state,ship_post,ship_country,promo_id,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              order_id=VALUES(order_id),po_date=VALUES(po_date),last_update_date=VALUES(last_update_date),ord_status=VALUES(ord_status),fulfillment=VALUES(fulfillment),sales_channel=VALUES(sales_channel),ord_sku=VALUES(ord_sku),asin=VALUES(asin),itm_status=VALUES(itm_status),gift_price=VALUES(gift_price),title=VALUES(title),ship_service=VALUES(ship_service),currency=VALUES(currency),itm_price=VALUES(itm_price),itm_tax=VALUES(itm_tax),ship_price=VALUES(ship_price),ship_tax=VALUES(ship_tax),gift_tax=VALUES(gift_tax),itm_promo_discount=VALUES(itm_promo_discount),itm_ship_discount=VALUES(itm_ship_discount),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_country=VALUES(ship_country),promo_id=VALUES(promo_id),user_id=VALUES(user_id);";
              $this->db->query($qi);
	          unset($bulk_data);
              unset($quer);
        }
     fclose($fp);
    }
  }
  public function process_converged_order_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i>=1 && !empty($buffer[1]) )
        {
			 $pay_status=isset($buffer[0])?$this->db->escape($buffer[0]):'';
             $amz_order_id=isset($buffer[1])?$this->db->escape($buffer[1]):'';
			 $amz_order_item_id=isset($buffer[2])?$this->db->escape($buffer[2]):'';
			 $pay_date=isset($buffer[3])?$this->db->escape($buffer[3]):'';
			 $pay_id=isset($buffer[4])?$this->db->escape($buffer[4]):'';
			 $itm_name=isset($buffer[5])?$this->db->escape($buffer[5]):'';
			 $list_id=isset($buffer[6])?$this->db->escape($buffer[6]):'';
			 $sku=isset($buffer[7])?$this->db->escape($buffer[7]):'';
			 $price=isset($buffer[8])?$this->db->escape($buffer[8]):'';
			 $ship_price=isset($buffer[9])?$this->db->escape($buffer[9]):'';
			 $qty=isset($buffer[10])?$this->db->escape($buffer[10]):'';
             $order_total=isset($buffer[11])?$this->db->escape($buffer[11]):'';
             $po_date=isset($buffer[12])?$this->db->escape($buffer[12]):'';
             $buyer_email=isset($buffer[14])?$this->db->escape($buffer[14]):'';
             $buyer_name=isset($buffer[15])?$this->db->escape($buffer[15]):'';
			 $recipient=isset($buffer[16])?$this->db->escape($buffer[16]):'';
             $ship_addr1=isset($buffer[17])?$this->db->escape($buffer[17]):'';
			 $ship_addr2=isset($buffer[18])?$this->db->escape($buffer[18]):'';
             $ship_city=isset($buffer[19])?$this->db->escape($buffer[19]):'';
             $ship_state=isset($buffer[20])?$this->db->escape($buffer[20]):'';
             $ship_zip=isset($buffer[21])?$this->db->escape($buffer[21]):'';
             $ship_country=isset($buffer[22])?$this->db->escape($buffer[22]):'';
             $country_code=$this->db->escape($country);
             $bulk_data[]="(".$pay_status.",".$amz_order_id.",".$amz_order_item_id.",".$pay_date.",".$pay_id.",".$itm_name.",".$list_id.",".$sku.",".$price.",".$ship_price.",".$qty.",".$order_total.",".$po_date.",".$buyer_name.",".$buyer_email.",".$recipient.",".$ship_addr1.",".$ship_addr2.",".$ship_city.",".$ship_state.",".$ship_zip.",".$ship_country.",'".$country."',".$user_id.")";

        }

        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          // print_r($bulk_data);
          $quer=implode(',',$bulk_data);
          $qi="INSERT INTO `rep_converger_orders_data_list` (pay_status,ord_id,ord_itm_id,pay_date,pay_id,itm_name,list_id,sku,price,ship_price,qty,order_total,po_date,buyer_name,buyer_email,recipient,ship_addr1,ship_addr2,ship_city,ship_state,ship_zip,ship_country,country,user_id)VALUES
          $quer
          ON DUPLICATE KEY
          UPDATE
          pay_status=VALUES(pay_status),ord_id=VALUES(ord_id),ord_itm_id=VALUES(ord_itm_id),pay_date=values(pay_date),pay_id=values(pay_id),itm_name=values(itm_name),list_id=values(list_id),sku=values(sku),price=values(price),ship_price=values(ship_price),qty=VALUES(qty),order_total=values(order_total),po_date=values(po_date),price=values(price),buyer_name=values(buyer_name),buyer_email=VALUES(buyer_email),recipient=values(recipient),ship_addr1=VALUES(ship_addr1),ship_addr2=values(ship_addr2),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_zip=VALUES(ship_zip),ship_country=VALUES(ship_country),country=VALUES(country),user_id=VALUES(user_id);";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {
          // print_r($bulk_data);
          $quer=implode(',',$bulk_data);
          $qi="INSERT INTO `rep_converger_orders_data_list` (pay_status,ord_id,ord_itm_id,pay_date,pay_id,itm_name,list_id,sku,price,ship_price,qty,order_total,po_date,buyer_name,buyer_email,recipient,ship_addr1,ship_addr2,ship_city,ship_state,ship_zip,ship_country,country,user_id)VALUES
          $quer
          ON DUPLICATE KEY
          UPDATE
          pay_status=VALUES(pay_status),ord_id=VALUES(ord_id),ord_itm_id=VALUES(ord_itm_id),pay_date=values(pay_date),pay_id=values(pay_id),itm_name=values(itm_name),list_id=values(list_id),sku=values(sku),price=values(price),ship_price=values(ship_price),qty=VALUES(qty),order_total=values(order_total),po_date=values(po_date),price=values(price),buyer_name=values(buyer_name),buyer_email=VALUES(buyer_email),recipient=values(recipient),ship_addr1=VALUES(ship_addr1),ship_addr2=values(ship_addr2),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_zip=VALUES(ship_zip),ship_country=VALUES(ship_country),country=VALUES(country),user_id=VALUES(user_id);";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
     }
     fclose($fp);
    }
  }
  public function process_fba_shipments_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        // print_r($buffer);
        if($i>=1 && !empty($buffer[0]) )
        {
          if(empty($buffer[1]))
          {
             $amz_order_id= isset($buffer[0])?$this->db->escape($buffer[0]):'';
			 $mer_order_id= isset($buffer[1])?$this->db->escape($buffer[1]):'';
			 $ship_id= isset($buffer[2])?$this->db->escape($buffer[2]):'';
			 $ship_item_id= isset($buffer[3])?$this->db->escape($buffer[3]):'';
			 $amz_order_item_id= isset($buffer[4])?$this->db->escape($buffer[4]):'';
			 $po_date=isset($buffer[6])?$this->db->escape($buffer[6]):'';
             $pay_date= isset($buffer[7])?$this->db->escape($buffer[7]):'';
             $ship_date= isset($buffer[8])?$this->db->escape($buffer[8]):'';
			 $report_date= isset($buffer[9])?$this->db->escape($buffer[9]):'';
             $buyer_email=isset($buffer[10])?$this->db->escape($buffer[10]):'';
             $buyer_name=isset($buffer[11])?$this->db->escape($buffer[11]):'';
			 $sku=isset($buffer[13])?$this->db->escape($buffer[13]):'';
			 $title=isset($buffer[14])?$this->db->escape($buffer[14]):'';
			 $qty=isset($buffer[15])?$this->db->escape($buffer[15]):'';
			 $currency=isset($buffer[16])?$this->db->escape($buffer[16]):'';
			 $itm_price=isset($buffer[17])?$this->db->escape($buffer[17]):'';
			 $itm_tax=isset($buffer[18])?$this->db->escape($buffer[18]):'';
			 $ship_price=isset($buffer[19])?$this->db->escape($buffer[19]):'';
			 $ship_tax=isset($buffer[20])?$this->db->escape($buffer[20]):'';
			 $gift_wrap_price=isset($buffer[21])?$this->db->escape($buffer[21]):'';
			 $gift_wrap_tax=isset($buffer[22])?$this->db->escape($buffer[22]):'';
             $ship_addr1=isset($buffer[25])?$this->db->escape($buffer[25]):'';
			 $ship_addr2=isset($buffer[26])?$this->db->escape($buffer[26]):'';
			 $ship_addr3=isset($buffer[27])?$this->db->escape($buffer[27]):'';
             $ship_city=isset($buffer[28])?$this->db->escape($buffer[28]):'';
             $ship_state=isset($buffer[29])?$this->db->escape($buffer[29]):'';
             $ship_zip=isset($buffer[30])?$this->db->escape($buffer[30]):'';
             $ship_country=isset($buffer[31])?$this->db->escape($buffer[31]):'';
             $track_no= isset($buffer[43])?$this->db->escape($buffer[43]):'';
             $esp_deliv_date= isset($buffer[44])?$this->db->escape($buffer[44]):'';
			 $fullfill= isset($buffer[46])?$this->db->escape($buffer[46]):'';
             $sale_channel= $buffer[47];
             $cnt=explode('.',$sale_channel);
             if(count($cnt) > 1)
             {
              $contry=$cnt[count($cnt)-1];
              $country=strtoupper($contry);
             }
             $sale_channel= isset($buffer[47])?$this->db->escape($buffer[47]):'';
             $bulk_data[]="(".$amz_order_id.",".$mer_order_id.",".$ship_id.",".$ship_item_id.",".$amz_order_item_id.",".$pay_date.",".$ship_date.",".$report_date.",".$track_no.",".$esp_deliv_date.",'".$country."',".$user_id.",'".$country."',".$po_date.",".$buyer_name.",".$buyer_email.",".$ship_addr1.",".$ship_addr2.",".$ship_addr3.",".$ship_city.",".$ship_state.",".$ship_zip.",".$ship_country.",".$sku.",".$title.",".$qty.",".$currency.",".$itm_price.",".$itm_tax.",".$ship_price.",".$ship_tax.",".$gift_wrap_price.",".$gift_wrap_tax.",".$fullfill.")";
          }
        }

        if(isset($bulk_data) && count($bulk_data)>=500)
        {
         // print_r($bulk_data);
          $quer=implode(',',$bulk_data);
          $qi="INSERT INTO `rep_amz_fullfill_list` (order_no,mer_order_no,ship_id,ship_itm_id,order_item_no,payment_date,calc_shipdate,report_date,tracking_number,calc_deliverydate,sales_channel,customer_id,sales_country,purchase_date,buyer_name,buyer_email,shipping_addr1,shipping_addr2,shipping_addr3,shipping_city,shipping_state,shipping_zip,shipping_country,ord_sku,ord_title,ord_qty,ord_curr,ord_itm_price,ord_itm_tax,ord_ship_price,ord_ship_tax,ord_gift_price,ord_gift_tax,ord_fullfill)VALUES
          $quer
          ON DUPLICATE KEY
          UPDATE
          order_no=VALUES(order_no),mer_order_no=VALUES(mer_order_no),ship_id=VALUES(ship_id),ship_itm_id=VALUES(ship_itm_id),order_item_no=VALUES(order_item_no),payment_date=VALUES(payment_date),calc_shipdate=VALUES(calc_shipdate),report_date=VALUES(report_date),tracking_number=VALUES(tracking_number),calc_deliverydate=VALUES(calc_deliverydate),sales_channel=VALUES(sales_channel),customer_id=VALUES(customer_id),sales_country=VALUES(sales_country),purchase_date=VALUES(purchase_date),buyer_name=values(buyer_name),buyer_email=values(buyer_email),shipping_addr1=values(shipping_addr1),shipping_addr2=values(shipping_addr2),shipping_addr3=values(shipping_addr3),shipping_city=values(shipping_city),shipping_state=values(shipping_state),shipping_zip=values(shipping_zip),shipping_country=values(shipping_country),ord_sku=values(ord_sku),ord_title=values(ord_title),ord_qty=values(ord_qty),ord_curr=values(ord_curr),ord_curr=values(ord_curr),ord_itm_price=values(ord_itm_price),ord_itm_tax=values(ord_itm_tax),ord_ship_price=values(ord_ship_price),ord_ship_tax=values(ord_ship_tax),ord_gift_price=values(ord_gift_price),ord_gift_tax=values(ord_gift_tax),ord_fullfill=values(ord_fullfill);";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {
         // print_r($bulk_data);
          $quer=implode(',',$bulk_data);
          $qi="INSERT INTO `rep_amz_fullfill_list` (order_no,mer_order_no,ship_id,ship_itm_id,order_item_no,payment_date,calc_shipdate,report_date,tracking_number,calc_deliverydate,sales_channel,customer_id,sales_country,purchase_date,buyer_name,buyer_email,shipping_addr1,shipping_addr2,shipping_addr3,shipping_city,shipping_state,shipping_zip,shipping_country,ord_sku,ord_title,ord_qty,ord_curr,ord_itm_price,ord_itm_tax,ord_ship_price,ord_ship_tax,ord_gift_price,ord_gift_tax,ord_fullfill)VALUES
          $quer
          ON DUPLICATE KEY
          UPDATE
          order_no=VALUES(order_no),mer_order_no=VALUES(mer_order_no),ship_id=VALUES(ship_id),ship_itm_id=VALUES(ship_itm_id),order_item_no=VALUES(order_item_no),payment_date=VALUES(payment_date),calc_shipdate=VALUES(calc_shipdate),report_date=VALUES(report_date),tracking_number=VALUES(tracking_number),calc_deliverydate=VALUES(calc_deliverydate),sales_channel=VALUES(sales_channel),customer_id=VALUES(customer_id),sales_country=VALUES(sales_country),purchase_date=VALUES(purchase_date),buyer_name=values(buyer_name),buyer_email=values(buyer_email),shipping_addr1=values(shipping_addr1),shipping_addr2=values(shipping_addr2),shipping_addr3=values(shipping_addr3),shipping_city=values(shipping_city),shipping_state=values(shipping_state),shipping_zip=values(shipping_zip),shipping_country=values(shipping_country),ord_sku=values(ord_sku),ord_title=values(ord_title),ord_qty=values(ord_qty),ord_curr=values(ord_curr),ord_curr=values(ord_curr),ord_itm_price=values(ord_itm_price),ord_itm_tax=values(ord_itm_tax),ord_ship_price=values(ord_ship_price),ord_ship_tax=values(ord_ship_tax),ord_gift_price=values(ord_gift_price),ord_gift_tax=values(ord_gift_tax),ord_fullfill=values(ord_fullfill);";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
     }
     fclose($fp);
    }
  }
  public function process_fba_fulfill_ship_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i >= 1 && !empty($buffer[0]))
        {
          $ship_date= isset($buffer[0])?$buffer[0]:'';
          $sku= isset($buffer[1])?$this->db->escape($buffer[1]):'';
          $fnsku= isset($buffer[2])?$this->db->escape($buffer[2]):'';
          $asin= isset($buffer[3])?$this->db->escape($buffer[3]):'';
          $fullfill_center_id= isset($buffer[4])?$this->db->escape($buffer[4]):'';
          $qty= isset($buffer[5])?$this->db->escape($buffer[5]):'';
          $amazon_order_id= isset($buffer[6])?$this->db->escape($buffer[6]):'';
          $currency= isset($buffer[7])?$this->db->escape($buffer[7]):'';
          $itm_price= isset($buffer[8])?$this->db->escape($buffer[8]):'';
	      $ship_price= isset($buffer[9])?$this->db->escape($buffer[9]):'';
		  $gift_price= isset($buffer[10])?$this->db->escape($buffer[10]):'';
		  $ship_city= isset($buffer[11])?$this->db->escape($buffer[11]):'';
		  $ship_state= isset($buffer[12])?$this->db->escape($buffer[12]):'';
		  $ship_post= isset($buffer[13])?$this->db->escape($buffer[13]):'';
          //$country_code=$this->db->escape($country);
          $bulk_data[]="('".$ship_date."',".$sku.",".$fnsku.",".$asin.",".$fullfill_center_id.",".$qty.",".$amazon_order_id.",".$currency.",".$itm_price.",".$ship_price.",".$gift_price.",".$ship_city.",".$ship_state.",".$ship_post.",".$user_id.")";
       }


        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT IGNORE INTO `rep_fba_fulfill_ship_data_list`(ship_date,sku,fnsku,asin,fullfill_id,qty,amz_order_id,currency,itm_price,ship_price,gift_price,ship_city,ship_state,ship_post,added_by)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
          ship_date=VALUES(ship_date),sku=VALUES(sku),fnsku=VALUES(fnsku),asin=VALUES(asin),fullfill_id=VALUES(fullfill_id),qty=VALUES(qty),amz_order_id=VALUES(amz_order_id),currency=VALUES(currency),itm_price=VALUES(itm_price),ship_price=VALUES(ship_price),gift_price=VALUES(gift_price),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_post=VALUES(ship_post),added_by=VALUES(added_by)";

          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {

          $quer=implode(',',$bulk_data);
          $qi="INSERT IGNORE INTO `rep_fba_fulfill_ship_data_list`(ship_date,sku,fnsku,asin,fullfill_id,qty,amz_order_id,currency,itm_price,ship_price,gift_price,ship_city,ship_state,ship_post,added_by)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
          ship_date=VALUES(ship_date),sku=VALUES(sku),fnsku=VALUES(fnsku),asin=VALUES(asin),fullfill_id=VALUES(fullfill_id),qty=VALUES(qty),amz_order_id=VALUES(amz_order_id),currency=VALUES(currency),itm_price=VALUES(itm_price),ship_price=VALUES(ship_price),gift_price=VALUES(gift_price),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_post=VALUES(ship_post),added_by=VALUES(added_by)";

          $this->db->query($qi);
        unset($bulk_data);
        unset($quer);
     }
     fclose($fp);
    }
  }

   public function process_actionable_order_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i >= 1 && !empty($buffer[0]))
        {
          $ord_id= isset($buffer[0])?$buffer[0]:'';
          $ord_itm_id= isset($buffer[1])?$this->db->escape($buffer[1]):'';
          $po_date= isset($buffer[2])?$this->db->escape($buffer[2]):'';
          $pay_date= isset($buffer[3])?$this->db->escape($buffer[3]):'';
          $rep_date= isset($buffer[4])?$this->db->escape($buffer[4]):'';
          $prom_date= isset($buffer[5])?$this->db->escape($buffer[5]):'';
          $day_past= isset($buffer[6])?$this->db->escape($buffer[6]):'';
          $buyer_email= isset($buffer[7])?$this->db->escape($buffer[7]):'';
          $buyer_name= isset($buffer[8])?$this->db->escape($buffer[8]):'';
	      $buyer_phone= isset($buffer[9])?$this->db->escape($buffer[9]):'';
		  $sku= isset($buffer[10])?$this->db->escape($buffer[10]):'';
		  $prod_name= isset($buffer[11])?$this->db->escape($buffer[11]):'';
		  $qty_pur= isset($buffer[12])?$this->db->escape($buffer[12]):'';
		  $qty_ship= isset($buffer[13])?$this->db->escape($buffer[13]):'';
		  $qty_unship= isset($buffer[14])?$this->db->escape($buffer[14]):'';
		  $ship_service= isset($buffer[15])?$this->db->escape($buffer[15]):'';
		  $ship_addr1= isset($buffer[17])?$this->db->escape($buffer[17]):'';
		  $ship_addr2= isset($buffer[18])?$this->db->escape($buffer[18]):'';
		  $ship_addr3= isset($buffer[19])?$this->db->escape($buffer[19]):'';
		  $ship_city= isset($buffer[20])?$this->db->escape($buffer[20]):'';
		  $ship_state= isset($buffer[21])?$this->db->escape($buffer[21]):'';
		  $ship_post= isset($buffer[22])?$this->db->escape($buffer[22]):'';
		  $ship_country= isset($buffer[23])?$this->db->escape($buffer[23]):'';
		  $is_buss_order= isset($buffer[24])?$this->db->escape($buffer[24]):'';

          //$country_code=$this->db->escape($country);
          $bulk_data[]="('".$ord_id."',".$ord_itm_id.",".$po_date.",".$pay_date.",".$rep_date.",".$prom_date.",".$day_past.",".$buyer_email.",".$buyer_name.",".$buyer_phone.",".$sku.",".$prod_name.",".$qty_pur.",".$qty_ship.",".$qty_unship.",".$ship_service.",".$ship_addr1.",".$ship_addr2.",".$ship_addr3.",".$ship_city.",".$ship_state.",".$ship_post.",".$ship_country.",".$is_buss_order.",'".$country."',".$user_id.")";
       }


        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT IGNORE INTO `rep_actionable_order_data_list`(order_id,order_item_id,po_date,pay_date,rep_date,prom_date,day_past,buy_email,buy_name,buy_phone,sku,prod_name,qty_pur,qty_ship,qty_unship,ship_ser,ship_addr1,ship_addr2,ship_addr3,ship_city,ship_state,ship_post,ship_country,buss_order,country,usr_id)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
          order_id=VALUES(order_id),order_item_id=VALUES(order_item_id),po_date=VALUES(po_date),pay_date=VALUES(pay_date),rep_date=VALUES(rep_date),prom_date=VALUES(prom_date),day_past=VALUES(day_past),buy_email=VALUES(buy_email),buy_name=VALUES(buy_name),buy_phone=VALUES(buy_phone),sku=VALUES(sku),prod_name=VALUES(prod_name),qty_pur=VALUES(qty_pur),qty_ship=VALUES(qty_ship),qty_unship=VALUES(qty_unship),ship_ser=VALUES(ship_ser),ship_addr1=VALUES(ship_addr1),ship_addr2=VALUES(ship_addr2),ship_addr3=VALUES(ship_addr3),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_country=VALUES(ship_country),buss_order=VALUES(buss_order),country=VALUES(country),usr_id=VALUES(usr_id)";

          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {

          $quer=implode(',',$bulk_data);
           $qi="INSERT IGNORE INTO `rep_actionable_order_data_list`(order_id,order_item_id,po_date,pay_date,rep_date,prom_date,day_past,buy_email,buy_name,buy_phone,sku,prod_name,qty_pur,qty_ship,qty_unship,ship_ser,ship_addr1,ship_addr2,ship_addr3,ship_city,ship_state,ship_post,ship_country,buss_order,country,usr_id)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
          order_id=VALUES(order_id),order_item_id=VALUES(order_item_id),po_date=VALUES(po_date),pay_date=VALUES(pay_date),rep_date=VALUES(rep_date),prom_date=VALUES(prom_date),day_past=VALUES(day_past),buy_email=VALUES(buy_email),buy_name=VALUES(buy_name),buy_phone=VALUES(buy_phone),sku=VALUES(sku),prod_name=VALUES(prod_name),qty_pur=VALUES(qty_pur),qty_ship=VALUES(qty_ship),qty_unship=VALUES(qty_unship),ship_ser=VALUES(ship_ser),ship_addr1=VALUES(ship_addr1),ship_addr2=VALUES(ship_addr2),ship_addr3=VALUES(ship_addr3),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_country=VALUES(ship_country),buss_order=VALUES(buss_order),country=VALUES(country),usr_id=VALUES(usr_id)";

          $this->db->query($qi);
        unset($bulk_data);
        unset($quer);
     }
     fclose($fp);
    }
  }
public function process_flat_order_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i >= 1 && !empty($buffer[0]))
        {
         $ord_id= isset($buffer[0])?$this->db->escape($buffer[0]):'';
               $ord_itm_id= isset($buffer[1])?$this->db->escape($buffer[1]):'';
               $po_date=isset($buffer[2])?$this->db->escape($buffer[2]):'';
               $payment_date=isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $buy_email=isset($buffer[4])?$this->db->escape($buffer[4]):'';
               $buy_name=isset($buffer[5])?$this->db->escape($buffer[5]):'';
			   $buy_phone=isset($buffer[6])?$this->db->escape($buffer[6]):'';
               $sku=isset($buffer[7])?$this->db->escape($buffer[7]):'';
			   $title=isset($buffer[8])?$this->db->escape($buffer[8]):'';
			   $qty=isset($buffer[9])?$this->db->escape($buffer[9]):'';
			   $currency=isset($buffer[10])?$this->db->escape($buffer[10]):'';
			   $itm_price=isset($buffer[11])?$this->db->escape($buffer[11]):'';
			   $itm_tax=isset($buffer[12])?$this->db->escape($buffer[12]):'';
			   $ship_price=isset($buffer[13])?$this->db->escape($buffer[13]):'';
			   $ship_tax=isset($buffer[14])?$this->db->escape($buffer[14]):'';
			   $ship_service=isset($buffer[15])?$this->db->escape($buffer[15]):'';
			   $recipient=isset($buffer[16])?$this->db->escape($buffer[16]):'';
			   $ship_addr1=isset($buffer[17])?$this->db->escape($buffer[17]):'';
			   $ship_addr2=isset($buffer[18])?$this->db->escape($buffer[18]):'';
			   $ship_addr3=isset($buffer[19])?$this->db->escape($buffer[19]):'';
			   $ship_city=isset($buffer[20])?$this->db->escape($buffer[20]):'';
			   $ship_state=isset($buffer[21])?$this->db->escape($buffer[21]):'';
			   $ship_post=isset($buffer[22])?$this->db->escape($buffer[22]):'';
			   $ship_country=isset($buffer[23])?$this->db->escape($buffer[23]):'';
			   $ship_phone=isset($buffer[24])?$this->db->escape($buffer[24]):'';
			   $itm_disc=isset($buffer[25])?$this->db->escape($buffer[25]):'';
			   $itm_id=isset($buffer[26])?$this->db->escape($buffer[26]):'';
			   $ship_disc=isset($buffer[27])?$this->db->escape($buffer[27]):'';
			   $ship_id=isset($buffer[28])?$this->db->escape($buffer[28]):'';
			   $del_start=isset($buffer[29])?$this->db->escape($buffer[29]):'';
			   $del_end=isset($buffer[30])?$this->db->escape($buffer[30]):'';
               $country_code=$this->db->escape($country);


               $bulk_data[]="(".$ord_id.",".$ord_itm_id.",".$po_date.",".$payment_date.",".$buy_email.",".$buy_name.",".$buy_phone.",".$sku.",".$title.",".$qty.",".$currency.",".$itm_price.",".$itm_tax.",".$ship_price.",".$ship_tax.",".$ship_service.",".$recipient.",".$ship_addr1.",".$ship_addr2.",".$ship_addr3.",".$ship_city.",".$ship_state.",".$ship_post.",".$ship_country.",".$ship_phone.",".$itm_disc.",".$itm_id.",".$ship_disc.",".$ship_id.",".$del_start.",".$del_end.",'".$country."',".$user_id.")";

           }



            if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_flat_orders_list` (order_id,order_item_id,po_date,pay_date,buy_email,buy_name,buy_phone,sku,title,qty,currency,itm_price,itm_tax,ship_price,ship_tax,ship_service,recipient,ship_addr1,ship_addr2,ship_addr3,ship_city,ship_state,ship_post,ship_country,ship_phone,itm_promo_discount,itm_promo_id,ship_promo_discount,ship_promo_id,del_start_date,del_end_date,country,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              order_id=VALUES(order_id),order_item_id=VALUES(order_item_id),po_date=VALUES(po_date),pay_date=VALUES(pay_date),buy_email=VALUES(buy_email),buy_name=VALUES(buy_name),buy_phone=VALUES(buy_phone),sku=VALUES(sku),title=VALUES(title),qty=VALUES(qty),currency=VALUES(currency),itm_price=VALUES(itm_price),itm_tax=VALUES(itm_tax),ship_price=VALUES(ship_price),ship_tax=VALUES(ship_tax),ship_service=VALUES(ship_service),recipient=VALUES(recipient),ship_addr1=VALUES(ship_addr1),ship_addr2=VALUES(ship_addr2),ship_addr3=VALUES(ship_addr3),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_country=VALUES(ship_country),ship_phone=VALUES(ship_phone),itm_promo_discount=VALUES(itm_promo_discount),itm_promo_id=VALUES(itm_promo_id),ship_promo_discount=VALUES(ship_promo_discount),ship_promo_id=VALUES(ship_promo_id),del_start_date=VALUES(del_start_date),del_end_date=VALUES(del_end_date),country=VALUES(country),user_id=VALUES(user_id);";
              $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {

              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_flat_orders_list` (order_id,order_item_id,po_date,pay_date,buy_email,buy_name,buy_phone,sku,title,qty,currency,itm_price,itm_tax,ship_price,ship_tax,ship_service,recipient,ship_addr1,ship_addr2,ship_addr3,ship_city,ship_state,ship_post,ship_country,ship_phone,itm_promo_discount,itm_promo_id,ship_promo_discount,ship_promo_id,del_start_date,del_end_date,country,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              order_id=VALUES(order_id),order_item_id=VALUES(order_item_id),po_date=VALUES(po_date),pay_date=VALUES(pay_date),buy_email=VALUES(buy_email),buy_name=VALUES(buy_name),buy_phone=VALUES(buy_phone),sku=VALUES(sku),title=VALUES(title),qty=VALUES(qty),currency=VALUES(currency),itm_price=VALUES(itm_price),itm_tax=VALUES(itm_tax),ship_price=VALUES(ship_price),ship_tax=VALUES(ship_tax),ship_service=VALUES(ship_service),recipient=VALUES(recipient),ship_addr1=VALUES(ship_addr1),ship_addr2=VALUES(ship_addr2),ship_addr3=VALUES(ship_addr3),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_post=VALUES(ship_post),ship_country=VALUES(ship_country),ship_phone=VALUES(ship_phone),itm_promo_discount=VALUES(itm_promo_discount),itm_promo_id=VALUES(itm_promo_id),ship_promo_discount=VALUES(ship_promo_discount),ship_promo_id=VALUES(ship_promo_id),del_start_date=VALUES(del_start_date),del_end_date=VALUES(del_end_date),country=VALUES(country),user_id=VALUES(user_id);";
              $this->db->query($qi);
              unset($bulk_data);
              unset($quer);
        }
     fclose($fp);
    }
  }


  public function process_vat_transaction_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i >= 1 && !empty($buffer[0]))
        {
          $unique_acc_identifier= isset($buffer[0])?$this->db->escape($buffer[0]):'';
		  $activity_period= isset($buffer[1])?$this->db->escape($buffer[1]):'';
		  $sales_channel= isset($buffer[2])?$this->db->escape($buffer[2]):'';
		  $market_palce= $buffer[3];
          $cnt=explode('.',$market_palce);
          if(count($cnt) > 3)
          {
           $contry=$cnt[count($cnt)-1];
           $country=strtoupper($contry);
             }
          $market_palce= isset($buffer[3])?$this->db->escape($buffer[3]):'';
		  $trans_type= isset($buffer[4])?$this->db->escape($buffer[4]):'';
		  $trans_event_id= isset($buffer[5])?$this->db->escape($buffer[5]):'';
          $activity_trans_id= isset($buffer[6])?$this->db->escape($buffer[6]):'';
		  $tax_cal_date_org= isset($buffer[7])?$buffer[7]:'';
          $tax_cal_date=$this->db->escape(date('Y-m-d',strtotime($tax_cal_date_org)));
          $trans_depart_date_org= isset($buffer[8])?$buffer[8]:'';
          $trans_depart_date=$this->db->escape(date('Y-m-d',strtotime($trans_depart_date_org)));
		  $trans_arraival_date_org= isset($buffer[9])?$buffer[9]:'';
          $trans_arraival_date=$this->db->escape(date('Y-m-d',strtotime($trans_arraival_date_org)));
          $trans_compile_date_org= isset($buffer[10])?$buffer[10]:'';
          $trans_compile_date=$this->db->escape(date('Y-m-d',strtotime($trans_compile_date_org)));
          $seller_sku= isset($buffer[11])?$this->db->escape($buffer[11]):'';
		  $prod_asin= isset($buffer[12])?$this->db->escape($buffer[12]):'';
		  $desc= isset($buffer[13])?$buffer[13]:'';
		   $description=str_replace("'","",$desc);
		  $qty= isset($buffer[15])?$this->db->escape($buffer[15]):'';
		  $itm_weight= isset($buffer[16])?$this->db->escape($buffer[16]):'';
		  $total_weight_activity= isset($buffer[17])?$this->db->escape($buffer[17]):'';
		  $cost_price_of_items=isset($buffer[18])?$this->db->escape($buffer[18]):'';
          $price_of_items_amt_vat_excl=isset($buffer[19])?$this->db->escape($buffer[19]):'';
          $promo_price_of_items_amt_vat_excl=isset($buffer[20])?$this->db->escape($buffer[20]):'';
          $total_price_of_items_amt_vat_excl=isset($buffer[21])?$this->db->escape($buffer[21]):'';
          $ship_charge_amt_vat_excl=isset($buffer[22])?$this->db->escape($buffer[22]):'';
          $promo_ship_charge_amt_vat_excl=isset($buffer[23])?$this->db->escape($buffer[23]):'';
          $total_ship_charge_amt_vat_excl=isset($buffer[24])?$this->db->escape($buffer[24]):'';
          $gift_wrap_amt_vat_excl=isset($buffer[25])?$this->db->escape($buffer[25]):'';
          $promo_gift_wrap_amt_vat_excl=isset($buffer[26])?$this->db->escape($buffer[26]):'';
          $total_gift_wrap_amt_vat_excl=isset($buffer[27])?$this->db->escape($buffer[27]):'';
          $total_activity_value_amt_vat_excl=isset($buffer[28])?$this->db->escape($buffer[28]):'';
          $price_of_items_vat_rate_percent=isset($buffer[29])?$this->db->escape($buffer[29]):'';
          $price_of_items_vat_amt=isset($buffer[30])?$this->db->escape($buffer[30]):'';
          $promo_price_of_items_vat_amt=isset($buffer[31])?$this->db->escape($buffer[31]):'';
          $total_price_of_items_vat_amt=isset($buffer[32])?$this->db->escape($buffer[32]):'';
          $ship_charge_vat_rate_percent=isset($buffer[33])?$this->db->escape($buffer[33]):'';
          $ship_charge_vat_amt=isset($buffer[34])?$this->db->escape($buffer[34]):'';
          $promo_ship_charge_vat_amt=isset($buffer[35])?$this->db->escape($buffer[35]):'';
          $total_ship_charge_vat_amt=isset($buffer[36])?$this->db->escape($buffer[36]):'';
          $gift_wrap_vat_rate_percent=isset($buffer[37])?$this->db->escape($buffer[37]):'';
          $gift_wrap_vat_amt=isset($buffer[38])?$this->db->escape($buffer[38]):'';
          $promo_gift_wrap_vat_amt=isset($buffer[39])?$this->db->escape($buffer[39]):'';
          $total_gift_wrap_vat_amt=isset($buffer[40])?$this->db->escape($buffer[40]):'';
          $total_activity_value_vat_amt=isset($buffer[41])?$this->db->escape($buffer[41]):'';
          $price_of_items_amt_vat_incl=isset($buffer[42])?$this->db->escape($buffer[42]):'';
          $promo_price_of_items_amt_vat_incl=isset($buffer[43])?$this->db->escape($buffer[43]):'';
          $total_price_of_items_amt_vat_incl=isset($buffer[44])?$this->db->escape($buffer[44]):'';
          $ship_charge_amt_vat_incl=isset($buffer[45])?$this->db->escape($buffer[45]):'';
          $promo_ship_charge_amt_vat_incl=isset($buffer[46])?$this->db->escape($buffer[46]):'';
          $total_ship_charge_amt_vat_incl=isset($buffer[47])?$this->db->escape($buffer[47]):'';
          $gift_wrap_amt_vat_incl=isset($buffer[48])?$this->db->escape($buffer[48]):'';
          $promo_gift_wrap_amt_vat_incl=isset($buffer[49])?$this->db->escape($buffer[49]):'';
          $total_gift_wrap_amt_vat_incl=isset($buffer[50])?$this->db->escape($buffer[50]):'';
          $total_activity_value_amt_vat_incl=isset($buffer[51])?$this->db->escape($buffer[51]):'';
          $transaction_currency_code=isset($buffer[52])?$this->db->escape($buffer[52]):'';
		  $commodity_code=isset($buffer[53])?$this->db->escape($buffer[53]):'';
          $statistical_code_depart=isset($buffer[54])?$this->db->escape($buffer[54]):'';
          $statistical_code_arrival=isset($buffer[55])?$this->db->escape($buffer[55]):'';
          $commodity_code_supplementary_unit=isset($buffer[56])?$this->db->escape($buffer[56]):'';
          $item_qty_supplementary_unit=isset($buffer[57])?$this->db->escape($buffer[57]):'';
          $total_activity_supplementary_unit=isset($buffer[58])?$this->db->escape($buffer[58]):'';
          $product_tax_code=isset($buffer[59])?$this->db->escape($buffer[59]):'';
          $depature_city=isset($buffer[60])?$this->db->escape($buffer[60]):'';
          $departure_country=isset($buffer[61])?$this->db->escape($buffer[61]):'';
          $departure_post_code=isset($buffer[62])?$this->db->escape($buffer[62]):'';
          $arrival_city=isset($buffer[63])?$this->db->escape($buffer[63]):'';
          $arrival_country=isset($buffer[64])?$this->db->escape($buffer[64]):'';
          $arrival_post_code=isset($buffer[65])?$this->db->escape($buffer[65]):'';
          $sale_depart_country=isset($buffer[66])?$this->db->escape($buffer[66]):'';
          $sale_arrival_country=isset($buffer[67])?$this->db->escape($buffer[67]):'';
          $transportation_mode=isset($buffer[68])?$this->db->escape($buffer[68]):'';
          $delivery_conditions=isset($buffer[69])?$this->db->escape($buffer[69]):'';
          $seller_depart_vat_number_country=isset($buffer[70])?$this->db->escape($buffer[70]):'';
          $seller_depart_country_vat_number=isset($buffer[71])?$this->db->escape($buffer[71]):'';
          $seller_arrival_vat_number_country=isset($buffer[72])?$this->db->escape($buffer[72]):'';
          $seller_arrival_country_vat_number=isset($buffer[73])?$this->db->escape($buffer[73]):'';
          $transaction_seller_vat_number_country=isset($buffer[74])?$this->db->escape($buffer[74]):'';
          $transaction_seller_vat_number=isset($buffer[75])?$this->db->escape($buffer[75]):'';
          $buyer_vat_number_country=isset($buffer[76])?$this->db->escape($buffer[76]):'';
          $buyer_vat_number=isset($buffer[77])?$this->db->escape($buffer[77]):'';
          $vat_calculation_imputation_country=isset($buffer[78])?$this->db->escape($buffer[78]):'';
          $taxable_jurisdiction=isset($buffer[79])?$this->db->escape($buffer[79]):'';
          $taxable_jurisdiction_level=isset($buffer[80])?$this->db->escape($buffer[80]):'';
          $vat_inv_number=isset($buffer[81])?$this->db->escape($buffer[81]):'';
          $vat_inv_converted_amt=isset($buffer[82])?$this->db->escape($buffer[82]):'';
          $vat_inv_currency_code=isset($buffer[83])?$this->db->escape($buffer[83]):'';
          $vat_inv_exchange_rate=isset($buffer[84])?$this->db->escape($buffer[84]):'';
          $vat_inv_exchange_rate_date=isset($buffer[85])?$this->db->escape($buffer[85]):'';
          $export_outside_eu=isset($buffer[86])?$this->db->escape($buffer[86]):'';
          $invoice_url=isset($buffer[87])?$this->db->escape($buffer[87]):'';
          $buyer_name=isset($buffer[88])?$this->db->escape($buffer[88]):'';
          $arrival_address=isset($buffer[89])?$this->db->escape($buffer[89]):'';
          //$country_code=$this->db->escape($country);
          $bulk_data[]="(".$unique_acc_identifier.",".$activity_period.",".$sales_channel.",".$market_palce.",".$trans_type.",".$trans_event_id.",".$activity_trans_id.",".$tax_cal_date.",'".$tax_cal_date_org."',".$trans_depart_date.",".$trans_arraival_date.",".$trans_compile_date.",".$seller_sku.",".$prod_asin.",'".$description."',".$qty.",".$itm_weight.",".$total_weight_activity.",".$cost_price_of_items.",".$price_of_items_amt_vat_excl.",".$promo_price_of_items_amt_vat_excl.",".$total_price_of_items_amt_vat_excl.",".$ship_charge_amt_vat_excl.",".$promo_ship_charge_amt_vat_excl.",".$total_ship_charge_amt_vat_excl.",".$gift_wrap_amt_vat_excl.",".$promo_gift_wrap_amt_vat_excl.",".$total_gift_wrap_amt_vat_excl.",".$total_activity_value_amt_vat_excl.",".$price_of_items_vat_rate_percent.",".$price_of_items_vat_amt.",".$promo_price_of_items_vat_amt.",".$total_price_of_items_vat_amt.",".$ship_charge_vat_rate_percent.",".$ship_charge_vat_amt.",".$promo_ship_charge_vat_amt.",".$total_ship_charge_vat_amt.",".$gift_wrap_vat_rate_percent.",".$gift_wrap_vat_amt.",".$promo_gift_wrap_vat_amt.",".$total_gift_wrap_vat_amt.",".$total_activity_value_vat_amt.",".$price_of_items_amt_vat_incl.",".$promo_price_of_items_amt_vat_incl.",".$total_price_of_items_amt_vat_incl.",".$ship_charge_amt_vat_incl.",".$promo_ship_charge_amt_vat_incl.",".$total_ship_charge_amt_vat_incl.",".$gift_wrap_amt_vat_incl.",".$promo_gift_wrap_amt_vat_incl.",".$total_gift_wrap_amt_vat_incl.",".$total_activity_value_amt_vat_incl.",".$transaction_currency_code.",".$commodity_code.",".$statistical_code_depart.",".$statistical_code_arrival.",".$commodity_code_supplementary_unit.",".$item_qty_supplementary_unit.",".$total_activity_supplementary_unit.",".$product_tax_code.",".$depature_city.",".$departure_country.",".$departure_post_code.",".$arrival_city.",".$arrival_country.",".$arrival_post_code.",".$sale_depart_country.",".$sale_arrival_country.",".$transportation_mode.",".$delivery_conditions.",".$seller_depart_vat_number_country.",".$seller_depart_country_vat_number.",".$seller_arrival_vat_number_country.",".$seller_arrival_country_vat_number.",".$transaction_seller_vat_number_country.",".$transaction_seller_vat_number.",".$buyer_vat_number_country.",".$buyer_vat_number.",".$vat_calculation_imputation_country.",".$taxable_jurisdiction.",".$taxable_jurisdiction_level.",".$vat_inv_number.",".$vat_inv_converted_amt.",".$vat_inv_currency_code.",".$vat_inv_exchange_rate.",".$vat_inv_exchange_rate_date.",".$export_outside_eu.",".$invoice_url.",".$buyer_name.",".$arrival_address."
,'".$user_id."')";
       }
       //die();
        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT IGNORE INTO `rep_vat_transaction_data`(unique_acc_identifier,activity_period,sales_channel,country,trans_type,trans_event_id,activity_trans_id,tax_cal_date,tax_cal_date_org,trans_depart_date,trans_arraival_date,trans_compile_date,seller_sku,prod_asin,description,qty,itm_weight,total_weight_activity,cost_price_of_items,price_of_items_amt_vat_excl,promo_price_of_items_amt_vat_excl,total_price_of_items_amt_vat_excl,ship_charge_amt_vat_excl,promo_ship_charge_amt_vat_excl,total_ship_charge_amt_vat_excl,gift_wrap_amt_vat_excl,promo_gift_wrap_amt_vat_excl,total_gift_wrap_amt_vat_excl,total_activity_value_amt_vat_excl,price_of_items_vat_rate_percent,price_of_items_vat_amt,promo_price_of_items_vat_amt,total_price_of_items_vat_amt,ship_charge_vat_rate_percent,ship_charge_vat_amt,promo_ship_charge_vat_amt,total_ship_charge_vat_amt,gift_wrap_vat_rate_percent,gift_wrap_vat_amt,promo_gift_wrap_vat_amt,total_gift_wrap_vat_amt,total_activity_value_vat_amt,price_of_items_amt_vat_incl,promo_price_of_items_amt_vat_incl,total_price_of_items_amt_vat_incl,ship_charge_amt_vat_incl,promo_ship_charge_amt_vat_incl,total_ship_charge_amt_vat_incl,gift_wrap_amt_vat_incl,promo_gift_wrap_amt_vat_incl,total_gift_wrap_amt_vat_incl,total_activity_value_amt_vat_incl,transaction_currency_code,commodity_code,statistical_code_depart,statistical_code_arrival,commodity_code_supplementary_unit,item_qty_supplementary_unit,total_activity_supplementary_unit,product_tax_code,depature_city,departure_country,departure_post_code,arrival_city,arrival_country,arrival_post_code,sale_depart_country,sale_arrival_country,transportation_mode,delivery_conditions,seller_depart_vat_number_country,seller_depart_country_vat_number,seller_arrival_vat_number_country,seller_arrival_country_vat_number,transaction_seller_vat_number_country,transaction_seller_vat_number,buyer_vat_number_country,buyer_vat_number,vat_calculation_imputation_country,taxable_jurisdiction,taxable_jurisdiction_level,vat_inv_number,vat_inv_converted_amt,vat_inv_currency_code,vat_inv_exchange_rate,vat_inv_exchange_rate_date,export_outside_eu,invoice_url,buyer_name,arrival_address,user_id)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
          unique_acc_identifier=VALUES(unique_acc_identifier),activity_period=VALUES(activity_period),sales_channel=VALUES(sales_channel),country=VALUES(country),trans_type=VALUES(trans_type),trans_event_id=VALUES(trans_event_id),activity_trans_id=VALUES(activity_trans_id),tax_cal_date=VALUES(tax_cal_date),tax_cal_date_org=VALUES(tax_cal_date_org),trans_depart_date=VALUES(trans_depart_date),trans_arraival_date=VALUES(trans_arraival_date),trans_compile_date=VALUES(trans_compile_date),seller_sku=VALUES(seller_sku),prod_asin=VALUES(prod_asin),description=VALUES(description),qty=VALUES(qty),itm_weight=VALUES(itm_weight),total_weight_activity=VALUES(total_weight_activity),
cost_price_of_items=VALUES(cost_price_of_items),price_of_items_amt_vat_excl=VALUES(price_of_items_amt_vat_excl),promo_price_of_items_amt_vat_excl=VALUES(promo_price_of_items_amt_vat_excl),
total_price_of_items_amt_vat_excl=VALUES(total_price_of_items_amt_vat_excl),ship_charge_amt_vat_excl=VALUES(ship_charge_amt_vat_excl),promo_ship_charge_amt_vat_excl=VALUES(promo_ship_charge_amt_vat_excl),
total_ship_charge_amt_vat_excl=VALUES(total_ship_charge_amt_vat_excl),gift_wrap_amt_vat_excl=VALUES(gift_wrap_amt_vat_excl),promo_gift_wrap_amt_vat_excl=VALUES(promo_gift_wrap_amt_vat_excl),
total_gift_wrap_amt_vat_excl=VALUES(total_gift_wrap_amt_vat_excl),total_activity_value_amt_vat_excl=VALUES(total_activity_value_amt_vat_excl),
price_of_items_vat_rate_percent=VALUES(price_of_items_vat_rate_percent),price_of_items_vat_amt=VALUES(price_of_items_vat_amt),
promo_price_of_items_vat_amt=VALUES(promo_price_of_items_vat_amt),total_price_of_items_vat_amt=VALUES(total_price_of_items_vat_amt),
ship_charge_vat_rate_percent=VALUES(ship_charge_vat_rate_percent),ship_charge_vat_amt=VALUES(ship_charge_vat_amt),promo_ship_charge_vat_amt=VALUES(promo_ship_charge_vat_amt),
total_ship_charge_vat_amt=VALUES(total_ship_charge_vat_amt),gift_wrap_vat_rate_percent=VALUES(gift_wrap_vat_rate_percent),gift_wrap_vat_amt=VALUES(gift_wrap_vat_amt),
promo_gift_wrap_vat_amt=VALUES(promo_gift_wrap_vat_amt),total_gift_wrap_vat_amt=VALUES(total_gift_wrap_vat_amt),total_activity_value_vat_amt=VALUES(total_activity_value_vat_amt),
price_of_items_amt_vat_incl=VALUES(price_of_items_amt_vat_incl),promo_price_of_items_amt_vat_incl=VALUES(promo_price_of_items_amt_vat_incl),
total_price_of_items_amt_vat_incl=VALUES(total_price_of_items_amt_vat_incl),ship_charge_amt_vat_incl=VALUES(ship_charge_amt_vat_incl),
promo_ship_charge_amt_vat_incl=VALUES(promo_ship_charge_amt_vat_incl),total_ship_charge_amt_vat_incl=VALUES(total_ship_charge_amt_vat_incl),
gift_wrap_amt_vat_incl=VALUES(gift_wrap_amt_vat_incl),promo_gift_wrap_amt_vat_incl=VALUES(promo_gift_wrap_amt_vat_incl),
total_gift_wrap_amt_vat_incl=VALUES(total_gift_wrap_amt_vat_incl),total_activity_value_amt_vat_incl=VALUES(total_activity_value_amt_vat_incl),
transaction_currency_code=VALUES(transaction_currency_code),commodity_code=VALUES(commodity_code),statistical_code_depart=VALUES(statistical_code_depart),
statistical_code_arrival=VALUES(statistical_code_arrival),commodity_code_supplementary_unit=VALUES(commodity_code_supplementary_unit),item_qty_supplementary_unit=VALUES(item_qty_supplementary_unit),
total_activity_supplementary_unit=VALUES(total_activity_supplementary_unit),product_tax_code=VALUES(product_tax_code),depature_city=VALUES(depature_city),departure_country=VALUES(departure_country),
departure_post_code=VALUES(departure_post_code),arrival_city=VALUES(arrival_city),arrival_country=VALUES(arrival_country),arrival_post_code=VALUES(arrival_post_code),
sale_depart_country=VALUES(sale_depart_country),sale_arrival_country=VALUES(sale_arrival_country),transportation_mode=VALUES(transportation_mode),delivery_conditions=VALUES(delivery_conditions),
seller_depart_vat_number_country=VALUES(seller_depart_vat_number_country),seller_depart_country_vat_number=VALUES(seller_depart_country_vat_number),seller_arrival_vat_number_country=VALUES(seller_arrival_vat_number_country),
seller_arrival_country_vat_number=VALUES(seller_arrival_country_vat_number),transaction_seller_vat_number_country=VALUES(transaction_seller_vat_number_country),transaction_seller_vat_number=VALUES(transaction_seller_vat_number),
buyer_vat_number_country=VALUES(buyer_vat_number_country),buyer_vat_number=VALUES(buyer_vat_number),vat_calculation_imputation_country=VALUES(vat_calculation_imputation_country),taxable_jurisdiction=VALUES(taxable_jurisdiction),
taxable_jurisdiction_level=VALUES(taxable_jurisdiction_level),vat_inv_number=VALUES(vat_inv_number),vat_inv_converted_amt=VALUES(vat_inv_converted_amt),vat_inv_currency_code=VALUES(vat_inv_currency_code),
vat_inv_exchange_rate=VALUES(vat_inv_exchange_rate),vat_inv_exchange_rate_date=VALUES(vat_inv_exchange_rate_date),export_outside_eu=VALUES(export_outside_eu),invoice_url=VALUES(invoice_url),
buyer_name=VALUES(buyer_name),arrival_address=VALUES(arrival_address),user_id=VALUES(user_id)";

          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {

          $quer=implode(',',$bulk_data);
          $qi="INSERT IGNORE INTO `rep_vat_transaction_data`(unique_acc_identifier,activity_period,sales_channel,country,trans_type,trans_event_id,activity_trans_id,tax_cal_date,tax_cal_date_org,trans_depart_date,trans_arraival_date,trans_compile_date,seller_sku,prod_asin,description,qty,itm_weight,total_weight_activity,cost_price_of_items,price_of_items_amt_vat_excl,promo_price_of_items_amt_vat_excl,total_price_of_items_amt_vat_excl,ship_charge_amt_vat_excl,promo_ship_charge_amt_vat_excl,total_ship_charge_amt_vat_excl,gift_wrap_amt_vat_excl,promo_gift_wrap_amt_vat_excl,total_gift_wrap_amt_vat_excl,total_activity_value_amt_vat_excl,price_of_items_vat_rate_percent,price_of_items_vat_amt,promo_price_of_items_vat_amt,total_price_of_items_vat_amt,ship_charge_vat_rate_percent,ship_charge_vat_amt,promo_ship_charge_vat_amt,total_ship_charge_vat_amt,gift_wrap_vat_rate_percent,gift_wrap_vat_amt,promo_gift_wrap_vat_amt,total_gift_wrap_vat_amt,total_activity_value_vat_amt,price_of_items_amt_vat_incl,promo_price_of_items_amt_vat_incl,total_price_of_items_amt_vat_incl,ship_charge_amt_vat_incl,promo_ship_charge_amt_vat_incl,total_ship_charge_amt_vat_incl,gift_wrap_amt_vat_incl,promo_gift_wrap_amt_vat_incl,total_gift_wrap_amt_vat_incl,total_activity_value_amt_vat_incl,transaction_currency_code,commodity_code,statistical_code_depart,statistical_code_arrival,commodity_code_supplementary_unit,item_qty_supplementary_unit,total_activity_supplementary_unit,product_tax_code,depature_city,departure_country,departure_post_code,arrival_city,arrival_country,arrival_post_code,sale_depart_country,sale_arrival_country,transportation_mode,delivery_conditions,seller_depart_vat_number_country,seller_depart_country_vat_number,seller_arrival_vat_number_country,seller_arrival_country_vat_number,transaction_seller_vat_number_country,transaction_seller_vat_number,buyer_vat_number_country,buyer_vat_number,vat_calculation_imputation_country,taxable_jurisdiction,taxable_jurisdiction_level,vat_inv_number,vat_inv_converted_amt,vat_inv_currency_code,vat_inv_exchange_rate,vat_inv_exchange_rate_date,export_outside_eu,invoice_url,buyer_name,arrival_address,user_id)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
          unique_acc_identifier=VALUES(unique_acc_identifier),activity_period=VALUES(activity_period),sales_channel=VALUES(sales_channel),country=VALUES(country),trans_type=VALUES(trans_type),trans_event_id=VALUES(trans_event_id),activity_trans_id=VALUES(activity_trans_id),tax_cal_date=VALUES(tax_cal_date),tax_cal_date_org=VALUES(tax_cal_date_org),trans_depart_date=VALUES(trans_depart_date),trans_arraival_date=VALUES(trans_arraival_date),trans_compile_date=VALUES(trans_compile_date),seller_sku=VALUES(seller_sku),prod_asin=VALUES(prod_asin),description=VALUES(description),qty=VALUES(qty),itm_weight=VALUES(itm_weight),total_weight_activity=VALUES(total_weight_activity),
cost_price_of_items=VALUES(cost_price_of_items),price_of_items_amt_vat_excl=VALUES(price_of_items_amt_vat_excl),promo_price_of_items_amt_vat_excl=VALUES(promo_price_of_items_amt_vat_excl),
total_price_of_items_amt_vat_excl=VALUES(total_price_of_items_amt_vat_excl),ship_charge_amt_vat_excl=VALUES(ship_charge_amt_vat_excl),promo_ship_charge_amt_vat_excl=VALUES(promo_ship_charge_amt_vat_excl),
total_ship_charge_amt_vat_excl=VALUES(total_ship_charge_amt_vat_excl),gift_wrap_amt_vat_excl=VALUES(gift_wrap_amt_vat_excl),promo_gift_wrap_amt_vat_excl=VALUES(promo_gift_wrap_amt_vat_excl),
total_gift_wrap_amt_vat_excl=VALUES(total_gift_wrap_amt_vat_excl),total_activity_value_amt_vat_excl=VALUES(total_activity_value_amt_vat_excl),
price_of_items_vat_rate_percent=VALUES(price_of_items_vat_rate_percent),price_of_items_vat_amt=VALUES(price_of_items_vat_amt),
promo_price_of_items_vat_amt=VALUES(promo_price_of_items_vat_amt),total_price_of_items_vat_amt=VALUES(total_price_of_items_vat_amt),
ship_charge_vat_rate_percent=VALUES(ship_charge_vat_rate_percent),ship_charge_vat_amt=VALUES(ship_charge_vat_amt),promo_ship_charge_vat_amt=VALUES(promo_ship_charge_vat_amt),
total_ship_charge_vat_amt=VALUES(total_ship_charge_vat_amt),gift_wrap_vat_rate_percent=VALUES(gift_wrap_vat_rate_percent),gift_wrap_vat_amt=VALUES(gift_wrap_vat_amt),
promo_gift_wrap_vat_amt=VALUES(promo_gift_wrap_vat_amt),total_gift_wrap_vat_amt=VALUES(total_gift_wrap_vat_amt),total_activity_value_vat_amt=VALUES(total_activity_value_vat_amt),
price_of_items_amt_vat_incl=VALUES(price_of_items_amt_vat_incl),promo_price_of_items_amt_vat_incl=VALUES(promo_price_of_items_amt_vat_incl),
total_price_of_items_amt_vat_incl=VALUES(total_price_of_items_amt_vat_incl),ship_charge_amt_vat_incl=VALUES(ship_charge_amt_vat_incl),
promo_ship_charge_amt_vat_incl=VALUES(promo_ship_charge_amt_vat_incl),total_ship_charge_amt_vat_incl=VALUES(total_ship_charge_amt_vat_incl),
gift_wrap_amt_vat_incl=VALUES(gift_wrap_amt_vat_incl),promo_gift_wrap_amt_vat_incl=VALUES(promo_gift_wrap_amt_vat_incl),
total_gift_wrap_amt_vat_incl=VALUES(total_gift_wrap_amt_vat_incl),total_activity_value_amt_vat_incl=VALUES(total_activity_value_amt_vat_incl),
transaction_currency_code=VALUES(transaction_currency_code),commodity_code=VALUES(commodity_code),statistical_code_depart=VALUES(statistical_code_depart),
statistical_code_arrival=VALUES(statistical_code_arrival),commodity_code_supplementary_unit=VALUES(commodity_code_supplementary_unit),item_qty_supplementary_unit=VALUES(item_qty_supplementary_unit),
total_activity_supplementary_unit=VALUES(total_activity_supplementary_unit),product_tax_code=VALUES(product_tax_code),depature_city=VALUES(depature_city),departure_country=VALUES(departure_country),
departure_post_code=VALUES(departure_post_code),arrival_city=VALUES(arrival_city),arrival_country=VALUES(arrival_country),arrival_post_code=VALUES(arrival_post_code),
sale_depart_country=VALUES(sale_depart_country),sale_arrival_country=VALUES(sale_arrival_country),transportation_mode=VALUES(transportation_mode),delivery_conditions=VALUES(delivery_conditions),
seller_depart_vat_number_country=VALUES(seller_depart_vat_number_country),seller_depart_country_vat_number=VALUES(seller_depart_country_vat_number),seller_arrival_vat_number_country=VALUES(seller_arrival_vat_number_country),
seller_arrival_country_vat_number=VALUES(seller_arrival_country_vat_number),transaction_seller_vat_number_country=VALUES(transaction_seller_vat_number_country),transaction_seller_vat_number=VALUES(transaction_seller_vat_number),
buyer_vat_number_country=VALUES(buyer_vat_number_country),buyer_vat_number=VALUES(buyer_vat_number),vat_calculation_imputation_country=VALUES(vat_calculation_imputation_country),taxable_jurisdiction=VALUES(taxable_jurisdiction),
taxable_jurisdiction_level=VALUES(taxable_jurisdiction_level),vat_inv_number=VALUES(vat_inv_number),vat_inv_converted_amt=VALUES(vat_inv_converted_amt),vat_inv_currency_code=VALUES(vat_inv_currency_code),
vat_inv_exchange_rate=VALUES(vat_inv_exchange_rate),vat_inv_exchange_rate_date=VALUES(vat_inv_exchange_rate_date),export_outside_eu=VALUES(export_outside_eu),invoice_url=VALUES(invoice_url),
buyer_name=VALUES(buyer_name),arrival_address=VALUES(arrival_address),user_id=VALUES(user_id)";

		$this->db->query($qi);
        unset($bulk_data);
        unset($quer);
     }
     fclose($fp);
    }
  }


  public function process_fba_monthly_inv_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        //print_r($buffer);
        if($i>=1 && !empty($buffer[0]) )
        {

               $month= isset($buffer[0])?$this->db->escape($buffer[0]):'';
			   $fnsku= isset($buffer[1])?$this->db->escape($buffer[1]):'';
               $sku= isset($buffer[2])?$this->db->escape($buffer[2]):'';
               $name= isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $avg_qty= isset($buffer[4])?$this->db->escape($buffer[4]):'';
               $qty= isset($buffer[5])?$this->db->escape($buffer[5]):'';
               $fulfill_id= isset($buffer[6])?$this->db->escape($buffer[6]):'';
               $deposition= isset($buffer[7])?$this->db->escape($buffer[7]):'';
			   $con= isset($buffer[8])?$this->db->escape($buffer[8]):'';


             $bulk_data[]="(".$month.",".$fnsku.",".$sku.",".$name.",".$avg_qty.",".$qty.",".$fulfill_id.",".$deposition.",".$con.",".$user_id.")";

        }

        if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_fba_monthly_inv_data`(prod_month,prod_fn_sku,prod_sku,prod_name,prod_avg_qty,prod_qty,prod_full_id,prod_disp,prod_country,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              prod_month=VALUES(prod_month),prod_fn_sku=VALUES(prod_fn_sku),prod_sku=VALUES(prod_sku),prod_name=VALUES(prod_name),prod_avg_qty=VALUES(prod_avg_qty),prod_qty=VALUES(prod_qty),prod_full_id=VALUES(prod_full_id),prod_disp=VALUES(prod_disp),prod_country=VALUES(prod_country),user_id=VALUES(user_id);";
              $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_fba_monthly_inv_data`(prod_month,prod_fn_sku,prod_sku,prod_name,prod_avg_qty,prod_qty,prod_full_id,prod_disp,prod_country,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              prod_month=VALUES(prod_month),prod_fn_sku=VALUES(prod_fn_sku),prod_sku=VALUES(prod_sku),prod_name=VALUES(prod_name),prod_avg_qty=VALUES(prod_avg_qty),prod_qty=VALUES(prod_qty),prod_full_id=VALUES(prod_full_id),prod_disp=VALUES(prod_disp),prod_country=VALUES(prod_country),user_id=VALUES(user_id);";
              $this->db->query($qi);
	          unset($bulk_data);
              unset($quer);
        }
     fclose($fp);
    }
  }

   public function process_fba_fullfill_cus_tax_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        //print_r($buffer);
        if($i>=1 && !empty($buffer[0]) )
        {

               $ship_date= isset($buffer[0])?$this->db->escape($buffer[0]):'';
			   $shipment_date=$this->db->escape(date('Y-m-d H:i:s',strtotime($ship_date)));
			   $sku= isset($buffer[1])?$this->db->escape($buffer[1]):'';
               $fnsku= isset($buffer[2])?$this->db->escape($buffer[2]):'';
               $asin= isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $fullfill_id= isset($buffer[4])?$this->db->escape($buffer[4]):'';
               $qty= isset($buffer[5])?$this->db->escape($buffer[5]):'';
               $amz_order_id= isset($buffer[6])?$this->db->escape($buffer[6]):'';
               $currency= isset($buffer[7])?$this->db->escape($buffer[7]):'';
			   $itm_price= isset($buffer[8])?$this->db->escape($buffer[8]):'';
			   $ship_price= isset($buffer[9])?$this->db->escape($buffer[9]):'';
			   $gift_wrap= isset($buffer[10])?$this->db->escape($buffer[10]):'';
			   $city= isset($buffer[11])?$this->db->escape($buffer[11]):'';
			   $state= isset($buffer[12])?$this->db->escape($buffer[12]):'';
			   $postal= isset($buffer[13])?$this->db->escape($buffer[13]):'';


             $bulk_data[]="(".$shipment_date.",".$sku.",".$fnsku.",".$asin.",".$fullfill_id.",".$qty.",".$amz_order_id.",".$currency.",".$itm_price.",".$ship_price.",".$gift_wrap.",".$city.",".$state.",".$postal.",".$user_id.")";

        }

        if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_fba_customer_tax_data`(ship_date,sku,fn_sku,asin,fullfill_id,qty,amz_order_id,currency,itm_price,ship_price,gift_wrap,ship_city,ship_state,ship_postal,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              ship_date=VALUES(ship_date),sku=VALUES(sku),fn_sku=VALUES(fn_sku),asin=VALUES(asin),fullfill_id=VALUES(fullfill_id),qty=VALUES(qty),amz_order_id=VALUES(amz_order_id),currency=VALUES(currency),itm_price=VALUES(itm_price),ship_price=VALUES(ship_price),gift_wrap=VALUES(gift_wrap),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_postal=VALUES(ship_postal),user_id=VALUES(user_id);";
              $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_fba_customer_tax_data`(ship_date,sku,fn_sku,asin,fullfill_id,qty,amz_order_id,currency,itm_price,ship_price,gift_wrap,ship_city,ship_state,ship_postal,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              ship_date=VALUES(ship_date),sku=VALUES(sku),fn_sku=VALUES(fn_sku),asin=VALUES(asin),fullfill_id=VALUES(fullfill_id),qty=VALUES(qty),amz_order_id=VALUES(amz_order_id),currency=VALUES(currency),itm_price=VALUES(itm_price),ship_price=VALUES(ship_price),gift_wrap=VALUES(gift_wrap),ship_city=VALUES(ship_city),ship_state=VALUES(ship_state),ship_postal=VALUES(ship_postal),user_id=VALUES(user_id);";
              $this->db->query($qi);
	          unset($bulk_data);
              unset($quer);
        }
     fclose($fp);
    }
  }

  public function process_fba_returns_data($user_id,$report_file,$country,$request_type)
  {
    //die();
	$fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i >= 1 && !empty($buffer[0]))
        {
          $return_date= isset($buffer[0])?$this->db->escape($buffer[0]):'';
		  $order_id= isset($buffer[1])?$this->db->escape($buffer[1]):'';
		  $sku= isset($buffer[2])?$this->db->escape($buffer[2]):'';
		  $asin= isset($buffer[3])?$this->db->escape($buffer[3]):'';
		  $fn_sku= isset($buffer[4])?$this->db->escape($buffer[4]):'';
		  $prod_name= isset($buffer[5])?$this->db->escape($buffer[5]):'';
		  $qty= isset($buffer[6])?$this->db->escape($buffer[6]):'';
		  $fullfill_cent_id= isset($buffer[7])?$this->db->escape($buffer[7]):'';
		  $detailed_disp= isset($buffer[8])?$this->db->escape($buffer[8]):'';
		  $reason= isset($buffer[9])?$this->db->escape($buffer[9]):'';
		  $status= isset($buffer[10])?$this->db->escape($buffer[10]):'';
		  $licence_plate_num= isset($buffer[11])?$this->db->escape($buffer[11]):'';
		  $cust_comments= isset($buffer[12])?$this->db->escape($buffer[12]):'';

          $bulk_data[]="(".$return_date.",".$order_id.",".$sku.",".$asin.",".$fn_sku.",".$prod_name.",".$qty.",".$fullfill_cent_id.",".$detailed_disp.",".$reason.",".$status.",".$licence_plate_num.",".$cust_comments.",".$user_id.")";

	   }


        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT  INTO `amz_order_return_data` (return_date,order_id,sku,asin,fn_sku,prod_name,qty,fullfill_cent_id,detailed_disp,reason,status,licence_plate_num,cust_comments,ret_for)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
		  return_date=VALUES(return_date),order_id=VALUES(order_id),sku=VALUES(sku),asin=VALUES(asin),fn_sku=VALUES(fn_sku),prod_name=VALUES(prod_name),qty=VALUES(qty),fullfill_cent_id=VALUES(fullfill_cent_id),detailed_disp=VALUES(detailed_disp),reason=VALUES(reason),status=VALUES(status),licence_plate_num=VALUES(licence_plate_num),cust_comments=VALUES(cust_comments),ret_for=VALUES(ret_for)";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {
        $quer=implode(',',$bulk_data);
         $qi="INSERT  INTO `amz_order_return_data` (return_date,order_id,sku,asin,fn_sku,prod_name,qty,fullfill_cent_id,detailed_disp,reason,status,licence_plate_num,cust_comments,ret_for)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
		  return_date=VALUES(return_date),order_id=VALUES(order_id),sku=VALUES(sku),asin=VALUES(asin),fn_sku=VALUES(fn_sku),prod_name=VALUES(prod_name),qty=VALUES(qty),fullfill_cent_id=VALUES(fullfill_cent_id),detailed_disp=VALUES(detailed_disp),reason=VALUES(reason),status=VALUES(status),licence_plate_num=VALUES(licence_plate_num),cust_comments=VALUES(cust_comments),ret_for=VALUES(ret_for)";
          $this->db->query($qi);
        $this->db->query($qi);
        unset($bulk_data);
        unset($quer);
     }
     fclose($fp);
    }
  }



  public function process_restock_inv_data($user_id,$report_file,$country,$request_type)
  {
	//print_r($country);
	if($country=='US')
	{

	 $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i >= 1 && !empty($buffer[0]))
        {
          $con= $this->db->escape($country);
		 // $res_desc=date("Y-m-d", strtotime($return_date));
		  $desc= isset($buffer[0])?$this->db->escape($buffer[0]):'';
		  $fn_sku='--';
		  $sku= isset($buffer[1])?$this->db->escape($buffer[1]):'';
		  $asin= isset($buffer[2])?$this->db->escape($buffer[2]):'';
		  $cond= isset($buffer[3])?$this->db->escape($buffer[3]):'';
		  $supp= isset($buffer[4])?$this->db->escape($buffer[4]):'';
		  $supp_no= '--';
		  $curr='USD';
		  $price= isset($buffer[5])?$this->db->escape($buffer[5]):'';
		  $sales_30_days= isset($buffer[6])?$this->db->escape($buffer[6]):'';
		  $sales_30_days_unit= isset($buffer[7])?$this->db->escape($buffer[7]):'';
		  $total_inv= isset($buffer[8])?$this->db->escape($buffer[8]):'';
		  $inb_inv= isset($buffer[9])?$this->db->escape($buffer[9]):'';
		  $avail_inv= isset($buffer[10])?$this->db->escape($buffer[10]):'';
		  $fc_trans= isset($buffer[11])?$this->db->escape($buffer[11]):'';
		  $fc_process= isset($buffer[12])?$this->db->escape($buffer[12]):'';
		  $cus_order= isset($buffer[13])?$this->db->escape($buffer[13]):'';
		  $unfull= isset($buffer[14])?$this->db->escape($buffer[14]):'';
		  $fullfill= isset($buffer[15])?$this->db->escape($buffer[15]):'';
		  $days_of_supply= isset($buffer[16])?$this->db->escape($buffer[16]):'';
		  $ins_alert= isset($buffer[17])?$this->db->escape($buffer[17]):'';
		  $rec_order_alert= isset($buffer[18])?$this->db->escape($buffer[18]):'';
		  $rec_order_da= isset($buffer[19])?$buffer[19]:'';
		  $rec_order_date=date("Y-m-d", strtotime($rec_order_da));

		//  print_r($rec_order_da);
		//  echo"\n";
		//  print_r($rec_order_date);
		//  die();
		  //$fc_code='FBA';


		   //print_r($return_date);
		   //die();
		  //$country_code=$this->db->escape($country);
          $bulk_data[]="(".$con.",".$desc.",'".$fn_sku."',".$sku.",".$asin.",".$cond.",".$supp.",'".$supp_no."','".$curr."',".$price.",".$sales_30_days.",".$sales_30_days_unit.",".$total_inv.",".$inb_inv.",".$avail_inv.",".$fc_trans.",".$fc_process.",".$cus_order.",".$unfull.",".$fullfill.",".$days_of_supply.",".$ins_alert.",".$rec_order_alert.",'".$rec_order_date."',".$user_id.")";
          //print_r($bulk_data);

	   }


        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT  INTO `rep_restock_inv_data` (res_country,res_desc,res_fn_sku,res_sku,res_asin,res_cond,res_supp,res_supp_no,res_curr,res_price,res_sales_30_days,res_sales_30_days_units,res_total_inv,res_inb_inv,res_avb_inv,res_fc_trans,res_fc_process,res_cus_order,res_unfill,res_fulfill,res_days_of_sup,res_instock_alert,res_recom_qty,res_recom_order_date,res_user_id)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
		  res_country=VALUES(res_country),res_desc=VALUES(res_desc),res_fn_sku=VALUES(res_fn_sku),res_sku=VALUES(res_sku),res_asin=VALUES(res_asin),res_cond=VALUES(res_cond),res_supp=VALUES(res_supp),res_supp_no=VALUES(res_supp_no),res_curr=VALUES(res_curr),res_price=VALUES(res_price),res_sales_30_days=VALUES(res_sales_30_days),res_sales_30_days_units=VALUES(res_sales_30_days_units),res_total_inv=VALUES(res_total_inv),res_inb_inv=VALUES(res_inb_inv),res_avb_inv=VALUES(res_avb_inv),res_fc_trans=VALUES(res_fc_trans),res_fc_process=VALUES(res_fc_process),res_cus_order=VALUES(res_cus_order),res_unfill=VALUES(res_unfill),res_fulfill=VALUES(res_fulfill),res_days_of_sup=VALUES(res_days_of_sup),res_instock_alert=VALUES(res_instock_alert),res_recom_qty=VALUES(res_recom_qty),res_recom_order_date=VALUES(res_recom_order_date),res_user_id=VALUES(res_user_id)";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {
        $quer=implode(',',$bulk_data);
         $qi="INSERT  INTO `rep_restock_inv_data` (res_country,res_desc,res_fn_sku,res_sku,res_asin,res_cond,res_supp,res_supp_no,res_curr,res_price,res_sales_30_days,res_sales_30_days_units,res_total_inv,res_inb_inv,res_avb_inv,res_fc_trans,res_fc_process,res_cus_order,res_unfill,res_fulfill,res_days_of_sup,res_instock_alert,res_recom_qty,res_recom_order_date,res_user_id)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
		  res_country=VALUES(res_country),res_desc=VALUES(res_desc),res_fn_sku=VALUES(res_fn_sku),res_sku=VALUES(res_sku),res_asin=VALUES(res_asin),res_cond=VALUES(res_cond),res_supp=VALUES(res_supp),res_supp_no=VALUES(res_supp_no),res_curr=VALUES(res_curr),res_price=VALUES(res_price),res_sales_30_days=VALUES(res_sales_30_days),res_sales_30_days_units=VALUES(res_sales_30_days_units),res_total_inv=VALUES(res_total_inv),res_inb_inv=VALUES(res_inb_inv),res_avb_inv=VALUES(res_avb_inv),res_fc_trans=VALUES(res_fc_trans),res_fc_process=VALUES(res_fc_process),res_cus_order=VALUES(res_cus_order),res_unfill=VALUES(res_unfill),res_fulfill=VALUES(res_fulfill),res_days_of_sup=VALUES(res_days_of_sup),res_instock_alert=VALUES(res_instock_alert),res_recom_qty=VALUES(res_recom_qty),res_recom_order_date=VALUES(res_recom_order_date),res_user_id=VALUES(res_user_id)";
          $this->db->query($qi);
        $this->db->query($qi);
        unset($bulk_data);
        unset($quer);
     }
     fclose($fp);
    }

	}
	else
	{
	$fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i >= 1 && !empty($buffer[0]))
        {
          $con= isset($buffer[0])?$this->db->escape(str_replace('GB','UK',$buffer[4])):'';
		 // $res_desc=date("Y-m-d", strtotime($return_date));
		  $desc= isset($buffer[1])?$this->db->escape($buffer[1]):'';
		  $fn_sku= isset($buffer[2])?$this->db->escape($buffer[2]):'';
		  $sku= isset($buffer[3])?$this->db->escape($buffer[3]):'';
		  $asin= isset($buffer[4])?$this->db->escape($buffer[4]):'';
		  $cond= isset($buffer[5])?$this->db->escape($buffer[5]):'';
		  $supp= isset($buffer[6])?$this->db->escape($buffer[6]):'';
		  $supp_no= isset($buffer[7])?$this->db->escape($buffer[7]):'';
		  $curr= isset($buffer[8])?$this->db->escape($buffer[8]):'';
		  $price= isset($buffer[9])?$this->db->escape($buffer[9]):'';
		  $sales_30_days= isset($buffer[10])?$this->db->escape($buffer[10]):'';
		  $sales_30_days_unit= isset($buffer[11])?$this->db->escape($buffer[11]):'';
		  $total_inv= isset($buffer[12])?$this->db->escape($buffer[12]):'';
		  $inb_inv= isset($buffer[13])?$this->db->escape($buffer[13]):'';
		  $avail_inv= isset($buffer[14])?$this->db->escape($buffer[14]):'';
		  $fc_trans= isset($buffer[15])?$this->db->escape($buffer[15]):'';
		  $fc_process= isset($buffer[16])?$this->db->escape($buffer[16]):'';
		  $cus_order= isset($buffer[17])?$this->db->escape($buffer[17]):'';
		  $unfull= isset($buffer[18])?$this->db->escape($buffer[18]):'';
		  $fullfill= isset($buffer[19])?$this->db->escape($buffer[19]):'';
		  $days_of_supply= isset($buffer[20])?$this->db->escape($buffer[20]):'';
		  $ins_alert= isset($buffer[21])?$this->db->escape($buffer[21]):'';
		  $rec_order_alert= isset($buffer[22])?$this->db->escape($buffer[22]):'';
		  $rec_order_da= isset($buffer[23])?$buffer[23]:'';
		  $rec_order_date=date("Y-m-d", strtotime($rec_order_da));

		//  print_r($rec_order_da);
		//  echo"\n";
		//  print_r($rec_order_date);
		//  die();
		  //$fc_code='FBA';


		   //print_r($return_date);
		   //die();
		  //$country_code=$this->db->escape($country);
          $bulk_data[]="(".$con.",".$desc.",".$fn_sku.",".$sku.",".$asin.",".$cond.",".$supp.",".$supp_no.",".$curr.",".$price.",".$sales_30_days.",".$sales_30_days_unit.",".$total_inv.",".$inb_inv.",".$avail_inv.",".$fc_trans.",".$fc_process.",".$cus_order.",".$unfull.",".$fullfill.",".$days_of_supply.",".$ins_alert.",".$rec_order_alert.",'".$rec_order_date."',".$user_id.")";
          //print_r($bulk_data);

	   }


        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT  INTO `rep_restock_inv_data` (res_country,res_desc,res_fn_sku,res_sku,res_asin,res_cond,res_supp,res_supp_no,res_curr,res_price,res_sales_30_days,res_sales_30_days_units,res_total_inv,res_inb_inv,res_avb_inv,res_fc_trans,res_fc_process,res_cus_order,res_unfill,res_fulfill,res_days_of_sup,res_instock_alert,res_recom_qty,res_recom_order_date,res_user_id)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
		  res_country=VALUES(res_country),res_desc=VALUES(res_desc),res_fn_sku=VALUES(res_fn_sku),res_sku=VALUES(res_sku),res_asin=VALUES(res_asin),res_cond=VALUES(res_cond),res_supp=VALUES(res_supp),res_supp_no=VALUES(res_supp_no),res_curr=VALUES(res_curr),res_price=VALUES(res_price),res_sales_30_days=VALUES(res_sales_30_days),res_sales_30_days_units=VALUES(res_sales_30_days_units),res_total_inv=VALUES(res_total_inv),res_inb_inv=VALUES(res_inb_inv),res_avb_inv=VALUES(res_avb_inv),res_fc_trans=VALUES(res_fc_trans),res_fc_process=VALUES(res_fc_process),res_cus_order=VALUES(res_cus_order),res_unfill=VALUES(res_unfill),res_fulfill=VALUES(res_fulfill),res_days_of_sup=VALUES(res_days_of_sup),res_instock_alert=VALUES(res_instock_alert),res_recom_qty=VALUES(res_recom_qty),res_recom_order_date=VALUES(res_recom_order_date),res_user_id=VALUES(res_user_id)";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {
        $quer=implode(',',$bulk_data);
         $qi="INSERT  INTO `rep_restock_inv_data` (res_country,res_desc,res_fn_sku,res_sku,res_asin,res_cond,res_supp,res_supp_no,res_curr,res_price,res_sales_30_days,res_sales_30_days_units,res_total_inv,res_inb_inv,res_avb_inv,res_fc_trans,res_fc_process,res_cus_order,res_unfill,res_fulfill,res_days_of_sup,res_instock_alert,res_recom_qty,res_recom_order_date,res_user_id)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
		  res_country=VALUES(res_country),res_desc=VALUES(res_desc),res_fn_sku=VALUES(res_fn_sku),res_sku=VALUES(res_sku),res_asin=VALUES(res_asin),res_cond=VALUES(res_cond),res_supp=VALUES(res_supp),res_supp_no=VALUES(res_supp_no),res_curr=VALUES(res_curr),res_price=VALUES(res_price),res_sales_30_days=VALUES(res_sales_30_days),res_sales_30_days_units=VALUES(res_sales_30_days_units),res_total_inv=VALUES(res_total_inv),res_inb_inv=VALUES(res_inb_inv),res_avb_inv=VALUES(res_avb_inv),res_fc_trans=VALUES(res_fc_trans),res_fc_process=VALUES(res_fc_process),res_cus_order=VALUES(res_cus_order),res_unfill=VALUES(res_unfill),res_fulfill=VALUES(res_fulfill),res_days_of_sup=VALUES(res_days_of_sup),res_instock_alert=VALUES(res_instock_alert),res_recom_qty=VALUES(res_recom_qty),res_recom_order_date=VALUES(res_recom_order_date),res_user_id=VALUES(res_user_id)";
          $this->db->query($qi);
        $this->db->query($qi);
        unset($bulk_data);
        unset($quer);
     }
     fclose($fp);
    }
	}


  }


  public function process_fba_inv_health_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while (!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        if($i >= 1 && !empty($buffer[0]))
        {

          $snap_date= isset($buffer[0])?$this->db->escape($buffer[0]):'';
		  $sku= isset($buffer[1])?$this->db->escape($buffer[1]):'';
		  $fn_sku= isset($buffer[2])?$this->db->escape($buffer[2]):'';
		  $asin= isset($buffer[3])?$this->db->escape($buffer[3]):'';
		  $prod_name= isset($buffer[4])?$this->db->escape($buffer[4]):'';
		  $prod_cond= isset($buffer[5])?$this->db->escape($buffer[5]):'';
		  $sales_rank= isset($buffer[6])?$this->db->escape($buffer[6]):'';
		  $prod_group= isset($buffer[7])?$this->db->escape($buffer[7]):'';
		  $total_qty= isset($buffer[8])?$this->db->escape($buffer[8]):'';
		  $sell_qty= isset($buffer[9])?$this->db->escape($buffer[9]):'';
		  $unsell_qty= isset($buffer[10])?$this->db->escape($buffer[10]):'';
		  $inv_age_0_to_90= isset($buffer[11])?$this->db->escape($buffer[11]):'';
		  $inv_age_91_to_180= isset($buffer[12])?$this->db->escape($buffer[12]):'';
		  $inv_age_181_to_270= isset($buffer[13])?$this->db->escape($buffer[13]):'';
		  $inv_age_271_to_365= isset($buffer[14])?$this->db->escape($buffer[14]):'';
		  $inv_age_365_plus= isset($buffer[15])?$this->db->escape($buffer[15]):'';
		  $unit_ship_24_hrs= isset($buffer[16])?$this->db->escape($buffer[16]):'';
		  $unit_ship_7_days= isset($buffer[17])?$this->db->escape($buffer[17]):'';
		  $unit_ship_30_days= isset($buffer[18])?$this->db->escape($buffer[18]):'';
		  $unit_ship_90_days= isset($buffer[19])?$this->db->escape($buffer[19]):'';
		  $unit_ship_180_days= isset($buffer[20])?$this->db->escape($buffer[20]):'';
		  $unit_ship_365_days= isset($buffer[21])?$this->db->escape($buffer[21]):'';
		  $weeks_of_cover_t7= isset($buffer[22])?$this->db->escape($buffer[22]):'';
		  $weeks_of_cover_t30= isset($buffer[23])?$this->db->escape($buffer[23]):'';
		  $weeks_of_cover_t90= isset($buffer[24])?$this->db->escape($buffer[24]):'';
		  $weeks_of_cover_t180= isset($buffer[25])?$this->db->escape($buffer[25]):'';
		  $weeks_of_cover_t365= isset($buffer[26])?$this->db->escape($buffer[26]):'';
		  $num_afn_new_sellers= isset($buffer[27])?$this->db->escape($buffer[27]):'';
		  $num_afn_user_sellers= isset($buffer[28])?$this->db->escape($buffer[28]):'';
		  $curr= isset($buffer[29])?$this->db->escape($buffer[29]):'';
		  $your_price= isset($buffer[30])?$this->db->escape($buffer[30]):'';
		  $sale_price= isset($buffer[31])?$this->db->escape($buffer[31]):'';
		  $low_afn_new_price= isset($buffer[32])?$this->db->escape($buffer[32]):'';
		  $low_afn_used_price= isset($buffer[33])?$this->db->escape($buffer[33]):'';
		  $low_mfn_new_price= isset($buffer[34])?$this->db->escape($buffer[34]):'';
		  $low_mfn_used_price= isset($buffer[35])?$this->db->escape($buffer[35]):'';
		  $qty_charged_12= isset($buffer[36])?$this->db->escape($buffer[36]):'';
		  $qty_charger_long_term= isset($buffer[37])?$this->db->escape($buffer[37]):'';
		  $qty_removal_in_progress= isset($buffer[38])?$this->db->escape($buffer[38]):'';
		  $projected_12= isset($buffer[39])?$this->db->escape($buffer[39]):'';
		  $per_unit_vol= isset($buffer[40])?$this->db->escape($buffer[40]):'';
		  $is_hazmat= isset($buffer[41])?$this->db->escape($buffer[41]):'';
		  $in_bound_qty= isset($buffer[42])?$this->db->escape($buffer[42]):'';
		  $asin_limit= isset($buffer[43])?$this->db->escape($buffer[43]):'';
		  $inbound_recomm_qty= isset($buffer[44])?$this->db->escape($buffer[44]):'';
		  $qty_charged_6= isset($buffer[45])?$this->db->escape($buffer[45]):'';
		  $projected_6= isset($buffer[46])?$this->db->escape($buffer[46]):'';

		  $bulk_data[]="(".$snap_date.",".$sku.",".$fn_sku.",".$asin.",".$prod_name.",".$prod_cond.",".$sales_rank.",".$prod_group.",".$total_qty.",".$sell_qty." ,".$unsell_qty."  ,".$inv_age_0_to_90.",".$inv_age_91_to_180.",".$inv_age_181_to_270.",".$inv_age_271_to_365.",".$inv_age_365_plus.",".$unit_ship_24_hrs.",".$unit_ship_7_days.",".$unit_ship_30_days.",".$unit_ship_90_days.",".$unit_ship_180_days.",".$unit_ship_365_days.",".$weeks_of_cover_t7.",".$weeks_of_cover_t30.",".$weeks_of_cover_t90.",".$weeks_of_cover_t180.",".$weeks_of_cover_t365.",".$num_afn_new_sellers.",".$num_afn_user_sellers.",".$curr.",".$your_price.",".$sale_price.",".$low_afn_new_price.",".$low_afn_used_price.",".$low_mfn_new_price.",".$low_mfn_used_price.",".$qty_charged_12.",".$qty_charger_long_term.",".$qty_removal_in_progress.",".$projected_12.",".$per_unit_vol.",".$is_hazmat.",".$in_bound_qty.",".$asin_limit.",".$inbound_recomm_qty.",".$qty_charged_6.",".$projected_6.",".$user_id.")";
      }


        if(isset($bulk_data) && count($bulk_data)>=500)
        {
          $quer=implode(',',$bulk_data);
          $qi="INSERT  INTO `rep_fba_inv_health_data` (snap_date,sku,fn_sku,asin,prod_name,prod_cond,sales_rank,prod_group,total_qty,sell_qty,unsell_qty,inv_age_0_to_90,inv_age_91_to_180,inv_age_181_to_270,inv_age_271_to_365,inv_age_365_plus,unit_ship_24_hrs,unit_ship_7_days,unit_ship_30_days,unit_ship_90_days,unit_ship_180_days,unit_ship_365_days,weeks_of_cover_t7,weeks_of_cover_t30,weeks_of_cover_t90,weeks_of_cover_t180,weeks_of_cover_t365,num_afn_new_sellers,num_afn_user_sellers,curr,your_price,sale_price,low_afn_new_price,low_afn_used_price,low_mfn_new_price,low_mfn_used_price,qty_charged_12,qty_charger_long_term,qty_removal_in_progress,projected_12,per_unit_vol,is_hazmat,in_bound_qty,asin_limit,inbound_recomm_qty,qty_charged_6,projected_6,added_by)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
		  snap_date=VALUES(snap_date),sku=VALUES(sku),fn_sku=VALUES(fn_sku),asin=VALUES(asin),prod_name=VALUES(prod_name),prod_cond=VALUES(prod_cond),sales_rank=VALUES(sales_rank),prod_group=VALUES(prod_group),total_qty=VALUES(total_qty),sell_qty=VALUES(sell_qty),unsell_qty=VALUES(unsell_qty),inv_age_0_to_90=VALUES(inv_age_0_to_90),inv_age_91_to_180=VALUES(inv_age_91_to_180),inv_age_181_to_270=VALUES(inv_age_181_to_270),inv_age_271_to_365=VALUES(inv_age_271_to_365),inv_age_365_plus=VALUES(inv_age_365_plus),unit_ship_24_hrs=VALUES(unit_ship_24_hrs),unit_ship_7_days=VALUES(unit_ship_7_days),unit_ship_30_days=VALUES(unit_ship_30_days),unit_ship_90_days=VALUES(unit_ship_90_days),unit_ship_180_days=VALUES(unit_ship_180_days),unit_ship_365_days=VALUES(unit_ship_365_days),weeks_of_cover_t7=VALUES(weeks_of_cover_t7),weeks_of_cover_t30=VALUES(weeks_of_cover_t30),weeks_of_cover_t90=VALUES(weeks_of_cover_t90),weeks_of_cover_t180=VALUES(weeks_of_cover_t180),weeks_of_cover_t365=VALUES(weeks_of_cover_t365),num_afn_new_sellers=VALUES(num_afn_new_sellers),num_afn_user_sellers=VALUES(num_afn_user_sellers),curr=VALUES(curr),your_price=VALUES(your_price),sale_price=VALUES(sale_price),low_afn_new_price=VALUES(low_afn_new_price),low_afn_used_price=VALUES(low_afn_used_price),low_mfn_new_price=VALUES(low_mfn_new_price),low_mfn_used_price=VALUES(low_mfn_used_price),qty_charged_12=VALUES(qty_charged_12),qty_charger_long_term=VALUES(qty_charger_long_term),qty_removal_in_progress=VALUES(qty_removal_in_progress),projected_12=VALUES(projected_12),per_unit_vol=VALUES(per_unit_vol),is_hazmat=VALUES(is_hazmat),in_bound_qty=VALUES(in_bound_qty),asin_limit=VALUES(asin_limit),inbound_recomm_qty=VALUES(inbound_recomm_qty),qty_charged_6=VALUES(qty_charged_6),projected_6=VALUES(projected_6),added_by=VALUES(added_by)";
          $this->db->query($qi);
          unset($bulk_data);
          unset($quer);
        }
        $i++;
     }
     if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
     {
        $quer=implode(',',$bulk_data);
         $qi="INSERT  INTO `rep_fba_inv_health_data` (snap_date,sku,fn_sku,asin,prod_name,prod_cond,sales_rank,prod_group,total_qty,sell_qty,unsell_qty,inv_age_0_to_90,inv_age_91_to_180,inv_age_181_to_270,inv_age_271_to_365,inv_age_365_plus,unit_ship_24_hrs,unit_ship_7_days,unit_ship_30_days,unit_ship_90_days,unit_ship_180_days,unit_ship_365_days,weeks_of_cover_t7,weeks_of_cover_t30,weeks_of_cover_t90,weeks_of_cover_t180,weeks_of_cover_t365,num_afn_new_sellers,num_afn_user_sellers,curr,your_price,sale_price,low_afn_new_price,low_afn_used_price,low_mfn_new_price,low_mfn_used_price,qty_charged_12,qty_charger_long_term,qty_removal_in_progress,projected_12,per_unit_vol,is_hazmat,in_bound_qty,asin_limit,inbound_recomm_qty,qty_charged_6,projected_6,added_by)VALUES
          $quer
		  ON DUPLICATE KEY
          UPDATE
		  snap_date=VALUES(snap_date),sku=VALUES(sku),fn_sku=VALUES(fn_sku),asin=VALUES(asin),prod_name=VALUES(prod_name),prod_cond=VALUES(prod_cond),sales_rank=VALUES(sales_rank),prod_group=VALUES(prod_group),total_qty=VALUES(total_qty),sell_qty=VALUES(sell_qty),unsell_qty=VALUES(unsell_qty),inv_age_0_to_90=VALUES(inv_age_0_to_90),inv_age_91_to_180=VALUES(inv_age_91_to_180),inv_age_181_to_270=VALUES(inv_age_181_to_270),inv_age_271_to_365=VALUES(inv_age_271_to_365),inv_age_365_plus=VALUES(inv_age_365_plus),unit_ship_24_hrs=VALUES(unit_ship_24_hrs),unit_ship_7_days=VALUES(unit_ship_7_days),unit_ship_30_days=VALUES(unit_ship_30_days),unit_ship_90_days=VALUES(unit_ship_90_days),unit_ship_180_days=VALUES(unit_ship_180_days),unit_ship_365_days=VALUES(unit_ship_365_days),weeks_of_cover_t7=VALUES(weeks_of_cover_t7),weeks_of_cover_t30=VALUES(weeks_of_cover_t30),weeks_of_cover_t90=VALUES(weeks_of_cover_t90),weeks_of_cover_t180=VALUES(weeks_of_cover_t180),weeks_of_cover_t365=VALUES(weeks_of_cover_t365),num_afn_new_sellers=VALUES(num_afn_new_sellers),num_afn_user_sellers=VALUES(num_afn_user_sellers),curr=VALUES(curr),your_price=VALUES(your_price),sale_price=VALUES(sale_price),low_afn_new_price=VALUES(low_afn_new_price),low_afn_used_price=VALUES(low_afn_used_price),low_mfn_new_price=VALUES(low_mfn_new_price),low_mfn_used_price=VALUES(low_mfn_used_price),qty_charged_12=VALUES(qty_charged_12),qty_charger_long_term=VALUES(qty_charger_long_term),qty_removal_in_progress=VALUES(qty_removal_in_progress),projected_12=VALUES(projected_12),per_unit_vol=VALUES(per_unit_vol),is_hazmat=VALUES(is_hazmat),in_bound_qty=VALUES(in_bound_qty),asin_limit=VALUES(asin_limit),inbound_recomm_qty=VALUES(inbound_recomm_qty),qty_charged_6=VALUES(qty_charged_6),projected_6=VALUES(projected_6),added_by=VALUES(added_by)";
          $this->db->query($qi);
        $this->db->query($qi);
        unset($bulk_data);
        unset($quer);
     }
     fclose($fp);
    }
  }


    public function process_stranded_inv_ui_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        //print_r($buffer);
        if($i>=1 && !empty($buffer[7]) )
        {

               $primary_action= isset($buffer[0])?$this->db->escape($buffer[0]):'';
			   $date_stran= isset($buffer[1])?$buffer[1]:'';
			   $date_stranded=date("Y-m-d", strtotime($date_stran));
			   //print_r( $date_stran);
			   //print_r($date_stranded);
			   //die();

               $status_primary= isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $status_secondary= isset($buffer[4])?$this->db->escape($buffer[4]):'';
               $error_msg= isset($buffer[5])?$this->db->escape($buffer[5]):'';
               $asin= isset($buffer[7])?$this->db->escape($buffer[7]):'';
               $sku= isset($buffer[8])?$this->db->escape($buffer[8]):'';
               $fnsku= isset($buffer[9])?$this->db->escape($buffer[9]):'';
			   $prod_name= isset($buffer[10])?$this->db->escape($buffer[10]):'';
			   $cond= isset($buffer[11])?$this->db->escape($buffer[11]):'';
			   $fulfilled_by= isset($buffer[12])?$this->db->escape($buffer[12]):'';
               $fulfillable_qty= isset($buffer[13])?$this->db->escape($buffer[13]):'';
			   $your_price= isset($buffer[14])?$this->db->escape($buffer[14]):'';
			   $unfulfillable_qty= isset($buffer[15])?$this->db->escape($buffer[15]):'';
			   $reserved_qty= isset($buffer[16])?$this->db->escape($buffer[16]):'';
               $inbound_shipped_qty= isset($buffer[17])?$this->db->escape($buffer[17]):'';


             $bulk_data[]="(".$primary_action.",'".$date_stranded."',".$status_primary.",".$status_secondary.",".$error_msg.",".$asin.",".$sku.",".$fnsku.",".$prod_name.",".$cond.",".$fulfilled_by.",".$fulfillable_qty.",".$your_price.",".$unfulfillable_qty.",".$reserved_qty.",".$inbound_shipped_qty.",".$user_id.")";

        }

        if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `stranded_inv_ui_data`(primary_action,date_stranded,status_primary,status_secondary,error_msg,asin,sku,fnsku,prod_name,cond,fulfilled_by,fulfillable_qty,your_price,unfulfillable_qty,reserved_qty,inbound_shipped_qty,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              primary_action=VALUES(primary_action),date_stranded=VALUES(date_stranded),status_primary=VALUES(status_primary),status_secondary=VALUES(status_secondary),error_msg=VALUES(error_msg),asin=VALUES(asin),sku=VALUES(sku),fnsku=VALUES(fnsku),prod_name=VALUES(prod_name),cond=VALUES(cond),fulfilled_by=VALUES(fulfilled_by),fulfillable_qty=VALUES(fulfillable_qty),your_price=VALUES(your_price),unfulfillable_qty=VALUES(unfulfillable_qty),reserved_qty=VALUES(reserved_qty),inbound_shipped_qty=VALUES(inbound_shipped_qty),user_id=VALUES(user_id);";
            //print_r($qi);
			 $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
              $quer=implode(',',$bulk_data);
             $qi="INSERT INTO `stranded_inv_ui_data`(primary_action,date_stranded,status_primary,status_secondary,error_msg,asin,sku,fnsku,prod_name,cond,fulfilled_by,fulfillable_qty,your_price,unfulfillable_qty,reserved_qty,inbound_shipped_qty,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              primary_action=VALUES(primary_action),date_stranded=VALUES(date_stranded),status_primary=VALUES(status_primary),status_secondary=VALUES(status_secondary),error_msg=VALUES(error_msg),asin=VALUES(asin),sku=VALUES(sku),fnsku=VALUES(fnsku),prod_name=VALUES(prod_name),cond=VALUES(cond),fulfilled_by=VALUES(fulfilled_by),fulfillable_qty=VALUES(fulfillable_qty),your_price=VALUES(your_price),unfulfillable_qty=VALUES(unfulfillable_qty),reserved_qty=VALUES(reserved_qty),inbound_shipped_qty=VALUES(inbound_shipped_qty),user_id=VALUES(user_id);";
              print_r($qi);

			  $this->db->query($qi);
	          unset($bulk_data);
              unset($quer);
        }
     fclose($fp);
    }
  }

    public function process_fba_storage_fee_data($user_id,$report_file,$country,$request_type)
  {
	  //print_r($country);
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
          //print_r($country);
        if($i>=1 && !empty($buffer[0]))
        {

               $asin= isset($buffer[0])?$this->db->escape($buffer[0]):'';
               $fnsku= isset($buffer[1])?$this->db->escape($buffer[1]):'';
			   $product_name= isset($buffer[2])?$this->db->escape($buffer[2]):'';
			   $fulfillment_center= isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $country_code= isset($buffer[4])?$this->db->escape(str_replace('GB','UK',$buffer[4])):'';
               $longest_side= isset($buffer[5])?$this->db->escape($buffer[5]):'';
               $median_side= isset($buffer[6])?$this->db->escape($buffer[6]):'';
               $shortest_side= isset($buffer[7])?$this->db->escape($buffer[7]):'';
               $measurement_units= isset($buffer[8])?$this->db->escape($buffer[8]):'';
			   $weight= isset($buffer[9])?$this->db->escape($buffer[9]):'';
			   $weight_units= isset($buffer[10])?$this->db->escape($buffer[10]):'';
			   $item_volume= isset($buffer[11])?$this->db->escape($buffer[11]):'';
               $volume_units= isset($buffer[12])?$this->db->escape($buffer[12]):'';
			  //print_r($country_code);
			 if($country!='US')
			 {
			  $product_size_tier="''";
		      $average_quantity_on_hand= isset($buffer[13])?$this->db->escape($buffer[13]):'';
		      $average_quantity_pending_removal= isset($buffer[14])?$this->db->escape($buffer[14]):'';
		      $estimated_total_item_volume= isset($buffer[15])?$this->db->escape($buffer[15]):'';
		      $month_of_charge= isset($buffer[16])?$this->db->escape($buffer[16]):'';
		      $storage_rate= isset($buffer[17])?$this->db->escape($buffer[17]):'';
              $currency= isset($buffer[18])?$this->db->escape($buffer[18]):'';
		      $estimated_monthly_storage_fee= isset($buffer[19])?$this->db->escape($buffer[19]):'';
		   	  }
		   	  else
		   	  {
		       $product_size_tier= isset($buffer[13])?$this->db->escape($buffer[13]):'';
		       $average_quantity_on_hand= isset($buffer[14])?$this->db->escape($buffer[14]):'';
			   $average_quantity_pending_removal= isset($buffer[15])?$this->db->escape($buffer[15]):'';
			   $estimated_total_item_volume= isset($buffer[16])?$this->db->escape($buffer[16]):'';
			   $month_of_charge= isset($buffer[17])?$this->db->escape($buffer[17]):'';
			   $storage_rate= isset($buffer[18])?$this->db->escape($buffer[18]):'';
               $currency= isset($buffer[19])?$this->db->escape($buffer[19]):'';
			   $estimated_monthly_storage_fee= isset($buffer[20])?$this->db->escape($buffer[20]):'';
		   	  }

             $bulk_data[]="(".$asin.",".$fnsku.",".$product_name.",".$fulfillment_center.",".$country_code.",".$longest_side.",".$median_side.",".$shortest_side.",".$measurement_units.",".$weight.",".$weight_units.",".$item_volume.",".$volume_units.",".$product_size_tier.",".$average_quantity_on_hand.",".$average_quantity_pending_removal.",".$estimated_total_item_volume.",".$month_of_charge.",".$storage_rate.",".$currency.",".$estimated_monthly_storage_fee.",".$user_id.")";

        }
		//if($i>=1 && !empty($buffer[0]) && $country!='US')
        //{
        //
        //       $asin= isset($buffer[0])?$this->db->escape($buffer[0]):'';
        //       $fnsku= isset($buffer[1])?$this->db->escape($buffer[1]):'';
		//	   $prod_name= isset($buffer[2])?$this->db->escape($buffer[2]):'';
		//	   $pro_name=str_replace("'","",$prod_name);
		//	   $product_name=trim($pro_name);
		//	   $fulfillment_center= isset($buffer[3])?$this->db->escape($buffer[3]):'';
        //       $country_code= isset($buffer[4])?$this->db->escape($buffer[4]):'';
        //       $longest_side= isset($buffer[5])?$this->db->escape($buffer[5]):'';
        //       $median_side= isset($buffer[6])?$this->db->escape($buffer[6]):'';
        //       $shortest_side= isset($buffer[7])?$this->db->escape($buffer[7]):'';
        //       $measurement_units= isset($buffer[8])?$this->db->escape($buffer[8]):'';
		//	   $weight= isset($buffer[9])?$this->db->escape($buffer[9]):'';
		//	   $weight_units= isset($buffer[10])?$this->db->escape($buffer[10]):'';
		//	   $item_volume= isset($buffer[11])?$this->db->escape($buffer[11]):'';
        //       $volume_units= isset($buffer[12])?$this->db->escape($buffer[12]):'';
		//	   $product_size_tier='';
		//	   $average_quantity_on_hand= isset($buffer[13])?$this->db->escape($buffer[13]):'';
		//	   $average_quantity_pending_removal= isset($buffer[14])?$this->db->escape($buffer[14]):'';
		//	   $estimated_total_item_volume= isset($buffer[15])?$this->db->escape($buffer[15]):'';
		//	   $month_of_charge= isset($buffer[16])?$this->db->escape($buffer[16]):'';
		//	   $storage_rate= isset($buffer[17])?$this->db->escape($buffer[17]):'';
        //       $currency= isset($buffer[18])?$this->db->escape($buffer[18]):'';
		//	   $estimated_monthly_storage_fee= isset($buffer[19])?$this->db->escape($buffer[19]):'';
		//
        //
        //     $bulk_data[]="(".$asin.",".$fnsku.",".$product_name.",".$fulfillment_center.",".$country_code.",".$longest_side.",".$median_side.",".$shortest_side.",".$measurement_units.",".$weight.",".$weight_units.",".$item_volume.",".$volume_units.",".$product_size_tier.",".$average_quantity_on_hand.",".$average_quantity_pending_removal.",".$estimated_total_item_volume.",".$month_of_charge.",".$storage_rate.",".$currency.",".$estimated_monthly_storage_fee.",".$user_id.")";
        //
        //}

        if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_fba_storage_fee_data`(`asin`,`fnsku`,`product_name`,`fulfillment_center`,`country_code`,`longest_side`,`median_side`,`shortest_side`,`measurement_units`,`weight`,`weight_units`,`item_volume`,`volume_units`,`product_size_tier`,`average_quantity_on_hand`,`average_quantity_pending_removal`,`estimated_total_item_volume`,`month_of_charge`,`storage_rate`,`currency`,`estimated_monthly_storage_fee`,`usr_id`)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              asin=VALUES(asin),fnsku=VALUES(fnsku),product_name=VALUES(product_name),fulfillment_center=VALUES(fulfillment_center),country_code=VALUES(country_code),longest_side=VALUES(longest_side),median_side=VALUES(median_side),shortest_side=VALUES(shortest_side),measurement_units=VALUES(measurement_units),weight=VALUES(weight),weight_units=VALUES(weight_units),item_volume=VALUES(item_volume),volume_units=VALUES(volume_units),product_size_tier=VALUES(product_size_tier),average_quantity_on_hand=VALUES(average_quantity_on_hand),average_quantity_pending_removal=VALUES(average_quantity_pending_removal),estimated_total_item_volume=VALUES(estimated_total_item_volume),month_of_charge=VALUES(month_of_charge),storage_rate=VALUES(storage_rate),currency=VALUES(currency),estimated_monthly_storage_fee=VALUES(estimated_monthly_storage_fee),usr_id=VALUES(usr_id);";
            //print_r($qi);
			 $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `rep_fba_storage_fee_data`(`asin`,`fnsku`,`product_name`,`fulfillment_center`,`country_code`,`longest_side`,`median_side`,`shortest_side`,`measurement_units`,`weight`,`weight_units`,`item_volume`,`volume_units`,`product_size_tier`,`average_quantity_on_hand`,`average_quantity_pending_removal`,`estimated_total_item_volume`,`month_of_charge`,`storage_rate`,`currency`,`estimated_monthly_storage_fee`,`usr_id`)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              asin=VALUES(asin),fnsku=VALUES(fnsku),product_name=VALUES(product_name),fulfillment_center=VALUES(fulfillment_center),country_code=VALUES(country_code),longest_side=VALUES(longest_side),median_side=VALUES(median_side),shortest_side=VALUES(shortest_side),measurement_units=VALUES(measurement_units),weight=VALUES(weight),weight_units=VALUES(weight_units),item_volume=VALUES(item_volume),volume_units=VALUES(volume_units),product_size_tier=VALUES(product_size_tier),average_quantity_on_hand=VALUES(average_quantity_on_hand),average_quantity_pending_removal=VALUES(average_quantity_pending_removal),estimated_total_item_volume=VALUES(estimated_total_item_volume),month_of_charge=VALUES(month_of_charge),storage_rate=VALUES(storage_rate),currency=VALUES(currency),estimated_monthly_storage_fee=VALUES(estimated_monthly_storage_fee),usr_id=VALUES(usr_id);";
            //print_r($qi);
			 $this->db->query($qi);
	          unset($bulk_data);
              unset($quer);
        }
           fclose($fp);
    }
  }



   public function process_fba_shipment_replacement_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        //print_r($buffer);
        if($i>=1 && !empty($buffer[7]) )
        {

               $shipment_date= isset($buffer[0])?$this->db->escape($buffer[0]):'';
			   $sku= isset($buffer[1])?$this->db->escape($buffer[1]):'';
               $asin= isset($buffer[2])?$this->db->escape($buffer[2]):'';
               $fulfillment_center_id= isset($buffer[3])?$this->db->escape($buffer[3]):'';
               $original_fulfillment_center_id= isset($buffer[4])?$this->db->escape($buffer[4]):'';
               $quantity= isset($buffer[5])?$this->db->escape($buffer[5]):'';
               $replacement_reason_code= isset($buffer[6])?$this->db->escape($buffer[6]):'';
			   $replacement_amazon_order_id= isset($buffer[7])?$this->db->escape($buffer[7]):'';
			   $original_amazon_order_id= isset($buffer[8])?$this->db->escape($buffer[8]):'';



             $bulk_data[]="(".$shipment_date.",".$sku.",".$asin.",".$fulfillment_center_id.",".$original_fulfillment_center_id.",".$quantity.",".$replacement_reason_code.",".$replacement_amazon_order_id.",".$original_amazon_order_id.",".$user_id.")";

        }

        if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `fba_shipment_replacement_data`(shipment_date,sku,asin,fulfillment_center_id,original_fulfillment_center_id,quantity,replacement_reason_code,replacement_amazon_order_id,original_amazon_order_id,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              shipment_date=VALUES(shipment_date),sku=VALUES(sku),asin=VALUES(asin),fulfillment_center_id=VALUES(fulfillment_center_id),quantity=VALUES(quantity),replacement_reason_code=VALUES(replacement_reason_code),replacement_amazon_order_id=VALUES(replacement_amazon_order_id),original_amazon_order_id=VALUES(original_amazon_order_id),user_id=VALUES(user_id);";
            //print_r($qi);
			 $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
              $quer=implode(',',$bulk_data);
             $qi="INSERT INTO `fba_shipment_replacement_data`(shipment_date,sku,asin,fulfillment_center_id,original_fulfillment_center_id,quantity,replacement_reason_code,replacement_amazon_order_id,original_amazon_order_id,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              shipment_date=VALUES(shipment_date),sku=VALUES(sku),asin=VALUES(asin),fulfillment_center_id=VALUES(fulfillment_center_id),quantity=VALUES(quantity),replacement_reason_code=VALUES(replacement_reason_code),replacement_amazon_order_id=VALUES(replacement_amazon_order_id),original_amazon_order_id=VALUES(original_amazon_order_id),user_id=VALUES(user_id);";
            //print_r($qi);
			  $this->db->query($qi);
	          unset($bulk_data);
              unset($quer);
        }
     fclose($fp);
    }
  }




  public function process_fba_estimated_fees_txt_data($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        //print_r($buffer);
        if($i>=1 && !empty($buffer[7]))
        {
            if($country!='US')
			{
			$sku=isset($buffer[0])?$this->db->escape($buffer[0]):'';
            $fnsku=isset($buffer[1])?$this->db->escape($buffer[1]):'';
            $asin=isset($buffer[2])?$this->db->escape($buffer[2]):'';
            $product_name=isset($buffer[3])?$this->db->escape($buffer[3]):'';
            $product_group=isset($buffer[4])?$this->db->escape($buffer[4]):'';
            $brand=isset($buffer[5])?$this->db->escape($buffer[5]):'';
            $fulfilled_by=isset($buffer[6])?$this->db->escape($buffer[6]):'';
            $has_local_inventory=isset($buffer[7])?$this->db->escape($buffer[7]):'';
            $your_price=isset($buffer[8])?$this->db->escape($buffer[8]):'';
            $sales_price=isset($buffer[9])?$this->db->escape($buffer[9]):'';
            $longest_side=isset($buffer[10])?$this->db->escape($buffer[10]):'';
            $median_side=isset($buffer[11])?$this->db->escape($buffer[11]):'';
            $shortest_side=isset($buffer[12])?$this->db->escape($buffer[12]):'';
            $length_and_girth=isset($buffer[13])?$this->db->escape($buffer[13]):'';
            $unit_of_dimension=isset($buffer[14])?$this->db->escape($buffer[14]):'';
            $item_package_weight=isset($buffer[15])?$this->db->escape($buffer[15]):'';
            $unit_of_weight=isset($buffer[16])?$this->db->escape($buffer[16]):'';
            $product_size_tier=isset($buffer[17])?$this->db->escape($buffer[17]):'';
            $currency=isset($buffer[18])?$this->db->escape($buffer[18]):'';
            $estimated_fee_total=isset($buffer[19])?$this->db->escape($buffer[19]):'';
            $estimated_referral_fee_per_unit=isset($buffer[20])?$this->db->escape($buffer[20]):'';
            $estimated_variable_closing_fee=isset($buffer[21])?$this->db->escape($buffer[21]):'';
            $expected_fulfillment_fee_per_unit=isset($buffer[22])?$this->db->escape($buffer[22]):'';
            $expected_efn_fulfilment_fee_per_unit_uk=isset($buffer[23])?$this->db->escape($buffer[23]):'';
            $expected_efn_fulfilment_fee_per_unit_de=isset($buffer[24])?$this->db->escape($buffer[24]):'';
            $expected_efn_fulfilment_fee_per_unit_fr=isset($buffer[25])?$this->db->escape($buffer[25]):'';
            $expected_efn_fulfilment_fee_per_unit_it=isset($buffer[26])?$this->db->escape($buffer[26]):'';
            $expected_efn_fulfilment_fee_per_unit_es=isset($buffer[27])?$this->db->escape($buffer[27]):'';
            $estimated_pick_pack_fee_per_unit="'--'";
            $estimated_order_handling_fee_per_order="'--'";
            $estimated_weight_handling_fee_per_unit="'--'";
            $estimated_future_fee="'--'";
            $estimated_future_order_handling_fee_per_order="'--'";
            $estimated_future_pick_pack_fee_per_unit="'--'";
            $estimated_future_weight_handling_fee_per_unit="'--'";
            $expected_future_fulfillment_fee_per_unit="'--'";
			}
			else
			{
		    $sku=isset($buffer[0])?$this->db->escape($buffer[0]):'';
            $fnsku=isset($buffer[1])?$this->db->escape($buffer[1]):'';
            $asin=isset($buffer[2])?$this->db->escape($buffer[2]):'';
            $product_name=isset($buffer[3])?$this->db->escape($buffer[3]):'';
            $product_group=isset($buffer[4])?$this->db->escape($buffer[4]):'';
            $brand=isset($buffer[5])?$this->db->escape($buffer[5]):'';
            $fulfilled_by=isset($buffer[6])?$this->db->escape($buffer[6]):'';
            $your_price=isset($buffer[7])?$this->db->escape($buffer[7]):'';
            $sales_price=isset($buffer[8])?$this->db->escape($buffer[8]):'';
            $longest_side=isset($buffer[9])?$this->db->escape($buffer[9]):'';
            $median_side=isset($buffer[10])?$this->db->escape($buffer[10]):'';
            $shortest_side=isset($buffer[11])?$this->db->escape($buffer[11]):'';
            $length_and_girth=isset($buffer[12])?$this->db->escape($buffer[12]):'';
            $unit_of_dimension=isset($buffer[13])?$this->db->escape($buffer[13]):'';
            $item_package_weight=isset($buffer[14])?$this->db->escape($buffer[14]):'';
            $unit_of_weight=isset($buffer[15])?$this->db->escape($buffer[15]):'';
            $product_size_tier=isset($buffer[16])?$this->db->escape($buffer[16]):'';
            $currency=isset($buffer[17])?$this->db->escape($buffer[17]):'';
            $estimated_fee_total=isset($buffer[18])?$this->db->escape($buffer[18]):'';
            $estimated_referral_fee_per_unit=isset($buffer[19])?$this->db->escape($buffer[19]):'';
            $estimated_variable_closing_fee=isset($buffer[20])?$this->db->escape($buffer[20]):'';
            $estimated_order_handling_fee_per_order=isset($buffer[21])?$this->db->escape($buffer[21]):'';
            $estimated_pick_pack_fee_per_unit=isset($buffer[22])?$this->db->escape($buffer[22]):'';
            $estimated_weight_handling_fee_per_unit=isset($buffer[23])?$this->db->escape($buffer[23]):'';
            $expected_fulfillment_fee_per_unit=isset($buffer[24])?$this->db->escape($buffer[24]):'';
            $estimated_future_fee=isset($buffer[25])?$this->db->escape($buffer[25]):'';
            $estimated_future_order_handling_fee_per_order=isset($buffer[26])?$this->db->escape($buffer[26]):'';
            $estimated_future_pick_pack_fee_per_unit=isset($buffer[27])?$this->db->escape($buffer[27]):'';
            $estimated_future_weight_handling_fee_per_unit=isset($buffer[28])?$this->db->escape($buffer[28]):'';
            $expected_future_fulfillment_fee_per_unit=isset($buffer[29])?$this->db->escape($buffer[29]):'';
			$has_local_inventory="'--'";
			$expected_efn_fulfilment_fee_per_unit_uk="'--'";
			$expected_efn_fulfilment_fee_per_unit_de="'--'";
			$expected_efn_fulfilment_fee_per_unit_fr="'--'";
			$expected_efn_fulfilment_fee_per_unit_it="'--'";
			$expected_efn_fulfilment_fee_per_unit_es="'--'";
			}


             $bulk_data[]="(".$sku.",".$fnsku.",".$asin.",".$product_name.",".$product_group.",".$brand.",".$fulfilled_by.",".$your_price.",".$sales_price.",".$longest_side.",".$median_side.",".$shortest_side.",".$length_and_girth.",".$unit_of_dimension.",".$item_package_weight.",".$unit_of_weight.",".$product_size_tier.",".$currency.",".$estimated_fee_total.",".$estimated_referral_fee_per_unit.",".$estimated_variable_closing_fee.",".$estimated_order_handling_fee_per_order.",".$estimated_pick_pack_fee_per_unit.",".$estimated_weight_handling_fee_per_unit.",".$expected_fulfillment_fee_per_unit.",".$estimated_future_fee.",".$estimated_future_order_handling_fee_per_order.",".$estimated_future_pick_pack_fee_per_unit.",".$estimated_future_weight_handling_fee_per_unit.",".$expected_future_fulfillment_fee_per_unit.",".$has_local_inventory.",".$expected_efn_fulfilment_fee_per_unit_uk.",".$expected_efn_fulfilment_fee_per_unit_de.",".$expected_efn_fulfilment_fee_per_unit_fr.",".$expected_efn_fulfilment_fee_per_unit_it.",".$expected_efn_fulfilment_fee_per_unit_es.",".$user_id.")";

        }

        if(isset($bulk_data) && count($bulk_data)==500)
            {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `fba_estimated_fees_txt_data`(sku,fnsku,asin,product_name,product_group,brand,fulfilled_by,your_price,sales_price,longest_side,median_side,shortest_side,length_and_girth,unit_of_dimension,item_package_weight,unit_of_weight,product_size_tier,currency,estimated_fee_total,estimated_referral_fee_per_unit,estimated_variable_closing_fee,estimated_order_handling_fee_per_order,estimated_pick_pack_fee_per_unit,estimated_weight_handling_fee_per_unit,expected_fulfillment_fee_per_unit,estimated_future_fee,estimated_future_order_handling_fee_per_order,estimated_future_pick_pack_fee_per_unit,estimated_future_weight_handling_fee_per_unit,expected_future_fulfillment_fee_per_unit,has_local_inventory,expected_efn_fulfilment_fee_per_unit_uk,expected_efn_fulfilment_fee_per_unit_de,expected_efn_fulfilment_fee_per_unit_fr,expected_efn_fulfilment_fee_per_unit_it,expected_efn_fulfilment_fee_per_unit_es,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              sku=VALUES(sku),fnsku=VALUES(fnsku),asin=VALUES(asin),product_name=VALUES(product_name),product_group=VALUES(product_group),brand=VALUES(brand),fulfilled_by=VALUES(fulfilled_by),your_price=VALUES(your_price),sales_price=VALUES(sales_price),longest_side=VALUES(longest_side),median_side=VALUES(median_side),shortest_side=VALUES(shortest_side),length_and_girth=VALUES(length_and_girth),unit_of_dimension=VALUES(unit_of_dimension),item_package_weight=VALUES(item_package_weight),unit_of_weight=VALUES(unit_of_weight),product_size_tier=VALUES(product_size_tier),currency=VALUES(currency),estimated_fee_total=VALUES(estimated_fee_total),estimated_referral_fee_per_unit=VALUES(estimated_referral_fee_per_unit),estimated_variable_closing_fee=VALUES(estimated_variable_closing_fee),estimated_order_handling_fee_per_order=VALUES(estimated_order_handling_fee_per_order),estimated_pick_pack_fee_per_unit=VALUES(estimated_pick_pack_fee_per_unit),estimated_weight_handling_fee_per_unit=VALUES(estimated_weight_handling_fee_per_unit),expected_fulfillment_fee_per_unit=VALUES(expected_fulfillment_fee_per_unit),estimated_future_fee=VALUES(estimated_future_fee),estimated_future_order_handling_fee_per_order=VALUES(estimated_future_order_handling_fee_per_order),estimated_future_pick_pack_fee_per_unit=VALUES(estimated_future_pick_pack_fee_per_unit),estimated_future_weight_handling_fee_per_unit=VALUES(estimated_future_weight_handling_fee_per_unit),expected_future_fulfillment_fee_per_unit=VALUES(expected_future_fulfillment_fee_per_unit),has_local_inventory=VALUES(has_local_inventory),expected_efn_fulfilment_fee_per_unit_uk=VALUES(expected_efn_fulfilment_fee_per_unit_uk),expected_efn_fulfilment_fee_per_unit_de=VALUES(expected_efn_fulfilment_fee_per_unit_de),expected_efn_fulfilment_fee_per_unit_fr=VALUES(expected_efn_fulfilment_fee_per_unit_fr),expected_efn_fulfilment_fee_per_unit_it=VALUES(expected_efn_fulfilment_fee_per_unit_it),expected_efn_fulfilment_fee_per_unit_es=VALUES(expected_efn_fulfilment_fee_per_unit_es),user_id=VALUES(user_id);";
            //print_r($qi);
			 $this->db->query($qi);

              unset($bulk_data);
              unset($quer);
            }
            $i++;
        }
        if(isset($bulk_data) && count($bulk_data)<500 && count($bulk_data)>0)
        {
              $quer=implode(',',$bulk_data);
              $qi="INSERT INTO `fba_estimated_fees_txt_data`(sku,fnsku,asin,product_name,product_group,brand,fulfilled_by,your_price,sales_price,longest_side,median_side,shortest_side,length_and_girth,unit_of_dimension,item_package_weight,unit_of_weight,product_size_tier,currency,estimated_fee_total,estimated_referral_fee_per_unit,estimated_variable_closing_fee,estimated_order_handling_fee_per_order,estimated_pick_pack_fee_per_unit,estimated_weight_handling_fee_per_unit,expected_fulfillment_fee_per_unit,estimated_future_fee,estimated_future_order_handling_fee_per_order,estimated_future_pick_pack_fee_per_unit,estimated_future_weight_handling_fee_per_unit,expected_future_fulfillment_fee_per_unit,has_local_inventory,expected_efn_fulfilment_fee_per_unit_uk,expected_efn_fulfilment_fee_per_unit_de,expected_efn_fulfilment_fee_per_unit_fr,expected_efn_fulfilment_fee_per_unit_it,expected_efn_fulfilment_fee_per_unit_es,user_id)VALUES
              $quer
              ON DUPLICATE KEY
              UPDATE
              sku=VALUES(sku),fnsku=VALUES(fnsku),asin=VALUES(asin),product_name=VALUES(product_name),product_group=VALUES(product_group),brand=VALUES(brand),fulfilled_by=VALUES(fulfilled_by),your_price=VALUES(your_price),sales_price=VALUES(sales_price),longest_side=VALUES(longest_side),median_side=VALUES(median_side),shortest_side=VALUES(shortest_side),length_and_girth=VALUES(length_and_girth),unit_of_dimension=VALUES(unit_of_dimension),item_package_weight=VALUES(item_package_weight),unit_of_weight=VALUES(unit_of_weight),product_size_tier=VALUES(product_size_tier),currency=VALUES(currency),estimated_fee_total=VALUES(estimated_fee_total),estimated_referral_fee_per_unit=VALUES(estimated_referral_fee_per_unit),estimated_variable_closing_fee=VALUES(estimated_variable_closing_fee),estimated_order_handling_fee_per_order=VALUES(estimated_order_handling_fee_per_order),estimated_pick_pack_fee_per_unit=VALUES(estimated_pick_pack_fee_per_unit),estimated_weight_handling_fee_per_unit=VALUES(estimated_weight_handling_fee_per_unit),expected_fulfillment_fee_per_unit=VALUES(expected_fulfillment_fee_per_unit),estimated_future_fee=VALUES(estimated_future_fee),estimated_future_order_handling_fee_per_order=VALUES(estimated_future_order_handling_fee_per_order),estimated_future_pick_pack_fee_per_unit=VALUES(estimated_future_pick_pack_fee_per_unit),estimated_future_weight_handling_fee_per_unit=VALUES(estimated_future_weight_handling_fee_per_unit),expected_future_fulfillment_fee_per_unit=VALUES(expected_future_fulfillment_fee_per_unit),has_local_inventory=VALUES(has_local_inventory),expected_efn_fulfilment_fee_per_unit_uk=VALUES(expected_efn_fulfilment_fee_per_unit_uk),expected_efn_fulfilment_fee_per_unit_de=VALUES(expected_efn_fulfilment_fee_per_unit_de),expected_efn_fulfilment_fee_per_unit_fr=VALUES(expected_efn_fulfilment_fee_per_unit_fr),expected_efn_fulfilment_fee_per_unit_it=VALUES(expected_efn_fulfilment_fee_per_unit_it),expected_efn_fulfilment_fee_per_unit_es=VALUES(expected_efn_fulfilment_fee_per_unit_es),user_id=VALUES(user_id);";
              //print_r($qi);
			 $this->db->query($qi);
	          unset($bulk_data);
              unset($quer);
        }
     fclose($fp);
    }
  }



  public function process_report_data_for_testing($user_id,$report_file,$country,$request_type)
  {
    $fp=fopen($report_file,'r');
    if ($fp)
    {
     $i=0;
     while(!feof($fp))
     {
        $buffer = fgetcsv($fp,0,"\t");
        print_r($buffer);
        if($i==2)
        {
          die();
        }
        $i++;
     }

     fclose($fp);
    }
  }


}
?>
