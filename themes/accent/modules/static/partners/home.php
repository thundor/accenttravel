<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$static_settings = $this->_ci->Options_model->get('static_partners_settings');
if(!$static_settings){
  $static_settings = array();
}
$items = isset($static_settings['status']) ? $static_settings['status'] : array();
$items = array_intersect($items, array(1));
if($items){
?>
<div class="container">
  <div class="tematice">
    <div class="row">
      <div class="col-0 col-sm-0 col-md-3 col-lg-4 borderTitle"></div>
      <h1 class="col-12 col-sm-12 col-md-6 col-lg-4">PARTENERI ACCENT</h1>
      <div class="col-0 col-sm-0 col-md-3 col-lg-4 borderTitle"></div>
      <?php foreach($items as $item_key => $item_value){
        $title = isset($static_settings['title'], $static_settings['title'][$item_key]) ? trim($static_settings['title'][$item_key]) : '';
        $image = isset($static_settings['image'], $static_settings['image'][$item_key]) ? $static_settings['image'][$item_key] : '';
        $url = isset($static_settings['url'], $static_settings['url'][$item_key]) ? trim($static_settings['url'][$item_key]) : '';
        if(strlen($url)){
          $url_parsed = parse_url($url);
          if(!isset($url_parsed['host']) && !isset($url_parsed['scheme'])){
            $url = site_url($url);
          }
        } else {
          $url = 'javascript:void(0);';
        }
      ?>
      <div class="col-6 col-sm-4 partBan">
        <a href="<?php echo $url; ?>"><img data-toggle="tooltip" title="<?php echo htmlspecialchars($title); ?>" src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($title); ?>" /></a>                    	
      </div>
      <?php } ?>
    </div> 
  </div>
</div>
<?php } ?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>