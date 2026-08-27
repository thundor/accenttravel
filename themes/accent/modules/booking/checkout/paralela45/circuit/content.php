<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $data = $this->view_data; ?>
<div class="form-group">
  <div class="custom-controls-stacked d-block">
    <label class="custom-control custom-checkbox">
      <input form="bookingCheckout" id="payment_details_tos" type="checkbox" name="tos" value="1" class="custom-control-input" required>
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">Am citit si sunt de acord pe deplin cu <a target="_BLANK" href="/termeni-si-conditii">termenii si conditiile</a> specificate de catre Accent Travel &amp; Events.</span>
    </label>
  </div>
</div>
<div class="form-group">
  <div class="custom-controls-stacked d-block">
    <label class="custom-control custom-checkbox">
      <input form="bookingCheckout" id="payment_details_tpc" type="checkbox" name="tpc" value="1" class="custom-control-input" required>
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">Sunt de acord cu prelucrarea datelor cu caracter personal conform <a target="_BLANK" href="/declaratie-de-consimtamant">Declaratiei de consimtamant</a>.</span>
    </label>
  </div>
</div>
<div class="form-group col-12 offset-sm-8 col-sm-4">
  <button form="bookingCheckout" type="submit" name="confirm" id="confirmButton" class="btn btn-block btn-primary">REZERVA</button>
</div>
<form id="bookingCheckout" name="bookingCheckout" action="<?php echo site_url('paralela45/circuit/checkout'); ?>" method="POST" onsubmit="return false;">
<input type="hidden" name="offer_id" value="<?php echo htmlspecialchars($data['offer_id']); ?>" />
<input type="hidden" name="package_variant_id" value="<?php echo htmlspecialchars($data['charter_id']); ?>" />
<input type="hidden" name="package_id" value="<?php echo htmlspecialchars($data['search_id']); ?>" />
<input type="hidden" name="occupancy" value="<?php echo htmlspecialchars(json_encode($data['occupancy'])); ?>" />
<input type="hidden" name="departure_city_code" value="<?php echo htmlspecialchars($data['departure_city_code']); ?>" />
<input type="hidden" name="destination_city_code" value="<?php echo htmlspecialchars($data['destination_city_code']); ?>" />
<input type="hidden" name="destination_country_code" value="<?php echo htmlspecialchars($data['destination_country_code']); ?>" />
<input type="hidden" name="start_date" value="<?php echo htmlspecialchars($data['start_date']); ?>" />
<input type="hidden" name="nights" value="<?php echo htmlspecialchars($data['nights']); ?>" />
<input type="hidden" name="hotel_name" value="<?php echo htmlspecialchars($data['hotel_name']); ?>" />
<input type="hidden" id="totalExpectedPrice" name="total_expected_price" value="" />
<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
<input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
<input type="text" name="<?php echo md5($this->_ci->security->get_csrf_hash()); ?>" value="" class="form-sec" />
<?php } ?>
</form>
<div id="result_bookingCheckout" class="form-group"></div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>