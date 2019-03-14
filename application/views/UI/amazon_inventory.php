<?php
$baseurl=base_url();
$base_url=$baseurl;
?>
<style type="text/css">
.hiddenRow {
    padding: 0 !important;
}
/* TABLES */
.user-list .tbl_body td > img {
    position: relative;
  max-width: 50px;
  float: left;
  margin-right: 15px;
}
.user-list .tbl_body td .user-link {
  display: block;
  font-size: 1.25em;
  padding-top: 3px;
  margin-left: 60px;
}
.user-list .tbl_body td .user-subhead {
  font-size: 1.0em;
  /*font-style: italic;*/
}

.table_custom {
    border-collapse: separate;
    width: 100%;
}
/*.table-hover > tbody > tr:hover > td,
.table-hover > tbody > tr:hover > th {
  background-color: #eee;
}*/
.table_custom .tbl-head > tr > th {
  background: #fff;
  color:#344644;
  border-bottom: 1px solid #C2C2C2;
  padding: 10px;
}
.table_custom .tbl_body > tr > td {
  font-size: 0.885em;
  background: #fff;
  border-top: 10px solid #f5f5f5;
  vertical-align: middle;
  padding: 12px 8px;
}
.table_custom .tbl_body > tr > td:first-child,
.table_custom .tbl-head > tr > th:first-child {
  padding-left: 20px;
}
.table_custom .tbl-head > tr > th span {
  border-bottom: 2px solid #C2C2C2;
  display: inline-block;
  padding: 0 5px;
  padding-bottom: 5px;
  font-weight: normal;
}
.table_custom .tbl-head > tr > th > a span {
  color: #344644;
}
.table_custom .tbl-head > tr > th > a span:after {
  content: "\f0dc";
  font-family: FontAwesome;
  font-style: normal;
  font-weight: normal;
  text-decoration: inherit;
  margin-left: 5px;
  font-size: 0.75em;
}
.table_custom .tbl-head > tr > th > a.asc span:after {
  content: "\f0dd";
}
.table_custom .tbl-head > tr > th > a.desc span:after {
  content: "\f0de";
}
.table_custom .tbl-head > tr > th > a:hover span {
  text-decoration: none;
  color: #2bb6a3;
  border-color: #2bb6a3;
}
.table_custom.table-hover .tbl_body > tr > td {
  -webkit-transition: background-color 0.15s ease-in-out 0s;
  transition: background-color 0.15s ease-in-out 0s;
}
.table_custom .tbl_body tr td .call-type {
  display: block;
  font-size: 0.75em;
  text-align: center;
}
.table_custom .tbl_body tr td .first-line {
  line-height: 1.5;
  font-weight: 400;
  font-size: 1.125em;
}
.table_custom .tbl_body tr td .first-line span {
  font-size: 0.875em;
  color: #969696;
  font-weight: 300;
}
.table_custom .tbl_body tr td .second-line {
  font-size: 0.875em;
  line-height: 1.2;
}
.table_custom a.table-link {
  margin: 0 5px;
  font-size: 1.125em;
}
.table_custom a.table-link:hover {
  text-decoration: none;
  color: #2aa493;
}
.table_custom a.table-link.danger {
  color: #fe635f;
}
.table_custom a.table-link.danger:hover {
  color: #dd504c;
}

.table-products .tbl_body > tr > td {
  background: none;
  border: none;
  border-bottom: 1px solid #ebebeb;
  -webkit-transition: background-color 0.15s ease-in-out 0s;
  transition: background-color 0.15s ease-in-out 0s;
  position: relative;
}
.table-products .tbl_body > tr:hover > td {
  text-decoration: none;
  background-color: #f6f6f6;
}
.table-products .name {
  display: block;
  font-weight: 600;
  padding-bottom: 7px;
}
.table-products .price {
  display: block;
  text-decoration: none;
  width: 50%;
  float: left;
  font-size: 0.875em;
}
.table-products .price > i {
  color: #8dc859;
}
.table-products .warranty {
  display: block;
  text-decoration: none;
  width: 50%;
  float: left;
  font-size: 0.875em;
}
.table-products .warranty > i {
  color: #f1c40f;
}
.table_custom .tbl_body > tr.table-line-fb > td {
  background-color: #9daccb;
  color: #262525;
}
.table_custom .tbl_body > tr.table-line-twitter > td {
  background-color: #9fccff;
  color: #262525;
}
.table_custom .tbl_body > tr.table-line-plus > td {
  background-color: #eea59c;
  color: #262525;
}
.table-stats .status-social-icon {
  font-size: 1.9em;
  vertical-align: bottom;
}
.table-stats .table-line-fb .status-social-icon {
  color: #556484;
}
.table-stats .table-line-twitter .status-social-icon {
  color: #5885b8;
}
.table-stats .table-line-plus .status-social-icon {
  color: #a75d54;
}
.ord_tbl tbody > tr > td {
  font-size: 1.09em;
}
</style>
<div class="page-container contanier" ng-controller='invCtrl'>   
<div id="campaign" class="modal fade" role="dialog">
 <div class="modal-dialog">
  <div class="card panel ">
      <div class="panel-heading">
      <button class="close" type="button" data-dismiss="modal">×</button>
          <h3 class="panel-title"><i class="fa fa-envelope"></i>Campaign List</h3>
          
      </div>
      <div class="panel-body ">
        <div class="row">
          <div class="col-sm-12">
          <div class='col-sm-6 no-padding'><p class="text-info"><b>ASIN : {{selected_prod.prod_asin}}</b></p></div><div class='col-sm-6 no-padding'><p><b>SKU : {{selected_prod.prod_sku}}</b></p></div>
          <table class="table table-bordered">
          <tr>
              <th><div class="custom-checkbox">
              <input type='checkbox' id="iCheckbox" class="checkbox-green" ng-model="checkStatus" ng-change="statusCheck()" ng-true-value="'Y'" ng-false-value="'N'"/>
              <label for="iCheckbox"></label>
              </div></th>
              <th>Campaign Name</th>
          </tr>
          <tr ng-repeat='cmp in campList'>

            <td> <div class="custom-checkbox">
                   <input type="checkbox" checklist-value="cmp.cpgn_id" checklist-model="selectedCamp" class="checkbox-blue" id="inlineCheckbox{{$index+1}}">
                   <label for="inlineCheckbox{{$index+1}}"></label>
                 </div>
                 </td>
            <td>{{cmp.cpgn_name}}</td>
          </tr>
          </table>
            
          </div>
          <div class="col-sm-12">
          <button class="btn btn-info" ng-click='update_campaign_details()'>Update</button>
          </div>
        </div>
      </div>
  </div>
 </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="card panel ">
                            <div class="panel-heading">
                            <button class="close" type="button" data-dismiss="modal">×</button>
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Add Product</h3>
                                
                            </div>
                            <div class="panel-body ">
                            <div class="row">
          <div class="col-sm-12">
          <form  ng-submit="update_product_info()" name='amzForm' novalidate>
                          <div class="col-sm-12">
                            <div class="col-sm-12 no-padding">
                              <div class="pad"><b>Product Title</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.product_title.$invalid && amz_submitted  }" >
                              <input type='text' class='form-control'  name='product_title' placeholder='Title' ng-model='cpn.prod_title' required>
                              </div>
                            </div>
                            <div class="col-sm-12 no-padding">
                              <div class="pad"><b>Product ASIN</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.product_asin.$invalid && amz_submitted  }" >
                              <input type='text' class='form-control'  name='product_asin' placeholder='ASIN' ng-model='cpn.prod_asin' required>
                              </div>
                             </div>
                            <div class="col-sm-12 no-padding">
                              <div class="pad"><b>Product SKU</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.product_sku.$invalid && amz_submitted  }" >
                              <input type='text' class='form-control'  name='product_sku' placeholder='SKU' ng-model='cpn.prod_sku' required>
                              </div>
                             </div>
                            
                            <div class="col-sm-12 no-padding">
                              <div class="pad"><b>Actual Price</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.actual_price.$invalid && amz_submitted  }" >
                              <input type='text' class='form-control'  name='actual_price' placeholder='Actual Price' ng-model='cpn.act_price' required ng-change='calc_profit()'>
                              </div>
                             </div>
                             <div class="col-sm-12 no-padding">
                              <div class="pad"><b>Selling Price</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.selling_price.$invalid && amz_submitted  }">
                              <input type='text' class='form-control'  name='selling_price'  placeholder='Selling Price' ng-model='cpn.itm_price' required  ng-change='calc_profit()'>
                              </div>
                             </div>

                            <div class="col-sm-12 no-padding">
                              <div class="pad"><b>Calculated Profit</b></div>
                              <div class="pad">
                              <input type='text' class='form-control'  placeholder='Profit' ng-model='cpn.profit'>
                              </div>
                             </div>
                             
                            
                            
                    </div>
                            
                            <div class="col-sm-12">
                             
                              <div class="pad" >
                              <br>
                              <input  type='reset' name='reset'  value='Reset Details' ng-click="reset()" class='btn btn-danger'>
                              <input type='submit' name='submit'  value='Update Product Details' ng-click="amz_submitted=true" class='btn btn-info'>
                              </div>
                           </div>   
                              
          </div>
        </div>
                            </div>
                        </div>
  </div>
</div>
    <div class="row">
    <div class="col-sm-12" style="margin-left: 20px;">
        <div class="card panel ">
            <div class="panel-heading">
                   <h3 class="panel-title"><i class="fa fa-truck"></i>Amazon Inventory</h3>
            </div>
            <div class="panel-body" >
            <div class="row">
        
     <div class="col-sm-12" >
                <div class="col-sm-4">
                                <form role="form">
                                    <div class="form-group contact-search m-b-30">
                                        <input type="text" placeholder="Search..." ng-model = 'filter.search' class="form-control" id="search">
                                        
                                    </div>
                                    <!-- form-group -->
                                </form>

                            </div>
                            <div class="col-sm-2">
                              <select class="form-control" ng-model='filter.list_status'>
                                <option value="ALL">ALL</option>
                                <option value="ACT">Active</option>
                                <option value="INAC">Inactive</option>
                                <option value="DEL">Deleted</option>
                              </select>
                            </div>
							<div class="col-sm-1" style="width:9%">
                              <select class="form-control" ng-model='filter.country_status'>
                                <option value="ALL">ALL</option>
                                <option value="IT">IT</option>
                                <option value="FR">FR</option>
                                <option value="DE">DE</option>
								<option value="ES">ES</option>
								<option value="UK">UK</option>
                              </select> 
                            </div>
                            
                            <div class="col-sm-1">
                              <a class='btn btn-info' ng-click='filtergrid()'>Search</a>
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
                            <div class="col-sm-5">
                           <!--- <div class="col-sm-6"><div class="pull-right"><a href='#' class="btn btn-success"  ng-click='sync_inventory()'>Sync</a></div></div> 
                            <div class="col-sm-6"><div class="pull-right"><a href='#' class="btn btn-primary"  data-toggle="modal" ng-click='reset()' data-target="#myModal">ADD Product</a></div></div> --->
                            </div>
      </div>   
      </div>
      </div>
      </div>
      </div>             
    <div class="col-sm-12" style="margin-left: 20px">
      <div class="main-box clearfix">
      <div class="table-responsive">
      
        <table class="table_custom  user-list">
          <thead style='background-color:'class='tbl-head table-bordered'>
            <tr>
              <th>Product</th>
			  <th>SKU</th>
			  <th>ASIN</th>
			  <th>Brand</th>
              <th ng-click="change_order('qty')">Qty <i class="fa " ng-class="sortorder=='qty' && direction=='ASC'?'fa-sort-numeric-asc':'fa-sort-numeric-desc'"></i></th>
                                            <!-- <th>Actual Price</th> -->
                                            <th ng-click="change_order('price')">Sell Price <i class="fa " ng-class="sortorder=='price' && direction=='ASC'?'fa-sort-numeric-asc':'fa-sort-numeric-desc'"></i></th>
                                            <!-- <th>Profit</th> -->
                                           <!--  <th ng-click="change_order('sold')">Sold Qty<i class="fa " ng-class="sortorder=='sold' && direction=='ASC'?'fa-sort-numeric-asc':'fa-sort-numeric-desc'"></i></th> -->
                                            <!-- <th ng-click="change_order('profit')">Profit<i class="fa " ng-class="sortorder=='profit' && direction=='ASC'?'fa-sort-numeric-asc':'fa-sort-numeric-desc'"></i></th>
                                             -->
                                           <!-- <th>Campaign</th>   -->
              <th>Open Date</th>
              <th>Status</th>
            </tr>
          </thead>
          
          <tbody class="tbl_body"  ng-repeat="lst in transactionList">
            <tr >
              <td>
                <img ng-if="lst.prod_image.length > 0" src="{{lst.prod_image}}" alt="" width='50' height="50">
                <img ng-if="lst.prod_image==''" src="<?php echo $base_url.'asset/img/no_image.gif'?>" width='50' height='50'alt="">
                <a  ng-click='get_product_detail(lst.prod_id)' data-toggle="tooltip" title="{{lst.prod_title}}" class="user-link">{{lst.prod_title | limitTo:70}}<span ng-if='lst.prod_title.length>70'>...</span></a>
               <!--- <div class="user-subhead">{{lst.prod_brand}}</div> --->
               <!---  <div class="user-subhead"><b>SKU : </b>{{lst.prod_sku}}   &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;<!---<span class="label label-info" data-toggle="collapse" ng-click='get_order_details(lst,$index)' data-target="#collapse{{$index}}">Show Orders</span>---></div>
                
              </td>
			  <td>{{lst.prod_sku}}</td>
			   <td><a style="color:green;font-weight:600" href="https://www.amazon.{{lst.prod_country}}/dp/{{lst.prod_asin}}" target="_blank">{{lst.prod_asin}}</td>
			  <td>{{lst.prod_brand}}</td>
              <td>
                {{lst.itm_qty}}
              </td>
              <!-- <td> 
                 {{lst.act_price}}&nbsp;<a href='#' ng-click='update_price(lst)'><i class="fa fa-edit" aria-hidden="true"></i> -->
              <!-- </td> -->
              <td >
                {{lst.itm_price}}
              </td>
            <!--   <td>{{lst.sold_qty}}</td> -->
              <!-- <td >
                {{lst.total_profit}}
              </td> -->
              <!-- <td>
                <select  class='form-control'  ng-change='add_to_campaign(cmp)' ng-model="cmp" ng-options="x.cpgn_name for x in campList"></select>
              </td> -->
              <!-- <td>
                <button class="btn btn-default" name='Campaign' ng-click='show_campaign_list(lst)'>Campaign</button>
              </td> -->
              <td>
                <a >{{lst.open_date}}</a>
              </td>
              <td style="">
                <span class="label label-success " ng-if="lst.is_active == 1 ">Active</span>
                                            <span class="label label-warning " ng-if="lst.is_active == 0 ">InActive</span>
                                            <span class="label label-danger " ng-if="lst.is_active==-1">Deleted</span>
                                            
                                            
              </td>
            </tr>
            <tr>
                <td class="hiddenRow" colspan="8">
                    <div style="padding: 20px" id="collapse{{$index}}" class="collapse">
                    <div class="row">
                    <div class="col-sm-12">
                            <div class="col-sm-5 no-padding">
                                <form role="form">
                                    <div class="form-group contact-search m-b-30">
                                        <input type="text" placeholder="Order No,Buyer Name,Title,SKU" ng-model = 'ord_filter.search' class="form-control" id="search">
                                        
                                    </div>
                                </form>

                            </div>
                             <div class="col-sm-2">
                              <select class="form-control" ng-model='ord_filter.tfm_status'>
                              <option value="ALL">ALL</option>
                                <option value="PNP">Pick-up Pending</option>
                                <option value="PIC">Picked up</option>
                                <option value="DLI">Delivered</option>
                                <option value="OUT">Out for delivery</option>
                                <option value="LBL">Label cancelled</option>
                                <option value="UDL">Undeliverable</option>
                                <option value="RBB">Rejected by buyer</option>
                                <option value="RTS">Return to seller</option>
                              </select>
                            </div>
                            <div class="col-sm-3">
                            <input type="text" class="form-control" ng-model='dt_text' popover-trigger="'outsideClick'" ng-popover-trigger="'outsideClick'" uib-popover-template="date_filter_tmpl" popover-title="Date Filter" popover-placement="bottom-left">
                            </div>
   
                            <div class="col-sm-1">
                              <a class='btn btn-info' ng-click='ord_filtergrid()'>Search</a>
                            </div>
                    </div>
                    </div>
                       <table class=" ord_tbl table table-condensed table-bordered">
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
                                            <th>Insight</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr  ng-show='orderList.length==0'>
                                          <td colspan="9" class="text-center"><h2>No Order found</h2></td>
                                        </tr>
                                        <tr   ng-show='transactionList.length>0' ng-repeat="tnx in orderList " >
                                            <td>{{tnx.order_no}}</td>
                                            <td>{{tnx.purchase_date}}</td>
                                            <td>{{tnx.buyer_name}}<span ng-if="tnx.buyer_name.length==0 ">-------</span></td>
                                            <td>{{tnx.itm_title | limitTo:45}}</td>
                                            <td>{{tnx.seller_sku}}</td>
                                            <td>{{tnx.no_of_item}}</td>
                                            <!-- <td>{{tnx.asin}}</td> -->
                                            
                                            <td>{{tnx.exp_ship_date}}</td>
                                            <td>{{tnx.deliver_by_date}}</td>
                                            <td>
                                            <!-- <span class='label label-success'>{{tnx.order_status}}</span> -->
                                                <span ng-if="tnx.order_status=='Shipped'" class='label label-success'>{{tnx.order_status}}</span>
                                                <span ng-if="tnx.order_status=='Unshipped'" class='label label-warning'>{{tnx.order_status}}</span>
                                                <span ng-if="tnx.order_status=='Canceled'" class='label label-danger'>{{tnx.order_status}}</span>
                                                <span ng-if="tnx.order_status=='PartiallyShipped'" class='label label-info'>{{tnx.order_status}}</span>
                                                <span ng-if="tnx.order_status=='Pending'" class='label label-danger'>{{tnx.order_status}}</span>
                                            </td>
                                            <td>
                                            <span ng-if="tnx.tfm_status=='Delivered'" class='label label-success'>{{tnx.tfm_status}}</span>
                                            <span ng-if="tnx.tfm_status=='OutForDelivery'" class='label label-success'>{{tnx.tfm_status}}</span>
                                            <span ng-if="tnx.tfm_status=='RejectedByBuyer'" class='label label-danger'>{{tnx.tfm_status}}</span>
                                            <span ng-if="tnx.tfm_status=='Undeliverable'" class='label label-danger'>{{tnx.tfm_status}}</span>
                                            <span ng-if="tnx.tfm_status=='LabelCanceled'" class='label label-danger'>{{tnx.tfm_status}}</span>
                                            <span ng-if="tnx.tfm_status=='ReturnedToSeller'" class='label label-danger'>{{tnx.tfm_status}}</span>
                                            <span ng-if="tnx.tfm_status=='PickedUp'" class='label label-info'>{{tnx.tfm_status}}</span>  
                                            <span ng-if="tnx.tfm_status=='PendingPickUp'" class='label label-info'>{{tnx.tfm_status}}</span>
                                            
                                            
                                            
                                                
                                                </td>
                                        </tr>
                                        
                                    </tbody>

                                </table>
                <ul class="pagination pull-right">
        <li ng-class="ord_prev_PageDisabled()">
          <a href="javascript:void(0)" ng-click="ord_prev_page()">« Prev</a>
        </li>
        <li ng-repeat="n in ord_range()" ng-class="{active: n == current_page}" ng-click="ord_set_page(n)">
          <a href="javascript:void(0)">{{n+1}}</a>
        </li>
        <li ng-class="ord_next_pageDisabled()">
          <a href="javascript:void(0)" ng-click="ord_next_page()">Next »</a>
        </li>
      </ul>
    
                    </div>
                </td>
            </tr>
          </tbody>
        </table>
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
<script type="text/ng-template" id="date_filter_tmpl.html">
        
     <div style="padding: 5px 5px; "><a href='#'  ng-click='ord_filter.date_rng="today";ord_filtergrid()'>Today</a></div>
     <div style="padding: 5px 5px; "><a href='#'  ng-click='ord_filter.date_rng="7 days";ord_filtergrid()'>Last 7 Days</a></div>
     <div style="padding: 5px 5px; "><a href='#'  ng-click='ord_filter.date_rng="30 days";ord_filtergrid()'>Last 30 Days</a></div>
     <div style="padding: 5px 5px; "><a href='#'  ng-click='ord_filter.date_rng="this month";ord_filtergrid()'>This month</a></div>
     <div style="padding: 5px 5px; "><a href='#' ng-click='ord_filter.date_rng="last month";ord_filtergrid()'>Last Month</a></ul></div>
   <div style="margin-top:5px">
     <div class="col-sm-6 no-padding">  
     <input type='text' class='form-control date_selector'  jqdatepicker name='from' placeholder='From' ng-model='ord_filter.frm_date'>
   </div>
   <div class="col-sm-6 no-padding">  
     <input type='text' class='form-control date_selector'  jqdatepicker name='to' placeholder='To' ng-model='ord_filter.to_date'>
   </div>
   </div>
   <div class="col-sm-12 no-padding" style="margin-top: 5px">  
     <a href='#' class="btn btn-info" style='width:100%' ng-click='ord_filtergrid()'>Filter</a>
   </div>
   
        
    </script>

<script type="text/javascript">
crawlApp.directive('onFinishRender', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true) {
                $timeout(function () {
                    scope.$emit('ngRepeatFinished');
                });
            }
        }
    }
});

crawlApp.factory('invFactory', ['$http', '$q','limitToFilter', function($http,$q,limitToFilter) {
    
    var inv_list_url        =   "<?php echo $baseurl."amazon_inventory/get_inventory_list/"?>";
    var ord_list_url        =   "<?php echo $baseurl."amazon_inventory/get_order_list/"?>";
	var export_list_url        =   "<?php echo $baseurl."amazon_inventory/export_data"?>";

    var get_transaction_list = function (orderby,direction,offset,limit,search) 
    {
          var deferred = $q.defer();
          var path =inv_list_url+orderby+'/'+direction+'/'+offset+'/'+limit+'/'+search;
          $http.get(path)
          .success(function(data,status,headers,config){deferred.resolve(data);})
          .error(function(data, status, headers, config) { deferred.reject(status);});
          return deferred.promise;
    };
    var get_data = function () {
        var dataset_path="<?php echo $baseurl.'manage_campaign/get_campaign_data'?>";
        var deferred = $q.defer();
        var path =dataset_path;
        $http.get(path)
        .success(function(data,status,headers,config){deferred.resolve(data);})
        .error(function(data, status, headers, config) { deferred.reject(status);});
        return deferred.promise;
    };
	var export_data = function (search) 
    {
          var deferred = $q.defer();
          var path =export_list_url+'/'+search;
          $http.get(path)
          .success(function(data,status,headers,config){deferred.resolve(data);})
          .error(function(data, status, headers, config) { deferred.reject(status);});
          return deferred.promise;
    };
 

    var sync_inventory=function()
    {
       var search_path="<?php echo $baseurl.'amazon_inventory/sync_inventory/';?>";
         return $http({
                      method: "post",
                      url: search_path,
                      data: 
                      {
                        flag:1
                      }
                     }); 
                   
    };
    
    var get_order_list = function (cnt_asin,cnt_sku,orderby,direction,offset,limit,search) 
    {
          var deferred = $q.defer();
          var path =ord_list_url+cnt_asin+'/'+cnt_sku+'/'+orderby+'/'+direction+'/'+offset+'/'+limit+'/'+search;
          $http.get(path)
          .success(function(data,status,headers,config){deferred.resolve(data);})
          .error(function(data, status, headers, config) { deferred.reject(status);});
          return deferred.promise;
    };
    var update_product_info=function(product_info)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'amazon_inventory/update_product_info'?>",
                      data:{
                          product_title:product_info.prod_title,
                          product_asin:product_info.prod_asin,
                          product_sku:product_info.prod_sku,
                          actual_price:product_info.act_price,
                          selling_price:product_info.itm_price,
                          prod_id:product_info.prod_id
                      }
                     }); 
                   
    };
    var get_graph_data=function(product_sku)
    {
       var search_path="<?php echo $baseurl.'amazon_inventory/get_graph_data/';?>";
         return $http({
                      method: "post",
                      url: search_path,
                      data: 
                      {
                        product_sku:product_sku
                      }
                     }); 
    } 

    var get_campaign_list_of_asin=function(cmp_asin,cmp_sku,cmp_country,cmp_fc)
    {
       var search_path="<?php echo $baseurl.'manage_campaign/get_campaign_list_of_asin/';?>";
         return $http({
                      method: "post",
                      url: search_path,
                      data: 
                      {
                        cmp_asin:cmp_asin,
                        cmp_sku:cmp_sku,
                        cmp_country:cmp_country,
                        cmp_fc:cmp_fc
                      }
                     }); 
    } 

    
   
    
    return {
        get_transaction_list:get_transaction_list,
        update_product_info:update_product_info,
        get_graph_data:get_graph_data,
        get_order_list:get_order_list,
        sync_inventory:sync_inventory,
        get_data:get_data,
		export_data:export_data,
        get_campaign_list_of_asin:get_campaign_list_of_asin
        
        
    };
    
}]);
crawlApp.controller('invCtrl', ['$scope','$parse','$window','invFactory','$http','limitToFilter',function($scope,$parse,$window,invFactory,$http,limitToFilter) {        
      $scope.transactionList=[];
      $scope.orderList=[];
      $scope.asin='';
      $scope.filter={};
      $scope.filter.search='';
      $scope.filter.list_status='ALL';
	  $scope.filter.country_status='ALL';
	  $scope.filter.order_status='CSV';
      $scope.reset=function()
      {
        $scope.cpn={};
        $scope.cpn.prod_title='';
        $scope.cpn.prod_asin='';
        $scope.cpn.prod_sku='';
        $scope.cpn.act_price='';
        $scope.cpn.itm_price='';
        $scope.cpn.profit='';
        $scope.cpn.prod_id='';

      }
      $scope.reset();
   $scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
      $('.collapse').on('show.bs.collapse', function () {
          $('.collapse.in').collapse('hide');
      });
  });

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
            },baseZ:9999});

        }
      
    $scope.itemsPerPage = 25;
    $scope.currentPage = 0;
    $scope.sortorder='is_active';
    $scope.direction='DESC';
    $scope.searchJSON=[];
    $scope.filterquery=[];
    $scope.selectedCamp=[];
    $scope.checkStatus='N';
    $scope.campList=[];
    
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
      var promise= invFactory.get_transaction_list($scope.sortorder,$scope.direction,currentPage*$scope.itemsPerPage,$scope.itemsPerPage,$scope.searchJSON);
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
     $scope.filterquery=[
                          {searchtext:$scope.filter.search},
                          {list_status:$scope.filter.list_status},
						  {country_status:$scope.filter.country_status},
						  {order_status:$scope.filter.order_status}
                          
                        ];
    var argum=JSON.stringify($scope.filterquery);
    $scope.searchJSON=encodeURIComponent(argum);
    $scope.get_transaction_list(0);
  
   }
   
   $scope.export_data=function()
   {
    
     $scope.filterquery=[
                          {searchtext:$scope.filter.search},
                          {list_status:$scope.filter.list_status},
						  {country_status:$scope.filter.country_status},
						  {order_status:$scope.filter.order_status}
                          
                        
                          
                        
                        ];
    var argum=JSON.stringify($scope.filterquery);
    $scope.searchJSON=encodeURIComponent(argum);
    $scope.block_site();
      var promise= invFactory.export_data($scope.searchJSON);
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

   $scope.update_price=function(tnx)
   {
     $scope.cpn=tnx;
     $('#myModal').modal('show');

   }
   $scope.show_campaign_list=function(cmp)
   {
      $scope.selected_prod=cmp;

      $scope.block_site();
      invFactory.get_campaign_list_of_asin(cmp.prod_asin,cmp.prod_sku,cmp.prod_country,cmp.fc_code)
                          .success(
                                    function( html )
                                    {
                                      $.unblockUI();
                                      $scope.clear_all();
                                      $scope.selectedCamp=[];
                             
                                      if(html.payload.length > 0)
                                      {
                                         for(i=0;i< html.payload.length;i++)
                                         {
                                          $scope.addToArray($scope.selectedCamp,html.payload[i].cmp_id);  
                                         }
                                         console.log($scope.selectedCamp);
                                      }
                                      $('#campaign').modal('show');
                                    }
                          )
                          .error(
                                   function(data, status, headers, config)
                                   {
                                   }

                          );                
   }
   $scope.sync_inventory=function()
        {
           swal({
                title: "Are you sure to sync Inventory?",
                text: "You will not be able to abort !",
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
                      invFactory.sync_inventory()
              .success(
                      function( html )
                      {
                        $.unblockUI();
                           if(html.status_code=='0')
                           {
                             swal('Error!',html.status_text,'error');
                           }
                           if(html.status_code == '1')
                           { 
                            swal('Success!',html.status_text,'success');
                           }
                      }
                );              
                    
                } else {
                    swal("Cancelled", "Sync cancelled:)", "error");
                }
            });
         
           
        }


        $scope.get_predata = function()
         {
            var promise=invFactory.get_data();
              promise.then(
                             function(response)
                             {
                                if(response.status_code == '1')
                                {
                                    $scope.campList=response.payload; 
                                }
                                else
                                {
                                 swal('Error!',response.status_text,'error');
                                }
                             }, 
                             function(reason)
                             {
                               $scope.serverErrorHandler(reason);
                             }
                          );
        }        
        $scope.get_predata();     

  

$scope.update_product_info=function()
      {
         if($scope.amzForm.$valid) 
          {
              invFactory.update_product_info($scope.cpn)
                          .success(
                                    function( html )
                                    {
                                      console.log(html);
                                        if(html.status_code==0)
                                        {
                                           swal('Error!',html.status_text,'error');
                                        }                    
                                        else if(html.status_code==1)
                                        {
                                           swal('Success!',html.status_text,'success');
                                        }
                                        $scope.currentPage=0;
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
           }
           else
           {
            swal('Error!',"Input error please try again",'error');
           }                       
    }
    $scope.calc_profit=function()
    {
      if(parseFloat($scope.cpn.act_price) > 0 && parseFloat($scope.cpn.itm_price) > 0 )
      {
          var act_price = parseFloat($scope.cpn.act_price);
          var sl_price = parseFloat($scope.cpn.itm_price);
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

    $scope.change_order=function(col)
    {
       console.log('roder');
      $scope.sortorder=col;

      if($scope.direction=='ASC')
        $scope.direction='DESC';
      else if($scope.direction=='DESC')
        $scope.direction='ASC';  
      $scope.currentPage=0;
      $scope.get_transaction_list($scope.currentPage);

    }
    $scope.show_graph=function(asn)
       {
          console.log(asn);

          $scope.graph={};
          $scope.graph.sku=asn.prod_sku;
          invFactory.get_graph_data(asn.prod_sku)
                          .success(
                                    function( html )
                                    {
                                      console.log(html);
                                        if(html.status_code==0)
                                        {
                                           swal('Error!',html.status_text,'error');
                                        }                    
                                        else if(html.status_code==1)
                                        {
                                            $('#chart').modal('show');
                                            $scope.draw_graph(html.payload);
                                        }
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              

            
       }
       $scope.draw_graph=function(graph_data)
       {
          $("#area-example").empty();
                         setTimeout(function(){
               
            Morris.Bar({
              element: 'area-example',
              data: graph_data,
              xkey: 'order_date',
              ykeys: ['order_count'],
              labels: ['Order'],
            });
            // When you open modal several times Morris charts over loading. So this is for destory to over loades Morris charts.
            // If you have better way please share it. 
        if($('#area-example').find('svg').length > 1){
                // Morris Charts creates svg by append, you need to remove first SVG
            $('#area-example svg:first').remove();
                // Also Morris Charts created for hover div by prepend, you need to remove last DIV
                $(".morris-hover:last").remove();
        }
            // Smooth Loading
            $('.js-loading').addClass('hidden');
      },400);
       }
      $scope.date_filter_tmpl="date_filter_tmpl.html";
      $scope.ord_filter={};
      $scope.ord_filter.search='';
      $scope.ord_filter.date_rng='';
      $scope.ord_filter.order_status='ALL';
      $scope.ord_filter.tfm_status='ALL';
      $scope.dt_text='';
      
      $scope.item_per_page = 25;
    $scope.current_page = 0;
    $scope.sort_order='ssg_tn';
    $scope.srt_direc='DESC';
    $scope.searchOrder=[];
    $scope.filterOrder=[];
    
     $scope.ord_range = function()
    {
        var rangeSize = 8;
        var ret = [];
        var start;

        start = $scope.current_page;

        if ( start > $scope.ord_page_count()-rangeSize ) {
          start = $scope.ord_page_count()-rangeSize;
        }

        for (var i=start; i<start+rangeSize; i++) {
          if(i>0)
          ret.push(i);
        }
        return ret;
   };

   $scope.ord_prev_page = function()
   {
        if ($scope.current_page > 0) 
        {
          $scope.current_page--;
        }
   };

   $scope.ord_prev_PageDisabled = function()
   {
        return $scope.current_page === 0 ? "disabled" : "";
   };

   $scope.ord_next_page = function()
   {
        if ($scope.current_page < $scope.ord_page_count() - 1)
        {
          $scope.current_page++;
        }
   };

   $scope.ord_next_pageDisabled = function()
   {
        return $scope.current_page === $scope.ord_page_count() - 1 ? "disabled" : "";
   };

   $scope.ord_page_count = function() 
   {
        return Math.ceil($scope.order_total/$scope.item_per_page);
   };

   $scope.ord_set_page = function(n)
   {
        if (n > 0 && n < $scope.ord_page_count()) 
        {
          $scope.current_page = n;
        }
   };

   $scope.$watch("current_page",function(newValue, oldValue) 
   {
     if(angular.isDefined($scope.context_sku) && angular.isDefined($scope.context_asin) )
     $scope.get_order_list(newValue);
   });
   
   $scope.get_order_details=function(lst,inx)
   {
            $scope.context_asin=lst.prod_asin;
            $scope.context_sku=lst.prod_sku
            $scope.box_index=inx;
            $scope.ord_filter={};
            $scope.ord_filter.search='';
            $scope.ord_filter.date_rng='';
            $scope.ord_filter.order_status='ALL';
            $scope.ord_filter.tfm_status='ALL';
            $scope.dt_text='';
      
            $scope.get_order_list(0);
           
   }
   
  $scope.get_order_list=function(currentPage)
   {
      var blk='#collapse'+$scope.box_index;
      $(blk).block({message:null  }); 
      var promise= invFactory.get_order_list($scope.context_asin,$scope.context_sku,$scope.sort_order,$scope.srt_direc,currentPage*$scope.item_per_page,$scope.item_per_page,$scope.searchOrder);
         promise.then(function(value){
          $(blk).unblock(); 
         if(value.status_code==1)
         {
              $scope.orderList=value.datalist;
              $scope.order_total=value.total;
         }
         else
         {
            $scope.orderList=[];
            $scope.order_total=0;
         }     
       }, 
      function(reason) 
      {
        console.log("Reason"+reason);
      });
   }
   $scope.ord_filtergrid=function()
   {
    
    if(angular.isDefined($scope.ord_filter.frm_date) && angular.isDefined($scope.ord_filter.to_date) && $scope.ord_filter.frm_date.length > 0 && $scope.ord_filter.to_date.length > 0)
    {
      $scope.dt_text="ORDERS BETWEEN ["+$scope.ord_filter.frm_date+"] To ["+$scope.ord_filter.to_date+"]";
    }
    
     $scope.filter_query=[
                          {searchtext:$scope.ord_filter.search},
                          {order_status:$scope.ord_filter.order_status},
                          {from_date:$scope.ord_filter.frm_date},
                          {to_date:$scope.ord_filter.to_date},
                          {tfm_status:$scope.ord_filter.tfm_status},
                          {date_rng:$scope.ord_filter.date_rng}
                          
                        ];
    var argum=JSON.stringify($scope.filter_query);
    $scope.searchOrder=encodeURIComponent(argum);
    $scope.get_order_list(0);
  
   }

$scope.select_all=function()
  {
    // console.log($scope.selectedCamp);
    for(i=0;i< $scope.campList.length;i++)
    {
      $scope.addToArray($scope.selectedCamp,$scope.campList[i].cmp_id);  
    }
    // console.log($scope.selectedCamp);
    $scope.selectcount=$scope.selectedCamp.length;
    $scope.totalcount=$scope.total;
  }

   $scope.clear_all=function()
   {
      $scope.clearArray($scope.selectedCamp);
   }
   
   $scope.checkExist=function(arr,item)
   {
      if (angular.isArray(arr)) {
      for (var i = arr.length; i--;) {
        if (angular.equals(arr[i], item)) {
          return true;
        }
      }
    }
    return false;
   }

   $scope.addToArray=function(arr,item)
   {
      arr = angular.isArray(arr) ? arr : [];
      if(!$scope.checkExist(arr, item)) 
      {
          arr.push(item);
      }
   }
   $scope.removeFromArray=function(arr,item)
   {
      arr = angular.isArray(arr) ? arr : [];
      for (var i = arr.length; i--;) 
      {
        if (angular.equals(arr[i], item)) 
        {
          arr.splice(i, 1); 
        }
      }
   }

   $scope.clearArray=function(arr)
   {
     if (angular.isArray(arr)) 
     {
       for (var i = arr.length; i--;)
        {
           arr.splice(i, 1);
        }
     }
   }

   $scope.$watch("selectedCamp.length",
           function(newValue, oldValue) 
           {
             if(newValue < $scope.campList.length)
             {
              $scope.checkStatus='N';
             }
            });

       $scope.statusCheck=function()
      {
       
           console.log("checkStatus");
           console.log($scope.checkStatus);

           if($scope.checkStatus=='Y')
           {
            $scope.select_all();
           }
           else if($scope.checkStatus=='N')
           {
            $scope.clear_all();
           }
      }        


      

}]);
</script>
