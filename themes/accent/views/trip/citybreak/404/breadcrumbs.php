<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $data = &$this->hotel_search_data; ?>
<div class="row blockBack">
  <div class="col-sm-4  mt-3 pr-0">
    <p>
      <a href="<?php echo site_url('trip/citybreaksasync'); ?>" class="backToCat"><i class="fa fa-caret-left mt-1"></i> Inapoi la oferte <?php echo $data['destination_city_name']; ?></a>
    </p>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>