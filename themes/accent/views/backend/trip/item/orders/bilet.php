<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/bilet/scripts.php'); ?>
<?php 
$bilet_label_size = array();
$bilet_label_size['xl'] = 2;
$bilet_label_size['lg'] = 4;
$bilet_label_size['md'] = 4;
$bilet_label_size['sm'] = 4;
$bilet_label_size[''] = 12;
$bilet_label_class = 'pt-1 text-sm-right';
$bilet_value_class = '';
foreach($bilet_label_size as $k=>$v){
  $bilet_label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $bilet_value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
?>
<div class="card mt-1">
      <div class="card-header d-flex align-items-center">
		  <h2 class="h5 display" id="ticket_title">Bilete</h2>
	  </div>
      <div class="card-block">
<iframe style="height:80px; width:100%;border-width:0;" border="0" id="bilet_list" name="bilet_list" src="<?php echo site_url('backend/trip/orders/bilets?id=' . $order->id); ?>"></iframe>
<?php if($can_write){ ?>
<form id="biletForm" target="bilet_list" name="biletForm" action="<?php echo site_url('backend/trip/orders/upload_bilet?id=' . $order->id); ?>" class="mt-3 ml-3 mr-3" enctype="multipart/form-data" method="POST">
  <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
  <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
  <?php } ?>
  <div class="form-group row">
    <label for="bilet_submit" class="<?php echo $bilet_label_class; ?>"></label>
    <div class="<?php echo $bilet_value_class; ?>">
		<input type="file" name="pdf" id="bilet_file" class="form-control" accept="application/pdf">
		<button type="submit" id="bilet_submit" class="btn btn-success"><i class="fa fa-save"></i> Incarca</button>
    </div>
  </div>
</form>
<?php } ?>
<div id="result_biletForm" class="form-group"></div>
      </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>