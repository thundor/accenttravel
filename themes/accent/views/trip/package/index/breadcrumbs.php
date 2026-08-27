<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = &$this->package_search_data;
?>
<div class="row blockBack">
  <div class="col-sm-4  mt-3 pr-0">
    <p>
      <a href="<?php echo site_url('trip/packages'); ?>" class="backToCat"><i class="fa fa-caret-left mt-1"></i> Inapoi la vacante</a>
    </p>
  </div>
  <div class="col-sm-4  mt-3 pr-0 pl-0 text-center">
     <a href="#descrierePachet"  class="backToCat">Descriere vacanta</a> 
     <i class=" fa fa-chevron-down"></i> 
   </div>
  <div class="col-sm-4  mt-3 pl-0 text-right">
    <p>Finalizare rezervare <i class="fa fa-caret-right mt-1"></i></p>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>