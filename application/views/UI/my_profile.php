<?php
$baseurl=base_url();

?>
<style type="text/css">
  .nav-tabs { border-bottom: 2px solid #DDD; }
    .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover { border-width: 0; }
    .nav-tabs > li > a { border: none; color: #666; }
        .nav-tabs > li.active > a, .nav-tabs > li > a:hover { border: none; color: #4285F4 !important; background: transparent; }
        .nav-tabs > li > a::after { content: ""; background: #4285F4; height: 2px; position: absolute; width: 100%; left: 0px; bottom: -1px; transition: all 250ms ease 0s; transform: scale(0); }
    .nav-tabs > li.active > a::after, .nav-tabs > li:hover > a::after { transform: scale(1); }
.tab-nav > li > a::after { background: #21527d none repeat scroll 0% 0%; color: #fff; }
.tab-pane { padding: 15px 0; }
.tab-content{padding:20px}

.profile-card {background: #FFF none repeat scroll 0% 0%; box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.3); margin-bottom: 30px; }

</style>
<div class="page-container contanier" ng-controller='profileCtrl'>   
<div  class="modal fade" id="Preview_email_box" role="dialog">
  <div class="modal-dialog">
  <div class="card panel ">
                            <div class="panel-heading">
                                                   <button class="close" type="button" data-dismiss="modal">×</button>
     
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>Instrucation</h3>
                                
                            </div>
                            <div class="panel-body ">
                            <div class="row">
                            <div class="col-sm-12">
                            <p class="p">
          <strong class="ph b">Instructions for Sellers to Authorize <b>ProSeller</b> Developer to Access Your Amazon
            Seller Account</strong>
        </p>
                              <ol id="faq__AuthorizeThirdPartyDev" class="ol">
          <li class="li">Go to the <a target="_blank" href="https://sellercentral.amazon.in/gp/account-manager/home.html" class="xref">User Permissions</a> page in Seller Central and log into your
            Amazon seller account as the primary user.<ul class="ul">
              <li class="li">If you have never signed up for Amazon MWS, the <span class="ph uicontrol">Sign up for
                  MWS</span> button appears. Click <span class="ph uicontrol">Sign up for MWS</span>.</li>

              <li class="li">If you have previously signed up for Amazon MWS, then <span class="ph uicontrol">Authorize a
                  developer</span> button appears. Click <span class="ph uicontrol">Authorize a
                  developer</span>.</li>

            </ul>
</li>

          <li class="li">On the Amazon MWS registration page, choose <span class="ph uicontrol">I want to authorize a developer
              to access my Amazon seller account with Amazon MWS</span>.</li>

          <li class="li">In the <span class="ph uicontrol">Developer's Name</span> text box, enter 
            <b>ProSeller</b>, It is merely for your reference in the future.</li>

          <li class="li">In the <span class="ph uicontrol">Developer Account Number</span> text box, enter the MWS
            developer account identifier <b>[2697-1113-7920]</b> For ProSeller integration.</li>

          <li class="li">Click the <span class="ph uicontrol">Next</span> button.</li>

          <li class="li">Check the box to confirm that you want to give ProSeller developer access to
            your account, and then click the <span class="ph uicontrol">Next</span> button.<p class="p">Your account
              identifiers (Seller ID and MWS Authorization Token) appear. These identifiers are used
              by ProSeller developer to programmatically access your Amazon seller account.
              You can access these identifiers at any time on the <a target="_blank" href="https://sellercentral.amazon.in/gp/account-manager/home.html" class="xref">User Permissions</a>
              page in Seller Central.</p>
</li>

          <li class="li">Then place the Seller ID, MWS auth token in the box here </li>

        </ol>
                            </div>
                            </div>
    </div>
                       
  </div>
</div>
</div>
  <div class="row">
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
                 My Profile
            </h3>    
                    </div>

                </div>
            </div>
   </div>
   </div>

   -->
  </div>   
    <div class="row">
    <div class="col-sm-3"></div>
      <div class="col-md-6" >
      <?php if($this->session->flashdata('msg'))
      {

        ?>
      <div class="alert alert-info alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
   <p><b><?php echo$this->session->flashdata('msg') ?></b></p>
  </div>
  <?php
}
?>
          <!-- Widget: user widget style 1 -->
         <!--- <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
          <!----  <div class="widget-user-header bg-aqua-active">
              <h3 class="widget-user-username">{{usr.fname}} {{usr.lname}}</h3>
              <h5 class="widget-user-desc">Joined: {{usr.joined}}</h5>
            </div> -->
        <!----   <div class="widget-user-image">
              <img alt="User Avatar" src="<?php echo $baseurl.'asset/profile_img/' ; ?>{{usr.pro_img}}" class="img-circle">
            </div>  ---->
          <!---  <div class="box-footer" >  --->
             <!---     <div class="row">
              <!--  <div class="col-sm-4 border-right">
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
              
        
          </div>   </div>   </div>
          <!-- /.widget-user -->
                                    <div class="col-md-12 no-padding" style=" margin-top: 20px">
                                    <!-- Nav tabs --><div class="profile-card">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Amazon MWS API</a></li>
                                        <!---<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">COMPANY DETAILS</a></li>  --->
                                        <!-- <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">PAYMENT HISTORY</a></li> -->
                                        <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">PROFILE DETAILS</a></li>
                                       <!--- <li role="presentation"><a href="#pwd" aria-controls="pwd" role="tab" data-toggle="tab">CHANGE PASSWORD</a></li>--->
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="home">
                                          <div class="row">
                                          <div class="col-sm-12"><h3 ng-if='msg.length>0'><u>{{msg}}</u></h3></div>
                                         <!--- <div class="col-sm-12"><span class="pull-right label label-warning"  data-toggle="modal" data-target="#Preview_email_box">What is This?</div> ---->
                                          <div class="col-md-12">
                                    <form novalidate="" name="amzForm" ng-submit="update_amazon_api()" class="ng-pristine ng-valid ng-valid-required">
                              <div class="pad"><b>Seller ID</b></div>
                              <div  ng-class="{ 'has-error' : amzForm.seller_id.$invalid &amp;&amp; amz_submitted  }" class="pad">
                              <input type="text" required="" ng-model="amz_api.seller_id" placeholder="Seller ID" name="seller_id"   class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required">
                              </div>
                              
                              
                              
                              <div class="pad"><b>Auth Token</b></div>
                              <div ng-class="{ 'has-error' : amzForm.auth_token.$invalid &amp;&amp; amz_submitted  }" class="pad">
                              <input type="text" required="" ng-model="amz_api.tokenid" placeholder="Token" name="auth_token" class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required">
                              </div>
                              <!---<div class="pad"><b>Access Key</b></div>
                              <div ng-class="{ 'has-error' : amzForm.access_key.$invalid &amp;&amp; amz_submitted  }" class="pad">
                              <input type="text" required="" ng-model="amz_api.access_key" placeholder="Access Key" name="access_key" class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required">
                              </div>
                              <div class="pad"><b>Secret Key</b></div>
                              <div ng-class="{ 'has-error' : amzForm.secret_key.$invalid &amp;&amp; amz_submitted  }" class="pad">
                              <input type="text" required="" ng-model="amz_api.secret_key" placeholder="Secret Key" name="secret_key" class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required">
                              </div>
                              <!--<div class="pad"><b>Market Place ID</b></div>
                              <div ng-class="{ 'has-error' : amzForm.market_id.$invalid &amp;&amp; amz_submitted  }" class="pad">
                              <input type="text" required="" ng-model="amz_api.market_id" placeholder="Market Place ID" name="market_id" class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required">
                              </div>
                              <div class="pad"><b>Vendor Code</b></div>
                              <div ng-class="{ 'has-error' : amzForm.vendor_code.$invalid &amp;&amp; amz_submitted  }" class="pad">
                              <input type="text" required="" ng-model="amz_api.vendor_code" placeholder="Amazon's unique vendor ID " ng-change="append_vendor_code()"  name="vendor_code" class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required">
                              </div>--><br>  

                              <div class="pad">  
                              <input type="submit" class="btn btn-warning" ng-click="amz_submitted=true" value="Update API Details" name="submit">
                              </div>
                           </form>
                                </div>
                                          </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="profile">
                                        <div class="row">
                                          <div class="col-md-12">
                                    <form novalidate="" name="comForm" ng-submit="update_company_api()" class="ng-pristine ng-valid ng-valid-required">
                              <div class="pad"><b>Company Name</b></div>
                              <div ng-class="{ 'has-error' : comForm.com_name.$invalid &amp;&amp; com_submitted  }" class="pad">
                              <input type="text" required="" ng-model="com_info.com_name" placeholder="Company Name" name="com_name" class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required">
                              </div>
                              <div class="pad"><b>Company Address</b></div>
                              <div ng-class="{ 'has-error' : comForm.com_addr.$invalid &amp;&amp; com_submitted  }" class="pad">
                              <textarea required="" rows='4' ng-model="com_info.com_addr" placeholder="Company Addres" name="com_addr" class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required"></textarea>
                              </div>
                              <div class="pad"><b>GST Number</b></div>
                              <div ng-class="{ 'has-error' : comForm.gst_number.$invalid &amp;&amp; com_submitted  }" class="pad">
                              <input type="text" required="" ng-model="com_info.gst_number" placeholder="GST Number" name="gst_number" class="form-control ng-pristine ng-untouched ng-not-empty ng-valid ng-valid-required">
                              </div>
                              
                              <div class="pad">  
                              <input type="submit" class="btn btn-warning" ng-click="com_submitted=true" value="Update Company Details" name="submit">
                              </div>
                           </form>
                                </div>
                                          </div>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="settings"><div class="row">
                                          <div class="col-sm-12">
                                            <div class=" table-responsive">
                  <table class="table">
                    <tr><td>EMAIL</td><td class="text-right">{{usr.email}}</td></tr>
                    <!---<tr><td>Mobile No</td><td class="text-right">{{usr.mobile_no}}</td></tr> --->
                    <tr><td>STATUS</td><td class="text-right"><span ng-if='usr.is_verified==1' class="label label-info">Verified</span><span ng-if='usr.is_verified==0' class="label label-danger">Not Verified</span>&nbsp;<span ng-if='usr.is_active==1' class="label label-info">Active</span><span ng-if='usr.is_active==0' class="label label-danger">Deactive</span></td></tr>
					 <tr><td>Joined</td><td class="text-right">{{usr.joined}}</td></tr>
                    <!-- <tr><td>PLAN</td><td class="text-right"><span class="label label-warning">Silver Plan</span></td></tr> -->
                  <!---  <tr><td>AMT Paid</td><td class="text-right"><span ng-if='usr.total_amt>0'>${{usr.total_amt}}</span><span ng-if='usr.total_amt==null'>Nil</span></td></tr>  --->
                  <!---  <tr><td>Change Profile </td><td align="right"><input type="file" ngf-select ng-model="picFile" name="file"  ngf-max-size="2MB"   ngf-model-invalid="errorFile">
      <i ng-show="myForm.file.$error.maxSize">File too large 
          {{errorFile.size / 1000000|number:1}}MB: max 2M</i><br>
      <img width='100' height='100' ng-show="myForm.file.$valid" ngf-thumbnail="picFile" class="thumb"> <button ng-click="picFile = null" ng-show="picFile">Remove</button>
      <br>
      <button style="margin-top: 10px;" class='btn btn-info'
              ng-click="send_message(picFile)">Submit</button>
      <span class="progress" ng-show="picFile.progress >= 0">
        <div style="width:{{picFile.progress}}%" 
            ng-bind="picFile.progress + '%'"></div>
      </span>
      <span ng-show="picFile.result">Upload Successful</span>
      <span class="err" ng-show="errorMsg">{{errorMsg}}</span>
      <!-- <div class="fileUpload btn btn-info">
    <span>Upload</span>
    <input type="file" class="upload" />
</div>

</td></tr>  ---->
<!-- <tr><td></td><td class="text-right"><input type="submit" class='btn btn-primary' name="" value="Update"></td></tr> -->
                  </table>
                </div>
                                          </div>
                                        </div></div>
      <div role="tabpanel" class="tab-pane" id="pwd">
      <div class="row">
          <div class="col-md-12">
                                    <form novalidate="" name="pwdForm" ng-submit="update_password()">
                              <div class="pad"><b>Current Password</b></div>
                              <div ng-class="{ 'has-error' : pwdForm.cur_pwd.$invalid &amp;&amp; pwd_submitted  }" class="pad">
                              <input type="password" required="" ng-model="pwd.cur_pwd" placeholder="Current Password" name="cur_pwd"  class="form-control">
                              </div>
                              <br>
                              <div class="pad"><b>New Password</b></div>
                              <div ng-class="{ 'has-error' : pwdForm.new_pwd.$invalid &amp;&amp; pwd_submitted  }" class="pad">
                              <input type="password" required="" ng-model="pwd.new_pwd" placeholder="New Password" name="new_pwd"  class="form-control">
                              </div>
                              <br>
                              <div class="pad"><b>Re-Enter New Password</b></div>
                              <div ng-class="{ 'has-error' : pwdForm.reenter_pwd.$invalid &amp;&amp; pwd_submitted  }" class="pad">
                              <input type="password" required="" ng-model="pwd.reenter_pwd" placeholder="Re-Enter Password" name="reenter_pwd"  class="form-control">
                              </div>
                              <br>
                              <div class="pad">  
                              <input type="submit" class="btn btn-warning" ng-click="pwd_submitted=true" value="Update Password" name="submit">
                              </div>
                           </form>
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

crawlApp.factory("profileFactory", function($http,$q) {
    var get_profile_info = function () {
        var deferred = $q.defer();
        var path ="<?php echo $baseurl.'my_profile/get_profile_info'?>";
        $http.get(path)
        .success(function(data,status,headers,config){deferred.resolve(data);})
        .error(function(data, status, headers, config) { deferred.reject(status);});
        return deferred.promise;
    };
     var update_amazon_api=function(api)
    {
       return $http({
                      method: "post",
                      url: "<?php echo $baseurl.'my_profile/update_amazon_api'?>",
                      data:{
                          api_detail:angular.toJson(api)
                      }
                     }); 
                   
    };
    var update_company_api=function(com_info)
    {
       return $http({
                      method: "post",
                      url: "<?php echo $baseurl.'my_profile/update_company_api'?>",
                      data:{
                          comp_info:angular.toJson(com_info)
                      }
                     }); 
                   
    };

    var update_password=function(pwd)
    {
       return $http({
                      method: "post",
                      url: "<?php echo $baseurl.'my_profile/update_password'?>",
                      data:{
                          pwd_detail:angular.toJson(pwd)
                      }
                     }); 
                   
    };
    
    return {
       get_profile_info:get_profile_info,
       update_amazon_api:update_amazon_api,
       update_company_api:update_company_api,
       update_password:update_password
    };

});
  crawlApp.controller("profileCtrl",function profileCtrl($window,$scope,profileFactory,$sce,$q,$timeout,Upload) 
  {
    $scope.amz_api={};
    $scope.amz_api.is_edit=0;
    $scope.amz_api.store_url="http://www.amazon.it/s?ie=UTF8&me=";
    $scope.pwd={};
    $scope.com_info={};

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
  
    $scope.append_vendor_code=function()
    {

      $scope.amz_api.store_url="http://www.amazon.it/s?ie=UTF8&me="+$scope.amz_api.seller_id;
    }
     $scope.update_amazon_api=function()
      {
         if($scope.amzForm.$valid) 
          {
     //update check       
             $scope.block_site();
                 profileFactory.update_amazon_api($scope.amz_api)
                          .success(
                                    function( html )
                                    {
                                      $.unblockUI();
                                        if(html.status_code==0)
                                        {
                                            swal("Error!",html.status_text,'error');  
                                        }                    
                                        else if(html.status_code==1)
                                        {
                                            $scope.msg=html.msg;
                                            swal("Success!",html.status_text,'success');  
                                        }
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {
                                        $.unblockUI();
                                           
                                       }

                          );              
                 
            
             
              
           }
           else
           {
            console.log("Form Error ");
           }                       
      }
      $scope.update_company_api=function()
      {


         if($scope.comForm.$valid) 
          {
             $scope.block_site();
     
              profileFactory.update_company_api($scope.com_info)
                          .success(
                                    function( html )
                                    {
                                      $.unblockUI();
                                        if(html.status_code==0)
                                        {
                                            swal("Error!",html.status_text,'error');  
                                        }                    
                                        else if(html.status_code==1)
                                        {
                                            $scope.msg=html.msg;
                                            swal("Success!",html.status_text,'success');  
                                        }
                                    }
                          )
                          .error(
                                 function(data, status, headers, config)
                                      {$.unblockUI();
                                           
                                       }

                          );              
                          $.unblockUI();
           }
           else
           {
            console.log("Fomr error ");
           }                       
      }
      $scope.update_password=function()
      {
         if($scope.pwdForm.$valid) 
          {
              profileFactory.update_password($scope.pwd)
                          .success(
                                    function( html )
                                    {
                                      console.log(html);
                                        if(html.status_code==0)
                                        {
                                            swal("Error!",html.status_text,'error');  
                                        }                    
                                        else if(html.status_code==1)
                                        {
                                            // $scope.msg=html.msg;
                                            swal("Success!",html.status_text,'success');  
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
            console.log("Fomr error ");
           }                       
      }
    $scope.get_profile_info=function()
    {
        var promise=profileFactory.get_profile_info();
            promise.then(function(response){
              $scope.usr=response.details[0];
              if(response.api_details.length > 0)
              $scope.amz_api=response.api_details[0];
              if(response.com_details.length > 0)
              $scope.com_info=response.com_details[0];
            
         console.log($scope.amz_api);

                 }, 
           function(reason) {
            console.log("Reason"+reason);
         });
    }
    $scope.get_profile_info();
    $scope.send_message = function(file) 
     {
        var upload = Upload.upload({
          url: '<?php echo $baseurl.'my_profile/update_profile/';?>',
          data: {attached_file: file},
        });

        upload.then(function (response) {
          if(angular.isDefined(file))
          {
            $timeout(function () {
            file.result = response.data;

            });
            
          }
          if(response.data.status_code == '1')
           {
             swal('Success!','profile updated','success');
           }
           else
           {
             swal("Error!",response.data.status_text,'error');
           }
        }, function (response) {
          if (response.status > 0)
            $scope.errorMsg = response.status + ': ' + response.data;
        }, function (evt) {
          if(angular.isDefined(file))
          file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
        });
    }
   
});
</script>