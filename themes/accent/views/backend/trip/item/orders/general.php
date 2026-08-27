<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/general/scripts.php'); ?>
<?php 
$client_label_size = array();
$client_label_size['xl'] = 3;
$client_label_size['lg'] = 4;
$client_label_size['md'] = 4;
$client_label_size['sm'] = 4;
$client_label_size[''] = 12;
$client_label_class = 'pt-1 text-sm-right';
$client_value_class = '';
$client_value_offset_class = '';
foreach($client_label_size as $k=>$v){
  $client_label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $client_value_offset_class .= ' offset-' . ($k ? $k . '-' : '') . $v;
  $client_value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$coupons = $coupons ?? $this->view_data['coupons'];
?>
<form id="generalForm" name="generalForm" action="<?php echo site_url('backend/trip/orders/save_order'); ?>" class="mt-3" method="POST" onsubmit="return false;">
  <?php if($can_write){ ?>
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <input type="hidden" name="order_id" value="<?php echo $order->id; ?>" />
  <?php } ?>
  <div class="row">
    <div class="col-12 col-xl-6">
      <div class="form-group row">
        <label for="order_status" class="<?php echo $client_label_class; ?>">Status comanda</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <div class="input-group d-block d-lg-flex">
            <select class="valid form-control" name="status" id="order_status" >
              <?php if($order->status == -1){ ?>
              <option value="-1" style="color:red;">Eroare</option>
              <?php } ?>
              <option value="0">Nefinalizata</option>
              <option value="1" <?php echo $order->status == 1 ? 'selected' : ''; ?>>In procesare</option>
              <option value="2" <?php echo $order->status == 2 ? 'selected' : ''; ?>>Confirmata</option>
              <option value="3" <?php echo $order->status == 3 ? 'selected' : ''; ?>>Anulata</option>
            </select>
          </div>
          <?php } else { ?>
          <div id="order_status" class="form-control" readonly>
          <?php 
            $status = 'Nefinalizata';
            if($order->status == 1){
              $status = 'In procesare';
            } elseif($order->status == 2){
              $status = 'Confirmata';
            } elseif($order->status == 3){
              $status = 'Anulata';
            }
            echo $status;
          ?>
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="form-group row">
        <label for="order_payment_method" class="<?php echo $client_label_class; ?>">Metoda de plata</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
          <div class="input-group d-block d-lg-flex">
            <select class="valid form-control" name="payment_method" id="order_payment_method" >
              <option value="agency">Agentie</option>
              <option value="bank" <?php echo $order->payment_method == 'bank' ? 'selected' : ''; ?>>Transfer bancar</option>
              <option value="online" <?php echo $order->payment_method == 'online' ? 'selected' : ''; ?>>Online</option>
              <option value="free" <?php echo $order->payment_method == 'free' ? 'selected' : ''; ?>>Gratuit</option>
            </select>
          </div>
          <?php } else { ?>
          <div id="order_payment_method" class="form-control" readonly>
          <?php 
            $payment_method = 'Agentie';
            if($order->payment_method == 'bank'){
              $payment_method = 'In procesare';
            } elseif($order->payment_method == 'online'){
              $payment_method = 'Confirmata';
            } elseif($order->payment_method == 'free'){
              $payment_method = 'Gratuit';
            }
            echo $payment_method;
          ?>
          </div>
          <?php } ?>
        </div>
      </div>
      <?php if($can_write){ ?>
      <div class="form-group row">
        <div class="<?php echo $client_value_class . $client_value_offset_class; ?>">
          <label class="input-group mb-0">
            <span class="input-group-addon mb-0">
              <input type="checkbox" name="notify_customer" value="1" checked>
            </span>
            <div class="form-control">Notificare client prin email</div>
          </label>
          <p class="text-primary">Pentru status "Confirmata", in mail se ataseaza automat voucher-ele emise.</p>
        </div>
      </div>
      <?php } ?>
      <div class="form-group row">
        <label for="order_coupon_code" class="<?php echo $client_label_class; ?>">Cod cupon</label>
        <div class="<?php echo $client_value_class; ?>">
          <?php if($can_write){ ?>
		<div id="coupons-wrapper">
			<input type="hidden" name="coupon_codes" value="">
		<?php foreach($coupons as $k=>$coupon) {?>
			<div class="input-group">
				<input type="text" value="<?php echo $coupon->coupon_code; ?>" name="coupon_codes[]" class="form-control" id="order_coupon_current-<?php echo $k; ?>" />
				<span class="input-group-addon">
					<?php echo ($coupon->coupon_discount_type == 'P' ? '(' . $coupon->coupon_percentage . '%)' : ($coupon->coupon_currency == 'RON' ? $coupon->coupon_fixed_ron . ' Lei' : $coupon->coupon_fixed_eur . ' EUR' )); ?>
				</span>
				<div class="input-group-btn">
					<button type="button" class="btn btn-danger remove-coupon"><i class="fa fa-trash"></i></button>
				</div>
			</div>
          <?php } ?>
		</div>
          <div class="input-group mb-0 mt-3">
            <select class="form-control" id="order_coupon_code" >
              <option value="">Alegeti cupon</option>
            </select>
          </div>
          <?php } else { ?>
		  <?php if(!$coupons) { ?>
		  <div class="form-control" id="order_coupon_current" readonly><?php echo '-niciun cupon aplicat-'; ?></div>
		  <?php } else { ?>
		  <?php foreach($coupons as $k=>$coupon) {?>
		  <div class="form-control" id="order_coupon_current-<?php echo $k; ?>" readonly><?php echo $coupon->coupon_code . ' ' . ($coupon->coupon_discount_type == 'P' ? '(' . $coupon->coupon_percentage . '%)' : ($coupon->coupon_currency == 'RON' ? $coupon->coupon_fixed_ron . ' Lei' : $coupon->coupon_fixed_eur . ' EUR' )); ?></div>
          <?php } ?>
          <?php } ?>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-6">
      <?php if($order->provider === 'trip') { ?>
      <div class="form-group row">
        <label for="trip_order_id" class="<?php echo $client_label_class; ?>">ID Trip</label>
        <div class="<?php echo $client_value_class; ?>">
          <div id="trip_order_id" class="form-control" readonly>
            <?php echo $order->trip_order_id; ?>&nbsp;
          </div>
        </div>
      </div>
      <?php } ?>
      <div class="form-group row">
        <label for="order_payment_gateway" class="<?php echo $client_label_class; ?>">Procesator plati (online)</label>
        <div class="<?php echo $client_value_class; ?>">
          <div id="order_payment_gateway" class="form-control" readonly>
            <?php echo $order->payment_gateway; ?>&nbsp;
          </div>
        </div>
      </div>
      <div class="form-group row">
        <label for="client_ip" class="<?php echo $client_label_class; ?>">IP client</label>
        <div class="<?php echo $client_value_class; ?>">
          <div id="client_ip" class="form-control" readonly>
            <?php echo $order->ip; ?>&nbsp;
          </div>
        </div>
      </div>
      <div id="order_payment_gateway" class="form-control" readonly>
        <?php echo $order->message; ?>&nbsp;
      </div>
    </div>
  </div>
  <?php if($can_write){ ?>
  <div class="form-group row">
    <label for="general_submit" class="<?php echo $client_label_class; ?>"></label>
    <div class="<?php echo $client_value_class; ?>">
      <button type="submit" id="general_submit" class="btn btn-success"><i class="fa fa-save"></i> Salveaza</button>
    </div>
  </div>
  <?php } ?>
</form>
<div id="result_generalForm" class="form-group"></div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>