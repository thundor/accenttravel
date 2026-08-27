<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Options_model');
$sliders_settings = $this->_ci->Options_model->get('sliders_homepage_settings');
if(!$sliders_settings){
  $sliders_settings = array();
}
$items = isset($sliders_settings['status']) ? $sliders_settings['status'] : array();
$items = array_intersect($items, array(1));
$items = array_keys($items);
shuffle($items);
$items = array_values($items);
?>
<div id="myCarousel" class="carousel slide homePageC" data-ride="carousel">
  <ol class="carousel-indicators">
    <?php foreach($items as $k=>$item_key){ ?>
    <li data-target="#myCarousel" data-slide-to="<?php echo $item_key; ?>" class="<?php echo !$k ? 'active' : ''; ?>"></li>
    <?php } ?>
  </ol>
  <div class="carousel-inner">
    <?php foreach($items as $k=>$item_key){
      $title = isset($sliders_settings['title'], $sliders_settings['title'][$item_key]) ? trim($sliders_settings['title'][$item_key]) : '';
      $description = isset($sliders_settings['description'], $sliders_settings['description'][$item_key]) ? trim($sliders_settings['description'][$item_key]) : '';
      $button = isset($sliders_settings['button'], $sliders_settings['button'][$item_key]) ? trim($sliders_settings['button'][$item_key]) : '';
      $url = isset($sliders_settings['url'], $sliders_settings['url'][$item_key]) ? trim($sliders_settings['url'][$item_key]) : '';
      if(strlen($url)){
        $url_parsed = parse_url($url);
        if(!isset($url_parsed['host']) && !isset($url_parsed['scheme'])){
          $url = site_url($url);
        }
      } else {
        $url = 'javascript:void(0);';
      }
    ?>
    <div class="carousel-item <?php echo !$k ? 'active' : ''; ?>">
      <?php /* <img  src="<?php echo $this->theme_url; ?>assets/images/ico-eye1.png"> */ ?>
      <div class="container">
        <div class="carousel-caption d-none d-md-block">
			<?php if('' !== $title) { ?>
          <h1><?php echo $title; ?></h1>
			<?php } ?>
          <p><?php echo $description; ?></p>
          <?php if(strlen($button)){ ?>
          <p><a class="btn btn-lg btn-primary" href="<?php echo trim($url); ?>" role="button"><?php echo trim($button); ?></a></p>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
  <?php if (count($items) > 1) { ?>
  <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Anterior</span>
  </a>
  <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Urmator</span>
  </a>
  <?php } ?>
</div>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>