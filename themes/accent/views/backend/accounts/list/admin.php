<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('accounts_list_admin'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/admin/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/admin/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/admin/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/admin/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/admin/stylesheets.php'); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::includeAddon('select2/4.0.4'); ?>
<section class="forms">
  <div class="col-12">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display"><?php echo lang('user_list/html'); ?></h2>
          </div>
          <div class="card-block">
            <div class="row">
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
                  <input type="number" min="0" step="1" class="form-control" placeholder="<?php echo lang('list_option_all'); ?>" name="limit" id="limit" value="">
                </div>
              </div>
              <div class="col-xl-4 col-lg-3 col-md-6">
                <select id="ordering" name="ordering">
                  <option value=""><?php echo lang('ordering_placeholder'); ?></select>
                </select>
              </div>
              <div class="col-lg-12 col-md-6">
                <nav aria-label="Pagination">
                  <ul class="pagination justify-content-center justify-content-md-end create-pagination">
                  </ul>
                </nav>
              </div>
              <?php /* <div class="col-xl-12">
                <div id="ordering" class="btn-group btn-group-sm sortable">
                  <button type="button" class="btn btn-danger text-center">
                    <i class="fa fa-trash"></i>
                  </button>
                  <span class="btn btn-warning text-center sortable-item">
                    <span class="sortable-handle text-success">
                      <i class="fa fa-arrows"></i>
                    </span>
                    <input type="hidden" name="ordering[]" value="user_id ASC" />
                    <strong>ID</strong> <i class="fa fa-sort-numeric-asc"></i>
                  </span>
                  <span class="btn btn-warning text-center sortable-item">
                    <span class="sortable-handle text-success">
                      <i class="fa fa-arrows"></i>
                    </span>
                    <input type="hidden" name="ordering[]" value="user_username ASC" />
                    <strong>Username</strong> <i class="fa fa-sort-alpha-asc"></i>
                  </span>
                  <button type="button" class="btn btn-primary text-center">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
              </div> */ ?>
            </div>
          <?php
          $col_count = 0;
          ?>
            <table class="table-responsive table table-striped table-hover table-bordered <?php echo $this->_controller; ?>_users_table">
              <thead>
                <tr>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;">
                    <?php echo lang('th_user_id'); ?>
                  <?php /* 
                    <div class="dropdown">
                      <button class="btn btn-default dropdown-toggle" type="button" id="th_user_id" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo lang('th_user_id'); ?>
                      </button>
                      <div class="dropdown-menu">
                        <div class="dropdown-submenu">
                          <a href="javascript:void(0);" class="dropdown-item"><?php echo lang('ordering/html'); ?></a>
                          <div class="dropdown-menu">
                            <button class="dropdown-item"><?php echo lang('sort_ascending_numeric/html'); ?></button>
                            <button class="dropdown-item"><?php echo lang('sort_descending_numeric/html'); ?></button>
                          </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-item">
                          <div class="input-group">
                            <span class="input-group-addon"><?php echo lang('th_user_id'); ?>:</span>
                            <input type="search" name="advsearch[user_id]" class="form-control"/>
                          </div>
                        </div>
                      </div>
                    </div>
                    */ ?>
                  </th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_user_username'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_user_lastname'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_user_firstname'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_user_email'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;"><?php echo lang('th_user_status'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;"><?php echo lang('th_user_role'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;"><?php echo lang('th_actions'); ?></th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>