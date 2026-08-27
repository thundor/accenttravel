<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('trip_discount'); ?>
<?php themeFunctions::includeAddon('jquery/cookie'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php //themeFunctions::includeAddon('select2'); ?>
<?php //themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/discount/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/discount/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/discount/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/discount/meta.php'); ?>
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
$discount = $this->view_data['discount'];
$editing = $discount->id != 0;
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12"><?php
  if(!$discount->type){ ?>
    <p>Alegeti tipul de discount</p>
    <ul class="list-group">
      <li class="list-group-item">
        <a href="<?php echo base_url('backend/trip/discounts/add/package'); ?>">Vacanta</a>
      </li>
    </ul>
  <?php
  } else { ?>
    <form id="discountsForm" name="discountsForm" action="<?php echo site_url('backend/trip/discounts/save'); ?>" method="POST" enctype="multipart/form-data">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="task" name="task" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="id" value="<?php echo $discount->id; ?>" />
      <?php } ?>
      <?php } ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('discount_info_section_title/html'); ?> <strong><?php echo lang('type_' . $discount->type . '/html'); ?></strong></h2>
            </div>
            <div class="card-block">
              <input type="hidden" name="type" value="<?php echo $discount->type; ?>" />
              <?php include(__DIR__ . '/discount/' . $discount->type . '.php'); ?>
              <div class="form-group row">
                <label for="discount_status" class="<?php echo $label_class; ?> text-center"><?php echo lang('status_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="discount_status_active" type="radio" value="1" name="status" <?php echo $discount->status ==1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="discount_status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="discount_status_inactive" type="radio" value="0" name="status" <?php echo !$discount->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="discount_status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $discount->status == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="discount_percentage" class="<?php echo $label_class; ?> text-center"><?php echo lang('percentage_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="input-group">
                    <?php if($can_write){ ?>
                    <input id="discount_percentage" type="number" min="0" max="100" step="0.01" name="percentage" placeholder="<?php echo lang('percentage_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($discount->percentage); ?>" required/>
                    <?php } else { ?>
                    <div class="form-control" readonly><?php echo htmlspecialchars($discount->percentage); ?>&nbsp;</div>
                    <?php } ?>
                    <span class="input-group-addon">%</span>
                  </div>
                </div>
              </div>
              <div class="form-group row">
                <label for="discount_date_start" class="<?php echo $label_class; ?> text-center"><?php echo lang('date_start_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <label class="input-group mb-0">
                    <input id="discount_date_start" maxlength="10" type="text" name="date_start" placeholder="<?php echo lang('date_start_field_placeholder'); ?>" class="form-control input-date_start" value="<?php echo htmlspecialchars($discount->date_start); ?>" />
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </label>
                  <?php } else { ?>
                  <div id="discount_date_start" class="form-control" readonly><?php echo htmlspecialchars($discount->date_start); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="discount_date_expire" class="<?php echo $label_class; ?> text-center"><?php echo lang('date_expire_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <label class="input-group mb-0">
                    <input id="discount_date_expire" maxlength="10" type="text" name="date_expire" placeholder="<?php echo lang('date_expire_field_placeholder'); ?>" class="form-control input-date_expire" value="<?php echo htmlspecialchars($discount->date_expire); ?>" />
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                  </label>
                  <?php } else { ?>
                  <div id="discount_date_expire" class="form-control" readonly><?php echo htmlspecialchars($discount->date_expire); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form><?php 
  } ?>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>