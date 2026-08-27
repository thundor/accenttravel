<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$offers_settings = $this->_ci->Options_model->get('offers_recommended_settings');
if(!$offers_settings){
  $offers_settings = array();
}
$items = isset($offers_settings['status']) ? $offers_settings['status'] : array();
$items = array_intersect($items, array(1));

?>
<div class="recomandate"> 
  <div class="row">
    <div class="col-0 col-sm-0 col-md-3 col-lg-4 borderTitle"></div>
    <h1 class="col-12 col-sm-12 col-md-6 col-lg-4">Oferte Recomandate</h1>
    <div class="col-0 col-sm-0 col-md-3 col-lg-4 borderTitle"></div>
    <?php foreach($items as $item_key => $item_value){
      $title = isset($offers_settings['title'], $offers_settings['title'][$item_key]) ? $offers_settings['title'][$item_key] : '';
      $icons = isset($offers_settings['icons'], $offers_settings['icons'][$item_key]) ? $offers_settings['icons'][$item_key] : '';
      $no = isset($offers_settings['no'], $offers_settings['no'][$item_key]) ? $offers_settings['no'][$item_key] : 0;
      $image = isset($offers_settings['image'], $offers_settings['image'][$item_key]) ? $offers_settings['image'][$item_key] : '';
      $url = isset($offers_settings['url'], $offers_settings['url'][$item_key]) ? $offers_settings['url'][$item_key] : '';
      if(strlen($url)){
        $url_parsed = parse_url($url);
        if(!isset($url_parsed['host']) && !isset($url_parsed['scheme'])){
          $url = site_url($url);
        }
      } else {
        $url = 'javascript:void(0);';
      }
      $discount = isset($offers_settings['discount'], $offers_settings['discount'][$item_key]) ? $offers_settings['discount'][$item_key] : 0;
      $icons_arr = explode(',', $icons);
      $icons_arr = array_diff($icons_arr,array(''));
    ?>
    <div class="col-6 col-sm-6 col-md-6 col-lg-3 oferta">
      <?php if($discount) { ?>
      <p class="promo">-<?php echo $discount; ?>%</p>
      <?php } ?>
      <?php if($icons_arr) { ?>
      <span><?php 
        $fa_start = '<i class="fa fa-';
        $fa_end = '"></i>';
        echo $fa_start . implode($fa_end . ' ' . $fa_start, $icons_arr) . $fa_end;
      ?>
      </span>
      <?php } ?>
      <a href="<?php echo $url; ?>"><img src="<?php echo $this->theme_url; ?>assets/images/<?php echo $image; ?>" alt="" /></a>
      <h3><a href="<?php echo $url; ?>"><?php echo htmlspecialchars($title); ?> <br /><?php echo $no ? '<span>' . ($no == 1 ? '1 oferta' : $no . ' oferte') . '</span>' : ''; ?></a></h3>
    </div>
    <?php } ?>
  </div> 
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>