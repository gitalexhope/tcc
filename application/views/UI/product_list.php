<?php
 $baseurl=base_url();
?>
<div class="page-container contanier" ng-controller='invCtrl'>   
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
<div id="import" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="card panel ">
                            <div class="panel-heading">
                            <button class="close" type="button" data-dismiss="modal">×</button>
                                <h3 class="panel-title">Import ASIN</h3>
                                
                            </div>
                            <div class="panel-body ">
                            <div class="row">
          <div class="col-sm-12">
          <div class="" >
                              <div class="row">
        <form class="form-horizontal" ng-submit="uploadImport(picFile)" novalidate>
                  <div class="col-sm-12" >
                                   <input type="file" ngf-select ng-model="picFile" name="file"  ngf-max-size="50MB"   ngf-model-invalid="errorFile">
      <i ng-show="myForm.file.$error.maxSize">File too large 
          {{errorFile.size / 1000000|number:1}}MB: max 50M</i><br>
      <br>
      <span class="progress" ng-show="picFile.progress >= 0">
        <div style="width:{{picFile.progress}}%" 
            ng-bind="picFile.progress + '%'"></div>
      </span>
      <span ng-show="picFile.result">Upload Successful</span>
      <span class="err" ng-show="errorMsg">{{errorMsg}}</span>
            <input type="reset" value="Reset"  class="btn btn-success"> <input type="submit" value="Save"  class="btn btn-success">
</form>
              </div><br><br>
              <div class="panel-body ">
                            <div class="col-sm-12 mg-top-10" ><br><br>
                           
                        <textarea class='form-control height-100' cols="10" rows='7' ng-model='extra.keyword'  ng-trim="false" maxlength="5500" placeholder="Enter ASINs here "></textarea>
                     </div>
                     <div class="col-sm-12">
                     <br>
                       <div class="pull-right"><a href='#' class='btn btn-success' style="margin-right:10px;" ng-click='add_new1()'>Reset</a><a href='#' class='btn btn-success' ng-click='add_extra_keyword_to_asin()'>Save</a></div>
                     </div>
         
              
      
                            </div>
          </div>
        </div>
                            </div>
                        </div>
						</div>
							</div>
  </div>
</div>


<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style="margin-left:350px;">
  <div class="card panel ">
                            <div class="panel-heading">
                            <button class="close" type="button" data-dismiss="modal">×</button>
                                <h3 class="panel-title"><i class="fa fa-database"></i> Product Attributes</h3>
                                
                            </div>
                              <div class="panel-body ">
                              <div class="col-sm-12">
                                <p><b>Product Name </b>: {{ord.pro_title}}</p>
								 </div>
                          <div class="col-sm-5">
                                 <table class="table table-condensed table-striped">
                                   <tr><th colspan="2" class="text-center">Vital Info</th></tr>
								   <tr><td>Thumbnail</td><td class="text-right">
                <img ng-if="ord.pro_image.length > 0" src="{{ord.pro_image}}" alt="" width='50' height="50">
                <img ng-if="ord.pro_image==''" src="<?php echo base_url().'asset/img/no_image.gif'?>" width='50' height='50'alt="">
               </td> </tr>
                                   <tr><td>ASIN</td><td class="text-right"><a target="_blank"  href="https://www.amazon.com/dp/{{ord.pro_asin}}">{{ord.pro_asin}}</a></td></tr>
                                   <tr><td>Package Quantity</td><td class="text-right">{{ord.pro_pack_qty}}</td></tr>
								   <tr><td>Item Weight</td><td class="text-right">{{ord.pro_itm_weight}}</td></tr>
                                   <tr><td>Length</td><td class="text-right">{{ord.pro_p_length	}}</td></tr>
                                   <tr><td>Width</td><td class="text-right">{{ord.pro_p_width}}</td></tr>
								   <tr><td>Height</td><td class="text-right">{{ord.pro_p_height}}</td></tr>
                                   <tr><td>Weight</td><td class="text-right">{{ord.pro_p_weight}}</td></tr>
								   
                                   
                                   
								   
                                   
                                </table>
                                 </div>
                                 <div class="col-sm-7">
                                 <table class="table table-condensed table-striped">
                                   <tr><th colspan="2" class="text-center">Price Info</th></tr>
	                               <tr><td>Current BB Price</td><td class="text-right">{{ord.pro_bb_price}}</td></tr>
                                   <tr><td>Current BB Offer Type</td><td class="text-right">{{ord.pro_bb_offer_type}}</td></tr>
                                   <tr><td>No of FBA Sellers</td><td class="text-right">{{ord.pro_no_of_fba_offers}}</td></tr>
								   <tr><td>Avg FBA Offer Price </td><td class="text-right">{{ord.pro_avg_fba_price}}</td></tr>
								   <tr><td>Min FBA Offer Price</td><td class="text-right">{{ord.pro_min_fba_price}}</td></tr>
								   <tr><td>Max FBA Offer Price</td><td class="text-right">{{ord.pro_max_fba_price	}}</td></tr>
                                   <tr><td>Currency Code</td><td class="text-right">{{ord.pro_curr_code}}</td></tr>
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
                   <h3 class="panel-title"><i class="fa fa-database"></i> Product Attribute Data </h3>
            </div>
            <div class="panel-body" >
            <div class="row">
            <div class="col-sm-12">
<div class="col-sm-4 pagination">
                                <form role="form">
                                    <div class="form-group contact-search m-b-30">
                                        <input type="text" placeholder="Search by ASIN" ng-model = 'filter.search' class="form-control" id="search">
                                        
                                    </div>
                                    <!-- form-group -->
                                </form>

                            </div>
                            <div class="col-sm-1 pagination">
                              <a class='btn btn-info' ng-click='filtergrid()'>Search</a>
                            </div>
                            <div class="col-sm-2">
                            
							<a href='#' class="btn btn-warning" style="margin-top:20px;margin-left:5px" data-toggle="modal" data-target="#import">Import ASIN</a> 
							</div>
							<div class="col-sm-2" style="margin-top:20px;">
                              <select class="form-control" name='type' ng-model='filter.order_status'>
                                <option value="CSV">CSV</option>
                                <option value="TXT">TXT</option>
                                <option value="XLS">XLS</option>
                              </select> 
                            </div>
							
							
                           <div class="col-sm-1 pagination">
							<a href='#' ng-click='export_data()'  class="btn btn-success" >Export</a>
							 </div>
						  
                                
</div>
                             </div>
                         
                </div>  
                
           
              <div class="col-sm-11 col-md-6 no-padding" style="width:100%;margin-right:20px;">
                 <div class="table-responsive"> <br>   <br>  
                                <table class="table table-actions-bar">
                                    <thead>
                                        <tr >
										    <th>Image</th>
										    <th>ASIN</th>
											<th>Title</th>
                                            <th>Brand</th>
											<th>Current BSR</th>
						                    <th>Category</th>
											<th>Part Number</th>
                                            <th>Model</th>
											<th>Manufacturer</th>
											
											
											
											
                                            
                                        </tr>
                                    </thead>

                                    <tbody>
									 <tr  ng-show='transactionList.length==0'>
                                          <td colspan="12" class="text-center"><h2>No Product details found</h2></td>
                                        </tr>
                                        <tr  ng-repeat="tnx in transactionList " >
                                        
									 <td>
                <img ng-if="tnx.pro_image.length > 0" src="{{tnx.pro_image}}" alt="" width='50' height="50">
                <img ng-if="tnx.pro_image==''" src="<?php echo base_url().'asset/img/no_image.gif'?>" width='50' height='50'alt="">
               </td> 
										<td><a target="_blank" style="color:green;font-weight:600" href="https://www.amazon.com/dp/{{tnx.pro_asin}}">{{tnx.pro_asin}}</a></td>
											<td ng-click='show_order_details(tnx)' data-target="#myModal" data-toggle="modal"  ng-show='transactionList.length>0'>{{tnx.pro_title | limitTo:50}}</td>
											<td>{{tnx.pro_brand}}</td>
											<td>{{tnx.pro_rank}}</td>
											<td>{{tnx.pro_category}}</td>
											<td>{{tnx.pro_part_num}}</td>
											<td>{{tnx.pro_model}}</td>
											<td>{{tnx.pro_manufacturer}}</td>
                                             </tr>
										
										
                                        
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>  
                         <div class="col-sm-11">
                                   <a href='#' ng-click='remove_all()' class="btn btn-danger" style="margin-top:20px;margin-left:100px;">Remove Data</a> 
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
    <div class="col-sm-12">
   
                             </div>

                    </div> 
       </div>
                          
   </div>                 
</div>
  </div>
                     
                                </div>

                                
                            </div>
                        </div>
          
   </div>

   </div>

</div>
</div>
         
   </div>

   </div>

</div>
</div>
<script type="text/javascript">
crawlApp.factory('invFactory', ['$http', '$q','limitToFilter', 'Upload',function($http,$q,limitToFilter,Upload) {

    
    var inv_list_url        =   "<?php echo $baseurl ."product_list/get_product_list/"?>";
    var p_path        =   "<?php echo $baseurl."product_list/import_data/"?>";
	var export_list_url        =   "<?php echo $baseurl."product_list/export_data/"?>";
    var r_path        =   "<?php echo $baseurl."product_list/remove_data/"?>"; 
    var c_path        =   "<?php echo $baseurl."product_list/cron_run/"?>"; 	
    var import_data=function(file)
    {
       return Upload.upload({
          url: p_path,
          data: {import_file: file},
         });

    }
  var export_data = function (search) 
    {
          var deferred = $q.defer();
          var path =export_list_url+'/'+search;
          $http.get(path)
          .success(function(data,status,headers,config){deferred.resolve(data);})
          .error(function(data, status, headers, config) { deferred.reject(status);});
          return deferred.promise;
    };
	var cron_run=function(file)
    {
      return $http({
                      method: "post",
                      url:c_path,
                      data:{
                          cron_id:1
                      }
                     }); 
          

    }
	var add_extra_keyword_to_asin=function(keyword)
    {
       var search_path="<?php echo $baseurl.'product_list/add_extra_keyword_to_asin/';?>";
         return $http({
                      method: "post",
                      url: search_path,
                      data: 
                      {
                        keyword:keyword
                      }
                     }); 
      
    }
	var update_amazon_api=function(api)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'amazon_pricing_list/update_amazon_api'?>",
                      data:{
                          api_detail:angular.toJson(api)
                      }
                     }); 
                   
    };
	
    var remove_data=function(file)
    {
      return $http({
                      method: "post",
                      url:r_path,
                      data:{
                          remove_id:1
                      }
                     }); 
          

    }
	
    var get_transaction_list = function (orderby,direction,offset,limit,search) 
    {
          var deferred = $q.defer();
          var path =inv_list_url+orderby+'/'+direction+'/'+offset+'/'+limit+'/'+search;
          $http.get(path)
          .success(function(data,status,headers,config){deferred.resolve(data);})
          .error(function(data, status, headers, config) { deferred.reject(status);});
          return deferred.promise;
    };
    
    return {
        get_transaction_list:get_transaction_list,
		update_amazon_api:update_amazon_api,
		cron_run:cron_run,
		add_extra_keyword_to_asin:add_extra_keyword_to_asin,
		import_data:import_data,
        export_data:export_data,
        remove_data:remove_data
        
    };
    
}]);
crawlApp.controller('invCtrl', ['$scope','$parse','$window','invFactory','$http','limitToFilter','$timeout',function($scope,$parse,$window,invFactory,$http,limitToFilter,$timeout) {
      $scope.transactionList=[];
      
      $scope.filter={};
      $scope.filter.search='';
	  $scope.amz_api={};
    $scope.amz_api.seller_id='';
	$scope.amz_api.access_key='';
    $scope.filter.order_status='CSV';
    $scope.selectedProduct=[];
    $scope.checkStatus='N';
    $scope.add_new=function()
    {
      $scope.amz_api.seller_id='';
    
   }
   $scope.add_new1=function()
    {
      $scope.extra.keyword='';
    
   }
     $scope.ord={};
      $scope.show_order_details=function(tnx)
      {
        $scope.ord=tnx;

      }
      
      $scope.reset=function()
      {
      $scope.cpn={};
      $scope.cpn.prod_asin='';
      $scope.cpn.id_type='ASIN';

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

            },baseZ:9999});

        }
      
    $scope.itemsPerPage = 25;
    $scope.currentPage = 0;
    $scope.sortorder='added_on';
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

   $scope.show_more_details=function(tnx)
   {
     $scope.prod_feature=tnx.lem_bullet;
     $scope.prod_desc=tnx.lem_desc;
     $('#desc').modal('show');

   }

$scope.update_product_info=function()
      {
         if($scope.amzForm.$valid) 
          {

            $scope.block_site();
              invFactory.update_product_info($scope.cpn)
                          .success(
                                    function( html )
                                    {
                                      $.unblockUI();
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
	
    $scope.search_product=function()
        {
         
           $scope.block_site();
           invFactory.search_amazon_product($scope.cpn)
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
                              $scope.items=html.items;
                           }
                      }
                )
                .error(
                       function(data, status, headers, config) {
                                 if(status == 404)
                                 {
                                  alert("Page Missing");
                                 }
                             }

                );  
                   
  
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

       $scope.select_all=function()
   {
      $scope.showbar=true;
      console.log("Before Select");
      console.log($scope.selectedProduct);
      
      for(i=0;i< $scope.transactionList.length;i++)
      {
        // $scope.selected.push($scope.pagedItems[i].asin);
        $scope.addToArray($scope.selectedProduct,$scope.transactionList[i].prod_sku)  
      }
      $scope.selectcount=$scope.selectedProduct.length;
      $scope.totalcount=$scope.total;
      console.log("After Select");
      console.log($scope.selectedProduct);
      
   }

   $scope.clear_all=function()
   {
      console.log("Before Cleared");
      console.log($scope.selectedProduct);
      
      $scope.clearArray($scope.selectedProduct);
      console.log("After Cleared");
      console.log($scope.selectedProduct);
      
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

   $scope.$watch("selectedProduct.length",
           function(newValue, oldValue) 
           {
              console.log($scope.selectedProduct);
             if(newValue < $scope.transactionList.length)
             {
              $scope.checkStatus='N';
             }
            });

       // $scope.changeSelectAllstatus=function()
       // {
       //  alert($scope.selectedProduct.length);
       // }
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
	   $scope.add_extra_keyword_to_asin=function()
       {
         if($scope.extra.keyword.lenght<=0 )
          {
            swal('Error!',"ASINs are Empty",'error');
          }
          else
          {
            invFactory.add_extra_keyword_to_asin($scope.extra.keyword)
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
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
          }  
          
       }
   $scope.remove_product=function()
   {
    if($scope.selectedProduct.length > 0) 
          {
            
              swal({
                title: "Are you sure remove product?",
                text: "You will not be able to undo!",
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
                  invFactory.remove_product($scope.selectedProduct)
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
                                        }
                                        $scope.get_transaction_list($scope.currentPage);
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
                    
                } else {
                    swal("Cancelled", "Delete cancelled:)", "error");
                }
            }); 
           }
           else
           {
             swal('Error!',"Please select some SKU",'error');
           }
   }
   
   $scope.update_amazon_api=function()
      {
         if($scope.amzForm.$valid) 
          {
              invFactory.update_amazon_api($scope.amz_api)
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
  
  
  

     $scope.uploadImport = function(file) 
     {
      $scope.block_site();
        invFactory.import_data(file)
         .then(function (response)
               {
                $.unblockUI(); 
                if(angular.isDefined(file))
                  {
                    $timeout(function () {
                    file.result = response.data;
                    });
                    
                  }
                if(response.data.status_code == '1')
                 {
                   swal('Success!',response.data.status_text,'success');
                 }
                 else
                 {
                   swal("Error!",response.data.status_text,'error');
                 }
               },
             function (response) 
             {
                if (response.status > 0)
                  $scope.errorMsg = response.status + ': ' + response.data;
             },
             function (evt)
             {
                if(angular.isDefined(file))
                file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
              });
    }


   
           
 $scope.cron_run=function()
    {
          swal({
                title: "Run ",
                text: "Are you ready to process ASINs?",
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
                  invFactory.cron_run()
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
                                        }
                                        
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
                    
                } else {
                    swal("Cancelled", "The job has been cancelled.", "error");
                }
            }); 
    }
       
    
     $scope.remove_all=function()
    {
          swal({
                title: "Remove Data",
                text: "Are you sure you want to remove all data?",
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
                  invFactory.remove_data()
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
                                        }
                                        
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
                    
                } else {
                    swal("Cancelled", "No data has been removed.", "error");
                }
            }); 
    }
           
    
   
  
    
   
   

}]);
</script>
