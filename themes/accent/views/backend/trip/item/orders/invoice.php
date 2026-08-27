<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/invoice/scripts.php'); ?>
<?php 
$invoice_label_size = array();
$invoice_label_size['xl'] = 2;
$invoice_label_size['lg'] = 4;
$invoice_label_size['md'] = 4;
$invoice_label_size['sm'] = 4;
$invoice_label_size[''] = 12;
$invoice_label_class = 'pt-1 text-sm-right';
$invoice_value_class = '';
foreach($invoice_label_size as $k=>$v){
  $invoice_label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $invoice_value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
?>
<div class="card">
      <div class="card-header d-flex align-items-center">
		  <h2 class="h5 display" id="ticket_title">Factura</h2>
	  </div>
      <div class="card-block">
<iframe style="height:80px; width:100%;border-width:0;" border="0" id="invoice_list" name="invoice_list" src="<?php echo site_url('backend/trip/orders/invoices?id=' . $order->id); ?>"></iframe>
<?php if($can_write){ ?>
<form id="invoiceForm" target="invoice_list" name="invoiceForm" action="<?php echo site_url('backend/trip/orders/upload_invoice?id=' . $order->id); ?>" class="mt-3 ml-3 mr-3" enctype="multipart/form-data" method="POST">
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <div class="form-group row">
    <label for="invoice_submit" class="<?php echo $invoice_label_class; ?>"></label>
    <div class="<?php echo $invoice_value_class; ?>">
		<input type="file" name="pdf" id="invoice_file" class="form-control" accept="application/pdf">
		<button type="submit" id="invoice_submit" class="btn btn-success"><i class="fa fa-save"></i> Incarca</button>
    </div>
  </div>
</form>
<?php } ?>
<div id="result_invoiceForm" class="form-group"></div>
      </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>