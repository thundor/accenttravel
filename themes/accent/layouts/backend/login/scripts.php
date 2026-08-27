<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script src="<?php echo $this->theme_url; ?>assets/js/login.js"></script>
<script>
  $(function() {
    Login.init()
  });
</script>
<script type="text/javascript">
$(function () {
  //login functionality
  $(".form-signin").on('submit',function(){
    $(".resultlogin").html("<div class='alert alert-info loading wow fadeOut animated'>Hold On...</div>");
    $.ajax({
      url: "<?php echo site_url('backend/account/login'); ?>",
      dataType: "json",
      method: "POST",
      data: $(".form-signin").serialize(),
      success:function(resp){
        if(resp.status !== 'success'){
          $(".resultlogin").html("<div class='alert alert-danger loading wow fadeIn animated'>"+resp.message+"</div>");
        } else {
          $(".resultlogin").html("<div class='alert alert-success login wow fadeIn animated'>Redirecting Please Wait...</div>");
          window.location.replace(resp.data.url);
        }
      }
    });
  });
  // end login functionality

  // start password reset functionality
  $(".resetbtn").on('click',function(){
    var resetemail = $("#resetemail").val();
    if(resetemail == ""){
      alert("Please Enter Email Address");
    } else {
      $(".resultreset").html("<div id='rotatingDiv'></div>");
      $.ajax({
        url: "<?php echo site_url('backend/account/resetpass'); ?>",
        dataType: "json",
        method: "POST",
        data: $("#passresetfrm").serialize(),
        success:function(resp){
          if(resp.status !== 'success'){
            $(".resultreset").html("<div class='alert alert-danger loading wow fadeIn animated'>"+resp.message+"</div>");
          } else {
            $(".resultreset").html("<div class='alert alert-success'>New Password sent to "+resetemail+", Kindly check email.</div>");
            window.location.replace(resp.data.url);
          }
        }
      });
    }
 });
  // end password reset functionality

});
</script>
<script>
  // Bind normal buttons
  Ladda.bind( 'div:not(.progress-demo) button', { timeout: 2000 } );

  // Bind progress buttons and simulate loading progress
  Ladda.bind( '.progress-demo button', {
    callback: function( instance ) {
      var progress = 0;
      var interval = setInterval( function() {
        progress = Math.min( progress + Math.random() * 0.1, 1 );
        instance.setProgress( progress );
        if( progress === 1 ) {
          instance.stop();
          clearInterval( interval );
        }
      }, 200 );
    }
  } );
</script>
<script>
var cb, optionSet1;
$(".checkbox").iCheck({
  checkboxClass: "icheckbox_square-grey",
  radioClass: "iradio_square-grey"
});

$(".radio").iCheck({
  checkboxClass: "icheckbox_square-grey",
  radioClass: "iradio_square-grey"
});
</script>
<script>
  new WOW().init();
</script>
<!-- WOWJs -->
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>