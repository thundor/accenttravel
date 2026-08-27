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
<form id="bookingCheckout" name="bookingCheckout" action="<?php echo site_url('trip/package/checkout'); ?>" method="POST" onsubmit="return false;">
<input type="hidden" name="package_id" value="<?php echo (int)$data['package_id']; ?>" />
<input type="hidden" name="code" value="<?php echo htmlspecialchars($data['code']); ?>" />
<input type="hidden" name="entry_id" value="<?php echo htmlspecialchars($data['entry_id']); ?>" />
<input type="hidden" name="rate_group_id" value="<?php echo htmlspecialchars($data['rate_group_id']); ?>" />
<?php foreach($data['occupations'] as $room_key=>$options){
  if(isset($options['extra-services'])){
    foreach($options['extra-services'] as $service_id=>$occupations){
      foreach($occupations as $occupation_type=>$values){
        if($occupation_type == 'a'){ ?>
          <input type="hidden" name="occupations[<?php echo $room_key; ?>][extra-services][<?php echo $service_id; ?>][a]" value="<?php echo htmlspecialchars($values); ?>" />
        <?php
        } else {
          foreach($values as $k=>$v){ ?>
            <input type="hidden" name="occupations[<?php echo $room_key; ?>][extra-services][<?php echo $service_id; ?>][c][]" value="<?php echo htmlspecialchars($v); ?>" />
          <?php
          }
        }
      }
    }
  }
  if(isset($options['rooms']) && is_array($options['rooms'])){
    foreach($options['rooms'] as $k=>$v){ ?>
      <input type="hidden" name="occupations[<?php echo $room_key; ?>][rooms][<?php echo htmlspecialchars($k); ?>]" value="<?php echo htmlspecialchars($v); ?>" />
    <?php
    }
  }
}
foreach($data['selected_extra_services'] as $service_id=>$options){
  if(isset($options['entries']) && is_array($options['entries'])){
    foreach($options['entries'] as $k=>$v){ ?>
      <input type="hidden" name="extra-services[<?php echo $service_id; ?>][entries][<?php echo htmlspecialchars($k); ?>]" value="<?php echo htmlspecialchars($v); ?>" />
    <?php
    }
  }
  if(isset($options['price-set']) && is_array($options['price-set'])){
    foreach($options['price-set'] as $k=>$v){ ?>
      <input type="hidden" name="extra-services[<?php echo $service_id; ?>][price-set][<?php echo htmlspecialchars($k); ?>]" value="<?php echo htmlspecialchars($v); ?>" />
    <?php
    }
  }
} ?>
<?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
<input type="hidden" id="csrfToken" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
<input type="text" name="<?php echo md5($this->_ci->security->get_csrf_hash()); ?>" value="" class="form-sec" />
<?php } ?>
</form>
<div id="result_bookingCheckout" class="form-group"></div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>