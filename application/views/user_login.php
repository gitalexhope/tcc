<?php 
$baseurl=base_url();
?>
<!DOCTYPE html>

<html lang="en" ng-app='crawler'>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	    <meta charset="utf-8">
	    <title>TCC | Admin</title>
	    <link rel="icon" type="image/png" href="asset/img/favicon.png"/>

	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <meta name="description" content="">
	    <meta name="author" content="">
	    <link href="<?php echo $baseurl.'asset/css/bootstrap.css' ;?>" rel="stylesheet">
		<link href="<?php echo $baseurl.'asset/font-awesome/css/font-awesome.css' ;?>" rel="stylesheet">
		<link href="<?php echo $baseurl.'asset/css1/simplify.css' ;?>" rel="stylesheet">
        <style type="text/css">
        	.sign-in-inner
        	{
        		width:700px !important;
        	}
        	.sbox
        	{
        		background: #fff;
        		padding: 20px;
        		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        	}
        </style>
  	</head>
  	<body class="light-background">

		<div class="wrapper no-navigation">
			<div class="sign-in-wrapper">
		
				<div class="sign1-in-inner" >

				  <div class="row">
				  <div class="col-sm-3"></div>
				  <div class="col-sm-6 sbox">
				    <div class="col-sm-6 login-brand text-center">
						<img style='width:100%;margin:20px 0px' src='<?php echo $baseurl.'asset/img/logo.jpg';?>'/>
					</div>
                   
                   	<div class="col-sm-6"><br><br><br>
                   	<?php if(validation_errors()){echo '<div style="color:#f00" >'.validation_errors()."</div>";} ?>
					<form action='<?php echo $baseurl.'user_auth/login';?>' method='post' >
						<div class="form-group m-bottom-md">
							<input class="form-control" placeholder="Email Address" type="text" name='username'>
						</div>
						<div class="form-group">
							<input class="form-control" placeholder="Password" type="password" name='password'>
						</div>
						<div class="m-top-md p-top-sm">
                        <input type='submit' class="btn btn-success block" style="width:100%" value="Sign In" >
							
						</div>
						<!---<div class="m-top-md p-top-sm">
							<div class="font-12 text-center m-bottom-xs">Do not have an account?</div>
							<a href="<?php echo $baseurl.'user_auth/signup';?>" class="btn btn-default block">Create an accounts</a> --->
							
						</div>

					</form>
					
					</div>
					</div>
				  </div>	
				</div><!-- ./sign-in-inner -->
			</div><!-- ./sign-in-wrapper --><!--Start of Zopim Live Chat Script-->

<!--End of Zopim Live Chat Script-->
			
		</div><!-- /wrapper -->

		<a href="" id="scroll-to-top" class="hidden-print"><i class="icon-chevron-up"></i></a>
			
		</div><!-- /wrapper -->

		<a href="#" class="scroll-to-top hidden-print"><i class="fa fa-chevron-up fa-lg"></i></a>
		
	    <!-- Le javascript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
		
		<!-- Jquery -->
		<script src="<?php echo $baseurl.'asset/css1/jquery-1.js' ;?>"></script>
		
		<!-- Bootstrap -->
	    <script src="<?php echo $baseurl.'asset/css1/bootstrap.js' ;?>"></script>
		
		<!-- Datatable -->
		<script src="<?php echo $baseurl.'asset/css1/jquery_003.js' ;?>"></script>
		
		<!-- Slimscroll -->
		<script src="<?php echo $baseurl.'asset/css1/jquery.js' ;?>"></script>

		<!-- Popup Overlay -->
		<script src="<?php echo $baseurl.'asset/css1/jquery_002.js' ;?>"></script>

		<!-- Modernizr -->
		<script src="<?php echo $baseurl.'asset/css1/modernizr.js' ;?>"></script>
		
		<!-- Simplify -->
		<script src="<?php echo $baseurl.'asset/css1/simplify.js' ;?>"></script>

		
  	

</body></html>
