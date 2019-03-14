<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance_api extends CI_Controller
{
  public function  __construct()
	{
	     parent::__construct();
       $this->load->model('new_cron/process_finance_api','product_api');
  }

  public function product_match($user_id='')
  {
    $users=$this->product_api->get_seller_for_process(9);
    if(count($users) > 0)
    {
      foreach($users as $usr)
      {
        $this->product_api->set_credentials($usr);
        $prod_list=$this->product_api->get_product_to_match($usr['profile_id'],$usr['country_code']);
        if(!empty($prod_list))
         {
           foreach($prod_list as $prd)
           {
              if(!empty($prd['order_id']))
              {
                  sleep(2);
				         echo $prd['prod_id']."\n".$prd['order_id']."\n".$usr['country_code']."\n";
          // die;
                $res=$this->product_api->fetch_product_details($usr['profile_id'],$prd['order_id'],$usr['market_placeID'],$usr['country_code']);

 			  	if($res['status_code']==1)
 			  	{
 			  		$product[]=$res['payload'];
            echo "<pre>";print_r($product);die;

 			  		$product[]=$prd['prod_id'];
 			  		$bulk_data[]="(".$this->db->escape($prd['prod_id']).",".$this->db->escape($usr['country_code']).",".$this->db->escape($res['payload']['order_id']).",".$this->db->escape($res['payload']['principal']).",".$this->db->escape($res['payload']['tax']).",".$this->db->escape($res['payload']['giftwrap']).",".$this->db->escape($res['payload']['giftwraptax']).",".$this->db->escape($res['payload']['shippingcharge']).",".$this->db->escape($res['payload']['shippingtax']).",".$this->db->escape($res['payload']['fbafee']).",".$this->db->escape($res['payload']['commission']).",".$this->db->escape($res['payload']['fixedclosingfee']).",".$this->db->escape($res['payload']['giftwrapchargeback']).",".$this->db->escape($res['payload']['shippingchargeback']).",".$this->db->escape($res['payload']['variableclosingfee']).",".$this->db->escape($res['payload']['sku']).",".$this->db->escape($res['payload']['itemid']).",".$this->db->escape($res['payload']['marketplace']).",".$this->db->escape($res['payload']['qty']).",".$this->db->escape($res['payload']['posted_date']).",".$this->db->escape($res['payload']['promo_price1']).",".$this->db->escape($res['payload']['promo_price2']).",".$this->db->escape($res['payload']['promo_price3']).",".$this->db->escape($res['payload']['promo_price4']).",".$this->db->escape($res['payload']['promo_price5']).",".$this->db->escape($res['payload']['promo_price6']).",".$this->db->escape($usr['profile_id']).",".$this->db->escape($res['payload']['market_facilatortax_principal']).",".$this->db->escape($res['payload']['market_facilatortax_shipping']).",".$this->db->escape($res['payload']['market_facilatortax_giftwrap']).",".$this->db->escape($res['payload']['market_facilatortax_other']).",".$this->db->escape($res['payload']['taxdiscount']).",".$this->db->escape($res['payload']['cod_item_charge']).",".$this->db->escape($res['payload']['cod_item_tax_charge']).",".$this->db->escape($res['payload']['cod_order_charge']).",".$this->db->escape($res['payload']['cod_order_tax_charge']).",".$this->db->escape($res['payload']['cod_shipping_charge']).",".$this->db->escape($res['payload']['cod_shipping_tax_charge']).",".$this->db->escape($res['payload']['good_will']).",".$this->db->escape($res['payload']['restocking_fee']).",".$this->db->escape($res['payload']['return_shipping']).",".$this->db->escape($res['payload']['points_fee']).",".$this->db->escape($res['payload']['generic_deduction']).",".$this->db->escape($res['payload']['free_replace_ret_shipping']).",".$this->db->escape($res['payload']['payment_method_fee']).",".$this->db->escape($res['payload']['export_charge']).",".$this->db->escape($res['payload']['safe_t_claim']).",".$this->db->escape($res['payload']['tcs_cgst']).",".$this->db->escape($res['payload']['tcs_sgst']).",".$this->db->escape($res['payload']['tcs_igst']).",".$this->db->escape($res['payload']['tcs_utgst']).",".$this->db->escape($res['payload']['bubble_wrap_fee']).",".$this->db->escape($res['payload']['fba_cus_ret_per_order_fee']).",".$this->db->escape($res['payload']['fba_cus_ret_per_unit_fee']).",".$this->db->escape($res['payload']['fba_cus_ret_weightbased_fee']).",".$this->db->escape($res['payload']['fba_disposal_fee']).",".$this->db->escape($res['payload']['fba_fulfil_cod_fee']).",".$this->db->escape($res['payload']['fba_inb_con_fee']).",".$this->db->escape($res['payload']['fba_inb_def_fee']).",".$this->db->escape($res['payload']['fba_inb_transport_fee']).",".$this->db->escape($res['payload']['fba_inb_transport_program_fee']).",".$this->db->escape($res['payload']['fba_longterm_storage_fee']).",".$this->db->escape($res['payload']['fba_overage_fee']).",".$this->db->escape($res['payload']['fba_perorder_fulfill_fee']).",".$this->db->escape($res['payload']['fba_removal_fee']).",".$this->db->escape($res['payload']['fba_storage_fee']).",".$this->db->escape($res['payload']['fba_transport_fee']).",".$this->db->escape($res['payload']['fba_weightbased_fee']).",".$this->db->escape($res['payload']['fullfill_fee']).",".$this->db->escape($res['payload']['fullfill_network_fee']).",".$this->db->escape($res['payload']['lable_fee']).",".$this->db->escape($res['payload']['opa_bagging_fee']).",".$this->db->escape($res['payload']['poly_bagging_fee']).",".$this->db->escape($res['payload']['ssof_fullfill_fee']).",".$this->db->escape($res['payload']['taping_fee']).",".$this->db->escape($res['payload']['transport_fee']).",".$this->db->escape($res['payload']['unfullfill_fee']).")";
 			  	}
 			  	elseif($res['status_code']==3)
 			  	{
 			  		//$product[]=$res['payload'];
 			  		$product[]=$prd['prod_id'];
 			  		$bulk_data[]="(".$this->db->escape($prd['prod_id']).",".$this->db->escape($usr['country_code']).",".$this->db->escape($res['payload']['order_id']).",".$this->db->escape($res['payload']['principal']).",".$this->db->escape($res['payload']['tax']).",".$this->db->escape($res['payload']['giftwrap']).",".$this->db->escape($res['payload']['giftwraptax']).",".$this->db->escape($res['payload']['shippingcharge']).",".$this->db->escape($res['payload']['shippingtax']).",".$this->db->escape($res['payload']['fbafee']).",".$this->db->escape($res['payload']['commission']).",".$this->db->escape($res['payload']['fixedclosingfee']).",".$this->db->escape($res['payload']['giftwrapchargeback']).",".$this->db->escape($res['payload']['shippingchargeback']).",".$this->db->escape($res['payload']['variableclosingfee']).",".$this->db->escape($res['payload']['sku']).",".$this->db->escape($res['payload']['itemid']).",".$this->db->escape($res['payload']['marketplace']).",".$this->db->escape($res['payload']['qty']).",".$this->db->escape($res['payload']['posted_date']).",".$this->db->escape($res['payload']['promo_price1']).",".$this->db->escape($res['payload']['promo_price2']).",".$this->db->escape($res['payload']['promo_price3']).",".$this->db->escape($res['payload']['promo_price4']).",".$this->db->escape($res['payload']['promo_price5']).",".$this->db->escape($res['payload']['promo_price6']).",".$this->db->escape($usr['profile_id']).",".$this->db->escape($res['payload']['market_facilatortax_principal']).",".$this->db->escape($res['payload']['market_facilatortax_shipping']).",".$this->db->escape($res['payload']['market_facilatortax_giftwrap']).",".$this->db->escape($res['payload']['market_facilatortax_other']).",".$this->db->escape($res['payload']['taxdiscount']).",".$this->db->escape($res['payload']['cod_item_charge']).",".$this->db->escape($res['payload']['cod_item_tax_charge']).",".$this->db->escape($res['payload']['cod_order_charge']).",".$this->db->escape($res['payload']['cod_order_tax_charge']).",".$this->db->escape($res['payload']['cod_shipping_charge']).",".$this->db->escape($res['payload']['cod_shipping_tax_charge']).",".$this->db->escape($res['payload']['good_will']).",".$this->db->escape($res['payload']['restocking_fee']).",".$this->db->escape($res['payload']['return_shipping']).",".$this->db->escape($res['payload']['points_fee']).",".$this->db->escape($res['payload']['generic_deduction']).",".$this->db->escape($res['payload']['free_replace_ret_shipping']).",".$this->db->escape($res['payload']['payment_method_fee']).",".$this->db->escape($res['payload']['export_charge']).",".$this->db->escape($res['payload']['safe_t_claim']).",".$this->db->escape($res['payload']['tcs_cgst']).",".$this->db->escape($res['payload']['tcs_sgst']).",".$this->db->escape($res['payload']['tcs_igst']).",".$this->db->escape($res['payload']['tcs_utgst']).",".$this->db->escape($res['payload']['bubble_wrap_fee']).",".$this->db->escape($res['payload']['fba_cus_ret_per_order_fee']).",".$this->db->escape($res['payload']['fba_cus_ret_per_unit_fee']).",".$this->db->escape($res['payload']['fba_cus_ret_weightbased_fee']).",".$this->db->escape($res['payload']['fba_disposal_fee']).",".$this->db->escape($res['payload']['fba_fulfil_cod_fee']).",".$this->db->escape($res['payload']['fba_inb_con_fee']).",".$this->db->escape($res['payload']['fba_inb_def_fee']).",".$this->db->escape($res['payload']['fba_inb_transport_fee']).",".$this->db->escape($res['payload']['fba_inb_transport_program_fee']).",".$this->db->escape($res['payload']['fba_longterm_storage_fee']).",".$this->db->escape($res['payload']['fba_overage_fee']).",".$this->db->escape($res['payload']['fba_perorder_fulfill_fee']).",".$this->db->escape($res['payload']['fba_removal_fee']).",".$this->db->escape($res['payload']['fba_storage_fee']).",".$this->db->escape($res['payload']['fba_transport_fee']).",".$this->db->escape($res['payload']['fba_weightbased_fee']).",".$this->db->escape($res['payload']['fullfill_fee']).",".$this->db->escape($res['payload']['fullfill_network_fee']).",".$this->db->escape($res['payload']['lable_fee']).",".$this->db->escape($res['payload']['opa_bagging_fee']).",".$this->db->escape($res['payload']['poly_bagging_fee']).",".$this->db->escape($res['payload']['ssof_fullfill_fee']).",".$this->db->escape($res['payload']['taping_fee']).",".$this->db->escape($res['payload']['transport_fee']).",".$this->db->escape($res['payload']['unfullfill_fee']).")";
				}
 			  	if(isset($bulk_data) && count($bulk_data) == 1 )
				{
				  //print_r($bulk_data);
				   $quer=implode(',',$bulk_data);
                   $qi="INSERT INTO `finance_data` (`fin_id`,`fin_country`,`order_id`,`principal`,`tax`,`giftwrap`,`giftwraptax`,`shippingcharge`,`shippingtax`,`fbafee`,`commission`,`fixedclosingfee`,`giftwrapchargeback`,`shippingchargeback`,`variableclosingfee`,`sku`,`itemid`,`marketplace`,`qty`,`posted_date`,`promo_price1`,`promo_price2`,`promo_price3`,`promo_price4`,`promo_price5`,`promo_price6`,`added_by`,`market_facilatortax_principal`,`market_facilatortax_shipping`,`market_facilatortax_giftwrap`,`market_facilatortax_other`,`taxdiscount`,`cod_item_charge`,`cod_item_tax_charge`,`cod_order_charge`,`cod_order_tax_charge`,`cod_shipping_charge`,`cod_shipping_tax_charge`,`good_will`,`restocking_fee`,`return_shipping`,`points_fee`,`generic_deduction`,`free_replace_ret_shipping`,`payment_method_fee`,`export_charge`,`safe_t_claim`,`tcs_cgst`,`tcs_sgst`,`tcs_igst`,`tcs_utgst`,`bubble_wrap_fee`,`fba_cus_ret_per_order_fee`,`fba_cus_ret_per_unit_fee`,`fba_cus_ret_weightbased_fee`,`fba_disposal_fee`,`fba_fulfil_cod_fee`,`fba_inb_con_fee`,`fba_inb_def_fee`,`fba_inb_transport_fee`,`fba_inb_transport_program_fee`,`fba_longterm_storage_fee`,`fba_overage_fee`,`fba_perorder_fulfill_fee`,`fba_removal_fee`,`fba_storage_fee`,`fba_transport_fee`,`fba_weightbased_fee`,`fullfill_fee`,`fullfill_network_fee`,`lable_fee`,`opa_bagging_fee`,`poly_bagging_fee`,`ssof_fullfill_fee`,`taping_fee`,`transport_fee`,`unfullfill_fee`)
            	   VALUES $quer
                   ON DUPLICATE KEY
                   UPDATE
                   fin_id=VALUES(fin_id),fin_country=VALUES(fin_country),order_id=VALUES(order_id),principal=VALUES(principal),tax=VALUES(tax),giftwrap=VALUES(giftwrap),giftwraptax=VALUES(giftwraptax),shippingcharge=VALUES(shippingcharge),shippingtax=VALUES(shippingtax),fbafee=VALUES(fbafee),commission=VALUES(commission),fixedclosingfee=VALUES(fixedclosingfee),giftwrapchargeback=VALUES(giftwrapchargeback),shippingchargeback=VALUES(shippingchargeback),variableclosingfee=VALUES(variableclosingfee),sku=VALUES(sku),itemid=VALUES(itemid),marketplace=VALUES(marketplace),qty=VALUES(qty),posted_date=VALUES(posted_date),promo_price1=VALUES(promo_price1),promo_price2=VALUES(promo_price2),promo_price3=VALUES(promo_price3),promo_price4=VALUES(promo_price4),promo_price5=VALUES(promo_price5),promo_price6=VALUES(promo_price6),added_by=VALUES(added_by),market_facilatortax_principal=VALUES(market_facilatortax_principal),market_facilatortax_shipping=VALUES(market_facilatortax_shipping),market_facilatortax_giftwrap=VALUES(market_facilatortax_giftwrap),market_facilatortax_other=VALUES(market_facilatortax_other),taxdiscount=VALUES(taxdiscount),cod_item_charge=VALUES(cod_item_charge),cod_item_tax_charge=VALUES(cod_item_tax_charge),cod_order_charge=VALUES(cod_order_charge),cod_order_tax_charge=VALUES(cod_order_tax_charge),cod_shipping_charge=VALUES(cod_shipping_charge),cod_shipping_tax_charge=VALUES(cod_shipping_tax_charge),good_will=VALUES(good_will),restocking_fee=VALUES(restocking_fee),return_shipping=VALUES(return_shipping),points_fee=VALUES(points_fee),generic_deduction=VALUES(generic_deduction),free_replace_ret_shipping=VALUES(free_replace_ret_shipping),payment_method_fee=VALUES(payment_method_fee),export_charge=VALUES(export_charge),safe_t_claim=VALUES(safe_t_claim),tcs_cgst=VALUES(tcs_cgst),tcs_sgst=VALUES(tcs_sgst),tcs_igst=VALUES(tcs_igst),tcs_utgst=VALUES(tcs_utgst),bubble_wrap_fee=VALUES(bubble_wrap_fee),fba_cus_ret_per_order_fee=VALUES(fba_cus_ret_per_order_fee),fba_cus_ret_per_unit_fee=VALUES(fba_cus_ret_per_unit_fee),fba_cus_ret_weightbased_fee=VALUES(fba_cus_ret_weightbased_fee),fba_disposal_fee=VALUES(fba_disposal_fee),fba_fulfil_cod_fee=VALUES(fba_fulfil_cod_fee),fba_inb_con_fee=VALUES(fba_inb_con_fee),fba_inb_def_fee=VALUES(fba_inb_def_fee),fba_inb_transport_fee=VALUES(fba_inb_transport_fee),fba_inb_transport_program_fee=VALUES(fba_inb_transport_program_fee),fba_longterm_storage_fee=VALUES(fba_longterm_storage_fee),fba_overage_fee=VALUES(fba_overage_fee),fba_perorder_fulfill_fee=VALUES(fba_perorder_fulfill_fee),fba_removal_fee=VALUES(fba_removal_fee),fba_storage_fee=VALUES(fba_storage_fee),fba_transport_fee=VALUES(fba_transport_fee),fba_weightbased_fee=VALUES(fba_weightbased_fee),fullfill_fee=VALUES(fullfill_fee),fullfill_network_fee=VALUES(fullfill_network_fee),lable_fee=VALUES(lable_fee),opa_bagging_fee=VALUES(opa_bagging_fee),poly_bagging_fee=VALUES(poly_bagging_fee),ssof_fullfill_fee=VALUES(ssof_fullfill_fee),taping_fee=VALUES(taping_fee),transport_fee=VALUES(transport_fee),unfullfill_fee=VALUES(unfullfill_fee);";
                   $this->db->query($qi);
                   echo "\n INSERT MADED**********************\n";
				   $sql="UPDATE rep_orders_data_order_date_list SET fee_flag=1 WHERE prod_id in (";
                   $id_csv="'";
                   foreach($product as $pro_asin)
                   {
                      $id_csv.=$pro_asin."','";

                   }
                   $id_csv=rtrim($id_csv,"','");
				   //echo"$id_csv";
                   $sql=$sql.$id_csv."')";
		           $this->db->query($sql);
		           unset($quer);
   				   unset($product);
   				   unset($bulk_data);
				}

 			  }

         	}
         	if(isset($bulk_data) && count($bulk_data) > 12 )
			  {

			  	//print_r($bulk_data);
				   $quer=implode(',',$bulk_data);
                   $qi="INSERT INTO `finance_data` (`fin_id`,`fin_country`,`order_id`,`principal`,`tax`,`giftwrap`,`giftwraptax`,`shippingcharge`,`shippingtax`,`fbafee`,`commission`,`fixedclosingfee`,`giftwrapchargeback`,`shippingchargeback`,`variableclosingfee`,`sku`,`itemid`,`marketplace`,`qty`,`posted_date`,`promo_price1`,`promo_price2`,`promo_price3`,`promo_price4`,`promo_price5`,`promo_price6`,`added_by`,`market_facilatortax_principal`,`market_facilatortax_shipping`,`market_facilatortax_giftwrap`,`market_facilatortax_other`,`taxdiscount`,`cod_item_charge`,`cod_item_tax_charge`,`cod_order_charge`,`cod_order_tax_charge`,`cod_shipping_charge`,`cod_shipping_tax_charge`,`good_will`,`restocking_fee`,`return_shipping`,`points_fee`,`generic_deduction`,`free_replace_ret_shipping`,`payment_method_fee`,`export_charge`,`safe_t_claim`,`tcs_cgst`,`tcs_sgst`,`tcs_igst`,`tcs_utgst`,`bubble_wrap_fee`,`fba_cus_ret_per_order_fee`,`fba_cus_ret_per_unit_fee`,`fba_cus_ret_weightbased_fee`,`fba_disposal_fee`,`fba_fulfil_cod_fee`,`fba_inb_con_fee`,`fba_inb_def_fee`,`fba_inb_transport_fee`,`fba_inb_transport_program_fee`,`fba_longterm_storage_fee`,`fba_overage_fee`,`fba_perorder_fulfill_fee`,`fba_removal_fee`,`fba_storage_fee`,`fba_transport_fee`,`fba_weightbased_fee`,`fullfill_fee`,`fullfill_network_fee`,`lable_fee`,`opa_bagging_fee`,`poly_bagging_fee`,`ssof_fullfill_fee`,`taping_fee`,`transport_fee`,`unfullfill_fee`)
            	   VALUES $quer
                   ON DUPLICATE KEY
                   UPDATE
                   fin_id=VALUES(fin_id),fin_country=VALUES(fin_country),order_id=VALUES(order_id),principal=VALUES(principal),tax=VALUES(tax),giftwrap=VALUES(giftwrap),giftwraptax=VALUES(giftwraptax),shippingcharge=VALUES(shippingcharge),shippingtax=VALUES(shippingtax),fbafee=VALUES(fbafee),commission=VALUES(commission),fixedclosingfee=VALUES(fixedclosingfee),giftwrapchargeback=VALUES(giftwrapchargeback),shippingchargeback=VALUES(shippingchargeback),variableclosingfee=VALUES(variableclosingfee),sku=VALUES(sku),itemid=VALUES(itemid),marketplace=VALUES(marketplace),qty=VALUES(qty),posted_date=VALUES(posted_date),promo_price1=VALUES(promo_price1),promo_price2=VALUES(promo_price2),promo_price3=VALUES(promo_price3),promo_price4=VALUES(promo_price4),promo_price5=VALUES(promo_price5),promo_price6=VALUES(promo_price6),added_by=VALUES(added_by),market_facilatortax_principal=VALUES(market_facilatortax_principal),market_facilatortax_shipping=VALUES(market_facilatortax_shipping),market_facilatortax_giftwrap=VALUES(market_facilatortax_giftwrap),market_facilatortax_other=VALUES(market_facilatortax_other),taxdiscount=VALUES(taxdiscount),cod_item_charge=VALUES(cod_item_charge),cod_item_tax_charge=VALUES(cod_item_tax_charge),cod_order_charge=VALUES(cod_order_charge),cod_order_tax_charge=VALUES(cod_order_tax_charge),cod_shipping_charge=VALUES(cod_shipping_charge),cod_shipping_tax_charge=VALUES(cod_shipping_tax_charge),good_will=VALUES(good_will),restocking_fee=VALUES(restocking_fee),return_shipping=VALUES(return_shipping),points_fee=VALUES(points_fee),generic_deduction=VALUES(generic_deduction),free_replace_ret_shipping=VALUES(free_replace_ret_shipping),payment_method_fee=VALUES(payment_method_fee),export_charge=VALUES(export_charge),safe_t_claim=VALUES(safe_t_claim),tcs_cgst=VALUES(tcs_cgst),tcs_sgst=VALUES(tcs_sgst),tcs_igst=VALUES(tcs_igst),tcs_utgst=VALUES(tcs_utgst),bubble_wrap_fee=VALUES(bubble_wrap_fee),fba_cus_ret_per_order_fee=VALUES(fba_cus_ret_per_order_fee),fba_cus_ret_per_unit_fee=VALUES(fba_cus_ret_per_unit_fee),fba_cus_ret_weightbased_fee=VALUES(fba_cus_ret_weightbased_fee),fba_disposal_fee=VALUES(fba_disposal_fee),fba_fulfil_cod_fee=VALUES(fba_fulfil_cod_fee),fba_inb_con_fee=VALUES(fba_inb_con_fee),fba_inb_def_fee=VALUES(fba_inb_def_fee),fba_inb_transport_fee=VALUES(fba_inb_transport_fee),fba_inb_transport_program_fee=VALUES(fba_inb_transport_program_fee),fba_longterm_storage_fee=VALUES(fba_longterm_storage_fee),fba_overage_fee=VALUES(fba_overage_fee),fba_perorder_fulfill_fee=VALUES(fba_perorder_fulfill_fee),fba_removal_fee=VALUES(fba_removal_fee),fba_storage_fee=VALUES(fba_storage_fee),fba_transport_fee=VALUES(fba_transport_fee),fba_weightbased_fee=VALUES(fba_weightbased_fee),fullfill_fee=VALUES(fullfill_fee),fullfill_network_fee=VALUES(fullfill_network_fee),lable_fee=VALUES(lable_fee),opa_bagging_fee=VALUES(opa_bagging_fee),poly_bagging_fee=VALUES(poly_bagging_fee),ssof_fullfill_fee=VALUES(ssof_fullfill_fee),taping_fee=VALUES(taping_fee),transport_fee=VALUES(transport_fee),unfullfill_fee=VALUES(unfullfill_fee);";
                   $this->db->query($qi);
                   echo "\n INSERT MADED**********************\n";
				   $sql="UPDATE rep_orders_data_order_date_list SET fee_flag=1 WHERE prod_id in (";
                   $id_csv="'";
                   foreach($product as $pro_asin)
                   {
                      $id_csv.=$pro_asin."','";

                   }
                   $id_csv=rtrim($id_csv,"','");
				   //echo"$id_csv";
                   $sql=$sql.$id_csv."')";
		           $this->db->query($sql);
		           unset($quer);
   				   unset($product);
   				   unset($bulk_data);
			  }
		    echo date('y-m-d h:i:s')."\n";
              }
           }
         }
$this->rerun_finace_empty();
      }



	public function rerun_finace_empty()
{
	$sql="UPDATE rep_orders_data_order_date_list SET fee_flag='0' where prod_id in(SELECT fin_id from finance_data where sku='')";
	//print_r($sql);
	$this->db->query($sql);
}



    }
