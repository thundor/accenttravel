<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('cms_menu'); ?>
<?php themeFunctions::includeAddon('nestable'); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/menus/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/menus/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/menus/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/menus/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/menus/meta.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
$value_offset_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_offset_class .= ' offset-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$items = $this->view_data['items'];
$item = $this->view_data['item'];

$menu = $this->view_data['menu'];
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
    <div class="card">
      <div class="card-header p-1">
        <div class="input-group">
          <select class="form-control" id="menu_selector"><?php
          foreach($items as $subitem){ ?>
            <option value="<?php echo $subitem; ?>" <?php echo $subitem == $item ? 'selected' : ''; ?>><?php echo $subitem; ?></option><?php
          } ?>
          </select>
          <?php /* 
          <span class="input-group-btn">
            <button type="button" id="create_menu" class="btn btn-primary">Creaza meniu</button>
          </span>
          */ ?>
        </div>
      </div>
      <div class="tab-content card-block">
        <div class="tab-pane active" id="general_tab" role="tabpanel">
          <div class="row">
            <div class="col-md-4">
              <form id="add_page_form" onsubmit="return false;">
                <select id="page_selector" class="form-control"></select>
                <br />
                <h3>Nume</h3>
                <input id="page_title" type="text" class="form-control" placeholder="introduceti numele" required/>
                <br />
                <h3>URL</h3>
                <input id="page_url" type="text" class="form-control" placeholder="introduceti url-ul" />
                <br />
                <h3>Deschide in</h3>
                <select id="page_target" class="form-control">
                  <option value="_self">Acelasi tab</option>
                  <option value="_blank">Tab nou</option>
                </select>
                <br />
                <input type="hidden" name="task" />
                <div class="d-flex justify-content-between">
                  <button type="submit" id="add_page_button" class="add-page btn btn-success ml-auto"><i class="fa fa-plus"></i> Adauga</button>
                  <button type="submit" id="edit_page_button" class="edit-page btn btn-primary ml-auto" style="display:none;"><i class="fa fa-pencil"></i> Modifica</button>
                  <button type="button" id="cancel_edit_page_button" class="cancel-edit-page btn btn-default ml-auto" style="display:none;"><i class="fa fa-times"></i> Anulare</button>
                </div>
                <input type="submit" style="display:none;">
              </form>
            </div>
            <div class="col-md-8">
              <div id="result_menuForm"></div>
              <h3>Structura meniu</h3>
              <div class="dd" id="menu_structure">
                <ol class="dd-list"></ol>
              </div>
              <input type="hidden" id="menu_structure-output" class="form-control" form="menuForm" name="structure" />
            </div>
          </div>
          <form id="menuForm" name="menuForm" action="<?php echo site_url('backend/cms/menus/save'); ?>" method="POST" onsubmit="return false;">
            <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
            <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
            <?php } ?>
            <?php if($can_write){ ?>
            <input type="hidden" id="task" name="task" value="" />
            <input type="hidden" name="item" value="<?php echo $item; ?>" />
            <?php } ?>
          </form>
          <?php /*
          <div class="form-group row">
            <label for="status" class="<?php echo $label_class; ?>">Status meniu</label>
            <div class="<?php echo $value_class; ?>">
              <?php if($can_write){ ?>
              <div class="i-checks">
                <input id="status_active" type="radio" value="1" name="status" <?php echo $menu->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                <label for="status_active"><?php echo lang('option_active'); ?></label>
              </div>
              <div class="i-checks">
                <input id="status_inactive" type="radio" value="0" name="status" <?php echo !$menu->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                <label for="status_inactive"><?php echo lang('option_inactive'); ?></label>
              </div>
              <?php } else { ?>
              <div class="form-control" readonly><?php echo $menu->status == 1 ? lang('option_active') : lang('option_inactive'); ?></div>
              <?php } ?>
            </div>
          </div> */ ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>