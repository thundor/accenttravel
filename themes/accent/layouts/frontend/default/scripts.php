<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$sliders_settings = $this->_ci->Options_model->get('sliders_homebackground_settings');
if(!$sliders_settings){
  $sliders_settings = array();
}
$items = isset($sliders_settings['status']) ? $sliders_settings['status'] : array();
$items = array_intersect($items, array(1));
$items = array_keys($items);
shuffle($items);
$images = array();
foreach($items as $item_key){
  $image = isset($sliders_settings['image'], $sliders_settings['image'][$item_key]) ? trim($sliders_settings['image'][$item_key]) : '';
  if(strlen($image)){
    $image = $this->theme_url . 'assets/images/bgs/' . $image;
    $images[] = $image;
  }
}
?>
<script type="text/javascript">//<!--
;(function($){
	$(document).ajaxComplete(function (e, xhr, settings) {
		console.warn(xhr.status, settings.url);
		if(xhr.status == 403) {
			window.location.href = window.location.href.replace(/#.*/, '');
		}
	})
})(jQuery)
//--></script>
<script type="text/javascript">
;(function($){
  $('.bg-home-slider').backstretch(<?php echo json_encode($images); ?>, {
      fade: 750,
      duration: 4000
  });
})(jQuery);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>