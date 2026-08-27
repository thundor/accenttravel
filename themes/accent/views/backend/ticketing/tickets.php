<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('ticketing_tickets'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/tickets/scripts.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/tickets/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/tickets/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/tickets/meta.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/tickets/stylesheets.php'); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('select2/4.0.4'); ?>

<?php 

$default_session_data = array(
  'page' => 1,
  'ordering' => 'last_change DESC',
  'search' => '',
  'limit' => 50,
  'filter_id' => '',
  'filter_trip_order_id' => '',
  'filter_time_created' => '',
  'filter_status' => array(),
);
$session_data = $this->_ci->session->userdata('backend/ticketing/tickets');
if (!$session_data) {
  $session_data = array();
}
$session_data = array_replace($default_session_data, $session_data);
$filter_id = isset($session_data['filter_id']) ? $session_data['filter_id'] : null;
$filter_trip_order_id = isset($session_data['filter_trip_order_id']) ? $session_data['filter_trip_order_id'] : null;
$filter_time_created = isset($session_data['filter_time_created']) ? $session_data['filter_time_created'] : null;
$filter_status = isset($session_data['filter_status']) ? $session_data['filter_status'] : null;
$this->tickets_session_data = $session_data;
// $tickets = $this->view_data['tickets'];
$adv_filter_active = isset($filter_id) || isset($filter_trip_order_id) || isset($filter_time_created) || !empty($filter_status); // TODO
?>

<section class="forms">
  <div class="col-12">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 display"><?php echo lang('tickets_list/html'); ?></h2>
            <div class="btn-group">
              <button id="search_adv_filter_button" data-toggle="collapse" data-target="#search_adv_filter_container" title="<?php echo lang('filter_adv_search'); ?>" type="button" class="btn btn-primary<?php echo $adv_filter_active ? '' : ' collapsed'; ?>" aria-expanded="<?php echo $adv_filter_active ? 'true' : 'false'; ?>"><?php echo lang('filter_adv_search/html'); ?></button>
            </div>
          </div>
          <div class="card-block">
            <div class="row">
              <div id="search_adv_filter_container" class="col-12 collapse <?php echo $adv_filter_active ? ' show' : ''; ?>" aria-expanded="<?php echo $adv_filter_active ? 'true' : 'false'; ?>">
                <form>
                  <div class="row">
                    <div class="col-sm-6 col-md-4 col-lg-3">
                      <label for="filters_adv_id"><?php echo lang('th_ticket_id'); ?></label>
                      <input type="number" step="1" min="1" class="form-control" name="filters[adv][id]" id="filters_adv_id" placeholder="<?php echo lang('th_ticket_id'); ?>" value="<?php echo htmlspecialchars($filter_id); ?>">
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                      <label for="filters_adv_trip_order_id"><?php echo lang('th_trip_order_id'); ?></label>
                      <input type="number" step="1" min="1" class="form-control" name="filters[adv][trip_order_id]" id="filters_adv_trip_order_id" placeholder="<?php echo lang('th_trip_order_id'); ?>" value="<?php echo htmlspecialchars($filter_trip_order_id); ?>">
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                      <label for="filters_adv_status">Status</label>
                      <select name="filters[adv][status][]" id="filters_adv_status" class="form-control d-block" multiple="true" size="3">
                        <option value="1" <?php echo in_array(1, $filter_status) ? 'selected' : ''; ?>>Nou</option>
                        <option value="2" <?php echo in_array(2, $filter_status) ? 'selected' : ''; ?>>In lucru</option>
                        <option value="3" <?php echo in_array(3, $filter_status) ? 'selected' : ''; ?>>Finalizat</option>
                      </select>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                      <label for="filters_adv_time_created"><?php echo lang('th_time_created'); ?></label>
                      <input type="text" class="form-control" name="filters[adv][time_created]" id="filters_adv_time_created" placeholder="<?php echo lang('th_time_created'); ?>" value="<?php echo htmlspecialchars($filter_time_created); ?>">
                    </div>
                  </div>
                </form>
                <hr />
              </div>
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
            <table class="table-responsive table table-striped table-hover table-bordered tickets_table">
              <thead>
                <tr>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;">
                    <?php echo lang('th_ticket_id'); ?>
                  </th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;">
                    <?php echo lang('th_trip_order_id'); ?>
                  </th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;"><?php echo lang('th_status'); ?></th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;">
                    <?php echo lang('th_time_created'); ?>
                  </th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;">
                    <?php echo lang('th_time_updated'); ?>
                  </th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;">
                    <?php echo lang('th_time_span'); ?>
                  </th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;">
                    <?php echo lang('th_time_modified'); ?>
                  </th>
                  <th class="tbl-col-<?php echo ++$col_count;?> text-center" style="width:1%;">
                    <?php echo lang('th_assignee'); ?>
                  </th>
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