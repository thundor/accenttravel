<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('trip_coupon'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php //themeFunctions::includeAddon('select2'); ?>
<?php //themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/coupon/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/coupon/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/coupon/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/coupon/meta.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$coupon = $this->view_data['coupon'];
$editing = $coupon->id != 0;
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
    <form id="couponsForm" name="couponsForm" action="<?php echo site_url('backend/trip/coupons/save'); ?>" method="POST" enctype="multipart/form-data">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="task" name="task" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="id" value="<?php echo $coupon->id; ?>" />
      <?php } ?>
      <?php } ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('coupon_info_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="coupon_type" class="<?php echo $label_class; ?> text-center">Tip cupon</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
					<select name="type" id="coupon_type" class="form-control">
						<option value="">Cupon singular</option>
						<option value="group" <?php echo $coupon->type == 'group' ? ' selected="selected"' : '' ?>>Grup de cupoane</option>
					</select>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->type == 'group' ? ' Grup de cupoane' : 'Cupon singular'; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row type-only group-only">
                <label for="coupon_prefix" class="<?php echo $label_class; ?> text-center">Prefix cod</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="coupon_prefix" type="text" maxlength="255" name="prefix" placeholder="Prefix cod" class="form-control" value="<?php echo htmlspecialchars($coupon->prefix); ?>" required />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($coupon->prefix); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row type-only singular-only">
                <label for="coupon_code" class="<?php echo $label_class; ?> text-center"><?php echo lang('code_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="coupon_code" type="text" maxlength="255" name="code" placeholder="<?php echo lang('code_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($coupon->code); ?>" required />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($coupon->code); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_status" class="<?php echo $label_class; ?> text-center"><?php echo lang('status_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_status_active" type="radio" value="1" name="status" <?php echo $coupon->status ==1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="coupon_status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="coupon_status_inactive" type="radio" value="0" name="status" <?php echo !$coupon->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="coupon_status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->status == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  
              <div class="form-group row">
                <label for="coupon_discount_type" class="<?php echo $label_class; ?> text-center">Tip discount</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
					<select name="discount_type" id="coupon_discount_type" class="form-control">
						<option value="P">Procentaj</option>
						<option value="F" <?php echo $coupon->type == 'F' ? ' selected="selected"' : '' ?>>Suma fixa</option>
					</select>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->type == 'P' ? 'Procentaj' : 'Suma fixa'; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_percentage" class="<?php echo $label_class; ?> text-center"><?php echo lang('percentage_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <?php if($can_write){ ?>
                    <input id="coupon_percentage" type="number" min="0" max="100" step="0.01" name="percentage" placeholder="<?php echo lang('percentage_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($coupon->percentage); ?>" required/>
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($coupon->percentage); ?>&nbsp;</div>
                    <?php } ?>
                    <span class="input-group-addon">%</span>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_amount_ron" class="<?php echo $label_class; ?> text-center">Suma</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <?php if($can_write){ ?>
                    <input id="coupon_amount_ron" type="number" min="0" max="100000" step="0.01" name="amount_ron" placeholder="SUMA RON" class="form-control" value="<?php echo htmlspecialchars($coupon->amount_ron); ?>" required/>
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($coupon->amount_ron); ?>&nbsp;</div>
                    <?php } ?>
                    <span class="input-group-addon">RON</span>
                  </div>
                  <div class="input-group">
                    <?php if($can_write){ ?>
                    <input id="coupon_amount_eur" type="number" min="0" max="100000" step="0.01" name="amount_ron" placeholder="SUMA RON" class="form-control" value="<?php echo htmlspecialchars($coupon->amount_eur); ?>" required/>
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($coupon->amount_eur); ?>&nbsp;</div>
                    <?php } ?>
                    <span class="input-group-addon">EUR</span>
                  </div>
                </div>
              </div>
              <div class="form-group row type-only singular-only">
                <label for="coupon_max_uses" class="<?php echo $label_class; ?> text-center"><?php echo lang('max_uses_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="coupon_max_uses" type="number" min="0" max="99999999999" step="1" name="max_uses" placeholder="<?php echo lang('max_uses_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($coupon->max_uses); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($coupon->max_uses); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row type-only singular-only">
                <label for="coupon_nr_uses" class="<?php echo $label_class; ?> text-center"><?php echo lang('nr_uses_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly><?php echo intval($coupon->nr_uses); ?>&nbsp;</div>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_date_start" class="<?php echo $label_class; ?> text-center"><?php echo lang('date_start_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <label class="input-group mb-0">
                    <input id="coupon_date_start" maxlength="10" type="text" name="date_start" placeholder="<?php echo lang('date_start_field_placeholder'); ?>" class="form-control input-date_start" value="<?php echo htmlspecialchars($coupon->date_start); ?>" />
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </label>
                  <?php } else { ?>
                  <div id="coupon_date_start" class="form-control" readonly><?php echo htmlspecialchars($coupon->date_start); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_date_expire" class="<?php echo $label_class; ?> text-center"><?php echo lang('date_expire_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <label class="input-group mb-0">
                    <input id="coupon_date_expire" maxlength="10" type="text" name="date_expire" placeholder="<?php echo lang('date_expire_field_placeholder'); ?>" class="form-control input-date_expire" value="<?php echo htmlspecialchars($coupon->date_expire); ?>" />
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </label>
                  <?php } else { ?>
                  <div id="coupon_date_expire" class="form-control" readonly><?php echo htmlspecialchars($coupon->date_expire); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_hotel_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru hoteluri</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_hotel_active" type="checkbox" value="1" name="hotel" <?php echo $coupon->hotel ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_hotel_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->hotel == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_package_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru pachete Romania</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_package_active" type="checkbox" value="1" name="package" <?php echo $coupon->package ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_package_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->package == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>