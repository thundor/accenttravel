<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('trip_coupon'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('twigjs'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php //themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/coupon/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/coupon/stylesheets.php'); ?>
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
      <div id="coupon-details" class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('coupon_info_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
				<div class="form-group row">
                <label for="coupon_type" class="<?php echo $label_class; ?> text-center">Tip cupon</label>
                <div class="<?php echo $value_class; ?>">
				 <?php $can_change_type = true; ?>
				 <?php if($coupon->id && $coupon->type == 'group'){ ?>
					 <?php $can_change_type = false; ?>
					<input type="hidden" id="coupon_type" name="type" class="form-control" value="<?php echo $coupon->type; ?>">
				 <?php } ?>
                  <?php if($can_write && $can_change_type){ ?>
					<select name="type" id="coupon_type" class="form-control">
						<option value="singular">Cupon singular</option>
						<option value="group" <?php echo $coupon->type == 'group' ? ' selected="selected"' : '' ?>>Serie de cupoane</option>
					</select>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->type == 'group' ? ' Serie de cupoane' : 'Cupon singular'; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_code" class="<?php echo $label_class; ?> text-center"><?php echo lang('code_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
					<?php $can_change_code = true; ?>
					<?php if($coupon->id && $coupon->type == 'group'){ ?>
						<?php $can_change_code = false; ?>
						<input type="hidden" id="coupon_code" name="code" class="form-control" value="<?php echo $coupon->code; ?>">
					<?php } ?>
                  <?php if($can_write && $can_change_code){ ?>
                  <input id="coupon_code" type="text" maxlength="255" name="code" placeholder="<?php echo lang('code_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($coupon->code); ?>" required />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($coupon->code); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_ean" class="<?php echo $label_class; ?> text-center">EAN</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly><?php echo $coupon->id ? htmlspecialchars($coupon->ean) : '- Se genereaza automat la salvare -'; ?></div>
                </div>
              </div>
			  <div class="form-group row">
                <label for="coupon_name" class="<?php echo $label_class; ?> text-center">Nume</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="coupon_name" type="text" maxlength="255" name="name" placeholder="Nume" class="form-control" value="<?php echo htmlspecialchars($coupon->name); ?>" required />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($coupon->name); ?>&nbsp;</div>
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
						<option value="F" <?php echo $coupon->discount_type == 'F' ? ' selected="selected"' : '' ?>>Suma fixa</option>
					</select>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->discount_type == 'P' ? 'Procentaj' : 'Suma fixa'; ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row discount-type-only P-only">
                <label for="coupon_percentage" class="<?php echo $label_class; ?> text-center"><?php echo lang('percentage_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <?php if($can_write){ ?>
                    <input id="coupon_percentage" type="number" min="0" max="100" step="0.01" name="percentage" placeholder="<?php echo lang('percentage_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($coupon->percentage); ?>" />
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($coupon->percentage); ?>&nbsp;</div>
                    <?php } ?>
                    <span class="input-group-addon">%</span>
                  </div>
                </div>
              </div>
			  <div class="form-group row discount-type-only F-only">
                <label for="coupon_fixed_ron" class="<?php echo $label_class; ?> text-center">Suma</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <?php if($can_write){ ?>
                    <input id="coupon_fixed_ron" type="number" min="0" max="100000" step="0.01" name="fixed_ron" placeholder="0.00" class="form-control" value="<?php echo htmlspecialchars($coupon->fixed_ron); ?>" />
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($coupon->fixed_ron); ?>&nbsp;</div>
                    <?php } ?>
                    <span class="input-group-addon">RON</span>
                  </div>
                  <div class="input-group">
                    <?php if($can_write){ ?>
                    <input id="coupon_fixed_eur" type="number" min="0" max="100000" step="0.01" name="fixed_eur" placeholder="0.00" class="form-control" value="<?php echo htmlspecialchars($coupon->fixed_eur); ?>" />
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($coupon->fixed_eur); ?>&nbsp;</div>
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
              <div class="form-group row">
                <label for="coupon_observation" class="<?php echo $label_class; ?> text-center"><?php echo lang('observation_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <textarea id="coupon_observation" name="observation" placeholder="<?php echo lang('observation_field_placeholder'); ?>" class="form-control"><?php echo htmlspecialchars($coupon->observation); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($coupon->observation); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
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
			  <div class="form-group row">
                <label for="coupon_flight_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru zboruri</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_flight_active" type="checkbox" value="1" name="flight" <?php echo $coupon->flight ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_flight_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->flight == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label for="coupon_citybreak_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru City Break</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_citybreak_active" type="checkbox" value="1" name="citybreak" <?php echo $coupon->citybreak ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_citybreak_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->citybreak == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_paralela45_strainatate_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru Paralela45 strainatate</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_paralela45_strainatate_active" type="checkbox" value="1" name="paralela45_strainatate" <?php echo $coupon->paralela45_strainatate ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_paralela45_strainatate_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->paralela45_strainatate == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_paralela45_circuit_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru Paralela45 circuit</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_paralela45_circuit_active" type="checkbox" value="1" name="paralela45_circuit" <?php echo $coupon->paralela45_circuit ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_paralela45_circuit_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->paralela45_circuit == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_travelfuse_charter_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru travelfuse charter</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_travelfuse_charter_active" type="checkbox" value="1" name="travelfuse_charter" <?php echo $coupon->travelfuse_charter ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_travelfuse_charter_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->travelfuse_charter == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_travelfuse_circuit_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru travelfuse circuit</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_travelfuse_circuit_active" type="checkbox" value="1" name="travelfuse_circuit" <?php echo $coupon->travelfuse_circuit ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_travelfuse_circuit_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->travelfuse_circuit == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_epay_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru EPAY <br/><small>Codurile vor fi accesibile serverului Epay prin API</small></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_epay_active" type="checkbox" value="1" name="epay" <?php echo $coupon->epay ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_epay_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->epay == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="coupon_fsli_active" class="<?php echo $label_class; ?> text-center">Disponibil pentru FSLI <br/><small>Numarul de telefon va fi verificat prin API FSLI</small></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="coupon_fsli_active" type="checkbox" value="1" name="fsli" <?php echo $coupon->fsli ==1 ? 'checked' : ''; ?> class="form-control-custom checkbox-custom">
                    <label for="coupon_fsli_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $coupon->fsli == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  <?php if($coupon->id && $coupon->type == 'group'){ ?>
  <div class="col-12 mt-3">
	<div class="card">
		<div class="card-header d-flex align-items-center justify-content-between">
		  <h2 class="h5 display">Coduri in serie</h2>
			<?php /*<button type="button" class="btn btn-success" id="export-button"><i class="fa fa-download"></i> Exporta</button> */ ?>
			
			<div class="input-group-btn dropdown">
				<button data-toggle="dropdown" type="button" class="btn btn-white dropdown-toggle" aria-expanded="false">Exporta XLS <span class="caret"></span></button>
				<div class="dropdown-menu">
					  <a class="dropdown-item export-button" data-type="all" href="javascript:void(0)">Toate</a>
					  <a class="dropdown-item export-button" data-type="filtered" href="javascript:void(0)">Filtrate</a>
					  <a class="dropdown-item export-button" data-type="current_page" href="javascript:void(0)">Pagina curenta</a>
				</div>
			</div>
		  <div class="input-group" style="max-width:250px;">
			<input type="number" step="1" min="1" value="" id="generate-number" class="form-control" />
			<div class="input-group-btn">
				<?php if($editing){ ?>
				<button type="button" class="btn btn-primary" id="generate-button"><i class="fa fa-plus"></i> Genereaza</button>
				<?php } ?>
			</div>
		  </div>
		</div>
		<div class="card-block" id="serii-cupoane-wrapper">
			<div class="row mb-3">
				<div class="col-xl-5 col-lg-5 col-md-6">
					<div class="input-group">
					  <input type="search" class="form-control" name="search" id="search" placeholder="<?php echo lang('filter_search_placeholder'); ?>">
					  <div class="input-group-btn">
						<div class="btn-group">
						  <button id="search_search_button" title="<?php echo lang('filter_search'); ?>" type="button" class="btn btn-primary"><?php echo lang('filter_search/html'); ?></button>
						  <button id="search_clear_button" title="<?php echo lang('filter_clear'); ?>" type="button" class="btn btn-default"><?php echo lang('filter_clear/html'); ?></button>
						</div>
					  </div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6">
					<div class="input-group">
					  <?php $list_limit_options = array(
						50, 100, 150, 200
					  ); ?>
					  <div class="input-group-btn">
						<button data-toggle="dropdown" type="button" class="btn btn-white dropdown-toggle" aria-expanded="false"><?php echo lang('list_per_page/html'); ?><span class="caret"></span></button>
						<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
						  <?php foreach($list_limit_options as $list_limit_option){ ?>
						  <a class="dropdown-item" href="#" onclick="event.preventDefault();jQuery('#limit').val(<?php echo $list_limit_option; ?>).trigger('change');return false;"><?php echo sprintf(lang('list_option_' . ($list_limit_option == 1 ? '1' : 'x').'/html'), $list_limit_option); ?></a>
						  <?php } ?>
						  <div class="dropdown-divider"></div>
						  <a class="dropdown-item" href="#" onclick="event.preventDefault();jQuery('#limit').val('').trigger('change');return false;"><?php echo lang('list_option_all/html'); ?></a>
						</div>
					  </div>
					  <input type="number" min="0" step="1" class="form-control" placeholder="<?php echo lang('list_option_all'); ?>" name="limit" id="limit" value="100">
					</div>
				  </div>
				  <div class="col-xl-4 col-lg-3 col-md-6">
					<select id="ordering" name="ordering" class="form-control">
					  <option value=""><?php echo lang('ordering_placeholder'); ?></select>
					</select>
				  </div>
			</div>
			<div class="row mb-3">
				<div class="col-md-4">
					<span>Afisare: <strong id="shown-coupons"></strong> din <strong id="total-coupons"></strong></span>
				</div>
				<div class="col-md-8">
					<nav aria-label="Pagination">
					  <ul class="pagination justify-content-center justify-content-md-end create-pagination"></ul>
					</nav>
				</div>
			</div>
			<table id="serii-cupoane" class="table table-bordered">
				<thead>
					<tr>
						<th width="1%" class="text-center">#</th>
						<th class="text-center">Cod cupon</th>
						<th class="text-center">Serial number</th>
						<th width="1%" class="text-center">Status</th>
						<th width="1%" class="text-center">Nr. Utilizari</th>
						<th class="text-center">Comenzi</th>
						<th class="text-center">Servicii</th>
						<th class="text-center unexportable">Actiune</th>
					</tr>
				</thead>
				<tbody id="serii-cupoane-tbody">
					
				</tbody>
			</table>
		</div>
	</div>
  </div>
  <?php } ?>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>