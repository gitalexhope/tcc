<?php
$base_url=base_url();
?>

    
    <script src="<?php echo $base_url.'/asset/js2/bootstrap.min.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2/fastclick.js'?>"></script>
	<script src="<?php echo $base_url.'/asset/js2/nprogress.js'?>"></script>
	<script src="<?php echo $base_url.'/asset/js2/Chart.min.js'?>"></script>
	<script src="<?php echo $base_url.'/asset/js2/gauge.min.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2/bootstrap-progressbar.min.js'?>"></script>
	<script src="<?php echo $base_url.'/asset/js2/icheck.min.js'?>"></script> 
	<script src="<?php echo $base_url.'/asset/js2/skycons.js'?>"></script> 
	<script src="<?php echo $base_url.'/asset/js2/jquery.flot.js'?>"></script>
	<script src="<?php echo $base_url.'/asset/js2/jquery.sparkline.min.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2/jquery.flot.pie.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2/jquery.flot.time.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2//jquery.flot.stack.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2//jquery.flot.resize.js'?>"></script> 
	<script src="<?php echo $base_url.'/asset/js2/jquery.flot.orderBars.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2/jquery.flot.spline.min.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2/curvedLines.js'?>"></script>
	<script src="<?php echo $base_url.'/asset/js2/date.js'?>"></script>
    <script src="<?php echo $base_url.'/asset/js2/moment.min.js'?>"></script>
	<script src="<?php echo $base_url.'/asset/js2/custom.min.js'?>"></script>
	<script src="<?php echo $base_url.'/asset/js2/jqueryblockUI.js'?>"></script>


<script type="text/javascript">
        ;(function(){

      // Menu settings
      $('#menuToggle, .menu-close').on('click', function(){
        $('#menuToggle').removeClass('active');
        $('body').removeClass(' body-push-toleft');
        $('#theMenu').removeClass('menu-open');
      });


})(jQuery)
</script>    
<script>

$(document).ready(function(){
    
    $('[data-toggle="tooltip"]').tooltip();
    
    $('.show_hide').click(function () {
        // $(".main-menu").toggle("slide");
        $(".main-menu").slideToggle();
        $("page-container").css("margin-left:20px"); 
    });


});

</script>    
                    
              
</body>

</html>
