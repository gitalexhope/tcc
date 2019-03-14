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


<div class="page-container contanier" ng-controller='transactionCtrl'>   
    <div class="row">
<div id="myModal" class="modal fade" role="dialog"  style="margin-left:100px;">
  <div class="modal-dialog modal-lg">
  <div class="card panel ">
                            <div class="panel-heading">
                            <button class="close" type="button" data-dismiss="modal">×</button>
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> ORDER DETAILS</h3>
                                
                            </div>
                              <div class="panel-body ">
                              <div class="col-sm-12">
                                <p><b>Product Name </b>: {{ord.itm_title}}</p>

                                <p><b>Seller SKU </b>: {{ord.seller_sku}}</p>
                                <p><b>Order NO </b>: {{ord.order_no}}</p>
                                


                              </div>
                              <div class="col-sm-5">
                                 <table class="table table-condensed table-striped">
                                   <tr><th colspan="2" class="text-center">Product Details</th></tr>
                                   <tr><td>Order No</td><td class="text-right">{{ord.order_no}}</td></tr>
                                   <tr><td>Purchase Date</td><td class="text-right">{{ord.purchase_date}}</td></tr>
                                   <tr><td>Expected Ship Date</td><td class="text-right">{{ord.calc_shipdate}}</td></tr>
                                   <tr><td>Expected Delivery Date</td><td class="text-right">{{ord.calc_deliverydate}}</td></tr>
                                   <tr><td>Seller SKU</td><td class="text-right">{{ord.seller_sku}}</td></tr>
                                   <tr><td>Quantity</td><td class="text-right">{{ord.no_of_item}}</td></tr>
                                   <tr><td>ASIN</td><td class="text-right">{{ord.asin}}</td></tr>
                                   <tr><td>Item Price</td><td class="text-right">{{ord.itm_price}}</td></tr>
                                   <tr><td>Shipping Price</td><td class="text-right">{{ord.itm_ship_price}}</td></tr>
                                   
                                   <tr><td>Order Status</td><td class="text-right"> <span ng-if="ord.order_status=='Shipped'" class='label label-success'>{{ord.order_status}}</span>
                                                <span ng-if="ord.order_status=='Unshipped'" class='label label-warning'>{{ord.order_status}}</span>
                                                <span ng-if="ord.order_status=='Canceled'" class='label label-danger'>{{ord.order_status}}</span>
                                                <span ng-if="ord.order_status=='PartiallyShipped'" class='label label-info'>{{ord.order_status}}</span>
                                                <span ng-if="ord.order_status=='Pending'" class='label label-danger'>{{ord.order_status}}</span></td></tr>

                                   <!-- <tr><td>Net Fee</td><td class="text-right">{{ord.fee}}</td></tr> -->
                                 </table>
                                 </div>
                                 <div class="col-sm-7">
                                 <table class="table table-condensed table-striped">
                                   <tr><th colspan="2" class="text-center">Buyer Details</th></tr>
                                   <tr><td>Buyer Name</td><td class="text-right">{{ord.buyer_name}}</td></tr>
                                   <tr><td>Buyer Email</td><td class="text-right">{{ord.buyer_email}}</td></tr>
                                   
                                   <tr><td>Address</td><td class="text-right">{{ord.shipping_addr1}}</td></tr>
                                   <tr><td>City</td><td class="text-right">{{ord.shipping_city}}</td></tr>
                                   <tr><td>Zip</td><td class="text-right">{{ord.shipping_zip}}</td></tr>
                                   <tr><td>State </td><td class="text-right">{{ord.shipping_state}}</td></tr>
                                   <tr><td>Country</td><td class="text-right">{{ord.shipping_country}}</td></tr>
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
    
        <div class="col-sm-12">
        <div class="card panel ">
            <div class="panel-heading">
                   <h3 class="panel-title"><i class="fa fa-truck"></i>Amazon Orders</h3>
            </div>
            <div class="panel-body" >
            <div class="row">
            <div class="col-sm-12">
                            <div class="col-sm-5">
                                <form role="form">
                                    <div class="form-group contact-search m-b-30">
                                        <input type="text" placeholder="Order No,Buyer Name,Title,SKU" ng-model = 'filter.search' ng-enter='filtergrid()' class="form-control" id="search">
                                        
                                    </div>
                                    <!-- form-group -->
                                </form>

                            </div>
                            <div class="col-sm-2">
                              <select class="form-control" ng-model='filter.order_status'>
                                <option value="ALL">ALL</option>
                                <option value="SHI">Shipped</option>
                                <option value="UNS">Unshipped</option>
                                <option value="CAN">Cancelled</option>
                                <option value="PEN">Pending</option>
                              </select>
                            </div>
                            
                            <!-- <div class="col-sm-1 no-padding">  
                                  <input type='text' class='form-control date_selector'  jqdatepicker name='from' placeholder='From' ng-model='filter.frm_date'>
                            </div>
                            <div class="col-sm-1 no-padding">  
                                    <input type='text' class='form-control date_selector'  jqdatepicker name='to' placeholder='To' ng-model='filter.to_date'>
                            </div> -->
                            <div class="col-sm-2">
                                                               <button class="btn btn-default" popover-trigger="'outsideClick'" uib-popover-template="date_filter_tmpl" popover-title="Date Filter" popover-placement="bottom-left" style="width:100%">Filter By Date</button>
                            <!-- <input type="text" class="form-control" ng-model='dt_text' popover-trigger="'outsideClick'" ng-popover-trigger="'outsideClick'" uib-popover-template="date_filter_tmpl" popover-title="Date Filter" popover-placement="bottom-left"> -->
 <script type="text/ng-template" id="date_filter_tmpl.html">
        
     <div style="padding: 5px 5px; "><a href='#'  ng-click='filter.date_rng="today";filtergrid()'>Today</a></div>
     <div style="padding: 5px 5px; "><a href='#'  ng-click='filter.date_rng="7 days";filtergrid()'>Last 7 Days</a></div>
     <div style="padding: 5px 5px; "><a href='#'  ng-click='filter.date_rng="30 days";filtergrid()'>Last 30 Days</a></div>
     <div style="padding: 5px 5px; "><a href='#'  ng-click='filter.date_rng="this month";filtergrid()'>This month</a></div>
     <div style="padding: 5px 5px; "><a href='#' ng-click='filter.date_rng="last month";filtergrid()'>Last Month</a></ul></div>
   <div style="margin-top:5px">
     <div class="col-sm-6 no-padding">  
     <input type='text' class='form-control date_selector'  jqdatepicker name='from' placeholder='From' ng-model='filter.frm_date'>
   </div>
   <div class="col-sm-6 no-padding">  
     <input type='text' class='form-control date_selector'  jqdatepicker name='to' placeholder='To' ng-model='filter.to_date'>
   </div>
   </div>
   <div class="col-sm-6 no-padding" style="margin-top: 5px">  
     <a href='#' class="btn btn-info" style='width:100%' ng-click="filter.frm_date='';filter.to_date=''">Reset</a>
   </div>
   
   <div class="col-sm-6 no-padding" style="margin-top: 5px">  
     <a href='#' class="btn btn-info" style='width:100%' ng-click='filtergrid()'>Filter</a>
   </div>
   
        
    </script>
                            </div>
   
                            <div class="col-sm-1">
                              <a class='btn btn-info' ng-click='filtergrid()'>Search</a>
                            </div>
            </div>                
            <div class="col-sm-12">
                 <div class="table-responsive">
                               <table class="table table-striped jambo_table bulk_action">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>PO Date</th>
                                            <th>Buyer</th>
                                            <th>Title</th>
                                            <th>SKU</th>
                                            <th>Qty</th>
                                            <!-- <th>ASIN</th> -->
                                            <th>Ship Date</th>
                                            <th>Delive Date</th>
                                            <th>Status</th>
                                           
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr  ng-show='transactionList.length==0'>
                                          <td colspan="9" class="text-center"><h2>No Order found</h2></td>
                                        </tr>
                                        <tr  ng-click='show_order_details(tnx)' data-target="#myModal" data-toggle="modal"  ng-show='transactionList.length>0' ng-repeat="tnx in transactionList " >
                                            <td class="col-sm-2">{{tnx.order_no}}</td>
                                            <td class="col-sm-1">{{tnx.purchase_date}}</td>
                                            <td>{{tnx.buyer_name}}<span ng-if="tnx.buyer_name.length==0 ">-------</span></td>
                                            <td>{{tnx.itm_title | limitTo:45}}</td>
                                            <td>{{tnx.seller_sku}}</td>
                                            <td>{{tnx.no_of_item}}</td>
                                            <!-- <td>{{tnx.asin}}</td> -->
                                            
                                            <td class="col-sm-1">{{tnx.calc_shipdate}}</td>
                                            <td class="col-sm-1">{{tnx.calc_deliverydate}}</td>
                                            <td>
                                            <!-- <span class='label label-success'>{{tnx.order_status}}</span> -->
                                                <span ng-if="tnx.order_status=='Shipped'" class='label label-success'>{{tnx.order_status}}</span>
                                                <span ng-if="tnx.order_status=='Unshipped'" class='label label-warning'>{{tnx.order_status}}</span>
                                                <span ng-if="tnx.order_status=='Canceled'" class='label label-danger'>{{tnx.order_status}}</span>
                                                <span ng-if="tnx.order_status=='PartiallyShipped'" class='label label-info'>{{tnx.order_status}}</span>
                                                <span ng-if="tnx.order_status=='Pending'" class='label label-danger'>{{tnx.order_status}}</span>
                                            </td>
                                         
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>  
                         <div class="col-sm-12">
                         <div class="col-sm-2">
                           <select class='form-control' ng-model='itm_per' ng-change='change_item_per_page()'>
                             <option value='10'>10</option>
                             <option value='15'>15</option>
                             <option value='20'>20</option>
                             <option value='25'>25</option>
                             <option value='50'>50</option>
                             <option value='100'>100</option>
                             <option value='200'>200</option>
                           </select>
                         </div>
                         <div class="col-sm-3">
                         <span class="btn btn-info">{{currentPage * itemsPerPage}} -to- {{(currentPage * itemsPerPage) + itemsPerPage}}-Of- {{total}}</span>
                         </div>
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
<script type="text/javascript">
crawlApp.factory('transactionFactory', ['$http', '$q','limitToFilter', function($http,$q,limitToFilter) {
    
    var order_list_url        =   "<?php echo $baseurl."amazon_order_list/get_order_list/"?>";
    
    
    var get_transaction_list = function (orderby,direction,offset,limit,search) 
    {
          var deferred = $q.defer();
          var path =order_list_url+orderby+'/'+direction+'/'+offset+'/'+limit+'/'+search;
          $http.get(path)
          .success(function(data,status,headers,config){deferred.resolve(data);})
          .error(function(data, status, headers, config) { deferred.reject(status);});
          return deferred.promise;
    };
   
    
    return {
        get_transaction_list:get_transaction_list,
        
        
    };
    
}]);
crawlApp.controller('transactionCtrl', ['$scope','$parse','$window','transactionFactory','$http','limitToFilter',function($scope,$parse,$window,transactionFactory,$http,limitToFilter) {        
      $scope.transactionList=[];
      $scope.date_filter_tmpl="date_filter_tmpl.html";
      $scope.outstanding='';
      $scope.filter={};
      $scope.filter.search='';
      $scope.filter.date_rng='';
      $scope.filter.order_status='ALL';
      $scope.filter.tfm_status='ALL';
      $scope.dt_text='';
      $scope.ord={};
      $scope.show_order_details=function(tnx)
      {
        $scope.ord=tnx;

      }
      $scope.reset=function()
      {
        $scope.order={};
        $scope.order_items=[];

      }
      $scope.reset();
   

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
    $scope.itm_per='25';
    $scope.currentPage = 0;
    $scope.sortorder='purchase_date';
    $scope.direction='DESC';
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
      var promise= transactionFactory.get_transaction_list($scope.sortorder,$scope.direction,currentPage*$scope.itemsPerPage,$scope.itemsPerPage,$scope.searchJSON);
         promise.then(function(value){
          $.unblockUI();
         if(value.status_code==1)
         {
              
              $scope.transactionList=value.datalist;
              $scope.total=value.total;
              $scope.outstanding=value.outstanding;
              
         }
         else
         {
            $scope.transactionList=[];
            $scope.total=0;
            $scope.outstanding=value.outstanding;
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
    
    if(angular.isDefined($scope.filter.frm_date) && angular.isDefined($scope.filter.to_date) && $scope.filter.frm_date.length > 0 && $scope.filter.to_date.length > 0)
    {
      $scope.dt_text="ORDERS BETWEEN ["+$scope.filter.frm_date+"] To ["+$scope.filter.to_date+"]";
    }
    
     $scope.filterquery=[
                          {searchtext:$scope.filter.search},
                          {order_status:$scope.filter.order_status},
                          {from_date:$scope.filter.frm_date},
                          {to_date:$scope.filter.to_date},
                          {tfm_status:$scope.filter.tfm_status},
                          {date_rng:$scope.filter.date_rng}
                          
                        ];
    var argum=JSON.stringify($scope.filterquery);
    $scope.searchJSON=encodeURIComponent(argum);
    $scope.get_transaction_list(0);
  
   }

   $scope.change_item_per_page=function()
   {
    
    $scope.itemsPerPage=parseInt($scope.itm_per);
    $scope.get_transaction_list($scope.currentPage);
   }
   

    
   
   

}]);
</script>
<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover({
    html: true, 
  content: function() {
          return $('#popover-content').html();
        }
});   
});
</script>
