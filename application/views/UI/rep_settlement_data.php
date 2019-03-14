<?php
$baseurl=base_url();

?>
<link href="<?php echo $baseurl.'/asset/css/datepicker.css'?>" rel="stylesheet">
<script src="<?php echo $baseurl.'/asset/js/jquery_ui_core_1_10.js'?>"></script>
<script src="<?php echo $baseurl.'/asset/js/jq_datepicker_1_10.js'?>"></script>
<script>
  $( function() {
    $(".date_selector").datepicker({minDate:0, dateFormat: "yy-mm-dd",});
  } );
</script>

<div class="page-container contanier" ng-controller='productCtrl'>   
<div id="export" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="card panel ">
                            <div class="panel-heading">
                            <button class="close" type="button" data-dismiss="modal">×</button>
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Download Data</h3>
                                
                            </div>
                            <div class="panel-body ">
                            <div class="row">
          <div class="col-sm-12">
          <a target='_blank' href='<?php echo $baseurl."asset/exportdata/" ?>{{file_name}}'>Download file</a>
</div>
</div>
</div>
</div>
</div>
</div>
<div id="myModal" class="modal fade" role="dialog" style="margin-left:150px;">
  <div class="modal-dialog modal-lg">
  <div class="card panel ">
                            <div class="panel-heading">
                            <button class="close" type="button" data-dismiss="modal">×</button>
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> ORDER DETAILS</h3>
                                
                            </div>
                              <div class="panel-body ">
                              <div class="col-sm-12">
                                <p><b>ASIN </b>: {{ord.prod_asin}}</p>
                                <p><b>Seller SKU </b>: {{ord.seller_sku}}</p>
                                <p><b>Order NO </b>: {{ord.trans_event_id}}</p>
                                


                              </div>
                              <div class="col-sm-5">
                                 <table class="table table-condensed table-striped">
                                   <tr><th colspan="2" class="text-center">Tax Details</th></tr>
								   <tr><td>Item Weight</td><td class="text-right">{{ord.itm_weight}}</td></tr>
								   <tr><td>Item Total Weight Activity</td><td class="text-right">{{ord.total_weight_activity}}</td></tr>
                                   <tr><td>Price Of Items Amt Vat Excl</td><td class="text-right">{{ord.price_of_items_amt_vat_excl}}</td></tr>
                                   <tr><td>Promo Price Of Items Amt Vat Excl</td><td class="text-right">{{ord.promo_price_of_items_amt_vat_excl}}</td></tr>
								   <tr><td>Total Price Of Items Amt Vat Excl</td><td class="text-right">{{ord.total_price_of_items_amt_vat_excl}}</td></tr>
								   <tr><td>Ship Charge Amt Vat Excl</td><td class="text-right">{{ord.ship_charge_amt_vat_excl}}</td></tr>
								   <tr><td>Promo Ship Charge Amt Vat Excl</td><td class="text-right">{{ord.promo_ship_charge_amt_vat_excl}}</td></tr>
								   <tr><td>Total Ship Charge Amt Vat Excl</td><td class="text-right">{{ord.total_ship_charge_amt_vat_excl}}</td></tr>
								   <tr><td>Gift wrap Amt Vat Excl</td><td class="text-right">{{ord.gift_wrap_amt_vat_excl}}</td></tr>
								   <tr><td>Promo Gift wrap  Amt Vat Excl</td><td class="text-right">{{ord.promo_gift_wrap_amt_vat_excl}}</td></tr>
								   <tr><td>Total Gift wrap Amt Vat Excl</td><td class="text-right">{{ord.total_ship_charge_amt_vat_excl}}</td></tr>
								   <tr><td>Total Activity Value Amt Vat Excl</td><td class="text-right">{{ord.total_activity_value_amt_vat_excl}}</td></tr>
                                  <tr><td>Price Of Items Vat Rate Percent</td><td class="text-right">{{ord.price_of_items_vat_rate_percent}}</td></tr>
			
                                   
                           <!-- <tr><td>Net Fee</td><td class="text-right">{{ord.fee}}</td></tr> -->
                                 </table>
                                 </div>
                                 <div class="col-sm-7">
                                 <table class="table table-condensed table-striped">
                                   <tr><th colspan="2" class="text-center">Tax Details</th></tr>
								    
									<tr><td>Price Of Items Vat Amt</td><td class="text-right">{{ord.price_of_items_vat_amt}}</td></tr>
									<tr><td>Promo Price Of Items Vat Amt</td><td class="text-right">{{ord.promo_price_of_items_vat_amt}}</td></tr>
									<tr><td>Total Price Of Items Vat Amt</td><td class="text-right">{{ord.total_price_of_items_vat_amt}}</td></tr>
									<tr><td>Ship Charge Vat Rate Percent</td><td class="text-right">{{ord.ship_charge_vat_rate_percent}}</td></tr>
									<tr><td>Ship Charge Vat Amt</td><td class="text-right">{{ord.ship_charge_vat_amt}}</td></tr>
									<tr><td>Promo Ship Charge Vat Amt</td><td class="text-right">{{ord.promo_ship_charge_vat_amt}}</td></tr>
									<tr><td>Total Ship Charge Vat Amt</td><td class="text-right">{{ord.total_ship_charge_vat_amt}}</td></tr>
									<tr><td>Gift Wrap Vat Rate_percent</td><td class="text-right">{{ord.gift_wrap_vat_rate_percent}}</td></tr>
									<tr><td>Gift Wrap Vat Amt</td><td class="text-right">{{ord.gift_wrap_vat_amt}}</td></tr>
									<tr><td>Gift Wrap Vat Amt</td><td class="text-right">{{ord.gift_wrap_vat_amt}}</td></tr>
									<tr><td>Promo Gift Wrap Vat Amt</td><td class="text-right">{{ord.promo_gift_wrap_vat_amt}}</td></tr>
									<tr><td>Total Gift Wrap Vat Amt</td><td class="text-right">{{ord.total_gift_wrap_vat_amt}}</td></tr>
									<tr><td>Total Activity Value Vat Amt</td><td class="text-right">{{ord.total_activity_value_vat_amt}}</td></tr>
                                  
                                   <!-- <tr><td>Net Fee</td><td class="text-right">{{ord.fee}}</td></tr> -->
                                 </table>
                                 </div>
                                 
                                 <div class="col-sm-12">
                                   <a href="#" class='btn btn-danger pull-right' data-dismiss="modal">Close</a>
                                 </div>
                              </div>
   </div>
  </div>
 </div>


    <div class="row">
    
        <div class="col-sm-12">
        <div class="card panel ">
            <div class="panel-heading">
                   <h3 class="panel-title"><i class="fa fa-database"></i> Flat File Settlement Report </h3>
            </div>
            <div class="panel-body" >
            <div class="row">
            <div class="col-sm-12">
                <div class="col-sm-5">
                                <form role="form">
                                    <div class="form-group contact-search m-b-30">
                                        <input type="text" placeholder="Search..." ng-model = 'filter.search' class="form-control" id="search">
                                        
                                    </div>
                                    <!-- form-group -->
                                </form>

                            </div>
                            <div class="col-sm-1" style="width:9%">
                              <select class="form-control" ng-model='filter.country_status'>
                                <option value="ALL">ALL</option>
                                <option value="IT">IT</option>
                                <option value="FR">FR</option>
                                <option value="DE">DE</option>
								<option value="ES">ES</option>
								<option value="UK">CO.UK</option>
                              </select> 
                            </div>
							  <div class="col-sm-1">
                              <a class='btn btn-info' id="myBtn"  ng-click='filtergrid()'>Search</a>
                            </div>
							<div class="col-sm-2">
                              <select class="form-control" name='type' ng-model='filter.order_status'>
                                <option value="CSV">CSV</option>
                                <option value="TXT">TXT</option>
                                <option value="XLS">XLS</option>
                              </select> 
                            </div>
							
							
                           <div class="col-sm-1">
							<a href='#' ng-click='export_data()'  class="btn btn-success" >Export</a>
							 </div>
						  
            <div class="col-sm-12">
                 <div class="table-responsive">
                                  <table class="table table-striped jambo_table bulk_action">
                                    <thead>
                                        <tr>
                                            <th>Order No</th>
											<th>Fullfillment</th>
										    <th>Sale amt</th>
											<th>Commission Fee</th>
                                            <th>Cost of Adv</th>
											<th>FBA Per Unit Fee</th>
											<th>FBA Weight Based Fee</th>
											<th>Refund Commission</th>
											<th>Shipping HB</th>
										    <th>Shipping Charge back</th>
										    <th>Qty</th>
											<th>Total Fee</th>
											<th>Total Settlement</th>
											<th>Country</th>
                                            
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr    ng-show='prodList.length>0' ng-repeat="tnx in prodList " >
                                            <td class="col-sm-2">{{tnx.order_id}}</td>
											<td>{{tnx.fulfilment_id}}</td>
											<td>{{tnx.sale_amt}}</td>
										    <td>{{tnx.stl_com_fee}}</td>
											<td >{{tnx.stl_cost_of_adv}}</td>
											<td >{{tnx.stl_fbau_fee}}</td>
										    <td >{{tnx.stl_fbaw_fee}}</td>
											<td >{{tnx.stl_ref_fee}}</td>
											<td >{{tnx.stl_shiphb_fee}}</td>
											<td >{{tnx.stl_shipchar_fee}}</td>
									        <td >{{tnx.qty_purchased}}</td>
											<td>{{tnx.old_fee_amt}}</td>
											<td>{{tnx.old_net_stl_amt}}</td>
											<td style="text-transform:uppercase">{{tnx.market_place}}</td>

                                        </tr>
                                        
                                    </tbody>
                                </table>
                                <h1 ng-show='prodList.length==0' class="text-center">No Transaction found</h1>
                            </div>
                        </div>  
                         <div class="col-sm-12">
                                   <ul class="pagination pull-right">
                                    <li ng-class="prevPageDisabled()">
                                      <a href="javascript:void(0)" ng-click="prevPage()">« Prev</a>
                                    </li>
                                    <li ng-repeat="n in range()" ng-class="{active: n == currentPage}" ng-click="setPage(n)">
                                      <a href="javascript:void(0)">{{n+1}}</a>
                                    </li>
                                    <li ng-class="nextPageDisabled()">
                                      <a href="javascript:void(0)" ng-click="nextPage()">Next »</a>
                                    </li>
                                  </ul>

                             </div>
                            
                 </div>         
                   
            </div>
        </div>
        
    </div>
    <div id="userModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="card panel ">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>COUPON USERS</h3>
                                
                            </div>
                            <div class="panel-body ">
                             <table class="table">
                              <tr><th>Sl.No</th><th>Name</th><th>Email</th></tr>
                              <tr ng-repeat='usr in coupon_user'>
                                <td>{{$index+1}}</td>
                                <td>{{usr.fname}} {{usr.lname}}</td>
                                <td>{{usr.umail}}</td>

                              </tr>
                             </table>

                            </div>
 </div> 
 </div>
 </div>                           



    
    
    
    </div>

</div>

<script>
var input = document.getElementById("search");
input.addEventListener("keyup", function(event) {
    event.preventDefault();
    if (event.keyCode === 13) {
        document.getElementById("myBtn").click();
    }
});
</script>
<script type="text/javascript">
crawlApp.factory('productFactory', ['$http', '$q','limitToFilter', function($http,$q,limitToFilter) {
    
    var product_list_url        =   "<?php echo $baseurl."rep_settlement_data/get_product_list/"?>";
	var d_path        =   "<?php echo $baseurl."rep_settlement_data/export_datasss/"?>";
	 var export_list_url        =   "<?php echo $baseurl."rep_settlement_data/export_data"?>";
    
    var export_data = function (search) 
    {
          var deferred = $q.defer();
          var path =export_list_url+'/'+search;
          $http.get(path)
          .success(function(data,status,headers,config){deferred.resolve(data);})
          .error(function(data, status, headers, config) { deferred.reject(status);});
          return deferred.promise;
    };
 
    
    var update_product_info=function(product_info)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'rep_settlement_data/update_product_info'?>",
                      data:{
                          product_title:product_info.product_title,
                          product_asin:product_info.product_asin,
                          actual_price:product_info.actual_price,
                          selling_price:product_info.selling_price
                      }
                     }); 
                   
    };
    
    var get_product_list = function (orderby,direction,offset,limit,search) 
    {
          var deferred = $q.defer();
          var path =product_list_url+orderby+'/'+direction+'/'+offset+'/'+limit+'/'+search;
          $http.get(path)
          .success(function(data,status,headers,config){deferred.resolve(data);})
          .error(function(data, status, headers, config) { deferred.reject(status);});
          return deferred.promise;
    };
   var download_data=function(file)
    {
      return $http({
                      method: "post",
                      url:d_path,
                      data:{
                          export_id:1
                      }
                     }); 
          

    }
    
    return {
        get_product_list:get_product_list,
		download_data:download_data,
		export_data:export_data,
        update_product_info:update_product_info
    };
    
}]);
crawlApp.controller('productCtrl', ['$scope','$parse','$window','productFactory','$http','limitToFilter',function($scope,$parse,$window,productFactory,$http,limitToFilter) {        
      $scope.prodList=[];
      
      $scope.filter={};
      $scope.filter.search='';
      $scope.filter.order_status='CSV';
	  $scope.filter.ord_status='ALL';
	  $scope.filter.itm_status='ALL';
	  $scope.filter.country_status='ALL';
      $scope.reset=function()
      {
        $scope.order={};
        $scope.order_items=[];

      }
      $scope.reset();
      $scope.ord={};
      $scope.show_order_details=function(tnx)
      {
        $scope.ord=tnx;

      }

  $scope.block_site=function()
        {
            $.blockUI({ css: { 
                border: 'none', 
                padding: '3px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff' 
            }});

        }
      
    $scope.itemsPerPage = 25;
    $scope.currentPage = 0;
    $scope.sortorder='order_id';
    $scope.direction='ASC';
    $scope.searchJSON=[];
    $scope.filterquery=[];
    $scope.order={};
    
    $scope.range = function()
    {
        var rangeSize = 8;
        var ret = [];
        var start;

        start = $scope.currentPage;

        if ( start > $scope.pageCount()-rangeSize ) {
          start = $scope.pageCount()-rangeSize;
        }

        for (var i=start; i<start+rangeSize; i++) {
          if(i>0)
          ret.push(i);
        }
        return ret;
   };

   $scope.prevPage = function()
   {
        if ($scope.currentPage > 0) 
        {
          $scope.currentPage--;
        }
   };

   $scope.prevPageDisabled = function()
   {
        return $scope.currentPage === 0 ? "disabled" : "";
   };

   $scope.nextPage = function()
   {
        if ($scope.currentPage < $scope.pageCount() - 1)
        {
          $scope.currentPage++;
        }
   };

   $scope.nextPageDisabled = function()
   {
        return $scope.currentPage === $scope.pageCount() - 1 ? "disabled" : "";
   };

   $scope.pageCount = function() 
   {
        return Math.ceil($scope.total/$scope.itemsPerPage);
   };

   $scope.setPage = function(n)
   {
        if (n > 0 && n < $scope.pageCount()) 
        {
          $scope.currentPage = n;
        }
   };

   $scope.$watch("currentPage",function(newValue, oldValue) 
   {
     $scope.get_transaction_list(newValue);
   });
   
   $scope.get_transaction_list=function(currentPage)
   {
      $scope.block_site();
      var promise= productFactory.get_product_list($scope.sortorder,$scope.direction,currentPage*$scope.itemsPerPage,$scope.itemsPerPage,$scope.searchJSON);
         promise.then(function(value){
          $.unblockUI();
         if(value.status_code==1)
         {
              
              $scope.prodList=value.datalist;
              $scope.total=value.total;
              
              
         }
         else
         {
            $scope.prodList=[];
            $scope.total=0;
            console.log(value);
             
         }     
       }, 
      function(reason) 
      {
        console.log("Reason"+reason);
      });
   }
     
   $scope.filtergrid=function()
   {
     $scope.filterquery=[
                          {searchtext:$scope.filter.search},
						  {order_status:$scope.filter.order_status},
						  {country_status:$scope.filter.country_status},
						  {ord_status:$scope.filter.ord_status},
						  {itm_status:$scope.filter.itm_status}
                        
                          
                        ];
    var argum=JSON.stringify($scope.filterquery);
    $scope.searchJSON=encodeURIComponent(argum);
    $scope.get_transaction_list(0);
  
   }
   $scope.export_data=function()
   {
    
     $scope.filterquery=[
                          {searchtext:$scope.filter.search},
						  {order_status:$scope.filter.order_status},
						  {country_status:$scope.filter.country_status}
						 
                        
                        ];
    var argum=JSON.stringify($scope.filterquery);
    $scope.searchJSON=encodeURIComponent(argum);
    $scope.block_site();
      var promise= productFactory.export_data($scope.searchJSON);
         promise.then(function(value){
          $.unblockUI();
         if(value.status_code==1)
         {
            location.href=value.download_url;            
         }
         else
         {
          swal('Error!',value.status_text,'error');
         }     
       }, 
      function(reason) 
      {
        console.log("Reason"+reason);
      });
  
   }
    $scope.export_datass=function()
    {
          swal({
                title: "Export data?",
                text: "!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, I am sure!',
                cancelButtonText: "No, cancel it!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                  $scope.block_site();
                  productFactory.download_data()
                          .success(
                                    function( html )
                                    {
                                      console.log(html);
                                      $.unblockUI(); 
                                        if(html.status_code==0)
                                        {
                                           swal('Error!',html.status_text,'error');
                                        }                    
                                        else if(html.status_code==1)
                                        {
                                          swal('Success!',html.status_text,'success');
                                          $scope.file_name=html.download_url;
                                          $('#export').modal('show');
                                           
                                        }
                                        
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
                    
                } else {
                    swal("Cancelled", "Download cancelled:)", "error");
                }
            }); 
    }
   
    $scope.calc_profit=function()
    {
      if(parseFloat($scope.cpn.actual_price) > 0 && parseFloat($scope.cpn.selling_price) > 0 )
      {
          var act_price = parseFloat($scope.cpn.actual_price);
          var sl_price = parseFloat($scope.cpn.selling_price);
          var amz_fee=sl_price * (15/100);
          var amazon_fee_deducted=sl_price-amz_fee;
          var earnings=amazon_fee_deducted-act_price;
          $scope.cpn.profit=earnings;
      }
      else
      {
        $scope.cpn.profit=''; 
      }
    }

    
   
   

}]);
</script>
