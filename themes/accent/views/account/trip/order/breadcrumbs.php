<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $order = &$this->view_data['order']; ?>
<div class="row blockBack">
  <div class="col-sm-4  mt-3 pr-0">
    <p>
      <a href="<?php echo site_url('account/trip/orders'); ?>" class="backToCat"><i class="fa fa-caret-left mt-1"></i> Inapoi la lista</a>
    </p>
  </div>
  <?php if(($order->status == 2) && ($order->trip_order->Status==1)) { ?>
  <div class="col-sm-8  mt-3 pl-0 text-right">
    <p><a href="<?php echo site_url('account/trip/orders/download?id=' . $order->id); ?>" class="backToCat"><i class="fa fa-file-archive-o mt-1"></i> Descarca voucherele</a></p>
  </div>
  <?php } ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>