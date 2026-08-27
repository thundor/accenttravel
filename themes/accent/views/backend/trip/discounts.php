<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('trip_discounts'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/discounts/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/discounts/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/discounts/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/discounts/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/discounts/stylesheets.php'); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::includeAddon('select2/4.0.4'); ?>
<section class="forms">
  <div class="col-12">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h2 class="h5 display"><?php echo lang('discounts_list/html'); ?></h2>
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
            </div>
          <?php
          $col_count = 0;
          ?>
            <table class="table-responsive table table-striped table-hover table-bordered Trip_Discounts_table">
              <thead>
                <tr>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;"><?php echo lang('th_id'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_type'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_type_id'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_name'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_percentage'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_date_start'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_date_expire'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center"><?php echo lang('th_status'); ?></th>
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