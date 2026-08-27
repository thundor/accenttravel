<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('cms_layout'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/layout/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/layout/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/layout/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/layout/meta.php'); ?>
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
$layout = $this->view_data['layout'];
$editing = trim($layout->slug) !== '';
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
    <form id="layoutForm" name="layoutForm" action="<?php echo site_url('backend/cms/layouts/save'); ?>" method="POST" onsubmit="return false;">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="action" name="action" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="slug" value="<?php echo $layout->slug; ?>" />
      <?php } ?>
      <?php } ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('layout_info_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="slug" class="<?php echo $label_class; ?>"><?php echo lang('slug_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="slug" type="text" maxlength="50" name="newslug" placeholder="<?php echo lang('slug_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($layout->slug); ?>" required />
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($layout->slug); ?></div>
                  <?php } ?>
                  <small class="text-muted"><?php echo lang('slug_field_help/html'); ?></small>
                </div>
              </div>
              <div class="form-group row">
                <label for="name" class="<?php echo $label_class; ?>"><?php echo lang('name_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="name" type="text" maxlength="255" name="name" placeholder="<?php echo lang('name_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($layout->name); ?>" required />
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($layout->name); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="author" class="<?php echo $label_class; ?>"><?php echo lang('author_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="author" type="text" maxlength="255" name="author" placeholder="<?php echo lang('author_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($layout->author); ?>" required />
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($layout->author); ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group row">
                <label for="version" class="<?php echo $label_class; ?>"><?php echo lang('version_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="version" type="text" maxlength="255" name="version" placeholder="<?php echo lang('version_field_placeholder'); ?>" class="form-control" value="<?php echo htmlspecialchars($layout->version); ?>" required />
                  <?php } else { ?>
                  <div class="form-control"><?php echo htmlspecialchars($layout->version); ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('layout_content_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#layout-content" role="tab">Content</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#layout-css" role="tab">CSS</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#layout-js" role="tab">Javascript</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#layout-addons" role="tab">Addons</a>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="layout-content" role="tabpanel">
                  <?php include 'layout/tab_content.php'; ?>
                </div>
                <div class="tab-pane" id="layout-css" role="tabpanel">
                  <?php include 'layout/tab_css.php'; ?>
                </div>
                <div class="tab-pane" id="layout-js" role="tabpanel">
                  <?php include 'layout/tab_js.php'; ?>
                </div>
                <div class="tab-pane" id="layout-addons" role="tabpanel">
                  <?php include 'layout/tab_addons.php'; ?>
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