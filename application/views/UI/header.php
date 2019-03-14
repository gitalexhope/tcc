<?php
$base_url=base_url();

?>
<!DOCTYPE html>
<html lang="en" ng-app='crawler'>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>TCC | Admin</title>
    <link rel="icon" type="image/png" href="asset/img/favicn.png"/>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo $base_url.'/asset/css2/bootstrap.min.css';?>" rel="stylesheet">
    <link href="<?php echo $base_url.'/asset/css2/font-awesome.min.css';?>" rel="stylesheet">
    <link href="<?php echo $base_url.'/asset/css2/nprogress.css';?>" rel="stylesheet">
    <link href="<?php echo $base_url.'/asset/css2/green.css';?>" rel="stylesheet">
    <link href="<?php echo $base_url.'/asset/css2/bootstrap-progressbar-3.3.4.min.css';?>" rel="stylesheet">
    <link href="<?php echo $base_url.'/asset/css2/daterangepicker.css';?>" rel="stylesheet">
    <link href="<?php echo $base_url.'/asset/css2/custom.min.css';?>" rel="stylesheet">
	<link href="<?php echo $base_url.'asset/css1/sweetalert.css' ;?>" rel="stylesheet">
	<script src="<?php echo $base_url.'/asset/ngapp/vendor/angular.min.js'?>"></script>
    <script src="<?php echo $base_url.'asset/ngapp/file_upload/ng-file-upload-shim.min.js' ;?>"></script>
    <script src="<?php echo $base_url.'asset/ngapp/file_upload/ng-file-upload.min.js' ;?>"></script>

    <script src="<?php echo $base_url.'/asset/ngapp/vendor/angular-animate.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/ngapp/vendor/ui-bootstrap-tpls-2.5.0.min.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/ngapp/app.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/ngapp/directive.js'?>"></script>
    <script src="<?php echo $base_url.'asset/js/sweetalert.min.js' ;?>"></script>
    <script src="<?php echo $base_url.'/asset/js/jquery.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js/plugins/morris/raphael.min.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js/plugins/morris/morris.min.js'?>"></script>
</head>
<body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?php echo $base_url.'amazon_inventory'?>" class="site_title"><i class="fa fa-registered"></i> <span>TCC</span></a>
            </div>

            <div class="clearfix"></div>

            
            

            <br />

            
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
               <!--- <h3>General</h3> --->
              <ul class="nav side-menu">
			 				   <?php
                     if($this->login_model->notadminLoginCheck())
                      {
                   ?>
				  <li id='test'><a class='test' href="<?php echo $base_url.'orders_report_orderby_date_list'?>"><i class="fa fa-database"></i> Reports</a></li>
				  <li id='test1'><a class='test1' href="<?php echo $base_url.'product_list'?>"><i class="fa fa-info"></i> Product Attributes</a></li> 
				  <li id='test2' ><a href="<?php echo $base_url.'Order_finance_data'?>"><i class="fa fa-usd"></i>Finnance Api</a></li> 
				  <?php
                    }
                   ?>
				   <?php
                     if($this->login_model->adminLoginCheck())
                      {
                   ?>

                  <li ><a href="<?php echo $base_url.'manage_users'?>"><i class="fa fa-users"></i>Users</a></li>
				   <li><a href="<?php echo $base_url.'super_admin'?>"><i class="fa fa-users"></i>Super Admin</a></li>
				  <?php
                    }
                   ?>
                </ul> 
              </div>

            </div>
            

            
 
            
          </div>
        </div>

        
        <div class="top_nav">
          <div class="nav_menu">
            <nav>
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
			      <?php
                     if($this->login_model->notadminLoginCheck())
                      {
                   ?>
			 	<div class="col-sm-3" style="margin-top:15px;margin-right:-100px;">

					<select class="form-control" id="dynamic_select">
                    <option value="" selected>Select  a Report Data</option>
	<!---<option  id='showme' value="<?php echo $base_url.'amazon_inventory'?>"> Active And Inactive Listings Report</option> --->
   <!--- <option  id='showme1' value="<?php echo $base_url.'orders_report_list'?>">Flat File Order Report</option> --->
   	<option  id='showme5' value="<?php echo $base_url.'orders_report_orderby_date_list'?>">Flat File All Orders Report by Order Date</option>
    <option  id='showme3' value="<?php echo $base_url.'orders_fullfill_shipment_report_list'?>">FBA Amazon Fulfilled Shipments Report</option>
    <option  id='showme4' value="<?php echo $base_url.'orders_report_last_update_list'?>">Flat File All Orders Report by Last Update</option> 
    <option  id='showme6' value="<?php echo $base_url.'orders_report_fba_ship_data_list'?>">FBA Customer Shipment Sales Report</option>	
	<!--<option  id='showme7' value="<?php echo $base_url.'orders_report_actionable_data_list'?>">Unshipped Orders Report</option>	
	<option  id='showme8' value="<?php echo $base_url.'orders_report_flat_list'?>">Requested or Scheduled Flat File Order Report</option>	--->
    <option  id='showme9' value="<?php echo $base_url.'order_fba_returns_data'?>">FBA Returns Report</option>
    <option  id='showme9' value="<?php echo $base_url.'restock_inventory_data'?>">Restock Inventory Report</option>
    <option  id='showme9' value="<?php echo $base_url.'fba_inventory_health_data'?>">FBA Inventory Health Report</option>
    <option  id='showme9' value="<?php echo $base_url.'stranded_inv_ui_data'?>">FBA Stranded Inventory Report</option>	
	<option  id='showme9' value="<?php echo $base_url.'active_inventory_report'?>">Active Listings Report</option>
    <option  id='showme9' value="<?php echo $base_url.'inactive_inventory_report'?>">Inactive Listings Report</option>
    <!---<option  id='showme9' value="<?php echo $base_url.'order_fba_storage_fee_data'?>">FBA Storage Fees Report</option> --->
    <option  id='showme9' value="<?php echo $base_url.'fba_estimated_fees_txt_data'?>">FBA Fee Preview Report</option>
    <option  id='showme9' value="<?php echo $base_url.'order_fba_shipment_replacement_data'?>">FBA Replacements Report</option>	
	
	
	

	
    </select>​
					 
              </div>
<?php
                    }
                   ?>
              <ul class="nav navbar-nav navbar-right" style="margin-right:5px;margin-top:15px;">
                <li class="">
                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <?php
                           $user=$this->session->userdata('user_logged_in');
                           echo $user['fname'];
                        ?>
                              
                    <span class="fa fa-angle-down"></span>
                  </a>
                  <ul class="dropdown-menu dropdown-usermenu pull-right">
				  <?php
                     if($this->login_model->notadminLoginCheck())
                      {
                   ?>
                   <li><a href="<?php echo $base_url.'my_profile'?>"> Profile</a></li>
				   <?php
					  }
                   ?>
                   <!---  <li><a href="javascript:;">Help</a></li> --->
                    <li><a  href="<?php echo $base_url.'user_auth/logout'?> "><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                  </ul>
                </li>

                
                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>

         <div class="right_col" role="main">
          
        <!---  <script type="text/javascript">
    window.onload = function(){
        location.href=document.getElementById("selectbox").value;
    }       
</script> --->
<script>
    $(function(){
    $('#dynamic_select').on('change', function () {
          var url = $(this).val();
		 
   if (url) { 
              window.location = url;
			  
			}
			
	     // alert(url);
          return false;
      });
    });
</script>


<script>
if(window.location =='<?php echo $base_url.'order_fba_returns_data'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'restock_inventory_data'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'fba_inventory_health_data'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'stranded_inv_ui_data'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'product_list'?>')
			  {
	   $(document).ready(function(){
       $("#test1").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'orders_report_list'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'orders_fullfill_shipment_report_list'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'orders_report_last_update_list'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'orders_report_orderby_date_list'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'orders_report_fba_ship_data_list'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'orders_report_actionable_data_list'?>')
			  {
	   $(document).ready(function(){
        $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'orders_report_flat_list'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'active_inventory_report'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'inactive_inventory_report'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'order_fba_storage_fee_data'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'order_fba_shipment_replacement_data'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'fba_estimated_fees_txt_data'?>')
			  {
	   $(document).ready(function(){
       $("#test").addClass("active");
    });	  
}
else if(window.location =='<?php echo $base_url.'Order_finance_data'?>')
			  {
	   $(document).ready(function(){
       $("#test2").addClass("active");
    });	  
}

</script>






