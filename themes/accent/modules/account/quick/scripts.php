<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadModule('helpers/titles/json', __FILE__ . '/json_selections'); ?>
<?php themeFunctions::loadAddons(__FILE__ . '/json_selections'); ?>
<script type="text/javascript">
;(function($){
  $('#register_title').select2_4({theme:'bootstrap',containerCssClass:'input-lg',placeholder:'Alegeti',minimumResultsForSearch:10, data: select2_adult_titles_prefix_selections, width: '100%'});
$("#contulMeu").on("click", function (e) {
  $("#utileVacanta").hide();
  $("#contOpt").toggle();
});
// login modal	
$("#login").on("click", function (e) {
  $("#loginWindow").show();
  $("#contOpt").hide();
});
$("#loginClose").on("click", function (e) {
  $("#loginWindow").hide();
  $("#contOpt").hide();
});
$("#regClose").on("click", function (e) {
  $("#regWindow").hide();
  $("#contOpt").hide();
});
$("#forgotClose").on("click", function (e) {
  $("#forgotWindow").hide();
  $("#contOpt").hide();
});
// fereastra modala inregistrare cont
$("#register").on("click", function (e) {
  $("#regWindow").show();
});
$("#register2").on("click", function (e) {
  $("#regWindow").show();
  $("#loginWindow").hide();
  $("#forgotWindow").hide();
});
$("#forgot2").on("click", function (e) {
  $("#regWindow").hide();
  $("#loginWindow").hide();
  $("#forgotWindow").show();
});
var clicked_outside = false;
$(document).mousedown(function(e){
  var container = $("#quick-login-content");
  // if the target of the click isn't the container nor a descendant of the container
  clicked_outside = false;
  if (!container.is(e.target) && container.has(e.target).length === 0){
    clicked_outside = true;
  }
}).mouseup(function(e){
  if(clicked_outside){
    var container = $("#quick-login-content");
    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0){
      $("#utileVacanta").hide();
      $("#regWindow").hide();
      $("#forgotWindow").hide();
      $("#contOpt").hide();
      $("#loginWindow").hide();
    }
  }
  clicked_outside = false;
});
function loginSubmitCallback($form,resp,$error_container){
  showMessage($error_container,resp.message,'success');
  if(resp.data.url){
    window.location.replace(resp.data.url);
  }
  return false;
}
function resetSubmitCallback($form,resp,$error_container){
  showMessage($error_container,resp.message,'success');
  if(resp.data.url){
    window.location.replace(resp.data.url);
  }
  return false;
}
$('#loginForm').on('submit',function(e){e.preventDefault();basicFormPostSubmit(this,"<?php echo site_url('account/login'); ?>", loginSubmitCallback);});
$('#registerForm').on('submit',function(e){e.preventDefault();basicFormPostSubmit(this,"<?php echo site_url('account/register'); ?>", loginSubmitCallback);});
$('#forgotForm').on('submit',function(e){e.preventDefault();basicFormPostSubmit(this,"<?php echo site_url('account/resetpass'); ?>", resetSubmitCallback);});
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>