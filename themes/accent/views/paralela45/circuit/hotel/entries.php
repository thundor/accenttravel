<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data; 
$hotel_details = $this->view_data['product'];
?>
<div id="package_entries"></div>
<div class="row mt-2">
  <div class="col-12">
  </div>
</div>
<form id="package-model" class="hotel-package card" action="<?php echo site_url('paralela45/circuit/booking/' . $data['hotel_code']); ?>" method="POST" style="display:none;">
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <input name="offer_id" value="" type="hidden" />
  <input name="offer_search_id" value="" type="hidden" />
  <input name="offer_variant_id" value="" type="hidden" />
  <input name="offer_occupancy" value="" type="hidden">

  <div class="room-options card-block">
    <div class="room-option">
      <div class="row chooseHead">
        <div class="col-12 col-sm-8">
          <h2><span class="choose-room-name">Oferta <span class="package-number"></span></span> <i class="fa fa-angle-down"></i></h2>
        </div>
      </div>
      <div class="package-rooms">
        <div class="row roomShow">
          <div class="thUp col-12 col-sm-6 col-lg-3 pr-0 pl-0">
            <h3>Nume</h3>
            <p>
              <strong></strong><br />
              <small></small>
            </p>
          </div>
          <div class="thUp col-12 col-sm-6 col-lg-2 pl-0 pr-0">
            <h3>Disponibilitate</h3>
            <p></p>
          </div>
          <div class="thUp col-12 col-sm-6 col-lg-3 pl-0 pr-0">
            <h3>Informatii</h3>
            <p class="text-left"></p>
          </div>
          <div class="thUp col-12 col-sm-6 col-lg-2 pl-0 pr-0">
            <h3 class="text-center">Pret</h3>
            <p class="text-center"></p>
          </div>
          <div class="thUp col-12 col-sm-12 col-lg-2 pr-0 pl-0">
            <h3>&nbsp;</h3>
            <p class="booking_button_wrapper">
              <button type="submit" role="button" class="btn btn-success rounded-0" onclick="jQuery('input[type=radio]', jQuery(this).parent().parent()).prop('checked',true); return true;">Rezerva acum</button>
              <br />
              Dureaza maxim 2 minute
              <br>
              <i class="fa fa-clock-o"></i>
            </p>
            <p class="no_booking_button_wrapper">
              Nu se mai pot efectua rezervari pentru aceasta oferta.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>