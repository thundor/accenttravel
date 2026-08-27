<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php 
$u0 = $this->_ci->uri->segment(0);
$u1 = $this->_ci->uri->segment(1);
$coupon_type = 'unknown';
if($u0 == 'trip') {
  $coupon_type = $u1;
} elseif($u0 == 'paralela45'){
  $coupon_type = $u0 . '_' . $u1;
} ?>
<h3 class="subSecTicket col-12 mb-4">Cupon promotional</h3>
<p class="infoCoup ml-3"><i class="fa fa-info-circle"></i> Ati primit un cupon promotional? Introduceti-l in acest camp si dati click pe butonul "<b>Activare cupon</b>" pentru a beneficia de reducere. Un singur cupon poate fi utilizat per comanda.</p>
<div id="infoCoupon" class="col-12">
  <form id="couponForm" name="couponForm" action="<?php echo site_url('trip/checkout/validate_coupon');?>" method="POST" class="no-submit">
    <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
    <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
    <?php } ?>
    <div class="row">
      <label for="coupon_code" class="col-xl-3 text-center">Cod</label>
      <div class="col-xl-9">
        <div class="input-group">
          <input id="coupon_code" type="text" maxlength="255" name="coupon_code" placeholder="Introduceti codul aici" class="form-control" required />
          <input id="coupon_type" type="hidden" maxlength="255" name="coupon_type" value="<?php echo $coupon_type; ?>" />
          <input id="coupon_phone" type="hidden" maxlength="255" name="coupon_phone" value="" />
          <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Activare cupon</button>
        </div>
      </div>
    </div>
    <div id="result_couponForm" class="form-group mb-3"></div>
  </form>
</div>
<?php themeFunctions::debugFileLine('end'); ?>