<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="packageResults">
</div>
<div id="packageResultModel" class="boxHotel" style="display:none;">
  <div class="row">
    <div class="col-sm-12 col-lg-4">
      <a class="hotel-image" href="#"></a>
    </div>
    <div class="col-sm-8 col-lg-6">
      <h2><a href="#" class="package-name"></a><br /><span class="package-stars"></span></h2>
      <?php /* <p class="package-category"></p> */ ?>
      <p class="greyCol package-project"></p>
      <p class="package-description" style="white-space:pre-wrap;"><span class="hotel-info-short"></span><a class="show-rest" href="#">vezi descriere</a><span class="hotel-info-rest"></span></p>
    </div>
    <div class="col-sm-4 col-lg-2">
      <p>
        <br />
        de la <span class="pretSrcH current-price"></span>
        <br /> 
      </p>
      <a class="reserve-button btn btn-block bg-primary rounded-0">REZERVA</a>
      <a class="notification-button btn btn-block btn-success rounded-0" data-toggle="tooltip" title="Notifica-ma cand pretul va scadea cu cel putin 10%"><i class="fa fa-bell" style="color:#fff;"></i> Alerta pret</a>
    </div>
  </div>
  <i class="fa fa-close inchideH"></i>
</div>
<?php themeFunctions::loadModule('trip/notifications',__FILE__); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>