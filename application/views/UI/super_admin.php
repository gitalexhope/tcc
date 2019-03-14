<?php
$baseurl=base_url();

?>
<div class="page-container contanier" ng-controller='userCtrl'>
  <div class="row">

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
  <div class="card panel ">
                            <div class="panel-heading">
                            <button class="close" type="button" data-dismiss="modal">×</button>
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>User Info</h3>
                                
                            </div>
                            <div class="panel-body ">
                            <div class="row">
          <div class="col-sm-12">
          <form  ng-submit="update_amazon_api()" name='amzForm' novalidate>
                              <div class="pad"><b>Seller Id</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.seller_id.$invalid && amz_submitted  }" >
                              <input type='text' class='form-control' name='seller_id' placeholder='Seller Id' ng-model='amz_api.seller_id' required>
                              </div><br>
							   <div class="pad"><b>Mws Auth Token</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.access_key.$invalid && amz_submitted  }" >
                              <input type='text' class='form-control' name='auth_token' placeholder='Mws Auth Token' ng-model='amz_api.auth_token' required>
                              </div><br>
                              <div class="pad"><b>Acess Key</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.access_key.$invalid && amz_submitted  }" >
                              <input type='text' class='form-control' name='access_key' placeholder='Access Key' ng-model='amz_api.access_key' required>
                              </div><br>
							  <div class="pad"><b>Secret Key</b></div>
                              <div class="pad" ng-class="{ 'has-error' : amzForm.secret_key.$invalid && amz_submitted  }" >
                              <input type='text' class='form-control' name='secret_key' placeholder='Secret Key' ng-model='amz_api.secret_key' required>
                              <div class="pad" >
                              <br><br>
                              <input  type='reset' name='reset'  value='Reset' ng-click="add_new()" class='col-sm-4 btn btn-danger'>
                              <input type='submit' name='submit'  value='Update' ng-click="amz_submitted=true" class='col-sm-4 btn btn-info'>
                              </div> 
                              
          </div>
        </div>
                            </div>
                        </div>
  </div>
</div>   
  <!-- <div class="col-sm-1">
    
  </div> 
  <div class="col-sm-10 ">
  <div class="card panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-2">
                                <img height='60' src="<?php echo $baseurl."asset/img/logo.png"?>" >
                        
                    </div > 
                    <div class="col-xs-10 text-right">
                    <h3 class="">
                 Manage Users
            </h3>    
                    </div>

                </div>
            </div>
   </div>
   </div>
 -->
  
  </div>   
    <!-- <div class="row">
       <div class="col-lg-12">
            <h1 class="page-header">
                Index <small>Test</small>
            </h1>
            
        </div>
    </div> -->
    <div class="row">
    <div class="col-sm-1"></div>
	
    <div class="col-sm-12" >
    <div class="card panel ">
      <div class="panel-heading">
             <h3 class="panel-title"><i class="fa fa-users">&nbsp;</i>USER List</h3>
      </div>
   
	  
      <div class="panel-body table-responsive" >
    
        <table class='table'>
         <thead>
           <tr><th>SL.No</th><th>Name</th><th>Seller Id</th><th>Mws Auth Token</th><th>Access Key</th><th>Secret Key</th><!---<th>Verify Status</th><th>Status</th><th>Action</th><!---<th>Amount Paid</th>---><th>Action</th></tr>
         </thead>
         <tbody>
          <tr ng-repeat='usr in user_list'>
            <td width='5%'>{{$index+1}}</td>
			 <td>{{usr.fname}} </td>
			 <td >{{usr.seller_id}} </td>
            <td class="col-sm-6">{{usr.auth_token}} </td>
            <td>{{usr.access_key}}</td>
			<td>{{usr.secret_key}}</td>
           		
          <!---  <td><span ng-if='usr.is_verified==1' class="label label-info">Verified</span><span ng-if='usr.is_verified==0' class="label label-danger">Not Verified</span></td>&nbsp; <td><span ng-if='usr.is_active==1' class="label label-success">Active</span><span ng-if='usr.is_active==0'  class="label label-danger">Inactive</span></td> --->
           <!--- <td>{{usr.total_amt}}</td>  --->
            <td ><a href='' class="label label-info" ng-click='trigger_edit(usr)'>Edit User</a></td> 
        	
			</tr>
         </tbody>
        </table>
      </div>
   </div>     
    </div>
   
   
                        </div>
    </div>

    
    
    </div>

</div>

    <!--<div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-10 no-padding">
    <div class="col-md-4" ng-repeat='usr in user_list'>
          
          <div class="box box-widget widget-user">
            <div class="widget-user-header bg-aqua-active">
              <h3 class="widget-user-username">{{usr.fname}} {{usr.lname}}</h3>
              <h5 class="widget-user-desc">Joined: {{usr.joined}}</h5>
            </div>
            <div class="widget-user-image">
              <img alt="User Avatar" src="<?php echo $baseurl.'asset/profile_img/' ; ?>{{usr.pro_img}}" class="img-circle">
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">10</h5>
                    <span class="description-text">I-SEARCH</span>
                  </div>
                </div>
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">25</h5>
                    <span class="description-text">W-SEARCH</span>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="description-block">
                    <h5 class="description-header">{{usr.credits}}</h5>
                    <span class="description-text">CREDITS</span>
                  </div>
                </div>
              </div>
              <div class="row mg-top-10">
                <div class="col-sm-12 table-responsive">
                  <table class="table">
                    <tr><td>EMAIL</td><td class="text-right">{{usr.email}}</td></tr>
                    <tr><td>STATUS</td><td class="text-right"><span ng-if='usr.is_verified==1' class="label label-info">Verified</span><span ng-if='usr.is_verified==0' class="label label-danger">Not Verified</span>&nbsp;<span ng-if='usr.is_active==1' class="label label-info">Active</span><span ng-if='usr.is_active==0' class="label label-danger">Deactive</span></td></tr>
                    <tr><td>AMT Paid</td><td class="text-right"><span ng-if='usr.total_amt>0'>${{usr.total_amt}}</span><span ng-if='usr.total_amt==null'>Nil</span></td></tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    </div>-->
</div>
<script type="text/javascript">

  crawlApp.factory("userFactory", function($http,$q) {
   
   var get_users = function () {
        var dataset_path="<?php echo $baseurl.'super_admin/get_user_list'?>";
        var deferred = $q.defer();
        var path =dataset_path;
        
        $http.get(path)
        .success(function(data,status,headers,config){deferred.resolve(data);})
        .error(function(data, status, headers, config) { deferred.reject(status);});
        
        return deferred.promise;
    };
    var send_credit=function(user,credit,notes)
    {
       var search_path="<?php echo $baseurl.'super_admin/add_credits/';?>";
         return $http({
                      method: "post",
                      url: search_path,
                      data: 
                      {
                        user_id:user,
                        credit:credit,
                        note:notes
                      }
                     }); 
                   
    };
	var update_amazon_api=function(api)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'super_admin/update_amazon_api'?>",
                      data:{
                          api_detail:angular.toJson(api)
                      }
                     }); 
                   
    };
	var delete_amazon_api=function(user)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'super_admin/delete_amazon_api'?>",
                      data:{
                          user_id:user
                      }
                     }); 
                   
    };
var activate_user=function(user)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'super_admin/activate_user'?>",
                      data:{
                          user_id:user
                      }
                     }); 
                   
    };
	var delete_user=function(user)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'super_admin/delete_user'?>",
                      data:{
                          user_id:user
                      }
                     }); 
                   
    };
	
	var make_user_admin=function(user)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'super_admin/make_user_admin'?>",
                      data:{
                          user_id:user
                      }
                     }); 
                   
    };
	
	var make_user_normal=function(user)
    {
       return $http({
                      method: "post",
                      url:"<?php echo $baseurl.'super_admin/make_user_normal'?>",
                      data:{
                          user_id:user
                      }
                     }); 
                   
    };

  return {
    send_credit:send_credit,
	update_amazon_api:update_amazon_api,
	delete_amazon_api:delete_amazon_api,
	activate_user:activate_user,
	delete_user:delete_user,
    get_users:get_users,
	make_user_admin:make_user_admin,
	make_user_normal:make_user_normal
    
  };
});
  
  crawlApp.controller("userCtrl",function userCtrl($window,$scope,userFactory,$timeout,$q,$rootScope) 
  {
	$scope.amz_api={};
    $scope.amz_api.seller_id='';
    $scope.amz_api.access_key='';
    $scope.amz_api.secret_key='';
    $scope.amz_api.key_id=null;
    $scope.reset=function()
    {
      $scope.amz_api.seller_id='';
    $scope.amz_api.access_key='';
    $scope.amz_api.secret_key='';
    $scope.amz_api.key_id=null;
    }
       $scope.get_users = function()
         {
           
            var promise=userFactory.get_users();
              promise.then(
                             function(response)
                             {
                                
                                if(response.status_code == '1')
                                {
                                    // $rootScope.free_usage=response.free_usage;
                                    $scope.user_list=response.payload;
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
        $scope.get_users(); 
        $scope.selected_user='';
        $scope.show_credit=function(usr)
        {
            $scope.selected_user=usr.uid;
            $('#popup1').modal('show');
        } 
        $scope.send_credit=function()
        {
              if($scope.selected_user.length > 0) 
              {
                  userFactory.send_credit($scope.selected_user)
                        .success(
                                   function( html ) {
                                   if(html.status_code == '1')
                                   {
                                    $scope.user_list=html.payload;
                                    swal("Succes!","Credit has been added",'success');
                                   }
                                   else
                                   {
                                     swal("Error!",html.status_text,'error');
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
               else
               {
                swal('Error!','Something went wrong please refresh and try again','error');
               }   
        }
		$scope.trigger_edit=function(usr) 
    {
      $scope.amz_api.seller_id=usr.seller_id;
	  $scope.amz_api.auth_token=usr.auth_token;
      $scope.amz_api.access_key=usr.access_key;
      $scope.amz_api.secret_key=usr.secret_key;
	  $scope.amz_api.key_id=usr.uid;
	  $('#myModal').modal('show');
    }
		$scope.trigger_delete=function(usr)
      {
         if(usr.uid > 0) 
          {
              swal({
                title: "Are you sure?",
                text: "You want to deactivate the user account!",
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
                  userFactory.delete_amazon_api(usr.uid)
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
                                        $scope.get_users(); 
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
                    
                } else {
                    swal("Cancelled", "Update cancelled:)", "error");
                }
            }); 
           }
           else
           {
             swal('Error!',"Input error please try again",'error');
           }                       
      }      
	  $scope.trigger_active=function(usr)
      {
         if(usr.uid > 0) 
          {
              swal({
                title: "Are you sure?",
                text: "You want to Activate the user account!",
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
                  userFactory.activate_user(usr.uid)
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
                                        $scope.get_users(); 
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
                    
                } else {
                    swal("Cancelled", "Update cancelled:)", "error");
                }
            }); 
           }
           else
           {
             swal('Error!',"Input error please try again",'error');
           }                       
      }  


	  
	  $scope.make_user_admin=function(usr)
      {
         if(usr.uid > 0) 
          {
              swal({
                title: "Are you sure?",
                text: "You want to add user as a Admin User!",
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
                  userFactory.make_user_admin(usr.uid)
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
                                        $scope.get_users(); 
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
                    
                } else {
                    swal("Cancelled", "Update cancelled:)", "error");
                }
            }); 
           }
           else
           {
             swal('Error!',"Input error please try again",'error');
           }                       
      }  
	  
	  $scope.make_user_normal=function(usr)
      {
         if(usr.uid > 0) 
          {
              swal({
                title: "Are you sure?",
                text: "You want to add user as a Normal User!",
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
                  userFactory.make_user_normal(usr.uid)
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
                                        $scope.get_users(); 
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                           
                                       }

                          );              
                    
                } else {
                    swal("Cancelled", "Update cancelled:)", "error");
                }
            }); 
           }
           else
           {
             swal('Error!',"Input error please try again",'error');
           }                       
      } 
 $scope.delete_user=function(usr)
      {
         if(usr.uid > 0) 
          {
              swal({
                title: "Are you sure?",
                text: "You want to Delete the user account!",
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
                  userFactory.delete_user(usr.uid)
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
                                        $scope.get_users(); 
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
             swal('Error!',"Input error please try again",'error');
           }                       
      }  	  
		$scope.update_amazon_api=function()
      {
         if($scope.amzForm.$valid) 
          {
              userFactory.update_amazon_api($scope.amz_api)
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
                                       $scope.get_users(); 
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
  

 
});
</script>