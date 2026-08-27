<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div id="hotelResults">
</div>
<form id="hotelResultModel" target="_BLANK" action="<?php site_url('trip/citybreak'); ?>" method="POST" class="boxHotel border-0 noboxSh mb-1" style="display:none;">
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <div class="row">
    <div class="col-sm-12 col-lg-4">
      <a class="hotel-image" href="#"></a>
    </div>
    <div class="col-sm-8 col-lg-6">
      <h2><a href="#" class="hotel-name"></a><br /><span class="hotel-stars"></span></h2>
      <p><i class="fa fa-map-marker"></i> <span class="hotel-location"></span> | <a href="javascript:void(0);" class="hartaHotel" > Arata harta <i class="fa fa-map-o"></i></a></p>
      <p class="hotel-info"><span class="hotel-info-short"></span><a class="show-rest" href="#">vezi descriere</a><span class="hotel-info-rest"></span></p>
      <p class="greyCol hotel-info-accomodation"></p>
    </div>
    <div class="col-sm-4 col-lg-2">
      <p>
        <span class="bigPrice initial-price">Tarif initial<br /> 
          <strong></strong>
        </span>
        <br />
        <span class="pretSrcH current-price"></span>
        <br /> 
        <div class="hotel-room-type"></div>
        <div class="hotel-dinner"></div>
      </p>
      <button type="submit" class="reserve-button btn btn-block btn-primary rounded-0">REZERVA</button>
      <a class="notification-button btn btn-block btn-success rounded-0" data-toggle="tooltip" title="Notifica-ma cand pretul va scadea cu cel putin 10%"><i class="fa fa-bell" style="color:#fff;"></i> Alerta pret</a>
    </div>
  </div>
  <div class="flightResults">
  </div>
  <i class="fa fa-close inchideH"></i>
</form>
<div id="modalMapH">
  <div class="row">
    <div class="col-sm-12">
      <span class="btn btn-outline-danger float-right mb-3">Inchide</span>
      <h4 class="float-left"></h4>
    </div>
    <div class="col-sm-12">
      <div id="googleMap" style="height:450px;"></div>
      <?php /* <iframe src="about:blank" height="450"   style="border:0; width:100%" allowfullscreen></iframe> */ ?>
    </div>
  </div>
</div>
<?php themeFunctions::loadModule('trip/notifications',__FILE__); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>